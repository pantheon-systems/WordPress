<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $vc_params_preset_form_field_js_appended;
$vc_params_preset_form_field_js_appended = false;
/**
 * Params preset shortcode attribute type generator.
 *
 * Allows to set list of attributes which will be
 *
 * @param $settings
 * @param $value
 *
 * @since 4.4
 * @return string - html string.
 */
function vc_params_preset_form_field( $settings, $value ) {
	$output = '';
	$output .= '<select name="'
	           . $settings['param_name']
	           . '" class="wpb_vc_param_value vc_params-preset-select '
	           . $settings['param_name']
	           . ' ' . $settings['type']
	           . '">';
	foreach ( $settings['options'] as $option ) {
		$selected = '';
		if ( isset( $option['value'] ) ) {
			$option_value_string = (string) $option['value'];
			$value_string = (string) $value;
			if ( '' !== $value && $option_value_string === $value_string ) {
				$selected = ' selected';
			}
			$output .= '<option class="vc_params-preset-' . $option['value']
			           . '" value="' . esc_attr( $option['value'] )
			           . '"' . $selected
			           . ' data-params="' . esc_attr( json_encode( $option['params'] ) ) . '">'
			           . esc_html( isset( $option['label'] ) ? $option['label'] : $option['value'] ) . '</option>';
		}
	}
	$output .= '</select>';

	return $output;
}
