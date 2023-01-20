<?php

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'header_socials' => [
				'label' => false,
				'type' => 'ct-layers',
				'manageable' => true,
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					[
						'id' => 'facebook',
						'enabled' => true,
					],

					[
						'id' => 'twitter',
						'enabled' => true,
					],

					[
						'id' => 'instagram',
						'enabled' => true,
					],
				],

				'settings' => apply_filters(
					'blocksy:header:socials:options:icon', 
					blocksy_get_social_networks_list()
				),
				'desc' => sprintf(
					// translators: placeholder here means the actual URL.
					__( 'Configure the social links in General ➝ %sSocial Network Accounts%s.', 'blocksy' ),
					sprintf(
						'<a data-trigger-section="general:social_section_options" href="%s">',
						admin_url('/customize.php?autofocus[section]=general&ct_autofocus=general:social_section_options')
					),
					'</a>'
				),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'link_target' => [
				'type'  => 'ct-switch',
				'label' => __( 'Open links in new tab', 'blocksy' ),
				'value' => 'no',
			],

			'link_nofollow' => [
				'type'  => 'ct-switch',
				'label' => __( 'Set links to nofollow', 'blocksy' ),
				'value' => 'no',
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'socialsIconSize' => [
				'label' => __( 'Icons Size', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 5,
				'max' => 50,
				'value' => 15,
				'responsive' => true,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'socialsIconSpacing' => [
				'label' => __( 'Icons Spacing', 'blocksy' ),
				'type' => 'ct-slider',
				'min' => 0,
				'max' => 50,
				'value' => 15,
				'responsive' => true,
				'divider' => 'bottom',
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'headerSocialsColor' => [
				'label' => __('Icons Color', 'blocksy'),
				'type' => 'ct-radio',
				'value' => 'custom',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'custom' => __( 'Custom', 'blocksy' ),
					'official' => __( 'Official', 'blocksy' ),
				],
			],

			'socialsType' => [
				'label' => __('Icons Shape Type', 'blocksy'),
				'type' => 'ct-radio',
				'value' => 'simple',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'simple' => __( 'None', 'blocksy' ),
					'rounded' => __( 'Rounded', 'blocksy' ),
					'square' => __( 'Square', 'blocksy' ),
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'socialsType' => '!simple' ],
				'options' => [

					'socialsFillType' => [
						'label' => __('Shape Fill Type', 'blocksy'),
						'type' => 'ct-radio',
						'value' => 'solid',
						'view' => 'text',
						'design' => 'block',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'solid' => __( 'Solid', 'blocksy' ),
							'outline' => __( 'Outline', 'blocksy' ),
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'socialsLabelVisibility' => [
				'label' => __( 'Label Visibility', 'blocksy' ),
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
					'desktop' => __( 'Desktop', 'blocksy' ),
					'tablet' => __( 'Tablet', 'blocksy' ),
					'mobile' => __( 'Mobile', 'blocksy' ),
				]),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'wp_customizer_current_view' => 'tablet|mobile' ],
				'options' => [

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'visibility' => [
						'label' => __( 'Element Visibility', 'blocksy' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'setting' => [ 'transport' => 'postMessage' ],
						'allow_empty' => true,
						'value' => [
							'tablet' => true,
							'mobile' => true,
						],

						'choices' => blocksy_ordered_keys([
							'tablet' => __( 'Tablet', 'blocksy' ),
							'mobile' => __( 'Mobile', 'blocksy' ),
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

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [
					'any' => [
						'socialsLabelVisibility/desktop' => true,
						'socialsLabelVisibility/tablet' => true,
						'socialsLabelVisibility/mobile' => true,
					]
				],
				'options' => [

					'socials_label_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Label Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '12px',
							'variation' => 'n6',
							'text-transform' => 'uppercase',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-labeled-group',
						'label' => __( 'Label Font Color', 'blocksy' ),
						'responsive' => true,
						'choices' => [
							[
								'id' => 'header_socials_font_color',
								'label' => __('Default State', 'blocksy'),
								'condition' => [
									'headerSocialsColor' => 'custom',
								],
							],

							[
								'id' => 'transparent_header_socials_font_color',
								'label' => __('Transparent State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'headerSocialsColor' => 'custom',
									'builderSettings/has_transparent_header' => 'yes',
								],
							],

							[
								'id' => 'sticky_header_socials_font_color',
								'label' => __('Sticky State', 'blocksy'),
								'condition' => [
									'row' => '!offcanvas',
									'headerSocialsColor' => 'custom',
									'builderSettings/has_sticky_header' => 'yes',
								],
							],
						],
						'options' => [
							'header_socials_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
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
										'inherit' => 'var(--color)'
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => 'var(--linkHoverColor)'
									],
								],
							],

							'transparent_header_socials_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
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

							'sticky_header_socials_font_color' => [
								'label' => __( 'Font Color', 'blocksy' ),
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
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Icons Color', 'blocksy' ),
				'responsive' => true,
				'divider' => 'top:full',
				'choices' => [
					[
						'id' => 'headerSocialsIconColor',
						'label' => __('Default State', 'blocksy'),
						'condition' => [ 'headerSocialsColor' => 'custom' ],
					],

					[
						'id' => 'transparentHeaderSocialsIconColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'headerSocialsColor' => 'custom',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderSocialsIconColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'headerSocialsColor' => 'custom',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],	
				],
				'options' => [

					'headerSocialsIconColor' => [
						'label' => __( 'Icons Color', 'blocksy' ),
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
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--paletteColor2)'
							],
						],
					],

					'transparentHeaderSocialsIconColor' => [
						'label' => __( 'Icons Color', 'blocksy' ),
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
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
							],
						],
					],

					'stickyHeaderSocialsIconColor' => [
						'label' => __( 'Icons Color', 'blocksy' ),
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
				'type' => 'ct-labeled-group',
				'label' => [
					__('Icons Background Color', 'blocksy') => [
						'socialsFillType' => 'solid'
					],

					__('Icons Border Color', 'blocksy') => [
						'socialsFillType' => 'outline'
					]
				],
				'responsive' => true,
				'divider' => 'top:full',
				'choices' => [
					[
						'id' => 'headerSocialsIconBackground',
						'label' => __('Default State', 'blocksy'),
						'condition' => [ 
							'headerSocialsColor' => 'custom',
							'socialsType' => '!simple'
						],
					],

					[
						'id' => 'transparentHeaderSocialsIconBackground',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'headerSocialsColor' => 'custom',
							'socialsType' => '!simple',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderSocialsIconBackground',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'headerSocialsColor' => 'custom',
							'socialsType' => '!simple',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerSocialsIconBackground' => [
						'label' => [
							__('Icons Background Color', 'blocksy') => [
								'socialsFillType' => 'solid'
							],

							__('Icons Border Color', 'blocksy') => [
								'socialsFillType' => 'outline'
							]
						],
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'divider' => 'top:full',
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'default' => [
								'color' => 'rgba(218, 222, 228, 0.3)',
							],

							'hover' => [
								'color' => 'var(--paletteColor1)',
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

					'transparentHeaderSocialsIconBackground' => [
						'label' => [
							__('Icons Background Color', 'blocksy') => [
								'socialsFillType' => 'solid'
							],

							__('Icons Border Color', 'blocksy') => [
								'socialsFillType' => 'outline'
							]
						],
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
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
							],
						],
					],

					'stickyHeaderSocialsIconBackground' => [
						'label' => [
							__('Icons Background Color', 'blocksy') => [
								'socialsFillType' => 'solid'
							],

							__('Icons Border Color', 'blocksy') => [
								'socialsFillType' => 'outline'
							]
						],
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
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
							],
						],
					],

				],
			],

			'headerSocialsMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true,
				'divider' => 'top:full',
			],

		],
	],

];
