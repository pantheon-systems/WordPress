<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

// Offcanvas background
$offcanvas_behavior = blocksy_akg('offcanvas_behavior', $atts, 'panel');

$offcanvasBackground = blocksy_akg(
	'offcanvasBackground',
	$atts,
	blocksy_background_default_value([
		'backgroundColor' => [
			'default' => [
				'color' => 'rgba(18, 21, 25, 0.98)'
			],
		],
	])
);

$offcanvasBackdrop = blocksy_akg(
	'offcanvasBackdrop',
	$atts,
	blocksy_background_default_value([
		'backgroundColor' => [
			'default' => [
				'color' => 'CT_CSS_SKIP_RULE',
			],
		],
	])
);

if ($offcanvas_behavior === 'panel') {
	blocksy_output_background_css([
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'suffix',
			'to_add' => '.ct-panel-inner'
		])),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'value' => $offcanvasBackground
	]);
}

// Offcanvas backdrop
blocksy_output_background_css([
	'selector' => blocksy_assemble_selector($root_selector),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'value' => $offcanvas_behavior === 'panel' ? $offcanvasBackdrop : $offcanvasBackground,
]);

blocksy_output_box_shadow([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector[0] . ' [data-behaviour*="side"]'),
	'value' => blocksy_akg('headerPanelShadow', $atts, blocksy_box_shadow_value([
		'enable' => true,
		'h_offset' => 0,
		'v_offset' => 0,
		'blur' => 70,
		'spread' => 0,
		'inset' => false,
		'color' => [
			'color' => 'rgba(0, 0, 0, 0.35)',
		],
	])),
	'responsive' => true
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'variableName' => 'side-panel-width',
	'unit' => '',
	'value' => blocksy_akg('side_panel_width', $atts, [
		'desktop' => '500px',
		'tablet' => '65vw',
		'mobile' => '90vw',
	])
]);

$vertical_alignment = blocksy_akg('offcanvas_content_vertical_alignment', $atts, 'flex-start');

if ($vertical_alignment !== 'flex-start') {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'vertical-alignment',
		'unit' => '',
		'value' => $vertical_alignment,
	]);
}

$horizontal_alignment = blocksy_akg(
	'offcanvasContentAlignment',
	$atts,
	'CT_CSS_SKIP_RULE'
);

if ($horizontal_alignment !== 'initial') {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector($root_selector),
		'variableName' => 'horizontal-alignment',
		'unit' => '',
		'value' => $horizontal_alignment,
	]);

	$text_horizontal_alignment = blocksy_map_values([
		'value' => $horizontal_alignment,
		'map' => [
			'initial' => 'CT_CSS_SKIP_RULE',
			'flex-end' => 'right'
		]
	]);

	if ($text_horizontal_alignment !== 'initial') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_assemble_selector($root_selector),
			'variableName' => 'text-horizontal-alignment',
			'unit' => '',
			'value' => $text_horizontal_alignment,
		]);
	}

	if (is_array($horizontal_alignment)) {
		if (
			$horizontal_alignment['desktop'] === 'center'
			||
			$horizontal_alignment['tablet'] === 'center'
			||
			$horizontal_alignment['mobile'] === 'center'
		) {
			blocksy_output_responsive([
				'css' => $css,
				'tablet_css' => $tablet_css,
				'mobile_css' => $mobile_css,
				'selector' => blocksy_assemble_selector($root_selector),
				'variableName' => 'has-indentation',
				'unit' => '',
				'value' => [
					'desktop' => $horizontal_alignment['desktop'] === 'center' ? '0' : '1',
					'tablet' => $horizontal_alignment['tablet'] === 'center' ? '0' : '1',
					'mobile' => $horizontal_alignment['mobile'] === 'center' ? '0' : '1',
				]
			]);
		}
	}

}

// close button
$close_button_type = blocksy_akg('menu_close_button_type', $atts, 'type-1');

blocksy_output_colors([
	'value' => blocksy_akg('menu_close_button_color', $atts),
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
				'operation' => 'suffix',
				'to_add' => '.ct-toggle-close'
			])),
			'variable' => 'icon-color'
		],

		'hover' => [
			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-toggle-close:hover'
			])),
			'variable' => 'icon-color'
		]
	],
	'responsive' => true
]);

if ($close_button_type === 'type-2') {
	blocksy_output_colors([
		'value' => blocksy_akg('menu_close_button_border_color', $atts),
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
					'operation' => 'suffix',
					'to_add' => '.ct-toggle-close[data-type="type-2"]'
				])),
				'variable' => 'toggle-button-border-color'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.ct-toggle-close[data-type="type-2"]:hover'
				])),
				'variable' => 'toggle-button-border-color'
			]
		],
		'responsive' => true
	]);
}

if ($close_button_type === 'type-3') {
	blocksy_output_colors([
		'value' => blocksy_akg('menu_close_button_shape_color', $atts),
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
					'operation' => 'suffix',
					'to_add' => '.ct-toggle-close[data-type="type-3"]'
				])),
				'variable' => 'toggle-button-background'
			],

			'hover' => [
				'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
					'selector' => $root_selector,
					'operation' => 'suffix',
					'to_add' => '.ct-toggle-close[data-type="type-3"]:hover'
				])),
				'variable' => 'toggle-button-background'
			]
		],
		'responsive' => true
	]);
}


$menu_close_button_icon_size = blocksy_akg( 'menu_close_button_icon_size', $atts, 12 );

if ($menu_close_button_icon_size !== 12) {
	$css->put( 
		blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'suffix',
			'to_add' => '.ct-toggle-close'
		])),
		'--icon-size: ' . $menu_close_button_icon_size . 'px' 
	);
}


if ($close_button_type !== 'type-1') {
	$menu_close_button_border_radius = blocksy_akg( 'menu_close_button_border_radius', $atts, 5 );

	if ($menu_close_button_border_radius !== 5) {
		$css->put( 
			blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '.ct-toggle-close'
			])),
			'--toggle-button-radius: ' . $menu_close_button_border_radius . 'px' 
		);
	}
}
