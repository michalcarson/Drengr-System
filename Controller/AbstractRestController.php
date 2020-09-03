<?php

namespace Drengr\Controller;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

class AbstractRestController extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args' => $this->get_collection_params(),
                ],
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                ],
                'schema' => [$this, 'get_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\w-]+)',
            [
                'args' => [
                    'id' => [
                        'description' => __('Numeric identifier of a user ID.', $this->namespace),
                        'type' => 'integer',
                    ],
                ],
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::READABLE),
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
                ],
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'delete_item'],
                    'permission_callback' => [$this, 'delete_item_permissions_check'],
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::DELETABLE),
                ],
                'schema' => [$this, 'get_item_schema'],
            ]
        );
    }

    protected function userIsLoggedIn()
    {
        if (is_user_logged_in()) {
            return true;
        }

        return new WP_Error(
            401,
            __('You must be logged in to perform this action.'),
            ['status' => 401]
        );
    }

    protected function userIsAdmin()
    {
        $loggedIn = $this->userIsLoggedIn();

        if ($loggedIn !== true) {
            return $loggedIn;
        }

        return true; // TODO: check for admin permissions
    }
}
