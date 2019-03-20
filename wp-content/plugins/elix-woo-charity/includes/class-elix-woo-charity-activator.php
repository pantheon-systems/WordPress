<?php

/**
 * Fired during plugin activation
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/includes
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_Woo_Charity_Activator {

	/**
	 * Add options.
	 *
	 * This is a temporary measure until a settings page is created to allow customizing the options.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// TODO: Move these options to a settings page
		
		$label = __('Please select which organization you would like us to donate 5% of your purchase to on your behalf', 'elix-woo-charity');
		$placeholder = __('Select a charity', 'elix-woo-charity');

		if ( is_multisite() && 36 == get_current_blog_id() ) {
			$field_select_options = array(
//				'Charity Name',
			);
		}
		else {
			$field_select_options = array(
				'Realm of Caring',
				'Autism One',
				'Vote Hemp',
				'Concussion Legacy Foundation ',
				'American Brain Tumor Association',
				'The Cancer Cure Foundation',
				'Wounded Warrior Project',
			);
		}

		update_option( 'elix-woo-charity-label', $label );
		update_option( 'elix-woo-charity-placeholder', $placeholder );
		update_option( 'elix-woo-charity-options', $field_select_options );

		if( !wp_next_scheduled( 'elix_woo_charity_cronjob' ) ) {
			wp_schedule_event( time(), 'hourly', 'elix_woo_charity_cronjob' );
		}

	}

}
