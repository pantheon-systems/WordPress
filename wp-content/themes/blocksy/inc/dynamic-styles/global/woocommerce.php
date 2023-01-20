<?php

if (! function_exists('is_woocommerce')) {
	return;
}


blocksy_output_background_css([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'selector' => '[data-prefix="woo_categories"]',
	'value' => get_theme_mod('shop_archive_background',
		blocksy_background_default_value([
			'backgroundColor' => [
				'default' => [
					'color' => Blocksy_Css_Injector::get_skip_rule_keyword()
				],
			],
		])
	),
	'responsive' => true,
]);

$shop_cards_type = get_theme_mod('shop_cards_type', 'type-1');

if ($shop_cards_type === 'type-1') {
	$shop_cards_alignment_1 = get_theme_mod(
		'shop_cards_alignment_1',
		'CT_CSS_SKIP_RULE'
	);

	$text_shop_cards_alignment_1 = $shop_cards_alignment_1;

	$text_shop_cards_alignment_1 = blocksy_map_values([
		'value' => $shop_cards_alignment_1,
		'map' => [
			'flex-start' => 'left',
			'flex-end' => 'right'
		]
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '[data-products="type-1"] .product',
		'variableName' => 'horizontal-alignment',
		'value' => $shop_cards_alignment_1,
		'unit' => '',
	]);

	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '[data-products="type-1"] .product',
		'variableName' => 'text-horizontal-alignment',
		'value' => $text_shop_cards_alignment_1,
		'unit' => '',
	]);
}

$shop_columns_gap = get_theme_mod('shopCardsGap', 30);

if ($shop_columns_gap !== 30) {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '[data-products]',
		'variableName' => 'grid-columns-gap',
		'value' => $shop_columns_gap
	]);
}

$shop_columns = get_theme_mod('blocksy_woo_columns', [
	'desktop' => 4,
	'tablet' => 3,
	'mobile' => 1,
]);

$shop_columns['desktop'] = get_option('woocommerce_catalog_columns', 4);

$shop_columns['desktop'] = 'CT_CSS_SKIP_RULE';
$shop_columns['tablet'] = 'repeat(' . $shop_columns['tablet'] . ', minmax(0, 1fr))';
$shop_columns['mobile'] = 'repeat(' . $shop_columns['mobile'] . ', minmax(0, 1fr))';

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '[data-products]',
	'variableName' => 'shop-columns',
	'value' => $shop_columns,
	'unit' => ''
]);

$related_columns = get_theme_mod('woo_product_related_cards_columns', [
	'mobile' => 1,
	'tablet' => 3,
	'desktop' => 4,
]);

$related_columns['desktop'] = 'CT_CSS_SKIP_RULE';
$related_columns['tablet'] = 'repeat(' . $related_columns['tablet'] . ', minmax(0, 1fr))';
$related_columns['mobile'] = 'repeat(' . $related_columns['mobile'] . ', minmax(0, 1fr))';

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.related [data-products], .upsells [data-products]',
	'variableName' => 'shop-columns',
	'value' => $related_columns,
	'unit' => ''
]);

