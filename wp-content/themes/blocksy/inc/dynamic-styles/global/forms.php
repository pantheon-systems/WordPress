<?php

$forms_type = get_theme_mod('forms_type', 'classic-forms');

if ($forms_type === 'classic-forms') {
	$css->put(
		':root',
		'--has-classic-forms: var(--true)'
	);

	$css->put(
		':root',
		'--has-modern-forms: var(--false)'
	);
} else {
	$css->put(
		':root',
		'--has-classic-forms: var(--false)'
	);

	$css->put(
		':root',
		'--has-modern-forms: var(--true)'
	);
}

// general
blocksy_output_colors([
	'value' => get_theme_mod('formTextColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'form-text-initial-color'
		],

		'focus' => [
			'selector' => ':root',
			'variable' => 'form-text-focus-color'
		],
	],
]);

$formFontSize = get_theme_mod('formFontSize', 16);

if ($formFontSize !== 16) {
	$css->put(':root', '--form-font-size: ' . $formFontSize . 'px');
}

blocksy_output_colors([
	'value' => get_theme_mod('formBackgroundColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'form-field-initial-background'
		],

		'focus' => [
			'selector' => ':root',
			'variable' => 'form-field-focus-background'
		],
	],
]);

$formInputHeight = get_theme_mod( 'formInputHeight', 40 );

if ($formInputHeight !== 40) {
	$css->put( ':root', '--form-field-height: ' . $formInputHeight . 'px' );
}


$formTextAreaHeight = get_theme_mod( 'formTextAreaHeight', 170 );
$css->put( 'form textarea', '--form-field-height: ' . $formTextAreaHeight . 'px' );


$formFieldBorderRadius = get_theme_mod( 'formFieldBorderRadius', 3 );

if ($formFieldBorderRadius !== 3) {
	$css->put( ':root', '--form-field-border-radius: ' . $formFieldBorderRadius . 'px' );
}


blocksy_output_colors([
	'value' => get_theme_mod('formBorderColor'),
	'default' => [
		'default' => [ 'color' => 'var(--border-color)' ],
		'focus' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'form-field-border-initial-color'
		],

		'focus' => [
			'selector' => ':root',
			'variable' => 'form-field-border-focus-color'
		],
	],
]);

$formBorderSize = get_theme_mod( 'formBorderSize', 1 );


if ($forms_type === 'classic-forms') {
	if($formBorderSize !== 1) {
		$css->put(
			':root',
			'--form-field-border-width: ' . $formBorderSize . 'px'
		);
	}
} else {
	$css->put(
		':root',
		'--form-field-border-width: 0 0 ' . $formBorderSize . 'px 0'
	);

	$css->put(
		':root',
		'--form-selection-control-border-width: ' . $formBorderSize . 'px'
	);
}

// dropdown select
blocksy_output_colors([
	'value' => get_theme_mod('formSelectFontColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'form-field-select-initial-color'
		],

		'active' => [
			'selector' => ':root',
			'variable' => 'form-field-select-active-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('formSelectBackgroundColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'form-field-select-initial-background'
		],

		'active' => [
			'selector' => ':root',
			'variable' => 'form-field-select-active-background'
		],
	],
]);

// radio & checkbox
blocksy_output_colors([
	'value' => get_theme_mod('radioCheckboxColor'),
	'default' => [
		'default' => [ 'color' => 'var(--border-color)' ],
		'accent' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'form-selection-control-initial-color'
		],

		'accent' => [
			'selector' => ':root',
			'variable' => 'form-selection-control-accent-color'
		],
	],
]);

$checkboxBorderRadius = get_theme_mod( 'checkboxBorderRadius', 3 );

if ($checkboxBorderRadius !== 3) {
	$css->put( ':root', '--form-checkbox-border-radius: ' . $checkboxBorderRadius . 'px' );
}