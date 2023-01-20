<?php

if (! isset($source)) {
	$source = [
		'strategy' => 'customizer',
		'prefix' => $prefix
	];
}

$default_hero_enabled = 'yes';

if ($prefix === 'blog') {
	$default_hero_enabled = 'no';
}

if (blocksy_akg_or_customizer(
	'hero_enabled',
	$source,
	$default_hero_enabled
) === 'no') {
	return;
}

$default_hero_elements = [];

$default_hero_elements[] = [
	'id' => 'custom_title',
	'enabled' => $prefix !== 'product',
];

$default_hero_elements[] = [
	'id' => 'custom_description',
	'enabled' => $prefix !== 'product',
];

if (
	strpos($prefix, 'single') !== false
	||
	$prefix === 'author'
) {
	$default_hero_elements[] = [
		'id' => 'custom_meta',
		'enabled' => $prefix !== 'single_page' && $prefix !== 'product',
	];
}

if ($prefix === 'author') {
	$default_hero_elements[] = [
		'id' => 'author_social_channels',
		'enabled' => true,
	];
}

$default_hero_elements[] = [
	'id' => 'breadcrumbs',
	'enabled' => $prefix === 'product',
];

$hero_elements = blocksy_akg_or_customizer(
	'hero_elements',
	$source,
	$default_hero_elements
);

$default_type = 'type-1';

if (
	$prefix === 'woo_categories'
	||
	$prefix === 'author'
) {
	$default_type = 'type-2';
}

$type = blocksy_akg_or_customizer('hero_section', $source, $default_type);

$hero_elements = blocksy_akg_or_customizer(
	'hero_elements',
	$source,
	$default_hero_elements
);

// title
blocksy_output_font_css([
	'font_value' => blocksy_akg_or_customizer(
		'pageTitleFont',
		$source,
		blocksy_typography_default_values([
			'size' => '30px',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_prefix_selector('.entry-header .page-title', $prefix)
]);


// meta
blocksy_output_font_css([
	'font_value' => blocksy_akg_or_customizer(
		'pageMetaFont',
		$source,
		blocksy_typography_default_values([
			'size' => '12px',
			'variation' => 'n6',
			'line-height' => '1.3',
			'text-transform' => 'uppercase',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_prefix_selector('.entry-header .entry-meta', $prefix)
]);

blocksy_output_colors([
	'value' => blocksy_akg_or_customizer('pageMetaFontColor', $source),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector('.entry-header .entry-meta', $prefix),
			'variable' => 'color'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector('.entry-header .entry-meta', $prefix),
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => blocksy_akg_or_customizer('page_meta_button_type_font_colors', $source),
	'default' => [
		'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
		'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector('.entry-header [data-type="pill"]', $prefix),
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector('.entry-header [data-type="pill"]', $prefix),
			'variable' => 'buttonTextHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => blocksy_akg_or_customizer(
		'page_meta_button_type_background_colors',
		$source
	),
	'default' => [
		'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
		'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'.entry-header [data-type="pill"]',
				$prefix
			),
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector(
				'.entry-header [data-type="pill"]',
				$prefix
			),
			'variable' => 'buttonHoverColor'
		],
	],
]);

// excerpt
blocksy_output_font_css([
	'font_value' => blocksy_akg_or_customizer(
		'pageExcerptFont',
		$source,
		blocksy_typography_default_values([
			// 'variation' => 'n5',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_prefix_selector('.entry-header .page-description', $prefix)
]);

blocksy_output_colors([
	'value' => blocksy_akg_or_customizer('pageExcerptColor', $source),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector('.entry-header .page-description', $prefix),
			'variable' => 'color'
		],
	],
]);

// breadcrumbs
$default_hero_elements = [];

$default_hero_elements[] = [
	'id' => 'breadcrumbs',
	'enabled' => $prefix === 'product',
];

foreach (get_theme_mod(
	$prefix . '_hero_elements',
	$default_hero_elements
) as $layer) {
	if (! $layer['enabled']) {
		continue;
	}

	if ($layer['id'] === 'breadcrumbs') {
		blocksy_output_font_css([
			'font_value' => blocksy_akg_or_customizer(
				'breadcrumbsFont',
				$source,
				blocksy_typography_default_values([])
			),
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('.entry-header .ct-breadcrumbs', $prefix)
		]);
	}
}

blocksy_output_colors([
	'value' => blocksy_akg_or_customizer('breadcrumbsFontColor', $source),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'initial' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_prefix_selector(
				'.entry-header .ct-breadcrumbs',
				$prefix
			),
			'variable' => 'color'
		],

		'initial' => [
			'selector' => blocksy_prefix_selector(
				'.entry-header .ct-breadcrumbs',
				$prefix
			),
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => blocksy_prefix_selector(
				'.entry-header .ct-breadcrumbs',
				$prefix
			),
			'variable' => 'linkHoverColor'
		],
	],
]);

if ($type === 'type-1') {
	$hero_alignment1 = blocksy_akg_or_customizer(
		'hero_alignment1',
		$source,
		apply_filters(
			'blocksy:hero:type-1:default-alignment',
			'CT_CSS_SKIP_RULE',
			$prefix
		)
	);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector(
			'.hero-section[data-type="type-1"]',
			$prefix
		),
		'variableName' => 'alignment',
		'value' => $hero_alignment1,
		'unit' => '',
	]);

	$hero_margin_bottom = blocksy_akg_or_customizer('hero_margin', $source, 40);

	if ($hero_margin_bottom !== 40) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector(
				'.hero-section[data-type="type-1"]',
				$prefix
			),
			'variableName' => 'margin-bottom',
			'value' => $hero_margin_bottom,
		]);
	}
}


