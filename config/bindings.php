<?php
return [
    'admin' => function (\Drengr\Framework\Container $container) {
        $config = [];
        $database = $container->get(\Drengr\Framework\Database::class);

        return new \Drengr\App\Admin(
            $config,
            $database
        );
    },

    'public' => function ($container) {
        return new \Drengr\App\Client();
    },

    \Drengr\Framework\Database::class => function (\Drengr\Framework\Container $container) {
        $wpdb = $container->get('wpdb');
        $container->require('upgrade');
        $config = $container->get('config')->get('database');

        return new \Drengr\Framework\Database(
            $config,
            $wpdb,
        );
    },

    'wpdb' => function (\Drengr\Framework\Container $container) {
        global $wpdb;
        return $wpdb;
    }
];
