<?php

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

if (! isset($enabled)) {
	$enabled = 'no';
}

if (! isset($post_type)) {
	$post_type = 'post';
}

$options = [
	$prefix . 'has_related_posts' => [
		'label' => __('Related Posts', 'blocksy'),
		'type' => 'ct-panel',
		'switch' => true,
		'value' => $enabled,
		'sync' => blocksy_sync_whole_page([
			'prefix' => $prefix,
		]),
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					[

						$prefix . 'related_criteria' => [
							'label' => __( 'Related Criteria', 'blocksy' ),
							'type' => $prefix === 'single_blog_post_' ? 'ct-select' : 'hidden',
							'type' => 'ct-select',
							'value' => array_keys(blocksy_get_taxonomies_for_cpt(
								$post_type
							))[0],
							'view' => 'text',
							'design' => 'inline',
							'choices' => blocksy_ordered_keys(
								blocksy_get_taxonomies_for_cpt($post_type)
							),
							'sync' => [
								'prefix' => $prefix,
								'selector' => '.ct-related-posts',
								'render' => function () {
									blocksy_related_posts();
								}
							]
						],

						$prefix . 'related_sort' => [
							'type' => 'ct-select',
							'label' => __('Sort by', 'blocksy'),
							'value' => 'recent',
							'design' => 'inline',
							'choices' => blocksy_ordered_keys(
								[
									'default' => __('Default', 'blocksy'),
									'recent' => __('Recent', 'blocksy'),
									'commented' => __('Most Commented', 'blocksy'),
									'random' => __('Random', 'blocksy'),
								]
							),
							'sync' => [
								'prefix' => $prefix,
								'selector' => '.ct-related-posts',
								'render' => function () {
									blocksy_related_posts();
								}
							]
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'related_label' => [
							'label' => __( 'Module Title', 'blocksy' ),
							'type' => 'text',
							'design' => 'inline',
							'value' => __( 'Related Posts', 'blocksy' ),
							'sync' => 'live'
						],

						$prefix . 'related_label_wrapper' => [
							'label' => __( 'Module Title Tag', 'blocksy' ),
							'type' => 'ct-select',
							'value' => 'h3',
							'view' => 'text',
							'design' => 'inline',
							'choices' => blocksy_ordered_keys(
								[
									'h1' => 'H1',
									'h2' => 'H2',
									'h3' => 'H3',
									'h4' => 'H4',
									'h5' => 'H5',
									'h6' => 'H6',
									'p' => 'p',
									'span' => 'span',
								]
							),
							'sync' => [
								'prefix' => $prefix,
								'selector' => '.ct-related-posts',
								'loader_selector' => '.ct-block-title',
								'render' => function () {
									blocksy_related_posts();
								}
							]
						],

						$prefix . 'related_label_alignment' => [
							'type' => 'ct-radio',
							'label' => __( 'Module Title Alignment', 'blocksy' ),
							'view' => 'text',
							'design' => 'block',
							'divider' => 'top',
							'responsive' => true,
							'attr' => [ 'data-type' => 'alignment' ],
							'setting' => [ 'transport' => 'postMessage' ],
							'value' => 'CT_CSS_SKIP_RULE',
							'choices' => [
								'left' => '',
								'center' => '',
								'right' => '',
							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						blocksy_rand_md5() => [
							'type' => 'ct-group',
							'label' => __( 'Columns & Posts', 'blocksy' ),
							'attr' => [ 'data-columns' => '2:medium' ],
							'responsive' => true,
							'options' => [

								$prefix . 'related_posts_columns' => [
									'label' => false,
									'type' => 'ct-number',
									'value' => [
										'desktop' => 3,
										'tablet' => 2,
										'mobile' => 1
									],
									'min' => 1,
									'max' => 4,
									'design' => 'block',
									'disableRevertButton' => true,
									'attr' => [ 'data-width' => 'full' ],
									'desc' => __('Number of columns', 'blocksy' ),
									'sync' => 'live',
									'responsive' => true,
									'skipResponsiveControls' => true,
								],

								$prefix . 'related_posts_count' => [
									'label' => false,
									'type' => 'ct-number',
									'value' => 3,
									'min' => 1,
									'max' => 20,
									'design' => 'block',
									'disableRevertButton' => true,
									'attr' => [ 'data-width' => 'full' ],
									'desc' => __( 'Number of posts', 'blocksy' ),
									'markAsAutoFor' => ['tablet', 'mobile'],
									'sync' => [
										[
											'prefix' => $prefix,
											'selector' => '.ct-related-posts',
											'render' => function () {
												blocksy_related_posts();
											}
										],

										[
											'id' => $prefix . 'related_posts_count_skip',
											'loader_selector' => 'skip',
											'prefix' => $prefix,
											'selector' => '.ct-related-posts',
											'render' => function () {
												blocksy_related_posts();
											}
										]
									]
								],

							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'has_related_featured_image' => [
							'label' => __( 'Featured Image', 'blocksy' ),
							'type' => 'ct-switch',
							'value' => 'yes',
							'sync' => [
								'prefix' => $prefix,
								'selector' => '.ct-related-posts',
								'render' => function () {
									blocksy_related_posts();
								}
							]
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ $prefix . 'has_related_featured_image' => 'yes' ],
							'options' => [

								$prefix . 'related_featured_image_ratio' => [
									'label' => __( 'Image Ratio', 'blocksy' ),
									'type' => 'ct-ratio',
									'value' => '16/9',
									'design' => 'inline',
									'sync' => 'live'
								],

								$prefix . 'related_featured_image_size' => [
									'label' => __('Image Size', 'blocksy'),
									'type' => 'ct-select',
									'value' => 'medium',
									'view' => 'text',
									'design' => 'inline',
									'choices' => blocksy_ordered_keys(
										blocksy_get_all_image_sizes()
									),
									'sync' => [
										'prefix' => $prefix,
										'selector' => '.ct-related-posts',
										'render' => function () {
											blocksy_related_posts();
										}
									],
								],

							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'related_posts_title_tag' => [
							'label' => __( 'Posts Title Tag', 'blocksy' ),
							'type' => 'ct-select',
							'value' => 'h4',
							'view' => 'text',
							'design' => 'inline',
							'choices' => blocksy_ordered_keys(
								[
									'h1' => 'H1',
									'h2' => 'H2',
									'h3' => 'H3',
									'h4' => 'H4',
									'h5' => 'H5',
									'h6' => 'H6',
									'p' => 'p',
									'span' => 'span',
								]
							),
							'sync' => [
								'prefix' => $prefix,
								'selector' => '.ct-related-posts',
								'render' => function () {
									blocksy_related_posts();
								}
							]
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],
					],

					blocksy_get_options('general/meta', [
						'prefix' => $prefix . 'related_single',
						'has_label' => true,
						'has_meta_elements_wrapper_attr' => false,
						'post_type' => $post_type,
						'meta_elements' => blocksy_post_meta_defaults([
							[
								'id' => 'post_date',
								'enabled' => true,
							],

							[
								'id' => 'comments',
								'enabled' => true,
							],
						]),
						'item_style_type' => 'hidden',
						'item_divider_type' => 'hidden',

						'skip_sync_id' => [
							'id' => $prefix . 'related_posts_count_skip',
						],

						'sync_id' => [
							'prefix' => $prefix,
							'selector' => '.ct-related-posts',
							'loader_selector' => '.entry-meta',
							'render' => function () {
								blocksy_related_posts();
							}
						]
					]),

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					$prefix . 'related_posts_containment' => [
						'label' => __('Module Placement', 'blocksy'),
						'type' => 'ct-radio',
						'value' => 'separated',
						'view' => 'text',
						'design' => 'block',
						'desc' => __('Separate or unify the related posts module from or with the entry content area.', 'blocksy'),
						'choices' => [
							'separated' => __('Separated', 'blocksy'),
							'contained' => __('Contained', 'blocksy'),
						],
						'sync' => blocksy_sync_whole_page([
							'prefix' => $prefix,
						]),
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							'any' => [
								'all' => [
									$prefix . 'related_posts_containment' => 'separated',
									$prefix . 'comments_containment' => 'separated',
									$prefix . 'has_comments' => 'yes'
								],

								'all_' => [
									$prefix . 'related_posts_containment' => 'contained',
									$prefix . 'comments_containment' => 'contained',
									$prefix . 'has_comments' => 'yes'
								],
							]
						],
						'options' => [

							$prefix . 'related_location' => [
								'label' => __( 'Location', 'blocksy' ),
								'type' => 'ct-radio',
								'value' => 'before',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'choices' => [
									'before' => __( 'Before Comments', 'blocksy' ),
									'after' => __( 'After Comments', 'blocksy' ),
								],
								'sync' => blocksy_sync_whole_page([
									'prefix' => $prefix,
								]),
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'related_posts_containment' => 'separated'
						],
						'options' => [

							$prefix . 'related_structure' => [
								'label' => __( 'Container Structure', 'blocksy' ),
								'type' => 'ct-radio',
								'value' => 'normal',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'choices' => [
									'normal' => __( 'Normal', 'blocksy' ),
									'narrow' => __( 'Narrow', 'blocksy' ),
								],
								'sync' => 'live'
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'related_structure' => 'narrow',
							$prefix . 'related_posts_containment' => 'separated'
						],
						'options' => [
							$prefix . 'related_narrow_width' => [
								'label' => __( 'Container Max Width', 'blocksy' ),
								'type' => 'ct-slider',
								'value' => 750,
								'min' => 500,
								'max' => 800,
								'sync' => 'live'
							],
						],
					],

					$prefix . 'related_visibility' => [
						'label' => __( 'Visibility', 'blocksy' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'divider' => 'top',
						'sync' => 'live',

						'value' => [
							'desktop' => true,
							'tablet' => true,
							'mobile' => true,
						],

						'choices' => blocksy_ordered_keys([
							'desktop' => __( 'Desktop', 'blocksy' ),
							'tablet' => __( 'Tablet', 'blocksy' ),
							'mobile' => __( 'Mobile', 'blocksy' ),
						]),
					],

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					$prefix . 'related_posts_label_color' => [
						'label' => __( 'Module Title Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'sync' => 'live',

						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => [
									'var(--heading-1-color, var(--headings-color))' => [
										$prefix . 'related_label_wrapper' => 'h1'
									],

									'var(--heading-2-color, var(--headings-color))' => [
										$prefix . 'related_label_wrapper' => 'h2'
									],

									'var(--heading-3-color, var(--headings-color))' => [
										$prefix . 'related_label_wrapper' => 'h3'
									],

									'var(--heading-4-color, var(--headings-color))' => [
										$prefix . 'related_label_wrapper' => 'h4'
									],

									'var(--heading-5-color, var(--headings-color))' => [
										$prefix . 'related_label_wrapper' => 'h5'
									],

									'var(--heading-6-color, var(--headings-color))' => [
										$prefix . 'related_label_wrapper' => 'h6'
									],

									'var(--color)' => [
										$prefix . 'related_label_wrapper' => 'span|p'
									],
								]
							],
						],
					],

					$prefix . 'related_posts_link_color' => [
						'label' => __( 'Posts Title Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'divider' => 'top',
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
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => [
									'var(--heading-1-color, var(--headings-color))' => [
										$prefix . 'related_posts_title_tag' => 'h1'
									],

									'var(--heading-2-color, var(--headings-color))' => [
										$prefix . 'related_posts_title_tag' => 'h2'
									],

									'var(--heading-3-color, var(--headings-color))' => [
										$prefix . 'related_posts_title_tag' => 'h3'
									],

									'var(--heading-4-color, var(--headings-color))' => [
										$prefix . 'related_posts_title_tag' => 'h4'
									],

									'var(--heading-5-color, var(--headings-color))' => [
										$prefix . 'related_posts_title_tag' => 'h5'
									],

									'var(--heading-6-color, var(--headings-color))' => [
										$prefix . 'related_posts_title_tag' => 'h6'
									],

									'var(--color)' => [
										$prefix . 'related_posts_title_tag' => 'span|p'
									],
								]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--linkHoverColor)'
							],
						],
					],

					$prefix . 'related_posts_meta_color' => [
						'label' => __( 'Posts Meta Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
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
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--linkHoverColor)'
							],
						],
					],

					$prefix . 'related_thumb_radius' => [
						'label' => __( 'Image Border Radius', 'blocksy' ),
						'type' => 'ct-spacing',
						'divider' => 'top',
						'value' => blocksy_spacing_value([
							'linked' => true,
						]),
						'inputAttr' => [
							'placeholder' => '5'
						],
						'responsive' => true,
						'sync' => 'live',
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'related_posts_containment' => 'separated'
						],
						'options' => [

							blocksy_rand_md5() => [
								'type' => 'ct-divider',
							],

							$prefix . 'related_posts_container_spacing' => [
								'label' => __( 'Container Inner Spacing', 'blocksy' ),
								'type' => 'ct-slider',
								'value' => '50px',
								'units' => blocksy_units_config([
									[
										'unit' => 'px',
										'min' => 0,
										'max' => 150,
									],
								]),
								'responsive' => true,
								'sync' => 'live',
							],

							$prefix . 'related_posts_background' => [
								'label' => __( 'Container Background', 'blocksy' ),
								'type' => 'ct-background',
								'design' => 'inline',
								'divider' => 'top',
								'value' => blocksy_background_default_value([
									'backgroundColor' => [
										'default' => [
											'color' => 'var(--paletteColor6)',
										],
									],
								]),
								'sync' => 'live',
							],

						],
					],
				],
			],
		],
	],
];

