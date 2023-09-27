<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Font
blocksy_output_font_css([
	'font_value' => blocksy_akg( 'copyrightFont', $atts,
		blocksy_typography_default_values([
			'size' => '15px',
			'variation' => 'n4',
			'line-height' => '1.3',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector)
]);

// Font color
blocksy_output_colors([
	'value' => blocksy_akg('copyrightColor', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'link_initial' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'link_hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'color'
		],

		'link_initial' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'linkInitialColor'
		],

		'link_hover' => [
			'selector' => blocksy_assemble_selector($root_selector),
			'variable' => 'linkHoverColor'
		],
	],
]);

// Alignment
blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,

	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => '[data-column="copyright"]'
	])),

	'variableName' => 'horizontal-alignment',
	'value' => blocksy_akg('footerCopyrightAlignment', $atts, 'CT_CSS_SKIP_RULE'),
	'unit' => '',
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => '[data-column="copyright"]'
	])),
	'variableName' => 'vertical-alignment',
	'value' => blocksy_akg('footerCopyrightVerticalAlignment', $atts, 'CT_CSS_SKIP_RULE'),
	'unit' => '',
]);

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg( 'copyrightMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
