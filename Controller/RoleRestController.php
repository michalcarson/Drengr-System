<?php

namespace Drengr\Controller;

use Drengr\Framework\Traits\AdminAccess;
use Drengr\Framework\Traits\RestController;
use Drengr\Repository\RoleRepository;
use WP_Error;
use WP_REST_Request;

class RoleRestController extends AbstractRestController
{
    use AdminAccess;
    use RestController;

    /** @var RoleRepository */
    private $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;

        $this->namespace = 'drengr/v1';
        $this->rest_base = 'role';
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

}
