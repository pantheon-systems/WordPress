<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Icon size
$iconSize = blocksy_akg( 'cta_button_icon_size', $atts, 15 );

if ($iconSize !== 15) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'icon-size',
		'value' => $iconSize,
		'responsive' => true
	]);
}

// Font color
blocksy_output_colors([
	'value' => blocksy_akg('headerButtonFontColor', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],

		'default_2' => [ 'color' => 'var(--buttonInitialColor)' ],
		'hover_2' => [ 'color' => '#ffffff' ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-button'
			])),
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-button'
			])),
			'variable' => 'buttonTextHoverColor'
		],


		'default_2' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-button-ghost'
			])),
			'variable' => 'buttonTextInitialColor'
		],

		'hover_2' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-button-ghost'
			])),
			'variable' => 'buttonTextHoverColor'
		],
	],
	'responsive' => true
]);

// Background color
blocksy_output_colors([
	'value' => blocksy_akg('headerButtonForeground', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'buttonHoverColor'
		],
	],
	'responsive' => true
]);

if (isset($has_transparent_header) && $has_transparent_header) {
	blocksy_output_colors([
		'value' => blocksy_akg('transparentHeaderButtonFontColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],

			'default_2' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover_2' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
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
							'to_add' => '.ct-button'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'buttonTextInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '.ct-button'
						]),

						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'buttonTextHoverColor'
			],

			'default_2' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '.ct-button-ghost'
						]),

						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'buttonTextInitialColor'
			],

			'hover_2' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '.ct-button-ghost'
						]),

						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'buttonTextHoverColor'
			],
		],
		'responsive' => true
	]);

	// Background color
	blocksy_output_colors([
		'value' => blocksy_akg('transparentHeaderButtonForeground', $atts),
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
				'variable' => 'buttonInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'buttonHoverColor'
			],
		],
		'responsive' => true
	]);
}


// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {
	blocksy_output_colors([
		'value' => blocksy_akg('stickyHeaderButtonFontColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],

			'default_2' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover_2' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
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
							'to_add' => '.ct-button'
						]),

						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'buttonTextInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '.ct-button'
						]),

						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'buttonTextHoverColor'
			],


			'default_2' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '.ct-button-ghost'
						]),

						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'buttonTextInitialColor'
			],

			'hover_2' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '.ct-button-ghost'
						]),

						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'buttonTextHoverColor'
			],
		],
		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('stickyHeaderButtonForeground', $atts),
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
				'variable' => 'buttonInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'buttonHoverColor'
			],
		],
		'responsive' => true
	]);
}


// Margin
blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
    'important' => true,
	'value' => blocksy_default_akg( 'headerCtaMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);

// Border radius
blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'property' => 'buttonBorderRadius',
	'value' => blocksy_default_akg( 'headerCtaRadius', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);


// footer button
$horizontal_alignment =
blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => $column_selector
	])),
	'variableName' => 'horizontal-alignment',
	'value' => blocksy_akg( 'footer_button_horizontal_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
	'unit' => '',
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => $column_selector
	])),
	'variableName' => 'vertical-alignment',
	'value' => blocksy_akg( 'footer_button_vertical_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
	'unit' => '',
]);
