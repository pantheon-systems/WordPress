<?php
/**
 * General options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$options = [
	'general_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'customizer_section' => 'layout',
		'inner-options' => [

			blocksy_get_options('general/layout'),

			blocksy_get_options('general/buttons'),

			blocksy_get_options('general/breadcrumbs'),

			blocksy_get_options('general/form-elements'),

			blocksy_get_options('general/content-elements'),

			blocksy_get_options('engagement/social-accounts'),

			blocksy_get_options('engagement/general'),

			blocksy_get_options('general/back-to-top'),

			[
				'has_passepartout' => [
					'label' => __( 'Website Frame', 'blocksy' ),
					'type' => 'ct-panel',
					'switch' => true,
					'value' => 'no',
					'setting' => [ 'transport' => 'postMessage' ],
					'inner-options' => [

						'passepartoutSize' => [
							'label' => __( 'Frame Size', 'blocksy' ),
							'type' => 'ct-slider',
							'min' => 0,
							'max' => 50,
							'responsive' => true,
							'value' => 10,
							'setting' => [ 'transport' => 'postMessage' ],
						],

						'passepartoutColor' => [
							'label' => __( 'Frame Color', 'blocksy' ),
							'type'  => 'ct-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],
							'value' => [
								'default' => [
									'color' => 'var(--paletteColor1)',
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
					'type' => 'ct-divider'
				],

				blocksy_rand_md5() => [
					'label' => __('Manage Options', 'blocksy'),
					'type' => 'ct-panel',
					'setting' => ['transport' => 'postMessage'],
					'inner-options' => [
						apply_filters('blocksy:options:manage-options:top', []),

						[
							blocksy_rand_md5() => [
								'type' => 'ct-title',
								'label' => __( 'Reset Options', 'blocksy' ),
								'desc' => __( 'Click this button if you want to reset all settings to their default values.', 'blocksy' ),
							],

							'blocksy-reset-customizer-options' => [
								'label' => false,
								'disableRevertButton' => true,
								'type' => 'ct-customizer-reset-options',
								'value' => ''
							]
						]
					],
				]
			],

			apply_filters('blocksy:options:general:bottom', [])
		],
	],

	'customizer_color_scheme' => [
		'label' => __( 'Color scheme', 'blocksy' ),
		'type' => 'hidden',
		'label' => '',
		'value' => 'no',
		'setting' => [ 'transport' => 'postMessage' ],
	],
];
