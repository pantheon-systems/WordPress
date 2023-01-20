<?php

$all_post_types = [
	'post' => __('Posts', 'blocksy-companion')
];

if (class_exists('WooCommerce')) {
	$all_post_types['product'] = __('Products', 'blocksy-companion');
}

if (function_exists('blocksy_manager')) {
	$post_types = blocksy_manager()->post_types->get_supported_post_types();

	foreach ($post_types as $single_post_type) {
		$post_type_object = get_post_type_object($single_post_type);

		if (! $post_type_object) {
			continue;
		}

		$all_post_types[
			$single_post_type
		] = $post_type_object->labels->singular_name;
	}
}

$cpt_options = [];

foreach ($all_post_types as $custom_post_type => $label) {
	if ($custom_post_type === 'page') {
		continue;
	}

	$opt_id = 'trending_block_category';
	$label = __('Category', 'blocksy-companion');
	$label_multiple = __('All categories', 'blocksy-companion');
	$taxonomy = 'category';

	if ($custom_post_type !== 'post') {
		$opt_id = 'trending_block_' . $custom_post_type . '_taxonomy';
		$label = __('Taxonomy', 'blocksy-companion');
		$label_multiple = __('All taxonomies', 'blocksy-companion');

		$taxonomies = get_object_taxonomies($custom_post_type);

		if (count($taxonomies) > 0) {
			$taxonomy = $taxonomies;
		} else {
			$taxonomy = 'nonexistent';
		}
	}

	$categories = get_terms([
		'taxonomy' => $taxonomy,
		// 'post_type' => $custom_post_type,
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => false
	]);

	$category_choices = [
		'all_categories' => $label_multiple
	];

	if (! is_wp_error($categories)) {
		foreach ($categories as $category) {
			$category_choices[$category->term_id] = $category->name;
		}
	}

	$cpt_options[blocksy_rand_md5()] = [
		'type' => 'ct-condition',
		'condition' => [
			'trending_block_post_type' => $custom_post_type,
			'trending_block_post_source' => '!custom'
		],
		'options' => [
			$opt_id => [
				'type' => 'ct-select',
				'label' => $label,
				'value' => 'all_categories',
				'choices' => blocksy_ordered_keys($category_choices),
				'design' => 'inline',
				'sync' => [
					'selector' => '.ct-trending-block',
					'render' => function () {
						echo blc_get_trending_block();
					}
				],
			],
		]
	];
}

