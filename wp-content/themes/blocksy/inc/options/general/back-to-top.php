<?php
/**
 * Back to top options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$is_pro = function_exists('blc_fs') && blc_fs()->can_use_premium_code();

$options = [

	blocksy_rand_md5() => [
		'type' => 'ct-divider',
	],

	'has_back_top' => [
		'label' => __( 'Scroll to Top', 'blocksy' ),
		'type' => 'ct-panel',
		'switch' => true,
		'value' => 'no',
		'inner-options' => [

			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy' ),
				'type' => 'tab',
				'options' => [
					$is_pro ? [
						'top_button_icon_source' => [
							'label' => __( 'Icon Source', 'blocksy' ),
							'type' => 'ct-radio',
							'value' => 'default',
							'view' => 'text',
							'design' => 'block',
							'choices' => [
								'default' => __( 'Default', 'blocksy' ),
								'custom' => __( 'Custom', 'blocksy' ),
							],
							'sync' => [
								'selector' => '.ct-back-to-top',
								'container_inclusive' => true,
								'render' => function () {
									blocksy_output_back_to_top_link();
								}
							]
						],

						blocksy_rand_md5() => [
							'type' => 'ct-condition',
							'condition' => ['top_button_icon_source' => 'custom'],
							'options' => [
								'top_button_icon' => [
									'type' => 'icon-picker',
									'label' => __('Icon', 'blocksy'),
									'design' => 'inline',
									'value' => [
										'icon' => 'blc blc-arrow-up-circle'
									],
									'sync' => [
										'container_inclusive' => true,
										'selector' => '.ct-back-to-top',
										'render' => function () {
											blocksy_output_back_to_top_link();
										}
									]
								]
							]
						]
					]: [],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => $is_pro ? [
							'top_button_icon_source' => 'default'
						] : [
							'top_button_icon_source' => '! not_existing'
						],
						'options' => [
							'top_button_type' => [
								'label' => false,
								'type' => 'ct-image-picker',
								'value' => 'type-1',
								'attr' => [
									'data-type' => 'background',
									'data-columns' => '3',
								],
								'setting' => [ 'transport' => 'postMessage' ],
								'choices' => [

									'type-1' => [
										'src'   => blocksy_image_picker_file( 'top-1' ),
										'title' => __( 'Type 1', 'blocksy' ),
									],

									'type-2' => [
										'src'   => blocksy_image_picker_file( 'top-2' ),
										'title' => __( 'Type 2', 'blocksy' ),
									],

									'type-3' => [
										'src'   => blocksy_image_picker_file( 'top-3' ),
										'title' => __( 'Type 3', 'blocksy' ),
									],

									'type-4' => [
										'src'   => blocksy_image_picker_file( 'top-4' ),
										'title' => __( 'Type 4', 'blocksy' ),
									],

									'type-5' => [
										'src'   => blocksy_image_picker_file( 'top-5' ),
										'title' => __( 'Type 5', 'blocksy' ),
									],

									'type-6' => [
										'src'   => blocksy_image_picker_file( 'top-6' ),
										'title' => __( 'Type 6', 'blocksy' ),
									],
								],
								'sync' => [
									'selector' => '.ct-back-to-top',
									'container_inclusive' => true,
									'render' => function () {
										blocksy_output_back_to_top_link();
									}
								]
							]
						]
					],

					'top_button_shape' => [
						'label' => __( 'Button Shape', 'blocksy' ),
						'type' => 'ct-radio',
						'value' => 'square',
						'view' => 'text',
						'design' => 'block',
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
						'choices' => [
							'square' => __( 'Square', 'blocksy' ),
							'circle' => __( 'Circle', 'blocksy' ),
						],
					],

					'topButtonSize' => [
						'label' => __( 'Icon Size', 'blocksy' ),
						'type' => 'ct-slider',
						'min' => 10,
						'max' => 50,
						'value' => 12,
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'topButtonOffset' => [
						'label' => __( 'Bottom Offset', 'blocksy' ),
						'type' => 'ct-slider',
						'min' => 5,
						'max' => 300,
						'value' => 25,
						'responsive' => true,
						'divider' => 'top',
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'sideButtonOffset' => [
						'label' => __( 'Side Offset', 'blocksy' ),
						'type' => 'ct-slider',
						'min' => 5,
						'max' => 300,
						'value' => 25,
						'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
					],

					'top_button_alignment' => [
						'label' => __( 'Alignment', 'blocksy' ),
						'type' => 'ct-radio',
						'value' => 'right',
						'setting' => [ 'transport' => 'postMessage' ],
						'view' => 'text',
						'divider' => 'top',
						'attr' => [ 'data-type' => 'alignment' ],
						'choices' => [
							'left' => '',
							'right' => '',
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'back_top_visibility' => [
						'label' => __( 'Visibility', 'blocksy' ),
						'type' => 'ct-visibility',
						'design' => 'block',
						'setting' => [ 'transport' => 'postMessage' ],

						'value' => [
							'desktop' => true,
							'tablet' => true,
							'mobile' => false,
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
				'title' => __( 'Design', 'blocksy' ),
				'type' => 'tab',
				'options' => [

					'topButtonIconColor' => [
						'label' => __( 'Icon Color', 'blocksy' ),
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

					'topButtonShapeBackground' => [
						'label' => __( 'Shape Background Color', 'blocksy' ),
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
								'inherit' => 'var(--paletteColor3)'
							],

							[
								'title' => __( 'Hover', 'blocksy' ),
								'id' => 'hover',
								'inherit' => 'var(--paletteColor4)'
							],
						],
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'top_button_shape' => 'square' ],
						'options' => [

							'topButtonRadius' => [
								'label' => __( 'Shape Border Radius', 'blocksy' ),
								'type' => 'ct-spacing',
								'divider' => 'top',
								'setting' => [ 'transport' => 'postMessage' ],
								'value' => blocksy_spacing_value([
									'linked' => true,
									'top' => '2px',
									'left' => '2px',
									'right' => '2px',
									'bottom' => '2px',
								]),
								// 'responsive' => true
							],

						],
					],

					'topButtonShadow' => [
						'label' => __( 'Shadow', 'blocksy' ),
						'type' => 'ct-box-shadow',
						'divider' => 'top',
						'design' => 'inline',
						// 'responsive' => true,
						'setting' => [ 'transport' => 'postMessage' ],
						'value' => blocksy_box_shadow_value([
							'enable' => false,
							'h_offset' => 0,
							'v_offset' => 5,
							'blur' => 20,
							'spread' => 0,
							'inset' => false,
							'color' => [
								'color' => 'rgba(210, 213, 218, 0.2)',
							],
						])
					],

				],
			],

		],
	],
];
