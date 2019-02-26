<?php

class WCML_Klarna_Gateway {

	public function add_hooks() {

		add_filter( 'wcml_multi_currency_ajax_actions', array( $this, 'ajax_action_needs_multi_currency' ) );

	}

	public function ajax_action_needs_multi_currency( $actions ) {

		$actions[] = 'klarna_checkout_cart_callback_update';
		$actions[] = 'klarna_checkout_coupons_callback';
		$actions[] = 'klarna_checkout_remove_coupon_callback';
		$actions[] = 'klarna_checkout_cart_callback_remove';
		$actions[] = 'klarna_checkout_shipping_callback';
		$actions[] = 'kco_iframe_shipping_option_change_cb';
		$actions[] = 'klarna_checkout_order_note_callback';
		$actions[] = 'kco_iframe_change_cb';
		$actions[] = 'kco_iframe_shipping_address_change_v2_cb';
		$actions[] = 'kco_iframe_shipping_address_change_cb';

		return $actions;
	}

}
