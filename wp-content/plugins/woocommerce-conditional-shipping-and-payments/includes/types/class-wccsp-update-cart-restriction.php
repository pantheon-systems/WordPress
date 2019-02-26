<?php
/**
 * WC_CSP_Update_Cart_Restriction interface
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update Cart Restriction Interface.
 *
 * @version 1.0.0
 */
interface WC_CSP_Update_Cart_Restriction {

	/**
	 * Restriction validation running on the 'woocommerce_update_cart_validation' hook.
	 *
	 * @param  bool   $passed
	 * @param  string $cart_item_key
	 * @param  array  $cart_item_values
	 * @param  mixed  $quantity
	 * @return void
	 */
	public function validate_cart_update( $passed, $cart_item_key, $cart_item_values, $quantity );

}
