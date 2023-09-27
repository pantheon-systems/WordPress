<?php
/**
 * Typography options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$options = [
	'typography_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			'rootTypography' => [
				'type' => 'ct-typography',
				'label' => __( 'Base Font', 'blocksy' ),
				'isDefault' => true,
				'value' => blocksy_typography_default_values([
					'family' => 'System Default',
					'variation' => 'n4',
					'size' => '16px',
					'line-height' => '1.65',
					'letter-spacing' => '0em',
					'text-transform' => 'none',
					'text-decoration' => 'none',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'h1Typography' => [
				'type' => 'ct-typography',
				'label' => __( 'Heading 1 (H1)', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '40px',
					'variation' => 'n7',
					'line-height' => '1.5'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'h2Typography' => [
				'type' => 'ct-typography',
				'label' => __( 'Heading 2 (H2)', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '35px',
					'variation' => 'n7',
					'line-height' => '1.5'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'h3Typography' => [
				'type' => 'ct-typography',
				'label' => __( 'Heading 3 (H3)', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '30px',
					'variation' => 'n7',
					'line-height' => '1.5'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'h4Typography' => [
				'type' => 'ct-typography',
				'label' => __( 'Heading 4 (H4)', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '25px',
					'variation' => 'n7',
					'line-height' => '1.5'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'h5Typography' => [
				'type' => 'ct-typography',
				'label' => __( 'Heading 5 (H5)', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '20px',
					'variation' => 'n7',
					'line-height' => '1.5'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'h6Typography' => [
				'type' => 'ct-typography',
				'label' => __( 'Heading 6 (H6)', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '16px',
					'variation' => 'n7',
					'line-height' => '1.5'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
				'attr' => [ 'data-type' => 'small' ],
			],

			'buttons' => [
				'type' => 'ct-typography',
				'label' => __( 'Buttons', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'size' => '15px',
					'variation' => 'n5',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'quote' => [
				'type' => 'ct-typography',
				'label' => __( 'Quote', 'blocksy' ),
				'value' => blocksy_typography_default_values([]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'pullquote' => [
				'type' => 'ct-typography',
				'label' => __( 'Pullquote', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'family' => 'Georgia',
					'size' => '25px',
					'variation' => 'n6',
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			'pre' => [
				'type' => 'ct-typography',
				'label' => __( 'Preformatted', 'blocksy' ),
				'value' => blocksy_typography_default_values([
					'family' => 'monospace',
					'size' => '16px',
					'variation' => 'n4'
				]),
				'setting' => [ 'transport' => 'postMessage' ],
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'font_family_fallback' => [
				'type' => 'text',
				'value' => 'Sans-Serif',
				'label' => __('Fallback Font Family', 'blocksy'),
				'desc' => __('The font used if the chosen font isn\'t available.', 'blocksy'),
			]
		],
	],
];
