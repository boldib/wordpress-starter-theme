<?php
/**
 * Theme setup functions.
 *
 * @package Theme
 */

abspath_check();

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @return void
 */
function theme_setup() {
	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Add support for block templates.
	add_theme_support( 'block-templates' );

	// Add support for custom spacing.
	add_theme_support( 'custom-spacing' );

	// Add support for custom line-height.
	add_theme_support( 'custom-line-height' );

	// Add support for experimental link color control.
	add_theme_support( 'experimental-link-color' );

	// Add support for custom units.
	add_theme_support( 'custom-units' );

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'starter-theme' ),
			'footer' => esc_html__( 'Footer Menu', 'starter-theme' ),
		)
	);

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for block styles.
	add_theme_support( 'wp-block-styles' );


}
add_action( 'after_setup_theme', 'theme_setup' );

/**
 * Register widget area.
 *
 * @return void
 */
function theme_widgets_init() {
	register_sidebar(
		array(
			'name' => esc_html__( 'Sidebar', 'theme' ),
			'id' => 'sidebar-1',
			'description' => esc_html__( 'Add widgets here.', 'theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'theme_widgets_init' );
