<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'base' => 'vc_flickr',
	'name' => __( 'Flickr Widget', 'js_composer' ),
	'icon' => 'icon-wpb-flickr',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Image feed from Flickr account', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Flickr ID', 'js_composer' ),
			'param_name' => 'flickr_id',
			'value' => '95572727@N00',
			'admin_label' => true,
			'description' => sprintf( __( 'To find your flickID visit %s.', 'js_composer' ), '<a href="http://idgettr.com/" target="_blank">idGettr</a>' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Number of photos', 'js_composer' ),
			'param_name' => 'count',
			'value' => array(
				9,
				8,
				7,
				6,
				5,
				4,
				3,
				2,
				1,
			),
			'description' => __( 'Select number of photos to display.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Type', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				__( 'User', 'js_composer' ) => 'user',
				__( 'Group', 'js_composer' ) => 'group',
			),
			'description' => __( 'Select photo stream type.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Display order', 'js_composer' ),
			'param_name' => 'display',
			'value' => array(
				__( 'Latest first', 'js_composer' ) => 'latest',
				__( 'Random', 'js_composer' ) => 'random',
			),
			'description' => __( 'Select photo display order.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'el_id',
			'heading' => __( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'js_composer' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
	),
);
