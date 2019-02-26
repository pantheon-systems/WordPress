<?php

class WCML_Multi_Currency_Orders {

	/**
	 * @var WCML_Multi_Currency
	 */
	private $multi_currency;

	public function __construct( &$multi_currency, &$woocommerce_wpml ) {
		$this->multi_currency   =& $multi_currency;
		$this->woocommerce_wpml =& $woocommerce_wpml;

		if ( is_admin() ) {
			add_filter( 'init', array( $this, 'orders_init' ) );
		}

		add_action( 'woocommerce_view_order', array( $this, 'show_price_in_client_currency' ), 9 );
	}

	public function orders_init() {
		global $wp;

		add_action( 'restrict_manage_posts', array( $this, 'show_orders_currencies_selector' ) );
		$wp->add_query_var( '_order_currency' );

		add_filter( 'posts_join', array( $this, 'filter_orders_by_currency_join' ) );
		add_filter( 'posts_where', array( $this, 'filter_orders_by_currency_where' ) );

		// override current currency value when viewing order in admin
		add_filter( 'woocommerce_currency_symbol', array( $this, '_use_order_currency_symbol' ) );

		//new order currency/language switchers
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'set_order_currency_on_update' ), 10, 2 );
		add_action( 'woocommerce_order_actions_start', array( $this, 'show_order_currency_selector' ) );

		add_filter( 'woocommerce_order_get_items', array( $this, 'set_totals_for_order_items' ) );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'add_woocommerce_hidden_order_itemmeta' ) );

		add_action( 'wp_ajax_wcml_order_set_currency', array( $this, 'set_order_currency_on_ajax_update' ) );

		//dashboard status screen
		if ( current_user_can( 'view_woocommerce_reports' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'publish_shop_orders' ) ) {
			//filter query to get order by status
			add_filter( 'query', array( $this, 'filter_order_status_query' ) );
		}

		add_action( 'woocommerce_email_before_order_table', array( $this, 'fix_currency_before_order_email' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'fix_currency_after_order_email' ) );

		if ( is_admin() ) {
			add_filter( 'woocommerce_order_get_currency', array( $this, 'get_currency_for_new_order' ), 10, 2 );
		}

	}

	public function get_orders_currencies() {
		global $wpdb;

		$cache_key              = 'wcml_get_orders_currencies';
		$temp_orders_currencies = wp_cache_get( $cache_key );
		if ( $temp_orders_currencies ) {
			return $temp_orders_currencies;
		}

		$currencies = array();

		$results = $wpdb->get_results( "
            SELECT m.meta_value AS currency, COUNT(m.post_id) AS c
            FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} m ON p.ID = m.post_id
            WHERE meta_key='_order_currency' AND p.post_type='shop_order'
            GROUP BY meta_value
        " );

		foreach ( $results as $row ) {
			$currencies[ $row->currency ] = (int) $row->c;
		}

		wp_cache_set( $cache_key, $currencies );

		return $currencies;

	}

	public function show_orders_currencies_selector() {
		global $wp_query, $typenow;

		if ( $typenow != 'shop_order' ) {
			return false;
		}

		$order_currencies = $this->get_orders_currencies();
		$currencies       = get_woocommerce_currencies();
		?>
        <select id="dropdown_shop_order_currency" name="_order_currency">
            <option value=""><?php _e( 'Show all currencies', 'woocommerce-multilingual' ) ?></option>
			<?php foreach ( $order_currencies as $currency => $count ): ?>
                <option value="<?php echo $currency ?>" <?php
				if ( isset( $wp_query->query['_order_currency'] ) ) {
					selected( $currency, $wp_query->query['_order_currency'] );
				}
				?> ><?php printf( "%s (%s) (%d)", $currencies[ $currency ], get_woocommerce_currency_symbol( $currency ), $count ) ?></option>
			<?php endforeach; ?>
        </select>
		<?php

	}

	public function filter_orders_by_currency_join( $join ) {
		global $wp_query, $typenow, $wpdb;

		if ( $typenow == 'shop_order' && ! empty( $wp_query->query['_order_currency'] ) ) {
			$join .= " JOIN {$wpdb->postmeta} wcml_pm ON {$wpdb->posts}.ID = wcml_pm.post_id AND wcml_pm.meta_key='_order_currency'";
		}

		return $join;
	}

	public function filter_orders_by_currency_where( $where ) {
		global $wp_query, $typenow;

		if ( $typenow == 'shop_order' && ! empty( $wp_query->query['_order_currency'] ) ) {
			$where .= " AND wcml_pm.meta_value = '" . esc_sql( $wp_query->query['_order_currency'] ) . "'";
		}

		return $where;
	}

	public function _use_order_currency_symbol( $currency ) {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return $currency;
		}

		$current_screen = get_current_screen();

		remove_filter( 'woocommerce_currency_symbol', array( $this, '_use_order_currency_symbol' ) );
		if ( ! empty( $current_screen ) && $current_screen->id == 'shop_order' ) {

			$the_order = new WC_Order( get_the_ID() );
			if ( $the_order ) {
				$order_currency = WooCommerce_Functions_Wrapper::get_order_currency( $the_order );

				if ( ! $order_currency && isset( $_COOKIE['_wcml_order_currency'] ) ) {
					$order_currency = $_COOKIE['_wcml_order_currency'];
				}

				$currency = get_woocommerce_currency_symbol( $order_currency );
			}

		} elseif (
			(
				isset( $_POST['action'] ) &&
				in_array( $_POST['action'], array(
						'woocommerce_add_order_item',
						'woocommerce_calc_line_taxes',
						'woocommerce_save_order_items'
					)
				) )
			|| (
				isset( $_GET['action'] ) &&
				$_GET['action'] == 'woocommerce_json_search_products_and_variations'
			)
		) {


			if ( isset( $_COOKIE['_wcml_order_currency'] ) ) {
				$currency = get_woocommerce_currency_symbol( $_COOKIE['_wcml_order_currency'] );
			} elseif ( isset( $_POST['order_id'] ) && $order_currency = get_post_meta( sanitize_text_field( $_POST['order_id'] ), '_order_currency', true ) ) {
				$currency = get_woocommerce_currency_symbol( $order_currency );
			}

			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				$arg = parse_url( $_SERVER['HTTP_REFERER'] );
				if ( isset( $arg['query'] ) ) {
					parse_str( $arg['query'], $arg );
					if ( isset( $arg['post'] ) && get_post_type( $arg['post'] ) == 'shop_order' ) {
						$currency = get_woocommerce_currency_symbol( get_post_meta( $arg['post'], '_order_currency', true ) );
					}
				}
			}
		}

		add_filter( 'woocommerce_currency_symbol', array( $this, '_use_order_currency_symbol' ) );

		return $currency;
	}

	public function set_order_currency_on_update( $post_id, $post ) {

		if ( isset( $_POST['wcml_shop_order_currency'] ) ) {
			update_post_meta( $post_id, '_order_currency', filter_input( INPUT_POST, 'wcml_shop_order_currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		}

	}

	public function show_order_currency_selector( $order_id ) {
		if ( ! get_post_meta( $order_id, '_order_currency' ) ) {

			$current_order_currency = $this->get_order_currency_cookie();

			$wc_currencies = get_woocommerce_currencies();
			$currencies    = $this->multi_currency->get_currency_codes();

			?>
            <li class="wide">
                <label><?php _e( 'Order currency:', 'woocommerce-multilingual' ); ?></label>
                <select id="dropdown_shop_order_currency" name="wcml_shop_order_currency">

					<?php foreach ( $currencies as $currency ): ?>

                        <option value="<?php echo $currency ?>" <?php echo $current_order_currency == $currency ? 'selected="selected"' : ''; ?>><?php echo $wc_currencies[ $currency ]; ?></option>

					<?php endforeach; ?>

                </select>
            </li>
			<?php
			$wcml_order_set_currency_nonce = wp_create_nonce( 'set_order_currency' );

			wc_enqueue_js( "
                var order_currency_current_value = jQuery('#dropdown_shop_order_currency option:selected').val();

                jQuery('#dropdown_shop_order_currency').on('change', function(){

                    if(confirm('" . esc_js( __( "All the products will be removed from the current order in order to change the currency", 'woocommerce-multilingual' ) ) . "')){
                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                action: 'wcml_order_set_currency',
                                currency: jQuery('#dropdown_shop_order_currency option:selected').val(),
                                wcml_nonce: '" . $wcml_order_set_currency_nonce . "'
                                },
                            success: function( response ){
                                if(typeof response.error !== 'undefined'){
                                    alert(response.error);
                                }else{
                                   window.location = window.location.href;
                                }
                            }
                        });
                    }else{
                        jQuery(this).val( order_currency_current_value );
                        return false;
                    }

                });

            " );

		}

	}

	public function set_totals_for_order_items( $items ) {

		if ( isset( $_POST['action'] ) && 'woocommerce_add_order_item' === $_POST['action'] ) {

			foreach ( $items as $item ) {
				$this->set_converted_totals_for_item( $item );
			}
		}

		return $items;
	}

	public function add_woocommerce_hidden_order_itemmeta( $itemmeta ) {

		$itemmeta[] = '_wcml_converted_subtotal';
		$itemmeta[] = '_wcml_converted_total';

		return $itemmeta;
	}

	private function set_converted_totals_for_item( $item ) {

		$order_currency = get_post_meta( $_POST['order_id'], '_order_currency', true );

		if ( ! $order_currency ) {
			$order_currency = $this->get_order_currency_cookie();
		}

		if ( ! isset( $this->multi_currency->prices ) ) {
			$this->multi_currency->prices = new WCML_Multi_Currency_Prices( $this->multi_currency );
			$this->multi_currency->prices->add_hooks();
			$this->multi_currency->prices->prices_init();
		}

		if ( $item instanceof WC_Order_Item_Product ) {

			$original_product_id = $this->woocommerce_wpml->products->get_original_product_id( $item->get_product_id() );

			if ( 'line_item' === $item->get_type() ) {

				$converted_price = get_post_meta( $original_product_id, '_price_' . $order_currency, true );

				if ( ! $converted_price ) {
					if( $item->meta_exists( '_wcml_converted_subtotal' ) ){
						$converted_subtotal_price = $item->get_meta( '_wcml_converted_subtotal' ) * $item->get_quantity();
					}else{
						$converted_subtotal_price = $this->multi_currency->prices->raw_price_filter( $item->get_subtotal(), $order_currency );
						$item->add_meta_data('_wcml_converted_subtotal', $converted_subtotal_price );
					}

					if( $item->meta_exists( '_wcml_converted_total' ) ){
						$converted_total_price = $item->get_meta( '_wcml_converted_total' ) * $item->get_quantity();
					}else{
						$converted_total_price = $this->multi_currency->prices->raw_price_filter( $item->get_total(), $order_currency );
						$item->add_meta_data('_wcml_converted_total', $converted_total_price );
					}
				}else{

					$converted_price = wc_get_price_excluding_tax( $item->get_product(), array( 'price' => $converted_price, 'qty' => $item->get_quantity() ) );

					$converted_subtotal_price = $converted_price;
					$converted_total_price    = $converted_price;
                }

				$item->set_subtotal( $converted_subtotal_price );
				$item->set_total( $converted_total_price );
				$item->save();
			}
		} else {

			$original_product_id = $this->woocommerce_wpml->products->get_original_product_id( $item[ 'product_id' ] );

			$converted_price = $converted_subtotal_price = $converted_total_price = get_post_meta( $original_product_id, '_price_' . $order_currency, true );

			if ( ! $converted_price ) {
				if( isset( $item[ '_wcml_converted_subtotal' ] ) ){
					$converted_subtotal_price = $item[ '_wcml_converted_subtotal' ] * $item['quantity'];
				}else{
					$converted_subtotal_price = $this->multi_currency->prices->raw_price_filter( $item['line_subtotal'], $order_currency );
					$item[ '_wcml_converted_subtotal' ] = $converted_subtotal_price;
				}

				if( isset( $item[ '_wcml_converted_total' ] ) ){
					$converted_total_price = $item[ '_wcml_converted_total' ] * $item['quantity'];
				}else{
					$converted_total_price = $this->multi_currency->prices->raw_price_filter( $item['line_total'] , $order_currency );
					$item[ '_wcml_converted_total' ] = $converted_total_price;
				}
			}else{

				$converted_price = wc_get_price_excluding_tax( wc_get_product( $item[ 'product_id' ] ), array( 'price' => $converted_price, 'qty' => $item['quantity'] ) );

				$converted_subtotal_price = $converted_price;
				$converted_total_price    = $converted_price;
			}

			$item['line_subtotal']      = $converted_subtotal_price;
			$item['line_total']         = $converted_total_price;
			$item['mc_total_converted'] = true;
		}

		return array( $item, $order_currency );
	}

	public function get_order_currency_cookie() {

		if ( isset( $_COOKIE['_wcml_order_currency'] ) ) {
			return $_COOKIE['_wcml_order_currency'];
		} else {
			return get_option( 'woocommerce_currency' );
		}

	}

	public function set_order_currency_on_ajax_update() {
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'set_order_currency' ) ) {
			echo json_encode( array( 'error' => __( 'Invalid nonce', 'woocommerce-multilingual' ) ) );
			die();
		}
		$currency = filter_input( INPUT_POST, 'currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$cookie_name = '_wcml_order_currency';
		// @todo uncomment or delete when #wpmlcore-5796 is resolved
		//do_action( 'wpsc_add_cookie', $cookie_name );
		setcookie( $cookie_name, $currency, time() + 86400, COOKIEPATH, COOKIE_DOMAIN );

		$return['currency'] = $currency;

		echo json_encode( $return );

		die();
	}

	/*
	* Filter status query
	*
	* @param string $query
	*
	* @return string
	*
	*/
	public function filter_order_status_query( $query ) {
		global $pagenow, $wpdb;

		if ( $pagenow == 'index.php' ) {
			$sql = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = 'shop_order' GROUP BY post_status";

			if ( $query == $sql ) {

				$currency = $this->multi_currency->admin_currency_selector->get_cookie_dashboard_currency();
				$query    = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts}
                          WHERE post_type = 'shop_order' AND ID IN
                            ( SELECT order_currency.post_id FROM {$wpdb->postmeta} AS order_currency
                              WHERE order_currency.meta_key = '_order_currency'
                                AND order_currency.meta_value = '{$currency}' )
                                GROUP BY post_status";

			}
		}

		return $query;
	}

	// handle currency in order emails before handled in woocommerce
	public function fix_currency_before_order_email( $order ) {

		$order_currency = WooCommerce_Functions_Wrapper::get_order_currency( $order );

		if ( ! $order_currency ) {
			return;
		}

		$this->order_currency = $order_currency;
		add_filter( 'woocommerce_currency', array( $this, '_override_woocommerce_order_currency_temporarily' ) );
	}

	public function fix_currency_after_order_email( $order ) {
		unset( $this->order_currency );
		remove_filter( 'woocommerce_currency', array( $this, '_override_woocommerce_order_currency_temporarily' ) );
	}

	public function _override_woocommerce_order_currency_temporarily( $currency ) {
		if ( isset( $this->order_currency ) ) {
			$currency = $this->order_currency;
		}

		return $currency;
	}

	public function show_price_in_client_currency( $order_id ) {
		$currency_code = get_post_meta( $order_id, '_order_currency', true );

		$this->client_currency = $currency_code;
	}

	public function get_currency_for_new_order( $value, $order ) {

		if ( did_action( 'current_screen' ) ) {
			$current_screen = get_current_screen();
			if ( ! empty( $current_screen ) && $current_screen->id == 'shop_order' ) {
				$order_id       = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
				$order_currency = get_post_meta( $order_id, '_order_currency', true );
				if ( empty( $order_currency ) ) {
					$value = $this->get_order_currency_cookie();
				}
			}
		}

		return $value;
	}

}
