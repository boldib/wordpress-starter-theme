import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl, SelectControl, TextControl, Spinner, Placeholder } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object} props Block props.
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const {
		categories,
		tags,
		postsPerPage,
		enablePagination,
		desktopPostsPerRow,
		mobilePostsPerRow,
		showExcerpt,
		showFeaturedImage,
		showDate
	} = attributes;

	// State for categories and tags
	const availableCategories = useSelect((select) => {
		const { getEntityRecords } = select(coreStore);
		const categoriesData = getEntityRecords('taxonomy', 'category', { per_page: -1 });
		return categoriesData ? categoriesData.map((category) => ({
			value: category.id,
			label: category.name,
		})) : [];
	}, []);

	const { posts, isLoading } = useSelect((select) => {
		const { getEntityRecords } = select('core');

		// Get all tags
		const allTags = getEntityRecords('taxonomy', 'post_tag', { per_page: -1 }) || [];
		const tagOpts = allTags.map(tag => ({ label: tag.name, value: tag.id }));

		// Get posts based on current filters
		const queryArgs = {
			per_page: postsPerPage,
			_embed: true
		};

		if (categories && categories.length > 0) {
			queryArgs.categories = categories;
		}

		// Handle tags as comma-separated string
		if (tags && tags.trim() !== '') {
			// Convert comma-separated string to array of tag slugs
			const tagSlugs = tags.split(',').map(tag => tag.trim());

			// Find tag IDs from slugs using the available tag options
			const tagIds = [];
			tagSlugs.forEach(slug => {
				// Find tag by slug in allTags
				const matchingTag = allTags.find(tag => tag.slug === slug);
				if (matchingTag) {
					tagIds.push(matchingTag.id);
				}
			});

			// Only add tags parameter if we found matching IDs
			if (tagIds.length > 0) {
				queryArgs.tags = tagIds;
			}
		}

		const allPosts = getEntityRecords('postType', 'post', queryArgs);

		return {
			categoryOptions: [{ label: __('Select a category', 'post-listing'), value: 0 }, ...availableCategories],
			tagOptions: [{ label: __('Select a tag', 'post-listing'), value: 0 }, ...tagOpts],
			posts: allPosts,
			isLoading: allPosts === null
		};
	}, [categories, tags, postsPerPage]);

	// Set responsive grid classes
	const desktopGridClass = `grid-cols-${ desktopPostsPerRow }`;
	const mobileGridClass = `grid-cols-${ mobilePostsPerRow }`;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Post Filters', 'starter-theme')} initialOpen={true}>
					<div className="category-select-container">
						<label className="components-base-control__label">
							{__('Add Category', 'starter-theme')}
						</label>
						<SelectControl
							// Always set to "0" to ensure dropdown shows the placeholder
							value="0"
							options={[
								{ value: '0', label: __('Select a category', 'starter-theme') },
								...availableCategories.filter(cat => !categories.includes(cat.value))
							]}
							onChange={(categoryId) => {
								const categoryIdInt = parseInt(categoryId);
								if (categoryIdInt) {
									// Add the category and reset the dropdown
									setAttributes({
										categories: [...categories, categoryIdInt]
									});
									// Force the dropdown to reset to placeholder by triggering a re-render
									setImmediate(() => {
										const select = document.querySelector('.category-select-container select');
										if (select) {
											select.value = '0';
										}
									});
								}
							}}
						/>
					</div>

					{categories.length > 0 && (
						<div className="selected-categories">
							<p>{__('Selected Categories:', 'starter-theme')}</p>
							<ul>
								{categories.map((catId) => {
									const category = availableCategories.find(c => c.value === catId);
									return category ? (
										<li key={catId}>
											{category.label}
											<button className='category-remove-button'
												onClick={() => {
													setAttributes({
														categories: categories.filter(id => id !== catId)
													});
												}}
											>
												{__('Remove', 'starter-theme')}
											</button>
										</li>
									) : null;
								})}
							</ul>
						</div>
					)}

					<TextControl
						label={__('Tags (comma-separated)', 'starter-theme')}
						value={tags}
						onChange={(value) => setAttributes({ tags: value })}
						help={__('Enter tag slugs separated by commas (e.g., news,featured,sports)', 'starter-theme')}
					/>

					<RangeControl
						label={__('Posts Per Page', 'post-listing')}
						value={postsPerPage}
						onChange={(value) => setAttributes({ postsPerPage: value })}
						min={1}
						max={20}
					/>

					<ToggleControl
						label={__('Enable Pagination', 'post-listing')}
						checked={enablePagination}
						onChange={(value) => setAttributes({ enablePagination: value })}
					/>

					<RangeControl
						label={__('Desktop Posts Per Row', 'post-listing')}
						value={desktopPostsPerRow}
						onChange={(value) => setAttributes({ desktopPostsPerRow: value })}
						min={1}
						max={4}
					/>

					<RangeControl
						label={__('Mobile Posts Per Row', 'post-listing')}
						value={mobilePostsPerRow}
						onChange={(value) => setAttributes({ mobilePostsPerRow: value })}
						min={1}
						max={2}
					/>

					<ToggleControl
						label={__('Show Excerpt', 'post-listing')}
						checked={showExcerpt}
						onChange={(value) => setAttributes({ showExcerpt: value })}
					/>

					<ToggleControl
						label={__('Show Featured Image', 'post-listing')}
						checked={showFeaturedImage}
						onChange={(value) => setAttributes({ showFeaturedImage: value })}
					/>

					<ToggleControl
						label={__('Show Date', 'post-listing')}
						checked={showDate}
						onChange={(value) => setAttributes({ showDate: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				{isLoading ? (
					<Placeholder label={__('Loading posts...', 'post-listing')}>
						<Spinner />
					</Placeholder>
				) : !posts || posts.length === 0 ? (
					<p>{__('No posts found. Try adjusting your filters.', 'post-listing')}</p>
				) : (
					<div className={`post-listing-grid ${ mobileGridClass } md:${ desktopGridClass }`}>
						{posts.map(post => (
							<article key={post.id} className="post-card">
								{showFeaturedImage && post._embedded && post._embedded['wp:featuredmedia'] && (
									<div className="post-featured-image">
										<img
											src={post._embedded['wp:featuredmedia'][0].source_url}
											alt={post._embedded['wp:featuredmedia'][0].alt_text || post.title.rendered}
											className="post-thumbnail"
										/>
									</div>
								)}

								<div className="post-content">
									<h3 className="post-title">
										<a href="#">{post.title.rendered}</a>
									</h3>

									{showExcerpt && (
										<div className="post-excerpt"
											dangerouslySetInnerHTML={{ __html: post.excerpt.rendered }}
										/>
									)}

									{showDate && (
										<div className="post-meta">
											<span className="post-date">{post.date ? new Date(post.date).toLocaleDateString() : ''}</span>
										</div>
									)}
								</div>
							</article>
						))}
					</div>
				)}

				{enablePagination && (
					<div className="post-pagination">
						<span className="page-numbers current">1</span>
						<a href="#" className="page-numbers">2</a>
						<a href="#" className="page-numbers">3</a>
						<a href="#" className="page-numbers next">&raquo;</a>
					</div>
				)}
			</div>
		</>
	);
}
