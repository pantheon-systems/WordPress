<?php
/**
 * Advertisement widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */


$options = [

	'title' => [
		'type' => 'text',
		'label' => __( 'Title', 'blocksy-companion' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'Advertisement', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	'ad_source' => [
		'label' => __( 'Source', 'blocksy-companion' ),
		'type' => 'ct-radio',
		'value' => 'code',
		'view' => 'radio',
		'design' => 'inline',
		'inline' => true,
		'disableRevertButton' => true,
		'choices' => [
			'code' => __( 'Code', 'blocksy-companion' ),
			'upload' => __( 'Image', 'blocksy-companion' ),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'ad_source' => 'code' ],
		'options' => [

			'ad_code' => [
				'label' => __( 'Ad Code', 'blocksy-companion' ),
				'type' => 'textarea',
				'value' => '',
				'design' => 'inline',
				'disableRevertButton' => true,
			],

		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'ad_source' => 'upload' ],
		'options' => [

			'ad_image' => [
				'label' => __('Upload Image', 'blocksy-companion'),
				'type' => 'ct-image-uploader',
				'design' => 'inline',
				'value' => [ 'attachment_id' => null ],
				'emptyLabel' => __('Select Image', 'blocksy-companion'),
				'filledLabel' => __('Change Image', 'blocksy-companion'),
			],

			'ad_image_ratio' => [
				'label' => __( 'Image Ratio', 'blocksy-companion' ),
				'type' => 'ct-ratio',
				'value' => 'original',
				'design' => 'inline',
			],

			'ad_link' => [
				'type' => 'text',
				'label' => __( 'Ad URL', 'blocksy-companion' ),
				'design' => 'inline',
				'value' => 'https://creativethemes.com',
				'disableRevertButton' => true,
			],

			'ad_link_target' => [
				'type'  => 'ct-switch',
				'label' => __( 'Open link in new tab', 'blocksy-companion' ),
				'value' => 'yes',
				'disableRevertButton' => true,
			],

		],
	],

];
