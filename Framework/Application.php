<?php

namespace Drengr\Framework;

use JetRouter\Router as JetRouter;

class Application
{
    /** @var Config */
    private $config;

    /** @var Container */
    private $container;

    /** @var JetRouter */
    private $router;

    /**
     * The name of the bootstrap file for this application. We need
     * this to register activation, deactivation and uninstall hooks.
     * @var string
     */
    protected $page;

    public function __construct(array $config, Config $configClass, Container $container)
    {
        $this->page = $config['page'];
        $this->config = $configClass;
        $this->container = $container;
    }

    public function initialize()
    {
        $this->config->initialize();
        $this->container->set('config', $this->config);

        $this->container->load($this->config->get('bindings'));

        $admin = $this->container->get('admin');
        $admin->register($this->page);

        $public = $this->container->get('public');
        $public->register();

        $api = $this->container->get('api');
        $api->register();

        return $this;
    }

    public function run()
    {
        $this->router = $this->container->get(JetRouter::class);

        RouteHelper::init($this->config->get('router.config'), $this->container, $this->router);

        $routes = $this->config->get('router.routes');
        $routes($this->router, $this->container);
    }
}
