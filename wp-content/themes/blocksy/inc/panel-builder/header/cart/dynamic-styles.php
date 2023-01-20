<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

$cart_drawer_type = blocksy_akg( 'cart_drawer_type', $atts, 'dropdown' );

// Icon size
$cartIconSize = blocksy_akg('cartIconSize', $atts, 15);

if ($cartIconSize !== 15) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'icon-size',
		'value' => $cartIconSize
	]);
}

blocksy_output_colors([
	'value' => blocksy_akg('cartHeaderIconColor', $atts),
	'default' => [
		'default' => [ 'color' => 'var(--color)' ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'icon-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'icon-hover-color'
		],
	],
	'responsive' => true
]);

$has_subtotal = (
	is_customize_preview()
	||
	blocksy_some_device(blocksy_default_akg(
		'cart_subtotal_visibility',
		$atts,
		[
			'desktop' => true,
			'tablet' => true,
			'mobile' => true,
		]
	))
);

$has_badge = (
	is_customize_preview()
	||
	blocksy_default_akg('has_cart_badge', $atts, 'yes') === 'yes'
);

if ($has_badge) {
	blocksy_output_colors([
		'value' => blocksy_akg('cartBadgeColor', $atts),
		'default' => [
			'background' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'text' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'background' => [
				'selector' => blocksy_assemble_selector($root_selector),
				'variable' => 'cartBadgeBackground'
			],

			'text' => [
				'selector' => blocksy_assemble_selector($root_selector),
				'variable' => 'cartBadgeText'
			],
		],
		'responsive' => true
	]);
}

if ($has_subtotal) {
	blocksy_output_font_css([
		'font_value' => blocksy_akg( 'cart_total_font', $atts,
		blocksy_typography_default_values([
			'size' => '12px',
			'variation' => 'n6',
			'text-transform' => 'uppercase',
		])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-label'
			])
		)
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('cart_total_font_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-item'
					])
				),
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-item'
					])
				),
				'variable' => 'linkHoverColor'
			],
		],
		'responsive' => true
	]);
}

// transparent state
if (isset($has_transparent_header) && $has_transparent_header) {
	if ($has_subtotal) {
		blocksy_output_colors([
			'value' => blocksy_akg('transparent_cart_total_font_color', $atts),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables' => [
				'default' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => blocksy_mutate_selector([
								'selector' => $root_selector,
								'operation' => 'suffix',
								'to_add' => '.ct-cart-item'
							]),
							'operation' => 'between',
							'to_add' => '[data-transparent-row="yes"]'
						])
					),
					'variable' => 'linkInitialColor'
				],

				'hover' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => blocksy_mutate_selector([
								'selector' => $root_selector,
								'operation' => 'suffix',
								'to_add' => '.ct-cart-item'
							]),
							'operation' => 'between',
							'to_add' => '[data-transparent-row="yes"]'
						])
					),
					'variable' => 'linkHoverColor'
				],
			],
			'responsive' => true
		]);
	}

	blocksy_output_colors([
		'value' => blocksy_akg('transparentCartHeaderIconColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'icon-hover-color'
			],
		],
		'responsive' => true
	]);

	// Badge color
	if ($has_badge) {
		blocksy_output_colors([
			'value' => blocksy_akg('transparentCartBadgeColor', $atts),
			'default' => [
				'background' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'text' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables' => [
				'background' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'between',
							'to_add' => '[data-transparent-row="yes"]'
						])
					),
					'variable' => 'cartBadgeBackground'
				],

				'text' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'between',
							'to_add' => '[data-transparent-row="yes"]'
						])
					),
					'variable' => 'cartBadgeText'
				],
			],
			'responsive' => true
		]);
	}
}


// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {
	if ($has_subtotal) {
		blocksy_output_colors([
			'value' => blocksy_akg('sticky_cart_total_font_color', $atts),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables' => [
				'default' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => blocksy_mutate_selector([
								'selector' => $root_selector,
								'operation' => 'suffix',
								'to_add' => '.ct-cart-item'
							]),
							'operation' => 'between',
							'to_add' => '[data-sticky*="yes"]'
						])
					),
					'variable' => 'linkInitialColor'
				],

				'hover' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => blocksy_mutate_selector([
								'selector' => $root_selector,
								'operation' => 'suffix',
								'to_add' => '.ct-cart-item'
							]),
							'operation' => 'between',
							'to_add' => '[data-sticky*="yes"]'
						])
					),
					'variable' => 'linkHoverColor'
				],
			],
			'responsive' => true
		]);
	}

	blocksy_output_colors([
		'value' => blocksy_akg('stickyCartHeaderIconColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'icon-hover-color'
			],
		],
		'responsive' => true
	]);


	// Badge color
	if ($has_badge) {
		blocksy_output_colors([
			'value' => blocksy_akg('stickyCartBadgeColor', $atts),
			'default' => [
				'background' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'text' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables' => [
				'background' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'between',
							'to_add' => '[data-sticky*="yes"]'
						])
					),
					'variable' => 'cartBadgeBackground'
				],

				'text' => [
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'between',
							'to_add' => '[data-sticky*="yes"]'
						])
					),
					'variable' => 'cartBadgeText'
				],
			],
			'responsive' => true
		]);
	}
}

