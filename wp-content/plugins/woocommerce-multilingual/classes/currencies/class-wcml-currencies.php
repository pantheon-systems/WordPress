<?php

/**
 * Class WCML_Currencies
 */
class WCML_Currencies {

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**

	/**
	 * WCML_Currencies constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		if ( is_admin() && wcml_is_multi_currency_on() ) {
			add_action( 'update_option_woocommerce_currency', array(
				$this,
				'setup_multi_currency_on_currency_update'
			), 10, 2 );
		}
	}

	/**
	 * @param string $old_value
	 * @param string $new_value
	 */
	public function setup_multi_currency_on_currency_update( $old_value, $new_value ) {
		$multi_currency_install = new WCML_Multi_Currency_Install( new WCML_Multi_Currency(), $this->woocommerce_wpml );
		$multi_currency_install->set_default_currencies_languages( $old_value, $new_value );
	}

}