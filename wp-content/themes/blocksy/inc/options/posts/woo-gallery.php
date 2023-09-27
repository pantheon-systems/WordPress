<?php

$options = [
	blocksy_rand_md5() => [
		'label' => __( 'Gallery Options', 'blocksy' ),
		'type' => 'ct-panel',
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					[
						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [
								'product_view_type' => 'default-gallery|stacked-gallery',
							],
							'options' => [

								'productGalleryWidth' => [
									'label' => __( 'Product Gallery Width', 'blocksy' ),
									'type' => 'ct-slider',
									'defaultUnit' => '%',
									'value' => 50,
									'min' => 20,
									'max' => 70,
									'setting' => [ 'transport' => 'postMessage' ],
								],

								blocksy_rand_md5() => [
									'type' => 'ct-divider',
								],

							],
						],
					],

					apply_filters(
						'blocksy:options:single_product:gallery-options:start',
						[]
					),

					[
						'product_thumbs_spacing' => [
							'label' => [
								__( 'Thumbnails Spacing', 'blocksy' ) => [
									'product_view_type' => '!columns-top-gallery|!stacked-gallery'
								],
								__( 'Columns Spacing', 'blocksy' ) => [
									'product_view_type' => 'columns-top-gallery|stacked-gallery'
								],
							],
							'type' => 'ct-slider',
							'value' => '15px',
							'units' => blocksy_units_config([
								[ 'unit' => 'px', 'min' => 0, 'max' => 100 ],
							]),
							'responsive' => true,
							'setting' => [ 'transport' => 'postMessage' ],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => [ 'product_view_type' => 'default-gallery' ],
							'options' => [

								'gallery_style' => [
									'label' => __('Thumbnails Position', 'blocksy'),
									'type' => 'ct-radio',
									'value' => 'horizontal',
									'view' => 'text',
									'design' => 'block',
									'divider' => 'top',
									'choices' => [
										'horizontal' => __( 'Horizontal', 'blocksy' ),
										'vertical' => __( 'Vertical', 'blocksy' ),
									],

									'sync' => blocksy_sync_whole_page([
										'loader_selector' => '.woocommerce-product-gallery',
										'prefix' => 'product'
									])
								],

							],
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
						],

						'product_gallery_ratio' => [
							'label' => __( 'Image', 'blocksy' ),
							'type' => 'ct-ratio',
							'value' => '3/4',
							'design' => 'inline',
							'attr' => [ 'data-type' => 'compact' ],
							'setting' => [ 'transport' => 'postMessage' ],
							'preview_width_key' => 'woocommerce_single_image_width',
							'inner-options' => [

								'woocommerce_single_image_width' => [
									'type' => 'text',
									'label' => __('Image Size', 'blocksy'),
									'desc' => __('Image size used for the main image on single product pages.', 'blocksy'),
									'value' => 600,
									'design' => 'inline',
									'setting' => [
										'type' => 'option',
										'capability' => 'manage_woocommerce',
									]
								],

							],
						],

						'has_product_single_lightbox' => [
							'label' => __( 'Lightbox', 'blocksy' ),
							'type' => 'ct-switch',
							'value' => 'no',
							'sync' => blocksy_sync_whole_page([
								'prefix' => 'product',
								'loader_selector' => '.woocommerce-product-gallery'
							]),
						],

						'has_product_single_zoom' => [
							'label' => __( 'Zoom Effect', 'blocksy' ),
							'type' => 'ct-switch',
							'value' => 'yes',
							'sync' => blocksy_sync_whole_page([
								'prefix' => 'product',
								'loader_selector' => '.woocommerce-product-gallery'
							]),
						],

					],

					apply_filters(
						'blocksy:options:single_product:gallery-options:end',
						[]
					),

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					'slider_nav_arrow_color' => [
						'label' => __( 'Prev/Next Arrow', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => '#ffffff'
							],
						],
					],

					'slider_nav_background_color' => [
						'label' => __( 'Prev/Next Background', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => '#ffffff'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--paletteColor1)'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'has_product_single_lightbox' => 'yes' ],
						'options' => [

							'lightbox_button_icon_color' => [
								'label' => __( 'Lightbox Button Icon Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],

									'hover' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
										'inherit' => 'var(--color)'
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => '#ffffff'
									],
								],
							],

							'lightbox_button_background_color' => [
								'label' => __( 'Lightbox Button Background', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],

									'hover' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
										'inherit' => '#ffffff'
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => 'var(--paletteColor1)'
									],
								],
							],

						],
					],

				],
			],

		],
	],
];
