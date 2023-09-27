<?php

$link_options = [
	'profile' => __( 'Profile Page', 'blocksy-companion' ),
	'dashboard' => __( 'Dashboard Page', 'blocksy-companion' ),
	// 'menu' => __( 'Menu', 'blocksy-companion' ),
	'custom' => __( 'Custom Link', 'blocksy-companion' ),
	'logout' => __( 'Logout', 'blocksy-companion' ),
];

$logout_link_options = [
	'modal' => __( 'Modal', 'blocksy-companion' ),
	'custom' => __( 'Custom Link', 'blocksy-companion' ),
];

if (class_exists('WooCommerce')) {
	$link_options['woocommerce_account'] = __('WooCommerce Account', 'blocksy-companion');
	$logout_link_options['woocommerce_account'] = __('WooCommerce Account', 'blocksy-companion');
}

$is_pro = function_exists('blc_fs') && blc_fs()->can_use_premium_code();

$options = [
	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => ['account_state' => 'in'],
		'options' => [
			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __('Customizing: Logged in State', 'blocksy-companion'),
			],
		]
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => ['account_state' => 'out'],
		'options' => [
			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __('Customizing: Logged out State', 'blocksy-companion'),
			],
		]
	],

	'account_state' => [
		'label' => false,
		'type' => 'ct-image-picker',
		'value' => 'in',
		'attr' => [ 'data-type' => 'background' ],
		'switchDeviceOnChange' => 'desktop',
		'choices' => [
			'in' => [
				'src'   => blocksy_image_picker_url( 'log-in-state.svg' ),
				'title' => __( 'Logged In Options', 'blocksy-companion' ),
			],

			'out' => [
				'src' => blocksy_image_picker_url('log-out-state.svg'),
				'title' => __('Logged Out Options', 'blocksy-companion'),
			],
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-divider',
	],

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy-companion' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'account_state' => 'in' ],
				'options' => [

					'account_link' => [
						'label' => __( 'Account Action', 'blocksy-companion' ),
						'type' => 'ct-select',
						'value' => 'profile',
						'view' => 'text',
						'design' => 'inline',
						'choices' => blocksy_ordered_keys($link_options)
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'account_link' => 'menu' ],
						'options' => [
							'loggedin_account_menu' => [
								'label' => __('Select Menu', 'blocksy-companion'),
								'type' => 'ct-select',
								'value' => 'blocksy_location',
								'view' => 'text',
								'design' => 'inline',
								'setting' => ['transport' => 'postMessage'],
								'placeholder' => __('Select menu...', 'blocksy-companion'),
								'choices' => blocksy_ordered_keys(blocksy_get_menus_items()),
								'desc' => sprintf(
									// translators: placeholder here means the actual URL.
									__( 'Manage your menu items in the %sMenus screen%s.', 'blocksy-companion' ),
									sprintf(
										'<a href="%s" target="_blank">',
										admin_url('/nav-menus.php')
									),
									'</a>'
								),
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'account_link' => 'custom' ],
						'options' => [

							'account_custom_page' => [
								'label' => __( 'Custom Page Link', 'blocksy-companion' ),
								'type' => 'text',
								'design' => 'inline',
								'disableRevertButton' => true,
								'value' => ''
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'loggedin_media' => [
						'label' => __( 'Account Image', 'blocksy-companion' ),
						'type' => 'ct-radio',
						'design' => 'block',
						'view' => 'text',
						'value' => 'avatar',
						'choices' => [
							'avatar' => __( 'Avatar', 'blocksy-companion' ),
							'icon' => __( 'Icon', 'blocksy-companion' ),
							'none' => __( 'None', 'blocksy-companion' ),
						],
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'loggedin_media' => 'avatar' ],
						'options' => [

							'accountHeaderAvatarSize' => [
								'label' => __( 'Avatar Size', 'blocksy-companion' ),
								'type' => 'ct-slider',
								'min' => 10,
								'max' => 40,
								'value' => 18,
								'responsive' => true,
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'loggedin_media' => 'icon' ],
						'options' => [
							$is_pro ? [
								'loggedin_icon_source' => [
									'label' => __( 'Icon Source', 'blocksy' ),
									'type' => 'ct-radio',
									'value' => 'default',
									'view' => 'text',
									'design' => 'block',
									'divider' => 'top',
									'setting' => [ 'transport' => 'postMessage' ],
									'choices' => [
										'default' => __( 'Default', 'blocksy' ),
										'custom' => __( 'Custom', 'blocksy' ),
									],
								],

								blocksy_rand_md5() => [
									'type' => 'ct-condition',
									'condition' => ['loggedin_icon_source' => 'custom'],
									'options' => [
										'loggedin_custom_icon' => [
											'type' => 'icon-picker',
											'label' => __('Icon', 'blocksy-companion'),
											'design' => 'inline',
											'divider' => 'top',
											'value' => [
												'icon' => 'blc blc-user'
											]
										]
									]
								],
							] : [],

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => $is_pro ? [
									'loggedin_icon_source' => 'default'
								] : [
									'loggedin_icon_source' => '! not_existing'
								],
								'options' => [
									'account_loggedin_icon' => [
										'label' => false,
										'type' => 'ct-image-picker',
										'value' => 'type-1',
										'attr' => [
											'data-type' => 'background',
											'data-columns' => '3',
										],
										'divider' => 'top',
										'setting' => [ 'transport' => 'postMessage' ],
										'choices' => [

											'type-1' => [
												'src'   => blocksy_image_picker_file( 'account-1' ),
												'title' => __( 'Type 1', 'blocksy-companion' ),
											],

											'type-2' => [
												'src'   => blocksy_image_picker_file( 'account-2' ),
												'title' => __( 'Type 2', 'blocksy-companion' ),
											],

											'type-3' => [
												'src'   => blocksy_image_picker_file( 'account-3' ),
												'title' => __( 'Type 3', 'blocksy-companion' ),
											],

											'type-4' => [
												'src'   => blocksy_image_picker_file( 'account-4' ),
												'title' => __( 'Type 4', 'blocksy-companion' ),
											],

											'type-5' => [
												'src'   => blocksy_image_picker_file( 'account-5' ),
												'title' => __( 'Type 5', 'blocksy-companion' ),
											],

											'type-6' => [
												'src'   => blocksy_image_picker_file( 'account-6' ),
												'title' => __( 'Type 6', 'blocksy-companion' ),
											],
										],
									],
								]
							],

							'account_loggedin_icon_size' => [
								'label' => __( 'Icon Size', 'blocksy-companion' ),
								'type' => 'ct-slider',
								'min' => 5,
								'max' => 50,
								'value' => 15,
								'responsive' => true,
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
							],
						]
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'loggedin_account_label_visibility' => [
						'label' => __( 'Label Visibility', 'blocksy-companion' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'allow_empty' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'desktop' => false,
							'tablet' => false,
							'mobile' => false,
						],

						'choices' => blocksy_ordered_keys([
							'desktop' => __( 'Desktop', 'blocksy-companion' ),
							'tablet' => __( 'Tablet', 'blocksy-companion' ),
							'mobile' => __( 'Mobile', 'blocksy-companion' ),
						]),
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							'any' => [
								'loggedin_account_label_visibility/desktop' => true,
								'loggedin_account_label_visibility/tablet' => true,
								'loggedin_account_label_visibility/mobile' => true,
							]
						],
						'options' => [

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => [ 'loggedin_media' => '!none' ],
								'options' => [

									'loggedin_label_position' => [
										'type' => 'ct-radio',
										'label' => __( 'Label Position', 'blocksy-companion' ),
										'value' => 'left',
										'view' => 'text',
										'design' => 'block',
										'divider' => 'top',
										'responsive' => [ 'tablet' => 'skip' ],
										'choices' => [
											'left' => __( 'Left', 'blocksy-companion' ),
											'right' => __( 'Right', 'blocksy-companion' ),
											'bottom' => __( 'Bottom', 'blocksy-companion' ),
										],
									],

								],
							],

							'loggedin_text' => [
								'label' => __('Label Type', 'blocksy-companion'),
								'type' => 'ct-radio',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'setting' => ['transport' => 'postMessage'],
								'value' => 'label',
								'choices' => [
									'label' => __('Text', 'blocksy-companion'),
									'username' => __('Name', 'blocksy-companion'),
								],
							],

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => ['loggedin_text' => 'label'],
								'options' => [

									'loggedin_label' => [
										'label' => __('Label Text', 'blocksy-companion'),
										'type' => 'text',
										'design' => 'block',
										'divider' => 'top',
										'setting' => ['transport' => 'postMessage'],
										'value' => __('My Account', 'blocksy-companion')
									],

								],
							],

						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'account_state' => 'out' ],
				'options' => [

					'login_account_action' => [
						'label' => __( 'Account Action', 'blocksy-companion' ),
						'type' => 'ct-select',
						'value' => 'modal',
						'view' => 'text',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => blocksy_ordered_keys($logout_link_options)
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'login_account_action' => 'custom' ],
						'options' => [

							'loggedout_account_custom_page' => [
								'label' => __( 'Custom Page Link', 'blocksy-companion' ),
								'type' => 'text',
								'design' => 'inline',
								'disableRevertButton' => true,
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => ''
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'logged_out_style' => [
						'label' => __( 'Account Image', 'blocksy-companion' ),
						'type' => 'ct-radio',
						'design' => 'block',
						'view' => 'text',
						'value' => 'icon',
						'choices' => [
							'icon' => __( 'Icon', 'blocksy-companion' ),
							'none' => __( 'None', 'blocksy-companion' ),
						],
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'logged_out_style' => 'icon' ],
						'options' => $is_pro ? [

							'logged_out_icon_source' => [
								'label' => __( 'Icon Source', 'blocksy' ),
								'type' => 'ct-radio',
								'value' => 'default',
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [
									'default' => __( 'Default', 'blocksy' ),
									'custom' => __( 'Custom', 'blocksy' ),
								],
							],

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => ['logged_out_icon_source' => 'custom'],
								'options' => [
									'logged_out_custom_icon' => [
										'type' => 'icon-picker',
										'label' => __('Icon', 'blocksy-companion'),
										'design' => 'inline',
										'divider' => 'top',
										'value' => [
											'icon' => 'blc blc-user'
										]
									]
								]
							],

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => ['logged_out_icon_source' => 'default'],
								'options' => [
									'accountHeaderIcon' => [
										'label' => false,
										'type' => 'ct-image-picker',
										'value' => 'type-1',
										'attr' => [
											'data-type' => 'background',
											'data-columns' => '3',
										],
										'divider' => 'top',
										'setting' => [ 'transport' => 'postMessage' ],
										'choices' => [

											'type-1' => [
												'src'   => blocksy_image_picker_file( 'account-1' ),
												'title' => __( 'Type 1', 'blocksy-companion' ),
											],

											'type-2' => [
												'src'   => blocksy_image_picker_file( 'account-2' ),
												'title' => __( 'Type 2', 'blocksy-companion' ),
											],

											'type-3' => [
												'src'   => blocksy_image_picker_file( 'account-3' ),
												'title' => __( 'Type 3', 'blocksy-companion' ),
											],

											'type-4' => [
												'src'   => blocksy_image_picker_file( 'account-4' ),
												'title' => __( 'Type 4', 'blocksy-companion' ),
											],

											'type-5' => [
												'src'   => blocksy_image_picker_file( 'account-5' ),
												'title' => __( 'Type 5', 'blocksy-companion' ),
											],

											'type-6' => [
												'src'   => blocksy_image_picker_file( 'account-6' ),
												'title' => __( 'Type 6', 'blocksy-companion' ),
											],
										],
									],
								]
							],

							'accountHeaderIconSize' => [
								'label' => __( 'Icon Size', 'blocksy-companion' ),
								'type' => 'ct-slider',
								'min' => 5,
								'max' => 50,
								'value' => 15,
								'responsive' => true,
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
							],

						] : [
							'accountHeaderIcon' => [
								'label' => false,
								'type' => 'ct-image-picker',
								'value' => 'type-1',
								'attr' => [
									'data-type' => 'background',
									'data-columns' => '3',
								],
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [

									'type-1' => [
										'src'   => blocksy_image_picker_file( 'account-1' ),
										'title' => __( 'Type 1', 'blocksy-companion' ),
									],

									'type-2' => [
										'src'   => blocksy_image_picker_file( 'account-2' ),
										'title' => __( 'Type 2', 'blocksy-companion' ),
									],

									'type-3' => [
										'src'   => blocksy_image_picker_file( 'account-3' ),
										'title' => __( 'Type 3', 'blocksy-companion' ),
									],

									'type-4' => [
										'src'   => blocksy_image_picker_file( 'account-4' ),
										'title' => __( 'Type 4', 'blocksy-companion' ),
									],

									'type-5' => [
										'src'   => blocksy_image_picker_file( 'account-5' ),
										'title' => __( 'Type 5', 'blocksy-companion' ),
									],

									'type-6' => [
										'src'   => blocksy_image_picker_file( 'account-6' ),
										'title' => __( 'Type 6', 'blocksy-companion' ),
									],
								],
							],

							'accountHeaderIconSize' => [
								'label' => __( 'Icon Size', 'blocksy-companion' ),
								'type' => 'ct-slider',
								'min' => 5,
								'max' => 50,
								'value' => 15,
								'responsive' => true,
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'loggedout_account_label_visibility' => [
						'label' => __( 'Label Visibility', 'blocksy-companion' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'allow_empty' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'desktop' => false,
							'tablet' => false,
							'mobile' => false,
						],

						'choices' => blocksy_ordered_keys([
							'desktop' => __( 'Desktop', 'blocksy-companion' ),
							'tablet' => __( 'Tablet', 'blocksy-companion' ),
							'mobile' => __( 'Mobile', 'blocksy-companion' ),
						]),
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							'any' => [
								'loggedout_account_label_visibility/desktop' => true,
								'loggedout_account_label_visibility/tablet' => true,
								'loggedout_account_label_visibility/mobile' => true,
							]
						],
						'options' => [

							blocksy_rand_md5() => [
								'type' => 'ct-condition',
								'condition' => [ 'logged_out_style' => 'icon' ],
								'options' => [

									'loggedout_label_position' => [
										'type' => 'ct-radio',
										'label' => __( 'Label Position', 'blocksy-companion' ),
										'value' => 'left',
										'view' => 'text',
										'design' => 'block',
										'divider' => 'top',
										'responsive' => [ 'tablet' => 'skip' ],
										'choices' => [
											'left' => __( 'Left', 'blocksy-companion' ),
											'right' => __( 'Right', 'blocksy-companion' ),
											'bottom' => __( 'Bottom', 'blocksy-companion' ),
										],
									],

								],
							],

							'login_label' => [
								'label' => __('Label Text', 'blocksy-companion'),
								'type' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'disableRevertButton' => true,
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => __('Login', 'blocksy-companion')
							],

						],
					],

				],
			],

			'account_user_visibility' => [
				'label' => __( 'User Visibility', 'blocksy-companion' ),
				'type' => 'ct-checkboxes',
				'design' => 'block',
				'view' => 'text',
				'divider' => 'top:full',
				'allow_empty' => true,
				'value' => [
					'logged_in' => true,
					'logged_out' => true,
				],
				'choices' => blocksy_ordered_keys([
					'logged_in' => __( 'Logged In', 'blocksy-companion' ),
					'logged_out' => __( 'Logged Out', 'blocksy-companion' ),
				]),
			],

		],
	],




	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy-companion' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'loggedin_account_label_visibility/desktop' => true,
						'loggedin_account_label_visibility/tablet' => true,
						'loggedin_account_label_visibility/mobile' => true,
						'loggedout_account_label_visibility/desktop' => true,
						'loggedout_account_label_visibility/tablet' => true,
						'loggedout_account_label_visibility/mobile' => true,
					]
				],
				'options' => [

					'account_label_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Label Font', 'blocksy-companion' ),
						'value' => blocksy_typography_default_values([
							'size' => '12px',
							'variation' => 'n6',
							'text-transform' => 'uppercase',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Label Color', 'blocksy-companion' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'accountHeaderColor',
								'label' => __('Default State', 'blocksy-companion')
							],

							[
								'id' => 'transparentAccountHeaderColor',
								'label' => __('Transparent State', 'blocksy-companion'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'stickyAccountHeaderColor',
								'label' => __('Sticky State', 'blocksy-companion'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [

							'accountHeaderColor' => [
								'label' => __( 'Label Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
										'inherit' => 'var(--color)'
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
										'inherit' => 'var(--linkHoverColor)'
									],
								],
							],

							'transparentAccountHeaderColor' => [
								'label' => __( 'Label Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
									],
								],
							],

							'stickyAccountHeaderColor' => [
								'label' => __( 'Label Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'all' => [
							'account_state' => 'in',
							'loggedin_media' => 'icon'
						],

						'all~' => [
							'account_state' => 'out',
							'logged_out_style' => 'icon'
						]
					]
				],
				'options' => [
					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Icon Color', 'blocksy-companion' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'header_account_icon_color',
								'label' => __('Default State', 'blocksy-companion'),
							],

							[
								'id' => 'transparent_header_account_icon_color',
								'label' => __('Transparent State', 'blocksy-companion'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'sticky_header_account_icon_color',
								'label' => __('Sticky State', 'blocksy-companion'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [
							'header_account_icon_color' => [
								'label' => __( 'Icon Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
										'inherit' => 'var(--color)',
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
										'inherit' => 'var(--paletteColor2)',
									],
								],
							],

							'transparent_header_account_icon_color' => [
								'label' => __( 'Icon Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
									],
								],
							],

							'sticky_header_account_icon_color' => [
								'label' => __( 'Icon Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
									],
								],
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],
				]
			],

			'accountHeaderMargin' => [
				'label' => __( 'Item Margin', 'blocksy-companion' ),
				'type' => 'ct-spacing',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'account_state' => 'out',
					'login_account_action' => 'modal'
				],
				'options' => [

					blocksy_rand_md5() => [
						'type' => 'ct-title',
						'label' => __( 'Modal Options', 'blocksy-companion' ),
					],

					'account_modal_font_color' => [
						'label' => __( 'Font Color', 'blocksy-companion' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'divider' => 'bottom',
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
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id' => 'default',
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Hover', 'blocksy-companion' ),
								'id' => 'hover',
								'inherit' => 'var(--linkHoverColor)'
							],
						],
					],

					'account_modal_form_text_color' => [
						'label' => __( 'Input Font Color', 'blocksy-companion' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'focus' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id' => 'default',
								'inherit' => 'var(--form-text-initial-color, var(--color))'
							],

							[
								'title' => __( 'Focus', 'blocksy-companion' ),
								'id' => 'focus',
								'inherit' => 'var(--form-text-focus-color, var(--color))'
							],
						],
					],

					'account_modal_form_border_color' => [
						'label' => __( 'Input Border Color', 'blocksy-companion' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'focus' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id' => 'default',
								'inherit' => 'var(--form-field-border-initial-color)'
							],

							[
								'title' => __( 'Focus', 'blocksy-companion' ),
								'id' => 'focus',
								'inherit' => 'var(--form-field-border-focus-color)'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => ['forms_type' => 'classic-forms'],
						'values_source' => 'global',
						'options' => [

							'account_modal_form_background_color' => [
								'label' => __( 'Input Background Color', 'blocksy-companion' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => [
									'default' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],

									'focus' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],
								],

								'pickers' => [
									[
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
										'inherit' => 'var(--form-field-initial-background)'
									],

									[
										'title' => __( 'Focus', 'blocksy-companion' ),
										'id' => 'focus',
										'inherit' => 'var(--form-field-focus-background)'
									],
								],
							],

						],
					],

					'accountHeaderFormBackground' => [
						'label' => __( 'Modal Background', 'blocksy-companion' ),
						'type'  => 'ct-background',
						'design' => 'inline',
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

					'accountHeaderBackground' => [
						'label' => __( 'Modal Backdrop', 'blocksy-companion' ),
						'type'  => 'ct-background',
						'design' => 'inline',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => blocksy_background_default_value([
							'backgroundColor' => [
								'default' => [
									'color' => 'rgba(18, 21, 25, 0.6)'
								],
							],
						])
					],

					'account_form_shadow' => [
						'label' => __( 'Modal Shadow', 'blocksy-companion' ),
						'type' => 'ct-box-shadow',
						'design' => 'inline',
						// 'responsive' => true,
						'divider' => 'top',
						'value' => blocksy_box_shadow_value([
							'enable' => true,
							'h_offset' => 0,
							'v_offset' => 0,
							'blur' => 70,
							'spread' => 0,
							'inset' => false,
							'color' => [
								'color' => 'rgba(0, 0, 0, 0.35)',
							],
						])
					],

					'account_close_button_type' => [
						'label' => __('Close Button Type', 'blocksy-companion'),
						'type' => 'ct-select',
						'value' => 'type-1',
						'view' => 'text',
						'design' => 'inline',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => blocksy_ordered_keys(
							[
								'type-1' => __( 'Simple', 'blocksy-companion' ),
								'type-2' => __( 'Border', 'blocksy-companion' ),
								'type-3' => __( 'Background', 'blocksy-companion' ),
							]
						),
					],

					'account_close_button_color' => [
						'label' => __( 'Icon Color', 'blocksy-companion' ),
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
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id' => 'default',
								'inherit' => 'rgba(255, 255, 255, 0.7)'
							],

							[
								'title' => __( 'Hover', 'blocksy-companion' ),
								'id' => 'hover',
								'inherit' => '#ffffff'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'account_close_button_type' => 'type-2' ],
						'options' => [

							'account_close_button_border_color' => [
								'label' => __( 'Border Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
										'inherit' => 'rgba(0, 0, 0, 0.5)'
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
										'inherit' => 'rgba(0, 0, 0, 0.5)'
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'account_close_button_type' => 'type-3' ],
						'options' => [

							'account_close_button_shape_color' => [
								'label' => __( 'Background Color', 'blocksy-companion' ),
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
										'title' => __( 'Initial', 'blocksy-companion' ),
										'id' => 'default',
										'inherit' => 'rgba(0, 0, 0, 0.5)'
									],

									[
										'title' => __( 'Hover', 'blocksy-companion' ),
										'id' => 'hover',
										'inherit' => 'rgba(0, 0, 0, 0.5)'
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-spacer',
						'height' => 50
					],

				],
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'wp_customizer_current_view' => 'tablet|mobile' ],
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'header_account_visibility' => [
				'label' => __( 'Element Visibility', 'blocksy-companion' ),
				'type' => 'ct-visibility',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'allow_empty' => true,
				'value' => [
					'tablet' => true,
					'mobile' => true,
				],

				'choices' => blocksy_ordered_keys([
					'tablet' => __( 'Tablet', 'blocksy-companion' ),
					'mobile' => __( 'Mobile', 'blocksy-companion' ),
				]),
			],

		],
	],
];
