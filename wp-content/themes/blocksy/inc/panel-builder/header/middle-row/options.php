<?php

$has_shrink = false;

if (empty($default_height)) {
	$has_shrink = true;

	$default_height = [
		'mobile' => 70,
		'tablet' => 70,
		'desktop' => 120,
	];
}

if (empty($default_background)) {
	$default_background = blocksy_background_default_value([
		'backgroundColor' => [
			'default' => [
				'color' => 'var(--paletteColor8)',
			],
		],
	]);
}

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'headerRowWidth' => [
				'label' => __( 'Container Structure', 'blocksy' ),
				'type' => 'ct-radio',
				'value' => 'fixed',
				'view' => 'text',
				'design' => 'block',
				'responsive' => [
					'tablet' => 'skip'
				],
				'choices' => [
					'fixed' => __( 'Default', 'blocksy' ),
					'boxed' => __( 'Boxed', 'blocksy' ),
					'fluid' => __( 'Full Width', 'blocksy' ),
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'headerRowHeight' => [
				'label' => __( 'Row Min Height', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 20,
				'max' => 300,
				'responsive' => true,
				'value' => $default_height,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'has_sticky_shrink' => [
				'label' => __( 'Sticky State Row Shrink', 'blocksy' ),
				'type' => 'ct-switch',
				'type' => $has_shrink ? 'ct-switch' : 'hidden',
				'value' => 'no',
				'divider' => 'top',

				'sync' => [
					'id' => 'header_placements_1'
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'has_sticky_shrink' => 'yes' ],
				'options' => [

					'stickyHeaderRowShrink' => [
						'label' => __( 'Row Max Height', 'blocksy' ),
						'type' => $has_shrink ? 'ct-slider' : 'hidden',
						'min' => 30,
						'max' => 100,
						'responsive' => true,
						'value' => 70,
						'defaultUnit' => '%',
						'sync' => [
							'id' => 'header_placements_1'
						],
					],

				],
			],


			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'itemsCount' => '0' ],
				'options' => [
					'render_empty_row' => [
						'label' => __( 'Render Empty Row', 'blocksy' ),
						'type' => 'ct-switch',
						'type' => 'ct-switch',
						'value' => 'no',
						'divider' => 'top:full',
						'sync' => [
							'id' => 'header_placements_1'
						],
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
				'type' => 'ct-labeled-group',
				'label' => __( 'Background', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerRowBackground',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderRowBackground',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderRowBackground',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerRowBackground' => [
						'label' => __( 'Background', 'blocksy' ),
						'type'  => 'ct-background',
						'design' => 'block:right',
						'responsive' => true,
						'value' => $default_background,
						'sync' => 'live'
					],

					'transparentHeaderRowBackground' => [
						'label' => __( 'Background', 'blocksy' ),
						'type'  => 'ct-background',
						'design' => 'block:right',
						'responsive' => true,
						'value' => blocksy_background_default_value([
							'backgroundColor' => [
								'default' => [
									'color' => 'rgba(255,255,255,0)',
								],
							],
						]),
						'sync' => 'live'
					],

					'stickyHeaderRowBackground' => [
						'label' => __( 'Background', 'blocksy' ),
						'type'  => 'ct-background',
						'design' => 'block:right',
						'responsive' => true,
						'value' => $default_background,
						'sync' => 'live'
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Top Border', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerRowTopBorder',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderRowTopBorder',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderRowTopBorder',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerRowTopBorder' => [
						'label' => __( 'Top Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'responsive' => true,
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => 'rgba(44,62,80,0.2)',
							],
						]
					],

					'transparentHeaderRowTopBorder' => [
						'label' => __( 'Top Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						]
					],

					'stickyHeaderRowTopBorder' => [
						'label' => __( 'Top Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						]
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'headerRowTopBorder/style:responsive' => '!none',
						'transparentHeaderRowTopBorder/style:responsive' => '!none',
						'stickyHeaderRowTopBorder/style:responsive' => '!none',
					]
				],
				'options' => [

					'headerRowTopBorderFullWidth' => [
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

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Bottom Border', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerRowBottomBorder',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderRowBottomBorder',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderRowBottomBorder',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerRowBottomBorder' => [
						'label' => __( 'Bottom Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => 'rgba(44,62,80,0.2)',
							],
						]
					],

					'transparentHeaderRowBottomBorder' => [
						'label' => __( 'Bottom Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						]
					],

					'stickyHeaderRowBottomBorder' => [
						'label' => __( 'Bottom Border', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'block',
						'responsive' => true,
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						]
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'headerRowBottomBorder/style:responsive' => '!none',
						'transparentHeaderRowBottomBorder/style:responsive' => '!none',
						'stickyHeaderRowBottomBorder/style:responsive' => '!none'
					]
				],
				'options' => [

					'headerRowBottomBorderFullWidth' => [
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
				'type' => 'ct-divider',
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Shadow', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerRowShadow',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderRowShadow',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderRowShadow',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerRowShadow' => [
						'label' => __( 'Shadow', 'blocksy' ),
						'type' => 'ct-box-shadow',
						'responsive' => true,
						'hide_shadow_placement' => true,
						'value' => blocksy_box_shadow_value([
							'enable' => false,
							'h_offset' => 0,
							'v_offset' => 10,
							'blur' => 20,
							'spread' => 0,
							'inset' => false,
							'color' => [
								'color' => 'rgba(44,62,80,0.05)',
							],
						])
					],

					'transparentHeaderRowShadow' => [
						'label' => __( 'Shadow', 'blocksy' ),
						'type' => 'ct-box-shadow',
						'responsive' => true,
						'hide_shadow_placement' => true,
						'value' => blocksy_box_shadow_value([
							'enable' => false,
							'h_offset' => 0,
							'v_offset' => 10,
							'blur' => 20,
							'spread' => 0,
							'inset' => false,
							'color' => [
								'color' => 'rgba(44,62,80,0.05)',
							],
						])
					],

					'stickyHeaderRowShadow' => [
						'label' => __( 'Shadow', 'blocksy' ),
						'type' => 'ct-box-shadow',
						'responsive' => true,
						'hide_shadow_placement' => true,
						'value' => blocksy_box_shadow_value([
							'enable' => false,
							'h_offset' => 0,
							'v_offset' => 10,
							'blur' => 20,
							'spread' => 0,
							'inset' => false,
							'color' => [
								'color' => 'rgba(44,62,80,0.05)',
							],
						])
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'headerRowWidth:responsive' => 'boxed' ],
				'options' => [
					'header_row_border_radius' => [
						'label' => __( 'Border Radius', 'blocksy' ),
						'type' => 'ct-spacing',
						'sync' => 'live',
						'divider' => 'top: full',
						'value' => blocksy_spacing_value([
							'linked' => true,
						]),
						'responsive' => true
					],
				],
			],

		],
	],
];
