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

        return $this;
    }

    public function run()
    {
        $router = $this->container->get(JetRouter::class);

        $router->get('group/edit/{id}', 'edit_group', function($id) {
            echo "edit group $id<br>";
        });
    }
}
