<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/*nectar addition*/
$options = (function_exists('get_nectar_theme_options')) ? get_nectar_theme_options() : ''; 
/*nectar addition end*/
return array(
	'name' => __( 'Pie Chart', 'js_composer' ),
	'base' => 'vc_pie',
	'class' => '',
	'icon' => 'icon-wpb-vc_pie',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Animated pie chart', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Value', 'js_composer' ),
			'param_name' => 'value',
			'description' => __( 'Enter value for graph (Note: choose range from 0 to 100).', 'js_composer' ),
			'value' => '50',
			'admin_label' => true,
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Label value', 'js_composer' ),
			'param_name' => 'label_value',
			'description' => __( 'Enter label for pie chart (Note: leaving empty will set value from "Value" field).', 'js_composer' ),
			'value' => '',
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Units', 'js_composer' ),
			'param_name' => 'units',
			'description' => __( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'js_composer' ),
		),
		/* nectar addition */ 
		array(
			 "type" => "dropdown",
			  "heading" => __("Color", "js_composer"),
			  "param_name" => "color",
			  "value" => array(
				 "Accent-Color" => ($options != '') ? $options["accent-color"] : 'None',
				 "Extra-Color-1" => ($options != '') ? $options["extra-color-1"] : 'None',
				 "Extra-Color-2" => ($options != '') ? $options["extra-color-2"] : 'None',
				 "Extra-Color-3" =>  ($options != '') ? $options["extra-color-3"] : 'None'
			   ),
			  'save_always' => true,
			  "description" => __("Please select the color you wish for your social links to display in.", "js_composer")
		),
		/* nectar addition end */ 
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Custom color', 'js_composer' ),
			'param_name' => 'custom_color',
			'description' => __( 'Select custom color.', 'js_composer' ),
			'dependency' => array(
				'element' => 'color',
				'value' => array( 'custom' ),
			),
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
