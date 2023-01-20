<?php

$options = [

	blocksy_rand_md5() => [
		'label' => __( 'Add to Cart', 'blocksy' ),
		'type' => 'ct-panel',
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					'add_to_cart_button_width' => [
						'label' => __( 'Button Width', 'blocksy' ),
						'type' => 'ct-slider',
						'value' => '100%',
						'units' => blocksy_units_config([
							[ 'unit' => '%', 'min' => 0, 'max' => 100 ],
							[ 'unit' => 'px', 'min' => 0, 'max' => 500 ],
						]),
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					'quantity_color' => [
						'label' => __( 'Quantity Color', 'blocksy' ),
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
								'inherit' => 'var(--quantity-initial-color, var(--buttonInitialColor))'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--quantity-hover-color, var(--buttonHoverColor))'
							],
						],
					],

					'quantity_arrows' => [
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
								'inherit' => 'var(--quantity-arrows-initial-color, #fff)',
								'condition_source' => 'global',
								'condition' => [ 'quantity_type' => 'type-1' ]
							],

							[
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default_type_2',
								'condition_source' => 'global',
								'condition' => [ 'quantity_type' => 'type-2' ]
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--quantity-arrows-hover-color, #fff)'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'has_ajax_add_to_cart' => 'no' ],
						'options' => [
							blocksy_rand_md5() => [
								'type' => 'ct-divider',
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'has_ajax_add_to_cart' => 'yes' ],
						'options' => [
							blocksy_rand_md5() => [
								'type' => 'ct-title',
								'label' => __( 'Add To Cart Button', 'blocksy' ),
							],
						],
					],

					'add_to_cart_text' => [
						'label' => __( 'Button Font Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
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

					'add_to_cart_background' => [
						'label' => __( 'Button Background Color', 'blocksy' ),
						'type'  => 'ct-color-picker',
						'design' => 'block:right',
						'responsive' => true,
						'divider' => 'top',
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
						'type' => 'ct-condition',
						'condition' => [ 'has_ajax_add_to_cart' => 'yes' ],
						'options' => [

							blocksy_rand_md5() => [
								'type' => 'ct-title',
								'label' => __( 'View Cart Button', 'blocksy' ),
							],

							'view_cart_button_text' => [
								'label' => __( 'Button Font Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
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
										'inherit' => 'var(--color)',
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => 'var(--color)',
									],
								],
							],

							'view_cart_button_background' => [
								'label' => __( 'Button Background Color', 'blocksy' ),
								'type'  => 'ct-color-picker',
								'design' => 'block:right',
								'responsive' => true,
								'divider' => 'top',
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
										'inherit' => 'rgba(224,229,235,0.6)'
									],

									[
										'title' => __( 'Hover', 'blocksy' ),
										'id' => 'hover',
										'inherit' => 'rgba(224,229,235,1)'
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
