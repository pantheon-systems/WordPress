<?php

if (! isset($location)) {
	$location = 'Header Menu 1';
}

$options = [
	'menu' => [
		'label' => __('Select Menu', 'blocksy'),
		'type' => 'ct-select',
		'value' => 'blocksy_location',
		'view' => 'text',
		'design' => 'inline',
		'setting' => ['transport' => 'postMessage'],
		'placeholder' => __('Select menu...', 'blocksy'),
		'choices' => blocksy_ordered_keys(blocksy_get_menus_items($location)),
		'desc' => sprintf(
			// translators: placeholder here means the actual URL.
			__( 'Manage your menu items in the %sMenus screen%s.', 'blocksy' ),
			sprintf(
				'<a href="%s" target="_blank">',
				admin_url('/nav-menus.php')
			),
			'</a>'
		),
	],

	blocksy_rand_md5() => [
		'type' => 'ct-title',
		'label' => __( 'Top Level Options', 'blocksy' ),
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'header_menu_type' => [
				'label' => false,
				'type' => 'ct-image-picker',
				'value' => 'type-1',
				'attr' => [ 'data-type' => 'background' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'switchDeviceOnChange' => 'desktop',
				'choices' => [

					'type-1' => [
						'src'   => blocksy_image_picker_url( 'menu-type-1.svg' ),
						'title' => __( 'Type 1', 'blocksy' ),
					],

					'type-2' => [
						'src'   => blocksy_image_picker_url( 'menu-type-2.svg' ),
						'title' => __( 'Type 2', 'blocksy' ),
					],

					'type-3' => [
						'src'   => blocksy_image_picker_url( 'menu-type-3.svg' ),
						'title' => __( 'Type 3', 'blocksy' ),
					],

					'type-4' => [
						'src'   => blocksy_image_picker_url( 'menu-type-4.svg' ),
						'title' => __( 'Type 4', 'blocksy' ),
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'header_menu_type' => 'type-2' ],
				'options' => [

					'menu_indicator_effect' => [
						'label' => __( 'Indicator Effect', 'blocksy' ),
						'type' => 'ct-select',
						'value' => 'default',
						'view' => 'text',
						'divider' => 'top',
						'design' => 'inline',
						'choices' => blocksy_ordered_keys(
							[
								'default' => __( 'Default', 'blocksy' ),
								'center' => __( 'Center to Sides', 'blocksy' ),
								'left' => __( 'Left to Right', 'blocksy' ),
							]
						),
					],

				],
			],

			'headerMenuItemsSpacing' => [
				'label' => __( 'Items Spacing', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => 25,
				'min' => 5,
				'max' => 100,
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'header_menu_type' => '!type-1' ],
				'options' => [

					'headerMenuItemsHeight' => [
						'label' => __( 'Items Height', 'blocksy' ),
						'type' => 'ct-slider',
						'value' => 100,
						'min' => 0,
						'max' => 100,
						'defaultUnit' => '%',
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

			'stretch_menu' => [
				'label' => __( 'Stretch Menu', 'blocksy' ),
				'type' => 'ct-switch',
				'value' => 'no',
				'divider' => 'top',
				'desc' => __('Enabling this option will make the menu to stretch and fit the width of its parent column. ', 'blocksy'),
				'setting' => [ 'transport' => 'postMessage' ],
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'headerMenuFont' => [
				'type' => 'ct-typography',
				'label' => __( 'Font', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '12px',
					'variation' => 'n7',
					'line-height' => '1.3',
					'text-transform' => 'uppercase',
				]),
				'typography_responsive' => [
					'desktop' => true,
					'tablet' => false,
					'mobile' => false,
				],
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Font Color', 'blocksy' ),
				'responsive' => false,
				'choices' => [
					[
						'id' => 'menuFontColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentMenuFontColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyMenuFontColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'menuFontColor' => [
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

							'hover-type-3' => [
								'color' => '#ffffff',
							],

							'active-type-3' => [
								'color' => '#ffffff',
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
								'condition' => [ 'header_menu_type' => '!type-3' ]
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'inherit' => 'var(--linkHoverColor)',
								'condition' => [ 'header_menu_type' => '!type-3' ],
								'inherit' => 'self:hover'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover-type-3',
								'condition' => [ 'header_menu_type' => 'type-3' ]
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active-type-3',
								'condition' => [ 'header_menu_type' => 'type-3' ],
							],
						],
					],

					'transparentMenuFontColor' => [
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

							'hover-type-3' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'active-type-3' => [
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
								'condition' => [ 'header_menu_type' => '!type-3' ]
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'condition' => [ 'header_menu_type' => '!type-3' ],
								'inherit' => 'self:hover'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover-type-3',
								'condition' => [ 'header_menu_type' => 'type-3' ],
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active-type-3',
								'condition' => [ 'header_menu_type' => 'type-3' ],
							],
						],
					],

					'stickyMenuFontColor' => [
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

							'hover-type-3' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'active-type-3' => [
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
								'condition' => [ 'header_menu_type' => '!type-3' ]
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'condition' => [ 'header_menu_type' => '!type-3' ],
								'inherit' => 'self:hover'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover-type-3',
								'condition' => [ 'header_menu_type' => 'type-3' ]
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active-type-3',
								'condition' => [ 'header_menu_type' => 'type-3' ],
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Active Indicator Color', 'blocksy' ),
				'responsive' => false,
				'divider' => 'top',
				'choices' => [
					[
						'id' => 'menuIndicatorColor',
						'label' => __('Default State', 'blocksy'),
						'condition' => [ 'header_menu_type' => '!type-1' ],
					],

					[
						'id' => 'transparentMenuIndicatorColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'header_menu_type' => '!type-1',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyMenuIndicatorColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'header_menu_type' => '!type-1',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'menuIndicatorColor' => [
						'label' => __( 'Active Indicator Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'active' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'inherit' => 'var(--paletteColor2)',
							],
						],
					],

					'transparentMenuIndicatorColor' => [
						'label' => __( 'Active Indicator Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'active' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
							],
						],
					],

					'stickyMenuIndicatorColor' => [
						'label' => __( 'Active Indicator Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'active' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'header_menu_type' => 'type-3' ],
				'options' => [

					'headerToplevelBorderRadius' => [
						'label' => __( 'Items Border Radius', 'blocksy' ),
						'type' => 'ct-spacing',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => blocksy_spacing_value([
							'linked' => true,
						]),
					],

				],
			],

			'headerMenuMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'top' => 'auto',
					'bottom' => 'auto',
					'linked' => true,
				]),
				// 'responsive' => true
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-title',
		'label' => __( 'Dropdown Options', 'blocksy' ),
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'dropdown_interaction' => [
				'label' => __('Interaction Type', 'blocksy'),
				'type' => 'ct-radio',
				'value' => 'hover',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'hover' => __( 'Hover', 'blocksy' ),
					'click' => __( 'Click', 'blocksy' ),
				],
				'desc' => __( 'Choose the interaction mode with the menu dropdown. ', 'blocksy' ),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'dropdown_interaction' => 'click' ],
				'options' => [

					'dropdown_click_interaction' => [
						'label' => __('Click Area', 'blocksy'),
						'type' => 'ct-radio',
						'value' => 'item',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'item' => __( 'Entire Item', 'blocksy' ),
							'arrow' => __( 'Only Arrow', 'blocksy' ),
						],
					],

				],
			],

			'dropdown_items_type' => [
				'label' => __('Items Hover Effect', 'blocksy'),
				'type' => 'ct-radio',
				'value' => 'simple',
				'view' => 'radio',
				'design' => 'block',
				'divider' => 'top:full',
				'attr' => [ 'data-columns' => '2' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'simple' => __( 'Simple', 'blocksy' ),
					'solid' => __( 'Solid Color', 'blocksy' ),
					'padded' => __( 'Boxed Color', 'blocksy' ),
					// 'bordered' => __( 'Bordered', 'blocksy' ),
				],
			],

			'dropdownItemsSpacing' => [
				'label' => __( 'Items Spacing', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => 13,
				'min' => 5,
				'max' => 30,
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'dropdown_animation' => [
				'label' => __('Reveal Effect', 'blocksy'),
				'type' => 'ct-radio',
				'value' => 'type-1',
				'view' => 'radio',
				'design' => 'block',
				'divider' => 'top:full',
				'attr' => [ 'data-columns' => '2' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'type-1' => __( 'Default', 'blocksy' ),
					'type-3' => __( 'Inner Reveal', 'blocksy' ),
					'type-2' => __( 'Opacity', 'blocksy' ),
					'type-4' => __( 'Simple', 'blocksy' ),
				],
			],

			'dropdownMenuWidth' => [
				'label' => __( 'Width', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => 200,
				'min' => 100,
				'max' => 300,
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'dropdownTopOffset' => [
				'label' => __( 'Top Offset', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => 0,
				'min' => -150,
				'max' => 150,
				'steps' => 'half',
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'builderSettings/has_sticky_header' => 'yes',
				],
				'options' => [

					'stickyStateDropdownTopOffset' => [
						'label' => __( 'Top Offset (Sticky State)', 'blocksy' ),
						'type' => 'ct-slider',
						'value' => 0,
						'min' => -150,
						'max' => 150,
						'steps' => 'half',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

				]
			],

			'dropdown_horizontal_offset' => [
				'label' => __( 'Horizontal Offset', 'blocksy' ),
				'type' => 'ct-slider',
				'value' => 5,
				'min' => 0,
				'max' => 20,
				'divider' => 'top',
				'setting' => [ 'transport' => 'postMessage' ],
				'desc' => __( 'Please note, this option will affect only submenus on 3rd level and below.', 'blocksy' ),
			],
		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'headerDropdownFont' => [
				'type' => 'ct-typography',
				'label' => __( 'Font', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '12px',
					'variation' => 'n5',
				]),
				'typography_responsive' => [
					'desktop' => true,
					'tablet' => false,
					'mobile' => false,
				],
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Font Color', 'blocksy' ),
				'responsive' => false,
				'divider' => 'bottom',
				'choices' => [
					[
						'id' => 'headerDropdownFontColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderDropdownFontColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderDropdownFontColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerDropdownFontColor' => [
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

					'transparentHeaderDropdownFontColor' => [
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

					'stickyHeaderDropdownFontColor' => [
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

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Items Background Color', 'blocksy' ),
				'responsive' => false,
				'divider' => 'bottom',
				'choices' => [
					[
						'id' => 'headerDropdownBackground',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderDropdownBackground',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderDropdownBackground',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerDropdownBackground' => [
						'label' => __( 'Items Background Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'divider' => 'bottom',
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
								'inherit' => 'var(--paletteColor4)'
							],

							[
								'title' => __( 'Hover/Active', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'rgba(255, 255, 255, 0.03)',
								'condition' => [ 'dropdown_items_type' => 'solid|padded' ]
							],
						],
					],

					'transparentHeaderDropdownBackground' => [
						'label' => __( 'Items Background Color', 'blocksy' ),
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
							],

							[
								'title' => __( 'Hover/Active', 'blocksy' ),
								'id' => 'hover',
								'condition' => [ 'dropdown_items_type' => 'solid|padded' ]
							],
						],
					],

					'stickyHeaderDropdownBackground' => [
						'label' => __( 'Items Background Color', 'blocksy' ),
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
							],

							[
								'title' => __( 'Hover/Active', 'blocksy' ),
								'id' => 'hover',
								'condition' => [ 'dropdown_items_type' => 'solid|padded' ]
							],
						],
					],

				],
			],

			'headerDropdownDivider' => [
				'label' => __( 'Items Divider', 'blocksy' ),
				'type' => 'ct-border',
				'design' => 'inline',
				'divider' => 'bottom',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'width' => 1,
					'style' => 'dashed',
					'color' => [
						'color' => 'rgba(255, 255, 255, 0.1)',
					],
				]
			],

			'headerDropdownShadow' => [
				'label' => __( 'Dropdown Shadow', 'blocksy' ),
				'type' => 'ct-box-shadow',
				'design' => 'inline',
				'divider' => 'bottom',
				'value' => blocksy_box_shadow_value([
					'enable' => true,
					'h_offset' => 0,
					'v_offset' => 10,
					'blur' => 20,
					'spread' => 0,
					'inset' => false,
					'color' => [
						'color' => 'rgba(41, 51, 61, 0.1)',
					],
				])
			],

			'headerDropdownRadius' => [
				'label' => __( 'Dropdown Border Radius', 'blocksy' ),
				'type' => 'ct-spacing',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => false,
					'top' => '0px',
					'left' => '2px',
					'right' => '0px',
					'bottom' => '2px',
				]),
				// 'responsive' => true
			],

		],
	],
];
