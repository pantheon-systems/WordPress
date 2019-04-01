<?php
/**
 * Description of wc_checkout_addons
 *
 * @author konrad
 */
class WCML_Checkout_Addons {
	public function __construct() {
		add_filter( 'wc_checkout_add_ons_options',  array( $this, 'wc_checkout_add_ons_options_wpml_multi_currency_support' ) );
	}
	
	public function wc_checkout_add_ons_options_wpml_multi_currency_support( $options ) {

		foreach ( $options as $i => $option ) {
			$options[ $i ]['cost'] = apply_filters( 'wcml_raw_price_amount', $options[ $i ]['cost'] );
		}

		return $options;
	} 
}
