<?php

use AFFWP\Integrations\Opt_In;

class Affiliate_WP_Integrations {

	/**
	 * Holds the opt_in integration property.
	 *
	 * @since 2.2
	 */
	public $opt_in;

	/**
	 * Get things started.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {

		$this->load();

		$this->opt_in = new \AFFWP\Integrations\Opt_In;;

	}

	/**
	 * Retrieves an array of all supported integrations.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_integrations() {


		return apply_filters( 'affwp_integrations', array(
			'caldera-forms'  => 'Caldera Forms',
			'contactform7'   => 'Contact Form 7',
			'edd'            => 'Easy Digital Downloads',
			'formidablepro'  => 'Formidable Pro',
			'give'           => 'Give',
			'gravityforms'   => 'Gravity Forms',
			'exchange'       => 'ExchangeWP (iThemes Exchange)',
			'jigoshop'       => 'Jigoshop',
			'lifterlms'      => 'LifterLMS',
			'marketpress'    => 'MarketPress',
			'membermouse'    => 'MemberMouse',
			'memberpress'    => 'MemberPress',
			'ninja-forms'    => 'Ninja Forms',
			'optimizemember' => 'OptimizeMember',
			'paypal'         => 'PayPal Buttons',
			'pmp'            => 'Paid Memberships Pro',
			'pms'            => 'Paid Member Subscriptions',
			'rcp'            => 'Restrict Content Pro',
			's2member'       => 's2Member',
			'shopp'	         => 'Shopp',
			'sproutinvoices' => 'Sprout Invoices',
			'stripe'         => 'Stripe Checkout (through WP Simple Pay)',
			'woocommerce'    => 'WooCommerce',
			'wpeasycart'     => 'WP EasyCart',
			'wpec'           => 'WP eCommerce',
			'wpforms'        => 'WPForms',
			'wp-invoice'     => 'WP-Invoice',
			'zippycourses'   => 'Zippy Courses',
		) );

	}

	/**
	 * Retrieves an array of all enabled integrations.
	 *
	 * @since 2.2
	 *
	 * @return array The list of enabled integrations.
	 */
	public function get_enabled_integrations() {
		return affiliate_wp()->settings->get( 'integrations', array() );
	}

	/**
	 * Retrieves a map of all integration keys and their associated class names.
	 *
	 * @since 2.2
	 *
	 * @return array The list of integration classes.
	 */
	public function get_integration_classes() {

		return apply_filters( 'affwp_integration_classes', array(
			'contactform7'   => 'Affiliate_WP_Contact_Form_7',
			'edd'            => 'Affiliate_WP_EDD',
			'caldera-forms'  => 'Affiliate_WP_Caldera_Forms',
			'formidablepro'  => 'Affiliate_WP_Formidable_Pro',
			'give'           => 'Affiliate_WP_Give',
			'gravityforms'   => 'Affiliate_WP_Gravity_Forms',
			'exchange'       => 'Affiliate_WP_Exchange',
			'jigoshop'       => 'Affiliate_WP_Jigoshop',
			'lifterlms'      => 'Affiliate_WP_LifterLMS',
			'marketpress'    => 'Affiliate_WP_MarketPress',
			'membermouse'    => 'Affiliate_WP_Membermouse',
			'memberpress'    => 'Affiliate_WP_MemberPress',
			'ninja-forms'    => 'Affiliate_WP_Ninja_Forms',
			'optimizemember' => 'Affiliate_WP_OptimizeMember',
			'paypal'         => 'Affiliate_WP_PayPal',
			'pmp'            => 'Affiliate_WP_PMP',
			'pms'            => 'Affiliate_WP_PMS',
			'rcp'            => 'Affiliate_WP_RCP',
			's2member'       => 'Affiliate_WP_S2Member',
			'shopp'	         => 'Affiliate_WP_Shopp',
			'sproutinvoices' => 'Affiliate_WP_Sprout_Invoices',
			'stripe'         => 'Affiliate_WP_Stripe',
			'woocommerce'    => 'Affiliate_WP_WooCommerce',
			'wpeasycart'     => 'Affiliate_WP_EasyCart',
			'wpec'           => 'Affiliate_WP_WPEC',
			'wpforms'        => 'Affiliate_WP_WPForms',
			'wp-invoice'     => 'Affiliate_WP_Invoice',
			'zippycourses'   => 'Affiliate_WP_ZippyCourses',
		) );

	}

	/**
	 * Retrieves the class name for a specific integration
	 *
	 * @since 2.2
	 * @return bool|string
	 */
	public function get_integration_class( $integration = '' ) {

		if( array_key_exists( $integration, $this->get_integration_classes() ) ) {
			$integrations = $this->get_integration_classes();
			return $integrations[ $integration ];
		}

		return false;
	}

	/**
	 * Load integration classes for each enabled integration.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function load() {

		// Load each enabled integrations
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/class-base.php';

		$enabled = apply_filters( 'affwp_enabled_integrations', $this->get_enabled_integrations() );

		/**
		 * Fires immediately prior to AffiliateWP integrations being loaded.
		 */
		do_action( 'affwp_integrations_load' );

		foreach( $enabled as $filename => $integration ) {

			if( file_exists( AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/class-' . $filename . '.php' ) ) {
				require_once AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/class-' . $filename . '.php';
			}

		}

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/class-opt-in.php';

		/**
		 * Fires immediately after all AffiliateWP integrations are loaded.
		 */
		do_action( 'affwp_integrations_loaded' );

	}

}
