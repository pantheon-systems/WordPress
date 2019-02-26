<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Hidden field param.
 *
 * @param $settings
 * @param $value
 *
 * @since 4.5
 * @return string - html string.
 */
function vc_hidden_form_field( $settings, $value ) {
	$value = htmlspecialchars( $value );

	return '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value vc_hidden-field vc_param-name-' . $settings['param_name'] . ' ' . $settings['type'] . '" type="hidden" value="' . $value . '"/>';
}

/**
 * Remove content before hidden field type input.
 *
 * @param $output
 *
 * @since 4.5
 *
 * @return string
 */
function vc_edit_form_fields_render_field_hidden_before( $output ) {
	return '<div class="vc_column vc_edit-form-hidden-field-wrapper">';
}

/**
 * Remove content after hidden field type input.
 *
 * @param $output
 *
 * @since 4.5
 *
 * @return string
 */
function vc_edit_form_fields_render_field_hidden_after( $output ) {
	return '</div>';
}
