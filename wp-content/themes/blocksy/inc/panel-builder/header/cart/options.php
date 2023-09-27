<?php

$options = [

	blocksy_rand_md5() => [
		'type' => 'ct-title',
		'label' => __( 'Top Level Options', 'blocksy' ),
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			apply_filters(
				'blocksy:header:cart:options:icon',
				[
					'mini_cart_type' => [
						'label' => false,
						'type' => 'ct-image-picker',
						'value' => 'type-1',
						'attr' => [
							'data-type' => 'background',
							'data-columns' => '3',
						],
						'divider' => 'bottom',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
		
							'type-1' => [
								'src'   => blocksy_image_picker_file( 'cart-1' ),
								'title' => __( 'Type 1', 'blocksy' ),
							],
		
							'type-2' => [
								'src'   => blocksy_image_picker_file( 'cart-2' ),
								'title' => __( 'Type 2', 'blocksy' ),
							],
		
							'type-3' => [
								'src'   => blocksy_image_picker_file( 'cart-3' ),
								'title' => __( 'Type 3', 'blocksy' ),
							],
		
							'type-4' => [
								'src'   => blocksy_image_picker_file( 'cart-4' ),
								'title' => __( 'Type 4', 'blocksy' ),
							],
		
							'type-5' => [
								'src'   => blocksy_image_picker_file( 'cart-5' ),
								'title' => __( 'Type 5', 'blocksy' ),
							],
		
							'type-6' => [
								'src'   => blocksy_image_picker_file( 'cart-6' ),
								'title' => __( 'Type 6', 'blocksy' ),
							],
						],
					],
				]
			),

			'cartIconSize' => [
				'label' => __( 'Icon Size', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 5,
				'max' => 50,
				'value' => 15,
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'has_cart_badge' => [
				'label' => __( 'Icon Badge', 'blocksy' ),
				'type' => 'ct-switch',
				'value' => 'yes',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'cart_subtotal_visibility' => [
				'label' => __( 'Cart Total Visibility', 'blocksy' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top',
				'allow_empty' => true,
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

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'cart_subtotal_visibility/desktop' => true,
						'cart_subtotal_visibility/tablet' => true,
						'cart_subtotal_visibility/mobile' => true,
					]
				],
				'options' => [
					'cart_total_position' => [
						'type' => 'ct-radio',
						'label' => __( 'Cart Total Position', 'blocksy' ),
						'value' => 'left',
						'view' => 'text',
						'divider' => 'top',
						'design' => 'block',
						'responsive' => [
							'tablet' => 'skip'
						],
						'choices' => [
							'left' => __( 'Left', 'blocksy' ),
							'right' => __( 'Right', 'blocksy' ),
							'bottom' => __( 'Bottom', 'blocksy' ),
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
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'cart_subtotal_visibility/desktop' => true,
						'cart_subtotal_visibility/tablet' => true,
						'cart_subtotal_visibility/mobile' => true,
					]
				],
				'options' => [
					'cart_total_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Cart Total Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '12px',
							'variation' => 'n6',
							'text-transform' => 'uppercase',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Cart Total Font Color', 'blocksy' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'cart_total_font_color',
								'label' => __('Default State', 'blocksy')
							],

							[
								'id' => 'transparent_cart_total_font_color',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'sticky_cart_total_font_color',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [
							'cart_total_font_color' => [
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

							'transparent_cart_total_font_color' => [
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

							'sticky_cart_total_font_color' => [
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
						'id' => 'cartHeaderIconColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentCartHeaderIconColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyCartHeaderIconColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [
					'cartHeaderIconColor' => [
						'label' => __( 'Icon Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'default' => [
								'color' => 'var(--color)',
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
								'inherit' => 'var(--paletteColor2)',
							],
						],
					],

					'transparentCartHeaderIconColor' => [
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

					'stickyCartHeaderIconColor' => [
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
				'label' => __( 'Badge Color', 'blocksy' ),
				'responsive' => true,
				'divider' => 'top',
				'choices' => [
					[
						'id' => 'cartBadgeColor',
						'label' => __('Default State', 'blocksy'),
						'condition' => [
							'has_cart_badge' => 'yes',
						],
					],

					[
						'id' => 'transparentCartBadgeColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'has_cart_badge' => 'yes',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyCartBadgeColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'has_cart_badge' => 'yes',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'cartBadgeColor' => [
						'label' => __( 'Badge Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'background' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'text' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Background', 'blocksy' ),
								'id' => 'background',
								'inherit' => 'var(--paletteColor1)',
							],

							[
								'title' => __( 'Text', 'blocksy' ),
								'id' => 'text',
								'inherit' => '#ffffff',
							],
						],
					],

					'transparentCartBadgeColor' => [
						'label' => __( 'Badge Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'divider' => 'top',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'background' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'text' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Background', 'blocksy' ),
								'id' => 'background',
							],

							[
								'title' => __( 'Text', 'blocksy' ),
								'id' => 'text',
							],
						],
					],

					'stickyCartBadgeColor' => [
						'label' => __( 'Badge Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'divider' => 'top',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'background' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'text' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Background', 'blocksy' ),
								'id' => 'background',
							],

							[
								'title' => __( 'Text', 'blocksy' ),
								'id' => 'text',
							],
						],
					],

				],
			],

			'headerCartMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
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
		'type' => 'ct-divider',
	],

	'has_cart_dropdown' => [
		'label' => __( 'Cart Drawer', 'blocksy' ),
		'type' => 'ct-switch',
		'value' => 'yes',
		'wrapperAttr' => [ 'data-label' => 'heading-label' ],
		'setting' => [ 'transport' => 'postMessage' ],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'has_cart_dropdown' => 'yes' ],
		'options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					'cart_drawer_type' => [
						'label' => __('Cart Drawer Type', 'blocksy'),
						'type' => apply_filters(
							'blocksy:header:cart:cart_drawer_type:option',
							'hidden'
						),
						'value' => 'dropdown',
						'divider' => 'bottom',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'dropdown' => [
								'src' => blocksy_image_picker_url('cart-1.svg'),
								'title' => __( 'Dropdown', 'blocksy' ),
							],

							'offcanvas' => [
								'src' => blocksy_image_picker_url('cart-2.svg'),
								'title' => __( 'Off Canvas', 'blocksy' ),
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'cart_drawer_type' => 'dropdown' ],
						'options' => [
							'cartDropdownTopOffset' => [
								'label' => __( 'Dropdown Top Offset', 'blocksy' ),
								'type' => 'ct-slider',
								'value' => 15,
								'min' => 0,
								'max' => 50,
								'setting' => [ 'transport' => 'postMessage' ],
							],
						]
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'cart_drawer_type' => 'offcanvas' ],
						'options' => [

							'cart_panel_width' => [
								'label' => __( 'Panel Width', 'blocksy' ),
								'type' => 'ct-slider',
								'value' => [
									'desktop' => '500px',
									'tablet' => '65vw',
									'mobile' => '90vw',
								],
								'units' => blocksy_units_config([
									[ 'unit' => 'px', 'min' => 0, 'max' => 1000 ],
								]),
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],
							],

							'cart_panel_position' => [
								'label' => __('Reveal From', 'blocksy'),
								'type' => 'ct-radio',
								'value' => 'right',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [
									'left' => __( 'Left Side', 'blocksy' ),
									'right' => __( 'Right Side', 'blocksy' ),
								],
							],

							'auto_open_cart' => [
								'label' => __( 'Open Cart Automatically On', 'blocksy' ),
								'type' => 'ct-checkboxes',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'allow_empty' => true,
								'setting' => ['transport' => 'postMessage'],
								'desc' => __( 'Automatically open the cart drawer after a product is added to cart.', 'blocksy' ),
								'value' => [
									'archive' => false,
									'product' => false,
								],
								'choices' => blocksy_ordered_keys([
									'archive' => __('Archive Page', 'blocksy'),
									'product' => __('Product Page', 'blocksy'),
								]),
							],

							'has_cart_panel_quantity' => [
								'label' => __( 'Quantity Input', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'no',
								'divider' => 'top',
								'desc' => __( 'Display the quantity input field inside the off-canvas cart panel.', 'blocksy' ),
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
						'condition' => [ 'cart_drawer_type' => 'dropdown' ],
						'options' => [

							'cartFontColor' => [
								'label' => __( 'Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => '#ffffff',
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
									],

									[
										'title' => __( 'Link Initial', 'blocksy' ),
										'id' => 'link_initial',
										'inherit' => 'var(--linkInitialColor)'
									],

									[
										'title' => __( 'Link Hover', 'blocksy' ),
										'id' => 'link_hover',
										'inherit' => 'var(--linkHoverColor)'
									],
								],
							],

							'cartTotalFontColor' => [
								'label' => __( 'Subtotal Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => '#ffffff',
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
									],
								],
							],

							'cartDropDownBackground' => [
								'label' => __( 'Background Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => '#29333C',
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'cart_drawer_type' => 'offcanvas' ],
						'options' => [

							'cart_panel_heading_font_color' => [
								'label' => __( 'Panel Heading Font Color', 'blocksy' ),
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
										'title' => __( 'Text Initial', 'blocksy' ),
										'id' => 'default',
										'inherit' => 'var(--headings-color)'
									],
								],
							],

							'cart_panel_font_color' => [
								'label' => __( 'Products Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'divider' => 'top',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],

									'link_initial' => [
										'color' => 'var(--headings-color)',
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
									],

									[
										'title' => __( 'Link Hover', 'blocksy' ),
										'id' => 'link_hover',
										'inherit' => 'var(--linkHoverColor)'
									],
								],
							],

							'cart_panel_total_font_color' => [
								'label' => __( 'Subtotal Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'divider' => 'top',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],
								],

								'pickers' => [
									[
										'title' => __( 'Text Initial', 'blocksy' ),
										'id' => 'default',
										'inherit' => 'var(--color)'
									],
								],
							],

							'cart_panel_background' => [
								'label' => __( 'Panel Background', 'blocksy' ),
								'type'  => 'ct-background',
								'design' => 'block:right',
								'responsive' => true,
								'divider' => 'top:full',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => blocksy_background_default_value([
									'backgroundColor' => [
										'default' => [
											'color' => '#ffffff'
										],
									],
								])
							],

							'cart_panel_backdrop' => [
								'label' => __( 'Panel Backdrop', 'blocksy' ),
								'type'  => 'ct-background',
								'design' => 'block:right',
								'responsive' => true,
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => blocksy_background_default_value([
									'backgroundColor' => [
										'default' => [
											'color' => 'rgba(18, 21, 25, 0.6)'
										],
									],
								])
							],

							'cart_panel_shadow' => [
								'label' => __( 'Panel Shadow', 'blocksy' ),
								'type' => 'ct-box-shadow',
								'design' => 'block',
								'divider' => 'top',
								'responsive' => true,
								'value' => blocksy_box_shadow_value([
									'enable' => true,
									'h_offset' => 0,
									'v_offset' => 0,
									'blur' => 70,
									'spread' => 0,
									'inset' => false,
									'color' => [
										'color' => 'rgba(0, 0, 0, 0.35)',
									],
								])
							],

							'cart_panel_close_button_type' => [
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

							'cart_panel_close_button_color' => [
								'label' => __( 'Icon Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => 'rgba(0, 0, 0, 0.5)',
									],

									'hover' => [
										'color' => 'rgba(0, 0, 0, 0.8)',
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

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => [ 'cart_panel_close_button_type' => 'type-2' ],
								'options' => [

									'cart_panel_close_button_border_color' => [
										'label' => __( 'Border Color', 'blocksy' ),
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
								'condition' => [ 'cart_panel_close_button_type' => 'type-3' ],
								'options' => [

									'cart_panel_close_button_shape_color' => [
										'label' => __( 'Background Color', 'blocksy' ),
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

							'cart_panel_close_button_icon_size' => [
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
								'condition' => [ 'cart_panel_close_button_type' => '!type-1' ],
								'options' => [

									'cart_panel_close_button_border_radius' => [
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

							blocksy_rand_md5() => [
								'type' => 'ct-spacer',
								'height' => 50
							],

						],
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

			'header_cart_visibility' => [
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
