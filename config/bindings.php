<?php
return [
    'admin' => function (\Drengr\Framework\Container $container) {
        $config = [];
        $database = $container->get(\Drengr\Framework\Database::class);
        $listingFactory = $container->get(\Drengr\Framework\ListingFactory::class);

        return new \Drengr\App\Admin(
            $config,
            $database,
            $listingFactory
        );
    },

    'public' => function ($container) {
        return new \Drengr\App\Client();
    },

    \Drengr\Framework\Database::class => function (\Drengr\Framework\Container $container) {
        $wpdb = $container->get('wpdb');
        $container->require('upgrade');

        $option = $container->get(\Drengr\Framework\Option::class);

        $config = $container->get('config')->get('database');

        return new \Drengr\Framework\Database(
            $config,
            $wpdb,
            $option
        );
    },

    \Drengr\Framework\ListingFactory::class => function (\Drengr\Framework\Container $container) {
        $wpdb = $container->get('wpdb');
        $wp_col_headers = $container->get('wp_col_headers');
        $container->require('class-wp-list-table');

        return new \Drengr\Framework\ListingFactory($wpdb, $wp_col_headers);
    },

    \Drengr\Framework\Option::class => function (\Drengr\Framework\Container $container) {
        return new \Drengr\Framework\Option();
    },

    'wpdb' => function (\Drengr\Framework\Container $container) {
        global $wpdb;
        return $wpdb;
    },

    'wp_col_headers' => function (\Drengr\Framework\Container $container) {
        global $_wp_col_headers;
        return $_wp_col_headers;
    }
];
