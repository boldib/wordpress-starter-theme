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
 * Include Composer autoloader or show public notice if it doesn't exist.
 */
$autoload_file = THEME_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload_file ) ) {
	require_once $autoload_file;
} else {
	// Add public notice if autoloader is missing
	add_action( 'wp_body_open', function () {
		?>
		<div class="theme-error-notice"
			style="background-color: #f44336; color: white; padding: 15px; margin-bottom: 15px; text-align: center;">
			<p><?php _e( '<strong>Theme Error:</strong> Composer dependencies not found. Please run <code>composer install</code> in the theme directory.', 'starter-theme' ); ?>
			</p>
		</div>
		<?php
	} );
	// Also keep admin notice
	add_action( 'admin_notices', function () {
		?>
		<div class="notice notice-error">
			<p><?php _e( '<strong>Theme Error:</strong> Composer dependencies not found. Please run <code>composer install</code> in the theme directory.', 'starter-theme' ); ?>
			</p>
		</div>
		<?php
	} );
}

/**
 * Include theme version file.
 */
require_once THEME_DIR . 'versioning.php';