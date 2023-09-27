<?php

// Color palette
blocksy_output_colors([
	'value' => get_theme_mod('colorPalette'),
	'default' => [
		'color1' => [ 'color' => '#2872fa' ],
		'color2' => [ 'color' => '#1559ed' ],
		'color3' => [ 'color' => '#3A4F66' ],
		'color4' => [ 'color' => '#192a3d' ],
		'color5' => [ 'color' => '#e1e8ed' ],
		'color6' => [ 'color' => '#f2f5f7' ],
		'color7' => [ 'color' => '#FAFBFC' ],
		'color8' => [ 'color' => '#ffffff' ],
	],
	'css' => $css,
	'variables' => [
		'color1' => ['variable' => 'paletteColor1'],
		'color2' => ['variable' => 'paletteColor2'],
		'color3' => ['variable' => 'paletteColor3'],
		'color4' => ['variable' => 'paletteColor4'],
		'color5' => ['variable' => 'paletteColor5'],
		'color6' => ['variable' => 'paletteColor6'],
		'color7' => ['variable' => 'paletteColor7'],
		'color8' => ['variable' => 'paletteColor8'],
	],
]);

// Colors
blocksy_output_colors([
	'value' => get_theme_mod('fontColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor3)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => ['variable' => 'color'],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('linkColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
		'hover' => [ 'color' => 'var(--paletteColor2)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => ['variable' => 'linkInitialColor'],
		'hover' => ['variable' => 'linkHoverColor'],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('selectionColor'),
	'default' => [
		'default' => [ 'color' => '#ffffff' ],
		'hover' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => ['variable' => 'selectionTextColor'],
		'hover' => ['variable' => 'selectionBackgroundColor'],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('border_color'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor5)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => ['variable' => 'border-color'],
	],
]);


// Headings
blocksy_output_colors([
	'value' => get_theme_mod('headingColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor4)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'headings-color'
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
			'selector' => ':root',
			'variable' => 'heading-1-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('heading_2_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
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
			'selector' => ':root',
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
			'selector' => ':root',
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
			'selector' => ':root',
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
			'selector' => ':root',
			'variable' => 'heading-6-color'
		],
	],
]);


// Content spacing
$contentSpacingMap = [
	'none' => '0',
	'compact' => '0.8em',
	'comfortable' => '1.5em',
	'spacious' => '2em',
];

$contentSpacing = get_theme_mod('contentSpacing', 'comfortable');

$contentSpacingResult = isset(
	$contentSpacingMap[$contentSpacing]
) ? $contentSpacingMap[$contentSpacing] : $contentSpacingMap['comfortable'];

$css->put(':root', '--content-spacing: ' . $contentSpacingResult);

if ($contentSpacing === 'none') {
	$css->put(':root', '--has-content-spacing: 0');
}

// Buttons
blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => ':root',
	'variableName' => 'buttonMinHeight',
	'value' => get_theme_mod('buttonMinHeight', 40)
]);

if (get_theme_mod('buttonHoverEffect', 'no') !== 'yes') {
	$css->put(':root', '--buttonShadow: none');
	$css->put(':root', '--buttonTransform: none');
}

blocksy_output_colors([
	'value' => get_theme_mod('buttonTextColor'),
	'default' => [
		'default' => [ 'color' => '#ffffff' ],
		'hover' => [ 'color' => '#ffffff' ],
	],
	'css' => $css,
	'variables' => [
		'default' => ['variable' => 'buttonTextInitialColor'],
		'hover' => ['variable' => 'buttonTextHoverColor'],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('buttonColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
		'hover' => [ 'color' => 'var(--paletteColor2)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => ['variable' => 'buttonInitialColor'],
		'hover' => ['variable' => 'buttonHoverColor'],
	],
]);

blocksy_output_border([
	'css' => $css,
	'selector' => ':root',

	'variableName' => 'button-border',
	'secondColorVariableName' => 'button-border-hover-color',

	'value' => get_theme_mod('buttonBorder'),
	'default' => [
		'width' => 1,
		'style' => 'none',
		'color' => [
			'color' => 'rgba(224, 229, 235, 0.5)',
		],
		'secondColor' => [
			'color' => 'rgba(224, 229, 235, 0.7)',
		]
	]
]);

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => ':root',
	'property' => 'buttonBorderRadius',
	'value' => get_theme_mod( 'buttonRadius',
		blocksy_spacing_value([
			'linked' => true,
			'top' => '3px',
			'left' => '3px',
			'right' => '3px',
			'bottom' => '3px',
		])
	)
]);

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => ':root',
	'property' => 'button-padding',
	'value' => get_theme_mod( 'buttonPadding',
		blocksy_spacing_value([
			'linked' => false,
			'top' => '5px',
			'left' => '20px',
			'right' => '20px',
			'bottom' => '5px',
		])
	)
]);


