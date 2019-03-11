<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://elixinol.com
 * @since      1.0.0
 *
 * @package    Elix_Charity_Report
 * @subpackage Elix_Charity_Report/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Elix_Charity_Report
 * @subpackage Elix_Charity_Report/includes
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_Charity_Report_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'elix-charity-report',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
