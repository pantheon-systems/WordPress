<?php

if (! function_exists('blocksy_assemble_selector')) {
	return;
}

if (empty($default_height)) {
	$default_height = [
		'mobile' => 70,
		'tablet' => 70,
		'desktop' => 120,
	];
}

if (empty($default_background)) {
	$default_background = blocksy_background_default_value([
		'backgroundColor' => [
			'default' => [
				'color' => 'var(--paletteColor8)',
			],
		],
	]);
}

// Row height
$headerRowHeight = blocksy_akg('headerRowHeight', $atts, $default_height);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'variableName' => 'height',
	'value' => $headerRowHeight
]);

// Row background
$headerRowBackground = blocksy_expand_responsive_value(
	blocksy_akg('headerRowBackground', $atts, $default_background)
);

$headerRowWidth = blocksy_expand_responsive_value(
	blocksy_akg('headerRowWidth', $atts, 'fixed')
);

unset($headerRowWidth['tablet']);

$rowBackgroundValue = $headerRowBackground;
$containerBackgroundValue = $headerRowBackground;

if ($headerRowWidth['desktop'] === 'boxed') {
	$rowBackgroundValue['desktop']['background_type'] = 'color';
	$rowBackgroundValue['desktop']['backgroundColor'][
		'default'
	]['color'] = 'transparent';
} else {
	$containerBackgroundValue['desktop']['background_type'] = 'color';
	$containerBackgroundValue['desktop']['backgroundColor'][
		'default'
	]['color'] = 'transparent';
}

if ($headerRowWidth['mobile'] === 'boxed') {
	$rowBackgroundValue['tablet']['background_type'] = 'color';
	$rowBackgroundValue['tablet']['backgroundColor'][
		'default'
	]['color'] = 'transparent';

	$rowBackgroundValue['mobile']['background_type'] = 'color';
	$rowBackgroundValue['mobile']['backgroundColor'][
		'default'
	]['color'] = 'transparent';
} else {
	$containerBackgroundValue['tablet']['background_type'] = 'color';
	$containerBackgroundValue['tablet']['backgroundColor'][
		'default'
	]['color'] = 'transparent';

	$containerBackgroundValue['mobile']['background_type'] = 'color';
	$containerBackgroundValue['mobile']['backgroundColor'][
		'default'
	]['color'] = 'transparent';
}

if (
	blocksy_some_device($headerRowWidth, 'fixed')
	||
	blocksy_some_device($headerRowWidth, 'fluid')
) {
	blocksy_output_background_css([
		'selector' => blocksy_assemble_selector($root_selector),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'value' => $rowBackgroundValue,
		'responsive' => true,
		'forced_background_image' => true
	]);
}

if (blocksy_some_device($headerRowWidth, 'boxed')) {
	blocksy_output_background_css([
		'selector' => blocksy_assemble_selector(
			blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'suffix',
				'to_add' => '> div'
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'value' => $containerBackgroundValue,
		'responsive' => true,
		'forced_background_image' => true
	]);
}

// Top Border
$headerRowTopBorderFullWidth = blocksy_akg('headerRowTopBorderFullWidth', $atts, 'no');

$top_has_border_selector = blocksy_mutate_selector([
	'selector' => $root_selector,
	'operation' => 'suffix',
	'to_add' => '> div'
]);

$top_has_no_border_selector = $root_selector;

if ($headerRowTopBorderFullWidth === 'yes') {
	$top_has_border_selector = $root_selector;

	$top_has_no_border_selector = blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'suffix',
		'to_add' => '> div'
	]);
}

blocksy_output_border([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($top_has_border_selector),
	'variableName' => 'borderTop',
	'value' => blocksy_akg('headerRowTopBorder', $atts),
	'default' => [
		'width' => 1,
		'style' => 'none',
		'color' => [
			'color' => 'rgba(44,62,80,0.2)',
		],
	],
	'responsive' => true
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($top_has_no_border_selector),
	'variableName' => 'borderTop',
	'value' => [
		'desktop' => 'none',
		'tablet' => 'none',
		'mobile' => 'none'
	],
	'unit' => ''
]);


