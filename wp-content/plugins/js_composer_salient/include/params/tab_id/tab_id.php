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
function vc_tab_id_form_field( $settings, $value ) {

	return '<div class="my_param_block">'
	       . '<input name="' . $settings['param_name']
	       . '" class="wpb_vc_param_value wpb-textinput '
	       . $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="'
	       . $value . '" />'
	       . '<label>' . $value . '</label>'
	       . '</div>';
}
