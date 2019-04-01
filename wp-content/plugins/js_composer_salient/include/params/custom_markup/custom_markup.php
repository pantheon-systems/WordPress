<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 * @since 4.4
 *
 * @param $settings
 * @param $value
 * @param $tag
 *
 * vc_filter: vc_custom_markup_render_filter - hook to override custom markup for field
 *
 * @return mixed|void rendered template for params in edit form
 *
 */
function vc_custom_markup_form_field( $settings, $value, $tag ) {

	return apply_filters( 'vc_custom_markup_render_filter', $value, $settings, $tag );
}

// Example
/*
  array(
		    'param_name' => 'hidden_markup1', // all params must have a unique name
		    'type' => 'custom_markup', // this param type
		    'description' => __( 'Enter your content..', 'js_composer' ), // some description if needed
			'value' => '<div style="background:red;width:100%;height:40px">aaa</div>', // your custom markup
  ),
 */