// Bottom Border
$headerRowBottomBorderFullWidth = blocksy_akg('headerRowBottomBorderFullWidth', $atts, 'no');

$bottom_has_border_selector = blocksy_mutate_selector([
	'selector' => $root_selector,
	'operation' => 'suffix',
	'to_add' => '> div'
]);
$bottom_has_no_border_selector = $root_selector;

if ($headerRowBottomBorderFullWidth === 'yes') {
	$bottom_has_border_selector = $root_selector;

	$bottom_has_no_border_selector = blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'suffix',
		'to_add' => '> div'
	]);
}

blocksy_output_border([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($bottom_has_border_selector),
	'variableName' => 'borderBottom',
	'value' => blocksy_akg('headerRowBottomBorder', $atts),
	'default' => [
		'width' => 1,
		'style' => 'none',
		'color' => [
			'color' => 'rgba(44,62,80,0.2)',
		],
	],
	'responsive' => true
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($bottom_has_no_border_selector),
	'variableName' => 'borderBottom',
	'value' => [
		'desktop' => 'none',
		'tablet' => 'none',
		'mobile' => 'none'
	],
	'unit' => ''
]);

// Box shadow
blocksy_output_box_shadow([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'value' => blocksy_akg('headerRowShadow', $atts, blocksy_box_shadow_value([
		'enable' => false,
		'h_offset' => 0,
		'v_offset' => 10,
		'blur' => 20,
		'spread' => 0,
		'inset' => false,
		'color' => [
			'color' => 'rgba(44,62,80,0.05)',
		],
	])),
	'responsive' => true,
	'should_skip_output' => false
]);

// transparent state
if (isset($has_transparent_header) && $has_transparent_header) {
	// background
	$transparentHeaderRowBackground = blocksy_expand_responsive_value(
		blocksy_akg(
			'transparentHeaderRowBackground',
			$atts,
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'rgba(255,255,255,0)',
					],
				],
			])
		)
	);

	$rowBackgroundValue = $transparentHeaderRowBackground;
	$containerBackgroundValue = $transparentHeaderRowBackground;

	if ($headerRowWidth['desktop'] === 'boxed') {
		$rowBackgroundValue['desktop']['background_type'] = 'color';
		$rowBackgroundValue['desktop']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	} else {
		$containerBackgroundValue['desktop']['background_type'] = 'color';
		$containerBackgroundValue['desktop']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	}

	if ($headerRowWidth['mobile'] === 'boxed') {
		$rowBackgroundValue['tablet']['background_type'] = 'color';
		$rowBackgroundValue['tablet']['backgroundColor'][
			'default'
		]['color'] = 'transparent';

		$rowBackgroundValue['mobile']['background_type'] = 'color';
		$rowBackgroundValue['mobile']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	} else {
		$containerBackgroundValue['tablet']['background_type'] = 'color';
		$containerBackgroundValue['tablet']['backgroundColor'][
			'default'
		]['color'] = 'transparent';

		$containerBackgroundValue['mobile']['background_type'] = 'color';
		$containerBackgroundValue['mobile']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	}

	if (
		blocksy_some_device($headerRowWidth, 'fixed')
		||
		blocksy_some_device($headerRowWidth, 'fluid')
	) {
		blocksy_output_background_css([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'operation' => 'el-prefix',
				'to_add' => '[data-transparent-row="yes"]'
			])),

			'value' => $rowBackgroundValue,
			'responsive' => true,
			'forced_background_image' => true
		]);
	}

	if (blocksy_some_device($headerRowWidth, 'boxed')) {
		blocksy_output_background_css([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'selector' => blocksy_assemble_selector(
				blocksy_mutate_selector([
					'selector' => blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'el-prefix',
						'to_add' => '[data-transparent-row="yes"]'
					]),
					'operation' => 'suffix',
					'to_add' => '> div'
				])
			),

			'value' => $containerBackgroundValue,
			'responsive' => true,
			'forced_background_image' => true
		]);
	}

	// Border top
	blocksy_output_border([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $top_has_border_selector,
			'operation' => 'el-prefix',
			'to_add' => '[data-transparent-row="yes"]'
		])),
		'variableName' => 'borderTop',
		'value' => blocksy_akg('transparentHeaderRowTopBorder', $atts),
		'default' => [
			'width' => 1,
			'style' => 'none',
			'color' => [
				'color' => 'rgba(44,62,80,0.2)',
			],
		],
		'responsive' => true
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $top_has_no_border_selector,
			'operation' => 'el-prefix',
			'to_add' => '[data-transparent-row="yes"]'
		])),

		'variableName' => 'borderTop',
		'value' => [
			'desktop' => 'none',
			'tablet' => 'none',
			'mobile' => 'none'
		],
		'unit' => ''
	]);

	// Border bottom
	blocksy_output_border([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $bottom_has_border_selector,
			'operation' => 'el-prefix',
			'to_add' => '[data-transparent-row="yes"]'
		])),

		'variableName' => 'borderBottom',
		'value' => blocksy_akg('transparentHeaderRowBottomBorder', $atts),
		'default' => [
			'width' => 1,
			'style' => 'none',
			'color' => [
				'color' => 'rgba(44,62,80,0.2)',
			],
		],
		'responsive' => true
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $bottom_has_no_border_selector,
			'operation' => 'el-prefix',
			'to_add' => '[data-transparent-row="yes"]'
		])),

		'variableName' => 'borderBottom',
		'value' => [
			'desktop' => 'none',
			'tablet' => 'none',
			'mobile' => 'none'
		],
		'unit' => ''
	]);

	// box shadow
	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $root_selector,
			'operation' => 'el-prefix',
			'to_add' => '[data-transparent-row="yes"]'
		])),

		'value' => blocksy_akg('transparentHeaderRowShadow', $atts, blocksy_box_shadow_value([
			'enable' => false,
			'h_offset' => 0,
			'v_offset' => 10,
			'blur' => 20,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(44,62,80,0.05)',
			],
		])),
		'responsive' => true,
		'should_skip_output' => false
	]);
}

