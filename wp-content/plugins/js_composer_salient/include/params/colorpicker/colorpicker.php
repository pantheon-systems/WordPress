<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Param 'colorpicker' field
 *
 * @param $settings
 * @param $value
 *
 * @since 4.4
 * @return string
 */
function vc_colorpicker_form_field( $settings, $value ) {
	/* nectar addition */
	$nectar_accent_color = '#dd3333';
	$nectar_extra_color_1 = '#dd9933';
	$nectar_extra_color_2 = '#eeee22';
	$nectar_extra_color_3 = '#81d742';
	
	if (function_exists('get_nectar_theme_options')) {
		$options = get_nectar_theme_options(); 

		if( !empty($options["accent-color"]) ) $nectar_accent_color = $options["accent-color"];
		if( !empty($options["extra-color-1"]) ) $nectar_extra_color_1 = $options["extra-color-1"];
		if( !empty($options["extra-color-2"]) ) $nectar_extra_color_2 = $options["extra-color-2"];
		if( !empty($options["extra-color-3"]) ) $nectar_extra_color_3 = $options["extra-color-3"];
	}
		
	return '<div class="color-group">'
				 . '<input name="' . $settings['param_name'] . '" data-color-1="'.$nectar_accent_color.'" data-color-2="'.$nectar_extra_color_1.'" data-color-3="'.$nectar_extra_color_2.'" data-color-4="'.$nectar_extra_color_3.'" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'] . ' ' . $settings['type'] . '_field vc_color-control" type="text" value="' . $value . '"/>'
				 . '</div>';
			 
	/* nectar addition end */
}
