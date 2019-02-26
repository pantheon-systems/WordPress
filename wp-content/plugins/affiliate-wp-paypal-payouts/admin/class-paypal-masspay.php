<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_PayPal_MassPay extends AffiliateWP_PayPal_API {

	/**
	 * Process a single referral payment
	 *
	 * @access public
	 * @since 1.1
	 * @return bool|WP_Error
	 */
	public function send_payment( $args = array() ) {

		$body_args = array(
			'USER'         => $this->credentials['username'],
			'PWD'          => $this->credentials['password'],
			'SIGNATURE'    => $this->credentials['signature'],
			'METHOD'       => 'MassPay',
			'VERSION'      => '124',
			'RECEIVERTYPE' => 'EmailAddress',
			'CURRENCYCODE' => affwp_get_currency(),
			'EMAILSUBJECT' => __( 'Affiliate Earnings Payout', 'affswp-paypal-payouts' ),
			'L_EMAIL0'     => $args['email'],
			'L_AMT0'       => $args['amount'],
			'L_NOTE0'      => $args['description']
		);

		$body_args_to_log = $body_args;
		unset( $body_args_to_log['USER'] );
		unset( $body_args_to_log['PWD'] );
		unset( $body_args_to_log['SIGNATURE'] );

		affiliate_wp()->utils->log( 'send_payment() body args (credentials removed): ' . print_r( $body_args_to_log, true ) );

		$mode     = affiliate_wp_paypal()->is_test_mode() ? 'sandbox.' : '';
		$request  = wp_remote_post( 'https://api-3t.' . $mode . 'paypal.com/nvp', array( 'timeout' => 45, 'sslverify' => false, 'body' => $body_args, 'httpversion' => '1.1' ) );
		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if( 200 === $code && 'ok' === strtolower( $message ) ) {

			if( is_string( $body ) ) {
				wp_parse_str( $body, $body );
			}

			if( 'failure' === strtolower( $body['ACK'] ) ) {

				affiliate_wp()->utils->log( 'send_payment() request failed with error code ' . $code  . ': ' . print_r( $body, true ) );

				return new WP_Error( 'api_error', $body['L_ERRORCODE0'] . ': ' . $body['L_LONGMESSAGE0'] );

			} else {

				if ( function_exists( 'affwp_add_payout' ) ) {
					if ( $referral = affwp_get_referral( $args['referral_id' ] ) ) {
						affwp_add_payout( array(
							'affiliate_id'  => $referral->affiliate_id,
							'referrals'     => $referral->ID,
							'amount'        => $referral->amount,
							'payout_method' => 'PayPal'
						) );
					}
				} else {
					affwp_set_referral_status( $args['referral_id'], 'paid' );
				}

			}

		} else {

			affiliate_wp()->utils->log( 'send_payment() request failed with error code ' . $code  . ': ' . $message );
			affiliate_wp()->utils->log( 'send_payment() request args: ' . print_r( $body_args, true ) );
			affiliate_wp()->utils->log( 'send_payment() request attempt: ' . print_r( $request, true ) );
			return new WP_Error( 'api_error', $code . ': ' . $message );

		}

		return true;

	}

	/**
	 * Process a referral payment for a bulk payout
	 *
	 * @access public
	 * @since 1.1
	 * @return bool|WP_Error
	 */
	public function send_bulk_payment( $payouts = array() ) {

		$body_args = array(
			'USER'         => $this->credentials['username'],
			'PWD'          => $this->credentials['password'],
			'SIGNATURE'    => $this->credentials['signature'],
			'METHOD'       => 'MassPay',
			'VERSION'      => '124',
			'RECEIVERTYPE' => 'EmailAddress',
			'CURRENCYCODE' => affwp_get_currency(),
			'EMAILSUBJECT' => __( 'Affiliate Earnings Payout', 'affwp-paypal-payouts' )
		);

		$i = 0;
		foreach( $payouts as $payout ) {

			$body_args[ 'L_EMAIL' . $i ] = $payout['email'];
			$body_args[ 'L_AMT' . $i ]   = $payout['amount'];
			$body_args[ 'L_NOTE' . $i ]  = $payout['description'];

			$i++;
		}

		$body_args_to_log = $body_args;
		unset( $body_args_to_log['USER'] );
		unset( $body_args_to_log['PWD'] );
		unset( $body_args_to_log['SIGNATURE'] );

		affiliate_wp()->utils->log( 'send_bulk_payment() body args (credentials removed): ' . print_r( $body_args_to_log, true ) );

		$mode     = affiliate_wp_paypal()->is_test_mode() ? 'sandbox.' : '';
		$request  = wp_remote_post( 'https://api-3t.' . $mode . 'paypal.com/nvp', array( 'timeout' => 45, 'sslverify' => false, 'body' => $body_args, 'httpversion' => '1.1' ) );
		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if ( is_wp_error( $request ) ) {

			affiliate_wp()->utils->log( 'send_bulk_payment() request failed with error code ' . $code  . ': ' . print_r( $body, true ) );

			return $request;

		} else if( 200 === $code && 'ok' === strtolower( $message ) ) {

			if( is_string( $body ) ) {
				wp_parse_str( $body, $body );
			}

			if( 'failure' === strtolower( $body['ACK'] ) ) {

				affiliate_wp()->utils->log( 'send_bulk_payment() request failed with error code ' . $code  . ': ' . print_r( $body, true ) );

				return new WP_Error( $body['L_ERRORCODE0'], $body['L_LONGMESSAGE0'] );

			}

		} else {

			affiliate_wp()->utils->log( 'send_bulk_payment() request failed with error code ' . $code  . ': ' . $message );
			affiliate_wp()->utils->log( 'send_bulk_payment() request args: ' . print_r( $body_args, true ) );
			affiliate_wp()->utils->log( 'send_bulk_payment() request attempt: ' . print_r( $request, true ) );
			return new WP_Error( $code, $message );

		}

		return true;

	}

}