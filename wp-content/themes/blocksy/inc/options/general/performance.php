<?php
/**
 * Performance options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$options = [
	'performance_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			apply_filters(
				'blocksy_performance_end_customizer_options',
				[]
			),

			[
				'emoji_scripts' => [
					'label' => __( 'Disable Emojis Script', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'setting' => [ 'transport' => 'postMessage' ],
					'desc' => __( 'Enable this option if you want to remove WordPress emojis script in order to improve the performance.', 'blocksy' )
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider'
				],

				'has_lazy_load' => [
					'label' => __( 'Lazy Load Images', 'blocksy' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'setting' => [ 'transport' => 'postMessage' ],
					'desc' => __( 'Enable lazy loading to improve performance.', 'blocksy' ),
				],

				blocksy_rand_md5() => [
					'type' => 'ct-condition',
					'condition' => [ 'has_lazy_load' => 'yes' ],
					'options' => [

						[
							'has_lazy_load_page_title_image' => [
								'label' => __( 'Post/Page Title Featured Image', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'yes',
								'setting' => [ 'transport' => 'postMessage' ],
							],

							'has_lazy_load_single_featured_image' => [
								'label' => __( 'Single Post/Page Featured Image', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'yes',
								'setting' => [ 'transport' => 'postMessage' ],
							],

							'has_lazy_load_archives_image' => [
								'label' => __( 'Archives Featured Image', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'yes',
								'setting' => [ 'transport' => 'postMessage' ],
							],

							'has_lazy_load_related_posts_image' => [
								'label' => __( 'Related Posts Featured Image', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'yes',
								'setting' => [ 'transport' => 'postMessage' ],
							],
						],

						function_exists('is_shop') ? [
							'has_lazy_load_single_product_image' => [
								'label' => __( 'Single Product Image', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'yes',
								'setting' => [ 'transport' => 'postMessage' ],
							],

							'has_lazy_load_shop_card_image' => [
								'label' => __( 'Shop Archive Featured Image', 'blocksy' ),
								'type' => 'ct-switch',
								'value' => 'yes',
								'setting' => [ 'transport' => 'postMessage' ],
							],
						] : []
					],
				],
			],
		],
	],
];
