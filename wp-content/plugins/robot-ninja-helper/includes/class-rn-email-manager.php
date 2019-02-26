<?php
/**
 * Robot Ninja Email Manager class
 *
 * @author 	Prospress
 * @since 	1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Email_Manager {

	/**
	 * Initialise Robot Ninja Helper Email Manager class
	 *
	 * @since 1.8.0
	 */
	public static function init() {
		if ( defined( 'RN_SEND_NEW_ORDER_EMAIL' ) && ! RN_SEND_NEW_ORDER_EMAIL ) {
			add_filter( 'woocommerce_email_enabled_new_order', __CLASS__ . '::disable_sending_rn_emails', 10, 2 );
		}

		if ( defined( 'RN_SEND_FAILED_ORDER_EMAIL' ) && ! RN_SEND_FAILED_ORDER_EMAIL ) {
			add_filter( 'woocommerce_email_enabled_failed_order', __CLASS__ . '::disable_sending_rn_emails', 10, 2 );
		}

		if ( defined( 'RN_SEND_CANCELLED_ORDER_EMAIL' ) && ! RN_SEND_CANCELLED_ORDER_EMAIL ) {
			add_filter( 'woocommerce_email_enabled_cancelled_order', __CLASS__ . '::disable_sending_rn_emails', 10, 2 );
		}

		if ( defined( 'RN_SEND_CANCELLED_SUBSCRIPTION_EMAIL' ) && ! RN_SEND_CANCELLED_SUBSCRIPTION_EMAIL ) {
			add_filter( 'woocommerce_email_enabled_cancelled_subscription', __CLASS__ . '::disable_sending_rn_emails', 10, 2 );
		}
	}

	/**
	 * Make sure we don't send emails for Robot Ninja test orders if the store
	 * has set the RN_SEND_{EMAIL_TYPE}_EMAIL set to false in wp-config.
	 *
	 * @since 1.8.0
	 * @param bool $email_enabled
	 * @param WC_Order|WC_Subscription $object
	 * @return bool
	 */
	public static function disable_sending_rn_emails( $email_enabled, $object ) {
		if ( $email_enabled && ( is_a( $object, 'WC_Order' ) || is_a( $object, 'WC_Subscription' ) ) ) {
			$customer_email = $object->get_billing_email();

			if ( $customer_email && preg_match( '/store[\+]guest[\-](\d+)[\@]robotninja.com/', $customer_email ) || preg_match( '/store[\+](\d+)[\@]robotninja.com/', $customer_email ) ) {
				$email_enabled = false;
			}
		}

		return $email_enabled;
	}
}
RN_Email_Manager::init();
