<?php

/**
 * Robot Ninja Avada Theme Support Class
 *
 * @author 	Prospress
 * @since 	1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Avada {

	/**
	 * Initialise the Avada Theme support class
	 *
	 * @since 1.7.0
	 */
	public static function init() {
		add_filter( 'rn_helper_theme_settings', __CLASS__ . '::add_theme_settings', 10, 2 );
	}

	/**
	 * Check if the store is using the Avada theme (by checking the current theme name and the "Template:" value in the child theme's style.css file)
	 * and attach the theme settings to the robot_ninja_data in system status
	 *
	 * @since 1.7.0
	 * @param array $theme_settings - stores all the store theme settings that WALL-E needs to know about
	 * @param WP_Theme $current_theme
	 * @return array
	 */
	public static function add_theme_settings( $theme_settings, $current_theme ) {
		if ( $current_theme && ( 'Avada' == $current_theme->name || 'Avada' == $current_theme->get( 'Template' ) ) ) {
			$avada_settings = get_option( 'fusion_options', false );

			if ( $avada_settings ) {
				$theme_settings['avada']['one_page_checkout_enabled'] = isset( $avada_settings['woocommerce_one_page_checkout'] ) && ( '1' == $avada_settings['woocommerce_one_page_checkout'] ) ? true : false;
			}
		}

		return $theme_settings;
	}
}
RN_Avada::init();
