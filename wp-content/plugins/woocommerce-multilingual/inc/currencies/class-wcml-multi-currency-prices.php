<?php

class WCML_Multi_Currency_Prices {

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**
	 * @var WCML_Multi_Currency
	 */
	private $multi_currency;
	/**
	 * @var orders_list_currency
	 */
	private $orders_list_currency;

	public function __construct( $multi_currency ) {
		$this->multi_currency = $multi_currency;
	}

	public function add_hooks() {
		add_filter( 'wcml_raw_price_amount', array( $this, 'raw_price_filter' ), 10, 2 );  // WCML filters

		if ( $this->multi_currency->load_filters ) {
			add_filter( 'init', array( $this, 'prices_init' ), 5 );

			// Currency and Amount filters
			add_filter( 'woocommerce_currency', array( $this, 'currency_filter' ) );

			add_filter( 'wcml_price_currency', array( $this, 'price_currency_filter' ) );      // WCML filters
			add_filter( 'wcml_product_price_by_currency', array(
				$this,
				'get_product_price_in_currency'
			), 10, 2 );  // WCML filters

			add_filter( 'get_post_metadata', array( $this, 'product_price_filter' ), 10, 4 );
			add_filter( 'get_post_metadata', array( $this, 'variation_prices_filter' ), 12, 4 ); // second

			add_filter( 'woocommerce_price_filter_widget_max_amount', array( $this, 'raw_price_filter' ), 99 );
			add_filter( 'woocommerce_price_filter_widget_min_amount', array( $this, 'raw_price_filter' ), 99 );

			add_filter( 'woocommerce_adjust_price', array( $this, 'raw_price_filter' ), 10 );

			add_filter( 'wcml_formatted_price', array( $this, 'formatted_price' ), 10, 2 ); // WCML filters

			// Shipping prices
			add_filter( 'woocommerce_paypal_args', array( $this, 'filter_price_woocommerce_paypal_args' ) );
			add_filter( 'woocommerce_get_variation_prices_hash', array(
				$this,
				'add_currency_to_variation_prices_hash'
			) );
			add_filter( 'woocommerce_cart_contents_total', array(
				$this,
				'filter_woocommerce_cart_contents_total'
			), 100 );
			add_filter( 'woocommerce_cart_subtotal', array( $this, 'filter_woocommerce_cart_subtotal' ), 100, 3 );

			//filters for wc-widget-price-filter
			add_filter( 'woocommerce_price_filter_results', array( $this, 'filter_price_filter_results' ), 10, 3 );
			add_filter( 'woocommerce_price_filter_widget_amount', array( $this, 'filter_price_filter_widget_amount' ) );

			add_action( 'woocommerce_cart_loaded_from_session', array(
				$this,
				'filter_currency_num_decimals_in_cart'
			) );

			add_filter( 'wc_price_args', array( $this, 'filter_wc_price_args' ) );

		}

		// formatting options
		add_filter( 'option_woocommerce_price_thousand_sep', array( $this, 'filter_currency_thousand_sep_option' ) );
		add_filter( 'option_woocommerce_price_decimal_sep', array( $this, 'filter_currency_decimal_sep_option' ) );
		add_filter( 'option_woocommerce_price_num_decimals', array( $this, 'filter_currency_num_decimals_option' ) );
		add_filter( 'option_woocommerce_currency_pos', array( $this, 'filter_currency_position_option' ) );

		//need for display correct price format for order on orders list page
		add_filter( 'get_post_metadata', array( $this, 'save_order_currency_for_filter' ), 10, 4 );
	}

	public function prices_init() {
		global $woocommerce_wpml;
		$this->woocommerce_wpml =& $woocommerce_wpml;

	}

	public function currency_filter( $currency ) {

		$currency = apply_filters( 'wcml_price_currency', $currency );

		return $currency;
	}

	public function price_currency_filter( $currency ) {

		if ( isset( $this->order_currency ) ) {
			$currency = $this->order_currency;
		} else {
			$currency = $this->multi_currency->get_client_currency();
		}

		return $currency;
	}

