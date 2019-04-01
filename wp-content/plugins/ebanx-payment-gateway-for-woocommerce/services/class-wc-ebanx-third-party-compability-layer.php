<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Third_Party_Compability_Layer
 */
class WC_EBANX_Third_Party_Compability_Layer {
	/**
	 * Check and solve third party incompabilities
	 */
	public static function check_and_solve() {
		add_action( 'wp_enqueue_scripts', array( 'WC_EBANX_Third_Party_Compability_Layer', 'check_and_solve_sticky_checkout' ), 90 );
	}

	/**
	 * Check and solve sticky checkout incompabilities
	 */
	public static function check_and_solve_sticky_checkout() {
		self::solve_sticky_checkout_storefront();
	}

	/**
	 * Disable Storefront sticky checkout
	 */
	private static function solve_sticky_checkout_storefront() {
		if ( wp_get_theme()->get( 'Name' ) === 'Storefront' ) {
			wp_deregister_script( 'storefront-sticky-payment' );
		}
	}
}
