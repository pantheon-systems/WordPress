<?php
/**
 * About me widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

require_once dirname( __FILE__ ) . '/helpers.php';

$options = [
	'title' => [
		'type' => 'text',
		'label' => __( 'Title', 'blc' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'About me', 'blc' ),
		'disableRevertButton' => true,
	],

	'about_type' => [
		'label' => __( 'Type', 'blc' ),
		'type' => 'ct-radio',
		'value' => 'simple',
		'view' => 'radio',
		'design' => 'inline',
		'inline' => true,
		'disableRevertButton' => true,
		'choices' => [
			'simple' => __( 'Simple', 'blc' ),
			'bordered' => __( 'Boxed', 'blc' ),
		],
	],

	'about_source' => [
		'label' => __( 'Source', 'blc' ),
		'type' => 'ct-radio',
		'value' => 'from_wp',
		'view' => 'radio',
		'design' => 'inline',
		'inline' => true,
		'disableRevertButton' => true,
		'choices' => [
			'from_wp' => __( 'From WP', 'blc' ),
			'custom' => __( 'Custom', 'blc' ),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'about_source' => 'from_wp' ],
		'options' => [

			'wp_user' => [
				'type' => 'ct-select',
				'label' => __( 'User', 'blc' ),
				'value' => array_keys(blc_get_user_choices())[0],
				'design' => 'inline',
				'choices' => blocksy_ordered_keys(blc_get_user_choices()),
			],

		]
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'about_source' => 'custom' ],
		'options' => [

			'about_avatar' => [
				'label' => __('Avatar', 'blc'),
				'type' => 'ct-image-uploader',
				'design' => 'inline',
				'value' => [ 'attachment_id' => null ],
				'attr' => [ 'data-type' => 'no-frame' ],
			],

			'about_name' => [
				'type' => 'text',
				'label' => __( 'Name', 'blc' ),
				'field_attr' => [ 'id' => 'widget-title' ],
				'design' => 'inline',
				'value' => __( 'John Doe', 'blc' ),
				'disableRevertButton' => true,
			],

			// 'about_text' => [
			// 	'label' => __( 'Description', 'blc' ),
			// 	'type' => 'textarea',
			// 	'value' => '',
			// 	'design' => 'inline',
			// 	'disableRevertButton' => true,
			// ],

			'about_text' => [
				'label' => __( 'Description', 'blc' ),
				'type' => 'wp-editor',
				'design' => 'inline',
				'value' => __( 'Sample text', 'blc' ),
				'desc' => __( 'You can add here some arbitrary HTML code.', 'blc' ),
				'disableRevertButton' => true,
				'setting' => [ 'transport' => 'postMessage' ],

				'mediaButtons' => false,
				'tinymce' => [
					'toolbar1' => 'bold,italic,link,undo,redo',
				],
			],
		],
	],

	'about_avatar_size' => [
		'type' => 'text',
		'label' => __( 'Avatar Size', 'blc' ),
		'design' => 'inline',
		'value' => 75,
		'disableRevertButton' => true,
	],

	'about_avatar_size' => [
		'label' => __( 'Avatar Size', 'blc' ),
		'type' => 'ct-select',
		'value' => 'small',
		'design' => 'inline',
		'disableRevertButton' => true,
		'choices' => [
			'small' => __( 'Small', 'blc' ),
			'medium' => __( 'Medium', 'blc' ),
			'large' => __( 'Large', 'blc' ),
		],
	],

	'avatar_shape' => [
		'label' => __( 'Avatar Shape', 'blc' ),
		'type' => 'ct-radio',
		'value' => 'rounded',
		'view' => 'radio',
		'design' => 'inline',
		'inline' => true,
		'disableRevertButton' => true,
		'choices' => [
			'rounded' => __( 'Rounded', 'blc' ),
			'square' => __( 'Square', 'blc' ),
		],
	],

	'about_alignment' => [
		'type' => 'ct-radio',
		'label' => __( 'Alignment', 'blc' ),
		'value' => 'center',
		'view' => 'text',
		'attr' => [ 'data-type' => 'alignment' ],
		'disableRevertButton' => true,
		'design' => 'inline',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'left' => '',
			'center' => '',
			'right' => '',
		],
	],

	'about_socials' => [
		'type' => 'ct-layers',
		'label' => __( 'Social Channels', 'blc' ),
		'manageable' => true,
		'desc' => sprintf(
			__( 'You can configure social URLs in %s.', 'blc' ),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				admin_url('/customize.php?autofocus[section]=social_accounts'),
				__('Customizer', 'blc')
			)
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

		'settings' => apply_filters(
			'blocksy:header:socials:options:icon', 
			blc_call_fn([
				'fn' => 'blocksy_get_social_networks_list',
				'default' => []
			])
		)
	],

	'about_social_icons_size' => [
		'label' => __( 'Icons Size', 'blc' ),
		'type' => 'ct-radio',
		'value' => 'small',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'small' => __( 'Small', 'blc' ),
			'medium' => __( 'Medium', 'blc' ),
			'large' => __( 'Large', 'blc' ),
		],
	],

	'about_social_type' => [
		'label' => __( 'Icons Shape Type', 'blc' ),
		'type' => 'ct-radio',
		'value' => 'rounded',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'simple' => __( 'None', 'blc' ),
			'rounded' => __( 'Rounded', 'blc' ),
			'square' => __( 'Square', 'blc' ),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'about_social_type' => '!simple' ],
		'options' => [

			'about_social_icons_fill' => [
				'label' => __( 'Shape Fill Type', 'blc' ),
				'type' => 'ct-radio',
				'value' => 'outline',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'solid' => __( 'Solid', 'blc' ),
					'outline' => __( 'Outline', 'blc' ),
				],
			],

		],
	],

];
