<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_PayPal_Payouts_Admin {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		add_filter( 'affwp_settings_tabs', array( $this, 'setting_tab' ) );
		add_filter( 'affwp_settings',      array( $this, 'settings'    ) );
	}

	/**
	 * Register the new settings tab
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function setting_tab( $tabs ) {
		$tabs['paypal'] = __( 'PayPal Payouts', 'affwp-paypal-payouts' );
		return $tabs;
	}

	/**
	 * Register the settings for our paypal Payouts tab
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function settings( $settings ) {

		$settings['paypal'] = array(
			'paypal_payout_mode' => array(
				'name' => __( 'Payout API to Use', 'affwp-paypal-payouts' ),
				'desc' => __( 'Select the payout method you wish to use. PayPal MassPay is an older technology not available to all accounts. See <a href="http://docs.affiliatewp.com/category/750-paypal-payouts" target="_blank">documentation</a> for assistance', 'affwp-paypal-payouts' ),
				'type' => 'select',
				'options' => array(
					'api'     => __( 'API Application', 'affwp-paypal-payouts' ),
					'masspay' => __( 'MassPay', 'affwp-paypal-payouts' )
				)
			),
			'paypal_test_mode' => array(
				'name' => __( 'Test Mode', 'affwp-paypal-payouts' ),
				'desc' => __( 'Check this box if you would like to use PayPal Payouts in Test Mode', 'affwp-paypal-payouts' ),
				'type' => 'checkbox'
			),
			'paypal_api_header' => array(
				'name' => __( 'PayPal API Application Credentials', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your PayPal API Application credentials.', 'affwp-paypal-payouts' ),
				'type' => 'header'
			),
			'paypal_live_client_id' => array(
				'name' => __( 'Client ID', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your PayPal Application\'s Client ID. Create or retrieve these from <a href="https://developer.paypal.com/developer/applications/" target="_blank">PayPal\'s Developer portal</a>', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_live_secret' => array(
				'name' => __( 'Secret', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your PayPal Application\'s Secret', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_test_client_id' => array(
				'name' => __( 'Test Client ID', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Sandbox PayPal Application\'s Client ID.', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_test_secret' => array(
				'name' => __( 'Test Secret', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Sandbox PayPal Application\'s Secret', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_masspay_header' => array(
				'name' => __( 'PayPal MassPay Credentials', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Test API Username', 'affwp-paypal-payouts' ),
				'type' => 'header'
			),
			'paypal_test_username' => array(
				'name' => __( 'Test API Username', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Test API Username', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_test_password' => array(
				'name' => __( 'Test API Password', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Test API Password', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_test_signature' => array(
				'name' => __( 'Test API Signature', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Test API Signature', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_live_username' => array(
				'name' => __( 'Live API Username', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Live API Username', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_live_password' => array(
				'name' => __( 'Live API Password', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Live API Password', 'affwp-paypal-payouts' ),
				'type' => 'text'
			),
			'paypal_live_signature' => array(
				'name' => __( 'Live API Signature', 'affwp-paypal-payouts' ),
				'desc' => __( 'Enter your Live API Signature', 'affwp-paypal-payouts' ),
				'type' => 'text'
			)
		);

		return $settings;
	}

}
new AffiliateWP_PayPal_Payouts_Admin;