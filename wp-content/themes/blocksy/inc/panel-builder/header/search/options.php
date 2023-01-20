<?php

$cpt_choices = [
	'post' => __('Posts', 'blocksy'),
	'page' => __('Pages', 'blocksy'),
	'product' => __('Products', 'blocksy')
];

$cpt_options = [
	'post' => true,
	'page' => true,
	'product' => true
];

$all_cpts = blocksy_manager()->post_types->get_supported_post_types();

if (function_exists('is_bbpress')) {
	$all_cpts[] = 'forum';
	$all_cpts[] = 'topic';
	$all_cpts[] = 'reply';
}

foreach ($all_cpts as $single_cpt) {
	if (get_post_type_object($single_cpt)) {
		$cpt_choices[$single_cpt] = get_post_type_labels(
			get_post_type_object($single_cpt)
		)->singular_name;
	} else {
		$cpt_choices[$single_cpt] = ucfirst($single_cpt);
	}

	$cpt_options[$single_cpt] = true;
}

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			apply_filters(
				'blocksy:header:search:options:icon',
				[]
			),

			'searchHeaderIconSize' => [
				'label' => __( 'Icon Size', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 5,
				'max' => 50,
				'value' => 15,
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'search_label_visibility' => [
				'label' => __( 'Label Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top',
				'allow_empty' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'desktop' => false,
					'tablet' => false,
					'mobile' => false,
				],

				'choices' => blocksy_ordered_keys([
					'desktop' => __( 'Desktop', 'blocksy' ),
					'tablet' => __( 'Tablet', 'blocksy' ),
					'mobile' => __( 'Mobile', 'blocksy' ),
				]),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'search_label_visibility/desktop' => true,
						'search_label_visibility/tablet' => true,
						'search_label_visibility/mobile' => true,
					]
				],
				'options' => [
					'search_label_position' => [
						'type' => 'ct-radio',
						'label' => __( 'Label Position', 'blocksy' ),
						'value' => 'left',
						'view' => 'text',
						'divider' => 'top',
						'design' => 'block',
						'responsive' => [ 'tablet' => 'skip' ],
						'choices' => [
							'left' => __( 'Left', 'blocksy' ),
							'right' => __( 'Right', 'blocksy' ),
							'bottom' => __( 'Bottom', 'blocksy' ),
						],
					],

					'search_label' => [
						'label' => __( 'Label Text', 'blocksy' ),
						'type' => 'text',
						'divider' => 'top',
						'design' => 'block',
						'value' => __( 'Search', 'blocksy' ),
						'sync' => 'live'
					],
				],
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'search_label_visibility/desktop' => true,
						'search_label_visibility/tablet' => true,
						'search_label_visibility/mobile' => true,
					]
				],
				'options' => [
					'search_label_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Label Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '12px',
							'variation' => 'n6',
							'text-transform' => 'uppercase',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Label Font Color', 'blocksy' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'header_search_font_color',
								'label' => __('Default State', 'blocksy')
							],

							[
								'id' => 'transparent_header_search_font_color',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'sticky_header_search_font_color',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [
							'header_search_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],
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

							'transparent_header_search_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],
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
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
									],
								],
							],

							'sticky_header_search_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],
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
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
									],
								],
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Icon Color', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'searchHeaderIconColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentSearchHeaderIconColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickySearchHeaderIconColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'searchHeaderIconColor' => [
						'label' => __( 'Icon Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

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
								'inherit' => 'var(--paletteColor2)'
							],
						],
					],

					'transparentSearchHeaderIconColor' => [
						'label' => __( 'Icon Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

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
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
							],
						],
					],

					'stickySearchHeaderIconColor' => [
						'label' => __( 'Icon Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

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
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
							],
						],
					],
				],
			],

			'headerSearchMargin' => [
				'label' => __( 'Icon Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-title',
		'label' => __( 'Search Results', 'blocksy' ),
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'header_search_placeholder' => [
				'label' => __( 'Placeholder Text', 'blocksy' ),
				'type' => 'text',
				'design' => 'block',
				'value' => __( 'Search', 'blocksy' ),
				'sync' => 'live'
			],

			'enable_live_results' => [
				'label' => __( 'Live Results', 'blocksy' ),
				'type' => 'ct-switch',
				'value' => 'yes',
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'enable_live_results' => 'yes' ],
				'options' => [

					'searchHeaderImages' => [
						'label' => __( 'Live Results Images', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'yes',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'search_through/product' => true ],
						'options' => [
							'searchHeaderProductPrice' => [
								'label' => __( 'Live Results Product Price', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'no',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
							],
						]
					],

				],
			],


			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __('Search Through Criteria', 'blocksy'),
				'desc' => __(
					'Chose in which post types do you want to perform searches.',
					'blocksy'
				)
			],

			'search_through' => [
				'label' => false,
				'type' => 'ct-checkboxes',
				'attr' => ['data-columns' => '2'],
				'disableRevertButton' => true,
				'choices' => blocksy_ordered_keys($cpt_choices),
				'value' => $cpt_options
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'searchHeaderModalFont' => [
				'type' => 'ct-typography',
				'label' => __( 'Font', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '14px',
					'variation' => 'n5',
					'line-height' => '1.4',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'searchHeaderLinkColor' => [
				'label' => __( 'Font Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'divider' => 'bottom',
				'setting' => [ 'transport' => 'postMessage' ],

				'value' => [
					'default' => [
						'color' => '#ffffff',
					],

					'hover' => [
						'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
					],
				],

				'pickers' => [
					[
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
					],

					[
						'title' => __( 'Hover', 'blocksy' ),
						'id' => 'hover',
						'inherit' => 'var(--linkHoverColor)'
					],
				],
			],

			'searchHeaderInputColor' => [
				'label' => __( 'Input Font Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'divider' => 'bottom',
				'setting' => [ 'transport' => 'postMessage' ],

				'value' => [
					'default' => [
						'color' => '#ffffff',
					],

					'focus' => [
						'color' => '#ffffff',
					],
				],

				'pickers' => [
					[
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
					],

					[
						'title' => __( 'Focus', 'blocksy' ),
						'id' => 'focus',
					],
				],
			],


			'search_button_icon_color' => [
				'label' => __( 'Search Icon Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'setting' => [ 'transport' => 'postMessage' ],

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
						'inherit' => 'rgba(255, 255, 255, 0.7)'
					],

					[
						'title' => __( 'Hover', 'blocksy' ),
						'id' => 'hover',
						'inherit' => '#ffffff'
					],
				],
			],

			'search_button_background_color' => [
				'label' => __( 'Search Button Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'setting' => [ 'transport' => 'postMessage' ],

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
						'inherit' => 'var(--paletteColor1)'
					],

					[
						'title' => __( 'Hover', 'blocksy' ),
						'id' => 'hover',
						'inherit' => 'var(--paletteColor1)'
					],
				],
			],

			'searchHeaderBackground' => [
				'label' => __( 'Modal Background', 'blocksy' ),
				'type'  => 'ct-background',
				'design' => 'inline',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_background_default_value([
					'backgroundColor' => [
						'default' => [
							'color' => 'rgba(18, 21, 25, 0.98)'
						],
					],
				])
			],

			'search_close_button_type' => [
				'label' => __('Close Button Type', 'blocksy'),
				'type' => 'ct-select',
				'value' => 'type-1',
				'view' => 'text',
				'design' => 'inline',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => blocksy_ordered_keys(
					[
						'type-1' => __( 'Simple', 'blocksy' ),
						'type-2' => __( 'Border', 'blocksy' ),
						'type-3' => __( 'Background', 'blocksy' ),
					]
				),
			],

			'search_close_button_color' => [
				'label' => __( 'Icon Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'setting' => [ 'transport' => 'postMessage' ],

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
						'inherit' => 'rgba(255, 255, 255, 0.7)'
					],

					[
						'title' => __( 'Hover', 'blocksy' ),
						'id' => 'hover',
						'inherit' => '#ffffff'
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'search_close_button_type' => 'type-2' ],
				'options' => [

					'search_close_button_border_color' => [
						'label' => __( 'Border Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],

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
								'inherit' => 'rgba(0, 0, 0, 0.5)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'rgba(0, 0, 0, 0.5)'
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'search_close_button_type' => 'type-3' ],
				'options' => [

					'search_close_button_shape_color' => [
						'label' => __( 'Background Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],

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
								'inherit' => 'rgba(0, 0, 0, 0.5)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'rgba(0, 0, 0, 0.5)'
							],
						],
					],

				],
			],

			'search_close_button_icon_size' => [
				'label' => __( 'Icon Size', 'blocksy' ),
				'type' => 'ct-number',
				'design' => 'inline',
				'value' => 12,
				'min' => 5,
				'max' => 50,
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'search_close_button_type' => '!type-1' ],
				'options' => [

					'search_close_button_border_radius' => [
						'label' => __( 'Border Radius', 'blocksy' ),
						'type' => 'ct-number',
						'design' => 'inline',
						'value' => 5,
						'min' => 0,
						'max' => 100,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'wp_customizer_current_view' => 'tablet|mobile' ],
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'header_search_visibility' => [
				'label' => __( 'Element Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'allow_empty' => true,
				'value' => [
					'tablet' => true,
					'mobile' => true,
				],

				'choices' => blocksy_ordered_keys([
					'tablet' => __( 'Tablet', 'blocksy' ),
					'mobile' => __( 'Mobile', 'blocksy' ),
				]),
			],

		],
	],
];