$options = [
	//  translators: This is a brand name. Preferably to not be translated
	'title' => _x('Trending Posts', 'Extension Brand Name', 'blocksy-companion'),
	'container' => [ 'priority' => 8 ],
	'options' => [
		'trending_posts_section_options' => [
			'type' => 'ct-options',
			'setting' => [ 'transport' => 'postMessage' ],
			'inner-options' => [
				blocksy_rand_md5() => [
					'type' => 'ct-title',
					'label' => __( 'Trending Posts', 'blocksy-companion' ),
				],

				blocksy_rand_md5() => [
					'title' => __( 'General', 'blocksy-companion' ),
					'type' => 'tab',
					'options' => [
						[
							'trending_block_label' => [
								'label' => __( 'Module Title', 'blocksy-companion' ),
								'type' => 'text',
								'design' => 'inline',
								'value' => __( 'Trending now', 'blocksy-companion' ),
								'sync' => 'live',
							],

							'trending_block_label_tag' => [
								'label' => __( 'Module Title Tag', 'blocksy-companion' ),
								'type' => 'ct-select',
								'value' => 'h3',
								'view' => 'text',
								'design' => 'inline',
								'divider' => 'bottom:full',
								'choices' => blocksy_ordered_keys(
									[
										'h1' => 'H1',
										'h2' => 'H2',
										'h3' => 'H3',
										'h4' => 'H4',
										'h5' => 'H5',
										'h6' => 'H6',
										'span' => 'span',
									]
								),
								'sync' => [
									'selector' => '.ct-trending-block',
									'render' => function () {
										echo blc_get_trending_block();
									}
								]
							],

							'trending_block_post_type' => count($all_post_types) > 1 ? [
								'label' => __( 'Post Type', 'blocksy-companion' ),
								'type' => 'ct-select',
								'value' => 'post',
								'design' => 'inline',
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => blocksy_ordered_keys($all_post_types),
								'sync' => [
									'selector' => '.ct-trending-block',
									'render' => function () {
										echo blc_get_trending_block();
									}
								],
							] : [
								'label' => __('Post Type', 'blocksy-companion'),
								'type' => 'hidden',
								'value' => 'post',
								'design' => 'none',
								'setting' => ['transport' => 'postMessage'],
							],

							'trending_block_post_source' => [
								'type' => 'ct-select',
								'label' => __( 'Source', 'blocksy-companion' ),
								'value' => 'categories',
								'design' => 'inline',
								'choices' => blocksy_ordered_keys(
									[
										'categories' => __('Taxonomies', 'blocksy-companion'),
										'custom' => __( 'Custom Query', 'blocksy-companion' ),
									]
								),
								'sync' => [
									'selector' => '.ct-trending-block',
									'render' => function () {
										echo blc_get_trending_block();
									}
								],
							],
						],

						$cpt_options,

						[
							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => [
									'trending_block_post_source' => 'custom'
								],
								'options' => [

									'trending_block_post_id' => [
										'label' => __( 'Posts ID', 'blocksy-companion' ),
										'type' => 'text',
										'design' => 'inline',
										'desc' => sprintf(
											__('Separate posts ID by comma. How to find the %spost ID%s.', 'blocksy-companion'),
											'<a href="https://www.wpbeginner.com/beginners-guide/how-to-find-post-category-tag-comments-or-user-id-in-wordpress/" target="_blank">',
											'</a>'
										),
										'sync' => [
											'selector' => '.ct-trending-block',
											'render' => function () {
												echo blc_get_trending_block();
											}
										],
									],

								],
							],

							'trending_block_filter' => [
								'label' => __( 'Trending From', 'blocksy-companion' ),
								'type' => 'ct-select',
								'divider' => 'top',
								'value' => 'all_time',
								'view' => 'text',
								'design' => 'inline',
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => blocksy_ordered_keys(
									[
										'all_time' => __( 'All Time', 'blocksy-companion' ),
										'last_24_hours' => __( 'Last 24 Hours', 'blocksy-companion' ),
										'last_7_days' => __( 'Last 7 Days', 'blocksy-companion' ),
										'last_month' => __( 'Last Month', 'blocksy-companion' ),
									]
								),

								'sync' => [
									'selector' => '.ct-trending-block',
									'render' => function () {
										echo blc_get_trending_block();
									}
								],
							],

							'trending_block_thumbnails_size' => [
								'label' => __('Image Size', 'blocksy-companion'),
								'type' => 'ct-select',
								'value' => 'thumbnail',
								'view' => 'text',
								'design' => 'inline',
								'divider' => 'top',
								'choices' => blocksy_ordered_keys(
									blocksy_get_all_image_sizes()
								),
								'sync' => [
									'selector' => '.ct-trending-block',
									'render' => function () {
										echo blc_get_trending_block();
									}
								],
							],

							blocksy_rand_md5() => [
								'type' => 'ct-divider',
							],

							'trending_block_visibility' => [
								'label' => __( 'Container Visibility', 'blocksy-companion' ),
								'type' => 'ct-visibility',
								'design' => 'block',
								'sync' => 'live',

								'value' => [
									'desktop' => true,
									'tablet' => true,
									'mobile' => false,
								],

								'choices' => blocksy_ordered_keys([
									'desktop' => __( 'Desktop', 'blocksy-companion' ),
									'tablet' => __( 'Tablet', 'blocksy-companion' ),
									'mobile' => __( 'Mobile', 'blocksy-companion' ),
								]),
							],
						],

						function_exists('blc_fs') && blc_fs()->can_use_premium_code() ? [
							'trending_block_location' => [
								'label' => __('Display Location', 'blocksy-companion'),
								'type' => 'ct-select',
								'design' => 'inline',
								'divider' => 'top',
								'value' => 'blocksy:content:bottom',
								'choices' => [
									[
										'key' => 'blocksy:content:bottom',
										'value' => __('Before Footer', 'blocksy-companion')
									],

									[
										'key' => 'blocksy:footer:after',
										'value' => __('After Footer', 'blocksy-companion')
									],

									[
										'key' => 'blocksy:header:after',
										'value' => __('After Header', 'blocksy-companion')
									]
								]
							],

							'trending_block_conditions' => [
								'label' => __('Display Conditions', 'blocksy-companion'),
								'type' => 'blocksy-display-condition',
								'divider' => 'top',
								'value' => [
									[
										'type' => 'include',
										'rule' => 'everywhere',
									]
								],
								'display' => 'modal',

								'modalTitle' => __('Trending Block Display Conditions', 'blocksy-companion'),
								'modalDescription' => __('Add one or more conditions to display the trending block.', 'blocksy-companion'),
								'design' => 'block',
								'sync' => 'live'
							],
						] : [],
					],
				],

				blocksy_rand_md5() => [
					'title' => __( 'Design', 'blocksy-companion' ),
					'type' => 'tab',
					'options' => [

						'trendingBlockPostsFont' => [
							'type' => 'ct-typography',
							'label' => __( 'Posts Font', 'blocksy-companion' ),
							'value' => blocksy_typography_default_values([
								'size' => '15px',
								'variation' => 'n5',
							]),
							'setting' => [ 'transport' => 'postMessage' ],
						],

						'trendingBlockFontColor' => [
							'label' => __( 'Font Color', 'blocksy-companion' ),
							'type'  => 'ct-color-picker',
							'design' => 'block:right',
							'divider' => 'top',
							'responsive' => true,
							'sync' => 'live',
							'value' => [
								'default' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
								],

								'hover' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy-companion' ),
									'id' => 'default',
									'inherit' => 'var(--color)'
								],

								[
									'title' => __( 'Hover', 'blocksy-companion' ),
									'id' => 'hover',
									'inherit' => 'var(--linkHoverColor)'
								],
							],
						],

						'trending_block_background' => [
							'label' => __( 'Container Background', 'blocksy-companion' ),
							'type' => 'ct-background',
							'design' => 'block:right',
							'responsive' => true,
							'divider' => 'top',
							'sync' => 'live',
							'value' => blocksy_background_default_value([
								'backgroundColor' => [
									'default' => [
										'color' => 'var(--paletteColor5)',
									],
								],
							])
						],

						'trendingBlockContainerSpacing' => [
							'label' => __( 'Container Inner Spacing', 'blocksy-companion' ),
							'type' => 'ct-slider',
							'divider' => 'top',
							'value' => '30px',
							'units' => blocksy_units_config([
								[
									'unit' => 'px',
									'min' => 0,
									'max' => 100,
								],
							]),
							'responsive' => true,
							'sync' => 'live',
						],

					],
				],
			]
		]
	]
];
