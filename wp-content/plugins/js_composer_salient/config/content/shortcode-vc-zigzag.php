<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'ZigZag Separator', 'js_composer' ),
	'base' => 'vc_zigzag',
	'icon' => 'vc_icon-vc-zigzag',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Horizontal zigzag separator line', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => __( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'value' => array_merge( getVcShared( 'colors' ), array( __( 'Custom color', 'js_composer' ) => 'custom' ) ),
			'std' => 'grey',
			'description' => __( 'Select color of separator.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Custom Color', 'js_composer' ),
			'param_name' => 'custom_color',
			'description' => __( 'Select color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'color',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'value' => array(
				__( 'Center', 'js_composer' ) => 'center',
				__( 'Left', 'js_composer' ) => 'left',
				__( 'Right', 'js_composer' ) => 'right',
			),
			'description' => __( 'Select separator alignment.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Element width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => array(
				'100%' => '100',
				'90%' => '90',
				'80%' => '80',
				'70%' => '70',
				'60%' => '60',
				'50%' => '50',
				'40%' => '40',
				'30%' => '30',
				'20%' => '20',
				'10%' => '10',
			),
			'description' => __( 'Select separator width (percentage).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Border width', 'js_composer' ),
			'param_name' => 'el_border_width',
			'std' => '12',
			'value' => array(
				__( 'Extra small', 'js_composer' ) => '8',
				__( 'Small', 'js_composer' ) => '10',
				__( 'Medium', 'js_composer' ) => '12',
				__( 'Large', 'js_composer' ) => '15',
				__( 'Extra large', 'js_composer' ) => '20',
			),
			'description' => __( 'Select separator border width.', 'js_composer' ),
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
