<?php

namespace Drengr\Framework;

use Exception;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Server;
use WP_User;

class AuthenticationController extends WP_REST_Controller
{
    protected $namespace;

    protected $request;

    /**
     * AuthenticationController constructor.
     * @param string $namespace used by the REST API like "blah-api/v1"
     * @param Request $request
     */
    public function __construct($namespace, Request $request)
    {
        $this->namespace = $namespace;
        $this->request = $request;
    }

    /**
     * Add actions and filters when application loads
     *
     * @see https://plugins.trac.wordpress.org/browser/oauth2-provider/trunk/wp-oauth-main.php
     */
    public function register()
    {
        add_action('determine_current_user', [$this, 'validateUser']);
    }

    /**
     * Invoke at 'rest_api_init'. This adds the /authenticate route under the API namespace.
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            'authenticate',
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'authenticate'],
                'permission_callback' => __return_true,
            ]
        );

    }

    /**
     * Authentication endpoint for the API. Send username and password parameters in the request.
     *
     * If credentials are valid, this endpoint will return a JWT token in a JSON structure:
     *    {
     *        "token": "eyJ0e...."
     *    }
     *
     * This token must be saved by the UI and returned as a Bearer token in the Authentication
     * header of each subsequent request.
     *
     * @param WP_REST_Request $request
     * @return WP_Error|WP_User
     */
    public function authenticate($request)
    {
        $username = $request->get_param('username');
        $password = $request->get_param('password');

        $user = get_user_by('login', $username);

        if ($user && ($user instanceof WP_User)) {
            $good = wp_check_password($password, $user->user_pass, $user->id);

            if ($good) {
                return rest_ensure_response([
                    'token' => $this->token($user)
                ]);
            }

            return new WP_Error('unauthorized', 'invalid credentials', ['status' => 401]);
        }

        return $user; // WP_Error
    }

    /**
     * Build the JWT for a particular user.
     *
     * @param WP_User $user
     * @return string
     */
    protected function token(WP_User $user)
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $claims = [
            'iss' => wp_guess_url(),
            'sub' => $user->id,
            'exp' => time() + (24 * 60 * 60), // 24 hours
        ];

        $payload = $this->encode64($header) . '.' . $this->encode64($claims);

        $key = $this->getUserKey($user);

        $signature = $this->signature($payload, $key);

