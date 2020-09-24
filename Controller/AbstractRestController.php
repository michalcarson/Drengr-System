<?php

namespace Drengr\Controller;

use WP_Error;
use WP_HTTP_Response;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class AbstractRestController extends WP_REST_Controller
{
    public function register()
    {
        // nop
    }

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            $this->rest_base,
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
            $this->rest_base . '/(?P<id>[\w-]+)',
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
                    'methods' => 'PUT, PATCH',
                    'callback' => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args' => $this->get_endpoint_args_for_item_schema('PUT, PATCH'),
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

    /**
     * Return the parameters that control pagination.
     *
     * @param WP_REST_Request $request
     * @return array
     */
    protected function getPageParameters(WP_REST_Request $request)
    {
        $page = $this->getRequestedPageNumber($request);
        $perPage = $this->getRequestedPerPage($request);

        return compact('page', 'perPage');
    }

    protected function getRequestedPerPage(WP_REST_Request $request)
    {
        return empty($request->get_param('per_page')) ? 10 : $request->get_param('per_page');
    }

    protected function getRequestedPageNumber(WP_REST_Request $request)
    {
        return empty($request->get_param('page')) ? 0 : $request->get_param('page');
    }

    /**
     * Set headers to let the Client Script be aware of the pagination.
     *
     * @param WP_REST_Response $response
     * @param int $total The number of found items
     * @param int $perPage The number of items per page
     * @return WP_REST_Response $response
     */
    protected function addTotalHeaders(WP_REST_Response $response, int $total, int $perPage)
    {
        if ( ! $total || ! $perPage) {
            return $response;
        }

        $totalPages = ceil($total / $perPage);

        $response->header('X-WP-Total', $total);
        $response->header('X-WP-TotalPages', $totalPages);

        return $response;
    }

    /**
     * @param array $items
     * @param WP_REST_Request $request
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response
     */
    protected function buildResponse(array $items, WP_REST_Request $request)
    {
        $responseData = [];

        foreach ($items as $item) {
            $responseData[] = $this->prepare_response_for_collection(
                $this->prepare_item_for_response($item, $request)
            );
        }

        return rest_ensure_response($responseData);
    }
}
