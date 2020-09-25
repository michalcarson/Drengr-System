<?php

namespace Drengr\Controller;

use Drengr\Framework\Traits\AdminAccess;
use Drengr\Framework\Traits\RestController;
use Drengr\Repository\RankRepository;
use WP_Error;
use WP_REST_Request;

class RankRestController extends AbstractRestController
{
    use AdminAccess;
    use RestController;

    /** @var RankRepository */
    private $repository;

    public function __construct(RankRepository $repository)
    {
        $this->repository = $repository;

        $this->namespace = 'drengr/v1';
        $this->rest_base = 'rank';
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
