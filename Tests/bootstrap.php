<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Drengr
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/index.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

/*

 Installation notes:

 docker-compose exec vikingsna_mysql bash
    mysql -u root -p
       create database wordpress_test;
       grant all privileges on wordpress_test.* to 'wordpress'@'%';
       flush privileges;

 docker-compose exec vikingsna_org bash
    apt-get update
    apt-get install wget subversion
    mkdir /tmp/wordpress-tests-lib
    cp ./wp-tests-config.php /tmp/wordpress-tests-lib/wp-tests-config.php
    wget -nv -O /tmp/wordpress-tests-lib/wordpress.tar.gz https://wordpress.org/latest.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress-tests-lib/wordpress.tar.gz -C /tmp/wordpress-tests-lib
    svn co https://develop.svn.wordpress.org/tags/5.5.1/tests/phpunit/includes/ /tmp/wordpress-tests-lib/includes
    svn co https://develop.svn.wordpress.org/tags/5.5.1/tests/phpunit/data/ /tmp/wordpress-tests-lib/data
    wget -O phpunit https://phar.phpunit.de/phpunit-7.phar
    chmod +x phpunit
    ./phpunit


 */
