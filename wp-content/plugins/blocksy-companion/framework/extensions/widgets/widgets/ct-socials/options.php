<?php
/**
 * Options for socials widget.
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$options = [

	'title' => [
		'type' => 'text',
		'label' => __( 'Title', 'blocksy-companion' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'Social Icons', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	'socials' => [
		'type' => 'ct-layers',
		'label' => false,
		'manageable' => true,
		'desc' => sprintf(
			// translators: placeholder here means the actual URL.
			__( 'Configure the social links in Customizer ➝ General ➝ %sSocial Network Accounts%s.', 'blocksy-companion' ),
			sprintf(
				'<a href="%s" data-trigger-section="general:social_section_options" target="_blank">',
				admin_url('/customize.php?autofocus[section]=general&ct_autofocus=general:social_section_options')
			),
			'</a>'
		),

		'value' => [
			[
				'id' => 'facebook',
				'enabled' => true,
			],

			[
				'id' => 'twitter',
				'enabled' => true,
			],

			[
				'id' => 'instagram',
				'enabled' => true,
			],
		],

		'settings' =>  apply_filters(
			'blocksy:header:socials:options:icon', 
			blc_call_fn([
				'fn' => 'blocksy_get_social_networks_list',
				'default' => []
			])
		)

	],

	'link_target' => [
		'type'  => 'ct-switch',
		'label' => __( 'Open links in new tab', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
		'disableRevertButton' => true,
	],

	'link_nofollow' => [
		'type'  => 'ct-switch',
		'label' => __( 'Set links to nofollow', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

	'social_icons_color' => [
		'label' => __('Icons Color', 'blocksy'),
		'type' => 'ct-radio',
		'value' => 'default',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'default' => __( 'Default', 'blocksy' ),
			'official' => __( 'Official', 'blocksy' ),
		],
	],

	'social_icons_size' => [
		'label' => __( 'Icons Size', 'blocksy-companion' ),
		'type' => 'ct-radio',
		'value' => 'medium',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'small' => __( 'Small', 'blocksy-companion' ),
			'medium' => __( 'Medium', 'blocksy-companion' ),
			'large' => __( 'Large', 'blocksy-companion' ),
		],
	],

	'social_type' => [
		'label' => __( 'Icons Shape Type', 'blocksy-companion' ),
		'type' => 'ct-radio',
		'value' => 'simple',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'simple' => __( 'None', 'blocksy-companion' ),
			'rounded' => __( 'Rounded', 'blocksy-companion' ),
			'square' => __( 'Square', 'blocksy-companion' ),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'social_type' => '!simple' ],
		'options' => [

			'social_icons_fill' => [
				'label' => __( 'Shape Fill Type', 'blocksy-companion' ),
				'type' => 'ct-radio',
				'value' => 'outline',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'solid' => __( 'Solid', 'blocksy-companion' ),
					'outline' => __( 'Outline', 'blocksy-companion' ),
				],
			],

		],
	],

];
