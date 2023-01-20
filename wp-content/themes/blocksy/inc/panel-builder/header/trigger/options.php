<?php

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'mobile_menu_trigger_type' => [
				'label' => false,
				'type' => 'ct-image-picker',
				'value' => 'type-1',
				'attr' => [
					'data-columns' => '3',
					'data-ratio' => '2:1',
				],
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [

					'type-1' => [
						'src'   => blocksy_image_picker_file( 'trigger-1' ),
						'title' => __( 'Type 1', 'blocksy' ),
					],

					'type-2' => [
						'src'   => blocksy_image_picker_file( 'trigger-2' ),
						'title' => __( 'Type 2', 'blocksy' ),
					],

					'type-3' => [
						'src'   => blocksy_image_picker_file( 'trigger-3' ),
						'title' => __( 'Type 3', 'blocksy' ),
					],
				],
			],

			'trigger_icon_size' => [
				'label' => __( 'Icon Size', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 5,
				'max' => 50,
				'value' => 18,
				'divider' => 'top',
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'trigger_design' => [
				'type' => 'ct-radio',
				'label' => __( 'Style', 'blocksy' ),
				'value' => 'simple',
				'view' => 'text',
				'design' => 'block',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],

				'choices' => [
					'simple' => __( 'Simple', 'blocksy' ),
					'outline' => __( 'Outline', 'blocksy' ),
					'solid' => __( 'Solid', 'blocksy' ),
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'trigger_label_visibility' => [
				'label' => __( 'Label Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
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
						'trigger_label_visibility/desktop' => true,
						'trigger_label_visibility/tablet' => true,
						'trigger_label_visibility/mobile' => true,
					]
				],
				'options' => [
					'trigger_label_alignment' => [
						'type' => 'ct-radio',
						'label' => __( 'Label Position', 'blocksy' ),
						'value' => 'right',
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

					'trigger_label' => [
						'label' => __( 'Label Text', 'blocksy' ),
						'type' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'value' => __( 'Menu', 'blocksy' ),
						'responsive' => [
							'tablet' => 'skip'
						],
						'setting' => [ 'transport' => 'postMessage' ],
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
						'trigger_label_visibility/desktop' => true,
						'trigger_label_visibility/tablet' => true,
						'trigger_label_visibility/mobile' => true,
					]
				],
				'options' => [
					'trigger_label_font' => [
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
								'id' => 'header_trigger_font_color',
								'label' => __('Default State', 'blocksy')
							],

							[
								'id' => 'transparent_header_trigger_font_color',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'sticky_header_trigger_font_color',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [
							'header_trigger_font_color' => [
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

							'transparent_header_trigger_font_color' => [
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

							'sticky_header_trigger_font_color' => [
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
						'id' => 'triggerIconColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentTriggerIconColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyTriggerIconColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'triggerIconColor' => [
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
								'inherit' => 'var(--paletteColor2)',
							],
						],
					],

					'transparentTriggerIconColor' => [
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

					'stickyTriggerIconColor' => [
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

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => [
					__('Border Color', 'blocksy') => [
						'trigger_design' => 'outline'
					],

					__('Background Color', 'blocksy') => [
						'trigger_design' => 'solid'
					]
				],
				'divider' => 'top',
				'responsive' => true,
				'choices' => [
					[
						'id' => 'triggerSecondColor',
						'label' => __('Default State', 'blocksy'),
						'condition' => [ 'trigger_design' => '!simple' ],
					],

					[
						'id' => 'transparentTriggerSecondColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'trigger_design' => '!simple',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyTriggerSecondColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'trigger_design' => '!simple',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'triggerSecondColor' => [
						'label' => __( 'Trigger Border Color', 'blocksy' ),
						'label' => [
							__('Border Color', 'blocksy') => [
								'trigger_design' => 'outline'
							],

							__('Background Color', 'blocksy') => [
								'trigger_design' => 'solid'
							]
						],
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
								'inherit' => 'rgba(224, 229, 235, 0.9)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'rgba(224, 229, 235, 0.9)'
							],
						],
					],

					'transparentTriggerSecondColor' => [
						'label' => __( 'Trigger Border Color', 'blocksy' ),
						'label' => [
							__('Border Color', 'blocksy') => [
								'trigger_design' => 'outline'
							],

							__('Background Color', 'blocksy') => [
								'trigger_design' => 'solid'
							]
						],
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

					'stickyTriggerSecondColor' => [
						'label' => __( 'Trigger Border Color', 'blocksy' ),
						'label' => [
							__('Border Color', 'blocksy') => [
								'trigger_design' => 'outline'
							],

							__('Background Color', 'blocksy') => [
								'trigger_design' => 'solid'
							]
						],
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
				'type' => 'ct-condition',
				'condition' => [ 'trigger_design' => '!simple' ],
				'options' => [

					'trigger_border_radius' => [
						'label' => __( 'Border Radius', 'blocksy' ),
						'type' => 'ct-number',
						'design' => 'inline',
						'value' => 3,
						'min' => 0,
						'max' => 100,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],


			'triggerMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true,
				'divider' => 'top'
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'wp_customizer_current_view' => 'tablet|mobile' ],
		'options' => [

			'header_trigger_visibility' => [
				'label' => __( 'Element Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top:full',
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
