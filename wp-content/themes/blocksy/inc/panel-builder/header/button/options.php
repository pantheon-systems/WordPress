<?php

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
        'options' => array_merge(
			[
				'header_button_type' => [
					'label' => false,
					'type' => 'ct-image-picker',
					'value' => 'type-1',
					'choices' => [

						'type-1' => [
							'src'   => blocksy_image_picker_file( 'button-1' ),
							'title' => __( 'Default', 'blocksy' ),
						],

						'type-2' => [
							'src'   => blocksy_image_picker_file( 'button-2' ),
							'title' => __( 'Ghost', 'blocksy' ),
						],

					],
				],

				'header_button_size' => [
					'label' => __('Size', 'blocksy'),
					'type' => 'ct-select',
					'value' => 'small',
					'view' => 'text',
					'design' => 'inline',
					'divider' => 'top',
					'choices' => blocksy_ordered_keys(
						[
							'default' => __( 'Default', 'blocksy' ),
							'small' => __( 'Small', 'blocksy' ),
							'medium' => __( 'Medium', 'blocksy' ),
							'large' => __( 'Large', 'blocksy' ),
						]
					),
				],

				'header_button_text' => [
					'label' => __( 'Label', 'blocksy' ),
					'type' => 'text',
					'design' => 'inline',
					'divider' => 'top',
					'value' => __( 'Download', 'blocksy' ),
				],

				'header_button_open' => [
					'label' => __('Click Behavior', 'blocksy'),
					'type' => (
						function_exists('blc_fs')
						&&
						blc_fs()->can_use_premium_code()
					) ? 'ct-select' : 'hidden',
					'value' => 'link',
					'view' => 'text',
					'design' => 'inline',
					'divider' => 'top:full',
					'choices' => [
						'link' => __('Open Link', 'blocksy'),
						'popup' => __('Open Popup', 'blocksy'),
					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'header_button_open' => '!popup' ],
					'options' => [

						'header_button_link' => [
							'label' => __( 'Link/URL', 'blocksy' ),
							'type' => 'text',
							'design' => 'inline',
							'value' => '#',
						],

						'header_button_target' => [
							'label' => __( 'Open in new tab', 'blocksy' ),
							'type'  => 'ct-switch',
							'value' => 'no',
							'divider' => 'top',
						],

						'header_button_nofollow' => [
							'type'  => 'ct-switch',
							'label' => __( 'Set link to nofollow', 'blocksy' ),
							'value' => 'no',
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'header_button_open' => 'popup' ],
					'options' => (
						function_exists('blocksy_get_default_content_block')
						&&
						blocksy_get_default_content_block(null, [
							'template_type' => 'popup'
						])
					) ? [
						'header_button_select_popup' => [
							'label' => __('Popup Template', 'blocksy' ),
							'type' => 'ct-select',
							'design' => 'inline',
							'value' => blocksy_get_default_content_block(null, [
								'template_type' => 'popup'
							]),
							'choices' => blocksy_ordered_keys(blc_get_content_blocks([
								'template_type' => 'popup'
							])),
						],
					] : [
						blocksy_rand_md5() => [
							'type' => 'html',
							'label' => __('Popup', 'blocksy' ),
							'html' => '<p>' . __('Please go ahead and create a popup first.', 'blocksy') . '</p>'
						]
					],
				],
			],

			apply_filters(
				'blocksy:header:button:options:after-link-options',
				[]
			),

			$panel_type === 'header' ? [
				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'wp_customizer_current_view' => 'tablet|mobile' ],
					'options' => [
						'visibility' => [
							'label' => __( 'Element Visibility', 'blocksy' ),
							'type' => 'ct-visibility',
							'design' => 'block',
							'divider' => 'top:full',
							'allow_empty' => true,
							'value' => [
								'tablet' => true,
								'mobile' => true,
							],

							'choices' => blocksy_ordered_keys([
								'tablet' => __( 'Tablet', 'blocksy' ),
								'mobile' => __( 'Mobile', 'blocksy' ),
							]),
							'setting' => [ 'transport' => 'postMessage' ],
						],
					],
				],
			] : [],

			$panel_type === 'footer' ? [
				'footer_button_horizontal_alignment' => [
					'type' => 'ct-radio',
					'label' => __( 'Horizontal Alignment', 'blocksy' ),
					'view' => 'text',
					'design' => 'block',
					'divider' => 'top:full',
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

				'footer_button_vertical_alignment' => [
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
					'label' => __( 'Element Visibility', 'blocksy' ),
					'type' => 'ct-visibility',
					'design' => 'block',
					'sync' => 'live',
					'divider' => 'top',
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
			] : [],

			[
				'user_visibility' => [
					'label' => __( 'User Visibility', 'blocksy' ),
					'type' => 'ct-checkboxes',
					'design' => 'block',
					'view' => 'text',
					'divider' => 'top:full',
					'value' => [
						'logged_in' => true,
						'logged_out' => true,
					],
					'choices' => blocksy_ordered_keys([
						'logged_in' => __( 'Logged In', 'blocksy' ),
						'logged_out' => __( 'Logged Out', 'blocksy' ),
					]),
				],
			],

			$panel_type === 'header' ? [
				'header_button_class' => [
					'label' => __( 'CSS Class', 'blocksy' ),
					'type' => 'text',
					'design' => 'block',
					'divider' => 'top:full',
					'value' => '',
					'desc' => __( 'Separate multiple classes with spaces.', 'blocksy' ),
				],
			] : [],

			[
				'button_aria_label' => [
					'label' => __( 'Custom Aria Label', 'blocksy' ),
					'type' => 'text',
					'design' => 'block',
					'divider' => 'top',
					'value' => '',
					'desc' => sprintf(
						// translators: placeholder here means the actual URL.
						__( 'Add a custom %saria label%s attribute.', 'blocksy' ),
						sprintf(
							'<a href="https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Attributes/aria-label" target="_blank">'
						),
						'</a>'
					),
				],
			]
		)
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Font Color', 'blocksy' ),
				'divider' => 'bottom',
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerButtonFontColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderButtonFontColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderButtonFontColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerButtonFontColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'default_2' => [
								'color' => 'var(--buttonInitialColor)',
							],

							'hover_2' => [
								'color' => '#ffffff',
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => 'var(--buttonTextInitialColor)',
								'condition' => [ 'header_button_type' => 'type-1' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--buttonTextHoverColor)',
								'condition' => [ 'header_button_type' => 'type-1' ]
							],

							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default_2',
								'condition' => [ 'header_button_type' => 'type-2' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover_2',
								'condition' => [ 'header_button_type' => 'type-2' ]
							],
						],
					],

					'transparentHeaderButtonFontColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'default_2' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover_2' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'condition' => [ 'header_button_type' => 'type-1' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'condition' => [ 'header_button_type' => 'type-1' ]
							],

							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default_2',
								'condition' => [ 'header_button_type' => 'type-2' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover_2',
								'condition' => [ 'header_button_type' => 'type-2' ]
							],
						],
					],

					'stickyHeaderButtonFontColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'default_2' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'hover_2' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'condition' => [ 'header_button_type' => 'type-1' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'condition' => [ 'header_button_type' => 'type-1' ]
							],

							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default_2',
								'condition' => [ 'header_button_type' => 'type-2' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover_2',
								'condition' => [ 'header_button_type' => 'type-2' ]
							],
						],
					],
				],
			],


			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Button Color', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerButtonForeground',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderButtonForeground',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderButtonForeground',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerButtonForeground' => [
						'label' => __( 'Button Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
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

					'transparentHeaderButtonForeground' => [
						'label' => __( 'Button Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
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

					'stickyHeaderButtonForeground' => [
						'label' => __( 'Button Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
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

			'headerCtaRadius' => [
				'label' => __( 'Border Radius', 'blocksy' ),
				'type' => 'ct-spacing',
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true
			],

			'headerCtaMargin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'divider' => 'top',
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true,
			],

		],
	],
];
