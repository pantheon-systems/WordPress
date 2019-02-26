<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$colors_arr = vc_colors_arr();
$size_arr = vc_size_arr();
$icons_arr = vc_icons_arr();
return array(
	'name' => __( 'Old Button', 'js_composer' ) . ' 1',
	'base' => 'vc_button',
	'icon' => 'icon-wpb-ui-button',
	'category' => __( 'Content', 'js_composer' ),
	'deprecated' => '4.5',
	'content_element' => false,
	'description' => __( 'Eye catching button', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Text', 'js_composer' ),
			'holder' => 'button',
			'class' => 'wpb_button',
			'param_name' => 'title',
			'value' => __( 'Text on the button', 'js_composer' ),
			'description' => __( 'Enter text on the button.', 'js_composer' ),
		),
		array(
			'type' => 'href',
			'heading' => __( 'URL (Link)', 'js_composer' ),
			'param_name' => 'href',
			'description' => __( 'Enter button link.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Target', 'js_composer' ),
			'param_name' => 'target',
			'value' => vc_target_param_list(),
			'dependency' => array(
				'element' => 'href',
				'not_empty' => true,
				'callback' => 'vc_button_param_target_callback',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'value' => $colors_arr,
			'description' => __( 'Select button color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Icon', 'js_composer' ),
			'param_name' => 'icon',
			'value' => $icons_arr,
			'description' => __( 'Select icon to display on button.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Size', 'js_composer' ),
			'param_name' => 'size',
			'value' => $size_arr,
			'description' => __( 'Select button size.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
	'js_view' => 'VcButtonView',
);
