<?php
/**
 * Robot Ninja Helper Gateways class
 *
 * @author 	Prospress
 * @since 	1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Gateways_Settings {

	/**
	 * @var array $gateway_setting_ids - array of gateway IDs that the RN Helper plugin will return an array of settings for
	 */
	public static $gateway_setting_ids = array(
		'stripe',
		'authorize_net_cim_credit_card',
		'braintree_credit_card',
		'moneris',
		'intuit_payments_credit_card',
	);

	/**
	 * Returns an array of all the settings needed for our gateway support classes in Robot Ninja.
	 *
	 * @since 1.3.0
	 * @return array
	 */
	public static function get_enabled_gateway_settings() {
		$gateway_settings   = array();
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		foreach ( self::$gateway_setting_ids as $gateway_id ) {
			// check if the store has any gateways that we provide support for
			if ( ! empty( $available_gateways[ $gateway_id ] ) ) {
				$gateway = $available_gateways[ $gateway_id ];

				if ( ! empty( $gateway->settings['enabled'] ) && 'yes' == $gateway->settings['enabled'] ) {
					if ( method_exists( __CLASS__, "get_{$gateway_id}_settings" ) ) {
						$gateway_settings[ $gateway_id ] = call_user_func( array( __CLASS__, "get_{$gateway_id}_settings" ), $gateway );
					} else {
						$gateway_settings[ $gateway_id ] = self::get_standard_gateway_settings( $gateway );
					}
				}
			}
		}

		return $gateway_settings;
	}

	/**
	* Return standard gateway settings to be used by Robot Ninja.
	* This standard is based off how Skyverge save the settings for all their gateways
	*
	* @since 1.3.0
	* @param WC_Gateway $gateway
	* @param array
	*/
	public static function get_standard_gateway_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['environment'] ) && 'test' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return Stripe settings for Robot Ninja
	 *
	 * @since 1.3.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_stripe_settings( $gateway ) {
		return array(
			'testmode'           => ( ! empty( $gateway->testmode ) ) ? $gateway->testmode : false,
			'payment_form_type'  => ( ! empty( $gateway->stripe_checkout ) && $gateway->stripe_checkout ) ? 'iframe' : 'checkout-form',
			'inline_cc_elements' => ( ! empty( $gateway->inline_cc_form ) ) ? $gateway->inline_cc_form : false,
			'transaction_type'   => ( ! empty( $gateway->capture ) && true == $gateway->capture ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return Braintree Credit Card settings for Robot Ninja
	 *
	 * @since 1.3.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_braintree_credit_card_settings( $gateway ) {
		return array(
			'testmode'                  => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'          => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'card_verification_enabled' => ( ! empty( $gateway->settings['require_csc'] ) && 'yes' == $gateway->settings['require_csc'] ) ? true : false,
		);
	}

	/**
	 * Return Moneris settings in System Status report for Robot Ninja
	 *
	 * @since 1.3.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_moneris_settings( $gateway ) {
		return array(
			'testmode'                     => ( ! empty( $gateway->settings['environment'] ) && 'test' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'             => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'hosted_tokenization'          => ( ! empty( $gateway->settings['hosted_tokenization'] ) && 'yes' == $gateway->settings['hosted_tokenization'] ) ? true : false,
			'card_verification_enabled'    => ( ! empty( $gateway->settings['enable_csc'] ) && 'yes' == $gateway->settings['enable_csc'] ) ? true : false,
		);
	}

	/**
	 * Return Intuit Payments Credit Card settings for Robot Ninja
	 *
	 * @since 1.4.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_intuit_payments_credit_card_settings( $gateway ) {
		return array(
			'testmode'                  => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'          => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'tokenization'              => ( ! empty( $gateway->settings['tokenization'] ) && 'yes' == $gateway->settings['tokenization'] ) ? true : false,
			'card_verification_enabled' => ( ! empty( $gateway->settings['enable_csc'] ) && 'yes' == $gateway->settings['enable_csc'] ) ? true : false,
		);
	}
}
