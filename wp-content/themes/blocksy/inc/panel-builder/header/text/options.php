<?php

$options = [
	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => array_merge(
			[
				'header_text' => [
					'label' => false,
					'type' => 'wp-editor',
					'value' => __( 'Sample text', 'blocksy' ),
					'desc' => __( 'You can add here some arbitrary HTML code.', 'blocksy' ),
					'divider' => 'bottom:full',
					'disableRevertButton' => true,
					'setting' => [ 'transport' => 'postMessage' ]
				],

				'has_header_text_full_width' => [
					'label' => __( 'Stretch Container', 'blocksy' ),
					'desc' => __( 'Allow the item container to expand and fill in all the available space.', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'no',
					'setting' => [ 'transport' => 'postMessage' ],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'has_header_text_full_width' => 'yes' ],
					'options' => [

						'headerTextMaxWidth' => [
							'label' => __( 'Max Width', 'blocksy' ),
							'type' => 'ct-slider',
							'min' => 10,
							'max' => 100,
							'value' => [
								'mobile' => '100',
								'tablet' => '100',
								'desktop' => '100',
							],
							'defaultUnit' => '%',
							'responsive' => true,
							'setting' => [ 'transport' => 'postMessage' ],
						],

					],
				]

			], $panel_type === 'header' ? [
				'header_html_horizontal_alignment' => [
					'type' => 'ct-radio',
					'label' => __( 'Content Alignment', 'blocksy' ),
					'view' => 'text',
					'design' => 'block',
					'divider' => 'top:full',
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

				'footer_html_horizontal_alignment' => [
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

				'footer_html_vertical_alignment' => [
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

				'footer_visibility' => [
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
				]
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
				]
			]
		)
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'headerTextFont' => [
				'type' => 'ct-typography',
				'label' => __( 'Font', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '15px',
					'line-height' => '1.3',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-labeled-group',
				'label' => __( 'Font Color', 'blocksy' ),
				'responsive' => true,
				'choices' => [
					[
						'id' => 'headerTextColor',
						'label' => __('Default State', 'blocksy')
					],

					[
						'id' => 'transparentHeaderTextColor',
						'label' => __('Transparent State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						],
					],

					[
						'id' => 'stickyHeaderTextColor',
						'label' => __('Sticky State', 'blocksy'),
						'condition' => [
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						],
					],
				],
				'options' => [

					'headerTextColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_initial' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Text Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => 'var(--color)'
							],

							[
								'title' => __( 'Link Initial', 'blocksy' ),
								'id' => 'link_initial',
								'inherit' => 'var(--linkInitialColor)'
							],

							[
								'title' => __( 'Link Hover', 'blocksy' ),
								'id' => 'link_hover',
								'inherit' => 'var(--linkHoverColor)'
							],
						],
					],

					'transparentHeaderTextColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_initial' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Text Initial', 'blocksy' ),
								'id' => 'default',
							],

							[
								'title' => __( 'Link Initial', 'blocksy' ),
								'id' => 'link_initial',
							],

							[
								'title' => __( 'Link Hover', 'blocksy' ),
								'id' => 'link_hover',
							],
						],
					],

					'stickyHeaderTextColor' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_initial' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],

							'link_hover' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Text Initial', 'blocksy' ),
								'id' => 'default',
							],

							[
								'title' => __( 'Link Initial', 'blocksy' ),
								'id' => 'link_initial',
							],

							[
								'title' => __( 'Link Hover', 'blocksy' ),
								'id' => 'link_hover',
							],
						],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'headerTextMargin' => [
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

if ($panel_type === 'header') {
	$options[blocksy_rand_md5()] = [
		'type' => 'ct-condition',
		'condition' => [
			'wp_customizer_current_view' => 'tablet|mobile'
		],
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
	];
}
