<?php
/**
 * Quote widget
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
		'value' => __( 'Quote', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	// 'quote_text' => [
	// 	'label' => __( 'Quote text', 'blocksy-companion' ),
	// 	'type' => 'textarea',
	// 	'value' => '',
	// 	'design' => 'inline',
	// 	'disableRevertButton' => true,
	// ],

	'quote_text' => [
		'label' => __( 'Text', 'blocksy-companion' ),
		'type' => 'wp-editor',
		'design' => 'inline',
		'desc' => __( 'You can add here some arbitrary HTML code.', 'blocksy-companion' ),
		'disableRevertButton' => true,
		'setting' => [ 'transport' => 'postMessage' ],

		'mediaButtons' => false,
		'tinymce' => [
			'toolbar1' => 'bold,italic,link,undo,redo',
		],
	],

	'quote_author' => [
		'type' => 'text',
		'label' => __( 'Author Name', 'blocksy-companion' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'John Doe', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	'quote_has_by_label' => [
		'type'  => 'ct-switch',
		'label' => __( 'Author Label', 'blocksy-companion' ),
		'value' => 'yes',
		'design' => 'inline-full',
		'disableRevertButton' => true,
	],

	'quote_avatar' => [
		'label' => __('Author Avatar', 'blocksy-companion'),
		'type' => 'ct-image-uploader',
		'design' => 'inline',
		'value' => [ 'attachment_id' => null ],
		'emptyLabel' => __('Select Image', 'blocksy-companion'),
		'filledLabel' => __('Change Image', 'blocksy-companion'),
	],

];
