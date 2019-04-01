<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Helper function to register new shortcode attribute hook.
 *
 * @param $name - attribute name
 * @param $form_field_callback - hook, will be called when settings form is shown and attribute added to shortcode
 *     param list
 * @param $script_url - javascript file url which will be attached at the end of settings form.
 *
 * @since 4.2
 * @deprecated due to without prefix name 4.4
 * @return bool
 */
function add_shortcode_param( $name, $form_field_callback, $script_url = null ) {
	_deprecated_function( 'add_shortcode_param', '4.4 (will be removed in 6.0)', 'vc_add_shortcode_param' );

	return vc_add_shortcode_param( $name, $form_field_callback, $script_url );
}

/**
 * @deprecated 4.2
 * @since 4.2
 * @return mixed|string|void
 */
function get_row_css_class() {
	_deprecated_function( 'get_row_css_class', '4.2 (will be removed in 6.0)' );
	$custom = vc_settings()->get( 'row_css_class' );

	return ! empty( $custom ) ? $custom : 'vc_row-fluid';
}

/* Backwards compatibility  **/
/**
 * @param $attributes
 *
 * @deprecated, use vc_map instead
 */
function wpb_map( $attributes ) {
	_deprecated_function( 'wpb_map', '4.2 (will be removed in 6.0)', 'vc_map' );

	vc_map( $attributes );
}

/**
 * @deprecated 5.2
 * @return string
 */
function vc_generate_dependencies_attributes() {
	_deprecated_function( 'vc_generate_dependencies_attributes', '5.1', '' );

	return '';
}
