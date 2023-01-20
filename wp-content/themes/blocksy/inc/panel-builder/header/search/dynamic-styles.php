<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Icon size
$search_header_icon_size = blocksy_akg('searchHeaderIconSize', $atts, 15);

if ($search_header_icon_size !== 15) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'icon-size',
		'value' => $search_header_icon_size
	]);
}

// Icon color
blocksy_output_colors([
	'value' => blocksy_akg('searchHeaderIconColor', $atts),
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

$has_label = (
	is_customize_preview()
	||
	blocksy_some_device(blocksy_default_akg(
		'search_label_visibility',
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
		'font_value' => blocksy_akg( 'search_label_font', $atts,
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
		'value' => blocksy_akg('header_search_font_color', $atts),
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
			'value' => blocksy_akg('transparent_header_search_font_color', $atts),
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
		'value' => blocksy_akg('transparentSearchHeaderIconColor', $atts),
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
}


// sticky state
if (isset($has_sticky_header) && $has_sticky_header) {

	if ($has_label) {
		blocksy_output_colors([
			'value' => blocksy_akg('sticky_header_search_font_color', $atts),
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
		'value' => blocksy_akg('stickySearchHeaderIconColor', $atts),
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
}

blocksy_output_font_css([
	'font_value' => blocksy_akg( 'searchHeaderModalFont', $atts,
		blocksy_typography_default_values([
			'size' => '14px',
			'variation' => 'n5',
			'line-height' => '1.4',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(
		$root_selector[0] . ' #search-modal .ct-search-results a'
	),
]);

// Links color
blocksy_output_colors([
	'value' => blocksy_akg('searchHeaderLinkColor', $atts),
	'default' => [
		'default' => [ 'color' => '#ffffff' ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal'
			),
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal'
			),
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => blocksy_akg('searchHeaderInputColor', $atts),
	'default' => [
		'default' => [ 'color' => '#ffffff' ],
		'focus' => [ 'color' => '#ffffff' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal'
			),
			'variable' => 'form-text-initial-color'
		],

		'focus' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal'
			),
			'variable' => 'form-text-focus-color'
		],
	],
]);

// Search button colors
blocksy_output_colors([
	'value' => blocksy_akg('search_button_icon_color', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal form button'
			),
			'variable' => 'icon-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal form button'
			),
			'variable' => 'icon-focus-color'
		],
	],
]);

blocksy_output_colors([
	'value' => blocksy_akg('search_button_background_color', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal form button'
			),
			'variable' => 'search-button-background'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal form button'
			),
			'variable' => 'search-button-focus-background'
		],
	],
]);


// Close button
$close_button_type = blocksy_akg('search_close_button_type', $atts, 'type-1');

blocksy_output_colors([
	'value' => blocksy_akg('search_close_button_color', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,

	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal .ct-toggle-close'
			),
			'variable' => 'icon-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal .ct-toggle-close:hover'
			),
			'variable' => 'icon-color'
		]
	],
]);

if ($close_button_type === 'type-2') {
	blocksy_output_colors([
		'value' => blocksy_akg('search_close_button_border_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,

		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					$root_selector[0] . ' #search-modal .ct-toggle-close[data-type="type-2"]'
				),
				'variable' => 'toggle-button-border-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					$root_selector[0] . ' #search-modal .ct-toggle-close[data-type="type-2"]:hover'
				),
				'variable' => 'toggle-button-border-color'
			]
		],
	]);
}

if ($close_button_type === 'type-3') {
	blocksy_output_colors([
		'value' => blocksy_akg('search_close_button_shape_color', $atts),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,

		'variables' => [
			'default' => [
				'selector' => blocksy_assemble_selector(
					$root_selector[0] . ' #search-modal .ct-toggle-close[data-type="type-3"]'
				),
				'variable' => 'toggle-button-background'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(
					$root_selector[0] . ' #search-modal .ct-toggle-close[data-type="type-3"]:hover'
				),
				'variable' => 'toggle-button-background'
			]
		],
	]);
}

$search_close_button_icon_size = blocksy_akg( 'search_close_button_icon_size', $atts, 12 );

if ($search_close_button_icon_size !== 12) {
	$css->put( 
		blocksy_assemble_selector(
			$root_selector[0] . ' #search-modal .ct-toggle-close'
		),
		'--icon-size: ' . $search_close_button_icon_size . 'px' 
	);
}


if ($close_button_type !== 'type-1') {
	$search_close_button_border_radius = blocksy_akg( 'search_close_button_border_radius', $atts, 5 );

	if ($search_close_button_border_radius !== 5) {
		$css->put( 
			blocksy_assemble_selector(
				$root_selector[0] . ' #search-modal .ct-toggle-close'
			),
			'--toggle-button-radius: ' . $search_close_button_border_radius . 'px' 
		);
	}
}

// Modal background
blocksy_output_background_css([
	'selector' => blocksy_assemble_selector(
		$root_selector[0] . ' #search-modal'
	),
	'css' => $css,
	'value' => blocksy_akg('searchHeaderBackground', $atts,
		blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => 'rgba(18, 21, 25, 0.98)'
				],
			],
		])
	)
]);


// Icon margin
blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'important' => true,
	'value' => blocksy_default_akg(
		'headerSearchMargin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
