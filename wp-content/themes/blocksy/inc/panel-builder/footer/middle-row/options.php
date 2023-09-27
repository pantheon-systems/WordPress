<?php

if (empty($default_background)) {
	$default_background = blocksy_background_default_value([
		'backgroundColor' => [
			'default' => [
				'color' => 'transparent'
			],
		],
	]);
}

if (empty($default_top_bottom_spacing)) {
	$default_top_bottom_spacing = [
		'desktop' => '70px',
		'tablet' => '50px',
		'mobile' => '40px',
	];
}

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'items_per_row' => [
				'label' => __( 'Columns', 'blocksy' ),
				'type' => 'ct-radio',
				'value' => '3',
				'view' => 'text',
				'design' => 'block',
				'allow_empty' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '2' ],
				'options' => [

					'2_columns_layout' => [
						'label' => __( 'Columns Layout', 'blocksy' ),
						'type' => 'ct-image-picker',
						'attr' => ['data-ratio' => '2:1'],
						'value' => [
							'desktop' => 'repeat(2, 1fr)',
							'tablet' => 'initial',
							'mobile' => 'initial'
						],
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
							],

							'2fr 1fr' => [
								'src' => blocksy_image_picker_file( '2-1' ),
							],

							'1fr 2fr' => [
								'src' => blocksy_image_picker_file( '1-2' ),
							],

							'3fr 1fr' => [
								'src' => blocksy_image_picker_file( '3-1' ),
							],

							'1fr 3fr' => [
								'src' => blocksy_image_picker_file( '1-3' ),
							],
						],

						'tabletChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],

						'mobileChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '3' ],
				'options' => [

					'3_columns_layout' => [
						'label' => __( 'Columns Layout', 'blocksy' ),
						'type' => 'ct-image-picker',
						'attr' => ['data-ratio' => '2:1'],
						'value' => [
							'desktop' => 'repeat(3, 1fr)',
							'tablet' => 'initial',
							'mobile' => 'initial',
						],
						'responsive' => true,
						'divider' => 'top',
						'setting' => ['transport' => 'postMessage'],
						'choices' => [
							'repeat(3, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1-1' ),
							],

							'1fr 2fr 1fr' => [
								'src' => blocksy_image_picker_file( '1-2-1' ),
							],

							'2fr 1fr 1fr' => [
								'src' => blocksy_image_picker_file( '2-1-1' ),
							],

							'1fr 1fr 2fr' => [
								'src' => blocksy_image_picker_file( '1-1-2' ),
							],
						],

						'tabletChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],

						'mobileChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '4' ],
				'options' => [

					'4_columns_layout' => [
						'label' => __( 'Columns Layout', 'blocksy' ),
						'type' => 'ct-image-picker',
						'attr' => ['data-ratio' => '2:1'],
						'value' => [
							'desktop' => 'repeat(4, 1fr)',
							'tablet' => 'initial',
							'mobile' => 'initial'
						],
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'repeat(4, 1fr)' => [
								'src'   => blocksy_image_picker_file( '1-1-1-1' ),
							],

							'1fr 2fr 2fr 1fr' => [
								'src'   => blocksy_image_picker_file( '1-2-2-1' ),
							],

							'2fr 1fr 1fr 1fr' => [
								'src'   => blocksy_image_picker_file( '2-1-1-1' ),
							],

							'1fr 1fr 1fr 2fr' => [
								'src'   => blocksy_image_picker_file( '1-1-1-2' ),
							],
						],

						'tabletChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],

						'mobileChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '5' ],
				'options' => [

					'5_columns_layout' => [
						'label' => __( 'Columns Layout', 'blocksy' ),
						'type' => 'ct-image-picker',
						'attr' => ['data-ratio' => '2:1'],
						'value' => [
							'desktop' => 'repeat(5, 1fr)',
							'tablet' => 'initial',
							'mobile' => 'initial'
						],
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'repeat(5, 1fr)' => [
								'src'   => blocksy_image_picker_file( '1-1-1-1-1' ),
							],

							'2fr 1fr 1fr 1fr 1fr' => [
								'src'   => blocksy_image_picker_file( '2-1-1-1-1' ),
							],

							'1fr 1fr 1fr 1fr 2fr' => [
								'src'   => blocksy_image_picker_file( '1-1-1-1-2' ),
							],

							'1fr 1fr 2fr 1fr 1fr' => [
								'src'   => blocksy_image_picker_file( '1-1-2-1-1' ),
							],
						],

						'tabletChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],

						'mobileChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '6' ],
				'options' => [

					'6_columns_layout' => [
						'label' => __( 'Columns Layout', 'blocksy' ),
						'type' => 'ct-image-picker',
						'attr' => ['data-ratio' => '2:1'],
						'value' => [
							'desktop' => 'repeat(6, 1fr)',
							'tablet' => 'initial',
							'mobile' => 'initial'
						],
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'repeat(6, 1fr)' => [
								'src'   => blocksy_image_picker_file( '1-1-1-1-1-1' ),
							],

							'2fr 1fr 1fr 1fr 1fr 1fr' => [
								'src'   => blocksy_image_picker_file( '2-1-1-1-1-1' ),
							],

							'1fr 1fr 1fr 1fr 1fr 2fr' => [
								'src'   => blocksy_image_picker_file( '1-1-1-1-1-2' ),
							],

							'1fr 1fr 2fr 2fr 1fr 1fr' => [
								'src'   => blocksy_image_picker_file( '1-1-2-2-1-1' ),
							],
						],

						'tabletChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],

						'mobileChoices' => [
							'initial' => [
								'src' => blocksy_image_picker_file( 'stacked' ),
								'title' => __( 'Stacked', 'blocksy' ),
							],

							'repeat(2, 1fr)' => [
								'src' => blocksy_image_picker_file( '1-1' ),
								'title' => __( 'Two Columns', 'blocksy' ),
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '!1' ],
				'options' => [

					'footerItemsGap' => [
						'label' => __( 'Columns Spacing', 'blocksy' ),
						'type' => 'ct-slider',
						'min' => 0,
						'max' => 200,
						'value' => 50,
						'responsive' => true,
						'divider' => 'bottom',
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

			'footerWidgetsGap' => [
				'label' => __( 'Widgets Spacing', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 0,
				'max' => 200,
				'value' => 40,
				'responsive' => true,
				'divider' => 'bottom',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'rowTopBottomSpacing' => [
				'label' => __( 'Row Vertical Spacing', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => $default_top_bottom_spacing,
				'units' => blocksy_units_config([
					[ 'unit' => 'px', 'min' => 0, 'max' => 500 ],
				]),
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footer_row_vertical_alignment' => [
				'type' => 'ct-radio',
				'label' => __( 'Vertical Alignment', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'responsive' => true,
				'attr' => [ 'data-type' => 'vertical-alignment' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'flex-start',
				'choices' => [
					'flex-start' => '',
					'center' => '',
					'flex-end' => '',
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footerRowWidth' => [
				'label' => __( 'Container Width', 'blocksy' ),
				'type' => 'ct-radio',
				'value' => 'fixed',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'fixed' => __( 'Default', 'blocksy' ),
					'fluid' => __( 'Full Width', 'blocksy' ),
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footerRowVisibility' => [
				'label' => __( 'Row Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],

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

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'has_widget_areas' => 'yes' ],
				'options' => [
					'footerWidgetsTitleFont' => [
						'type' => 'ct-typography',
						'label' => __( 'Widgets Title Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '16px',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'footerWidgetsTitleColor' => [
						'label' => __( 'Widgets Title Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit_source' => 'global',
								'inherit' => [
									'var(--heading-1-color, var(--headings-color))' => [
										'widgets_title_wrapper' => 'h1'
									],

									'var(--heading-2-color, var(--headings-color))' => [
										'widgets_title_wrapper' => 'h2'
									],

									'var(--heading-3-color, var(--headings-color))' => [
										'widgets_title_wrapper' => 'h3'
									],

									'var(--heading-4-color, var(--headings-color))' => [
										'widgets_title_wrapper' => 'h4'
									],

									'var(--heading-5-color, var(--headings-color))' => [
										'widgets_title_wrapper' => 'h5'
									],

									'var(--heading-6-color, var(--headings-color))' => [
										'widgets_title_wrapper' => 'h6'
									]
								]
							],
						],
					],

					'footerWidgetsFont' => [
						'type' => 'ct-typography',
						'label' => __( 'Widgets Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							// 'size' => '16px',
						]),
						'divider' => 'top:full',
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'rowFontColor' => [
						'label' => __( 'Widgets Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_initial' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Text Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Link Initial', 'blocksy' ),
								'id' => 'link_initial',
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Link Hover', 'blocksy' ),
								'id' => 'link_hover',
								'inherit' => 'var(--linkHoverColor)'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],
				],
			],

			'footerRowBackground' => [
				'label' => __( 'Row Background', 'blocksy' ),
				'type'  => 'ct-background',
				'design' => 'block:right',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => $default_background
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footerRowTopDivider' => [
				'label' => __( 'Row Top Divider', 'blocksy' ),
				'type' => 'ct-border',
				'design' => 'block',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'width' => 1,
					'style' => 'none',
					'color' => [
						'color' => '#dddddd',
					],
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'footerRowTopDivider/style:responsive' => '!none' ],
				'options' => [

					'footerRowTopBorderFullWidth' => [
						'label' => __( 'Top Border Width', 'blocksy' ),
						'type' => 'ct-radio',
						'value' => 'no',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'no' => __( 'Default', 'blocksy' ),
							'yes' => __( 'Full Width', 'blocksy' ),
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footerRowBottomDivider' => [
				'label' => __( 'Row Bottom Divider', 'blocksy' ),
				'type' => 'ct-border',
				'design' => 'block',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'width' => 1,
					'style' => 'none',
					'color' => [
						'color' => '#dddddd',
					],
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'footerRowBottomDivider/style:responsive' => '!none' ],
				'options' => [

					'footerRowBottomBorderFullWidth' => [
						'label' => __( 'Bottom Border Width', 'blocksy' ),
						'type' => 'ct-radio',
						'value' => 'no',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'no' => __( 'Default', 'blocksy' ),
							'yes' => __( 'Full Width', 'blocksy' ),
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'items_per_row' => '!1' ],
				'options' => [

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'footerColumnsDivider' => [
						'label' => __( 'Columns Divider', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => '#dddddd',
							],
						]
					],

				],
			],
		],
	],
];
