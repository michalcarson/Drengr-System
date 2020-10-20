<?php
defined('ABSPATH') or die('Direct script access disallowed.');

add_action('init', function () {

    add_filter(
        'script_loader_tag',
        function ($tag, $handle) {
            if ( ! preg_match('/^drengr-/', $handle)) {
                return $tag;
            }
            return str_replace(' src', ' async defer src', $tag);
        },
        10,
        2
    );

    add_action('wp_enqueue_scripts', function () {
        $asset_manifest = json_decode(file_get_contents(__DIR__ . '/build/asset-manifest.json'), true)['files'];

        if (isset($asset_manifest['main.css'])) {
            wp_enqueue_style('drengr', get_site_url() . $asset_manifest['main.css']);
        }

        wp_enqueue_script('drengr-runtime', get_site_url() . $asset_manifest['runtime-main.js'], [], null, true);

        wp_enqueue_script('drengr-main', get_site_url() . $asset_manifest['main.js'], ['drengr-runtime'], null, true);

        foreach ($asset_manifest as $key => $value) {
            if (preg_match('@static/js/(.*)\.chunk\.js@', $key, $matches)) {
                if ($matches && is_array($matches) && count($matches) === 2) {
                    $name = 'drengr-' . preg_replace('/[^A-Za-z0-9_]/', '-', $matches[1]);
                    wp_enqueue_script($name, get_site_url() . $value, ['drengr-main'], null, true);
                }
            }

            if (preg_match('@static/css/(.*)\.chunk\.css@', $key, $matches)) {
                if ($matches && is_array($matches) && count($matches) == 2) {
                    $name = 'drengr-' . preg_replace('/[^A-Za-z0-9_]/', '-', $matches[1]);
                    wp_enqueue_style($name, get_site_url() . $value, ['drengr'], null);
                }
            }
        }

        // for material-ui
        wp_enqueue_style('drengr-roboto', "https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap");
        add_action('wp_head', function () {
            echo "<meta
                name=\"viewport\"
                content=\"minimum-scale=1, initial-scale=1, width=device-width\"
            />";
        });
    });
});
