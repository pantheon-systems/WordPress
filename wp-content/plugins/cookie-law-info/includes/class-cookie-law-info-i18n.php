<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://cookielawinfo.com/
 * @since      1.6.6
 *
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.6.6
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Cookie_Law_Info_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.6.6
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'cookie-law-info',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
