<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

$icon_size = blocksy_akg('trigger_icon_size', $atts, 18);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'variableName' => 'icon-size',
	'value' => $icon_size
]);

// Icon color
blocksy_output_colors([
	'value' => blocksy_akg('triggerIconColor', $atts),
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
			'variable' => 'icon-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'icon-hover-color'
		],
	],
	'responsive' => true
]);

blocksy_output_colors([
	'value' => blocksy_akg('triggerSecondColor', $atts),
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
			'variable' => 'secondColor'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'secondColorHover'
		],
	],
	'responsive' => true
]);

// Margin
blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg( 'triggerMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);

$trigger_design = blocksy_akg( 'trigger_design', $atts, 'simple' );

if ($trigger_design !== 'simple') {
	$trigger_border_radius = blocksy_akg( 'trigger_border_radius', $atts, 3 );

	$css->put( 
		blocksy_assemble_selector($root_selector),
		'--toggle-button-radius: ' . $trigger_border_radius . 'px' 
	);
}


$has_label = (
	is_customize_preview()
	||
	blocksy_some_device(blocksy_default_akg(
		'trigger_label_visibility',
		$atts,
		[
			'desktop' => false,
			'tablet' => false,
			'mobile' => false,
		]
	))
);

if ($has_label) {
	blocksy_output_font_css([
		'font_value' => blocksy_akg( 'trigger_label_font', $atts,
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
		'value' => blocksy_akg('header_trigger_font_color', $atts),
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
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector($root_selector),
				'variable' => 'linkHoverColor'
			],
		],
		'responsive' => true
	]);
}

// transparent state
if (isset($has_transparent_header) && $has_transparent_header) {

	if ($has_label) {
		blocksy_output_colors([
			'value' => blocksy_akg('transparent_header_trigger_font_color', $atts),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables' => [
				'default' => [
					'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])),
					'variable' => 'linkInitialColor'
				],

				'hover' => [
					'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])),
					'variable' => 'linkHoverColor'
				],
			],
			'responsive' => true
		]);
	}

	blocksy_output_colors([
		'value' => blocksy_akg('transparentTriggerIconColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-transparent-row="yes"]'
				])),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-transparent-row="yes"]'
				])),
				'variable' => 'icon-hover-color'
			],
		],
		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('transparentTriggerSecondColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-transparent-row="yes"]'
				])),
				'variable' => 'secondColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-transparent-row="yes"]'
				])),
				'variable' => 'secondColorHover'
			],
		],
		'responsive' => true
	]);
}


// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {

	if ($has_label) {
		blocksy_output_colors([
			'value' => blocksy_akg('sticky_header_trigger_font_color', $atts),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables' => [
				'default' => [
					'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])),
					'variable' => 'linkInitialColor'
				],

				'hover' => [
					'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])),
					'variable' => 'linkHoverColor'
				],
			],
			'responsive' => true
		]);
	}

	blocksy_output_colors([
		'value' => blocksy_akg('stickyTriggerIconColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-sticky*="yes"]'
				])),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-sticky*="yes"]'
				])),
				'variable' => 'icon-hover-color'
			],
		],
		'responsive' => true
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('stickyTriggerSecondColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-sticky*="yes"]'
				])),
				'variable' => 'secondColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'between',
					'to_add' => '[data-sticky*="yes"]'
				])),
				'variable' => 'secondColorHover'
			],
		],
		'responsive' => true
	]);
}