blocksy_output_colors([
	'value' => get_theme_mod('cardProductTitleColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'variables' => [
		'default' => [
			'selector' => '[data-products] .woocommerce-loop-product__title, [data-products] .woocommerce-loop-category__title',
			'variable' => 'heading-color'
		],

		'hover' => [
			'selector' => '[data-products] .woocommerce-loop-product__title, [data-products] .woocommerce-loop-category__title',
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('cardProductPriceColor'),
	'default' => [
		'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ]
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'variables' => [
		'default' => [
			'selector' => '[data-products] .price',
			'variable' => 'color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('starRatingColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'inactive' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => ':root',
			'variable' => 'star-rating-initial-color'
		],

		'inactive' => [
			'selector' => ':root',
			'variable' => 'star-rating-inactive-color'
		],
	],
]);


// global quantity colors
$has_custom_quantity = get_theme_mod('has_custom_quantity', 'yes');

if ($has_custom_quantity === 'yes') {
	blocksy_output_colors([
		'value' => get_theme_mod('global_quantity_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.quantity',
				'variable' => 'quantity-initial-color'
			],

			'hover' => [
				'selector' => '.quantity',
				'variable' => 'quantity-hover-color'
			],
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('global_quantity_arrows'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'default_type_2' => [ 'color' => 'var(--color)' ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.quantity[data-type="type-1"]',
				'variable' => 'quantity-arrows-initial-color'
			],

			'default_type_2' => [
				'selector' => '.quantity[data-type="type-2"]',
				'variable' => 'quantity-arrows-initial-color'
			],

			'hover' => [
				'selector' => '.quantity',
				'variable' => 'quantity-arrows-hover-color'
			],
		],
	]);
}


blocksy_output_colors([
	'value' => get_theme_mod('saleBadgeColor'),
	'default' => [
		'text' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'background' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'text' => [
			'selector' => ':root',
			'variable' => 'badge-text-color'
		],

		'background' => [
			'selector' => ':root',
			'variable' => 'badge-background-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('outOfStockBadgeColor'),
	'default' => [
		'text' => [ 'color' => '#ffffff' ],
		'background' => [ 'color' => '#24292E' ],
	],
	'css' => $css,
	'variables' => [
		'text' => [
			'selector' => '.out-of-stock-badge',
			'variable' => 'badge-text-color'
		],

		'background' => [
			'selector' => '.out-of-stock-badge',
			'variable' => 'badge-background-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('cardProductCategoriesColor'),
	'default' => [
		'default' => [ 'color' => 'var(--color)' ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'variables' => [
		'default' => [
			'selector' => '[data-products] .entry-meta a',
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => '[data-products] .entry-meta a',
			'variable' => 'linkHoverColor'
		],
	],
]);


// quick view
if (get_theme_mod('woocommerce_quickview_enabled', 'yes') === 'yes') {
	blocksy_output_colors([
		'value' => get_theme_mod('quick_view_button_icon_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'variables' => [
			'default' => [
				'selector' => '.ct-woo-card-extra .ct-open-quick-view',
				'variable' => 'icon-color'
			],
			'hover' => [
				'selector' => '.ct-woo-card-extra .ct-open-quick-view',
				'variable' => 'icon-hover-color'
			],
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('quick_view_button_background_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
			'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'variables' => [
			'default' => [
				'selector' => '.ct-woo-card-extra .ct-open-quick-view',
				'variable' => 'trigger-background'
			],
			'hover' => [
				'selector' => '.ct-woo-card-extra .ct-open-quick-view',
				'variable' => 'trigger-hover-background'
			],
		],
	]);

	blocksy_output_font_css([
		'font_value' => get_theme_mod(
			'quickViewProductTitleFont',
			blocksy_typography_default_values([
				// 'size' => '30px',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.ct-quick-view-card .product_title'
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('quick_view_title_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.ct-quick-view-card .entry-summary .product_title',
				'variable' => 'heading-color'
			],
		],
	]);

	blocksy_output_font_css([
		'font_value' => get_theme_mod(
			'quickViewProductPriceFont',
			blocksy_typography_default_values([
				// 'size' => '30px',
			])
		),
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.ct-quick-view-card .entry-summary .price'
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('quick_view_price_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.ct-quick-view-card .entry-summary .price',
				'variable' => 'color'
			],
		],
	]);

	blocksy_output_colors([
		'value' => get_theme_mod('quick_view_description_color'),
		'default' => [
			'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.ct-quick-view-card .woocommerce-product-details__short-description',
				'variable' => 'color'
			],
		],
	]);

	blocksy_output_box_shadow([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.ct-quick-view-card',
		'value' => get_theme_mod('quick_view_shadow', blocksy_box_shadow_value([
			'enable' => true,
			'h_offset' => 0,
			'v_offset' => 50,
			'blur' => 100,
			'spread' => 0,
			'inset' => false,
			'color' => [
				'color' => 'rgba(18, 21, 25, 0.5)',
			],
		])),
		'responsive' => true
	]);

	blocksy_output_background_css([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'selector' => '.ct-quick-view-card > section',
		'value' => get_theme_mod('quick_view_background',
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => '#ffffff'
					],
				],
			])
		)
	]);

	blocksy_output_background_css([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'selector' => '.quick-view-modal',
		'value' => get_theme_mod('quick_view_backdrop',
			blocksy_background_default_value([
				'backgroundColor' => [
					'default' => [
						'color' => 'rgba(18, 21, 25, 0.8)'
					],
				],
			])
		)
	]);
}


if ($shop_cards_type === 'type-1') {
	blocksy_output_colors([
		'value' => get_theme_mod('cardProductButton1Text'),
		'default' => [
			'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
			'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'variables' => [
			'default' => [
				'selector' => '[data-products="type-1"]',
				'variable' => 'buttonTextInitialColor'
			],

			'hover' => [
				'selector' => '[data-products="type-1"]',
				'variable' => 'buttonTextHoverColor'
			],
		],
	]);
}

if ($shop_cards_type === 'type-2') {
	blocksy_output_colors([
		'value' => get_theme_mod('cardProductButton2Text'),
		'default' => [
			'default' => ['color' => 'var(--color)'],
			'hover' => ['color' => 'var(--linkHoverColor)'],
		],
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'responsive' => true,
		'variables' => [
			'default' => [
				'selector' => '[data-products="type-2"]',
				'variable' => 'buttonTextInitialColor'
			],

			'hover' => [
				'selector' => '[data-products="type-2"]',
				'variable' => 'buttonTextHoverColor'
			],
		],
	]);
}

blocksy_output_colors([
	'value' => get_theme_mod('cardProductButtonBackground'),
	'default' => [
		'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
		'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'variables' => [
		'default' => [
			'selector' => '[data-products]',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '[data-products]',
			'variable' => 'buttonHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('cardProductBackground'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'variables' => [
		'default' => [
			'selector' => '[data-products="type-2"]',
			'variable' => 'backgroundColor'
		],
	],
]);

// Border radius
blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '[data-products] .product',
	'property' => 'borderRadius',
	'value' => get_theme_mod( 'cardProductRadius',
		blocksy_spacing_value([
			'linked' => true,
			'top' => '3px',
			'left' => '3px',
			'right' => '3px',
			'bottom' => '3px',

		])
	)
]);

// Box shadow
blocksy_output_box_shadow([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '[data-products="type-2"]',
	'value' => get_theme_mod('cardProductShadow', blocksy_box_shadow_value([
		'enable' => true,
		'h_offset' => 0,
		'v_offset' => 12,
		'blur' => 18,
		'spread' => -6,
		'inset' => false,
		'color' => [
			'color' => 'rgba(34, 56, 101, 0.03)',
		],
	])),
	'responsive' => true
]);

// woo single product
$product_thumbs_spacing = get_theme_mod( 'product_thumbs_spacing', '15px' );

if ($product_thumbs_spacing !== '15px') {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.product-entry-wrapper',
		'variableName' => 'thumbs-spacing',
		'unit' => '',
		'value' => $product_thumbs_spacing
	]);
}



$productGalleryWidth = get_theme_mod( 'productGalleryWidth', 50 );

if ($productGalleryWidth !== 50) {
	$css->put(
		'.product-entry-wrapper',
		'--product-gallery-width: ' . $productGalleryWidth . '%'
	);
}

if (
	get_theme_mod('product_view_type', 'default-gallery')
	&&
	get_theme_mod('gallery_style', 'horizontal') === 'vertical'
) {
	global $_wp_additional_image_sizes;

	if (isset($_wp_additional_image_sizes['woocommerce_gallery_thumbnail'])) {
		$css->put(
			'.product-entry-wrapper',
			'--thumbs-width: ' . $_wp_additional_image_sizes['woocommerce_gallery_thumbnail']['width'] . 'px'
		);
	}
}

blocksy_output_colors([
	'value' => get_theme_mod('slider_nav_arrow_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-product-gallery',
			'variable' => 'flexy-nav-arrow-color'
		],

		'hover' => [
			'selector' => '.woocommerce-product-gallery',
			'variable' => 'flexy-nav-arrow-hover-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('slider_nav_background_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-product-gallery',
			'variable' => 'flexy-nav-background-color'
		],

		'hover' => [
			'selector' => '.woocommerce-product-gallery',
			'variable' => 'flexy-nav-background-hover-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('lightbox_button_icon_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-product-gallery__trigger',
			'variable' => 'lightbox-button-icon-color'
		],

		'hover' => [
			'selector' => '.woocommerce-product-gallery__trigger',
			'variable' => 'lightbox-button-icon-hover-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('lightbox_button_background_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-product-gallery__trigger',
			'variable' => 'lightbox-button-background-color'
		],

		'hover' => [
			'selector' => '.woocommerce-product-gallery__trigger',
			'variable' => 'lightbox-button-hover-background-color'
		],
	],
]);

blocksy_output_font_css([
	'font_value' => get_theme_mod(
		'singleProductTitleFont',
		blocksy_typography_default_values([
			'size' => '30px',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.entry-summary .entry-title'
]);

blocksy_output_colors([
	'value' => get_theme_mod('singleProductTitleColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .entry-title',
			'variable' => 'heading-color'
		],
	],
]);

blocksy_output_font_css([
	'font_value' => get_theme_mod(
		'singleProductPriceFont',
		blocksy_typography_default_values([
			'size' => '20px',
			'variation' => 'n7',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.product-entry-wrapper .price'
]);

blocksy_output_colors([
	'value' => get_theme_mod('singleProductPriceColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.product-entry-wrapper .price',
			'variable' => 'color'
		],
	],
]);

blocksy_output_font_css([
	'font_value' => get_theme_mod(
		'cardProductTitleFont',
		blocksy_typography_default_values([
			'size' => '17px',
			'variation' => 'n6',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '[data-products] .woocommerce-loop-product__title, [data-products] .woocommerce-loop-category__title'
]);

blocksy_output_font_css([
	'font_value' => get_theme_mod(
		'cardProductExcerptFont',
		blocksy_typography_default_values([])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '[data-products] .entry-excerpt'
]);

blocksy_output_colors([
	'value' => get_theme_mod('cardProductExcerptColor'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'responsive' => true,
	'variables' => [
		'default' => [
			'selector' => '[data-products] .entry-excerpt',
			'variable' => 'color'
		],
	],
]);

// Store notice
blocksy_output_colors([
	'value' => get_theme_mod('wooNoticeContent'),
	'default' => [
		'default' => ['color' => '#ffffff']
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.demo_store',
			'variable' => 'color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('wooNoticeBackground'),
	'default' => [
		'default' => ['color' => 'var(--paletteColor1)']
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.demo_store',
			'variable' => 'backgroundColor'
		],
	],
]);

// success message
blocksy_output_colors([
	'value' => get_theme_mod('success_message_text_color'),
	'default' => [
		'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
		'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-message',
			'variable' => 'color'
		],

		'hover' => [
			'selector' => '.woocommerce-message',
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('success_message_background_color'),
	'default' => [
		'default' => ['color' => '#F0F1F3'],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-message',
			'variable' => 'background-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('success_message_button_text_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-message',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-message',
			'variable' => 'buttonTextHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('success_message_button_background'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-message',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-message',
			'variable' => 'buttonHoverColor'
		],
	],
]);


// info message
blocksy_output_colors([
	'value' => get_theme_mod('info_message_text_color'),
	'default' => [
		'default' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
		'hover' => ['color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT')],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-info, .woocommerce-thankyou-order-received',
			'variable' => 'color'
		],

		'hover' => [
			'selector' => '.woocommerce-info, .woocommerce-thankyou-order-received',
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('info_message_background_color'),
	'default' => [
		'default' => ['color' => '#F0F1F3'],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-info, .woocommerce-thankyou-order-received',
			'variable' => 'background-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('info_message_button_text_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-info',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-info',
			'variable' => 'buttonTextHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('info_message_button_background'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-info',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-info',
			'variable' => 'buttonHoverColor'
		],
	],
]);

// error message
blocksy_output_colors([
	'value' => get_theme_mod('error_message_text_color'),
	'default' => [
		'default' => ['color' => '#ffffff'],
		'hover' => ['color' => '#ffffff'],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-error',
			'variable' => 'color'
		],

		'hover' => [
			'selector' => '.woocommerce-error',
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('error_message_background_color'),
	'default' => [
		'default' => ['color' => 'rgba(218, 0, 28, 0.7)'],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-error',
			'variable' => 'background-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('error_message_button_text_color'),
	'default' => [
		'default' => [ 'color' => '#ffffff' ],
		'hover' => [ 'color' => '#ffffff' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-error',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-error',
			'variable' => 'buttonTextHoverColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('error_message_button_background'),
	'default' => [
		'default' => [ 'color' => '#b92c3e' ],
		'hover' => [ 'color' => '#9c2131' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-error',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-error',
			'variable' => 'buttonHoverColor'
		],
	],
]);


// add to cart actions
$add_to_cart_button_width = get_theme_mod('add_to_cart_button_width', '100%');

if ($add_to_cart_button_width !== '100%') {
	blocksy_output_responsive([
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector' => '.entry-summary form.cart',
		'variableName' => 'button-width',
		'unit' => '',
		'value' => $add_to_cart_button_width,
	]);
}


blocksy_output_colors([
	'value' => get_theme_mod('quantity_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .quantity',
			'variable' => 'quantity-initial-color'
		],

		'hover' => [
			'selector' => '.entry-summary .quantity',
			'variable' => 'quantity-hover-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('quantity_arrows'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'default_type_2' => [ 'color' => 'var(--color)' ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .quantity[data-type="type-1"]',
			'variable' => 'quantity-arrows-initial-color'
		],

		'default_type_2' => [
			'selector' => '.entry-summary .quantity[data-type="type-2"]',
			'variable' => 'quantity-arrows-initial-color'
		],

		'hover' => [
			'selector' => '.entry-summary .quantity',
			'variable' => 'quantity-arrows-hover-color'
		],
	],
]);


blocksy_output_colors([
	'value' => get_theme_mod('add_to_cart_text'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .single_add_to_cart_button',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.entry-summary .single_add_to_cart_button',
			'variable' => 'buttonTextHoverColor'
		],
	],
	'responsive' => true
]);


blocksy_output_colors([
	'value' => get_theme_mod('add_to_cart_background'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .single_add_to_cart_button',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.entry-summary .single_add_to_cart_button',
			'variable' => 'buttonHoverColor'
		],
	],
	'responsive' => true
]);


blocksy_output_colors([
	'value' => get_theme_mod('view_cart_button_text'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .ct-cart-actions .added_to_cart',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.entry-summary .ct-cart-actions .added_to_cart',
			'variable' => 'buttonTextHoverColor'
		],
	],
	'responsive' => true
]);


blocksy_output_colors([
	'value' => get_theme_mod('view_cart_button_background'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'variables' => [
		'default' => [
			'selector' => '.entry-summary .ct-cart-actions .added_to_cart',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.entry-summary .ct-cart-actions .added_to_cart',
			'variable' => 'buttonHoverColor'
		],
	],
	'responsive' => true
]);

// product tabs
$tabs_type = get_theme_mod( 'woo_tabs_type', 'type-1' );

blocksy_output_font_css([
	'font_value' => get_theme_mod( 'woo_tabs_font',
		blocksy_typography_default_values([
			'size' => '12px',
			'variation' => 'n6',
			'text-transform' => 'uppercase',
		])
	),
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => '.woocommerce-tabs .tabs',
]);

blocksy_output_colors([
	'value' => get_theme_mod('woo_tabs_font_color'),
	'default' => [
		'default' => [ 'color' => 'var(--color)' ],
		'hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-tabs .tabs',
			'variable' => 'linkInitialColor'
		],

		'hover' => [
			'selector' => '.woocommerce-tabs .tabs',
			'variable' => 'linkHoverColor'
		],

		'active' => [
			'selector' => '.woocommerce-tabs .tabs',
			'variable' => 'linkActiveColor'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('woo_tabs_border_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-tabs[data-type] .tabs',
			'variable' => 'tab-border-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('woo_actibe_tab_border'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.woocommerce-tabs[data-type] .tabs',
			'variable' => 'tab-background'
		],
	],
]);

if ($tabs_type === 'type-2') {
	blocksy_output_colors([
		'value' => get_theme_mod('woo_actibe_tab_background'),
		'default' => [
			'default' => [ 'color' => 'rgba(242, 244, 247, 0.7)' ],
			'border' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT') ],
		],
		'css' => $css,
		'variables' => [
			'default' => [
				'selector' => '.woocommerce-tabs[data-type*="type-2"] .tabs',
				'variable' => 'tab-background'
			],

		'border' => [
				'selector' => '.woocommerce-tabs[data-type*="type-2"] .tabs li.active',
				'variable' => 'tab-border-color'
			],
		],
	]);
}


// account page
blocksy_output_colors([
	'value' => get_theme_mod('account_nav_text_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-acount-nav',
			'variable' => 'account-nav-text-initial-color'
		],

		'active' => [
			'selector' => '.ct-acount-nav',
			'variable' => 'account-nav-text-active-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('account_nav_background_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'active' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-acount-nav',
			'variable' => 'account-nav-background-initial-color'
		],

		'active' => [
			'selector' => '.ct-acount-nav',
			'variable' => 'account-nav-background-active-color'
		],
	],
]);

blocksy_output_colors([
	'value' => get_theme_mod('account_nav_divider_color'),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.ct-acount-nav',
			'variable' => 'account-nav-divider-color'
		],
	],
]);

blocksy_output_box_shadow([
	'css' => $css,
	'selector' => '.ct-acount-nav',
	'value' => get_theme_mod('account_nav_shadow', blocksy_box_shadow_value([
		'enable' => false,
		'h_offset' => 0,
		'v_offset' => 10,
		'blur' => 20,
		'spread' => 0,
		'inset' => false,
		'color' => [
			'color' => 'rgba(0, 0, 0, 0.03)',
		],
	])),
]);
