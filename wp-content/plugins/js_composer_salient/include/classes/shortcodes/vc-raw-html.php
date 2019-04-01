<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 */
class WPBakeryShortCode_VC_Raw_html extends WPBakeryShortCode {

	public function singleParamHtmlHolder( $param, $value ) {
		$output = '';
		// Compatibility fixes
		// TODO: check $old_names & &new_names. Leftover from copypasting?
		$old_names = array(
			'yellow_message',
			'blue_message',
			'green_message',
			'button_green',
			'button_grey',
			'button_yellow',
			'button_blue',
			'button_red',
			'button_orange',
		);
		$new_names = array(
			'alert-block',
			'alert-info',
			'alert-success',
			'btn-success',
			'btn',
			'btn-info',
			'btn-primary',
			'btn-danger',
			'btn-warning',
		);
		$value = str_ireplace( $old_names, $new_names, $value );

		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';

		if ( isset( $param['holder'] ) && 'hidden' !== $param['holder'] ) {
			if ( 'textarea_raw_html' === $param['type'] ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . htmlentities( rawurldecode( base64_decode( strip_tags( $value ) ) ), ENT_COMPAT, 'UTF-8' ) . '</' . $param['holder'] . '><input type="hidden" name="' . $param_name . '_code" class="' . $param_name . '_code" value="' . strip_tags( $value ) . '" />';
			} else {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
		}

		return $output;
	}
}
