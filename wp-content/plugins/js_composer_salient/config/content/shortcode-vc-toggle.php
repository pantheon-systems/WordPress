<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$cta_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'custom_', __( 'Heading', 'js_composer' ), array(
	'exclude' => array(
		'source',
		'text',
		'css',
		'link',
	),
), array(
	'element' => 'use_custom_heading',
	'value' => 'true',
) );

$params = array_merge( array(
	array(
		'type' => 'textfield',
		'holder' => 'h4',
		'class' => 'vc_toggle_title',
		'heading' => __( 'Toggle title', 'js_composer' ),
		'param_name' => 'title',
		'value' => __( 'Toggle title', 'js_composer' ),
		'description' => __( 'Enter title of toggle block.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-9',
	),
	array(
		'type' => 'checkbox',
		'heading' => __( 'Use custom font?', 'js_composer' ),
		'param_name' => 'use_custom_heading',
		'description' => __( 'Enable Google fonts.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-3',
	),
	array(
		'type' => 'textarea_html',
		'holder' => 'div',
		'class' => 'vc_toggle_content',
		'heading' => __( 'Toggle content', 'js_composer' ),
		'param_name' => 'content',
		'value' => __( '<p>Toggle content goes here, click edit button to change this text.</p>', 'js_composer' ),
		'description' => __( 'Toggle block content.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Style', 'js_composer' ),
		'param_name' => 'style',
		'value' => getVcShared( 'toggle styles' ),
		'description' => __( 'Select toggle design style.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Icon color', 'js_composer' ),
		'param_name' => 'color',
		'value' => array( __( 'Default', 'js_composer' ) => 'default' ) + getVcShared( 'colors' ),
		'description' => __( 'Select icon color.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown',
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Size', 'js_composer' ),
		'param_name' => 'size',
		'value' => array_diff_key( getVcShared( 'sizes' ), array( 'Mini' => '' ) ),
		'std' => 'md',
		'description' => __( 'Select toggle size', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Default state', 'js_composer' ),
		'param_name' => 'open',
		'value' => array(
			__( 'Closed', 'js_composer' ) => 'false',
			__( 'Open', 'js_composer' ) => 'true',
		),
		'description' => __( 'Select "Open" if you want toggle to be open by default.', 'js_composer' ),
	),
	vc_map_add_css_animation(),
	array(
		'type' => 'el_id',
		'heading' => __( 'Element ID', 'js_composer' ),
		'param_name' => 'el_id',
		'description' => sprintf( __( 'Enter optional ID. Make sure it is unique, and it is valid as w3c specification: %s (Must not have spaces)', 'js_composer' ), '<a target="_blank" href="http://www.w3schools.com/tags/att_global_id.asp">' . __( 'link', 'js_composer' ) . '</a>' ),
	),
	array(
		'type' => 'textfield',
		'heading' => __( 'Extra class name', 'js_composer' ),
		'param_name' => 'el_class',
		'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
	),
), $cta_custom_heading, array(
	array(
		'type' => 'css_editor',
		'heading' => __( 'CSS box', 'js_composer' ),
		'param_name' => 'css',
		'group' => __( 'Design Options', 'js_composer' ),
	),
) );

return array(
	'name' => __( 'FAQ', 'js_composer' ),
	'base' => 'vc_toggle',
	'icon' => 'icon-wpb-toggle-small-expand',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Toggle element for Q&A block', 'js_composer' ),
	'params' => $params,
	'js_view' => 'VcToggleView',
);
