<?php
/*
Plugin Name: GP JSON Format support
Plugin URI: https://github.com/swissspidy/glotpress-json
Description: Add JSON file support to GlotPress.
Version: 0.1.0
Author: Pascal Birchler
Author URI: https://pascalbirchler.com
Tags: glotpress, glotpress plugin, translate, json
License: GPLv2 or later
*/

/**
 * Initializes the GlotPres JSON Format plugin.
 *
 * Loads the GP_Format_JSON class and registers the format with GlotPRess.
 *
 * @since 0.1.0
 *
 * @codeCoverageIgnore
 */
function gp_json_format_init() {
	if ( class_exists( 'GP_Format_JSON' ) ) {
		// Probably running GlotPress 2.3.0.
		return;
	}
	
	require_once( __DIR__ . '/includes/class-gp-format-json.php' );
	require_once( __DIR__ . '/includes/class-gp-format-jed1x.php' );

	GP::$formats['json'] = new GP_Format_JSON();
	GP::$formats['jed1x'] = new GP_Format_Jed1x();
}

add_action( 'gp_init', 'gp_json_format_init' );
