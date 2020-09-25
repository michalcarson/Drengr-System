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
    public function setUp()
    {
        parent::setUp();

        Autoloader::initialize();
        $this->application = new Application(
            ['page' => __FILE__], // todo: confirm this path works
            new Config(dirname(__DIR__) . '/config'),
            new Container()
        );
    }
}
