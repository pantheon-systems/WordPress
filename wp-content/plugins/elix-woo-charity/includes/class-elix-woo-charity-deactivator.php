<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/includes
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_Woo_Charity_Deactivator {

	/**
	 * Delete options.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'elix-woo-charity-label' );
		delete_option( 'elix-woo-charity-placeholder' );
		delete_option( 'elix-woo-charity-options' );

		$timestamp = wp_next_scheduled('elix_woo_charity_cronjob');
		wp_unschedule_event($timestamp, 'elix_woo_charity_cronjob');
	}

}
