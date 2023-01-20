<?php

// Content color
blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('cookieContentColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'color'
		],

		'hover' => [
			'selector' => '.cookie-notification',
			'variable' => 'colorHover'
		],
	],
]);

// Accept button color
blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('cookieButtonText'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification .ct-cookies-accept-button',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification .ct-cookies-accept-button',
			'variable' => 'buttonTextHoverColor'
		]
	],
]);

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('cookieButtonBackground'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification .ct-cookies-accept-button',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification .ct-cookies-accept-button',
			'variable' => 'buttonHoverColor'
		]
	],
]);

// Decline button color
blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('cookieDeclineButtonText'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor3)' ],
		'hover' => [ 'color' => 'var(--paletteColor3)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification .ct-cookies-decline-button',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification .ct-cookies-decline-button',
			'variable' => 'buttonTextHoverColor'
		]
	],
]);

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('cookieDeclineButtonBackground'),
	'default' => [
		'default' => [ 'color' => 'rgba(224, 229, 235, 0.6)' ],
		'hover' => [ 'color' => 'rgba(224, 229, 235, 1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification .ct-cookies-decline-button',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification .ct-cookies-decline-button',
			'variable' => 'buttonHoverColor'
		]
	],
]);


// Background color
blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('cookieBackground'),
	'default' => [
		'default' => [ 'color' => '#ffffff' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'backgroundColor'
		],
	],
]);

$cookieMaxWidth = get_theme_mod( 'cookieMaxWidth', 400 );
$css->put(
	'.cookie-notification',
	'--maxWidth: ' . $cookieMaxWidth . 'px'
);

