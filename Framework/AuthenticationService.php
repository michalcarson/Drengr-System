<?php

namespace Drengr\Framework;

use Exception;
use SodiumException;
use WP_Error;
use WP_REST_Response;
use WP_User;

class AuthenticationService
{
    /**
     * @param $username
     * @param $password
     * @return WP_Error|WP_REST_Response|WP_User
     * @throws SodiumException
     */
    public function authenticate($username, $password)
    {
        $user = get_user_by('login', $username);

        if ($user && ($user instanceof WP_User)) {
            $good = wp_check_password($password, $user->user_pass, $user->id);

            if ($good) {
                return rest_ensure_response([
                    'token' => $this->token($user),
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
     * @throws SodiumException
     */
    public function token(WP_User $user)
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $claims = [
            'iss' => wp_guess_url(),
            'sub' => $user->ID,
            'exp' => time() + (24 * 60 * 60), // 24 hours
        ];

        $payload = $this->encode64($header) . '.' . $this->encode64($claims);

        $key = $this->getUserKey($user);

        $signature = $this->signature($payload, $key);

        return $payload . '.' . $this->encode64($signature);
    }

    /**
     * @param $token
     * @return WP_User
     * @throws SodiumException
     * @throws Exception
     */
    public function validateToken($token)
    {
        list($message, $header, $claims, $signature) = $this->explodeToken($token);

        $this->validateHeader($header);
        $this->validateClaims($claims);

        /** @var WP_User $user */
        $user = WP_User::get_data_by('id', $claims['sub']);

        $key = $this->getUserKey($user);

        $this->validateSignature($message, $signature, $key);

        return $user;
    }

    /**
     * Retrieve the user's unique encryption key. This is stored by WordPress on the user meta table.
     * If we do not find one stored, we will generate a new one and save it. This key is kept by us;
     * it should never be given to the user.
     *
     * @param $user
     * @return mixed|string
     * @throws SodiumException
     * @throws Exception
     */
    protected function getUserKey($user)
    {
        $key = get_user_meta($user->ID, 'rest_api_key', true);
        if ($key) {
            return $this->decode64($key);
        }

        $key = $this->generateUserKey();
        add_user_meta($user->ID, 'rest_api_key', $this->encode64($key));
        return $key;
    }

    /**
     * Create a sufficiently random key to use as an encryption key for this user.
     *
     * @return string
     * @throws Exception
     */
    public function generateUserKey()
    {
        return sodium_crypto_auth_keygen();
    }

    /**
     * Generate a signature for the text. This signature is intended to verify that the text has
     * not been modified when we see it again so we base the encryption on a key that we have and
     * the user will not have.
     *
     * @param $message
     * @param $key
     * @return string
     * @throws SodiumException
     */
    public function signature($message, $key)
    {
        return sodium_crypto_auth($message, $key);
    }

    /**
     * Validate the signature on this encoded content ($message) using a key
     * we created specifically for this user.
     *
     * @param $message
     * @param $signature
     * @param $key
     * @return bool
     * @throws SodiumException
     * @throws Exception
     */
    public function validateSignature($message, $signature, $key)
    {
        if (sodium_crypto_auth_verify($signature, $message, $key)) {
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
     * @throws SodiumException
     */
    public function encode64($bytes)
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
     * @return mixed|string
     * @throws SodiumException
     */
    public function decode64($string)
    {
        $string = sodium_base642bin($string, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
        $struct = json_decode($string, true);

        if (empty($struct)) {
            return $string;
        }

        return $struct;
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

        $message = $parts[0] . '.' . $parts[1];
        $header = $this->decode64($parts[0]);
        $claims = $this->decode64($parts[1]);
        $signature = $this->decode64($parts[2]);

        return [$message, $header, $claims, $signature];
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
}
