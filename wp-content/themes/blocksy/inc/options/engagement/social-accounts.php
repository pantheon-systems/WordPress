<?php
/**
 * Social Accounts
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$social_networks_options = [];

foreach (blocksy_get_social_networks_list() as $id => $network) {
	$social_section_options[$id] = [
		'label' => $network['label'],
		'type' => 'text',
		'design' => 'inline',
		'value' => '',
		'setting' => [ 'transport' => 'postMessage' ],
	];
}

$options = [
	blocksy_rand_md5() => [
		'type' => 'ct-divider',
	],
	
	'social_section_options' => [
		'label' => __( 'Social Network Accounts', 'blocksy' ),
		'type' => 'ct-panel',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			[
				blocksy_rand_md5() => [
					'label' => __( 'Social Network Accounts', 'blocksy' ),
					'type' => 'ct-title',
					'desc' => __( 'Easily link your social media accounts and display them throughout your website, with the various elements provided in the customizer.', 'blocksy' ),
				],

				blocksy_rand_md5() => [
					'type' => 'ct-divider',
				],
			],

			$social_section_options
		],
	],
];
