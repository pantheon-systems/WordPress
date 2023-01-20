<?php

if (! isset($is_cpt)) {
	$is_cpt = false;
}

if (! isset($prefix)) {
	$prefix = '';
} else {
	$prefix = $prefix . '_';
}

if (! isset($title)) {
	$title = __('Blog', 'blocksy');
}

$has_card_matching_template = (
	function_exists('blc_get_content_block_that_matches')
	&&
	blc_get_content_block_that_matches([
		'template_type' => 'archive',
		'template_subtype' => 'card',
		'match_conditions_strategy' => rtrim($prefix, '_')
	])
);

$overridable_card_options = [
	blocksy_rand_md5() => [
		'title' => __('General', 'blocksy'),
		'type' => 'tab',
		'options' => [
			[
				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'structure' => '!gutenberg'
					],
					'perform_replace' => $has_card_matching_template ? [
						[
							'condition' => [
								$prefix . 'structure' => '!__never__'
							],
							'key' => $prefix . 'structure',
							'from' => 'simple',
							'to' => 'grid'
						],

						[
							'condition' => [
								$prefix . 'structure' => '!__never__'
							],
							'key' => $prefix . 'structure',
							'from' => 'gutenberg',
							'to' => 'grid'
						]
					] : [],

					'options' => [
						$prefix . 'card_type' => [
							'label' => __('Card Type', 'blocksy'),
							'type' => 'ct-radio',
							'value' => 'boxed',
							'view' => 'text',
							'divider' => 'bottom:full',
							'choices' => [
								'simple' => __('Simple', 'blocksy'),
								'boxed' => __('Boxed', 'blocksy'),
								'cover' => __('Cover', 'blocksy'),
							],
							'conditions' => [
								'cover' => $has_card_matching_template ? [
									$prefix . 'structure' => '__never__'
								] : [
									$prefix . 'structure' => '!simple'
								]
							],
							'sync' => blocksy_sync_whole_page([
								'prefix' => $prefix,
								'loader_selector' => '.entries > article[id]'
							])
						],
					],
				],
			],

			[
				$prefix . 'archive_order' => apply_filters('blocksy:options:posts-listing-archive-order', [
					'label' => __('Card Elements', 'blocksy'),
					'type' => $has_card_matching_template ? 'hidden' : 'ct-layers',

					'sync' => [
						blocksy_sync_whole_page([
							'prefix' => $prefix,
							'loader_selector' => '.entries > article[id]'
						]),

						blocksy_sync_whole_page([
							'id' => $prefix . 'dynamic_data_sync',
							'prefix' => $prefix,
							'loader_selector' => '.entries > article[id] .ct-dynamic-data'
						]),

						[
							'prefix' => $prefix,
							'id' => $prefix . 'archive_order_heading_tag',
							'loader_selector' => '.entry-title',
							'container_inclusive' => false
						],

						[
							'prefix' => $prefix,
							'id' => $prefix . 'archive_order_image',
							'loader_selector' => '.ct-image-container',
							'container_inclusive' => false
						],

						[
							'prefix' => $prefix,
							'id' => $prefix . 'archive_order_button',
							'loader_selector' => '.entry-button',
							'container_inclusive' => false
						],

						[
							'prefix' => $prefix,
							'id' => $prefix . 'archive_order_skip',
							'loader_selector' => 'skip',
							'container_inclusive' => false
						],

						[
							'prefix' => $prefix,
							'id' => $prefix . 'archive_order_meta_first',
							'loader_selector' => '.entry-meta:1',
							'container_inclusive' => false
						],

						[
							'prefix' => $prefix,
							'id' => $prefix . 'archive_order_meta_second',
							'loader_selector' => '.entry-meta:2',
							'container_inclusive' => false
						],
					],

					'value' => [
						[
							'id' => 'post_meta',
							'enabled' => true,
							'meta_elements' => blocksy_post_meta_defaults([
								[
									'id' => 'categories',
									'enabled' => true,
								],
							]),
							'meta_type' => 'simple',
							'meta_divider' => 'slash',
						],

						[
							'id' => 'title',
							'heading_tag' => 'h2',
							'enabled' => true,
						],

						[
							'id' => 'featured_image',
							'thumb_ratio' => '4/3',
							'is_boundless' => 'yes',
							'image_size' => 'medium_large',
							'enabled' => true,
						],

						[
							'id' => 'excerpt',
							'excerpt_length' => '40',
							'enabled' => true,
						],

						[
							'id' => 'read_more',
							'button_type' => 'background',
							'enabled' => false,
						],

						[
							'id' => 'post_meta',
							'enabled' => true,
							'meta_elements' => blocksy_post_meta_defaults([
								[
									'id' => 'author',
									'enabled' => true,
								],

								[
									'id' => 'post_date',
									'enabled' => true,
								],

								[
									'id' => 'comments',
									'enabled' => true,
								],
							]),
							'meta_type' => 'simple',
							'meta_divider' => 'slash',
						],

						[
							'id' => 'divider',
							'enabled' => false
						]
					],

					'settings' => [
						'title' => [
							'label' => __('Title', 'blocksy'),
							'options' => [

								'heading_tag' => [
									'label' => __('Heading tag', 'blocksy'),
									'type' => 'ct-select',
									'value' => 'h2',
									'view' => 'text',
									'design' => 'inline',
									'sync' => [
										'id' => $prefix . 'archive_order_heading_tag',
									],
									'choices' => blocksy_ordered_keys(
										[
											'h1' => 'H1',
											'h2' => 'H2',
											'h3' => 'H3',
											'h4' => 'H4',
											'h5' => 'H5',
											'h6' => 'H6',
										]
									),
								],

							],
						],

						'featured_image' => [
							'label' => __('Featured Image', 'blocksy'),
							'options' => [
								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [
										$prefix . 'card_type' => '!cover',
									],
									'values_source' => 'global',
									'perform_replace' => [
										'condition' => $has_card_matching_template ? [
											$prefix . 'structure' => '!__never__'
										] : [
											$prefix . 'structure' => 'simple'
										],
										'key' => $prefix . 'card_type',
										'from' => 'cover',
										'to' => 'boxed'
									],
									'options' => [
										'thumb_ratio' => [
											'label' => __('Image Ratio', 'blocksy'),
											'type' => 'ct-ratio',
											'view' => 'inline',
											'value' => '4/3',
											'sync' => [
												'id' => $prefix . 'archive_order_skip',
											],
										],
									],
								],

								'image_hover_effect' => [
									'label' => __( 'Hover Effect', 'blocksy' ),
									'type' => 'ct-select',
									'value' => 'none',
									'view' => 'text',
									'design' => 'inline',
									'setting' => [ 'transport' => 'postMessage' ],
									'choices' => blocksy_ordered_keys(
										[
											'none' => __( 'None', 'blocksy' ),
											'zoom-in' => __( 'Zoom In', 'blocksy' ),
											'zoom-out' => __( 'Zoom Out', 'blocksy' ),
										]
									),

									'sync' => blocksy_sync_whole_page([
										'prefix' => 'woo_categories',
										'loader_selector' => '.products > li'
									]),
								],

								'image_size' => [
									'label' => __('Size', 'blocksy'),
									'type' => 'ct-select',
									'value' => 'medium_large',
									'view' => 'text',
									'design' => 'inline',
									'sync' => [
										'id' => $prefix . 'archive_order_image',
									],
									'choices' => blocksy_ordered_keys(
										blocksy_get_all_image_sizes()
									),
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [
										$prefix . 'card_type' => 'boxed',
										$prefix . 'structure' => '!gutenberg'
									],
									'values_source' => 'global',
									'perform_replace' => $has_card_matching_template ? [
										[
											'condition' => [
												$prefix . 'structure' => '!__never__'
											],
											'key' => $prefix . 'structure',
											'from' => 'simple',
											'to' => 'grid'
										],

										[
											'condition' => [
												$prefix . 'structure' => '!__never__'
											],
											'key' => $prefix . 'structure',
											'from' => 'gutenberg',
											'to' => 'grid'
										]
									] : [],
									'options' => [
										'is_boundless' => [
											'label' => __('Boundless Image', 'blocksy'),
											'type' => 'ct-switch',
											'sync' => [
												'id' => $prefix . 'archive_order_skip',
											],
											'value' => 'yes',
										],
									],
								],

							],
						],

						'excerpt' => [
							'label' => __('Excerpt', 'blocksy'),
							'options' => [
								'excerpt_source' => [
									'label' => false,
									'type' => 'ct-radio',
									'value' => 'excerpt',
									'view' => 'text',
									'choices' => [
										'excerpt' => __('Excerpt', 'blocksy'),
										'full' => __('Full Post', 'blocksy'),
									],
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [ 'excerpt_source' => 'excerpt' ],
									'options' => [

										'excerpt_length' => [
											'label' => __('Length', 'blocksy'),
											'type' => 'ct-number',
											'design' => 'inline',
											'value' => 40,
											'min' => 1,
											'max' => 300,
										],

									],
								],
							],
						],

						'read_more' => [
							'label' => __('Read More Button', 'blocksy'),
							'options' => [
								'button_type' => [
									'label' => false,
									'type' => 'ct-radio',
									'value' => 'background',
									'view' => 'text',
									'choices' => [
										'simple' => __('Simple', 'blocksy'),
										'background' => __('Background', 'blocksy'),
										'outline' => __('Outline', 'blocksy'),
									],

									'sync' => [
										'id' => $prefix . 'archive_order_skip',
									]
								],

								'read_more_text' => [
									'label' => __('Text', 'blocksy'),
									'type' => 'text',
									'design' => 'inline',
									'value' => __('Read More', 'blocksy'),
									'sync' => [
										'id' => $prefix . 'archive_order_skip',
									]
								],

								'read_more_arrow' => [
									'label' => __('Show Arrow', 'blocksy'),
									'type' => 'ct-switch',
									'value' => 'no',
									'sync' => [
										'id' => $prefix . 'archive_order_button',
									]
								],

								'read_more_alignment' => [
									'type' => 'ct-radio',
									'label' => __('Alignment', 'blocksy'),
									'value' => 'left',
									'view' => 'text',
									'attr' => ['data-type' => 'alignment'],
									'design' => 'block',
									'sync' => [
										'prefix' => $prefix,
										'id' => $prefix . 'archive_order_skip',
									],
									'choices' => [
										'left' => '',
										'center' => '',
										'right' => '',
									],
								],
							],
						],

						'post_meta' => [
							'label' => __('Post Meta', 'blocksy'),
							'clone' => true,
							'sync' => [
								'id' => $prefix . 'archive_order_meta'
							],
							'options' => blocksy_get_options('general/meta', [
								'is_cpt' => $is_cpt,
								'computed_cpt' => $is_cpt ? trim(
									$prefix, '_'
								) : false,
								'skip_sync_id' => [
									'id' => $prefix . 'archive_order_skip'
								],
								'meta_elements' => blocksy_post_meta_defaults([
									[
										'id' => 'author',
										'enabled' => true,
									],

									[
										'id' => 'post_date',
										'enabled' => true,
									],

									[
										'id' => 'comments',
										'enabled' => true,
									],
								]),
							])
						],

						'divider' => [
							'label' => __('Divider', 'blocksy'),
							'clone' => true,
							'sync' => [
								'id' => $prefix . 'archive_order_meta'
							],
						]
					],
				], trim($prefix, '_')),

			],

			$has_card_matching_template ? [] : [
				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],
			],

			[
				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'card_type' => 'cover',
						$prefix . 'structure' => '!gutenberg'
					],
					'perform_replace' => array_merge([
						'condition' => $has_card_matching_template ? [
							$prefix . 'structure' => '!__never__'
						] : [
							$prefix . 'structure' => 'simple'
						],
						'key' => $prefix . 'card_type',
						'from' => 'cover',
						'to' => 'boxed'
					], $has_card_matching_template ? [
						[
							'condition' => [
								$prefix . 'structure' => '!__never__'
							],
							'key' => $prefix . 'structure',
							'from' => 'simple',
							'to' => 'grid'
						],

						[
							'condition' => [
								$prefix . 'structure' => '!__never__'
							],
							'key' => $prefix . 'structure',
							'from' => 'gutenberg',
							'to' => 'grid'
						]
					] : []),
					'options' => [
						$prefix . 'card_min_height' => [
							'label' => __( 'Card Min Height', 'blocksy' ),
							'type' => 'ct-slider',
							'min' => 0,
							'max' => 1000,
							'responsive' => true,
							'sync' => 'live',
							'value' => 400,
							'divider' => 'bottom',
						],
					],
				],

				$prefix . 'cardsGap' => [
					'label' => __( 'Cards Gap', 'blocksy' ),
					'type' => 'ct-slider',
					'min' => 0,
					'max' => 100,
					'responsive' => true,
					'sync' => 'live',
					'value' => 30,
				],

				$prefix . 'card_spacing' => [
					'label' => __( 'Card Inner Spacing', 'blocksy' ),
					'type' => 'ct-slider',
					'min' => 0,
					'max' => 100,
					'responsive' => true,
					'value' => 30,
					'divider' => 'top',
					'sync' => 'live',
				],
			],

			$has_card_matching_template ? [] : [
				$prefix . 'content_horizontal_alignment' => [
					'type' => $has_card_matching_template ? 'hidden' : 'ct-radio',
					'label' => __( 'Content Alignment', 'blocksy' ),
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
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					$prefix . 'structure' => '!gutenberg'
				],
				'perform_replace' => $has_card_matching_template ? [
					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'simple',
						'to' => 'grid'
					],

					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'gutenberg',
						'to' => 'grid'
					]
				] : [],
				'options' => $has_card_matching_template ? [] : [
					$prefix . 'content_vertical_alignment' => [
						'type' => 'ct-radio',
						'label' => __( 'Vertical Alignment', 'blocksy' ),
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'responsive' => true,
						'attr' => [ 'data-type' => 'vertical-alignment' ],
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => 'CT_CSS_SKIP_RULE',
						'choices' => [
							'flex-start' => '',
							'center' => '',
							'flex-end' => '',
						],
					],
				],
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => apply_filters('blocksy:options:posts-listing:design', [
			[
				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:title:enabled' => '!no'
					],
					'options' => [
						$prefix . 'cardTitleFont' => [
							'type' => 'ct-typography',
							'label' => __( 'Title Font', 'blocksy' ),
							'sync' => 'live',
							'value' => blocksy_typography_default_values([
								'size' => [
									'desktop' => '20px',
									'tablet'  => '20px',
									'mobile'  => '18px'
								],
								'line-height' => '1.3'
							]),
						],

						$prefix . 'cardTitleColor' => [
							'label' => __( 'Title Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'sync' => 'live',
							'design' => 'inline',

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
											$prefix . 'archive_order:array-ids:title:heading_tag' => 'h1'
										],

										'var(--heading-2-color, var(--headings-color))' => [
											$prefix . 'archive_order:array-ids:title:heading_tag' => 'h2'
										],

										'var(--heading-3-color, var(--headings-color))' => [
											$prefix . 'archive_order:array-ids:title:heading_tag' => 'h3'
										],

										'var(--heading-4-color, var(--headings-color))' => [
											$prefix . 'archive_order:array-ids:title:heading_tag' => 'h4'
										],

										'var(--heading-5-color, var(--headings-color))' => [
											$prefix . 'archive_order:array-ids:title:heading_tag' => 'h5'
										],

										'var(--heading-6-color, var(--headings-color))' => [
											$prefix . 'archive_order:array-ids:title:heading_tag' => 'h6'
										]
									]
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--linkHoverColor)'
								],
							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:excerpt:enabled' => '!no'
					],
					'options' => [

						$prefix . 'cardExcerptFont' => [
							'type' => 'ct-typography',
							'label' => __( 'Excerpt Font', 'blocksy' ),
							'sync' => 'live',
							'value' => blocksy_typography_default_values([]),
						],

						$prefix . 'cardExcerptColor' => [
							'label' => __( 'Excerpt Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'noColor' => [ 'background' => 'var(--color)'],
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
									'inherit' => 'var(--color)'
								],
							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

					],
				],

				$prefix . 'cardMetaFont' => [
					'type' => 'ct-typography',
					'label' => __( 'Meta Font', 'blocksy' ),
					'sync' => 'live',
					'value' => blocksy_typography_default_values([
						'size' => [
							'desktop' => '12px',
							'tablet'  => '12px',
							'mobile'  => '12px'
						],
						'variation' => 'n6',
						'text-transform' => 'uppercase',
					]),
				],

				$prefix . 'cardMetaColor' => [
					'label' => __( 'Meta Font Color', 'blocksy' ),
					'type'  => 'ct-color-picker',
					'design' => 'inline',
					'noColor' => [ 'background' => 'var(--color)'],
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

				blocksy_rand_md5() => [
					'type' => 'ct-has-meta-category-button',
					'optionId' => $prefix . 'archive_order',
					'options' => [
						$prefix . 'card_meta_button_type_font_colors' => [
							'label' => __( 'Meta Button Font', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'divider' => 'top',
							'noColor' => [ 'background' => 'var(--color)'],
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
									'inherit' => 'var(--buttonTextInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonTextHoverColor)'
								],
							],
						],

						$prefix . 'card_meta_button_type_background_colors' => [
							'label' => __( 'Meta Button Background', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'noColor' => [ 'background' => 'var(--color)'],
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
									'inherit' => 'var(--buttonInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonHoverColor)'
								],
							],
						],
					]
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:read_more:button_type' => 'simple',
						$prefix . 'archive_order:array-ids:read_more:enabled' => '!no'
					],
					'options' => [

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'cardButtonSimpleTextColor' => [
							'label' => __( 'Button Font Color', 'blocksy' ),
							'sync' => 'live',
							'type'  => 'ct-color-picker',
							'design' => 'inline',

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
									'inherit' => 'var(--linkInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--linkHoverColor)'
								],
							],
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:read_more:button_type' => 'background',
						$prefix . 'archive_order:array-ids:read_more:enabled' => '!no'
					],
					'options' => [

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'cardButtonBackgroundTextColor' => [
							'label' => __( 'Button Font Color', 'blocksy' ),
							'sync' => 'live',
							'type'  => 'ct-color-picker',
							'design' => 'inline',

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
									'inherit' => 'var(--buttonTextInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonTextHoverColor)'
								],
							],
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:read_more:button_type' => 'outline',
						$prefix . 'archive_order:array-ids:read_more:enabled' => '!no'
					],
					'options' => [

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'cardButtonOutlineTextColor' => [
							'label' => __( 'Button Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'sync' => 'live',
							'design' => 'inline',

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
									'inherit' => 'var(--linkInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--linkHoverColor)'
								],
							],
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:read_more:button_type' => '!simple',
						$prefix . 'archive_order:array-ids:read_more:enabled' => '!no'
					],
					'options' => [

						$prefix . 'cardButtonColor' => [
							'label' => __( 'Button Color', 'blocksy' ),
							'sync' => 'live',
							'type'  => 'ct-color-picker',
							'design' => 'inline',

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
									'inherit' => 'var(--buttonInitialColor)'
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonHoverColor)'
								],
							],
						],

					],
				],

				blocksy_rand_md5() =>  [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'card_type' => 'simple',
						$prefix . 'archive_order:array-ids:featured_image:enabled' => '!no'
					],
					'options' => [

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'cardThumbRadius' => [
							'label' => __( 'Featured Image Radius', 'blocksy' ),
							'type' => 'ct-spacing',
							'sync' => 'live',
							'value' => blocksy_spacing_value([
								'linked' => true,
							]),
							'responsive' => true
						],

						$prefix . 'cardDivider' => [
							'label' => __( 'Card bottom divider', 'blocksy' ),
							'type' => 'ct-border',
							'sync' => 'live',
							'design' => 'inline',
							'divider' => 'top',
							'value' => [
								'width' => 1,
								'style' => 'dashed',
								'color' => [
									'color' => 'rgba(224, 229, 235, 0.8)',
								],
							]
						],
					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [
						$prefix . 'archive_order:array-ids:divider:enabled' => '!no'

					],
					'options' => [
						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						$prefix . 'entryDivider' => [
							'label' => __( 'Card Divider', 'blocksy' ),
							'type' => 'ct-border',
							'sync' => 'live',
							'design' => 'inline',
							'value' => [
								'width' => 1,
								'style' => 'solid',
								'color' => [
									'color' => 'rgba(224, 229, 235, 0.8)',
								],
							]
						],
					],
				],
			],

			apply_filters(
				'blocksy:options:posts-listing:design:before_card_background',
				[],
				trim($prefix, '_')
			),

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					$prefix . 'card_type' => 'boxed|cover',
					$prefix . 'structure' => '!gutenberg'
				],
				'perform_replace' => array_merge([
					'condition' => $has_card_matching_template ? [
						$prefix . 'structure' => '!__never__'
					] : [
						$prefix . 'structure' => 'simple'
					],
					'key' => $prefix . 'card_type',
					'from' => 'cover',
					'to' => 'boxed'
				], $has_card_matching_template ? [
					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'simple',
						'to' => 'grid'
					],

					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'gutenberg',
						'to' => 'grid'
					]
				] : []),
				'options' => [

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							$prefix . 'card_type' => 'cover',
						],
						'perform_replace' => [
							'condition' => $has_card_matching_template ? [
								$prefix . 'structure' => '!__never__'
							] : [
								$prefix . 'structure' => 'simple'
							],
							'key' => $prefix . 'card_type',
							'from' => 'cover',
							'to' => 'boxed'
						],
						'options' => [

							$prefix . 'card_overlay_background' => [
								'label' => __( 'Card Overlay Color', 'blocksy' ),
								'type'  => 'ct-background',
								'design' => 'block:right',
								'responsive' => true,
								'divider' => 'bottom',
								'activeTabs' => ['color', 'gradient'],
								'sync' => 'live',
								'value' => blocksy_background_default_value([
									'backgroundColor' => [
										'default' => [
											'color' => 'rgba(0,0,0,0.5)',
										],
									],
								]),
							],
						],
					],

					$prefix . 'cardBackground' => [
						'label' => __( 'Card Background Color', 'blocksy' ),
						'type'  => 'ct-background',
						'design' => 'block:right',
						'responsive' => true,
						'activeTabs' => ['color', 'gradient'],
						'sync' => 'live',
						'value' => blocksy_background_default_value([
							'backgroundColor' => [
								'default' => [
									'color' => 'var(--paletteColor8)',
								],
							],
						]),
					],

					$prefix . 'cardBorder' => [
						'label' => __( 'Card Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'sync' => 'live',
						'divider' => 'top',
						'responsive' => true,
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => 'rgba(44,62,80,0.2)',
							],
						]
					],

					$prefix . 'cardShadow' => [
						'label' => __( 'Card Shadow', 'blocksy' ),
						'type' => 'ct-box-shadow',
						'sync' => 'live',
						'responsive' => true,
						'divider' => 'top',
						'value' => blocksy_box_shadow_value([
							'enable' => true,
							'h_offset' => 0,
							'v_offset' => 12,
							'blur' => 18,
							'spread' => -6,
							'inset' => false,
							'color' => [
								'color' => 'rgba(34, 56, 101, 0.04)',
							],
						])
					],

					$prefix . 'cardRadius' => [
						'label' => __( 'Border Radius', 'blocksy' ),
						'sync' => 'live',
						'type' => 'ct-spacing',
						'divider' => 'top',
						'value' => blocksy_spacing_value([
							'linked' => true,
						]),
						'responsive' => true
					],

				],
			],
		], trim($prefix, '_'))
	]
];


