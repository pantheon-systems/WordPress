<?php

$pages = get_pages(
	[
		'post_type' => 'page',
		'post_status' => 'publish,private,draft',
		'number' => 0,
		'child_of' => 0,
		'parent' => -1,
		'exclude' => [
			wc_get_page_id('cart'),
			wc_get_page_id('checkout'),
			wc_get_page_id('myaccount'),
		],
		'sort_order' => 'asc',
		'sort_column' => 'post_title'
	]
);

$page_choices_result = [];

$page_choices = array(
	'' => __('No page set', 'woocommerce')
) + array_combine(
	array_map(
		'strval',
		wp_list_pluck($pages, 'ID')
	),
	wp_list_pluck($pages, 'post_title')
);

foreach ($page_choices as $page_id => $page_label) {
	$page_choices_result[strval($page_id)] = $page_label;
}

$options = [
	'woo_general_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			[
				blocksy_rand_md5() => [
					'label' => __('Messages', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [

						blocksy_rand_md5() => [
							'type' => 'ct-title',
							'label' => __( 'Info Messages', 'blocksy' ),
						],

						'info_message_text_color' => [
							'label' => __( 'Text Color', 'blocksy' ),
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
									'inherit' => 'var(--linkHoverColor)'
								],
							],
						],

						'info_message_background_color' => [
							'label' => __( 'Background Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => '#F0F1F3',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy' ),
									'id' => 'default',
								],
							],
						],

						'info_message_button_text_color' => [
							'label' => __( 'Button Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'sync' => 'live',
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
									'inherit' => 'var(--buttonTextInitialColor)',
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonTextHoverColor)',
								],
							],
						],

						'info_message_button_background' => [
							'label' => __( 'Button Background Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'sync' => 'live',
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

						blocksy_rand_md5() => [
							'type' => 'ct-title',
							'label' => __( 'Success Messages', 'blocksy' ),
						],

						'success_message_text_color' => [
							'label' => __( 'Text Color', 'blocksy' ),
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
									'inherit' => 'var(--linkHoverColor)'
								],
							],
						],

						'success_message_background_color' => [
							'label' => __( 'Background Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => '#F0F1F3',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy' ),
									'id' => 'default',
								],
							],
						],

						'success_message_button_text_color' => [
							'label' => __( 'Button Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'sync' => 'live',
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
									'inherit' => 'var(--buttonTextInitialColor)',
								],

								[
									'title' => __( 'Hover', 'blocksy' ),
									'id' => 'hover',
									'inherit' => 'var(--buttonTextHoverColor)',
								],
							],
						],

						'success_message_button_background' => [
							'label' => __( 'Button Background Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'sync' => 'live',
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

						blocksy_rand_md5() => [
							'type' => 'ct-title',
							'label' => __( 'Error Messages', 'blocksy' ),
						],

						'error_message_text_color' => [
							'label' => __( 'Text Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => '#ffffff',
								],

								'hover' => [
									'color' => '#ffffff',
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

						'error_message_background_color' => [
							'label' => __( 'Background Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => 'rgba(218, 0, 28, 0.7)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy' ),
									'id' => 'default',
								],
							],
						],

						'error_message_button_text_color' => [
							'label' => __( 'Button Font Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'sync' => 'live',
							'value' => [
								'default' => [
									'color' => '#ffffff',
								],

								'hover' => [
									'color' => '#ffffff',
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

						'error_message_button_background' => [
							'label' => __( 'Button Background Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'sync' => 'live',
							'value' => [
								'default' => [
									'color' => '#b92c3e',
								],

								'hover' => [
									'color' => '#9c2131',
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

					],
				],

				blocksy_rand_md5() => [
					'label' => __('Star Rating', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [

						'starRatingColor' => [
							'label' => __( 'Star Rating Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
								],

								'inactive' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
								],
							],

							'pickers' => [
								[
									'title' => __( 'Active', 'blocksy' ),
									'id' => 'default',
									'inherit' => '#FDA256'
								],

								[
									'title' => __( 'Inactive', 'blocksy' ),
									'id' => 'inactive',
									'inherit' => '#F9DFCC'
								],
							],
						],

					],
				],

				blocksy_rand_md5() => [
					'label' => __('Quantity Input', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [

						blocksy_rand_md5() => [
							'title' => __( 'General', 'blocksy' ),
							'type' => 'tab',
							'options' => [

								'has_custom_quantity' => [
									'label' => __( 'Custom Quantity Input', 'blocksy' ),
									'type' => 'ct-switch',
									'value' => 'yes',
									'sync' => blocksy_sync_whole_page([
										'prefix' => 'product',
										'loader_selector' => '.quantity'
									]),
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [ 'has_custom_quantity' => 'yes' ],
									'options' => [

										'quantity_type' => [
											// 'label' => __( 'Quantity Input Type', 'blocksy' ),
											'label' => false,
											'type' => 'ct-radio',
											'value' => 'type-2',
											'view' => 'text',
											'design' => 'block',
											// 'divider' => 'top',
											'disableRevertButton' => true,
											'setting' => [ 'transport' => 'postMessage' ],
											'choices' => [
												'type-1' => __( 'Type 1', 'blocksy' ),
												'type-2' => __( 'Type 2', 'blocksy' ),
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

								'global_quantity_color' => [
									'label' => __( 'Quantity Main Color', 'blocksy' ),
									'type'  => 'ct-color-picker',
									'design' => 'inline',
									'sync' => 'live',
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
											'inherit' => 'var(--buttonInitialColor)',

										],

										[
											'title' => __( 'Hover', 'blocksy' ),
											'id' => 'hover',
											'inherit' => 'var(--buttonHoverColor)'
										],
									],
								],

								'global_quantity_arrows' => [
									'label' => __( 'Quantity Arrows Color', 'blocksy' ),
									'type'  => 'ct-color-picker',
									'design' => 'inline',
									'sync' => 'live',
									'value' => [
										'default' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],

										'default_type_2' => [
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
											'inherit' => '#ffffff',
											'condition' => [ 'quantity_type' => 'type-1' ]
										],

										[
											'title' => __( 'Initial', 'blocksy' ),
											'id' => 'default_type_2',
											'condition' => [ 'quantity_type' => 'type-2' ]
										],

										[
											'title' => __( 'Hover', 'blocksy' ),
											'id' => 'hover',
											'inherit' => '#ffffff'
										],
									],
								],

							],
						],

					],
				],

				blocksy_rand_md5() => [
					'label' => __('Sale & Stock Badge', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [

						blocksy_rand_md5() => [
							'title' => __( 'General', 'blocksy' ),
							'type' => 'tab',
							'options' => [

								'sale_badge_shape' => [
									'label' => __( 'Badge Shape', 'blocksy' ),
									'type' => 'ct-image-picker',
									'value' => 'type-2',
									'attr' => [
										'data-type' => 'background',
										'data-columns' => '3',
									],
									'setting' => [ 'transport' => 'postMessage' ],
									'choices' => [

										'type-1' => [
											'src'   => blocksy_image_picker_file( 'badge-1' ),
											'title' => __( 'Type 1', 'blocksy' ),
										],

										'type-2' => [
											'src'   => blocksy_image_picker_file( 'badge-2' ),
											'title' => __( 'Type 2', 'blocksy' ),
										],

										'type-3' => [
											'src'   => blocksy_image_picker_file( 'badge-3' ),
											'title' => __( 'Type 3', 'blocksy' ),
										],
									],
								],

								'sale_badge_value' => [
									'type' => 'ct-radio',
									'label' => __( 'Sale Badge Value', 'blocksy' ),
									'value' => 'default',
									'view' => 'text',
									'design' => 'block',
									'divider' => 'top',
									'setting' => [ 'transport' => 'postMessage' ],
									'choices' => [
										'default' => __( 'Default', 'blocksy' ),
										'custom' => __( 'Custom', 'blocksy' ),
									],
									'sync' => blocksy_sync_whole_page([
										'prefix' => 'woo_categories',
										'loader_selector' => '.onsale'
									]),
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [ 'sale_badge_value' => 'default' ],
									'options' => [
										'sale_badge_default_value' => [
											'label' => false,
											'type' => 'text',
											'design' => 'block',
											'value' => 'SALE!',
											'disableRevertButton' => true,
											'sync' => blocksy_sync_whole_page([
												'prefix' => 'woo_categories',
												'loader_selector' => '.onsale'
											]),
										],
									],
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [ 'sale_badge_value' => 'custom' ],
									'options' => [
										'sale_badge_custom_value' => [
											'label' => false,
											'type' => 'text',
											'design' => 'block',
											'value' => '-[value]%',
											'disableRevertButton' => true,
											'sync' => blocksy_sync_whole_page([
												'prefix' => 'woo_categories',
												'loader_selector' => '.onsale'
											]),
										],
									],
								],

								'has_stock_badge' => [
									'label' => __( 'Show Stock Badge', 'blocksy' ),
									'type' => 'ct-checkboxes',
									'design' => 'block',
									'view' => 'text',
									'allow_empty' => true,
									'value' => [
										'archive' => true,
										'single' => true,
									],
									'divider' => 'top',
									'choices' => blocksy_ordered_keys([
										'archive' => __( 'Archive', 'blocksy' ),
										'single' => __( 'Single', 'blocksy' ),
									]),
									'sync' => blocksy_sync_whole_page([
										'prefix' => 'woo_categories',
										'loader_selector' => '.out-of-stock-badge'
									])
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [
										'any' => [
											'has_stock_badge/archive' => true,
											'has_stock_badge/single' => true,
										]
									],
									'options' => [
										'stock_badge_value' => [
											'label' => false,
											'type' => 'text',
											'design' => 'block',
											'value' => __('OUT OF STOCK', 'blocksy'),
											'disableRevertButton' => true,
											'sync' => blocksy_sync_whole_page([
												'prefix' => 'woo_categories',
												'loader_selector' => '.out-of-stock-badge'
											]),
										],
									],
								],

							],
						],

						blocksy_rand_md5() => [
							'title' => __( 'Design', 'blocksy' ),
							'type' => 'tab',
							'options' => [

								'saleBadgeColor' => [
									'label' => __( 'Sale Badge', 'blocksy' ),
									'type'  => 'ct-color-picker',
									'design' => 'inline',
									'setting' => [ 'transport' => 'postMessage' ],

									'value' => [
										'text' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],

										'background' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],
									],

									'pickers' => [
										[
											'title' => __( 'Text', 'blocksy' ),
											'id' => 'text',
											'inherit' => '#ffffff'
										],

										[
											'title' => __( 'Background', 'blocksy' ),
											'id' => 'background',
											'inherit' => 'var(--paletteColor1)'
										],
									],
								],

								'outOfStockBadgeColor' => [
									'label' => __( 'Out of Stock Badge', 'blocksy' ),
									'type'  => 'ct-color-picker',
									'design' => 'inline',
									'setting' => [ 'transport' => 'postMessage' ],

									'value' => [
										'text' => [
											'color' => '#ffffff',
										],

										'background' => [
											'color' => '#24292E',
										],
									],

									'pickers' => [
										[
											'title' => __( 'Text', 'blocksy' ),
											'id' => 'text',
										],

										[
											'title' => __( 'Background', 'blocksy' ),
											'id' => 'background',
										],
									],
								],

							],
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],

				blocksy_rand_md5() => [
					'label' => __('Account Page', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [

						blocksy_rand_md5() => [
							'title' => __( 'General', 'blocksy' ),
							'type' => 'tab',
							'options' => [

								'has_account_page_avatar' => [
									'label' => __( 'User Avatar', 'blocksy' ),
									'type' => 'ct-switch',
									'value' => 'no',
									'sync' => blocksy_sync_whole_page([
										'prefix' => 'single_page',
										'loader_selector' => '.ct-woo-account'
									]),
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => [ 'has_account_page_avatar' => 'yes' ],
									'options' => [

										'account_page_avatar_size' => [
											'label' => __( 'Avatar Size', 'blocksy' ),
											'type' => 'ct-number',
											'design' => 'inline',
											'value' => 35,
											'min' => 20,
											'max' => 100,
											'divider' => 'bottom',
											'setting' => [ 'transport' => 'postMessage' ],
										],
									],
								],

								'has_account_page_name' => [
									'label' => __( 'User Name', 'blocksy' ),
									'type' => 'ct-switch',
									'value' => 'no',
									'sync' => blocksy_sync_whole_page([
										'prefix' => 'single_page',
										'loader_selector' => '.ct-woo-account'
									]),
								],

								'has_account_page_quick_actions' => [
									'label' => __( 'Navigation Quick Links', 'blocksy' ),
									'type' => 'ct-switch',
									'value' => 'no',
									'sync' => blocksy_sync_whole_page([
										'prefix' => 'single_page',
										'loader_selector' => '.ct-woo-account'
									]),
								],
							],
						],

						blocksy_rand_md5() => [
							'title' => __( 'Design', 'blocksy' ),
							'type' => 'tab',
							'options' => [

								'account_nav_text_color' => [
									'label' => __( 'Navigation Text Color', 'blocksy' ),
									'type'  => 'ct-color-picker',
									'design' => 'inline',
									'setting' => [ 'transport' => 'postMessage' ],
									'value' => [
										'default' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],

										'active' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],
									],

									'pickers' => [
										[
											'title' => __( 'Initial', 'blocksy' ),
											'id' => 'default',
											'inherit' => 'var(--paletteColor3)'
										],

										[
											'title' => __( 'Active', 'blocksy' ),
											'id' => 'active',
											'inherit' => '#ffffff'
										],
									],
								],

								'account_nav_background_color' => [
									'label' => __( 'Navigation Background Color', 'blocksy' ),
									'type'  => 'ct-color-picker',
									'design' => 'inline',
									'setting' => [ 'transport' => 'postMessage' ],
									'value' => [
										'default' => [
											'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
										],

										'active' => [
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
											'title' => __( 'Active', 'blocksy' ),
											'id' => 'active',
											'inherit' => 'var(--paletteColor1)'
										],
									],
								],

								'account_nav_divider_color' => [
									'label' => __( 'Navigation Divider Color', 'blocksy' ),
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
											'inherit' => 'rgba(0, 0, 0, 0.05)'
										],
									],
								],

								'account_nav_shadow' => [
									'label' => __( 'Navigation Shadow', 'blocksy' ),
									'type' => 'ct-box-shadow',
									'design' => 'inline',
									'sync' => 'live',
									// 'responsive' => true,
									'divider' => 'top',
									'value' => blocksy_box_shadow_value([
										'enable' => false,
										'h_offset' => 0,
										'v_offset' => 10,
										'blur' => 20,
										'spread' => 0,
										'inset' => false,
										'color' => [
											'color' => 'rgba(0, 0, 0, 0.03)',
										],
									])
								],

							],
						],

					],
				],

				blocksy_rand_md5() => [
					'label' => __('Checkout Page', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [

						'blocksy_has_checkout_coupon' => [
							'label' => __( 'Coupon Form', 'blocksy' ),
							'type' => 'ct-switch',
							'value' => false,
							'divider' => 'bottom:full',
							'behavior' => 'bool',
							'setting' => [
							],
						],

						'woocommerce_checkout_highlight_required_fields' => [
							'label' => __('Highlight Required Fields', 'blocksy'),
							'type' => 'ct-switch',
							'value' => 'yes',
							'behavior' => 'bool',
							'divider' => 'bottom',
							'setting' => [
								'type' => 'option'
							],
						],

						'woocommerce_checkout_company_field' => [
							'label' => __( 'Company Name Field', 'blocksy' ),
							'type' => 'ct-select',
							'value' => 'optional',
							'view' => 'text',
							'design' => 'block',
							'choices' => blocksy_ordered_keys(
								[
									'hidden' => __( 'Hidden', 'blocksy' ),
									'optional' => __( 'Optional', 'blocksy' ),
									'required' => __( 'Required', 'blocksy' ),
								]
							),
							'setting' => [
								'type' => 'option'
							],
						],

						'woocommerce_checkout_address_2_field' => [
							'label' => __( 'Address Line 2 Field', 'blocksy' ),
							'type' => 'ct-select',
							'value' => 'optional',
							'view' => 'text',
							'design' => 'block',
							'choices' => blocksy_ordered_keys(
								[
									'hidden' => __( 'Hidden', 'blocksy' ),
									'optional' => __( 'Optional', 'blocksy' ),
									'required' => __( 'Required', 'blocksy' ),
								]
							),
							'setting' => [
								'type' => 'option'
							],
						],

						'woocommerce_checkout_phone_field' => [
							'label' => __( 'Phone Field', 'blocksy' ),
							'type' => 'ct-select',
							'value' => 'required',
							'view' => 'text',
							'design' => 'block',
							'choices' => blocksy_ordered_keys(
								[
									'hidden' => __( 'Hidden', 'blocksy' ),
									'optional' => __( 'Optional', 'blocksy' ),
									'required' => __( 'Required', 'blocksy' ),
								]
							),
							'setting' => [
								'type' => 'option'
							],
						],

						'wp_page_for_privacy_policy' => [
							'label' => __('Privacy Policy Page', 'blocksy'),
							'type' => 'ct-select',
							'value' => '',
							'view' => 'text',
							'design' => 'block',
							'divider' => 'top:full',
							'choices' => blocksy_ordered_keys($page_choices_result),
							'setting' => [
								'type' => 'option'
							],
						],

						'woocommerce_terms_page_id' => [
							'label' => __( 'Terms And Conditions Page', 'blocksy' ),
							'type' => 'ct-select',
							'value' => '',
							'view' => 'text',
							'design' => 'block',
							'choices' => blocksy_ordered_keys($page_choices_result),
							'setting' => [
								'type' => 'option'
							],
						],

						'woocommerce_checkout_privacy_policy_text' => [
							'label' => __( 'Privacy policy', 'blocksy' ),
							'desc' => __( 'Optionally add some text about your store privacy policy to show during checkout.', 'blocksy' ),
							'type' => 'wp-editor',
							'value' => __('Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our [privacy_policy].', 'blocksy'),
							'disableRevertButton' => true,
							'quicktags' => false,
							'mediaButtons' => false,
							'tinymce' => [
								'toolbar1' => 'bold,italic,link,alignleft,aligncenter,alignright,undo,redo',
							],
							'setting' => [
								'type' => 'option'
							],
						],

						'woocommerce_checkout_terms_and_conditions_checkbox_text' => [
							'label' => __( 'Terms and conditions', 'blocksy' ),
							'desc' => __( 'Optionally add some text for the terms checkbox that customers must accept.', 'blocksy' ),
							'type' => 'text',
							'value' => sprintf(
								__(
									'I have read and agree to the website %s',
									'woocommerce'
								),
								'[terms]'
							),
							'disableRevertButton' => true,
							'setting' => [
								'type' => 'option'
							],
						],
					],
				],

			],

			apply_filters(
				'blocksy_customizer_options:woocommerce:general:end',
				[]
			),

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'woocommerce_demo_store' => [
				'label' => __('Store Notice', 'blocksy'),
				'type' => 'ct-panel',
				'switch' => true,
				'value' => 'no',
				'switchBehavior' => 'boolean',
				'setting' => [
					'type' => 'option',
				],
				'inner-options' => [

					blocksy_rand_md5() => [
						'title' => __( 'General', 'blocksy' ),
						'type' => 'tab',
						'options' => [

							'woocommerce_demo_store_notice' => [
								'label' => false,
								'type' => 'textarea',
								'value' => __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'blocksy' ),
								'setting' => [
									'type' => 'option',
									'transport' => 'postMessage'
								],
								'disableRevertButton' => true,
							],

							'store_notice_position' => [
								'type' => 'ct-radio',
								'label' => __( 'Notice Position', 'blocksy' ),
								'value' => 'bottom',
								'view' => 'text',
								// 'disableRevertButton' => true,
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [
									'top' => __('Top', 'blocksy'),
									'bottom' => __('Bottom', 'blocksy'),
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'title' => __( 'Design', 'blocksy' ),
						'type' => 'tab',
						'options' => [

							'wooNoticeContent' => [
								'label' => __( 'Notice Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'divider' => 'top',
								'skipEditPalette' => true,
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => '#ffffff',
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
									],
								],
							],

							'wooNoticeBackground' => [
								'label' => __( 'Notice Background Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'skipEditPalette' => true,
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => 'var(--paletteColor1)',
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy' ),
										'id' => 'default',
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
