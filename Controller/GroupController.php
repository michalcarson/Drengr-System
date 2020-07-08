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
        try {
            return $this->repository->getAll(
                $this->request->getPageParameters()
            );
        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function create()
    {
        try {
            return $this->repository->create(
                $this->request->getValidatedParameters()
            );
        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function read($id)
    {
        try {
            return $this->repository->find($id);
        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function update($id)
    {
        try {
            return $this->repository->update(
                $id,
                $this->request->getValidatedParameters()
            );
        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function patch($id)
    {
        try {
            return $this->repository->update(
                $id,
                $this->request->getValidatedParameters()
            );
        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            return [
                'rows' => $this->repository->delete($id)
            ];
        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

}
