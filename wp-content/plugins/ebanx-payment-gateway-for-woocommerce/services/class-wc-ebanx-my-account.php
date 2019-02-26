<?php
/**
 * EBANX.com My Account actions
 *
 * @package WooCommerce_EBANX/Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_EBANX_My_Account enables the thank you pages
 */
class WC_EBANX_My_Account {


	/**
	 * Constructor and initialize the filters and actions
	 */
	public function __construct() {
		// Actions.
		add_action( 'woocommerce_order_items_table', array( $this, 'order_details' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 100 );

		// Filters.
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'my_orders_banking_ticket_link' ), 10, 2 );
	}

	/**
	 * Load the assets needed by my account page
	 *
	 * @return void
	 */
	public function assets() {
		wp_enqueue_style(
			'woocommerce_my_account_style',
			plugins_url( 'assets/css/my-account.css', WC_EBANX::DIR )
		);
	}

	/**
	 * Add banking ticket link/button in My Orders section on My Accout page.
	 *
	 * @param array    $actions Actions.
	 * @param WC_Order $order   Order data.
	 *
	 * @return array
	 */
	public function my_orders_banking_ticket_link( $actions, $order ) {
		if ( 'ebanx-banking-ticket' === $order->payment_method && in_array( $order->get_status(), array( 'pending', 'on-hold' ) ) ) {
			$url = get_post_meta( $order->id, 'Banking Ticket URL', true );

			if ( ! empty( $url ) ) {
				$actions[] = array(
					'url'  => $url,
					'name' => __( 'View Banking Ticket', 'woocommerce-gateway-ebanx' ),
				);
			}
		}

		return $actions;
	}

	/**
	 * Call thankyou pages on order details page on My Account by gateway method
	 *
	 * @param  WC_Order $order      The object order.
	 * @return void
	 */
	public static function order_details( $order ) {
		// For test purposes.
		$hash = get_post_meta( $order->id, '_ebanx_payment_hash', true );

		printf( '<input type="hidden" name="ebanx_payment_hash" value="%s" />', $hash ); // phpcs:ignore WordPress.XSS.EscapeOutput

		switch ( $order->payment_method ) {
			case 'ebanx-credit-card-br':
				WC_EBANX_Credit_Card_BR_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-credit-card-mx':
				WC_EBANX_Credit_Card_MX_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-credit-card-ar':
				WC_EBANX_Credit_Card_AR_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-credit-card-co':
				WC_EBANX_Credit_Card_CO_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-banking-ticket':
				WC_EBANX_Banking_Ticket_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-eft':
				WC_EBANX_Eft_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-oxxo':
				WC_EBANX_Oxxo_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-spei':
				WC_EBANX_Spei_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-pagoefectivo':
				WC_EBANX_Pagoefectivo_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-safetypay':
				WC_EBANX_Safetypay_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-servipag':
				WC_EBANX_Servipag_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-tef':
				WC_EBANX_Tef_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-account':
				WC_EBANX_Account_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-debit-card':
				WC_EBANX_Debit_Card_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-sencillito':
				WC_EBANX_Sencillito_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-baloto':
				WC_EBANX_Baloto_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-efectivo':
				WC_EBANX_Efectivo_Gateway::thankyou_page( $order );
				break;
			case 'ebanx-banktransfer':
				WC_EBANX_Bank_Transfer_Gateway::thankyou_page( $order );
				break;
		}
	}
}

/**
 * Initialize the thank you pages
 */
new WC_EBANX_My_Account();
