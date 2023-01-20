<?php

if (get_theme_mod($prefix . '_has_comments', 'yes') !== 'yes') {
	return;
}

$comments_narrow_width = get_theme_mod($prefix. '_comments_narrow_width', 750);

if ($comments_narrow_width !== 750) {
	$css->put(
		blocksy_prefix_selector('.ct-comments-container', $prefix),
		'--narrow-container-max-width: ' . $comments_narrow_width . 'px'
	);
}

blocksy_output_colors([
	'value' => get_theme_mod(
		$prefix . '_comments_font_color',
		[
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		]
	),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector('.ct-comments', $prefix),
			'variable' => 'color'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector('.ct-comments', $prefix),
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_background_css([
	'selector' => blocksy_prefix_selector('.ct-comments-container', $prefix),
	'css' => $css,
	'value' => get_theme_mod(
		$prefix . '_comments_background',
		blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => Blocksy_Css_Injector::get_skip_rule_keyword()
				],
			],
		])
	)
]);
