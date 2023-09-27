<?php

$forms_type =  get_theme_mod('forms_type', 'classic-forms');

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('newsletter_subscribe_title_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'heading-color'
		],
	],
]);

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('newsletter_subscribe_content'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'color'
		],

		'hover' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'linkHoverColor'
		],
	],
]);

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('newsletter_subscribe_button'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
		'hover' => [ 'color' => 'var(--paletteColor2)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'buttonHoverColor'
		]
	],
]);

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('newsletter_subscribe_input_font_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'form-text-initial-color'
		],

		'focus' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'form-text-focus-color'
		],
	],
]);

blc_call_fn(['fn' => 'blocksy_output_colors'], [
	'value' => get_theme_mod('newsletter_subscribe_border_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'form-field-border-initial-color'
		],

		'focus' => [
			'selector' => '.ct-newsletter-subscribe-block',
			'variable' => 'form-field-border-focus-color'
		],
	],
]);

if ($forms_type !== 'classic-forms' || is_customize_preview()) {
	blc_call_fn(['fn' => 'blocksy_output_colors'], [
		'value' => get_theme_mod('newsletter_subscribe_input_background'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
			'focus' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.ct-newsletter-subscribe-block',
				'variable' => 'form-field-initial-background'
			],

			'focus' => [
				'selector' => '.ct-newsletter-subscribe-block',
				'variable' => 'form-field-focus-background'
			],
		],
	]);
}

blc_call_fn(['fn' => 'blocksy_output_background_css'], [
	'selector' => '.ct-newsletter-subscribe-block',
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'value' => get_theme_mod(
		'newsletter_subscribe_container_background',
		blc_call_fn([
			'fn' => 'blocksy_background_default_value',
			'default' => null
		], [
			'backgroundColor' => [
				'default' => [
					'color' => '#ffffff'
				],
			],
		])
	),
	'responsive' => true,
]);

blc_call_fn(['fn' => 'blocksy_output_border'], [
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.ct-newsletter-subscribe-block',
	'variableName' => 'newsletter-container-border',
	'value' => get_theme_mod('newsletter_subscribe_container_border'),
	'skip_none' => true,
	'default' => [
		'width' => 1,
		'style' => 'none',
		'color' => [
			'color' => 'var(--paletteColor5)',
		],
	],
	'responsive' => true,
	'skip_none' => true
]);

blc_call_fn(['fn' => 'blocksy_output_box_shadow'], [
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.ct-newsletter-subscribe-block',
	'value' => get_theme_mod(
		'newsletter_subscribe_shadow',
		blc_call_fn(['fn' => 'blocksy_box_shadow_value'], [
			'enable' => true,
			'h_offset' => 0,
			'v_offset' => 50,
			'blur' => 90,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(210, 213, 218, 0.4)',
			],
		])
	),
	'responsive' => true
]);

blc_call_fn(['fn' => 'blocksy_output_spacing'], [
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.ct-newsletter-subscribe-block',
	'property' => 'padding',
	'value' => get_theme_mod(
		'newsletter_subscribe_container_spacing',
		blocksy_spacing_value([
			'linked' => true,
			'top' => '30px',
			'left' => '30px',
			'right' => '30px',
			'bottom' => '30px',
		])
	)
]);

blc_call_fn(['fn' => 'blocksy_output_spacing'], [
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.ct-newsletter-subscribe-block',
	'property' => 'border-radius',
	'value' => get_theme_mod(
		'newsletter_subscribe_container_border_radius',
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