        return $payload . '.' . $this->encode64($signature);
    }

    /**
     * Called by WordPress when trying to identify a user. If we find a valid token,
     * we return the WP_User. Otherwise, just return null and WordPress will look for another
     * validation method.
     *
     * @param $id
     * @return WP_User|null
     */
    public function validateUser($id)
    {
        if ( ! empty($id)) {
            return $id;
        }

        try {
            $token = $this->getTokenFromRequest($this->request);

            list($mac, $header, $claims, $signature) = $this->explodeToken($token);

            $this->validateHeader($header);
            $this->validateClaims($claims);

            /** @var WP_User $user */
            $user = WP_User::get_data_by('id', $claims['sub']);

            $key = $this->getUserKey($user);

            $this->validateSignature($mac, $signature, $key);

            return $user;
        } catch (\Exception $e) {
            // nop
        }

        return null;
    }

    /**
     * Break down the token into the constituent parts.
     *
     * @param $token
     * @return array
     * @throws Exception
     */
    protected function explodeToken($token)
    {
        $parts = explode('.', $token);

        if (count($parts) <> 3) {
            throw new Exception('invalid token');
        }

        $mac = $parts[0] . '.' . $parts[1];
        $header = $this->decode64($parts[0]);
        $claims = $this->decode64($parts[1]);
        $signature = $this->decode64($parts[2]);

        return [$mac, $header, $claims, $signature];
    }

    /**
     * Extract the Bearer token from an Authentication header.
     *
     * @param Request $request
     * @return false|string
     * @throws Exception
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->header('Authentication');
        if ($token && ! empty($token) && strtolower(substr($token, 0, 8)) === 'bearer: ') {
            return substr($token, 8);
        }

        $token = $request->header('X-Auth');
        if ($token && ! empty($token) && strtolower(substr($token, 0, 8)) === 'bearer: ') {
            return substr($token, 8);
        }

        throw new Exception('could not find authentication header');
    }

    /**
     * Validate aspects of the header after it is decoded.
     *
     * @param $header
     * @throws Exception
     */
    protected function validateHeader($header)
    {
        if ( ! is_array($header)) {
            throw new Exception('header is not an array');
        }

        if ( ! isset($header['typ']) || $header['typ'] !== 'JWT') {
            throw new Exception('header has invalid typ');
        }
    }

    /**
     * Validate claims after they have been decoded.
     *
     * @param $claims
     * @throws Exception
     */
    protected function validateClaims($claims)
    {
        if ( ! is_array($claims)) {
            throw new Exception('claims is not an array');
        }

        foreach (['iss', 'sub', 'exp'] as $item) {
            if ( ! isset($claims[$item])) {
                throw new Exception("claims is missing $item");
            }
        }

        if ($claims['iss'] !== wp_guess_url()) {
            throw new Exception('claims has invalid iss');
        }

        if ($claims['exp'] - time() <= 0) {
            throw new Exception('claims has expired');
        }
    }

    /**
     * Retrieve the user's unique encryption key. This is stored by WordPress on the user meta table.
     * If we do not find one stored, we will generate a new one and save it. This key is kept by us;
     * it should never be given to the user.
     *
     * @param $user
     * @return mixed|string
     */
    protected function getUserKey($user)
    {
        $key = get_user_meta($user->id, 'rest_api_key', true);
        if ($key) {
            return $key;
        }

        $key = $this->generateUserKey();
        add_user_meta($user->id, 'rest_api_key', $key);
        return $key;
    }

    /**
     * Create a sufficiently random key to use as an encryption key for this user.
     *
     * @return string
     * @throws Exception
     */
    protected function generateUserKey()
    {
        return sodium_crypto_auth_keygen();
    }

    /**
     * Generate a signature for the text. This signature is intended to verify that the text has
     * not been modified when we see it again so we base the encryption on a key that we have and
     * the user will not have.
     *
     * @param $text
     * @param $key
     * @return string
     * @throws \SodiumException
     */
    protected function signature($text, $key)
    {
        $mac = substr($text, 0, SODIUM_CRYPTO_AUTH_BYTES);
        return sodium_crypto_auth($mac, $key);
    }

    /**
     * Validate the signature on this encrypted content ($mac) using a key we created specifically
     * for this user.
     *
     * @param $mac
     * @param $signature
     * @param $key
     * @return bool
     * @throws \SodiumException
     * @throws Exception
     */
    protected function validateSignature($mac, $signature, $key)
    {
        $mac = substr($mac, 0, SODIUM_CRYPTO_AUTH_BYTES);

        if (sodium_crypto_auth_verify($mac, $signature, $key)) {
            return true;
        }

        throw new Exception('could not verify signature');
    }

    /**
     * Base64-encode a variable. If it is not already a string, we will convert it to
     * a string by JSON encoding.
     *
     * @param $bytes
     * @return string
     * @throws \SodiumException
     */
    protected function encode64($bytes)
    {
        if ( ! is_scalar($bytes)) {
            $bytes = json_encode($bytes);
        }

        return sodium_bin2base64($bytes, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
    }

    /**
     * Base64-decode a string. If it will JSON-decode, we will return that structure.
     * If not, we return a simple scalar (probably a string).
     *
     * @param $string
     * @param false $echo
     * @return mixed|string|void
     * @throws \SodiumException
     */
    protected function decode64($string, $echo = false)
    {
        $string = sodium_base642bin($string, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
        $struct = json_decode($string, true);

        if (empty($struct)) {
            return $string;
        }

        return $struct;
    }
}
