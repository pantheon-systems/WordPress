<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Progress Bar', 'js_composer' ),
	'base' => 'vc_progress_bar',
	'icon' => 'icon-wpb-graph',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Animated progress bar', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'param_group',
			'heading' => __( 'Values', 'js_composer' ),
			'param_name' => 'values',
			'description' => __( 'Enter values for graph - value, title and color.', 'js_composer' ),
			'value' => urlencode( json_encode( array(
				array(
					'label' => __( 'Development', 'js_composer' ),
					'value' => '90',
				),
				array(
					'label' => __( 'Design', 'js_composer' ),
					'value' => '80',
				),
				array(
					'label' => __( 'Marketing', 'js_composer' ),
					'value' => '70',
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Label', 'js_composer' ),
					'param_name' => 'label',
					'description' => __( 'Enter text used as title of bar.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Value', 'js_composer' ),
					'param_name' => 'value',
					'description' => __( 'Enter value of bar.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Color', 'js_composer' ),
					'param_name' => 'color',
					'value' => array(
							__( 'Default', 'js_composer' ) => '',
						) + array(
							__( 'Classic Grey', 'js_composer' ) => 'bar_grey',
							__( 'Classic Blue', 'js_composer' ) => 'bar_blue',
							__( 'Classic Turquoise', 'js_composer' ) => 'bar_turquoise',
							__( 'Classic Green', 'js_composer' ) => 'bar_green',
							__( 'Classic Orange', 'js_composer' ) => 'bar_orange',
							__( 'Classic Red', 'js_composer' ) => 'bar_red',
							__( 'Classic Black', 'js_composer' ) => 'bar_black',
						) + getVcShared( 'colors-dashed' ) + array(
							__( 'Custom Color', 'js_composer' ) => 'custom',
						),
					'description' => __( 'Select single bar background color.', 'js_composer' ),
					'admin_label' => true,
					'param_holder_class' => 'vc_colored-dropdown',
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Custom color', 'js_composer' ),
					'param_name' => 'customcolor',
					'description' => __( 'Select custom single bar background color.', 'js_composer' ),
					'dependency' => array(
						'element' => 'color',
						'value' => array( 'custom' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Custom text color', 'js_composer' ),
					'param_name' => 'customtxtcolor',
					'description' => __( 'Select custom single bar text color.', 'js_composer' ),
					'dependency' => array(
						'element' => 'color',
						'value' => array( 'custom' ),
					),
				),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Units', 'js_composer' ),
			'param_name' => 'units',
			'description' => __( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Color', 'js_composer' ),
			'param_name' => 'bgcolor',
			'value' => array(
					__( 'Classic Grey', 'js_composer' ) => 'bar_grey',
					__( 'Classic Blue', 'js_composer' ) => 'bar_blue',
					__( 'Classic Turquoise', 'js_composer' ) => 'bar_turquoise',
					__( 'Classic Green', 'js_composer' ) => 'bar_green',
					__( 'Classic Orange', 'js_composer' ) => 'bar_orange',
					__( 'Classic Red', 'js_composer' ) => 'bar_red',
					__( 'Classic Black', 'js_composer' ) => 'bar_black',
				) + getVcShared( 'colors-dashed' ) + array(
					__( 'Custom Color', 'js_composer' ) => 'custom',
				),
			'description' => __( 'Select bar background color.', 'js_composer' ),
			'admin_label' => true,
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Bar custom background color', 'js_composer' ),
			'param_name' => 'custombgcolor',
			'description' => __( 'Select custom background color for bars.', 'js_composer' ),
			'dependency' => array(
				'element' => 'bgcolor',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Bar custom text color', 'js_composer' ),
			'param_name' => 'customtxtcolor',
			'description' => __( 'Select custom text color for bars.', 'js_composer' ),
			'dependency' => array(
				'element' => 'bgcolor',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Options', 'js_composer' ),
			'param_name' => 'options',
			'value' => array(
				__( 'Add stripes', 'js_composer' ) => 'striped',
				__( 'Add animation (Note: visible only with striped bar).', 'js_composer' ) => 'animated',
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
