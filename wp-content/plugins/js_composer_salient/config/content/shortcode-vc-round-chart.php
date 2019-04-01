<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Round Chart', 'js_composer' ),
	'base' => 'vc_round_chart',
	'class' => '',
	'icon' => 'icon-wpb-vc-round-chart',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Pie and Doughnut charts', 'js_composer' ),
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
				__( 'Pie', 'js_composer' ) => 'pie',
				__( 'Doughnut', 'js_composer' ) => 'doughnut',
			),
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
			'type' => 'dropdown',
			'heading' => __( 'Gap', 'js_composer' ),
			'param_name' => 'stroke_width',
			'value' => array(
				0 => 0,
				1 => 1,
				2 => 2,
				5 => 5,
			),
			'description' => __( 'Select gap size.', 'js_composer' ),
			'std' => 2,
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Outline color', 'js_composer' ),
			'param_name' => 'stroke_color',
			'value' => getVcShared( 'colors-dashed' ) + array( __( 'Custom', 'js_composer' ) => 'custom' ),
			'description' => __( 'Select outline color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
			'std' => 'white',
			'dependency' => array(
				'element' => 'stroke_width',
				'value_not_equal_to' => '0',
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Custom outline color', 'js_composer' ),
			'param_name' => 'custom_stroke_color',
			'description' => __( 'Select custom outline color.', 'js_composer' ),
			'dependency' => array(
				'element' => 'stroke_color',
				'value' => array( 'custom' ),
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
			'type' => 'param_group',
			'heading' => __( 'Values', 'js_composer' ),
			'param_name' => 'values',
			'value' => urlencode( json_encode( array(
				array(
					'title' => __( 'One', 'js_composer' ),
					'value' => '60',
					'color' => 'blue',
				),
				array(
					'title' => __( 'Two', 'js_composer' ),
					'value' => '40',
					'color' => 'pink',
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Title', 'js_composer' ),
					'param_name' => 'title',
					'description' => __( 'Enter title for chart area.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Value', 'js_composer' ),
					'param_name' => 'value',
					'description' => __( 'Enter value for area.', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Color', 'js_composer' ),
					'param_name' => 'color',
					'value' => getVcShared( 'colors-dashed' ),
					'description' => __( 'Select area color.', 'js_composer' ),
					'param_holder_class' => 'vc_colored-dropdown',
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Custom color', 'js_composer' ),
					'param_name' => 'custom_color',
					'description' => __( 'Select custom area color.', 'js_composer' ),
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
