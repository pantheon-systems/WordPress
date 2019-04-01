<?php

class Affiliate_WP_Lifetime_Commissions_PayPal extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.3
	 */
	public function init() {

		$this->context = 'paypal';

		add_action( 'init', array( $this, 'insert_referral_ipn' ) );

	}

	/**
	 * Insert a referral for the lifetime commission
	 *
	 * @access  public
	 * @since   1.3
	 * @return  string
	 */
	public function insert_referral_ipn() {

		if ( empty( $_GET['affwp-listener'] ) || 'paypal' !== strtolower( $_GET['affwp-listener'] ) ) {
			return;
		}

		$ipn_data = $_POST;

		if ( ! is_array( $ipn_data ) ) {
			wp_parse_str( $ipn_data, $ipn_data );
		}

		$verified = $this->verify_ipn( $ipn_data );

		if ( ! $verified ) {

			affiliate_wp()->utils->log( 'IPN verification failed during insert_referral_ipn()' );

			die( 'IPN verification failed' );

		}

		$to_process = array(
			'web_accept',
			'cart',
			'subscr_payment',
			'express_checkout',
			'recurring_payment',
			'recurring_payment_outstanding_payment',
		);

		if ( ! empty( $ipn_data['txn_type'] ) && ! in_array( $ipn_data['txn_type'], $to_process ) ) {
			return;
		}

		if ( empty( $ipn_data['mc_gross'] ) ) {

			affiliate_wp()->utils->log( 'IPN not processed during insert_referral_ipn() because mc_gross was empty' );

			return;

		}

		if ( empty( $ipn_data['payer_email'] ) ) {

			affiliate_wp()->utils->log( 'IPN not processed during insert_referral_ipn() because payer_email was empty' );

			return;

		}

		$reference = sanitize_text_field( $ipn_data['txn_id'] );
		$referral  = affiliate_wp()->referrals->get_by( 'reference', $reference );

		if ( $referral ) {

			affiliate_wp()->utils->log( 'IPN not processed during insert_referral_ipn() because referral already exist' );

			return;
		}

		$total       = sanitize_text_field( $ipn_data['mc_gross'] );
		$payer_email = sanitize_text_field( $ipn_data['payer_email'] );

		$customer = affwp_get_customer( $payer_email );

		if ( $customer ) {

			$this->filter_affiliate_rates();

			$integration = new Affiliate_WP_PayPal;

			$amount      = $integration->calculate_referral_amount( $total, $reference );
			$description = ! empty( $ipn_data['item_name'] ) ? sanitize_text_field( $ipn_data['item_name'] ) : $payer_email;

			$referral_id = $integration->insert_pending_referral( $amount, $reference, $description );

			if ( $referral_id ) {

				affiliate_wp()->utils->log( 'Pending referral created successfully during insert_referral_ipn()' );

				if ( 'completed' === strtolower( $ipn_data['payment_status'] ) ) {

					if ( 'pending' !== $referral->status ) {

						$completed = $integration->complete_referral( $reference );

						if ( $completed ) {

							affiliate_wp()->utils->log( 'Referral completed successfully during insert_referral_ipn()' );

						}
					}

				}

				affiliate_wp()->referrals->update( $referral_id, array( 'customer_id' => $customer->customer_id ) );

			}

		}

	}

	/**
	 * Retrieve the email address of a customer from the PayPal IPN
	 *
	 * @access  public
	 * @since   1.3
	 * @return  string
	 */
	public function get_email( $reference = '' ) {

		if ( empty( $_GET['affwp-listener'] ) || 'paypal' !== strtolower( $_GET['affwp-listener'] ) ) {
			return;
		}

		$email = '';

		if ( isset( $_POST['payer_email'] ) && ! empty( $_POST['payer_email'] ) ) {
			$email = sanitize_text_field( $_POST['payer_email'] );
		}

		return $email;

	}

	/**
	 * Verify IPN from PayPal
	 *
	 * @access  public
	 * @since   1.3
	 * @return  bool True|false
	 */
	private function verify_ipn( $post_data ) {

		$verified = false;
		$endpoint = array_key_exists( 'test_ipn', $post_data ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
		$args     = wp_unslash( array_merge( array( 'cmd' => '_notify-validate' ), $post_data ) );

		affiliate_wp()->utils->log( 'Data passed to verify_ipn(): ' . print_r( $post_data, true ) );
		affiliate_wp()->utils->log( 'Data to be sent to IPN verification: ' . print_r( $args, true ) );

		$request = wp_remote_post( $endpoint, array(
			'timeout'     => 45,
			'sslverify'   => false,
			'httpversion' => '1.1',
			'body'        => $args
		) );

		$body    = wp_remote_retrieve_body( $request );
		$code    = wp_remote_retrieve_response_code( $request );
		$message = wp_remote_retrieve_response_message( $request );

		if ( ! is_wp_error( $request ) && 200 === (int) $code && 'OK' == $message ) {

			if ( 'VERIFIED' == strtoupper( $body ) ) {

				$verified = true;

				affiliate_wp()->utils->log( 'IPN successfully verified' );

			} else {

				affiliate_wp()->utils->log( 'IPN response came back as INVALID' );

			}

		} else {

			affiliate_wp()->utils->log( 'IPN verification request failed' );
			affiliate_wp()->utils->log( 'Request: ' . print_r( $request, true ) );

		}

		return $verified;
	}

}

new Affiliate_WP_Lifetime_Commissions_PayPal;
