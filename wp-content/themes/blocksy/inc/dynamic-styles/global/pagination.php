<?php

$selector_prefix = $prefix;

if ($selector_prefix === 'blog') {
	$selector_prefix = '';
}


$paginationSpacing = get_theme_mod($prefix . '_paginationSpacing', 60);

if ($paginationSpacing !== 60) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.ct-pagination', $selector_prefix),
		'variableName' => 'spacing',
		'value' => $paginationSpacing
	]);
}

blocksy_output_border([
	'css' => $css,
	'selector' => blocksy_prefix_selector('.ct-pagination[data-divider]', $selector_prefix),
	'variableName' => 'pagination-divider',
	'value' => get_theme_mod($prefix . '_paginationDivider'),
	'default' => [
		'width' => 1,
		'style' => 'none',
		'color' => [
			'color' => 'rgba(224, 229, 235, 0.5)',
		],
	],
	'skip_none' => true
]);

blocksy_output_colors([
	'value' => get_theme_mod($prefix . '_simplePaginationFontColor', []),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="simple"], [data-pagination="next_prev"]',
				$selector_prefix
			),
			'variable' => 'color'
		],

		'active' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="simple"]',
				$selector_prefix
			),
			'variable' => 'colorActive'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="simple"], [data-pagination="next_prev"]',
				$selector_prefix
			),
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod($prefix . '_paginationButtonText', []),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="load_more"]',
				$selector_prefix
			),
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="load_more"]',
				$selector_prefix
			),
			'variable' => 'buttonTextHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod($prefix . '_paginationButton', []),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="load_more"]',
				$selector_prefix
			),
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector(
				'[data-pagination="load_more"]',
				$selector_prefix
			),
			'variable' => 'buttonHoverColor'
		],
	],
]);

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_prefix_selector('.ct-pagination', $prefix),
	'property' => 'border-radius',
	'value' => get_theme_mod($prefix . '_pagination_border_radius',
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);