	public function raw_price_filter( $price, $currency = false ) {

		if ( $currency === false ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		if ( $currency != get_option( 'woocommerce_currency' ) ) {
			$price = $this->convert_price_amount( $price, $currency );
			$price = $this->apply_rounding_rules( $price, $currency );
		}

		return $price;

	}

	public function get_product_price_in_currency( $product_id, $currency = false ) {

		if ( ! $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		remove_filter( 'get_post_metadata', array(
			$this->woocommerce_wpml->multi_currency->prices,
			'product_price_filter'
		), 10, 4 );

		$manual_prices = $this->multi_currency->custom_prices->get_product_custom_prices( $product_id, $currency );

		if ( $manual_prices && isset( $manual_prices['_price'] ) ) {

			$price = $manual_prices['_price'];

		} else {

			$product = wc_get_product( $product_id );
			$price   = $this->raw_price_filter( $product->get_price(), $currency );

		}

		add_filter( 'get_post_metadata', array(
			$this->woocommerce_wpml->multi_currency->prices,
			'product_price_filter'
		), 10, 4 );

		return $price;

	}

	public function product_price_filter( $null, $object_id, $meta_key, $single ) {
		global $sitepress;

		static $no_filter = false;

		if ( empty( $no_filter ) && in_array( get_post_type( $object_id ), array( 'product', 'product_variation' ) ) ) {

			$price_keys = apply_filters( 'wcml_price_custom_fields_filtered', array(
				'_price',
				'_regular_price',
				'_sale_price',
				'_min_variation_price',
				'_max_variation_price',
				'_min_variation_regular_price',
				'_max_variation_regular_price',
				'_min_variation_sale_price',
				'_max_variation_sale_price'
			) );

			if ( in_array( $meta_key, $price_keys ) ) {
				$no_filter = true;

				// exception for products migrated from before WCML 3.1 with independent prices
				// legacy prior 3.1
				$original_object_id = apply_filters( 'translate_object_id', $object_id, get_post_type( $object_id ), false, $sitepress->get_default_language() );
				$ccr                = get_post_meta( $original_object_id, '_custom_conversion_rate', true );

				if ( in_array( $meta_key, array(
						'_price',
						'_regular_price',
						'_sale_price'
					) ) && ! empty( $ccr ) && isset( $ccr[ $meta_key ][ $this->multi_currency->get_client_currency() ] )
				) {
					$price_original = get_post_meta( $original_object_id, $meta_key, $single );
					$price          = $price_original * $ccr[ $meta_key ][ $this->multi_currency->get_client_currency() ];

				} else {

					// normal filtering
					// 1. manual prices
					$manual_prices = $this->multi_currency->custom_prices->get_product_custom_prices( $object_id, $this->multi_currency->get_client_currency() );

					if ( $manual_prices && isset( $manual_prices[ $meta_key ] ) ) {

						$price = $manual_prices[ $meta_key ];

					} else {
						// 2. automatic conversion
						$price = get_post_meta( $object_id, $meta_key, $single );
						if( is_numeric( $price ) ){
							$price = apply_filters( 'wcml_raw_price_amount', $price );
						}
					}
				}

				$no_filter = false;
			}

		}

		return isset( $price ) ? $price : $null;
	}

	public function variation_prices_filter( $null, $object_id, $meta_key, $single ) {

		if ( empty( $meta_key ) && get_post_type( $object_id ) == 'product_variation' ) {
			static $no_filter = false;

			if ( empty( $no_filter ) ) {
				$no_filter = true;

				$variation_fields = get_post_meta( $object_id );

				$manual_prices = $this->multi_currency->custom_prices->get_product_custom_prices( $object_id, $this->multi_currency->get_client_currency() );

				foreach ( $variation_fields as $k => $v ) {

					if ( in_array( $k, array( '_price', '_regular_price', '_sale_price' ) ) ) {

						foreach ( $v as $j => $amount ) {

							if ( isset( $manual_prices[ $k ] ) ) {
								$variation_fields[ $k ][ $j ] = $manual_prices[ $k ];     // manual price

							} elseif( $amount ) {
								$variation_fields[ $k ][ $j ] = apply_filters( 'wcml_raw_price_amount', $amount );   // automatic conversion
							}

						}

					}

				}

				$no_filter = false;
			}

		}

		return isset( $variation_fields ) ? $variation_fields : $null;

	}

	public function convert_price_amount( $amount, $currency = false ) {

		if ( empty( $currency ) ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		if ( $currency != get_option( 'woocommerce_currency' ) ) {

			$exchange_rates = $this->multi_currency->get_exchange_rates();

			if ( isset( $exchange_rates[ $currency ] ) && is_numeric( $amount ) ) {
				$amount = $amount * $exchange_rates[ $currency ];

				// exception - currencies_without_cents
				if ( in_array( $currency, $this->multi_currency->get_currencies_without_cents() ) ) {
					$amount = $this->round_up( $amount );
				}

			} else {
				$amount = 0;
			}

		}

		return $amount;

	}

	// convert back to default currency
	public function unconvert_price_amount( $amount, $currency = false ) {

		if ( empty( $currency ) ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		if ( $currency != get_option( 'woocommerce_currency' ) ) {

			$exchange_rates = $this->multi_currency->get_exchange_rates();

			if ( isset( $exchange_rates[ $currency ] ) && is_numeric( $amount ) ) {
				$amount = $amount / $exchange_rates[ $currency ];

				// exception - currencies_without_cents
				if ( in_array( $currency, $this->multi_currency->get_currencies_without_cents() ) ) {
					$amount = $this->round_up( $amount );
				}

			} else {
				$amount = 0;
			}

		}

		return $amount;

	}

	public function apply_rounding_rules( $price, $currency = false ) {

		if ( is_null( $this->woocommerce_wpml ) ) {
			global $woocommerce_wpml;
			$this->woocommerce_wpml = $woocommerce_wpml;
		}

		if ( ! $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		$currency_options = $this->woocommerce_wpml->settings['currency_options'][ $currency ];

		if ( $currency_options['rounding'] != 'disabled' ) {

			if ( $currency_options['rounding_increment'] > 1 ) {
				$price = $price / $currency_options['rounding_increment'];
			}

			switch ( $currency_options['rounding'] ) {
				case 'up':
					$rounded_price = ceil( $price );
					break;
				case 'down':
					$rounded_price = floor( $price );
					break;
				case 'nearest':
					$rounded_price = $this->round_up( $price );
					break;
			}

			if ( $rounded_price > 0 ) {
				$price = $rounded_price;
			}

			if ( $currency_options['rounding_increment'] > 1 ) {
				$price = $price * $currency_options['rounding_increment'];
			}

			if ( $currency_options['auto_subtract'] && $currency_options['auto_subtract'] < $price ) {
				$price = $price - $currency_options['auto_subtract'];
			}

		} else {

			// Use configured number of decimals
			$price = floor( $price * pow( 10, $currency_options['num_decimals'] ) + 0.0001 ) / pow( 10, $currency_options['num_decimals'] );

		}


		return apply_filters( 'wcml_rounded_price', $price, $currency );

	}

	/**
	 * The PHP 5.2 compatible equivalent to "round($amount, 0, PHP_ROUND_HALF_UP)"
	 *
	 * @param int $amount
	 *
	 * @return int
	 *
	 */
	private function round_up( $amount ) {
		if ( $amount - floor( $amount ) < 0.5 ) {
			$amount = floor( $amount );
		} else {
			$amount = ceil( $amount );
		}

		return $amount;
	}

	/*
	* Converts the price from the default currency to the given currency and applies the format
	*/
	public function formatted_price( $amount, $currency = false ) {

		if ( $currency === false ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		$amount = $this->raw_price_filter( $amount, $currency );

		$currency_details = $this->woocommerce_wpml->multi_currency->get_currency_details_by_code( $currency );

		switch ( $currency_details['position'] ) {
			case 'left' :
				$format = '%1$s%2$s';
				break;
			case 'right' :
				$format = '%2$s%1$s';
				break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
				break;
		}

		$wc_price_args = array(

			'currency'           => $currency,
			'decimal_separator'  => $currency_details['decimal_sep'],
			'thousand_separator' => $currency_details['thousand_sep'],
			'decimals'           => $currency_details['num_decimals'],
			'price_format'       => $format,


		);

		$price = wc_price( $amount, $wc_price_args );

		return $price;
	}

	// Exposed function
	public function apply_currency_position( $price, $currency_code ) {

		$currencies = $this->woocommerce_wpml->multi_currency->get_currencies();

		if ( isset( $currencies[ $currency_code ]['position'] ) ) {
			$position = $currencies[ $currency_code ]['position'];
		} else {
			remove_filter( 'option_woocommerce_currency_pos', array(
				$this->woocommerce_wpml->multi_currency->prices,
				'filter_currency_position_option'
			) );
			$position = get_option( 'woocommerce_currency_pos' );
			add_filter( 'option_woocommerce_currency_pos', array(
				$this->woocommerce_wpml->multi_currency->prices,
				'filter_currency_position_option'
			) );
		}

		switch ( $position ) {
			case 'left':
				$price = sprintf( '%s%s', get_woocommerce_currency_symbol( $currency_code ), $price );
				break;
			case 'right':
				$price = sprintf( '%s%s', $price, get_woocommerce_currency_symbol( $currency_code ) );
				break;
			case 'left_space':
				$price = sprintf( '%s %s', get_woocommerce_currency_symbol( $currency_code ), $price );
				break;
			case 'right_space':
				$price = sprintf( '%s %s', $price, get_woocommerce_currency_symbol( $currency_code ) );
				break;
		}

		return $price;
	}

	public function filter_price_woocommerce_paypal_args( $args ) {

		foreach ( $args as $key => $value ) {
			if ( substr( $key, 0, 7 ) == 'amount_' ) {

				$currency_details = $this->woocommerce_wpml->multi_currency->get_currency_details_by_code( $args['currency_code'] );

				$args[ $key ] = number_format( $value, $currency_details['num_decimals'], '.', '' );
			}
		}

		return $args;
	}

	public function add_currency_to_variation_prices_hash( $data ) {

		$data['currency']            = $this->multi_currency->get_client_currency();
		$data['exchange_rates_hash'] = md5( json_encode( $this->multi_currency->get_exchange_rates() ) );

		return $data;

	}

	public function filter_woocommerce_cart_contents_total( $cart_contents_total ) {
		global $woocommerce;
		remove_filter( 'woocommerce_cart_contents_total', array(
			$this,
			'filter_woocommerce_cart_contents_total'
		), 100 );
		$woocommerce->cart->calculate_totals();
		$cart_contents_total = $woocommerce->cart->get_cart_total();
		add_filter( 'woocommerce_cart_contents_total', array( $this, 'filter_woocommerce_cart_contents_total' ), 100 );

		return $cart_contents_total;
	}

	public function filter_woocommerce_cart_subtotal( $cart_subtotal, $compound, $cart_object ) {

		remove_filter( 'woocommerce_cart_subtotal', array( $this, 'filter_woocommerce_cart_subtotal' ), 100, 3 );

		$cart_subtotal = $cart_object->get_cart_subtotal( $compound );

		add_filter( 'woocommerce_cart_subtotal', array( $this, 'filter_woocommerce_cart_subtotal' ), 100, 3 );

		return $cart_subtotal;
	}

	public function filter_price_filter_results( $matched_products, $min, $max ) {
		global $wpdb;

		$current_currency = $this->multi_currency->get_client_currency();
		if ( $current_currency != get_option( 'woocommerce_currency' ) ) {
			$filtered_min = $this->unconvert_price_amount( $min, $current_currency );
			$filtered_max = $this->unconvert_price_amount( $max, $current_currency );

			$matched_products = $wpdb->get_results( $wpdb->prepare( "
	        	SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta ON ID = post_id
				WHERE post_type IN ( 'product', 'product_variation' ) AND post_status = 'publish' AND meta_key = %s AND meta_value BETWEEN %d AND %d
			", '_price', $filtered_min, $filtered_max ), OBJECT_K );

			foreach ( $matched_products as $key => $matched_product ) {
				$custom_price = get_post_meta( $matched_product->ID, '_price_' . $current_currency, true );
				if ( $custom_price && ( $custom_price < $min || $custom_price > $max ) ) {
					unset( $matched_products[ $key ] );
				}
			}
		}

		return $matched_products;
	}

	public function filter_price_filter_widget_amount( $amount ) {

		$current_currency = $this->multi_currency->get_client_currency();
		if ( $current_currency != get_option( 'woocommerce_currency' ) ) {
			$amount = apply_filters( 'wcml_raw_price_amount', $amount );
		}

		return $amount;

	}

	private function check_admin_order_currency_code() {
		global $pagenow;

		$actions              = array(
			'woocommerce_add_order_item',
			'woocommerce_save_order_items',
			'woocommerce_calc_line_taxes'
		);
		$is_ajax_order_action =
			is_ajax() &&
			(
			(
				isset( $_POST['action'] ) &&
				in_array( $_POST['action'], $actions ) ||
				(
					isset( $_GET['action'] ) &&
					$_GET['action'] == 'woocommerce_json_search_products_and_variations'
				)
			)
			);

		$is_shop_order_new = $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order';

		if ( ( $is_ajax_order_action || $is_shop_order_new ) && isset( $_COOKIE['_wcml_order_currency'] ) ) {
			$currency_code = $_COOKIE['_wcml_order_currency'];
		} elseif ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == 'shop_order' ) {
			$currency_code = get_post_meta( $_GET['post'], '_order_currency', true );
		} elseif ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' && ! is_null( $this->orders_list_currency ) ) {
			$currency_code = $this->orders_list_currency;
		} elseif ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-reports' && isset( $_COOKIE['_wcml_reports_currency'] ) ) {
			$currency_code = $_COOKIE['_wcml_reports_currency'];
		} elseif ( isset( $_COOKIE['_wcml_dashboard_currency'] ) && is_admin() && ! defined( 'DOING_AJAX' ) && $pagenow == 'index.php' ) {
			$currency_code = $_COOKIE['_wcml_dashboard_currency'];
		} else {
			$currency_code = $this->multi_currency->get_client_currency();
		}

		return apply_filters( 'wcml_filter_currency_position', $currency_code );

	}

	public function get_admin_order_currency_code() {

		return $this->check_admin_order_currency_code();

	}

	public function save_order_currency_for_filter( $null, $object_id, $meta_key, $single ) {

		if (
			$meta_key == '_order_currency' &&
			isset( $_GET['post_type'] ) &&
			$_GET['post_type'] == 'shop_order' &&
			! isset( $_GET['post'] ) &&
			get_post_type( $object_id ) == 'shop_order'
		) {
			remove_filter( 'get_post_metadata', array( $this, 'save_order_currency_for_filter' ), 10, 4 );
			$this->orders_list_currency = get_post_meta( $object_id, $meta_key, true );
			add_filter( 'get_post_metadata', array( $this, 'save_order_currency_for_filter' ), 10, 4 );
		}

		return $null;
	}

	public function filter_currency_thousand_sep_option( $value ) {

		$default_currency = $this->multi_currency->get_default_currency();
		$currency_code    = $this->check_admin_order_currency_code();

		if ( $currency_code !== $default_currency && isset( $this->multi_currency->currencies[ $currency_code ]['thousand_sep'] ) ) {
			$value = $this->multi_currency->currencies[ $currency_code ]['thousand_sep'];
		}

		return $value;
	}

	public function filter_currency_decimal_sep_option( $value ) {

		$default_currency = $this->multi_currency->get_default_currency();
		$currency_code    = $this->check_admin_order_currency_code();

		if ( $currency_code !== $default_currency && isset( $this->multi_currency->currencies[ $currency_code ]['decimal_sep'] ) ) {
			$value = $this->multi_currency->currencies[ $currency_code ]['decimal_sep'];

		}

		return $value;
	}

	public function filter_currency_num_decimals_option( $value ) {
		// no other way available (at the moment) to filter currency_num_decimals_option
		$default_currency = $this->multi_currency->get_default_currency();

		$db = debug_backtrace();
		if (
			isset( $db['8']['function'] ) && isset( $db['5']['function'] ) &&
			$db['8']['function'] == 'calculate_shipping_for_package' && $db['5']['function'] == 'add_rate'
			||
			isset( $db['7']['function'] ) && isset( $db['4']['function'] ) &&
			$db['7']['function'] == 'calculate_shipping_for_package' && $db['4']['function'] == 'add_rate'
		) {
			$currency_code = $default_currency;
		} else {
			$currency_code = $this->check_admin_order_currency_code();
		}

		if ( $currency_code !== $default_currency && isset( $this->multi_currency->currencies[ $currency_code ]['num_decimals'] ) ) {
			$value = $this->multi_currency->currencies[ $currency_code ]['num_decimals'];
		}

		return $value;
	}

	public function filter_currency_position_option( $value ) {

		$default_currency = $this->multi_currency->get_default_currency();
		$currency_code    = $this->get_admin_order_currency_code();

		if ( $currency_code !== $default_currency &&
		     isset( $this->multi_currency->currencies[ $currency_code ]['position'] ) && get_option( 'woocommerce_currency' ) != $currency_code &&
		     in_array( $this->multi_currency->currencies[ $currency_code ]['position'], array(
			     'left',
			     'right',
			     'left_space',
			     'right_space'
		     ) )
		) {
			$value = $this->multi_currency->currencies[ $currency_code ]['position'];
		}

		return $value;
	}

	public function filter_currency_num_decimals_in_cart( $cart ) {
		$cart->dp = wc_get_price_decimals();
	}

	/*
	 * Limitation: If the default currency is configured to display more decimals than the other currencies,
	 * the prices in the secondary currencies would be approximated to the number of decimals that they have more.
	*/
	public function price_in_specific_currency( $return, $price, $args ) {

		if ( isset( $args['currency'] ) && $this->multi_currency->get_client_currency() != $args['currency'] ) {
			remove_filter( 'wc_price', array( $this, 'price_in_specific_currency' ), 10, 3 );
			$this->multi_currency->set_client_currency( $args['currency'] );
			$return = wc_price( $price, $args );
			add_filter( 'wc_price', array( $this, 'price_in_specific_currency' ), 10, 3 );
		}

		return $return;

	}

	public function filter_wc_price_args( $args ) {

		if ( isset( $args['currency'] ) ) {

			if ( isset( $this->multi_currency->currencies[ $args['currency'] ]['decimal_sep'] ) ) {
				$args['decimal_separator'] = $this->multi_currency->currencies[ $args['currency'] ]['decimal_sep'];
			}

			if ( isset( $this->multi_currency->currencies[ $args['currency'] ]['thousand_sep'] ) ) {
				$args['thousand_separator'] = $this->multi_currency->currencies[ $args['currency'] ]['thousand_sep'];
			}

			if ( isset( $this->multi_currency->currencies[ $args['currency'] ]['num_decimals'] ) ) {
				$args['decimals'] = $this->multi_currency->currencies[ $args['currency'] ]['num_decimals'];
			}

			if ( isset( $this->multi_currency->currencies[ $args['currency'] ]['position'] ) ) {
				$current_currency = $this->multi_currency->get_client_currency();
				$this->multi_currency->set_client_currency( $args['currency'] );
				$args['price_format'] = get_woocommerce_price_format();
				$this->multi_currency->set_client_currency( $current_currency ); //restore
			}

		}

		return $args;
	}

	/**
	 * @param float $price
	 * @param null|string $currency
	 *
	 * @return float
	 */
	public function convert_raw_woocommerce_price( $price, $currency = null ) {
		if ( null === $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		return apply_filters( 'wcml_raw_price_amount', $price, $currency );
	}

	/**
	 * @param float $value
	 * @param WC_Product $product
	 *
	 * @return float
	 */
	public function get_original_product_price( $value, $product ) {
		return get_post_meta( $product->get_id(), '_price', 1 );
	}

}