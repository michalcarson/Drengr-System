<?php

namespace Drengr\Framework;

class Request
{
    protected $get;
    protected $cookie;
    protected $post;
    protected $env;
    protected $server;

    public function initialize()
    {
        $this->get = $_GET;
        $this->cookie = $_COOKIE;
        $this->post = $_POST;
        $this->env = $_ENV;
        $this->server = $_SERVER;

        return $this;
    }

    public function get($name, $default = null)
    {
        return isset($this->get[$name]) ? $this->get[$name] : $default;
    }

    /**
     * Return the parameters that control pagination.
     *
     * @return array
     */
    public function getPageParameters()
    {
        $page = isset($this->get['page']) ? $this->get['page'] : 0;
        $perPage = isset($this->get['perPage']) ? $this->get['perPage'] : 10;

        return compact('page', 'perPage');
    }
}