// Layout
$max_site_width = get_theme_mod( 'maxSiteWidth', 1290 );
$css->put(
	':root',
	'--normal-container-max-width: ' . $max_site_width . 'px'
);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => ':root',
	'variableName' => 'content-vertical-spacing',
	'unit' => '',
	'value' => get_theme_mod('contentAreaSpacing', [
		'desktop' => '60px',
		'tablet' => '60px',
		'mobile' => '50px',

	])
]);

$narrowContainerWidth = get_theme_mod( 'narrowContainerWidth', 750 );
$css->put(
	':root',
	'--narrow-container-max-width: ' . $narrowContainerWidth . 'px'
);

$wideOffset = get_theme_mod( 'wideOffset', 130 );
$css->put(
	':root',
	'--wide-offset: ' . $wideOffset . 'px'
);

// sidebars
$sidebar_type = get_theme_mod('sidebar_type', 'type-1');

// sidebar width
$sidebar_width = get_theme_mod( 'sidebarWidth', 27 );
if ($sidebar_width !== 27) {
	$css->put(
		'[data-sidebar]',
		'--sidebar-width: ' . $sidebar_width . '%'
	);

	$css->put(
		'[data-sidebar]',
		'--sidebar-width-no-unit: ' . intval($sidebar_width)
	);
}

// sidebar gap
$sidebarGap = blocksy_get_with_percentage('sidebarGap', '4%');
if ($sidebarGap !== '4%') {
	$css->put(
		'[data-sidebar]',
		'--sidebar-gap: ' . $sidebarGap
	);
}


// sticky sidebar offset
$sidebarOffset = get_theme_mod('sidebarOffset', 50);
if ($sidebarOffset !== 50) {
	$css->put(
		'[data-sidebar]',
		'--sidebar-offset: ' . $sidebarOffset . 'px'
	);
}

blocksy_output_colors([
	'value' => get_theme_mod('sidebarWidgetsTitleColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => '.ct-sidebar .widget-title',
			'variable' => 'heading-color'
		],
	],
	'responsive' => true
]);

blocksy_output_colors([
	'value' => get_theme_mod('sidebarWidgetsFontColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'link_initial' => [ 'color' => 'var(--color)' ],
		'link_hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => '.ct-sidebar > *',
			'variable' => 'color'
		],

		'link_initial' => [
			'selector' => '.ct-sidebar',
			'variable' => 'linkInitialColor'
		],

		'link_hover' => [
			'selector' => '.ct-sidebar',
			'variable' => 'linkHoverColor'
		],
	],
	'responsive' => true
]);

if (
	$sidebar_type === 'type-2'
	||
	$sidebar_type === 'type-4'
	||
	is_customize_preview()
) {
	blocksy_output_colors([
		'value' => get_theme_mod('sidebarBackgroundColor'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'variables' => [
			'default' => [
				'selector' => '[data-sidebar] > aside',
				'variable' => 'sidebar-background-color'
			],
		],
		'responsive' => true
	]);
}

// Sidebar border
if ($sidebar_type === 'type-2') {
	blocksy_output_border([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'aside[data-type="type-2"]',
		'variableName' => 'border',
		'value' => get_theme_mod('sidebarBorder'),
		'default' => [
			'width' => 1,
			'style' => 'none',
			'color' => [
				'color' => 'rgba(224, 229, 235, 0.8)',
			],
		],
		'responsive' => true
	]);

	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'aside[data-type="type-2"]',
		'property' => 'borderRadius',
		'value' => get_theme_mod( 'sidebarRadius',
			blocksy_spacing_value([
				'linked' => true,
			])
		)
	]);

	// Sidebar shadow
	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'aside[data-type="type-2"]',
		'value' => get_theme_mod('sidebarShadow', blocksy_box_shadow_value([
			'enable' => true,
			'h_offset' => 0,
			'v_offset' => 12,
			'blur' => 18,
			'spread' => -6,
			'inset' => false,
			'color' => [
				'color' => 'rgba(34, 56, 101, 0.04)',
			],
		])),
		'responsive' => true
	]);
}

