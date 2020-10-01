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

    /** @var Request */
    protected $request;

    /** @var AuthenticationService */
    protected $service;

    /**
     * AuthenticationController constructor.
     * @param string $namespace used by the REST API like "blah-api/v1"
     * @param Request $request
     */
    public function __construct($namespace, Request $request, AuthenticationService $service)
    {
        $this->namespace = $namespace;
        $this->request = $request;
        $this->service = $service;
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

        return $this->service->authenticate($username, $password);
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
            $this->service->validateToken($token);
        } catch (\Exception $e) {
            // nop
            echo $e->getMessage() . "\n";
        }

        return null;
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
}
