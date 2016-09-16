<?php
/**
 * Bootstraps unit tests.
 */

/**
 * Determines where the GlotPress test suite lives.
 */
if ( false !== getenv( 'GP_TESTS_DIR' ) ) {
	define( 'GP_TESTS_DIR', getenv( 'GP_TESTS_DIR' ) );
} else {
	define( 'GP_TESTS_DIR', dirname( dirname( dirname( __DIR__ ) ) ) . '/glotpress/tests/phpunit' );
}

if ( ! file_exists( GP_TESTS_DIR . '/bootstrap.php' ) ) {
	die( "GlotPress test suite could not be found.\n" );
}

require_once GP_TESTS_DIR . '/includes/constants.php';
require_once WP_TESTS_DIR . '/includes/functions.php';

/**
 * Load plugin.
 */
tests_add_filter( 'muplugins_loaded', function () {
	require_once GP_TESTS_DIR . '/includes/loader.php';

	require_once dirname( dirname( __DIR__ ) ) . '/glotpress-json.php';
} );

global $wp_tests_options;
$wp_tests_options['permalink_structure'] = GP_TESTS_PERMALINK_STRUCTURE;

require WP_TESTS_DIR . '/includes/bootstrap.php';

require_once GP_TESTS_DIR . '/lib/testcase.php';
require_once GP_TESTS_DIR . '/lib/testcase-route.php';
require_once GP_TESTS_DIR . '/lib/testcase-request.php';

