<?php

namespace Drengr\Tests;
require_once(dirname(__DIR__) . '/Framework/Autoloader.php');

use Drengr\Framework\Application;
use Drengr\Framework\Autoloader;
use Drengr\Framework\Config;
use Drengr\Framework\Container;
use WP_UnitTestCase;

class TestCase extends WP_UnitTestCase
{
    /** @var Container */
    protected $container;
    /** @var Config */
    protected $config;
    /** @var Application */
    protected $application;

    public function setUp()
    {
        parent::setUp();

        Autoloader::initialize();

        $this->container = new Container();
        $this->config = new Config(dirname(__DIR__) . '/config');

        $this->application = new Application(
            ['page' => __FILE__], // todo: confirm this path works
            $this->config,
            $this->container
        );
        $this->application->initialize();

        $this->factory = self::factory();
    }

    /**
     * Convenience routine to instantiate classes from the bindings.
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->container->get($name);
    }
}