if ($type === 'type-2') {
	$hero_alignment2 = blocksy_akg_or_customizer(
		'hero_alignment2',
		$source,
		'center'
	);

	if ($hero_alignment2 !== 'center') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector(
				'.hero-section[data-type="type-2"]',
				$prefix
			),
			'variableName' => 'alignment',
			'unit' => '',
			'value' => $hero_alignment2,
		]);
	}

	$hero_vertical_alignment = blocksy_akg_or_customizer(
		'hero_vertical_alignment',
		$source,
		'center'
	);

	if ($hero_vertical_alignment !== 'center') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector(
				'.hero-section[data-type="type-2"]',
				$prefix
			),
			'variableName' => 'vertical-alignment',
			'unit' => '',
			'value' => $hero_vertical_alignment,
		]);
	}

	// height
	$hero_height = blocksy_akg_or_customizer('hero_height', $source, '250px');

	if ($hero_height !== '250px') {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector(
				'.hero-section[data-type="type-2"]',
				$prefix
			),
			'variableName' => 'min-height',
			'unit' => '',
			'value' => $hero_height,
		]);
	}

	// overlay color
	blocksy_output_background_css([
		'selector' => blocksy_prefix_selector(
			'.hero-section[data-type="type-2"] > figure .ct-image-container:after',
			$prefix
		),
		'css' => $css,
		'value' => blocksy_akg_or_customizer(
			'pageTitleOverlay',
			$source,
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'CT_CSS_SKIP_RULE'
					],
				],
			])
		)
	]);

	// background
	blocksy_output_background_css([
		'selector' => blocksy_prefix_selector(
			'.hero-section[data-type="type-2"]',
			$prefix
		),
		'css' => $css,
		'value' => blocksy_akg_or_customizer(
			'pageTitleBackground',
			$source,
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'var(--paletteColor6)'
					],
				],
			])
		),
		'forced_background_image' => true
	]);

	// padding
	blocksy_output_spacing([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector(
			'.hero-section[data-type="type-2"]',
			$prefix
		),
		'property' => 'container-padding',
		'value' => blocksy_akg_or_customizer(
			'pageTitlePadding',
			$source,
			blocksy_spacing_value([
				'top' => '50px',
				'left' => 'auto',
				'right' => 'auto',
				'bottom' => '50px',
				'linked' => true,
			])
		)
	]);
}

$selectors_map = [
	// custom_meta is a bit specially handled
	'author_social_channels' => blocksy_prefix_selector(
		'.hero-section .author-box-social',
		$prefix
	),
	'custom_description' => blocksy_prefix_selector(
		'.hero-section .page-description',
		$prefix
	),
	'custom_title' => implode(', ', [
		blocksy_prefix_selector('.hero-section .page-title', $prefix),
		blocksy_prefix_selector('.hero-section .ct-author-name', $prefix),
	]),
	'breadcrumbs' => blocksy_prefix_selector(
		'.hero-section .ct-breadcrumbs',
		$prefix
	),
	'custom_meta' => blocksy_prefix_selector(
		'.hero-section .entry-meta',
		$prefix
	)
];

$meta_indexes = [
	'first' => null,
	'second' => null
];

foreach ($hero_elements as $index => $single_hero_element) {
	if (! isset($single_hero_element['enabled'])) {
		continue;
	}

	if ($single_hero_element['id'] === 'custom_meta') {
		if ($meta_indexes['first'] === null) {
			$meta_indexes['first'] = $index;
		} else {
			$meta_indexes['second'] = $index;
		}
	}
}

