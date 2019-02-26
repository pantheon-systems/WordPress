<?php

class WCML_Url_Filters_Redirect_Location {
	/** @var WPML_URL_Converter */
	private $wpml_url_converter;

	/**
	 * @param WPML_URL_Converter $wpml_url_converter
	 */
	public function __construct( WPML_URL_Converter $wpml_url_converter ) {
		$this->wpml_url_converter = $wpml_url_converter;
	}

	public function add_hooks() {
		$hooks = array( 'woocommerce_get_checkout_payment_url', 'woocommerce_get_cancel_order_url', 'woocommerce_get_return_url' );

		foreach ( $hooks as $hook ) {
			add_filter( $hook, array( $this, 'filter' ), 10, 1 );
		}
	}

	/**
	 * @param string $link
	 *
	 * @return string
	 */
	public function filter( $link ) {
		return html_entity_decode( $this->wpml_url_converter->convert_url( $link ) );
	}
}
