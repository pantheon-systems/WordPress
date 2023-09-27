<?php

$options = [
	'woo_single_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			blocksy_get_options('general/page-title', [
				'prefix' => 'product',
				'is_single' => true,
				'enabled_label' => __('Product Title', 'blocksy')
			]),

			[
				blocksy_rand_md5() => [
					'type'  => 'ct-title',
					'label' => __( 'Page Structure', 'blocksy' ),
				],

				blocksy_rand_md5() => [
					'title' => __( 'General', 'blocksy' ),
					'type' => 'tab',
					'options' => [
						blocksy_get_options('single-elements/structure', [
							'prefix' => 'product',
							'default_structure' => 'type-4',
							'has_v_spacing' => true
						]),

						[
							blocksy_rand_md5() => [
								'type'  => 'ct-title',
								'label' => __( 'Product Gallery', 'blocksy' ),
							],
						],

						apply_filters(
							'blocksy:options:single_product:product-general-tab:start',
							[
								'product_view_type' => [
									'type' => 'hidden',
									'value' => 'default-gallery'
								]
							]
						),

						blocksy_get_options('posts/woo-gallery'),
					],
				],

				blocksy_rand_md5() => [
					'title' => __( 'Design', 'blocksy' ),
					'type' => 'tab',
					'options' => [
						blocksy_get_options('single-elements/structure-design', [
							'prefix' => 'product',
						]),
					],
				],

				blocksy_rand_md5() => [
					'type'  => 'ct-title',
					'label' => __( 'Product Elements', 'blocksy' ),
				],

				'has_product_single_title' => [
					'label' => __('Product Title', 'blocksy'),
					'type' => 'ct-panel',
					'switch' => true,
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'prefix' => 'product',
						'loader_selector' => '.entry-summary'
					]),
					'inner-options' => [

						'singleProductTitleFont' => [
							'type' => 'ct-typography',
							'label' => __( 'Font', 'blocksy' ),
							'value' => blocksy_typography_default_values([
								'size' => '30px',
							]),
							'setting' => [ 'transport' => 'postMessage' ],
						],

						'singleProductTitleColor' => [
							'label' => __( 'Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy' ),
									'id' => 'default',
									'inherit' => 'var(--heading-1-color, var(--headings-color))'
								],
							],
						],

					],
				],

				blocksy_rand_md5() => [
					'label' => __( 'Product Price', 'blocksy' ),
					'type' => 'ct-panel',
					'inner-options' => [

						'singleProductPriceFont' => [
							'type' => 'ct-typography',
							'label' => __( 'Font', 'blocksy' ),
							'value' => blocksy_typography_default_values([
								'size' => '20px',
								'variation' => 'n7',
							]),
							'setting' => [ 'transport' => 'postMessage' ],
						],

						'singleProductPriceColor' => [
							'label' => __( 'Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy' ),
									'id' => 'default',
									'inherit' => 'var(--color)'
								],
							],
						],

					],
				],
			],

			blocksy_get_options('posts/woo-add-to-cart'),

			[
				'has_product_single_onsale' => [
					'label' => __( 'Sale Badge', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'prefix' => 'product',
						'loader_selector' => '.entry-summary'
					]),
				],

				'has_product_single_rating' => [
					'label' => __( 'Star Rating', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'prefix' => 'product',
						'loader_selector' => '.entry-summary'
					]),
				],

				'has_product_single_meta' => [
					'label' => __( 'Product Meta', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'prefix' => 'product',
						'loader_selector' => '.entry-summary'
					]),
				],
			],

			apply_filters(
				'blocksy:options:single_product:product-elements:end',
				[]
			),

			[
				blocksy_rand_md5() => [
					'type'  => 'ct-title',
					'label' => __( 'Page Elements', 'blocksy' ),
				],
			],

			blocksy_get_options('posts/woo-product-tabs'),

			[
				blocksy_rand_md5() => [
					'label' => __( 'Related & Upsells', 'blocksy' ),
					'type' => 'ct-panel',
					'inner-options' => [

						'woo_product_related_cards_columns' => [
							'label' => __('Columns & Rows', 'blocksy'),
							'type' => 'ct-woocommerce-columns-and-rows',
							'value' => [
								'desktop' => 4,
								'tablet' => 3,
								'mobile' => 1
							],
							'min' => 1,
							'max' => 5,
							'responsive' => true,
							'sync' => blocksy_sync_whole_page([
								'prefix' => 'product',
								'loader_selector' => '[class*="post"] .products'
							]),
							'columns_id' => 'woo_product_related_cards_columns',
							'rows_id' => 'woo_product_related_cards_rows'
						],

						'woo_product_related_cards_rows' => [
							'type' => 'hidden',
							'value' => 1,
							'sync' => blocksy_sync_whole_page([
								'prefix' => 'product',
								'loader_selector' => '[class*="post"] .products'
							]),
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						'related_products_visibility' => [
							'label' => __('Related Products Visibility', 'blocksy'),
							'type' => 'ct-visibility',
							'design' => 'block',
							'setting' => ['transport' => 'postMessage'],
							'allow_empty' => true,

							'value' => [
								'desktop' => true,
								'tablet' => false,
								'mobile' => false,
							],

							'choices' => blocksy_ordered_keys([
								'desktop' => __( 'Desktop', 'blocksy' ),
								'tablet' => __( 'Tablet', 'blocksy' ),
								'mobile' => __( 'Mobile', 'blocksy' ),
							]),
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						'upsell_products_visibility' => [
							'label' => __('Upsell Products Visibility', 'blocksy'),
							'type' => 'ct-visibility',
							'design' => 'block',
							'setting' => ['transport' => 'postMessage'],
							'allow_empty' => true,

							'value' => [
								'desktop' => true,
								'tablet' => false,
								'mobile' => false,
							],

							'choices' => blocksy_ordered_keys([
								'desktop' => __( 'Desktop', 'blocksy' ),
								'tablet' => __( 'Tablet', 'blocksy' ),
								'mobile' => __( 'Mobile', 'blocksy' ),
							]),
						],

					],
				],
			],

			apply_filters(
				'blocksy_single_product_elements_end_customizer_options',
				[]
			),

			blocksy_rand_md5() => [
				'type'  => 'ct-title',
				'label' => __( 'Functionality Options', 'blocksy' ),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'product_view_type' => 'default-gallery|stacked-gallery'
				],
				'options' => [
					'has_product_sticky_gallery' => [
						'label' => __('Sticky Gallery', 'blocksy'),
						'type' => 'ct-switch',
						'value' => 'no',
						'sync' => 'live'
					],

					'has_product_sticky_summary' => [
						'label' => __('Sticky Summary', 'blocksy'),
						'type' => 'ct-switch',
						'value' => 'no',
						'sync' => 'live'
					],
				],
			],

			'has_ajax_add_to_cart' => [
				'label' => __('AJAX Add To Cart', 'blocksy'),
				'type' => 'ct-switch',
				'value' => 'no',
			],
		],
	],
];
