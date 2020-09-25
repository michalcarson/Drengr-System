<?php

/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
if ( defined( 'WP_RUN_CORE_TESTS' ) && WP_RUN_CORE_TESTS ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/build/' );
} else {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
define( 'WP_DEFAULT_THEME', 'default' );

/*
 * Test with multisite enabled.
 * Alternatively, use the tests/phpunit/multisite.xml configuration file.
 */
// define( 'WP_TESTS_MULTISITE', true );

/*
 * Force known bugs to be run.
 * Tests with an associated Trac ticket that is still open are normally skipped.
 */
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //

/*
 * This configuration file will be used by the copy of WordPress being tested.
 * wordpress/wp-config.php will be ignored.
 *
 * WARNING WARNING WARNING!
 * These tests will DROP ALL TABLES in the database with the prefix named below.
 * DO NOT use a production database or one that is shared with something else.
 */

define( 'DB_NAME', 'wordpress_test' );
define( 'DB_USER', 'wordpress' );
define( 'DB_PASSWORD', 'wordpress' );
define( 'DB_HOST', 'vikingsna_mysql' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY',         '@6pJ%f_e*+YT(|KM()D:q3dS)|TEH)tr-W:j+^F+;-DT|6Iyt(U.a)7J@_jfsr[o');
define('SECURE_AUTH_KEY',  'F^,Q_hrL[4(Sxw!y A{-2gA~yq{q/Qu !_&fC!k?-$+ h&Iu{XTVVew-0s*!p;6w');
define('LOGGED_IN_KEY',    '#Nz#alS0PHh?r#lH1V$07Q|t1)^|l(^Rp@<6#Dbx_!;uBef@5 5=w6.{u@Sf|U|,');
define('NONCE_KEY',        '?)YI/x@lZa}yL/9j6qA|rDTE^28j1~TO)R#64E]t&(t4mx M|L4/w6+e^Z-~~)@e');
define('AUTH_SALT',        'T|FZ-z*XLeZK`i6C<}-bOQD=pA/%/LmKf7pW6gI_#Ni-wYw&jSV]-?+r$pH%oXW4');
define('SECURE_AUTH_SALT', 'rHWQEOOWVi8O,xK9F[~xVI;e-9(]I(?!b;^ IEU|0;xEK j}b]wg]&|co*N&25H?');
define('LOGGED_IN_SALT',   'm2:z~{z pj)NQBYQUNg*y)AlR0Ddb lWhU:avs`.ablG/aO}VPIKR{PX<1(C&yn*');
define('NONCE_SALT',       'h]Q*;Z~|qiYRULLbBN[elIMsW9E]ZEm-5~ix`0KW@)tnppe/mwiG=cgFSl/@Yz]I');

$table_prefix = 'wptests_';   // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
