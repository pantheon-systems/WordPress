<?php

$options = [
	'label' => __( 'Subscribe Form', 'blocksy-companion' ),
	'type' => 'ct-panel',
	'switch' => true,
	'value' => 'yes',
	'sync' => blocksy_sync_single_post_container(),
	'inner-options' => [

		blocksy_rand_md5() => [
			'title' => __( 'General', 'blocksy-companion' ),
			'type' => 'tab',
			'options' => [

				'newsletter_subscribe_title' => [
					'type' => 'text',
					'label' => __( 'Title', 'blocksy-companion' ),
					'field_attr' => [ 'id' => 'widget-title' ],
					'design' => 'block',
					'value' => __( 'Newsletter Updates', 'blocksy-companion' ),
					'disableRevertButton' => true,
					'setting' => [ 'transport' => 'postMessage' ],
				],

				'newsletter_subscribe_text' => [
					'label' => __( 'Description', 'blocksy-companion' ),
					'type' => 'textarea',
					'value' => __( 'Enter your email address below to subscribe to our newsletter', 'blocksy-companion' ),
					'design' => 'block',
					'disableRevertButton' => true,
					'setting' => [ 'transport' => 'postMessage' ],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
					'attr' => [ 'data-type' => 'small' ],
				],

				'newsletter_subscribe_list_id_source' => [
					'type' => 'ct-radio',
					'label' => __( 'List Source', 'blocksy-companion' ),
					'value' => 'default',
					'view' => 'radio',
					'inline' => true,
					'design' => 'inline',
					'disableRevertButton' => true,
					'choices' => [
						'default' => __('Default', 'blocksy-companion'),
						'custom' => __('Custom', 'blocksy-companion'),
					],

					'setting' => [ 'transport' => 'postMessage' ],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'newsletter_subscribe_list_id_source' => 'custom' ],
					'options' => [

						'newsletter_subscribe_list_id' => [
							'label' => __( 'List ID', 'blocksy-companion' ),
							'type' => 'blocksy-newsletter-subscribe',
							'value' => '',
							'design' => 'inline',
							'disableRevertButton' => true,
							'setting' => [ 'transport' => 'postMessage' ],
						],

					],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
					'attr' => [ 'data-type' => 'small' ],
				],

				'has_newsletter_subscribe_name' => [
					'type'  => 'ct-switch',
					'label' => __( 'Name Field', 'blocksy-companion' ),
					'value' => 'no',
					'disableRevertButton' => true,
					'sync' => blocksy_sync_single_post_container([
						'loader_selector' => '.ct-newsletter-subscribe-block'
					]),
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'has_newsletter_subscribe_name' => 'yes' ],
					'options' => [

						'newsletter_subscribe_name_label' => [
							'type' => 'text',
							'label' => __( 'Name Label', 'blocksy-companion' ),
							'design' => 'inline',
							'value' => __( 'Your name', 'blocksy-companion' ),
							'disableRevertButton' => true,
							'setting' => [ 'transport' => 'postMessage' ],
						],

					],
				],

				'newsletter_subscribe_mail_label' => [
					'type' => 'text',
					'label' => __( 'Mail Label', 'blocksy-companion' ),
					'design' => 'inline',
					'value' => __( 'Your email', 'blocksy-companion' ),
					'disableRevertButton' => true,
					'setting' => [ 'transport' => 'postMessage' ],
				],

				'newsletter_subscribe_button_text' => [
					'type' => 'text',
					'label' => __( 'Button Label', 'blocksy-companion' ),
					'design' => 'inline',
					'value' => __( 'Subscribe', 'blocksy-companion' ),
					'disableRevertButton' => true,
					'setting' => [ 'transport' => 'postMessage' ],
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],

				'newsletter_subscribe_subscribe_visibility' => [
					'label' => __( 'Visibility', 'blocksy-companion' ),
					'type' => 'ct-visibility',
					'design' => 'block',
					'setting' => [ 'transport' => 'postMessage' ],
					'value' => [
						'desktop' => true,
						'tablet' => true,
						'mobile' => false,
					],

					'choices' => blocksy_ordered_keys([
						'desktop' => __( 'Desktop', 'blocksy-companion' ),
						'tablet' => __( 'Tablet', 'blocksy-companion' ),
						'mobile' => __( 'Mobile', 'blocksy-companion' ),
					]),
				],

			],
		],

		blocksy_rand_md5() => [
			'title' => __( 'Design', 'blocksy-companion' ),
			'type' => 'tab',
			'options' => [

				'newsletter_subscribe_title_color' => [
					'label' => __( 'Title Color', 'blocksy-companion' ),
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
							'title' => __( 'Initial', 'blocksy-companion' ),
							'id' => 'default',
							'inherit' => 'var(--heading-color, var(--heading-3-color, var(--headings-color)))'
						],
					],
				],

				'newsletter_subscribe_content' => [
					'label' => __( 'Description Color', 'blocksy-companion' ),
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
							'inherit' => 'var(--color)'
						],

						[
							'title' => __( 'Hover', 'blocksy-companion' ),
							'id' => 'hover',
							'inherit' => 'var(--linkHoverColor)'
						],
					],
				],

				'newsletter_subscribe_input_font_color' => [
					'label' => __( 'Input Font Color', 'blocksy-companion' ),
					'type'  => 'ct-color-picker',
					'design' => 'inline',
					'divider' => 'top',
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

				'newsletter_subscribe_border_color' => [
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

						'newsletter_subscribe_input_background' => [
							'label' => __( 'Input Background Color', 'blocksy-companion' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
								],

								'focus' => [
									'color' => Blocksy_Css_Injector::get_skip_rule_keyword(),
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'blocksy-companion' ),
									'id' => 'default',
								],

								[
									'title' => __( 'Focus', 'blocksy-companion' ),
									'id' => 'focus',
								],
							],
						],

					],
				],

				'newsletter_subscribe_button' => [
					'label' => __( 'Button Color', 'blocksy-companion' ),
					'type'  => 'ct-color-picker',
					'design' => 'inline',
					'setting' => [ 'transport' => 'postMessage' ],

					'value' => [
						'default' => [
							'color' => 'var(--paletteColor1)',
						],

						'hover' => [
							'color' => 'var(--paletteColor2)',
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

				'newsletter_subscribe_container_background' => [
					'label' => __( 'Container Background', 'blocksy-companion' ),
					'type' => 'ct-background',
					'design' => 'block:right',
					'responsive' => true,
					'divider' => 'top:full',
					'sync' => 'live',
					'value' => blocksy_background_default_value([
						'backgroundColor' => [
							'default' => [
								'color' => '#ffffff',
							],
						],
					])
				],

				'newsletter_subscribe_container_border' => [
					'label' => __( 'Container Border', 'blocksy-companion' ),
					'type' => 'ct-border',
					'sync' => 'live',
					'design' => 'block',
					'divider' => 'top',
					'value' => [
						'width' => 1,
						'style' => 'none',
						'color' => [
							'color' => 'var(--paletteColor5)',
						],
					],
					'responsive' => true,
				],

				'newsletter_subscribe_shadow' => [
					'label' => __( 'Container Shadow', 'blocksy' ),
					'type' => 'ct-box-shadow',
					'responsive' => true,
					'divider' => 'top',
					'setting' => [ 'transport' => 'postMessage' ],
					'value' => blocksy_box_shadow_value([
						'enable' => true,
						'h_offset' => 0,
						'v_offset' => 50,
						'blur' => 90,
						'spread' => 0,
						'inset' => false,
						'color' => [
							'color' => 'rgba(210, 213, 218, 0.4)',
						],
					])
				],

				'newsletter_subscribe_container_spacing' => [
					'label' => __( 'Container Inner Spacing', 'blocksy-companion' ),
					'type' => 'ct-spacing',
					'divider' => 'top',
					'setting' => [ 'transport' => 'postMessage' ],
					'value' => blocksy_spacing_value([
						'linked' => true,
						'top' => '30px',
						'left' => '30px',
						'right' => '30px',
						'bottom' => '30px',
					]),
					'responsive' => true
				],

				'newsletter_subscribe_container_border_radius' => [
					'label' => __( 'Container Border Radius', 'blocksy-companion' ),
					'type' => 'ct-spacing',
					'divider' => 'top',
					'setting' => [ 'transport' => 'postMessage' ],
					'value' => blocksy_spacing_value([
						'linked' => true,
					]),
					'responsive' => true
				],

			],
		],
	],
];
