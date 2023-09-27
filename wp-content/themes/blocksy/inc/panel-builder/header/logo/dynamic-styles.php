<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Logo size
$logo_max_height = blocksy_akg('logoMaxHeight', $atts, 50);

if ($logo_max_height !== 50) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.site-logo-container'
			])
		),
		'variableName' => 'logo-max-height',
		'value' => $logo_max_height,
	]);
}

$logo_max_height = blocksy_expand_responsive_value($logo_max_height);

// Site title font
blocksy_output_font_css([
	'font_value' => blocksy_akg('siteTitle', $atts,
		blocksy_typography_default_values([
			'size' => '25px',
			'variation' => 'n7',
			'line-height' => '1.5'
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(
		blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'suffix',
			'to_add' => '.site-title'
		])
	),
]);

// Site title color
blocksy_output_colors([
	'value' => blocksy_akg('siteTitleColor', $atts),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor4)' ],
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
					'to_add' => '.site-title'
				])
			),
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(
				blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.site-title'
				])
			),
			'variable' => 'linkHoverColor'
		],
	],
	'responsive' => true
]);

if (isset($has_transparent_header) && $has_transparent_header) {
	blocksy_output_colors([
		'value' => blocksy_akg('transparentSiteTitleColor', $atts),
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
							'to_add' => '.site-title'
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
							'to_add' => '.site-title'
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

	// Site tagline color
	blocksy_output_colors([
		'value' => blocksy_akg('transparentSiteTaglineColor', $atts),
		'default' => [
			'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
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
							'to_add' => '.site-description'
						]),
						'operation' => 'between',
						'to_add' => '[data-transparent-row="yes"]'
					])
				),
				'variable' => 'color'
			],
		],
		'responsive' => true
	]);
}

// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {
	blocksy_output_colors([
		'value' => blocksy_akg('stickySiteTitleColor', $atts),
		'default' => [
			'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
			'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
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
							'to_add' => '.site-title'
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
							'to_add' => '.site-title'
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

	// Site tagline color
	blocksy_output_colors([
		'value' => blocksy_akg('stickySiteTaglineColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
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
							'to_add' => '.site-description'
						]),
						'operation' => 'between',
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variable' => 'color'
			],
		],
		'responsive' => true
	]);

	if (blocksy_akg('has_sticky_logo_shrink', $atts, 'no') === 'yes') {
		$sticky_logo_shrink = blocksy_expand_responsive_value(blocksy_akg(
			'sticky_logo_shrink',
			$atts,
			70
		));

		$sticky_logo_shrink['desktop'] = intval($sticky_logo_shrink['desktop']) / 100;
		$sticky_logo_shrink['tablet'] = intval($sticky_logo_shrink['tablet']) / 100;
		$sticky_logo_shrink['mobile'] = intval($sticky_logo_shrink['mobile']) / 100;

		if (
			$has_sticky_header['effect'] !== 'shrink'
			&&
			$has_sticky_header['effect'] !== 'auto-hide'
		) {
			$shrinkedHeight = [
				'desktop' => intval($logo_max_height['desktop'] * $sticky_logo_shrink['desktop']) . 'px',
				'tablet' => intval($logo_max_height['tablet'] * $sticky_logo_shrink['tablet']) . 'px',
				'mobile' => intval($logo_max_height['mobile'] * $sticky_logo_shrink['mobile']) . 'px'
			];

			blocksy_output_responsive([
				'css' => $css,
				'tablet_css' => $tablet_css,
				'mobile_css' => $mobile_css,
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => $root_selector,
						'to_add' => '[data-sticky*="yes"]'
					])
				),
				'variableName' => 'logo-shrink-height',
				'value' => $shrinkedHeight,
				'unit' => ''
			]);
		} else {
			blocksy_output_responsive([
				'css' => $css,
				'tablet_css' => $tablet_css,
				'mobile_css' => $mobile_css,
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'to_add' => '[data-sticky]'
				])),
				'variableName' => 'logo-sticky-shrink',
				'value' => $sticky_logo_shrink,
				'unit' => ''
			]);
		}
	}
}

// Site tagline font
$has_tagline = blocksy_akg('has_tagline', $atts, 'no');

if ($has_tagline === 'yes') {
	blocksy_output_font_css([
		'font_value' => blocksy_akg('siteTagline', $atts,
			blocksy_typography_default_values([
				'size' => '13px',
				'variation' => 'n5',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.site-description'
			])
		),
	]);

	blocksy_output_colors([
		'value' => blocksy_akg('siteTaglineColor', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
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
						'to_add' => '.site-description'
					])
				),
				'variable' => 'color'
			],
		],
		'responsive' => true
	]);
}

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg(
		'headerLogoMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'variableName' => 'horizontal-alignment',
	'value' => blocksy_akg( 'header_logo_horizontal_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
	'unit' => '',
]);


// footer logo
blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => '[data-column="logo"]'
	])),
	'variableName' => 'horizontal-alignment',
	'value' => blocksy_akg( 'footer_logo_horizontal_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
	'unit' => '',
]);


blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => '[data-column="logo"]'
	])),
	'variableName' => 'vertical-alignment',
	'value' => blocksy_akg( 'footer_logo_vertical_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
	'unit' => '',
]);
