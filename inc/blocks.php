<?php

abspath_check();

/**
 * Register custom blocks with I/O optimization using WordPress Transients API.
 *
 * This function scans the build/blocks directory and registers each block
 * with its associated styles. It uses WordPress Transients API for caching
 * to improve performance by avoiding filesystem operations on every request.
 *
 * @return void
 */
function theme_register_blocks() {
	$blocks_dir = STYLESHEET_DIR . '/build/blocks';
	$cache_key = 'theme_blocks_data_' . THEME_VERSION;

	// Validate blocks directory exists.
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	// Try to get cached data first (only in production).
	if ( ! WP_DEBUG ) {
		$blocks_data = get_transient( $cache_key );
		if ( false !== $blocks_data ) {
			register_cached_blocks( $blocks_data );
			return;
		}
	}

	// Process blocks normally.
	$dirs = glob( $blocks_dir . '/*', GLOB_ONLYDIR );
	if ( false === $dirs ) {
		return;
	}

	$blocks_data = array();

	foreach ( $dirs as $dir ) {
		// Sanitize directory path.
		$real_dir = realpath( $dir );
		if ( false === $real_dir || 0 !== strpos( $real_dir, $blocks_dir ) ) {
			continue; // Skip invalid paths
		}

		$block_name = basename( $dir );
		$style_name = 'block-' . sanitize_key( $block_name ) . '-style';
		$style_path = $dir . '/style.css';
		$style_uri = get_template_directory_uri() . '/build/blocks/' . $block_name . '/style.css';

		// Check if the style file exists and is readable.
		$style_exists = file_exists( $style_path ) && is_readable( $style_path );

		if ( $style_exists ) {
			wp_register_style(
				$style_name,
				$style_uri,
				array(),
				THEME_VERSION
			);
		}

		// Validate metadata file exists before registration.
		$metadata_file = $dir . '/block.json';
		if ( file_exists( $metadata_file ) && is_readable( $metadata_file ) ) {
			register_block_type_from_metadata(
				$dir,
				array(
					'style' => $style_exists ? $style_name : null,
				)
			);
		}

		// Store data for caching.
		$blocks_data[] = array(
			'dir' => $real_dir,
			'style_name' => $style_name,
			'style_uri' => $style_uri,
			'style_exists' => $style_exists
		);
	}

	// Cache the results (only in production).
	if ( ! WP_DEBUG && ! empty( $blocks_data ) ) {
		set_transient( $cache_key, $blocks_data, 12 * HOUR_IN_SECONDS );
	}
}
add_action( 'init', 'theme_register_blocks' );

/**
 * Register blocks from cached data.
 *
 * @param array $blocks_data Array of block data from cache.
 * @return void
 */
function register_cached_blocks( $blocks_data ) {
	foreach ( $blocks_data as $data ) {
		// Validate cached data
		if ( ! is_array( $data ) || ! isset( $data['dir'] ) ) {
			continue;
		}

		if ( $data['style_exists'] ) {
			wp_register_style(
				$data['style_name'],
				$data['style_uri'],
				array(),
				THEME_VERSION
			);
		}

		// Validate block directory exists before registration
		if ( is_dir( $data['dir'] ) ) {
			// Validate metadata file exists
			$metadata_file = $data['dir'] . '/block.json';
			if ( file_exists( $metadata_file ) && is_readable( $metadata_file ) ) {
				register_block_type_from_metadata(
					$data['dir'],
					array(
						'style' => $data['style_exists'] ? $data['style_name'] : null,
					)
				);
			}
		}
	}
}

/**
 * Enqueue Tailwind CSS for block editor styles.
 */
function theme_enqueue_block_assets() {
	wp_enqueue_style(
		'tailwind',
		get_template_directory_uri() . '/build/css/main.css',
		array(),
		THEME_VERSION
	);
}
add_action( 'enqueue_block_assets', 'theme_enqueue_block_assets' );

/**
 * Register Theme Blocks category.
 */
function theme_register_custom_block_category( $categories ) {
	$custom_category = array(
		'slug' => 'theme-blocks',
		'title' => __( 'Theme Blocks', 'starter-theme' ),
		'icon' => null,
	);

	array_unshift( $categories, $custom_category );

	return $categories;
}
add_filter( 'block_categories_all', 'theme_register_custom_block_category', 10, 1 );
