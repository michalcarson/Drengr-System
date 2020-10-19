<?php
/*
Plugin Name: Drengr System
Description: Track achievements for Vikings of North America members
Author: Michal Carson
Version: 0.0.1
Requires PHP: 7.2.5
*/
defined( 'ABSPATH' ) or die( 'Direct script access disallowed.' );

/*
 * Set up our autoloader. Have to load this one file with a "require" but
 * everything else will be auto-loaded for us.
 */
require_once (__DIR__ . '/Framework/Autoloader.php');
\Drengr\Framework\Autoloader::initialize();

/*
 * Create our top level application (the plugin) which will register
 * menus and event listeners.
 */
$application = new \Drengr\Framework\Application(
    ['page' => __FILE__],
    new \Drengr\Framework\Config(__DIR__ . '/config'),
    new \Drengr\Framework\Container()
);

$application->initialize()->run();
