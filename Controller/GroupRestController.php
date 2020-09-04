<?php

namespace Drengr\Controller;

use Drengr\Exception\ModelNotFoundException;
use Drengr\Repository\GroupRepository;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class GroupRestController extends AbstractRestController
{
    /** @var GroupRepository */
    private $repository;

    public function __construct(GroupRepository $repository)
    {
        $this->repository = $repository;

        $this->namespace = 'drengr/v1';
        $this->rest_base = '/group';
    }

    /**
     * Checks if a given request has access to get items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check($request)
    {
        return true; // @TODO $this->userIsLoggedIn();
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     * @since 4.7.0
     */
    public function get_items($request)
    {
        try {
            $items = $this->repository->getAll(
                $this->getPageParameters($request)
            );

            $response = $this->buildResponse($items, $request);

            $response = $this->addTotalHeaders(
                $response,
                count($items),
                $this->getRequestedPerPage($request)
            );

            return $response;
        } catch (\Exception $e) {
            return new WP_Error('Exception', $e->getMessage(), ['status' => empty($e->getCode()) ? 500 : $e->getCode()]);
        }
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
        return true; // @TODO $this->userIsLoggedIn();
    }

    /**
     * Retrieves one item from the collection.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     * @since 4.7.0
     */
    public function get_item($request)
    {
        try {
            $id = $request->get_param('id');
            $item = $this->repository->findOrFail($id);

            return $this->buildResponse([$item], $request);
        } catch (ModelNotFoundException $e) {
            return new WP_Error('Not Found', 'Group was not found.', ['status' => 404]);
        } catch (\Exception $e) {
            return new WP_Error('Exception', $e->getMessage(), ['status' => empty($e->getCode()) ? 500 : $e->getCode()]);
        }
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
        return true; // @TODO $this->userIsAdmin();
    }

    /**
     * Creates one item from the collection.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     * @since 4.7.0
     */
    public function create_item($request)
    {
        try {
            $item = $this->repository->create($request->get_params());

            return $this->buildResponse([$item], $request);
        } catch (\Exception $e) {
            return new WP_Error('Exception', $e->getMessage(), ['status' => empty($e->getCode()) ? 500 : $e->getCode()]);
        }
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
        return true; // @TODO $this->userIsAdmin();
    }

    /**
     * Updates one item from the collection.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     * @since 4.7.0
     */
    public function update_item($request)
    {
        try {
            $id = $request->get_param('id');
            $item = $this->repository->update($id, $request->get_params());

            return $this->buildResponse([$item], $request);
        } catch (\Exception $e) {
            return new WP_Error('Exception', $e->getMessage(), ['status' => empty($e->getCode()) ? 500 : $e->getCode()]);
        }
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
        return true; // @TODO $this->userIsAdmin();
    }

    /**
     * Deletes one item from the collection.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     * @since 4.7.0
     */
    public function delete_item($request)
    {
        try {
            $id = $request->get_param('id');
            $data = [
                'rows' => $this->repository->delete($id)
            ];

            $response = $this->buildResponse([], $request);
            $response->set_data($data);

            return $response;
        } catch (\Exception $e) {
            return new WP_Error('Exception', $e->getMessage(), ['status' => empty($e->getCode()) ? 500 : $e->getCode()]);
        }
    }

    /**
     * Prepares one item for create or update operation.
     *
     * @param WP_REST_Request $request Request object.
     * @return object|WP_Error The prepared item, or WP_Error object on failure.
     * @since 4.7.0
     */
    protected function prepare_item_for_database($request)
    {
        return (object) ['pretend' => 'stuff'];
    }

    /**
     * Prepares the item for the REST response.
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     * @since 4.7.0
     */
    public function prepare_item_for_response($item, $request)
    {
        return new WP_REST_Response((array) $item);
    }
}
