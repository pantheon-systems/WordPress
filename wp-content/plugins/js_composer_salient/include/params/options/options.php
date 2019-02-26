<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @since 4.2
 * @return string
 */
function vc_options_form_field( $settings, $value ) {
	return '<div class="vc_options">'
	       . '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value  ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="' . $value . '"/>'
	       . '<a href="#" class="button vc_options-edit ' . $settings['param_name'] . '_button">' . __( 'Manage options', 'js_composer' ) . '</a>'
	       . '</div><div class="vc_options-fields" data-settings="' . htmlspecialchars( json_encode( $settings['options'] ) ) . '"><a href="#" class="button vc_close-button">' . __( 'Close', 'js_composer' ) . '</a></div>';
}

/**
 * @since 4.2
 */
function vc_options_include_templates() {
	require_once vc_path_dir( 'TEMPLATES_DIR', 'params/options/templates.html' );
}

add_action( 'admin_footer', 'vc_options_include_templates' );
