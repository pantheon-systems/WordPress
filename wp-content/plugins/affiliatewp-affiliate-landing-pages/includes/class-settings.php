<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Affiliate_Landing_Pages_Admin {

	/**
	 * Sets up the class.
	 *
	 * @access public
	 * @since  1.0
	 */
	public function __construct() {

		// Settings tab.
		add_filter( 'affwp_settings_tabs', array( $this, 'setting_tab' ) );

		// Settings.
		add_filter( 'affwp_settings', array( $this, 'register_settings' ) );

	}

	/**
	 * Register the new settings tab.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function setting_tab( $tabs ) {
		$tabs['affiliate-landing-pages'] = __( 'Affiliate Landing Pages', 'affiliatewp-affiliate-landing-pages' );
		return $tabs;
	}

	/**
	 * Register our settings.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function register_settings( $settings = array() ) {

		$settings['affiliate-landing-pages'] = array(
			'affiliate-landing-pages' => array(
				'name' => __( 'Enable', 'affiliatewp-affiliate-landing-pages' ),
				'desc' => __( 'Enable Affiliate Landing Pages. This will allow a page or post to be assigned to an affiliate.', 'affiliatewp-affiliate-landing-pages' ),
				'type' => 'checkbox'
			)
		);

		return $settings;

	}

}

new AffiliateWP_Affiliate_Landing_Pages_Admin();
