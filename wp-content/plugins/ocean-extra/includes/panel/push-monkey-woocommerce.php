<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) 
{
exit;
}

/**
* WooCommerce model for push monkey.
*/
class Ocean_Woo_Commerce extends Ocean_Extra_Theme_Panel {

	/**
	 * Public functions
	 */
	public function add_actions() {

		$woo_enabled = get_option( self::WOO_COMMERCE_ENABLED, false );
		if ( $woo_enabled !== '1' ) {
			return;
		}
		add_filter( 'woocommerce_cart_id', array( $this,'filter_wc_cart_id'), 10, 5 );
		add_action( 'woocommerce_add_to_cart', array( $this,  'add_to_cart_hook' ) );
		add_action( 'woocommerce_order_status_completed', array( $this,  'update_cart_hook' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'action_woocommerce_checkout_update_order_meta' ), 10, 2 );
	}

	public function filter_wc_cart_id ( $cart_id, $product_id, $variation_id, $variation, $cart_item_data ) {

		$cart_id = substr( $cart_id, 1, 9 ).strtotime( 'now' ).mt_rand( 10, 9999 );
		return $cart_id;
	}

	public function add_to_cart_hook( $key ) {

		global $woocommerce;

		$pushmonkey = new Ocean_Extra_Theme_Panel();
		$api_token = $pushmonkey::account_key();
		$stored_key = WC()->session->get( '_push_monkey' );
		if( $pushmonkey::has_account_key() && $stored_key == null ) {
			$response = $pushmonkey::$apiClient->create_cart( $key, $api_token );
			WC()->session->set( '_push_monkey', $key );
			wc_setcookie( '_push_monkey_wc_cart_id', $key, time()+60*60*24*5 );
		}
		return $key;
	}

	public function update_cart_hook( $order_id ) {

		global $woocommerce;
		$order = new WC_order( $order_id );
		$key = get_post_meta( $order_id, '_cart_id', true );
		$pushmonkey = new Ocean_Extra_Theme_Panel();
		$api_token = $pushmonkey::account_key();
		/**
		 * Update cart if key is not empty. 
		 */
		if( $key != '' ) {

			$response = $pushmonkey::$apiClient->update_cart( $key, $api_token );
		}
	}

	public function action_woocommerce_checkout_update_order_meta( $order_id ) {

		$key = WC()->session->get( '_push_monkey' );
		update_post_meta( $order_id, '_cart_id', $key );
		wc_setcookie( '_push_monkey_wc_cart_id', '', -1 );                
	}

	/**
	 * Private
	 */
	function __construct() {

		$this->add_actions();
	}
}
$push_monkey_wc = new Ocean_Woo_Commerce();