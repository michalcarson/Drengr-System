<?php

namespace Drengr\Framework\Traits;

use WP_Error;
use WP_REST_Request;

trait AdminAccess
{
    /**
     * Checks if a given request has access to get items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check($request)
    {
        return $this->userIsLoggedIn();
    }

    /**
     * Checks if a given request has access to get a specific item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
     * @since 4.7.0
     */
    public function get_item_permissions_check($request)
    {
        return $this->userIsLoggedIn();
    }

    /**
     * Checks if a given request has access to create items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     * @since 4.7.0
     */
    public function create_item_permissions_check($request)
    {
        return $this->userIsAdmin();
    }

    /**
     * Checks if a given request has access to update a specific item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
     * @since 4.7.0
     */
    public function update_item_permissions_check($request)
    {
        return $this->userIsAdmin();
    }

    /**
     * Checks if a given request has access to delete a specific item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
     * @since 4.7.0
     */
    public function delete_item_permissions_check($request)
    {
        return $this->userIsAdmin();
    }

}
