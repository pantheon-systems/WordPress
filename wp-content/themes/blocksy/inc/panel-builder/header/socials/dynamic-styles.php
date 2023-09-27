<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Icon size
$socialsIconSize = blocksy_akg( 'socialsIconSize', $atts, 15 );

if ($socialsIconSize !== 15) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'icon-size',
		'value' => $socialsIconSize,
		'responsive' => true
	]);
}


// Icon spacing
$socialsIconSpacing = blocksy_akg( 'socialsIconSpacing', $atts, 15 );

if ($socialsIconSpacing !== 15) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'spacing',
		'value' => $socialsIconSpacing,
		'responsive' => true
	]);
}

$has_label = (
	is_customize_preview()
	||
	blocksy_some_device(blocksy_default_akg(
		'socialsLabelVisibility',
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
		'font_value' => blocksy_akg( 'socials_label_font', $atts,
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
		'value' => blocksy_akg('header_socials_font_color', $atts),
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
						'to_add' => 'a'
					])
				),
				'variable' => 'linkInitialColor'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'suffix',
						'to_add' => 'a'
					])
				),
				'variable' => 'linkHoverColor'
			],
		],
		'responsive' => true
	]);
}

// Icons custom color
blocksy_output_colors([
	'value' => blocksy_akg('headerSocialsIconColor', $atts),
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
				'operation' => 'suffix',
				'to_add' => '[data-color="custom"]'
			])),
			'variable' => 'icon-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '[data-color="custom"]'
			])),
			'variable' => 'icon-hover-color'
		]
	],

	'responsive' => true
]);

// Icons custom background
blocksy_output_colors([
	'value' => blocksy_akg('headerSocialsIconBackground', $atts),
	'default' => [
		'default' => [ 'color' => 'rgba(218, 222, 228, 0.3)' ],
		'hover' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,

	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '[data-color="custom"]'
			])),
			'variable' => 'background-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '[data-color="custom"]'
			])),
			'variable' => 'background-hover-color'
		]
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
	'value' => blocksy_default_akg(
		'headerSocialsMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);

if (function_exists('blocksy_output_responsive_switch')) {
	blocksy_output_responsive_switch([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'suffix',
			'to_add' => '.ct-label'
		])),
		'value' => blocksy_default_akg(
			'socialsLabelVisibility',
			$atts,
			[
				'desktop' => false,
				'tablet' => false,
				'mobile' => false,
			]
		),
		'on' => 'block'
	]);
}

// transparent state
if (isset($has_transparent_header) && $has_transparent_header) {

	if ($has_label) {
		blocksy_output_colors([
			'value' => blocksy_akg('transparent_header_socials_font_color', $atts),
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
								'to_add' => 'a'
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
								'to_add' => 'a'
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

	// Icons custom color
	blocksy_output_colors([
		'value' => blocksy_akg('transparentHeaderSocialsIconColor', $atts),
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
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'icon-hover-color'
			]
		],

		'responsive' => true
	]);

	// Icons custom background
	blocksy_output_colors([
		'value' => blocksy_akg('transparentHeaderSocialsIconBackground', $atts),
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
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'background-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'background-hover-color'
			]
		],

		'responsive' => true
	]);
}


// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {

	if ($has_label) {
		blocksy_output_colors([
			'value' => blocksy_akg('sticky_header_socials_font_color', $atts),
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
								'to_add' => 'a'
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
								'to_add' => 'a'
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

	// Icons custom color
	blocksy_output_colors([
		'value' => blocksy_akg('stickyHeaderSocialsIconColor', $atts),
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
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'icon-hover-color'
			]
		],

		'responsive' => true
	]);

	// Icons custom background
	blocksy_output_colors([
		'value' => blocksy_akg('stickyHeaderSocialsIconBackground', $atts),
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
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'background-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'operation' => 'suffix',
							'to_add' => '[data-color="custom"]'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'background-hover-color'
			]
		],

		'responsive' => true
	]);
}
