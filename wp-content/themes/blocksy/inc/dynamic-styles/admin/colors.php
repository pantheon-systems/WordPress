<?php

if (! isset($selector)) {
    $selector = ':root';
}

// Color palette
$colorPalette = blocksy_get_colors(
	get_theme_mod('colorPalette'),
	[
		'color1' => ['color' => '#2872fa'],
		'color2' => ['color' => '#1559ed'],
		'color3' => ['color' => '#3A4F66'],
		'color4' => ['color' => '#192a3d'],
		'color5' => ['color' => '#e1e8ed'],
		'color6' => ['color' => '#f2f5f7'],
		'color7' => ['color' => '#FAFBFC'],
		'color8' => ['color' => '#ffffff'],
	]
);

$css->put(
	$selector,
	"--paletteColor1: {$colorPalette['color1']}"
);

$css->put(
	$selector,
	"--paletteColor2: {$colorPalette['color2']}"
);

$css->put(
	$selector,
	"--paletteColor3: {$colorPalette['color3']}"
);

$css->put(
	$selector,
	"--paletteColor4: {$colorPalette['color4']}"
);

$css->put(
	$selector,
	"--paletteColor5: {$colorPalette['color5']}"
);

$css->put(
	$selector,
	"--paletteColor6: {$colorPalette['color6']}"
);

$css->put(
	$selector,
	"--paletteColor7: {$colorPalette['color7']}"
);

$css->put(
	$selector,
	"--paletteColor8: {$colorPalette['color8']}"
);


// body font color
blocksy_output_colors([
	'value' => get_theme_mod('fontColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor3)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'variable' => 'color',
			'selector' => $selector
		],
	],
]);


// link color
blocksy_output_colors([
	'value' => get_theme_mod('linkColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
		'hover' => [ 'color' => 'var(--paletteColor2)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'variable' => 'linkInitialColor',
			'selector' => $selector
		],
		'hover' => [
			'variable' => 'linkHoverColor',
			'selector' => $selector
		],
	],
]);


// border color
blocksy_output_colors([
	'value' => get_theme_mod('border_color'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor5)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'variable' => 'border-color',
			'selector' => $selector
		],
	],
]);


// headins
blocksy_output_colors([
	'value' => get_theme_mod('headingColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor4)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'variable' => 'headings-color',
			'selector' => $selector
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_1_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'heading-1-color'
		],
	]
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_2_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'heading-2-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_3_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'heading-3-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_4_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'heading-4-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_5_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'heading-5-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_6_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'heading-6-color'
		],
	],
]);


// forms
blocksy_output_colors([
	'value' => get_theme_mod('formTextColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'form-text-initial-color'
		],

		'focus' => [
			'selector' => $selector,
			'variable' => 'form-text-focus-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('formBorderColor'),
	'default' => [
		'default' => [ 'color' => 'var(--border-color)' ],
		'focus' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'form-field-border-initial-color'
		],

		'focus' => [
			'selector' => $selector,
			'variable' => 'form-field-border-focus-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('formBackgroundColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'form-field-initial-background'
		],

		'focus' => [
			'selector' => $selector,
			'variable' => 'form-field-focus-background'
		],
	],
]);


// buttons
$buttonTextColor = blocksy_get_colors( get_theme_mod('buttonTextColor'),
	[
		'default' => [ 'color' => '#ffffff' ],
		'hover' => [ 'color' => '#ffffff' ],
	]
);

$css->put(
	$selector,
	"--buttonTextInitialColor: {$buttonTextColor['default']}"
);

$css->put(
	$selector,
	"--buttonTextHoverColor: {$buttonTextColor['hover']}"
);

$button_color = blocksy_get_colors( get_theme_mod('buttonColor'),
	[
		'default' => [ 'color' => 'var(--paletteColor1)' ],
		'hover' => [ 'color' => 'var(--paletteColor2)' ],
	]
);

$css->put(
	$selector,
	"--buttonInitialColor: {$button_color['default']}"
);

$css->put(
	$selector,
	"--buttonHoverColor: {$button_color['hover']}"
);

blocksy_output_colors([
	'value' => get_theme_mod('global_quantity_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'quantity-initial-color'
		],

		'hover' => [
			'selector' => $selector,
			'variable' => 'quantity-hover-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('global_quantity_arrows'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => $selector,
			'variable' => 'quantity-arrows-initial-color'
		],

		'hover' => [
			'selector' => $selector,
			'variable' => 'quantity-arrows-hover-color'
		],
	],
]);

