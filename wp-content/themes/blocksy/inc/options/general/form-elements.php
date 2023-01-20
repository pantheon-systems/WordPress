<?php
/**
 * Forms options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$options = [

	'form_elements_panel' => [
		'label' => __( 'Form Elements', 'blocksy' ),
		'type' => 'ct-panel',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [

			'forms_type' => [
				'label' => false,
				'type' => 'ct-image-picker',
				'value' => 'classic-forms',
				'setting' => [ 'transport' => 'postMessage' ],
				'switchDeviceOnChange' => 'desktop',
				'choices' => [

					'classic-forms' => [
						'src'   => blocksy_image_picker_url( 'forms-type-1.svg' ),
						'title' => __( 'Classic', 'blocksy' ),
					],

					'modern-forms' => [
						'src'   => blocksy_image_picker_url( 'forms-type-2.svg' ),
						'title' => __( 'Modern', 'blocksy' ),
					],

				],
			],

			'formTextColor' => [
				'label' => __( 'Font Color', 'blocksy' ),
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
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
						'inherit' => 'var(--color)'
					],

					[
						'title' => __( 'Focus', 'blocksy' ),
						'id' => 'focus',
						'inherit' => 'var(--color)'
					],
				],
			],

			'formFontSize' => [
				'label' => __( 'Font Size', 'blocksy' ),
				'type' => 'ct-number',
				'design' => 'inline',
				'value' => 16,
				'min' => 5,
				'max' => 50,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __( 'Input & Textarea', 'blocksy' ),
			],

			'formBorderColor' => [
				'label' => __( 'Border Color', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'default' => [
						'color' => 'var(--border-color)',
					],

					'focus' => [
						'color' => 'var(--paletteColor1)',
					],
				],

				'pickers' => [
					[
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
					],

					[
						'title' => __( 'Focus', 'blocksy' ),
						'id' => 'focus',
					],
				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'forms_type' => 'classic-forms' ],
				'options' => [

					'formBackgroundColor' => [
						'label' => __( 'Background Color', 'blocksy' ),
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
								'title' => __( 'Initial', 'blocksy' ),
								'id' => 'default',
							],

							[
								'title' => __( 'Focus', 'blocksy' ),
								'id' => 'focus',
							],
						],
					],

				],
			],

			'formBorderSize' => [
				'label' => __( 'Border Size', 'blocksy' ),
				'type' => 'ct-number',
				'design' => 'inline',
				'value' => 1,
				'min' => 1,
				'max' => 5,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'formInputHeight' => [
				'label' => __( 'Input Height', 'blocksy' ),
				'type' => 'ct-number',
				'design' => 'inline',
				'value' => 40,
				'min' => 20,
				'max' => 80,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'formTextAreaHeight' => [
				'label' => __( 'Textarea Height', 'blocksy' ),
				'type' => 'ct-number',
				'design' => 'inline',
				'value' => 170,
				'min' => 50,
				'max' => 250,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => [ 'forms_type' => 'classic-forms' ],
				'options' => [

					'formFieldBorderRadius' => [
						'label' => __( 'Border Radius', 'blocksy' ),
						'type' => 'ct-number',
						'design' => 'inline',
						'value' => 3,
						'min' => 0,
						'max' => 200,
						'setting' => [ 'transport' => 'postMessage' ],
					],

				],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __( 'Radio & Checkbox', 'blocksy' ),
			],

			'radioCheckboxColor' => [
				'label' => __( 'Colors', 'blocksy' ),
				'type'  => 'ct-color-picker',
				'design' => 'inline',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => [
					'default' => [
						'color' => 'var(--border-color)',
					],

					'accent' => [
						'color' => 'var(--paletteColor1)',
					],
				],

				'pickers' => [
					[
						'title' => __( 'Initial', 'blocksy' ),
						'id' => 'default',
					],

					[
						'title' => __( 'Active', 'blocksy' ),
						'id' => 'accent',
					],
				],
			],

			'checkboxBorderRadius' => [
				'label' => __( 'Checkbox Border Radius', 'blocksy' ),
				'type' => 'ct-number',
				'design' => 'inline',
				'value' => 3,
				'min' => 0,
				'max' => 10,
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-title',
				'label' => __( 'Select Dropdown', 'blocksy' ),
			],

			'formSelectFontColor' => [
				'label' => __( 'Font Color', 'blocksy' ),
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
						'inherit' => 'var(--color)'
					],

					[
						'title' => __( 'Active', 'blocksy' ),
						'id' => 'active',
						'inherit' => '#ffffff'
					],
				],
			],

			'formSelectBackgroundColor' => [
				'label' => __( 'Background Color', 'blocksy' ),
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
		]
	]
];