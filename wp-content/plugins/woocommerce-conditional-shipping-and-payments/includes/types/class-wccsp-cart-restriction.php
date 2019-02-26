<?php
/**
 * WC_CSP_Cart_Restriction interface
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
 * Check Cart Restriction Interface.
 *
 * @version 1.0.0
 */
interface WC_CSP_Cart_Restriction {

	/**
	 * Restriction validation running on the 'check_cart_items' hook.
	 *
	 * @return void
	 */
	public function validate_cart();
}
