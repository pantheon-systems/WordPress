<?php

class WCML_Price_Filter {

	/**
	 * @var woocommerce_wpml;
	 */
	private $woocommerce_wpml;

	public function __construct( woocommerce_wpml &$woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_action( 'wp_footer', array( $this, 'override_currency_symbol' ), 100 );

		if ( ! is_admin() ) {
			add_filter( 'woocommerce_product_query_meta_query', array( $this, 'unconvert_price_filter_limits' ) );
		}

	}

	public function override_currency_symbol() {
		?>
        <script type="text/javascript">
        /* <![CDATA[ */
            if( typeof woocommerce_price_slider_params !== 'undefined' ) {
                woocommerce_price_slider_params.currency_format_symbol = wcml_mc_settings.current_currency.symbol;
            }
        /* ]]> */
        </script>
		<?php
	}

	/**
	 * @param array $meta_query
	 *
	 * @return array
	 */
	public function unconvert_price_filter_limits( $meta_query ) {

		$multi_currency = $this->woocommerce_wpml->multi_currency;

		if ( $multi_currency->get_client_currency() !== get_option( 'woocommerce_currency' ) ) {
			if ( isset( $meta_query['price_filter'] ) && isset($meta_query['price_filter']['key']) && $meta_query['price_filter']['key'] === '_price' ) {
				$meta_query['price_filter']['value'][0] = $multi_currency->prices->unconvert_price_amount( $meta_query['price_filter']['value'][0] );
				$meta_query['price_filter']['value'][1] = $multi_currency->prices->unconvert_price_amount( $meta_query['price_filter']['value'][1] );
			}
		}

		return $meta_query;
	}

}