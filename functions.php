<?php
/**
 * Theme functions and definitions.
 *
 * @package Theme
 */

/**
 * Check if WordPress ABSPATH is defined.
 * This function can be reused in other PHP files for security.
 *
 * @return void
 */
function abspath_check() {
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
}

// Run the ABSPATH check.
abspath_check();

// Define theme constants.
define( 'THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'THEME_URI', trailingslashit( get_template_directory_uri() ) );
define( 'THEME_BUILD_DIR', THEME_DIR . 'build/' );
define( 'THEME_BUILD_URI', THEME_URI . 'build/' );
define( 'STYLESHEET_DIR', get_stylesheet_directory() );
define( 'STYLESHEET_URI', get_stylesheet_directory_uri() );

/**
 * Include Composer autoloader.
 */
require_once THEME_DIR . 'vendor/autoload.php';

/**
 * Include theme version file.
 */
require_once THEME_DIR . 'versioning.php';

/**
 * Hook the enqueue function to WordPress.
 */
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