foreach ($hero_elements as $index => $single_hero_element) {
	if (! $single_hero_element['enabled']) {
		continue;
	}

	if ($single_hero_element['id'] === 'custom_title') {
		blocksy_output_colors([
			'value' => blocksy_akg_or_customizer('pageTitleFontColor', $source),
			'default' => [
				'default' => [
					'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')
				],
			],
			'css' => $css,
			'variables' => [
				'default' => [
					'selector' => blocksy_prefix_selector('.entry-header .page-title', $prefix),
					'variable' => 'heading-color'
				],
			],
		]);
	}

	if (isset($single_hero_element['typography'])) {
		blocksy_output_font_css([
			'font_value' => blocksy_akg(
				'typography',
				$single_hero_element,
				blocksy_typography_default_values([])
			),
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => blocksy_prefix_selector('[data-field*="' . substr(
				$single_hero_element['__id'],
				0, 6
			) . '"]', $prefix)
		]);
	}

	if (isset($single_hero_element['color'])) {
		blocksy_output_colors([
			'value' => blocksy_akg('color', $single_hero_element),
			'default' => [
				'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
				'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			],
			'css' => $css,
			'variables' => [
				'default' => [
					'selector' => blocksy_prefix_selector('[data-field*="' . substr(
						$single_hero_element['__id'],
						0, 6
					) . '"]', $prefix),
					'variable' => 'color'
				],

				'hover' => [
					'selector' => blocksy_prefix_selector('[data-field*="' . substr(
						$single_hero_element['__id'],
						0, 6
					) . '"]', $prefix),
					'variable' => 'linkHoverColor'
				],
			],
		]);
	}

	if (
		$single_hero_element['id'] === 'custom_meta'
		&&
		$index === $meta_indexes['second']
	) {
		$selectors_map['custom_meta'] = blocksy_prefix_selector(
			'.entry-meta[data-id="second"]',
			$prefix
		);
	}

	$hero_item_spacing = blocksy_akg('hero_item_spacing', $single_hero_element, 20);

	if (intval($hero_item_spacing) !== 20) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => $selectors_map[$single_hero_element['id']],
			'variableName' => 'itemSpacing',
			'value' => $hero_item_spacing
		]);
	}

	$description_width = blocksy_akg(
		'hero_item_max_width',
		$single_hero_element,
		100
	);

	if (
		$type === 'type-1'
		&&
		$single_hero_element['id'] === 'custom_description'
		&&
		$description_width !== 100
	) {
		blocksy_output_responsive([
			'css' => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector' => $selectors_map[$single_hero_element['id']],
			'variableName' => 'description-max-width',
			'value' => $description_width,
			'unit' => '%'
		]);
	}
}

if ($prefix === 'courses_single') {
	blocksy_output_font_css([
		'font_value' => blocksy_akg_or_customizer(
			'hero_title_font',
			$source,
			blocksy_typography_default_values([
				'size' => '30px',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.tutor-course-details-title', $prefix)
	]);

	blocksy_output_colors([
		'value' => blocksy_akg_or_customizer('hero_title_font_color', $source),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.tutor-course-details-title', $prefix),
				'variable' => 'heading-color'
			],
		],
	]);

	blocksy_output_font_css([
		'font_value' => blocksy_akg_or_customizer(
			'hero_categories_font',
			$source,
			blocksy_typography_default_values([
				'size' => '14px',
				'variation' => 'n5',
				'line-height' => '1.3',
				// 'text-transform' => 'uppercase',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.tutor-meta > *', $prefix)
	]);

	blocksy_output_colors([
		'value' => blocksy_akg_or_customizer('hero_categories_colors', $source),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.tutor-meta', $prefix),
				'variable' => 'color'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.tutor-meta', $prefix),
				'variable' => 'linkHoverColor'
			],
		],
	]);

	blocksy_output_font_css([
		'font_value' => blocksy_akg_or_customizer(
			'hero_actions_font',
			$source,
			blocksy_typography_default_values([
				'size' => '15px',
				'variation' => 'n4',
				'line-height' => '1.4',
				// 'text-transform' => 'uppercase',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.tutor-course-details-actions > a', $prefix)
	]);

	blocksy_output_colors([
		'value' => blocksy_akg_or_customizer('hero_actions_colors', $source),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.tutor-course-details-actions > a', $prefix),
				'variable' => 'color'
			],

			'hover' => [
				'selector' => blocksy_prefix_selector('.tutor-course-details-actions > a', $prefix),
				'variable' => 'linkHoverColor'
			],
		],
	]);

	blocksy_output_font_css([
		'font_value' => blocksy_akg_or_customizer(
			'hero_title_rating_font',
			$source,
			blocksy_typography_default_values([
				'size' => '14px',
				'variation' => 'n4',
				// 'text-transform' => 'uppercase',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => blocksy_prefix_selector('.tutor-ratings', $prefix)
	]);

	blocksy_output_colors([
		'value' => blocksy_akg_or_customizer('hero_title_rating_font_color', $source),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.tutor-ratings', $prefix),
				'variable' => 'color'
			],
		],
	]);

	blocksy_output_colors([
		'value' => blocksy_akg_or_customizer('hero_title_star_rating_color', $source),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => blocksy_prefix_selector('.tutor-ratings-stars', $prefix),
				'variable' => 'tutor-ratings-stars-color'
			],
		],
	]);
}
