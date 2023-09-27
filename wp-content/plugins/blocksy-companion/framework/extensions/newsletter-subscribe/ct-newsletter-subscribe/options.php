<?php
/**
 * Newsletter Subscribe widget
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
		'value' => __( 'Newsletter', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	// 'newsletter_subscribe_text' => [
	// 	'label' => __( 'Message', 'blocksy-companion' ),
	// 	'type' => 'textarea',
	// 	'value' => __( 'Enter your email address below to subscribe to our newsletter', 'blocksy-companion' ),
	// 	'design' => 'inline',
	// 	'disableRevertButton' => true,
	// ],

	'newsletter_subscribe_text' => [
		'label' => __( 'Text', 'blocksy-companion' ),
		'type' => 'wp-editor',
		'design' => 'inline',
		'value' => __( 'Enter your email address below to subscribe to our newsletter', 'blocksy-companion' ),
		'desc' => __( 'You can add here some arbitrary HTML code.', 'blocksy-companion' ),
		'disableRevertButton' => true,
		'setting' => [ 'transport' => 'postMessage' ],

		'mediaButtons' => false,
		'tinymce' => [
			'toolbar1' => 'bold,italic,link,undo,redo',
		],
	],

	'newsletter_subscribe_list_id_source' => [
		'type' => 'ct-radio',
		'label' => __( 'List Source', 'blocksy-companion' ),
		'value' => 'default',
		'view' => 'radio',
		'inline' => true,
		'design' => 'inline',
		'disableRevertButton' => true,
		'choices' => [
			'default' => __('Default', 'blocksy-companion'),
			'custom' => __('Custom', 'blocksy-companion'),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'newsletter_subscribe_list_id_source' => 'custom' ],
		'options' => [

			'newsletter_subscribe_list_id' => [
				'label' => __( 'List ID', 'blocksy-companion' ),
				'type' => 'blocksy-newsletter-subscribe',
				'value' => '',
				'design' => 'inline',
				'disableRevertButton' => true,
			],

		],
	],

	'has_newsletter_subscribe_name' => [
		'type'  => 'ct-switch',
		'label' => __( 'Name Field', 'blocksy-companion' ),
		'value' => 'no',
		'disableRevertButton' => true,
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'has_newsletter_subscribe_name' => 'yes' ],
		'options' => [

			'newsletter_subscribe_name_label' => [
				'type' => 'text',
				'label' => __( 'Name Label', 'blocksy-companion' ),
				'design' => 'inline',
				'value' => __( 'Your name', 'blocksy-companion' ),
				'disableRevertButton' => true,
			],

		],
	],

	'newsletter_subscribe_mail_label' => [
		'type' => 'text',
		'label' => __( 'Mail Label', 'blocksy-companion' ),
		'design' => 'inline',
		'value' => __( 'Your email', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	'newsletter_subscribe_button_text' => [
		'type' => 'text',
		'label' => __( 'Button Label', 'blocksy-companion' ),
		'design' => 'inline',
		'value' => __( 'Subscribe', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	'newsletter_subscribe_container' => [
		'label' => __( 'Container Type', 'blocksy-companion' ),
		'type' => 'ct-select',
		'value' => 'default',
		'design' => 'inline',
		'disableRevertButton' => true,
		'choices' => [
			'default' => __( 'Default', 'blocksy-companion' ),
			'boxed' => __( 'Boxed', 'blocksy-companion' ),
		],
	],

	'newsletter_subscribe_alignment' => [
		'type' => 'ct-radio',
		'label' => __( 'Content Alignment', 'blocksy-companion' ),
		'value' => 'left',
		'view' => 'text',
		'design' => 'inline',
		'attr' => [ 'data-type' => 'alignment' ],
		'disableRevertButton' => true,
		'choices' => [
			'left' => '',
			'center' => '',
			'right' => '',
		],
	],

];
