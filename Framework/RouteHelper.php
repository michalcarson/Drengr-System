<?php

namespace Drengr\Framework;

use \JetRouter\Router as JetRouter;

class RouteHelper
{
    /** @var array */
    protected static $config;

    /** @var Container */
    protected static $container;

    /** @var JetRouter */
    protected static $router;

    /** @var string[] These HTTP verbs will map to the designated method within a resource controller. */
    protected static $methods = [
        'POST' => 'create',
        'GET' => 'read',
        'PUT' => 'update',
        'PATCH' => 'patch',
        'DELETE' => 'delete'
    ];

    public static function init(array $config, Container $container, JetRouter $router)
    {
        self::$config = $config;
        self::$container = $container;
        self::$router = $router;
    }

    /**
     * Add full set of REST routes for a resource.
     *
     * @param string $routePrefix  name of the entity (usually) that will be created/updated/deleted
     * @param string $controllerName   name of the controller class
     * @param array $except list of HTTP methods that should be omitted
     */
    public static function resource($routePrefix, $controllerName, array $except = [])
    {
        // JetRouter will pre-pend the namespace to this path. Namespace
        // is defined in the `config/router.php` file.
        $basePath = $routePrefix;

        // Most of the REST methods are similar enough that we can process them
        // in a loop. "List" is an exception and we want that to come after the rest.
        foreach (self::$methods as $method => $name) {
            if (in_array($method, $except)) {
                continue;
            }

            $routeName = $name . '_' . $routePrefix;

            $routePath = $basePath;
            if ($method !== 'POST') {
                $routePath .= '/{id}';
            }

            self::$router->addRoute($method, $routePath, $routeName, function () use ($controllerName, $name) {
                $args = func_get_args();
                $controller = self::$container->get($controllerName);
                return call_user_func_array([$controller, $name], $args);
            });
        }

        // Add the "list" method last so that it doesn't take priority over the "read" method.
        self::$router->get($basePath, 'list_' . $routePrefix, function () use ($controllerName, $routePrefix) {
            $controller = self::$container->get($controllerName);
            return call_user_func([$controller, 'index']);
        });
    }
}
