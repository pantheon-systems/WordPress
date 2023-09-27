<?php

$options = [
	'woo_has_product_tabs' => [
		'label' => __( 'Product Tabs', 'blocksy' ),
		'type' => 'ct-panel',
		'switch' => true,
		'value' => 'yes',
		'sync' => blocksy_sync_whole_page([
			'prefix' => 'product',
			'loader_selector' => '.type-product'
		]),
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [
					'woo_tabs_type' => [
						'label' => __( 'Tabs Type', 'blocksy' ),
						'type' => 'ct-select',
						'value' => 'type-1',
						'view' => 'text',
						'design' => 'inline',
						'choices' => blocksy_ordered_keys(
							[
								'type-1' => __( 'Type 1', 'blocksy' ),
								'type-2' => __( 'Type 2', 'blocksy' ),
							]
						),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'woo_tabs_type' => '!type-4' ],
						'options' => [

							'woo_tabs_alignment' => [
								'type' => 'ct-radio',
								'label' => __( 'Horizontal Alignment', 'blocksy' ),
								'view' => 'text',
								'design' => 'block',
								'divider' => 'top',
								'attr' => [ 'data-type' => 'alignment' ],
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => 'center',
								'choices' => [
									'left' => '',
									'center' => '',
									'right' => '',
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

					'woo_tabs_font' => [
						'type' => 'ct-typography',
						'label' => __( 'Font', 'blocksy' ),
						'value' => blocksy_typography_default_values([
							'size' => '12px',
							'variation' => 'n6',
							'text-transform' => 'uppercase',
						]),
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'woo_tabs_font_color' => [
						'label' => __( 'Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'sync' => 'live',
						'value' => [
							'default' => [
								'color' => 'var(--color)',
							],

							'hover' => [
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
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--linkHoverColor)'
							],

							[
								'title' => __( 'Active', 'blocksy' ),
								'id' => 'active',
								'inherit' => 'var(--linkHoverColor)'
							],
						],
					],

					'woo_tabs_border_color' => [
						'label' => __( 'Border Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'inline',
						'divider' => 'top',
						'sync' => 'live',
						'value' => [
							'default' => [
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
							],
						],

						'pickers' => [
							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
								'inherit' => 'var(--border-color)'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'woo_tabs_type' => 'type-1' ],
						'options' => [

							'woo_actibe_tab_border' => [
								'label' => __( 'Active Tab Border', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'divider' => 'top',
								'sync' => 'live',
								'value' => [
									'default' => [
										'color' => 'var(--paletteColor1)',
									],
								],

								'pickers' => [
									[
										'title' => __( 'Active', 'blocksy' ),
										'id' => 'default',
									],
								],
							],

						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'woo_tabs_type' => 'type-2' ],
						'options' => [

							'woo_actibe_tab_background' => [
								'label' => __( 'Active Tab Colors', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'inline',
								'divider' => 'top',
								'sync' => 'live',
								'value' => [
									'default' => [
										'color' => 'rgba(242, 244, 247, 0.7)',
									],

									'border' => [
										'color' => Blocksy_Css_Injector::get_skip_rule_keyword('DEFAULT'),
									],
								],

								'pickers' => [
									[
										'title' => __( 'Background', 'blocksy' ),
										'id' => 'default',
									],

									[
										'title' => __( 'Border', 'blocksy' ),
										'id' => 'border',
										'inherit' => 'var(--border-color)'
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