$options = [

	blocksy_rand_md5() => [
		'type'  => 'ct-title',
		'label' => sprintf(
			// translators: placeholder here means the actual structure title.
			__('%s Structure', 'blocksy'),
			$title
		),
		'desc' => sprintf(
			// translators: placeholder here means the actual structure title.
			__('Set the %s entries default structure.', 'blocksy'),
			$title
		),
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			$prefix . 'structure' => [
				'label' => false,
				'type' => 'ct-image-picker',
				'value' => 'grid',
				'divider' => 'bottom',
				'sync' => blocksy_sync_whole_page([
					'prefix' => $prefix,
					'loader_selector' => '.entries > article'
				]),
				'choices' => [
					'simple' => [
						'src' => blocksy_image_picker_url('simple.svg'),
						'title' => __('Simple', 'blocksy'),
					],

					'classic' => [
						'src' => blocksy_image_picker_url('classic.svg'),
						'title' => __('Classic', 'blocksy'),
					],

					'grid' => [
						'src' => blocksy_image_picker_url('grid.svg'),
						'title' => __('Grid', 'blocksy'),
					],

					'enhanced-grid' => [
						'src' => blocksy_image_picker_url('enhanced-grid.svg'),
						'title' => __('Enhanced Grid', 'blocksy'),
					],

					'gutenberg' => [
						'src' => blocksy_image_picker_url('gutenberg.svg'),
						'title' => __('Gutenberg', 'blocksy'),
					],
				],

				'conditions' => $has_card_matching_template ? [
					'simple' => [
						$prefix . 'structure' => '__never__'
					],

					'gutenberg' => [
						$prefix . 'structure' => '__never__'
					],
				] : [],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ $prefix . 'structure' => '!grid' ],
				'perform_replace' => $has_card_matching_template ? [
					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'simple',
						'to' => 'grid'
					],

					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'gutenberg',
						'to' => 'grid'
					]
				] : [],
				'options' => [

					$prefix . 'archive_per_page' => [
						'label' => __( 'Number of Posts', 'blocksy' ),
						'type' => 'ct-number',
						'value' => get_option('posts_per_page', 10),
						'min' => 1,
						'max' => 500,
						'design' => 'inline',
						'sync' => blocksy_sync_whole_page([
							'prefix' => $prefix,
							'loader_selector' => '.entries > article'
						]),
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ $prefix . 'structure' => 'grid' ],
				'perform_replace' => $has_card_matching_template ? [
					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'simple',
						'to' => 'grid'
					],

					[
						'condition' => [
							$prefix . 'structure' => '!__never__'
						],
						'key' => $prefix . 'structure',
						'from' => 'gutenberg',
						'to' => 'grid'
					]
				] : [],
				'options' => [

					blocksy_rand_md5() => [
						'type' => 'ct-group',
						'label' => __( 'Columns & Posts', 'blocksy' ),
						'attr' => [ 'data-columns' => '2:medium' ],
						'responsive' => true,
						'options' => [

							$prefix . 'columns' => [
								'label' => false,
								'desc' => __( 'Number of columns', 'blocksy' ),
								'type' => 'ct-number',
								'value' => [
									'desktop' => 3,
									'tablet' => 2,
									'mobile' => 1
								],
								'min' => 1,
								'max' => 6,
								'design' => 'block',
								'disableRevertButton' => true,
								'attr' => [ 'data-width' => 'full' ],
								'sync' => 'live',
								'responsive' => true,
								'skipResponsiveControls' => true
							],

							$prefix . 'archive_per_page' => [
								'label' => false,
								'desc' => __( 'Number of posts', 'blocksy' ),
								'type' => 'ct-number',
								'value' => get_option('posts_per_page', 10),
								'min' => 1,
								'max' => 500,
								'markAsAutoFor' => ['tablet', 'mobile'],
								'design' => 'block',
								'disableRevertButton' => true,
								'attr' => [ 'data-width' => 'full' ],
								'sync' => blocksy_sync_whole_page([
									'prefix' => $prefix,
									'loader_selector' => '.entries > article'
								]),
							],

						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
				'attr' => ['data-type' => 'small']
			],

			$prefix . 'archive_listing_panel' => [
				'label' => __('Cards Options', 'blocksy'),
				'type' => 'ct-panel',
				'value' => 'yes',
				'wrapperAttr' => ['data-panel' => 'only-arrow'],
				'inner-options' => $overridable_card_options
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			$prefix . 'background' => [
				'label' => __('Page Background', 'blocksy'),
				'type' => 'ct-background',
				'design' => 'block:right',
				'responsive' => true,
				'sync' => 'live',
				'divider' => 'bottom',
				'value' => blocksy_background_default_value([
					'backgroundColor' => [
						'default' => [
							'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
						],
					],
				]),
				'desc' => sprintf(
					// translators: placeholder here means the actual URL.
					__( 'Please note, by default this option is inherited from Colors ➝ %sSite Background%s.', 'blocksy' ),
					sprintf(
						'<a data-trigger-section="color" href="%s">',
						admin_url('/customize.php?autofocus[section]=color')
					),
					'</a>'
				),
			],

		],
	],
];
