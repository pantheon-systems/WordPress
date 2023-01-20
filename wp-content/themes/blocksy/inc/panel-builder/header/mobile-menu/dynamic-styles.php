<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// off canvas menu styles
if (isset($row_id) && $row_id === 'offcanvas') {

	$mobile_menu_items_spacing = blocksy_akg('mobile_menu_items_spacing', $atts, 5);

	if ($mobile_menu_items_spacing !== 5) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_assemble_selector($root_selector),
			'variableName' => 'items-vertical-spacing',
			'value' => $mobile_menu_items_spacing
		]);
	}

	blocksy_output_font_css([
		'font_value' => blocksy_akg('mobileMenuFont', $atts,
		blocksy_typography_default_values([
			'size' => '20px',
			'variation' => 'n7',
		])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector)
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('mobileMenuColor', $atts),
		'default' => [
			'default' => [ 'color' => '#ffffff' ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector($root_selector),
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector($root_selector),
				'variable' => 'linkHoverColor'
			],

			'active' => [
				'selector' => blocksy_assemble_selector($root_selector),
				'variable' => 'linkActiveColor'
			],
		],
	]);

	blocksy_output_font_css([
		'font_value' => blocksy_akg('mobileMenuDropdownFont', $atts,
			blocksy_typography_default_values([])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'suffix',
			'to_add' => '.sub-menu'
		])),
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('mobileMenuDropdownColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.sub-menu'
				])),
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.sub-menu'
				])),
				'variable' => 'linkHoverColor'
			],

			'active' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.sub-menu'
				])),
				'variable' => 'linkActiveColor'
			],
		],
	]);

	// $mobile_menu_child_size = blocksy_akg( 'mobile_menu_child_size', $atts, '20px' );

	// if ($mobile_menu_child_size !== '20px') {
	// 	$css->put(
	// 		blocksy_assemble_selector($root_selector),
	// 		'--mobile-menu-child-size: ' . $mobile_menu_child_size
	// 	);
	// }

	blocksy_output_border([
		'css' => $css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'mobile-menu-divider',
		'value' => blocksy_akg('mobile_menu_items_divider', $atts),
		'default' => [
			'width' => 1,
			'style' => 'none',
			'color' => [
				'color' => 'rgba(255, 255, 255, 0.2)',
			],
		]
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'value' => blocksy_default_akg(
			'mobileMenuMargin', $atts,
			blocksy_spacing_value([
				'left' => 'auto',
				'right' => 'auto',
				'linked' => true,
			])
		)
	]);

	return;
}

// inline menu styles
$menu_items_spacing = blocksy_akg('inline_menu_items_spacing', $atts, 25);

if ($menu_items_spacing !== 25) {
	if (isset($menu_items_spacing['desktop'])) {
		$menu_items_spacing['desktop'] = 'CT_CSS_SKIP_RULE';
	}

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'menu-items-spacing',
		'value' => $menu_items_spacing
	]);
}

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'variableName' => 'horizontal-alignment',
	'value' => blocksy_akg( 'inline_menu_horizontal_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
	'unit' => '',
]);

blocksy_output_font_css([
	'font_value' => blocksy_akg( 'inline_mobile_menu_font', $atts,
		blocksy_typography_default_values([
			'size' => '12px',
			'variation' => 'n7',
			'line-height' => '1.3',
			'text-transform' => 'uppercase',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'suffix',
		'to_add' => 'ul'
	])),
]);

blocksy_output_colors([
	'value' => blocksy_akg('inline_menu_font_color', $atts),
	'default' => [
		'default' => [ 'color' => 'var(--color)' ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '> ul > li > a'
			])),
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '> ul > li > a'
			])),
			'variable' => 'linkHoverColor'
		],

		'active' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '> ul > li > a'
			])),
			'variable' => 'linkActiveColor'
		],
	],
]);

// transparent state
if (isset($has_transparent_header) && $has_transparent_header) {
	blocksy_output_colors([
		'value' => blocksy_akg('transparent_inline_menu_font_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,

		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '> ul > li > a'
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
							'to_add' => '> ul > li > a'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'linkHoverColor'
			],

			'active' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '> ul > li > a'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'linkActiveColor'
			],
		],
	]);
}

// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {
	blocksy_output_colors([
		'value' => blocksy_akg('sticky_inline_menu_font_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,

		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '> ul > li > a'
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
							'to_add' => '> ul > li > a'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'linkHoverColor'
			],

			'active' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '> ul > li > a'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'linkActiveColor'
			],
		],
	]);
}

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg(
		'inline_menu_margin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
