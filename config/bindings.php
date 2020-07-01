<?php

use Drengr\App\Admin;
use Drengr\App\Client;
use Drengr\Controller\GroupController;
use Drengr\Framework\Container;
use Drengr\Framework\Database;
use Drengr\Framework\ListingFactory;
use Drengr\Framework\Option;
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

    GroupController::class => function (Container $container) {
        return new GroupController();
    },

    ListingFactory::class => function (Container $container) {
        $wpdb = $container->get('wpdb');
        $container->require('class-wp-list-table');

        return new ListingFactory($wpdb);
    },

    Option::class => function (Container $container) {
        return new Option();
    },

    Router::class => function (Container $container) {
        $config = $container->get('config')->get('router.config');
        return Router::create($config);
    },

    'wpdb' => function (Container $container) {
        global $wpdb;
        return $wpdb;
    },
];
