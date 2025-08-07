<?php

// Get block attributes
$categories = isset( $attributes['categories'] ) ? $attributes['categories'] : [];
$tags = isset( $attributes['tags'] ) ? $attributes['tags'] : '';
$posts_per_page = isset( $attributes['postsPerPage'] ) ? intval( $attributes['postsPerPage'] ) : 6;
$enable_pagination = isset( $attributes['enablePagination'] ) ? $attributes['enablePagination'] : false;
$desktop_posts_per_row = isset( $attributes['desktopPostsPerRow'] ) ? intval( $attributes['desktopPostsPerRow'] ) : 3;
$mobile_posts_per_row = isset( $attributes['mobilePostsPerRow'] ) ? intval( $attributes['mobilePostsPerRow'] ) : 1;
$show_excerpt = isset( $attributes['showExcerpt'] ) ? $attributes['showExcerpt'] : true;
$show_featured_image = isset( $attributes['showFeaturedImage'] ) ? $attributes['showFeaturedImage'] : true;
$show_date = isset( $attributes['showDate'] ) ? $attributes['showDate'] : true;

// Setup query args
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$query_args = [ 
	'post_type' => 'post',
	'post_status' => 'publish',
	'posts_per_page' => $posts_per_page,
];

// Add pagination if enabled
if ( $enable_pagination ) {
	$query_args['paged'] = $paged;
}

// Add category filter if set
if ( ! empty( $categories ) ) {
	$query_args['category__in'] = $categories;
}

// Add tag filter if set
if ( ! empty( $tags ) ) {
	// Convert comma-separated string to array of tag slugs
	$tag_slugs = array_map( 'trim', explode( ',', $tags ) );
	if ( ! empty( $tag_slugs ) ) {
		$query_args['tag_slug__in'] = $tag_slugs;
	}
}

// Run the query
$query = new WP_Query( $query_args );

// Set responsive grid classes
$desktop_grid_class = 'grid-cols-' . $desktop_posts_per_row;
$mobile_grid_class = 'grid-cols-' . $mobile_posts_per_row;

// Wrapper classes
$wrapper_classes = get_block_wrapper_attributes( [ 
	'class' => 'wp-block-theme-post-listing'
] );
?>

<div <?php echo $wrapper_classes; ?>>
	<?php if ( $query->have_posts() ) : ?>
		<div class="post-listing-grid <?php echo esc_attr( $mobile_grid_class ); ?> md:<?php echo esc_attr( $desktop_grid_class ); ?>"
			role="feed" aria-label="<?php echo esc_attr__( 'List of posts', 'post-listing' ); ?>">
			<?php while ( $query->have_posts() ) :
				$query->the_post();
				$post_id = get_the_ID();
				$post_title = get_the_title();
				$post_url = get_permalink();
				$post_date = get_the_date();
				?>
				<article class="post-card" id="post-<?php echo esc_attr( $post_id ); ?>" role="article"
					aria-labelledby="title-<?php echo esc_attr( $post_id ); ?>">
					<?php if ( $show_featured_image && has_post_thumbnail() ) :
						$image_id = get_post_thumbnail_id();
						$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						// If no alt text is set, use the post title as fallback
						if ( empty( $image_alt ) ) {
							$image_alt = sprintf( __( 'Featured image for %s', 'post-listing' ), $post_title );
						}
						?>
						<div class="post-featured-image">
							<?php the_post_thumbnail( 'medium', [ 
								'class' => 'post-thumbnail',
								'alt' => $image_alt,
								'loading' => 'lazy'
							] ); ?>
						</div>
					<?php endif; ?>

					<div class="post-content">
						<h3 class="post-title" id="title-<?php echo esc_attr( $post_id ); ?>">
							<a href="<?php echo esc_url( $post_url ); ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'Read more about %s', 'post-listing' ), $post_title ) ); ?>">
								<?php echo esc_html( $post_title ); ?>
							</a>
						</h3>

						<?php if ( $show_excerpt ) : ?>
							<div class="post-excerpt">
								<?php the_excerpt(); ?>
							</div>
						<?php endif; ?>

						<?php if ( $show_date ) : ?>
							<div class="post-meta">
								<time class="post-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( $post_date ); ?>
								</time>
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>

		<?php if ( $enable_pagination && $query->max_num_pages > 1 ) : ?>
			<nav class="post-pagination" aria-label="<?php echo esc_attr__( 'Posts navigation', 'post-listing' ); ?>">
				<?php
				echo paginate_links( [ 
					'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, $paged ),
					'total' => $query->max_num_pages,
					'prev_text' => '<span class="screen-reader-text">' . __( 'Previous page', 'post-listing' ) . '</span>&laquo;',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'post-listing' ) . '</span>&raquo;',
					'aria_current' => 'page',
				] );
				?>
			</nav>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<p role="status"><?php esc_html_e( 'No posts found.', 'post-listing' ); ?></p>
	<?php endif; ?>
</div>