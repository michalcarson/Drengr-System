<?php

namespace Drengr\Controller;

class GroupController extends BaseController
{
    public function index()
    {
        echo __METHOD__ . '<br>';
    }

    public function create()
    {
        echo __METHOD__ . '<br>';
    }

    public function read($id)
    {
        echo __METHOD__ . '<br>id = ' . $id;
    }

    public function update($id)
    {
        echo __METHOD__ . '<br>';
    }

    public function patch()
    {
        echo __METHOD__ . '<br>';
    }

    public function delete()
    {
        echo __METHOD__ . '<br>';
    }

}
