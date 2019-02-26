<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @var $tag - shortcode tag;
 */
return array(
	'name' => __( 'Inner Column', 'js_composer' ),
	'base' => 'vc_column_inner',
	'icon' => 'icon-wpb-row',
	'class' => '',
	'wrapper_class' => '',
	'controls' => 'full',
	'allowed_container_element' => false,
	'content_element' => false,
	'is_container' => true,
	'description' => __( 'Place content elements inside the inner column', 'js_composer' ),
	'params' => array(
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
			'value' => '',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Width', 'js_composer' ),
			'param_name' => 'width',
			'value' => array(
				__( '1 column - 1/12', 'js_composer' ) => '1/12',
				__( '2 columns - 1/6', 'js_composer' ) => '1/6',
				__( '3 columns - 1/4', 'js_composer' ) => '1/4',
				__( '4 columns - 1/3', 'js_composer' ) => '1/3',
				__( '5 columns - 5/12', 'js_composer' ) => '5/12',
				__( '6 columns - 1/2', 'js_composer' ) => '1/2',
				__( '7 columns - 7/12', 'js_composer' ) => '7/12',
				__( '8 columns - 2/3', 'js_composer' ) => '2/3',
				__( '9 columns - 3/4', 'js_composer' ) => '3/4',
				__( '10 columns - 5/6', 'js_composer' ) => '5/6',
				__( '11 columns - 11/12', 'js_composer' ) => '11/12',
				__( '12 columns - 1/1', 'js_composer' ) => '1/1',
				__( '20% - 1/5', 'js_composer' ) => '1/5',
				__( '40% - 2/5', 'js_composer' ) => '2/5',
				__( '60% - 3/5', 'js_composer' ) => '3/5',
				__( '80% - 4/5', 'js_composer' ) => '4/5',
			),
			'group' => __( 'Responsive Options', 'js_composer' ),
			'description' => __( 'Select column width.', 'js_composer' ),
			'std' => '1/1',
		),
		array(
			'type' => 'column_offset',
			'heading' => __( 'Responsiveness', 'js_composer' ),
			'param_name' => 'offset',
			'group' => __( 'Responsive Options', 'js_composer' ),
			'description' => __( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer' ),
		),
	),
	'js_view' => 'VcColumnView',
);