if ($sidebar_type === 'type-3') {
	blocksy_output_border([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => 'aside[data-type="type-3"]',
		'variableName' => 'border',
		'value' => get_theme_mod('sidebarDivider'),
		'default' => [
			'width' => 1,
			'style' => 'solid',
			'color' => [
				'color' => 'rgba(224, 229, 235, 0.8)',
			],
		],
		'responsive' => true
	]);
}

$sidebarWidgetsSpacing = get_theme_mod('sidebarWidgetsSpacing', 40);

if ($sidebarWidgetsSpacing !== 40) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.ct-sidebar',
		'variableName' => 'sidebar-widgets-spacing',
		'value' => $sidebarWidgetsSpacing
	]);
}

$sidebarInnerSpacing = get_theme_mod('sidebarInnerSpacing', 35);

if ($sidebarInnerSpacing !== 35) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => "[data-sidebar] > aside",
		'variableName' => 'sidebar-inner-spacing',
		'value' => $sidebarInnerSpacing,
	]);
}


// Mobile sidebar position
$sidebar_position = get_theme_mod('mobile_sidebar_position', 'bottom');

if ($sidebar_position === 'top') {
	$mobile_css->put(
		':root',
		'--sidebar-order: -1'
	);

	$tablet_css->put(
		':root',
		'--sidebar-order: -1'
	);
}


// To top button
$has_back_top = get_theme_mod('has_back_top', 'no');

if ($has_back_top === 'yes') {

	$topButtonSize = get_theme_mod('topButtonSize', 12);

	if ($topButtonSize !== 12) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => '.ct-back-to-top .ct-icon',
			'variableName' => 'icon-size',
			'value' => $topButtonSize
		]);
	}

	$topButtonOffset = get_theme_mod('topButtonOffset', 25);

	if ($topButtonOffset !== 25) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => '.ct-back-to-top',
			'variableName' => 'back-top-bottom-offset',
			'value' => $topButtonOffset
		]);
	}

	$sideButtonOffset = get_theme_mod('sideButtonOffset', 25);

	if ($sideButtonOffset !== 25) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => '.ct-back-to-top',
			'variableName' => 'back-top-side-offset',
			'value' => $sideButtonOffset
		]);
	}

	blocksy_output_colors([
		'value' => get_theme_mod('topButtonIconColor'),
		'default' => [
			'default' => [ 'color' => '#ffffff' ],
			'hover' => [ 'color' => '#ffffff' ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.ct-back-to-top',
				'variable' => 'icon-color'
			],

			'hover' => [
				'selector' => '.ct-back-to-top',
				'variable' => 'icon-hover-color'
			]
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('topButtonShapeBackground'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.ct-back-to-top',
				'variable' => 'top-button-background-color'
			],

			'hover' => [
				'selector' => '.ct-back-to-top',
				'variable' => 'top-button-background-hover-color'
			]
		],
	]);

	$topButtonSgape = get_theme_mod('top_button_shape', 'square');

	if($topButtonSgape === 'square') {
		blocksy_output_spacing([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => '.ct-back-to-top',
			'property' => 'border-radius',
			'value' => get_theme_mod( 'topButtonRadius',
				blocksy_spacing_value([
					'linked' => true,
					'top' => '2px',
					'left' => '2px',
					'right' => '2px',
					'bottom' => '2px',
				])
			)
		]);
	}

	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.ct-back-to-top',
		'value' => get_theme_mod('topButtonShadow', blocksy_box_shadow_value([
			'enable' => false,
			'h_offset' => 0,
			'v_offset' => 5,
			'blur' => 20,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(210, 213, 218, 0.2)',
			],
		])),
		'responsive' => true
	]);
}

// Passepartout
$has_passepartout = get_theme_mod('has_passepartout', 'no');

if ($has_passepartout !== 'no') {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => ':root',
		'variableName' => 'frame-size',
		'value' => get_theme_mod('passepartoutSize', 10)
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('passepartoutColor'),
		'default' => [
			'default' => [ 'color' => 'var(--paletteColor1)' ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => ':root',
				'variable' => 'frame-color'
			],
		],
	]);
}


// breadcrumbs
blocksy_output_colors([
	'value' => get_theme_mod('breadcrumbsFontColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'initial' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-breadcrumbs',
			'variable' => 'color'
		],

		'initial' => [
			'selector' => '.ct-breadcrumbs',
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => '.ct-breadcrumbs',
			'variable' => 'linkHoverColor'
		],
	],
]);
