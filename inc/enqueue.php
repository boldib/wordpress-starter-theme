<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Theme
 */

abspath_check();

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function theme_enqueue_scripts() {
	// Enqueue main stylesheet.
	wp_enqueue_style(
		'theme-style',
		THEME_BUILD_URI . 'app.css',
		array(),
		THEME_VERSION
	);

	// Enqueue main JavaScript file.
	wp_enqueue_script(
		'theme-script',
		THEME_BUILD_URI . 'js/app.js',
		array(),
		THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );

/**
 * Enqueue editor styles.
 *
 * @return void
 */
function theme_editor_styles() {
	// Add editor styles with version for cache busting.
	add_editor_style( array(
		THEME_BUILD_URI . 'app.css'
	), null, null, THEME_VERSION );
}
add_action( 'admin_init', 'theme_editor_styles' );
