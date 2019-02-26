<?php

class Affiliate_WP_OptimizeMember extends Affiliate_WP_Base {

	/**
	 * Setup the integration
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function init() {

		$this->context = 'optimizemember';

		add_action( 'init', array( $this, 'generate_referral' ) );
		add_action( 'init', array( $this, 'revoke_referral_on_refund' ) );

		add_action( 'plugins_loaded', array( $this, 'notify_url' ) );
		add_action('ws_plugin__optimizemember_before_sc_paypal_button_after_shortcode_atts', array( $this, 'set_referral_variable' ) );
		add_action('ws_plugin__optimizemember_pro_before_sc_stripe_form_after_shortcode_atts', array( $this, 'set_referral_variable' ) );
		add_action('ws_plugin__optimizemember_pro_before_sc_authnet_form_after_shortcode_atts', array( $this, 'set_referral_variable' ) );
		add_action('ws_plugin__optimizemember_pro_before_sc_paypal_form_after_shortcode_atts', array( $this, 'set_referral_variable' ) );
	}

	/**
	 * Create a payment notification url & refund notification url for AffiliateWP
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function notify_url(){

		$om_options = &$GLOBALS['WS_PLUGIN__']['optimizemember']['o'];

		$auth_key    = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret_auth = defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '';
		$hash        = md5( $auth_key . home_url() . get_bloginfo( 'admin_email' ) . $secret_auth );

		$om_payment_notification_urls = &$om_options['payment_notification_urls'];
		$affwp_payment_notify_url     = home_url( '/?optimizemember_affiliatewp_notify=payment&amount=%%amount%%&txn_id=%%txn_id%%&item_name=%%item_name%%&&ip=%%user_ip%%&user_id=%%user_id%%&item_number=%%item_number%%&payer_email=%%payer_email%%&affiliate_id=%%cv1%%&secret=' . $hash );

		// Add AffiliateWP payment notification URL to the list dynamically.
		if( stripos( $om_payment_notification_urls, $affwp_payment_notify_url ) === FALSE ){

			$om_payment_notification_urls .= "\n" . $affwp_payment_notify_url;

		}

		$om_page_sale_notification_urls = &$om_options['sp_sale_notification_urls'];
		$affwp_page_sale_notify_url     = home_url( '/?optimizemember_affiliatewp_notify=payment&amount=%%amount%%&txn_id=%%txn_id%%&item_name=%%item_name%%&&ip=%%user_ip%%&user_id=%%user_id%%&item_number=%%item_number%%&payer_email=%%payer_email%%&affiliate_id=%%cv1%%&secret=' . $hash );

		// Add AffiliateWP payment notification URL to the list dynamically.
		if( stripos( $om_page_sale_notification_urls, $affwp_page_sale_notify_url ) === FALSE ){

			$om_page_sale_notification_urls .= "\n" . $affwp_page_sale_notify_url;

		}

		$om_ref_rev_notification_urls = &$om_options['ref_rev_notification_urls'];
		$affwp_refund_notify_url      = home_url( '/?optimizemember_affiliatewp_notify=refund&txn_id=%%parent_txn_id%%&secret=' . $hash );

		// Add AffiliateWP refund notification URL notification URL to the list dynamically.
		if( stripos( $om_ref_rev_notification_urls, $affwp_refund_notify_url ) === FALSE ){

			$om_ref_rev_notification_urls .= "\n". $affwp_refund_notify_url;

		}

		$om_page_ref_rev_notification_urls = &$om_options['sp_ref_rev_notification_urls'];
		$affwp_page_refund_notify_url      = home_url( '/?optimizemember_affiliatewp_notify=refund&txn_id=%%parent_txn_id%%&secret=' . $hash );

		// Add AffiliateWP refund notification URL notification URL to the list dynamically.
		if( stripos( $om_page_ref_rev_notification_urls, $affwp_page_refund_notify_url ) === FALSE ){

			$om_page_ref_rev_notification_urls .= "\n". $affwp_page_refund_notify_url;

		}

	}

	/**
	 * Mark a referral as complete when an payment is received
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function generate_referral() {

		if( ! empty( $_REQUEST['optimizemember_affiliatewp_notify'] ) && 'payment' === $_REQUEST['optimizemember_affiliatewp_notify'] ) {

			$this->log( 'OptimizeMember payment notification.' );

			$auth_key    = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
			$secret_auth = defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '';
			$hash        = md5( $auth_key . home_url() . get_bloginfo( 'admin_email' ) . $secret_auth );

			if( empty( $_REQUEST['secret'] ) || ! hash_equals( $hash, $_REQUEST['secret'] ) ) {

				$this->log( 'OptimizeMember hash invalid.' );

				return;
			}

			$this->log( 'OptimizeMember hash verified.' );

			if( ! empty( $_REQUEST['affiliate_id'] ) ){

				$this->log( 'OptimizeMember referral validation passed.' );

				$affiliate_id 	= (int) $_REQUEST['affiliate_id'];
				$user_id 		= ! empty( $_REQUEST['user_id'] ) ? (int) $_REQUEST['user_id'] : 0;
				$amount 		= ! empty( $_REQUEST['amount'] ) ? affwp_sanitize_amount( $_REQUEST['amount'] ) : 0;
				$txn_id 		= sanitize_text_field( $_REQUEST['txn_id'] );
				$item_name 		= sanitize_text_field( $_REQUEST['item_name'] );
				$user_ip 		= sanitize_text_field( $_REQUEST['ip'] );
				$item_number 	= sanitize_text_field( $_REQUEST['item_number'] );
				$payer_email 	= sanitize_text_field( $_REQUEST['payer_email'] );

				$args =  array(
					'user_id' 		=> $user_id,
					'amount'		=> $amount,
					'txn_id'		=> $txn_id,
					'desc'			=> $item_name,
					'affiliate_id'	=> $affiliate_id
				);

				$this->add_pending_referral( $args );

				$this->log( 'OptimizeMember pending referral created.' );

				$this->complete_referral( $txn_id );

				$this->log( 'OptimizeMember referral completed.' );
			}

			$this->log( 'OptimizeMember referral validation failed. Missing affiliate ID.' );

			exit;
		}

	}

	/**
	 * Revoke a referral on refund or cancellation
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function revoke_referral_on_refund() {

		if( ! empty( $_REQUEST['optimizemember_affiliatewp_notify'] ) && $_REQUEST['optimizemember_affiliatewp_notify'] === 'refund' ) {

			$this->log( 'OptimizeMember refund notification.' );

			$auth_key    = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
			$secret_auth = defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '';
			$hash        = md5( $auth_key . home_url() . get_bloginfo( 'admin_email' ) . $secret_auth );

			if( empty( $_REQUEST['secret'] ) || ! hash_equals( $hash, $_REQUEST['secret'] ) ) {

				$this->log( 'OptimizeMember hash invalid.' );

				return;
			}

			$this->log( 'OptimizeMember hash verified.' );

			if( ! empty( $_REQUEST['txn_id'] ) ) {

				if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
					return;
				}

				$this->reject_referral( $_REQUEST['txn_id'] );

			}

			exit;

		}

	}

	/**
	 * Record a pending referral
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function add_pending_referral( $args ){

		if ( affiliate_wp()->tracking->is_valid_affiliate( $args['affiliate_id'] ) ) {

			$user           = get_userdata( $args['user_id'] );
			$customer_email = $user->user_email;

			if ( $this->is_affiliate_email( $customer_email ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return; // Customers cannot refer themselves

			}

			$amount      = $args['amount'];
			$order_id    = $args['txn_id'];
			$description = $args['desc'];

			if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {

			}

			$this->affiliate_id = $args['affiliate_id'];

			$referral_total = $this->calculate_referral_amount( $amount, $order_id );

			$this->insert_pending_referral( $referral_total, $order_id, $description );
		}
	}


	/**
	 * Add the Affiliate ID if set to the request that is sent to the gateway
	 *
	 * @access  public
	 * @since 1.9
	*/
	public function set_referral_variable( $vars ){

		$this->log( 'OptimizeMember set_referral_variable() ran.' );

		if( affiliate_wp()->tracking->get_affiliate_id() ){

			$this->log( 'OptimizeMember affiliate ID was found.' );

			$affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
			$vars["__refs"]["attr"]["custom"] .= "|" . $affiliate_id;

			$this->log( 'OptimizeMember custom variable set to ' . $vars["__refs"]["attr"]["custom"] );

		}
	}

}

if ( function_exists( 'load_opm_plugin_screen' ) ) {
	new Affiliate_WP_OptimizeMember;
}
