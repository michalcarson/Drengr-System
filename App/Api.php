<?php

namespace Drengr\App;

class Api
{
    /**
     * @var array
     */
    private $controllers;

    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    public function register()
    {
        add_action('rest_api_init', [$this, 'registerControllers']);
    }

    public function registerControllers()
    {
        foreach ($this->controllers as $controller) {
            $controller->register_routes();
        }
    }
}
