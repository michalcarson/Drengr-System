<?php

namespace Drengr\Controller;

use Drengr\Repository\GroupRepository;
use Drengr\Request\GroupRequest;

class GroupController extends BaseController
{
    /** @var GroupRequest */
    private $request;

    /** @var GroupRepository */
    private $repository;

    public function __construct(GroupRequest $request, GroupRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->getAll(
            $this->request->getPageParameters()
        );
    }

    public function create()
    {
        return $this->repository->create(
            $this->request->getValidatedParameters()
        );
    }

    public function read($id)
    {
        return $this->repository->find($id);
    }

    public function update($id)
    {
        echo __METHOD__ . '<br>';
    }

    public function patch($id)
    {
        echo __METHOD__ . '<br>';
    }

    public function delete($id)
    {
        echo __METHOD__ . '<br>';
    }

}
