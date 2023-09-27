<?php

$options = [
	'woo_categories_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [

			blocksy_get_options('general/page-title', [
				'prefix' => 'woo_categories',
				'is_woo' => true,
			]),

			[

				blocksy_rand_md5() => [
					'type' => 'ct-title',
					'label' => __( 'Shop Settings', 'blocksy' ),
				],

				blocksy_rand_md5() => [
					'title' => __( 'General', 'blocksy' ),
					'type' => 'tab',
					'options' => [

						'shop_cards_type' => [
							'label' => false,
							'type' => 'ct-image-picker',
							'value' => 'type-1',
							'divider' => 'bottom',
							'setting' => [ 'transport' => 'postMessage' ],
							'choices' => [

								'type-1' => [
									'src'   => blocksy_image_picker_url( 'woo-type-1.svg' ),
									'title' => __( 'Type 1', 'blocksy' ),
								],

								'type-2' => [
									'src'   => blocksy_image_picker_url( 'woo-type-2.svg' ),
									'title' => __( 'Type 2', 'blocksy' ),
								],


							],

							'sync' => blocksy_sync_whole_page([
								'prefix' => 'woo_categories',
								'loader_selector' => '.products > li'
							]),
						],

						'blocksy_woo_columns' => [
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
							'setting' => [
								'transport' => 'postMessage'
							],
						],

						'woocommerce_catalog_columns' => [
							'type' => 'hidden',
							'value' => 4,
							'setting' => [
								'type' => 'option',
								'transport' => 'postMessage'
							],
						],

						'woocommerce_catalog_rows' => [
							'type' => 'hidden',
							'value' => 4,
							'setting' => [
								'type' => 'option',
							],

							'sync' => blocksy_sync_whole_page([
								'prefix' => 'woo_categories',
								'loader_selector' => '.products > li'
							]),
						],

						blocksy_rand_md5() => [
							'type' => 'ct-divider',
							'attr' => [ 'data-type' => 'small' ]
						],

						'product_card_options_panel' => [
							'label' => __( 'Cards Options', 'blocksy' ),
							'type' => 'ct-panel',
							'wrapperAttr' => [ 'data-panel' => 'only-arrow' ],
							'setting' => [ 'transport' => 'postMessage' ],
							'inner-options' => [

								blocksy_rand_md5() => [
									'title' => __( 'General', 'blocksy' ),
									'type' => 'tab',
									'options' => [
										[
											'blocksy_woocommerce_thumbnail_cropping' => [
												'label' => __('Image', 'blocksy'),
												'type' => 'ct-woocommerce-ratio',
												/**
												 * Can be
												 * 1:1
												 * custom
												 * predefined
												 */
												'value' => 'predefined',
												'design' => 'inline',
												'setting' => [
													// 'type' => 'option',
													'transport' => 'postMessage'
												],
												'preview_width_key' => 'woocommerce_thumbnail_image_width',
												'inner-options' => [
													'woocommerce_thumbnail_image_width' => [
														'type' => 'text',
														'label' => __('Image Width', 'blocksy'),
														'desc' => __('Image height will be automatically calculated based on the image ratio.', 'blocksy'),
														'value' => 500,
														'design' => 'inline',
														'setting' => [
															'type' => 'option',
															'capability' => 'manage_woocommerce',
														]
													],
												],
											],

											'woocommerce_thumbnail_cropping_custom_width' => [
												'label' => false,
												'type' => 'hidden',
												'value' => 4,
												'setting' => [
													'type' => 'option',
													'capability' => 'manage_woocommerce',
													'transport' => 'postMessage'
												],
												'disableRevertButton' => true,
												'desc' => __('Width', 'blocksy'),
											],

											'woocommerce_thumbnail_cropping_custom_height' => [
												'label' => false,
												'type' => 'hidden',
												'value' => 3,
												'setting' => [
													'type' => 'option',
													'capability' => 'manage_woocommerce',
													'transport' => 'postMessage'
												],
												'disableRevertButton' => true,
												'desc' => __('Height', 'blocksy'),
											],

											'product_image_hover' => [
												'label' => __( 'Image Hover Effect', 'blocksy' ),
												'type' => 'ct-select',
												'value' => 'none',
												'view' => 'text',
												'design' => 'inline',
												'setting' => [ 'transport' => 'postMessage' ],
												'choices' => blocksy_ordered_keys(
													[
														'none' => __( 'None', 'blocksy' ),
														'swap' => __( 'Swap Images', 'blocksy' ),
														'zoom-in' => __( 'Zoom In', 'blocksy' ),
														'zoom-out' => __( 'Zoom Out', 'blocksy' ),
													]
												),

												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],
										],

										(
											function_exists('blc_fs')
											&&
											blc_fs()->can_use_premium_code()
										) ? [
											'has_archive_video_thumbnail' => [
												'label' => __( 'Video Thumbnail', 'blocksy' ),
												'type' => 'ct-switch',
												'value' => 'no',
												// 'divider' => 'top',
												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],
										] : [],

										[
											'has_star_rating' => [
												'label' => __('Star Rating', 'blocksy'),
												'type' => 'ct-switch',
												'value' => 'yes',
												'divider' => 'top',
												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],

											'has_sale_badge' => [
												'label' => __( 'Sale Badge', 'blocksy' ),
												'type' => 'ct-switch',
												'value' => 'yes',
												'divider' => 'top',
												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],

											'has_product_categories' => [
												'label' => __( 'Product Categories', 'blocksy' ),
												'type' => 'ct-switch',
												'value' => 'no',
												'divider' => 'top',
												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],

											'has_excerpt' => [
												'label' => __('Short Description', 'blocksy'),
												'type' => 'ct-switch',
												'value' => 'no',
												'divider' => 'top',
												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],

											blocksy_rand_md5() => [
												'type' => 'ct-condition',
												'condition' => [ 'has_excerpt' => 'yes' ],
												'options' => [

													'excerpt_length' => [
														'label' => __('Length', 'blocksy'),
														'type' => 'ct-number',
														'design' => 'inline',
														'value' => 40,
														'min' => 1,
														'max' => 300,
													],

												],
											],

											'has_product_action_button' => [
												'label' => __( 'Add to Cart Button', 'blocksy' ),
												'type' => 'ct-switch',
												'value' => 'yes',
												'divider' => 'top',
												'sync' => blocksy_sync_whole_page([
													'prefix' => 'woo_categories',
													'loader_selector' => '.products > li'
												]),
											],
										],

										apply_filters(
											'blocksy_woo_card_options_elements',
											[]
										),

										[
											blocksy_rand_md5() => [
												'type' => 'ct-condition',
												'condition' => [ 'shop_cards_type' => 'type-1' ],
												'options' => [

													'shop_cards_alignment_1' => [
														'type' => 'ct-radio',
														'label' => __( 'Content Alignment', 'blocksy' ),
														'view' => 'text',
														'design' => 'block',
														'divider' => 'top',
														'responsive' => true,
														'attr' => [ 'data-type' => 'alignment' ],
														'setting' => [ 'transport' => 'postMessage' ],
														'value' => 'CT_CSS_SKIP_RULE',
														'choices' => [
															'flex-start' => '',
															'center' => '',
															'flex-end' => '',
														],
													],
												],
											],

											'shopCardsGap' => [
												'label' => __( 'Cards Gap', 'blocksy' ),
												'type' => 'ct-slider',
												'min' => 0,
												'max' => 100,
												'responsive' => true,
												'divider' => 'top',
												'value' => [
													'mobile' => 30,
													'tablet' => 30,
													'desktop' => 30,
												],
												'setting' => [ 'transport' => 'postMessage' ],
											],
										],

									],
								],

								blocksy_rand_md5() => [
									'title' => __( 'Design', 'blocksy' ),
									'type' => 'tab',
									'options' => [

										'cardProductTitleFont' => [
											'type' => 'ct-typography',
											'label' => __( 'Title Font', 'blocksy' ),
											'value' => blocksy_typography_default_values([
												'size' => '17px',
												'variation' => 'n6',
											]),
											'setting' => [ 'transport' => 'postMessage' ],
										],

										'cardProductTitleColor' => [
											'label' => __( 'Title Color', 'blocksy' ),
											'type'  => 'ct-color-picker',
											'design' => 'block:right',
											'responsive' => true,
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
													'inherit' => 'var(--heading-2-color, var(--headings-color))'
												],

												[
													'title' => __( 'Hover', 'blocksy' ),
													'id' => 'hover',
													'inherit' => 'var(--linkHoverColor)'
												],
											],
										],

										blocksy_rand_md5() => [
											'type' => 'ct-condition',
											'condition' => [ 'has_excerpt' => 'yes' ],
											'options' => [

												'cardProductExcerptFont' => [
													'type' => 'ct-typography',
													'label' => __( 'Short Description Font', 'blocksy' ),
													'value' => blocksy_typography_default_values([]),
													'setting' => [ 'transport' => 'postMessage' ],
													'divider' => 'top:full',
												],

												'cardProductExcerptColor' => [
													'label' => __( 'Short Description Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
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

										'cardProductPriceColor' => [
											'label' => __( 'Price Color', 'blocksy' ),
											'type'  => 'ct-color-picker',
											'design' => 'block:right',
											'responsive' => true,
											'divider' => 'top:full',
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

										blocksy_rand_md5() => [
											'type' => 'ct-condition',
											'condition' => [ 'has_product_categories' => 'yes' ],
											'options' => [

												'cardProductCategoriesColor' => [
													'label' => __( 'Categories Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'top:full',
													'setting' => [ 'transport' => 'postMessage' ],

													'value' => [
														'default' => [
															'color' => 'var(--color)',
														],

														'hover' => [
															'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
														],
													],

													'pickers' => [
														[
															'title' => __( 'Initial', 'blocksy' ),
															'id' => 'default',
														],

														[
															'title' => __( 'Hover', 'blocksy' ),
															'id' => 'hover',
															'inherit' => 'var(--linkHoverColor)'
														],
													],
												],

											],
										],

										blocksy_rand_md5() => [
											'type' => 'ct-condition',
											'condition' => [ 'shop_cards_type' => 'type-1' ],
											'options' => [

												'cardProductButton1Text' => [
													'label' => __( 'Button Text Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'top:full',
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
															'inherit' => 'var(--buttonTextInitialColor)'
														],

														[
															'title' => __( 'Hover', 'blocksy' ),
															'id' => 'hover',
															'inherit' => 'var(--buttonTextHoverColor)'
														],
													],
												],

												'cardProductButtonBackground' => [
													'label' => __( 'Button Background Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
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
															'inherit' => 'var(--buttonInitialColor)'
														],

														[
															'title' => __( 'Hover', 'blocksy' ),
															'id' => 'hover',
															'inherit' => 'var(--buttonHoverColor)'
														],
													],
												],

											],
										],


										blocksy_rand_md5() => [
											'type' => 'ct-condition',
											'condition' => [ 'shop_cards_type' => 'type-2' ],
											'options' => [

												'cardProductButton2Text' => [
													'label' => __( 'Button Text Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'top:full',
													'setting' => [ 'transport' => 'postMessage' ],

													'value' => [
														'default' => [
															'color' => 'var(--color)',
														],

														'hover' => [
															'color' => 'var(--linkHoverColor)',
														],
													],

													'pickers' => [
														[
															'title' => __( 'Initial', 'blocksy' ),
															'id' => 'default',
														],

														[
															'title' => __( 'Hover', 'blocksy' ),
															'id' => 'hover',
														],
													],
												],

												'cardProductBackground' => [
													'label' => __( 'Card Background Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'top:full',
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
															'inherit' => '#ffffff'
														],
													],
												],

												'cardProductShadow' => [
													'label' => __( 'Card Shadow', 'blocksy' ),
													'type' => 'ct-box-shadow',
													'responsive' => true,
													'divider' => 'top',
													'setting' => [ 'transport' => 'postMessage' ],
													'value' => blocksy_box_shadow_value([
														'enable' => true,
														'h_offset' => 0,
														'v_offset' => 12,
														'blur' => 18,
														'spread' => -6,
														'inset' => false,
														'color' => [
															'color' => 'rgba(34, 56, 101, 0.03)',
														],
													])
												],

											],
										],

										'cardProductRadius' => [
											'label' => [
												__('Image Border Radius', 'blocksy') => [
													'shop_cards_type' => 'type-1'
												],

												__('Card Border Radius', 'blocksy') => [
													'shop_cards_type' => 'type-2'
												]
											],
											'type' => 'ct-spacing',
											'divider' => 'top:full',
											'setting' => [ 'transport' => 'postMessage' ],
											'value' => blocksy_spacing_value([
												'linked' => true,
												'top' => '3px',
												'left' => '3px',
												'right' => '3px',
												'bottom' => '3px',
											]),
											'responsive' => true
										],

										blocksy_rand_md5() => [
											'type' => 'ct-condition',
											'condition' => [ 'woocommerce_quickview_enabled' => 'yes' ],
											'options' => [

												blocksy_rand_md5() => [
													'type' => 'ct-title',
													'label' => __( 'Quick View Button', 'blocksy' ),
												],

												'quick_view_button_icon_color' => [
													'label' => __( 'Icon Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
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
															'inherit' => 'var(--color)',
														],

														[
															'title' => __( 'Hover/Active', 'blocksy' ),
															'id' => 'hover',
															'inherit' => '#ffffff',
														],
													],
												],

												'quick_view_button_background_color' => [
													'label' => __( 'Background Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													// 'divider' => 'top',
													'responsive' => true,
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
															'inherit' => '#ffffff',
														],

														[
															'title' => __( 'Hover/Active', 'blocksy' ),
															'id' => 'hover',
															'inherit' => 'var(--paletteColor1)',
														],
													],
												],

												blocksy_rand_md5() => [
													'type' => 'ct-title',
													'label' => __( 'Quick View Modal', 'blocksy' ),
												],

												'quickViewProductTitleFont' => [
													'type' => 'ct-typography',
													'label' => __( 'Title Font', 'blocksy' ),
													'value' => blocksy_typography_default_values([
														// 'size' => '30px',
													]),
													'setting' => [ 'transport' => 'postMessage' ],
												],

												'quick_view_title_color' => [
													'label' => __( 'Title Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'bottom',
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

												'quickViewProductPriceFont' => [
													'type' => 'ct-typography',
													'label' => __( 'Price Font', 'blocksy' ),
													'value' => blocksy_typography_default_values([
														// 'size' => '30px',
													]),
													'setting' => [ 'transport' => 'postMessage' ],
												],

												'quick_view_price_color' => [
													'label' => __( 'Price Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'bottom',
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

												'quick_view_description_color' => [
													'label' => __( 'Description Color', 'blocksy' ),
													'type'  => 'ct-color-picker',
													'design' => 'block:right',
													'responsive' => true,
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

												'quick_view_shadow' => [
													'label' => __( 'Modal Shadow', 'blocksy' ),
													'type' => 'ct-box-shadow',
													'responsive' => true,
													'divider' => 'top',
													'sync' => 'live',
													'value' => blocksy_box_shadow_value([
														'enable' => true,
														'h_offset' => 0,
														'v_offset' => 50,
														'blur' => 100,
														'spread' => 0,
														'inset' => false,
														'color' => [
															'color' => 'rgba(18, 21, 25, 0.5)',
														],
													])
												],

												'quick_view_background' => [
													'label' => __( 'Modal Background', 'blocksy' ),
													'type'  => 'ct-background',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'top',
													'setting' => [ 'transport' => 'postMessage' ],
													'value' => blocksy_background_default_value([
														'backgroundColor' => [
															'default' => [
																'color' => '#ffffff'
															],
														],
													])
												],

												'quick_view_backdrop' => [
													'label' => __( 'Modal Backgrop', 'blocksy' ),
													'type'  => 'ct-background',
													'design' => 'block:right',
													'responsive' => true,
													'divider' => 'top',
													'setting' => [ 'transport' => 'postMessage' ],
													'value' => blocksy_background_default_value([
														'backgroundColor' => [
															'default' => [
																'color' => 'rgba(18, 21, 25, 0.8)'
															],
														],
													])
												],

											],
										],

									],
								],

							],
						],

					],
				],

				blocksy_rand_md5() => [
					'title' => __( 'Design', 'blocksy' ),
					'type' => 'tab',
					'options' => [
						'shop_archive_background' => [
							'label' => __('Page Background', 'blocksy'),
							'type' => 'ct-background',
							'design' => 'block:right',
							'responsive' => true,
							'sync' => 'live',
							'divider' => 'bottom',
							'value' => blocksy_background_default_value([
								'backgroundColor' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
									],
								],
							]),
							'desc' => sprintf(
								// translators: placeholder here means the actual URL.
								__( 'Please note, by default this option is inherited from Colors ➝ %sSite Background%s.', 'blocksy' ),
								sprintf(
									'<a data-trigger-section="color" href="%s">',
									admin_url('/customize.php?autofocus[section]=color')
								),
								'</a>'
							),
						],
					],
				],

				blocksy_rand_md5() => [
					'type'  => 'ct-title',
					'label' => __( 'Page Elements', 'blocksy' ),
				],

				'has_shop_sort' => [
					'label' => __( 'Shop Sort', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'prefix' => 'woo_categories',
						'loader_selector' => '.woo-listing-top'
					]),
				],

				'has_shop_results_count' => [
					'label' => __( 'Shop Results Count', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'prefix' => 'woo_categories',
						'loader_selector' => '.woo-listing-top'
					]),
				],
			],

			blocksy_get_options('general/sidebar-particular', [
				'prefix' => 'woo_categories',
			]),

			blocksy_get_options('general/pagination', [
				'prefix' => 'woo_categories',
			]),

			apply_filters(
				'blocksy:options:woocommerce:archive:page-elements-end',
				[]
			),

			[
				blocksy_rand_md5() => [
					'type'  => 'ct-title',
					'label' => __( 'Functionality Options', 'blocksy' ),
				],

				'product_catalog_panel' => [
					'label' => __( 'Product Catalog', 'blocksy' ),
					'type' => 'ct-panel',
					'wrapperAttr' => [ 'data-panel' => 'only-arrow' ],
					'setting' => [ 'transport' => 'postMessage' ],
					'inner-options' => [

						'woocommerce_shop_page_display' => [
							'label' => __( 'Shop page display', 'blocksy' ),
							'type' => 'ct-select',
							'value' => '',
							'view' => 'text',
							'placeholder' => __('Show products', 'blocksy'),
							'design' => 'block',
							'setting' => [
								'type' => 'option'
							],
							'desc' => __( 'Choose what to display on the main shop page.', 'blocksy' ),
							'choices' => blocksy_ordered_keys(
								[
									'' => __('Show products', 'blocksy'),
									'subcategories' => __('Show categories', 'blocksy'),
									'both' => __('Show categories & products', 'blocksy'),
								]
							),
						],

						'woocommerce_category_archive_display' => [
							'label' => __( 'Category display', 'blocksy' ),
							'type' => 'ct-select',
							'value' => '',
							'view' => 'text',
							'placeholder' => __('Show products', 'blocksy'),
							'design' => 'block',
							'setting' => [
								'type' => 'option'
							],
							'desc' => __( 'Choose what to display on product category pages.', 'blocksy' ),
							'choices' => blocksy_ordered_keys(
								[
									'' => __('Show products', 'blocksy'),
									'subcategories' => __('Show subcategories', 'blocksy'),
									'both' => __('Show subcategories & products', 'blocksy'),
								]
							),
						],

						'woocommerce_default_catalog_orderby' => [
							'label' => __( 'Default product sorting', 'blocksy' ),
							'type' => 'ct-select',
							'value' => 'menu_order',
							'view' => 'text',
							'design' => 'block',
							'desc' => __( 'How should products be sorted in the catalog by default?', 'blocksy' ),
							'setting' => [
								'type' => 'option'
							],
							'choices' => blocksy_ordered_keys(
								apply_filters(
									'woocommerce_default_catalog_orderby_options',
									[
										'menu_order' => __('Default sorting (custom ordering + name)', 'blocksy'),
										'popularity' => __('Popularity (sales)', 'blocksy'),
										'rating' => __('Average rating', 'blocksy'),
										'date' => __('Sort by most recent', 'blocksy'),
										'price' => __('Sort by price (asc)', 'blocksy'),
										'price-desc' => __('Sort by price (desc)', 'blocksy'),
									]
								)
							),
						],

					],
				],

			],

		],
	],
];
