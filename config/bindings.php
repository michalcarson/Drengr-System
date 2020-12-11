<?php

use Drengr\App\Admin;
use Drengr\App\Api;
use Drengr\App\Client;
use Drengr\Controller\AuthenticationController;
use Drengr\Controller\CertificationRestController;
use Drengr\Controller\EmailTypeRestController;
use Drengr\Controller\GroupRestController;
use Drengr\Controller\MemberRestController;
use Drengr\Controller\PhoneTypeRestController;
use Drengr\Controller\RankRestController;
use Drengr\Controller\RoleRestController;
use Drengr\Framework\AuthenticationService;
use Drengr\Framework\Container;
use Drengr\Framework\Database;
use Drengr\Framework\ListingFactory;
use Drengr\Framework\Option;
use Drengr\Framework\Request;
use Drengr\Framework\Validator;
use Drengr\Repository\CertificationRepository;
use Drengr\Repository\EmailTypeRepository;
use Drengr\Repository\GroupRepository;
use Drengr\Repository\MemberRepository;
use Drengr\Repository\PhoneTypeRepository;
use Drengr\Repository\RankRepository;
use Drengr\Repository\RoleRepository;
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
            $container->get(CertificationRestController::class),
            $container->get(EmailTypeRestController::class),
            $container->get(GroupRestController::class),
            $container->get(MemberRestController::class),
            $container->get(PhoneTypeRestController::class),
            $container->get(RankRestController::class),
            $container->get(RoleRestController::class),
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

    CertificationRestController::class => function (Container $container) {
        $repository = $container->get(CertificationRepository::class);
        return new CertificationRestController($repository);
    },

    CertificationRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new CertificationRepository($database);
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

    EmailTypeRestController::class => function (Container $container) {
        $repository = $container->get(EmailTypeRepository::class);
        return new EmailTypeRestController($repository);
    },

    EmailTypeRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new EmailTypeRepository($database);
    },

    GroupRestController::class => function (Container $container) {
        $repository = $container->get(GroupRepository::class);
        return new GroupRestController($repository);
    },

    GroupRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new GroupRepository($database);
    },

    MemberRestController::class => function (Container $container) {
        $repository = $container->get(MemberRepository::class);
        $service = $container->get(AuthenticationService::class);
        return new MemberRestController($repository, $service);
    },

    MemberRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new MemberRepository($database);
    },

    ListingFactory::class => function (Container $container) {
        $wpdb = $container->get('wpdb');
        $container->require('class-wp-list-table');

        return new ListingFactory($wpdb);
    },

    Option::class => function (Container $container) {
        return new Option();
    },

    PhoneTypeRestController::class => function (Container $container) {
        $repository = $container->get(PhoneTypeRepository::class);
        return new PhoneTypeRestController($repository);
    },

    PhoneTypeRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new PhoneTypeRepository($database);
    },

    RankRestController::class => function (Container $container) {
        $repository = $container->get(RankRepository::class);
        return new RankRestController($repository);
    },

    RankRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new RankRepository($database);
    },

    Request::class => function (Container $container) {
        $validator = $container->get(Validator::class);
        return (new Request($validator))->initialize();
    },

    RoleRestController::class => function (Container $container) {
        $repository = $container->get(RoleRepository::class);
        return new RoleRestController($repository);
    },

    RoleRepository::class => function (Container $container) {
        $database = $container->get(Database::class);
        return new RoleRepository($database);
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
