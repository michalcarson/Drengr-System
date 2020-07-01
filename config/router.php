<?php

use Drengr\Controller\GroupController;
use Drengr\Framework\Container;
use Drengr\Framework\RouteHelper;
use JetRouter\Router as JetRouter;

return [
    'config' => [
        'namespace' => 'drengr',
    ],
    'routes' => function (JetRouter $router, Container $container) {
        RouteHelper::resource('group', GroupController::class);
    }
];
