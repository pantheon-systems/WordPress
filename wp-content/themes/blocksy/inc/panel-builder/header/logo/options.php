<?php

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => array_merge([

			'custom_logo' => [
				'label' => __( 'Logo', 'blocksy' ),
				'type' => 'ct-image-uploader',
				'value' => get_theme_mod('custom_logo', ''),
				'inline_value' => true,
				'responsive' => [
					'tablet' => 'skip'
				],
				'attr' => [ 'data-type' => 'small' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'builderSettings/has_transparent_header' => 'yes',
				],
				'options' => [
					'transparent_logo' => [
						'label' => __( 'Transparent State Logo', 'blocksy' ),
						'type' => 'ct-image-uploader',
						'value' => '',
						'inline_value' => true,
						'responsive' => [
							'tablet' => 'skip'
						],
						'divider' => 'top',
						'attr' => [ 'data-type' => 'small' ],
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'builderSettings/has_sticky_header' => 'yes',
				],
				'options' => [
					'sticky_logo' => [
						'label' => __( 'Sticky State Logo', 'blocksy' ),
						'type' => 'ct-image-uploader',
						'value' => '',
						'inline_value' => true,
						'responsive' => [
							'tablet' => 'skip'
						],
						'divider' => 'top',
						'attr' => [ 'data-type' => 'small' ],
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'logoMaxHeight' => [
				'label' => __( 'Logo Height', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 0,
				'max' => 300,
				'value' => 50,
				'responsive' => true,
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'builderSettings/has_sticky_header' => 'yes',
					'row' => 'middle-row'
				],
				'options' => [
					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'has_sticky_logo_shrink' => [
						'label' => __( 'Sticky State Shrink', 'blocksy' ),
						'type' => 'ct-switch',
						'type' => 'ct-switch',
						'value' => 'no',
						'sync' => [
							'id' => 'header_placements_1'
						]
					],
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'builderSettings/has_sticky_header' => 'yes',
					'row' => 'middle-row',
					'has_sticky_logo_shrink' => 'yes'
				],
				'options' => [

					'sticky_logo_shrink' => [
						'label' => __( 'Logo Height', 'blocksy' ),
						'type' => 'ct-slider',
						'min' => 30,
						'max' => 100,
						'responsive' => true,
						'value' => 70,
						'defaultUnit' => '%',
						'sync' => [
							'id' => 'header_placements_1'
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'has_site_title' => [
				'label' => __( 'Site Title', 'blocksy' ),
				'type' => 'ct-switch',
				'value' => 'yes',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'has_site_title' => 'yes' ],
				'options' => [

					'blogname' => [
						'label' => false,
						'type' => 'text',
						'design' => 'block',
						'disableRevertButton' => true,
						'value' => get_option('blogname'),
					],

					'blogname_visibility' => [
						'label' => __( 'Site Title Visibility', 'blocksy' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'allow_empty' => true,
						'sync' => 'live',
						// 'view' => 'modal',

						'value' => [
							'desktop' => true,
							'tablet' => true,
							'mobile' => true,
						],

						'choices' => blocksy_ordered_keys([
							'desktop' => __( 'Desktop', 'blocksy' ),
							'tablet' => __( 'Tablet', 'blocksy' ),
							'mobile' => __( 'Mobile', 'blocksy' ),
						]),
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'has_tagline' => [
				'label' => __( 'Site Tagline', 'blocksy' ),
				'type' => 'ct-switch',
				'value' => 'no',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'has_tagline' => 'yes' ],
				'options' => [

					'blogdescription' => [
						'label' => false,
						'type' => 'text',
						'design' => 'block',
						'disableRevertButton' => true,
						'value' => get_option( 'blogdescription' ),
					],

					'blogdescription_visibility' => [
						'label' => __( 'Site Tagline Visibility', 'blocksy' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'allow_empty' => true,
						'sync' => 'live',

						'value' => [
							'desktop' => true,
							'tablet' => true,
							'mobile' => true,
						],

						'choices' => blocksy_ordered_keys([
							'desktop' => __( 'Desktop', 'blocksy' ),
							'tablet' => __( 'Tablet', 'blocksy' ),
							'mobile' => __( 'Mobile', 'blocksy' ),
						]),
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'custom_logo:truthy' => 'yes',
				],
				'options' => [
					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [
							'any' => [
								'has_site_title' => 'yes',
								'has_tagline' => 'yes',
							]
						],
						'options' => [

							blocksy_rand_md5() => [
								'type' => 'ct-divider',
							],

							'logo_position' => [
								'label' => __( 'Logo Image Position', 'blocksy' ),
								'type' => 'ct-radio',
								'value' => 'top',
								'view' => 'text',
								'design' => 'block',
								'responsive' => [
									'tablet' => 'skip'
								],
								'choices' => [
									'left' => __( 'Left', 'blocksy' ),
									'right' => __( 'Right', 'blocksy' ),
									'top' => __( 'Top', 'blocksy' ),
								],
							],

						],
					],

				],
			],
		], $panel_type === 'header' ? [
			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'header_logo_horizontal_alignment' => [
				'type' => 'ct-radio',
				'label' => __( 'Content Alignment', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'responsive' => true,
				'attr' => [ 'data-type' => 'alignment' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'CT_CSS_SKIP_RULE',
				'choices' => [
					'left' => '',
					'center' => '',
					'right' => '',
				],
			],
		] : [],

		$panel_type === 'footer' ? [
			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'footer_logo_horizontal_alignment' => [
				'type' => 'ct-radio',
				'label' => __( 'Horizontal Alignment', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
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

			'footer_logo_vertical_alignment' => [
				'type' => 'ct-radio',
				'label' => __( 'Vertical Alignment', 'blocksy' ),
				'view' => 'text',
				'design' => 'block',
				'divider' => 'top',
				'responsive' => true,
				'attr' => [ 'data-type' => 'vertical-alignment' ],
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => 'CT_CSS_SKIP_RULE',
				'choices' => [
					'flex-start' => '',
					'center' => '',
					'flex-end' => '',
				],
			],

			'visibility' => [
				'label' => __('Element Visibility', 'blocksy'),
				'type' => 'ct-visibility',
				'design' => 'block',
				'divider' => 'top',
				'sync' => 'live',
				'value' => [
					'desktop' => true,
					'tablet' => true,
					'mobile' => true,
				],
				'choices' => blocksy_ordered_keys([
					'desktop' => __( 'Desktop', 'blocksy' ),
					'tablet' => __( 'Tablet', 'blocksy' ),
					'mobile' => __( 'Mobile', 'blocksy' ),
				]),
			],
		] : []),
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'has_site_title' => 'yes' ],
				'options' => [

					'siteTitle' => [
						'type' => 'ct-typography',
						'label' => __( 'Site Title', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '25px',
							'variation' => 'n7',
							'line-height' => '1.5'
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Site Title Color', 'blocksy' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'siteTitleColor',
								'label' => __('Default State', 'blocksy')
							],

							[
								'id' => 'transparentSiteTitleColor',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'stickySiteTitleColor',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [

							'siteTitleColor' => [
								'label' => __( 'Site Title Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'setting' => [ 'transport' => 'postMessage' ],

								'value' => [
									'default' => [
										'color' => 'var(--paletteColor4)',
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

							'transparentSiteTitleColor' => [
								'label' => __( 'Site Title Color', 'blocksy' ),
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
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
									],
								],
							],

							'stickySiteTitleColor' => [
								'label' => __( 'Site Title Color', 'blocksy' ),
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
						'type' => 'ct-divider',
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'has_tagline' => 'yes' ],
				'options' => [

					'siteTagline' => [
						'type' => 'ct-typography',
						'label' => __( 'Site Tagline Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '13px',
							'variation' => 'n5',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Site Tagline Color', 'blocksy' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'siteTaglineColor',
								'label' => __('Default State', 'blocksy')
							],

							[
								'id' => 'transparentSiteTaglineColor',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'stickySiteTaglineColor',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [

							'siteTaglineColor' => [
								'label' => __( 'Site Tagline Color', 'blocksy' ),
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

							'transparentSiteTaglineColor' => [
								'label' => __( 'Site Tagline Color', 'blocksy' ),
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
									],
								],
							],

							'stickySiteTaglineColor' => [
								'label' => __( 'Site Tagline Color', 'blocksy' ),
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

			'headerLogoMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true
			],

		],
	],
];
