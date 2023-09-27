<?php

$options = [
	'menu' => [
		'label' => __('Select Menu', 'blocksy'),
		'type' => 'ct-select',
		'value' => 'blocksy_location',
		'view' => 'text',
		'design' => 'inline',
		'setting' => [ 'transport' => 'postMessage' ],
		'placeholder' => __('Select menu...', 'blocksy'),
		'choices' => blocksy_ordered_keys(blocksy_get_menus_items()),
		'desc' => sprintf(
			// translators: placeholder here means the actual URL.
			__( 'Manage your menus in the %sMenus screen%s.', 'blocksy' ),
			sprintf(
				'<a href="%s" target="_blank">',
				admin_url('/nav-menus.php')
			),
			'</a>'
		),
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'row' => 'offcanvas' ],
				'options' => [

					'mobile_menu_interactive' => [
						'label' => __( 'Interactive Collapse', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'yes',
						'setting' => [ 'transport' => 'postMessage' ],
						'desc' => __('This option will collapse/expand the sub menu items on click/touch.', 'blocksy'),
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'mobile_menu_interactive' => 'yes' ],
						'options' => [

							'mobile_menu_type' => [
								'label' => __( 'Dropdown Toggle Icon', 'blocksy' ),
								'type' => 'ct-image-picker',
								'value' => 'type-1',
								'divider' => 'top',
								'attr' => [
									'data-type' => 'background',
									'data-columns' => '3',
								],
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [
									'type-1' => [
										'src'   => blocksy_image_picker_file( 'mobile-toggle-type-1' ),
										'title' => __( 'Type 1', 'blocksy' ),
									],

									'type-2' => [
										'src'   => blocksy_image_picker_file( 'mobile-toggle-type-2' ),
										'title' => __( 'Type 2', 'blocksy' ),
									],

									'type-3' => [
										'src'   => blocksy_image_picker_file( 'mobile-toggle-type-3' ),
										'title' => __( 'Type 3', 'blocksy' ),
									],
								],
							],

							'mobile_menu_toggle_shape' => [
								'label' => __('Dropdown Toggle Shape', 'blocksy'),
								'type' => 'ct-radio',
								'value' => 'type-1',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [
									'type-1' => __( 'Simple', 'blocksy' ),
									'type-2' => __( 'Border', 'blocksy' ),
								],
							],

						],
					],

					'mobile_menu_items_spacing' => [
						'label' => __( 'Items Vertical Spacing', 'blocksy' ),
						'type' => 'ct-slider',
						'min' => 0,
						'max' => 50,
						'value' => 5,
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'row' => '!offcanvas' ],
				'options' => [

					'inline_menu_items_spacing' => [
						'label' => __( 'Items Spacing', 'blocksy' ),
						'type' => 'ct-slider',
						'value' => 25,
						'min' => 5,
						'max' => 100,
						'divider' => 'top',
						'responsive' => [
							'desktop' => 'skip'
						],
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'inline_menu_stretch_menu' => [
						'label' => __( 'Stretch Menu', 'blocksy' ),
						'type' => 'ct-switch',
						'value' => 'no',
						'divider' => 'top',
						'desc' => __('Enabling this option will make the menu to stretch and fit the width of its parent column. ', 'blocksy'),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'inline_menu_horizontal_alignment' => [
						'type' => 'ct-radio',
						'label' => __( 'Horizontal Alignment', 'blocksy' ),
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'responsive' => [
							'desktop' => 'skip'
						],
						'attr' => [ 'data-type' => 'alignment' ],
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => 'CT_CSS_SKIP_RULE',
						'choices' => [
							'flex-start' => '',
							'center' => '',
							'flex-end' => '',
						],
					],

					'inline_menu_visibility' => [
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


		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'row' => 'offcanvas' ],
				'options' => [

					'mobileMenuFont' => [
						'type' => 'ct-typography',
						'label' => __( 'Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '20px',
							'variation' => 'n7',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'mobileMenuColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'default' => [
								'color' => '#ffffff',
							],

							'hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'active' => [
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

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'inherit' => 'self:hover'
							],
						],
					],

					'mobileMenuDropdownFont' => [
						'type' => 'ct-typography',
						'label' => __( 'Dropdown Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([]),
						'setting' => [ 'transport' => 'postMessage' ],
						'divider' => 'top',
					],

					'mobileMenuDropdownColor' => [
						'label' => __( 'Dropdown Font Color', 'blocksy' ),
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

							'active' => [
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
								// 'inherit' => 'var(--linkHoverColor)'
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'inherit' => 'self:hover'
							],
						],
					],

					// 'mobile_menu_child_size' => [
					// 	'label' => __( 'Dropdown Font Size', 'blocksy' ),
					// 	'type' => 'ct-slider',
					// 	'value' => '20px',
					// 	'divider' => 'top',
					// 	'units' => [
					// 		[ 'unit' => 'px', 'min' => 0, 'max' => 100 ],
					// 		[ 'unit' => 'pt', 'min' => 0, 'max' => 500 ],
					// 		[ 'unit' => 'em', 'min' => 0, 'max' => 100 ],
					// 		[ 'unit' => 'rem', 'min' => 0, 'max' => 100 ],
					// 		[ 'unit' => 'vw', 'min' => 0, 'max' => 50 ],
					// 	],
					// 	'setting' => [ 'transport' => 'postMessage' ],
					// ],

					'mobile_menu_items_divider' => [
						'label' => __( 'Items Divider', 'blocksy' ),
						'type' => 'ct-border',
						'design' => 'inline',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'width' => 1,
							'style' => 'none',
							'color' => [
								'color' => 'rgba(255, 255, 255, 0.2)',
							],
						]
					],

					'mobileMenuMargin' => [
						'label' => __( 'Container Margin', 'blocksy' ),
						'type' => 'ct-spacing',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => blocksy_spacing_value([
							'left' => 'auto',
							'right' => 'auto',
							'linked' => true,
						]),
						'responsive' => true
					],

				],
			],


			// inline menu
			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'row' => '!offcanvas' ],
				'options' => [

					'inline_mobile_menu_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '12px',
							'variation' => 'n7',
							'line-height' => '1.3',
							'text-transform' => 'uppercase',
						]),
						'typography_responsive' => [
							'desktop' => false,
							'tablet' => true,
							'mobile' => true,
						],
						'setting' => [ 'transport' => 'postMessage' ],
					],


					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Font Color', 'blocksy' ),
						'responsive' => false,
						'choices' => [
							[
								'id' => 'inline_menu_font_color',
								'label' => __('Default State', 'blocksy')
							],

							[
								'id' => 'transparent_inline_menu_font_color',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'sticky_inline_menu_font_color',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [

							'inline_menu_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => 'var(--color)',
									],

									'hover' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],

									'active' => [
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
										'inherit' => 'var(--linkHoverColor)',
									],

									[
										'title' => __( 'Active', 'blocksy' ),
										'id' => 'active',
										'inherit' => 'self:hover'
									],
								],
							],

							'transparent_inline_menu_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
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

									'active' => [
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

									[
										'title' => __( 'Active', 'blocksy' ),
										'id' => 'active',
										'inherit' => 'self:hover'
									],
								],
							],

							'sticky_inline_menu_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
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

									'active' => [
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

									[
										'title' => __( 'Active', 'blocksy' ),
										'id' => 'active',
										'inherit' => 'self:hover'
									],
								],
							],

						],
					],

					'inline_menu_margin' => [
						'label' => __( 'Margin', 'blocksy' ),
						'type' => 'ct-spacing',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => blocksy_spacing_value([
							'linked' => true,
						]),
					],

				],
			],

		],
	],
];