// dropdown type
if ($cart_drawer_type === 'dropdown' || is_customize_preview()) {

	// Dropdown top offset
	$cartDropdownTopOffset = blocksy_akg( 'cartDropdownTopOffset', $atts, 15 );
	$css->put(
		blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-cart-content'
			])
		),
		'--dropdownTopOffset: ' . $cartDropdownTopOffset . 'px'
	);

	// Cart font color
	blocksy_output_colors([
		'value' => blocksy_akg('cartFontColor', $atts),
		'default' => [
			'default' => [ 'color' => '#ffffff' ],
			'link_initial' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'link_hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-content'
					])
				),
				'variable' => 'color'
			],

			'link_initial' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-content'
					])
				),
				'variable' => 'linkInitialColor'
			],

			'link_hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-content'
					])
				),
				'variable' => 'linkHoverColor'
			],
		],
	]);

	// Cart total font color
	blocksy_output_colors([
		'value' => blocksy_akg('cartTotalFontColor', $atts),
		'default' => [
			'default' => [ 'color' => '#ffffff' ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-content .total'
					])
				),
				'variable' => 'color'
			],
		],
	]);

	// Cart dropdown
	blocksy_output_colors([
		'value' => blocksy_akg('cartDropDownBackground', $atts),
		'default' => ['default' => ['color' => '#29333C']],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => '.ct-cart-content'
					])
				),
				'variable' => 'backgroundColor'
			]
		],
	]);
}


// offcanvas type
if ($cart_drawer_type === 'offcanvas' || is_customize_preview()) {

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '#woo-cart-panel',
		'variableName' => 'side-panel-width',
		'responsive' => true,
		'unit' => '',
		'value' => blocksy_akg('cart_panel_width', $atts, [
			'desktop' => '500px',
			'tablet' => '65vw',
			'mobile' => '90vw',
		])
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('cart_panel_heading_font_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => '#woo-cart-panel .ct-panel-actions',
				'variable' => 'color'
			],
		],
		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('cart_panel_font_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'link_initial' => [ 'color' => 'var(--headings-color)' ],
			'link_hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => '#woo-cart-panel .cart_list, #woo-cart-panel [class*="empty-message"]',
				'variable' => 'color'
			],

			'link_initial' => [
				'selector' => '#woo-cart-panel .cart_list',
				'variable' => 'linkInitialColor'
			],

			'link_hover' => [
				'selector' => '#woo-cart-panel .cart_list',
				'variable' => 'linkHoverColor'
			],
		],
		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('cart_panel_total_font_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => '#woo-cart-panel .total',
				'variable' => 'color'
			],
		],
		'responsive' => true
	]);

	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '#woo-cart-panel',
		'value' => blocksy_akg('cart_panel_shadow', $atts, blocksy_box_shadow_value([
			'enable' => true,
			'h_offset' => 0,
			'v_offset' => 0,
			'blur' => 70,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(0, 0, 0, 0.35)',
			],
		])),
		'responsive' => true
	]);

	blocksy_output_background_css([
		'selector' => '#woo-cart-panel .ct-panel-inner',
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'value' => blocksy_akg('cart_panel_background', $atts,
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => '#ffffff'
					],
				],
			])
		)
	]);

	blocksy_output_background_css([
		'selector' => '#woo-cart-panel',
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'value' => blocksy_akg('cart_panel_backdrop', $atts,
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'rgba(18, 21, 25, 0.6)'
					],
				],
			])
		)
	]);

	$close_button_type = blocksy_akg('cart_panel_close_button_type', $atts, 'type-1');

	blocksy_output_colors([
		'value' => blocksy_akg('cart_panel_close_button_color', $atts),
		'default' => [
			'default' => [ 'color' => 'rgba(0, 0, 0, 0.5)' ],
			'hover' => [ 'color' => 'rgba(0, 0, 0, 0.8)' ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'variables' => [
			'default' => [
				'selector' => '#woo-cart-panel .ct-toggle-close',
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => '#woo-cart-panel .ct-toggle-close:hover',
				'variable' => 'icon-color'
			]
		],
	]);

	if ($close_button_type === 'type-2') {
		blocksy_output_colors([
			'value' => blocksy_akg('cart_panel_close_button_border_color', $atts),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'responsive' => true,
			'variables' => [
				'default' => [
					'selector' => '#woo-cart-panel .ct-toggle-close[data-type="type-2"]',
					'variable' => 'toggle-button-border-color'
				],

				'hover' => [
					'selector' => '#woo-cart-panel .ct-toggle-close[data-type="type-2"]:hover',
					'variable' => 'toggle-button-border-color'
				]
			],
		]);
	}

	if ($close_button_type === 'type-3') {
		blocksy_output_colors([
			'value' => blocksy_akg('cart_panel_close_button_shape_color', $atts),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'responsive' => true,
			'variables' => [
				'default' => [
					'selector' => '#woo-cart-panel .ct-toggle-close[data-type="type-3"]',
					'variable' => 'toggle-button-background'
				],

				'hover' => [
					'selector' => '#woo-cart-panel .ct-toggle-close[data-type="type-3"]:hover',
					'variable' => 'toggle-button-background'
				]
			],
		]);
	}

	$cart_panel_close_button_icon_size = blocksy_akg( 'cart_panel_close_button_icon_size', $atts, 12 );

	if ($cart_panel_close_button_icon_size !== 12) {
		$css->put( 
			'#woo-cart-panel .ct-toggle-close',
			'--icon-size: ' . $cart_panel_close_button_icon_size . 'px' 
		);
	}


	if ($close_button_type !== 'type-1') {
		$cart_panel_close_button_border_radius = blocksy_akg( 'cart_panel_close_button_border_radius', $atts, 5 );

		if ($cart_panel_close_button_border_radius !== 5) {
			$css->put( 
				'#woo-cart-panel .ct-toggle-close',
				'--toggle-button-radius: ' . $cart_panel_close_button_border_radius . 'px' 
			);
		}
	}
}


// Margin
blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg(
		'headerCartMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