// sticky state
if (
	isset($has_sticky_header)
	&&
	$has_sticky_header
) {
	// background
	$stickyHeaderRowBackground = blocksy_expand_responsive_value(blocksy_akg(
		'stickyHeaderRowBackground',
		$atts,
		$default_background
	));

	$rowBackgroundValue = $stickyHeaderRowBackground;
	$containerBackgroundValue = $stickyHeaderRowBackground;

	if ($headerRowWidth['desktop'] === 'boxed') {
		$rowBackgroundValue['desktop']['background_type'] = 'color';
		$rowBackgroundValue['desktop']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	} else {
		$containerBackgroundValue['desktop']['background_type'] = 'color';
		$containerBackgroundValue['desktop']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	}

	if ($headerRowWidth['mobile'] === 'boxed') {
		$rowBackgroundValue['tablet']['background_type'] = 'color';
		$rowBackgroundValue['tablet']['backgroundColor'][
			'default'
		]['color'] = 'transparent';

		$rowBackgroundValue['mobile']['background_type'] = 'color';
		$rowBackgroundValue['mobile']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	} else {
		$containerBackgroundValue['tablet']['background_type'] = 'color';
		$containerBackgroundValue['tablet']['backgroundColor'][
			'default'
		]['color'] = 'transparent';

		$containerBackgroundValue['mobile']['background_type'] = 'color';
		$containerBackgroundValue['mobile']['backgroundColor'][
			'default'
		]['color'] = 'transparent';
	}

	if (
		blocksy_some_device($headerRowWidth, 'fixed')
		||
		blocksy_some_device($headerRowWidth, 'fluid')
	) {
		blocksy_output_background_css([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
				'selector' => $root_selector,
				'to_add' => '[data-sticky*="yes"]'
			])),

			'value' => $rowBackgroundValue,
			'forced_background_image' => true,
			'responsive' => true
		]);
	}

	if (blocksy_some_device($headerRowWidth, 'boxed')) {
		blocksy_output_background_css([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'selector' => blocksy_assemble_selector(
				blocksy_mutate_selector([
					'selector' => blocksy_mutate_selector([
						'selector' => $root_selector,
						'to_add' => '[data-sticky*="yes"]'
					]),
					'operation' => 'suffix',
					'to_add' => '> div'
				])
			),

			'value' => $containerBackgroundValue,
			'forced_background_image' => true,
			'responsive' => true
		]);
	}

	if (
		blocksy_akg('has_sticky_shrink', $atts, 'no') === 'yes'
		&&
		isset($root_selector_header)
		&&
		isset($has_sticky_header['behaviour'])
		&&
		(
			strpos($has_sticky_header['behaviour'], 'middle') !== false
			||
			$has_sticky_header['behaviour'] === 'entire_header'
		)
	) {
		if (
			$has_sticky_header['effect'] !== 'shrink'
			&&
			$has_sticky_header['effect'] !== 'auto-hide'
		) {
			$stickyHeaderRowShrink = blocksy_expand_responsive_value(blocksy_akg(
				'stickyHeaderRowShrink',
				$atts,
				70
			));

			$shrinkedHeight = [
				'desktop' => intval(floatval(
					$headerRowHeight['desktop']
				) * floatval($stickyHeaderRowShrink['desktop']) / 100) . 'px',
				'tablet' => intval(floatval(
					$headerRowHeight['tablet']
				) * intval($stickyHeaderRowShrink['tablet']) / 100) . 'px',
				'mobile' => intval(floatval(
					$headerRowHeight['mobile']
				) * intval($stickyHeaderRowShrink['mobile']) / 100) . 'px'
			];

			blocksy_output_responsive([
				'css' => $css,
				'tablet_css' => $tablet_css,
				'mobile_css' => $mobile_css,
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector([
						'selector' => blocksy_mutate_selector([
							'selector' => $root_selector,
							'to_add' => '[data-sticky*="yes"]'
						]),
						'operation' => 'suffix',
						'to_add' => '> div'
					])
				),
				'variableName' => 'shrink-height',
				'value' => $shrinkedHeight,
				'unit' => ''
			]);
		}

		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_assemble_selector($root_selector_header),
			'variableName' => 'sticky-shrink',
			'value' => blocksy_akg('stickyHeaderRowShrink', $atts, 70),
			'unit' => ''
		]);
	}

	// Border top
	blocksy_output_border([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $top_has_border_selector,
			'to_add' => '[data-sticky*="yes"]'
		])),
		'variableName' => 'borderTop',
		'value' => blocksy_akg('stickyHeaderRowTopBorder', $atts),
		'default' => [
			'width' => 1,
			'style' => 'none',
			'color' => [
				'color' => 'rgba(44,62,80,0.2)',
			],
		],
		'responsive' => true
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $top_has_no_border_selector,
			'to_add' => '[data-sticky*="yes"]'
		])),

		'variableName' => 'borderTop',
		'value' => [
			'desktop' => 'none',
			'tablet' => 'none',
			'mobile' => 'none'
		],
		'unit' => ''
	]);

	// Border bottom
	blocksy_output_border([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $bottom_has_border_selector,
			'to_add' => '[data-sticky*="yes"]'
		])),

		'variableName' => 'borderBottom',
		'value' => blocksy_akg('stickyHeaderRowBottomBorder', $atts),
		'default' => [
			'width' => 1,
			'style' => 'none',
			'color' => [
				'color' => 'rgba(44,62,80,0.2)',
			],
		],
		'responsive' => true
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $bottom_has_no_border_selector,
			'to_add' => '[data-sticky*="yes"]'
		])),

		'variableName' => 'borderBottom',
		'value' => [
			'desktop' => 'none',
			'tablet' => 'none',
			'mobile' => 'none'
		],
		'unit' => ''
	]);

	// box shadow
	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
			'selector' => $root_selector,
			'to_add' => '[data-sticky*="yes"]'
		])),
		'value' => blocksy_akg('stickyHeaderRowShadow', $atts, blocksy_box_shadow_value([
			'enable' => false,
			'h_offset' => 0,
			'v_offset' => 10,
			'blur' => 20,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(44,62,80,0.05)',
			],
		])),
		'responsive' => true,
		'should_skip_output' => false
	]);
}

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'property' => 'row-border-radius',
	'value' => blocksy_akg('header_row_border_radius', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);
