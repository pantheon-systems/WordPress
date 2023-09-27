<?php
/**
 * Layout options
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$options = [

	'layout_panel' => [
		'label' => __( 'Layout', 'blocksy' ),
		'type' => 'ct-panel',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [

				'maxSiteWidth' => [
					'label' => __( 'Maximum Site Width', 'blocksy' ),
					'type' => 'ct-slider',
					'value' => 1290,
					'min' => 700,
					'max' => 1900,
                    'sync' => 'live',
				],

				'contentAreaSpacing' => [
					'label' => __( 'Content Area Spacing', 'blocksy' ),
					'type' => 'ct-slider',
					'value' => [
						'desktop' => '60px',
						'tablet' => '60px',
						'mobile' => '50px',
					],
					'units' => blocksy_units_config([
						[ 'unit' => 'px', 'min' => 0, 'max' => 300 ],
					]),
					'responsive' => true,
					'divider' => 'top',
					'setting' => [ 'transport' => 'postMessage' ],
					'desc' => __( 'Adjusts the spacing between the main content area and the header and footer.', 'blocksy' ),
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],

				'narrowContainerWidth' => [
					'label' => __( 'Narrow Container Max Width', 'blocksy' ),
					'type' => 'ct-slider',
					'value' => 750,
					'min' => 400,
					'max' => 1000,
					'setting' => [ 'transport' => 'postMessage' ],
					'desc' => __( 'This option applies only if the posts or pages are set to Narrow Width structure.', 'blocksy' ),
				],

				'wideOffset' => [
					'label' => __( 'Wide Alignment Offset', 'blocksy' ),
					'type' => 'ct-slider',
					'defaultUnit' => 'px',
					'value' => 130,
					'min' => 20,
					'max' => 200,
					'divider' => 'top',
					'setting' => [ 'transport' => 'postMessage' ],
					'desc' => __( 'This option will apply only to those elements that have a wide alignment option.', 'blocksy' ),
				],

		],
	],

];
