<?php

use Drengr\App\Admin;
use Drengr\App\Api;
use Drengr\App\Client;
use Drengr\Controller\AuthenticationController;
use Drengr\Controller\GroupRestController;
use Drengr\Framework\AuthenticationService;
use Drengr\Framework\Container;
use Drengr\Framework\Database;
use Drengr\Framework\ListingFactory;
use Drengr\Framework\Option;
use Drengr\Framework\Request;
use Drengr\Framework\Validator;
use Drengr\Repository\GroupRepository;
use JetRouter\Router;

return [
    'admin' => function (Container $container) {
        $config = [];
        $database = $container->get(Database::class);
        $listingFactory = $container->get(ListingFactory::class);

        return new Admin(
            $config,
            $database,
            $listingFactory
        );
    },

    'public' => function ($container) {
        return new Client();
    },

    'api' => function ($container) {
        $controllers = [
            $container->get(AuthenticationController::class),
            $container->get(GroupRestController::class),
        ];
        return new Api($controllers);
    },

    AuthenticationController::class => function (Container $container) {
        $request = $container->get(Request::class);
        $service = $container->get(AuthenticationService::class);
        return new AuthenticationController('drengr/v1', $request, $service);
    },

    AuthenticationService::class => function (Container $container) {
        return new AuthenticationService();
    },

    Database::class => function (Container $container) {
        $wpdb = $container->get('wpdb');
        $container->require('upgrade');

        $option = $container->get(Option::class);

        $config = $container->get('config')->get('database');

        return new Database(
            $config,
            $wpdb,
            $option
        );
    },

    GroupRestController::class => function (Container $container) {
        $repository = $container->get(GroupRepository::class);

        return new GroupRestController($repository);
    },

    GroupRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new GroupRepository($database);
    },

    ListingFactory::class => function (Container $container) {
        $wpdb = $container->get('wpdb');
        $container->require('class-wp-list-table');

        return new ListingFactory($wpdb);
    },

    Option::class => function (Container $container) {
        return new Option();
    },

    Request::class => function (Container $container) {
        $validator = $container->get(Validator::class);
        return (new Request($validator))->initialize();
    },

    Router::class => function (Container $container) {
        $config = $container->get('config')->get('router.config');

        // work-around until JetRouter recognizes Content_Type: application/json
        if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === 'application/json') {
            $config['outputFormat'] = 'json';
        }

        return Router::create($config);
    },

    Validator::class => function (Container $container) {
        return new Validator();
    },

    'wpdb' => function (Container $container) {
        global $wpdb;
        return $wpdb;
    },
];
