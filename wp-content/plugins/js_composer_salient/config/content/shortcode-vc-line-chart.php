<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Line Chart', 'js_composer' ),
	'base' => 'vc_line_chart',
	'class' => '',
	'icon' => 'icon-wpb-vc-line-chart',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Line and Bar charts', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Design', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				__( 'Line', 'js_composer' ) => 'line',
				__( 'Bar', 'js_composer' ) => 'bar',
			),
			'std' => 'bar',
			'description' => __( 'Select type of chart.', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style', 'js_composer' ),
			'description' => __( 'Select chart color style.', 'js_composer' ),
			'param_name' => 'style',
			'value' => array(
				__( 'Flat', 'js_composer' ) => 'flat',
				__( 'Modern', 'js_composer' ) => 'modern',
				__( 'Custom', 'js_composer' ) => 'custom',
			),
			'dependency' => array(
				'callback' => 'vcChartCustomColorDependency',
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Show legend?', 'js_composer' ),
			'param_name' => 'legend',
			'description' => __( 'If checked, chart will have legend.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
			'std' => 'yes',
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Show hover values?', 'js_composer' ),
			'param_name' => 'tooltips',
			'description' => __( 'If checked, chart will show values on hover.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
			'std' => 'yes',
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'X-axis values', 'js_composer' ),
			'param_name' => 'x_values',
			'description' => __( 'Enter values for axis (Note: separate values with ";").', 'js_composer' ),
			'value' => 'JAN; FEB; MAR; APR; MAY; JUN; JUL; AUG',
		),
		array(
			'type' => 'param_group',
			'heading' => __( 'Values', 'js_composer' ),
			'param_name' => 'values',
			'value' => urlencode( json_encode( array(
				array(
					'title' => __( 'One', 'js_composer' ),
					'y_values' => '10; 15; 20; 25; 27; 25; 23; 25',
					'color' => 'blue',
				),
				array(
					'title' => __( 'Two', 'js_composer' ),
					'y_values' => '25; 18; 16; 17; 20; 25; 30; 35',
					'color' => 'pink',
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Title', 'js_composer' ),
					'param_name' => 'title',
					'description' => __( 'Enter title for chart dataset.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Y-axis values', 'js_composer' ),
					'param_name' => 'y_values',
					'description' => __( 'Enter values for axis (Note: separate values with ";").', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Color', 'js_composer' ),
					'param_name' => 'color',
					'value' => getVcShared( 'colors-dashed' ),
					'description' => __( 'Select chart color.', 'js_composer' ),
					'param_holder_class' => 'vc_colored-dropdown',
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Custom color', 'js_composer' ),
					'param_name' => 'custom_color',
					'description' => __( 'Select custom chart color.', 'js_composer' ),
				),
			),
			'callbacks' => array(
				'after_add' => 'vcChartParamAfterAddCallback',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Animation', 'js_composer' ),
			'description' => __( 'Select animation style.', 'js_composer' ),
			'param_name' => 'animation',
			'value' => getVcShared( 'animation styles' ),
			'std' => 'easeInOutCubic',
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
