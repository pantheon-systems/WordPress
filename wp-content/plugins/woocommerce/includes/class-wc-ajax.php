<?php
/**
 * WooCommerce WC_AJAX. AJAX Event Handlers.
 *
 * @class   WC_AJAX
 * @package WooCommerce/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Ajax class.
 */
class WC_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_wc_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get WC Ajax Endpoint.
	 *
	 * @param string $request Optional.
	 *
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'woocommerce_ajax_get_endpoint', add_query_arg( 'wc-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart', 'order_again', '_wpnonce' ), home_url( '/', 'relative' ) ) ), $request ) );
	}

	/**
	 * Set WC AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['wc-ajax'] ) ) {
			wc_maybe_define_constant( 'DOING_AJAX', true );
			wc_maybe_define_constant( 'WC_DOING_AJAX', true );
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send headers for WC Ajax Requests.
	 *
	 * @since 2.5.0
	 */
	private static function wc_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		wc_nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for WC Ajax request and fire action.
	 */
	public static function do_wc_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['wc-ajax'] ) ) {
			$wp_query->set( 'wc-ajax', sanitize_text_field( wp_unslash( $_GET['wc-ajax'] ) ) );
		}

		$action = $wp_query->get( 'wc-ajax' );

		if ( $action ) {
			self::wc_ajax_headers();
			$action = sanitize_text_field( $action );
			do_action( 'wc_ajax_' . $action );
			wp_die();
		}
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		// woocommerce_EVENT => nopriv.
		$ajax_events = array(
			'get_refreshed_fragments'                          => true,
			'apply_coupon'                                     => true,
			'remove_coupon'                                    => true,
			'update_shipping_method'                           => true,
			'get_cart_totals'                                  => true,
			'update_order_review'                              => true,
			'add_to_cart'                                      => true,
			'remove_from_cart'                                 => true,
			'checkout'                                         => true,
			'get_variation'                                    => true,
			'get_customer_location'                            => true,
			'feature_product'                                  => false,
			'mark_order_status'                                => false,
			'get_order_details'                                => false,
			'add_attribute'                                    => false,
			'add_new_attribute'                                => false,
			'remove_variation'                                 => false,
			'remove_variations'                                => false,
			'save_attributes'                                  => false,
			'add_variation'                                    => false,
			'link_all_variations'                              => false,
			'revoke_access_to_download'                        => false,
			'grant_access_to_download'                         => false,
			'get_customer_details'                             => false,
			'add_order_item'                                   => false,
			'add_order_fee'                                    => false,
			'add_order_shipping'                               => false,
			'add_order_tax'                                    => false,
			'add_coupon_discount'                              => false,
			'remove_order_coupon'                              => false,
			'remove_order_item'                                => false,
			'remove_order_tax'                                 => false,
			'reduce_order_item_stock'                          => false,
			'increase_order_item_stock'                        => false,
			'add_order_item_meta'                              => false,
			'remove_order_item_meta'                           => false,
			'calc_line_taxes'                                  => false,
			'save_order_items'                                 => false,
			'load_order_items'                                 => false,
			'add_order_note'                                   => false,
			'delete_order_note'                                => false,
			'json_search_products'                             => false,
			'json_search_products_and_variations'              => false,
			'json_search_downloadable_products_and_variations' => false,
			'json_search_customers'                            => false,
			'json_search_categories'                           => false,
			'term_ordering'                                    => false,
			'product_ordering'                                 => false,
			'refund_line_items'                                => false,
			'delete_refund'                                    => false,
			'rated'                                            => false,
			'update_api_key'                                   => false,
			'load_variations'                                  => false,
			'save_variations'                                  => false,
			'bulk_edit_variations'                             => false,
			'tax_rates_save_changes'                           => false,
			'shipping_zones_save_changes'                      => false,
			'shipping_zone_add_method'                         => false,
			'shipping_zone_methods_save_changes'               => false,
			'shipping_zone_methods_save_settings'              => false,
			'shipping_classes_save_changes'                    => false,
			'toggle_gateway_enabled'                           => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );

				// WC AJAX can be used for frontend ajax requests.
				add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Get a refreshed cart fragment, including the mini cart HTML.
	 */
	public static function get_refreshed_fragments() {
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		$data = array(
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
		);

		wp_send_json( $data );
	}

	/**
	 * AJAX apply coupon on checkout page.
	 */
	public static function apply_coupon() {

		check_ajax_referer( 'apply-coupon', 'security' );

		if ( ! empty( $_POST['coupon_code'] ) ) {
			WC()->cart->add_discount( sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) );
		} else {
			wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER ), 'error' );
		}

		wc_print_notices();
		wp_die();
	}

	/**
	 * AJAX remove coupon on cart and checkout page.
	 */
	public static function remove_coupon() {
		check_ajax_referer( 'remove-coupon', 'security' );

		$coupon = isset( $_POST['coupon'] ) ? wc_clean( $_POST['coupon'] ) : false;

		if ( empty( $coupon ) ) {
			wc_add_notice( __( 'Sorry there was a problem removing this coupon.', 'woocommerce' ), 'error' );
		} else {
			WC()->cart->remove_coupon( $coupon );
			wc_add_notice( __( 'Coupon has been removed.', 'woocommerce' ) );
		}

		wc_print_notices();
		wp_die();
	}

	/**
	 * AJAX update shipping method on cart page.
	 */
	public static function update_shipping_method() {
		check_ajax_referer( 'update-shipping-method', 'security' );

		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
			foreach ( $_POST['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

		self::get_cart_totals();
	}

	/**
	 * AJAX receive updated cart_totals div.
	 */
	public static function get_cart_totals() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
		WC()->cart->calculate_totals();
		woocommerce_cart_totals();
		wp_die();
	}

	/**
	 * Session has expired.
	 */
	private static function update_order_review_expired() {
		wp_send_json(
			array(
				'fragments' => apply_filters(
					'woocommerce_update_order_review_fragments', array(
						'form.woocommerce-checkout' => '<div class="woocommerce-error">' . __( 'Sorry, your session has expired.', 'woocommerce' ) . ' <a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="wc-backward">' . __( 'Return to shop', 'woocommerce' ) . '</a></div>',
					)
				),
			)
		);
	}

	/**
	 * AJAX update order review on checkout.
	 */
	public static function update_order_review() {
		check_ajax_referer( 'update-order-review', 'security' );

		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_update_order_review_expired', true ) ) {
			self::update_order_review_expired();
		}

		do_action( 'woocommerce_checkout_update_order_review', $_POST['post_data'] );

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
			foreach ( $_POST['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->session->set( 'chosen_payment_method', empty( $_POST['payment_method'] ) ? '' : $_POST['payment_method'] );
		WC()->customer->set_props(
			array(
				'billing_country'   => isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : null,
				'billing_state'     => isset( $_POST['state'] ) ? wp_unslash( $_POST['state'] ) : null,
				'billing_postcode'  => isset( $_POST['postcode'] ) ? wp_unslash( $_POST['postcode'] ) : null,
				'billing_city'      => isset( $_POST['city'] ) ? wp_unslash( $_POST['city'] ) : null,
				'billing_address_1' => isset( $_POST['address'] ) ? wp_unslash( $_POST['address'] ) : null,
				'billing_address_2' => isset( $_POST['address_2'] ) ? wp_unslash( $_POST['address_2'] ) : null,
			)
		);

		if ( wc_ship_to_billing_address_only() ) {
			WC()->customer->set_props(
				array(
					'shipping_country'   => isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : null,
					'shipping_state'     => isset( $_POST['state'] ) ? wp_unslash( $_POST['state'] ) : null,
					'shipping_postcode'  => isset( $_POST['postcode'] ) ? wp_unslash( $_POST['postcode'] ) : null,
					'shipping_city'      => isset( $_POST['city'] ) ? wp_unslash( $_POST['city'] ) : null,
					'shipping_address_1' => isset( $_POST['address'] ) ? wp_unslash( $_POST['address'] ) : null,
					'shipping_address_2' => isset( $_POST['address_2'] ) ? wp_unslash( $_POST['address_2'] ) : null,
				)
			);
		} else {
			WC()->customer->set_props(
				array(
					'shipping_country'   => isset( $_POST['s_country'] ) ? wp_unslash( $_POST['s_country'] ) : null,
					'shipping_state'     => isset( $_POST['s_state'] ) ? wp_unslash( $_POST['s_state'] ) : null,
					'shipping_postcode'  => isset( $_POST['s_postcode'] ) ? wp_unslash( $_POST['s_postcode'] ) : null,
					'shipping_city'      => isset( $_POST['s_city'] ) ? wp_unslash( $_POST['s_city'] ) : null,
					'shipping_address_1' => isset( $_POST['s_address'] ) ? wp_unslash( $_POST['s_address'] ) : null,
					'shipping_address_2' => isset( $_POST['s_address_2'] ) ? wp_unslash( $_POST['s_address_2'] ) : null,
				)
			);
		}

		if ( wc_string_to_bool( $_POST['has_full_address'] ) ) {
			WC()->customer->set_calculated_shipping( true );
		} else {
			WC()->customer->set_calculated_shipping( false );
		}

		WC()->customer->save();
		WC()->cart->calculate_totals();

		// Get order review fragment
		ob_start();
		woocommerce_order_review();
		$woocommerce_order_review = ob_get_clean();

		// Get checkout payment fragment
		ob_start();
		woocommerce_checkout_payment();
		$woocommerce_checkout_payment = ob_get_clean();

		// Get messages if reload checkout is not true
		if ( ! isset( WC()->session->reload_checkout ) ) {
			$messages = wc_print_notices( true );
		} else {
			$messages = '';
		}

		unset( WC()->session->refresh_totals, WC()->session->reload_checkout );

		wp_send_json(
			array(
				'result'    => empty( $messages ) ? 'success' : 'failure',
				'messages'  => $messages,
				'reload'    => isset( WC()->session->reload_checkout ) ? 'true' : 'false',
				'fragments' => apply_filters(
					'woocommerce_update_order_review_fragments', array(
						'.woocommerce-checkout-review-order-table' => $woocommerce_order_review,
						'.woocommerce-checkout-payment' => $woocommerce_checkout_payment,
					)
				),
			)
		);
	}

	/**
	 * AJAX add to cart.
	 */
	public static function add_to_cart() {
		ob_start();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			// Return fragments
			self::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
	}

	/**
	 * AJAX remove from cart.
	 */
	public static function remove_from_cart() {
		ob_start();

		$cart_item_key = wc_clean( $_POST['cart_item_key'] );

		if ( $cart_item_key && false !== WC()->cart->remove_cart_item( $cart_item_key ) ) {
			self::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Process ajax checkout form.
	 */
	public static function checkout() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
		WC()->checkout()->process_checkout();
		wp_die( 0 );
	}

	/**
	 * Get a matching variation based on posted attributes.
	 */
	public static function get_variation() {
		ob_start();

		if ( empty( $_POST['product_id'] ) || ! ( $variable_product = wc_get_product( absint( $_POST['product_id'] ) ) ) ) {
			wp_die();
		}

		$data_store   = WC_Data_Store::load( 'product' );
		$variation_id = $data_store->find_matching_product_variation( $variable_product, wp_unslash( $_POST ) );
		$variation    = $variation_id ? $variable_product->get_available_variation( $variation_id ) : false;
		wp_send_json( $variation );
	}

	/**
	 * Locate user via AJAX.
	 */
	public static function get_customer_location() {
		$location_hash = WC_Cache_Helper::geolocation_ajax_get_location_hash();
		wp_send_json_success( array( 'hash' => $location_hash ) );
	}

	/**
	 * Toggle Featured status of a product from admin.
	 */
	public static function feature_product() {
		if ( current_user_can( 'edit_products' ) && check_admin_referer( 'woocommerce-feature-product' ) ) {
			$product = wc_get_product( absint( $_GET['product_id'] ) );

			if ( $product ) {
				$product->set_featured( ! $product->get_featured() );
				$product->save();
			}
		}

		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=product' ) );
		exit;
	}

	/**
	 * Mark an order with a status.
	 */
	public static function mark_order_status() {
		if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'woocommerce-mark-order-status' ) ) {
			$status = sanitize_text_field( $_GET['status'] );
			$order  = wc_get_order( absint( $_GET['order_id'] ) );

			if ( wc_is_order_status( 'wc-' . $status ) && $order ) {
				// Initialize payment gateways in case order has hooked status transition actions.
				wc()->payment_gateways();

				$order->update_status( $status, '', true );
				do_action( 'woocommerce_order_edit_status', $order->get_id(), $status );
			}
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
		exit;
	}

	/**
	 * Get order details.
	 */
	public static function get_order_details() {
		check_admin_referer( 'woocommerce-preview-order', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$order = wc_get_order( absint( $_GET['order_id'] ) ); // WPCS: sanitization ok.

		if ( $order ) {
			include_once 'admin/list-tables/class-wc-admin-list-table-orders.php';

			wp_send_json_success( WC_Admin_List_Table_Orders::order_preview_get_order_details( $order ) );
		}
		wp_die();
	}

	/**
	 * Add an attribute row.
	 */
	public static function add_attribute() {
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$i             = absint( $_POST['i'] );
		$metabox_class = array();
		$attribute     = new WC_Product_Attribute();

		$attribute->set_id( wc_attribute_taxonomy_id_by_name( sanitize_text_field( $_POST['taxonomy'] ) ) );
		$attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
		$attribute->set_visible( apply_filters( 'woocommerce_attribute_default_visibility', 1 ) );
		$attribute->set_variation( apply_filters( 'woocommerce_attribute_default_is_variation', 0 ) );

		if ( $attribute->is_taxonomy() ) {
			$metabox_class[] = 'taxonomy';
			$metabox_class[] = $attribute->get_name();
		}

		include 'admin/meta-boxes/views/html-product-attribute.php';
		wp_die();
	}

	/**
	 * Add a new attribute via ajax function.
	 */
	public static function add_new_attribute() {
		check_ajax_referer( 'add-attribute', 'security' );

		if ( current_user_can( 'manage_product_terms' ) ) {
			$taxonomy = esc_attr( $_POST['taxonomy'] );
			$term     = wc_clean( $_POST['term'] );

			if ( taxonomy_exists( $taxonomy ) ) {

				$result = wp_insert_term( $term, $taxonomy );

				if ( is_wp_error( $result ) ) {
					wp_send_json(
						array(
							'error' => $result->get_error_message(),
						)
					);
				} else {
					$term = get_term_by( 'id', $result['term_id'], $taxonomy );
					wp_send_json(
						array(
							'term_id' => $term->term_id,
							'name'    => $term->name,
							'slug'    => $term->slug,
						)
					);
				}
			}
		}
		wp_die( -1 );
	}

	/**
	 * Delete variations via ajax function.
	 */
	public static function remove_variations() {
		check_ajax_referer( 'delete-variations', 'security' );

		if ( current_user_can( 'edit_products' ) ) {
			$variation_ids = (array) $_POST['variation_ids'];

			foreach ( $variation_ids as $variation_id ) {
				if ( 'product_variation' === get_post_type( $variation_id ) ) {
					$variation = wc_get_product( $variation_id );
					$variation->delete( true );
				}
			}
		}

		wp_die( -1 );
	}

	/**
	 * Save attributes via ajax.
	 */
	public static function save_attributes() {
		check_ajax_referer( 'save-attributes', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		try {
			parse_str( $_POST['data'], $data );

			$attributes   = WC_Meta_Box_Product_Data::prepare_attributes( $data );
			$product_id   = absint( $_POST['post_id'] );
			$product_type = ! empty( $_POST['product_type'] ) ? wc_clean( $_POST['product_type'] ) : 'simple';
			$classname    = WC_Product_Factory::get_product_classname( $product_id, $product_type );
			$product      = new $classname( $product_id );

			$product->set_attributes( $attributes );
			$product->save();

			$response = array();

			ob_start();
			$attributes = $product->get_attributes( 'edit' );
			$i          = -1;

			foreach ( $data['attribute_names'] as $attribute_name ) {
				$attribute = isset( $attributes[ sanitize_title( $attribute_name ) ] ) ? $attributes[ sanitize_title( $attribute_name ) ] : false;
				if ( ! $attribute ) {
					continue;
				}
				$i++;
				$metabox_class = array();

				if ( $attribute->is_taxonomy() ) {
					$metabox_class[] = 'taxonomy';
					$metabox_class[] = $attribute->get_name();
				}

				include( 'admin/meta-boxes/views/html-product-attribute.php' );
			}

			$response['html'] = ob_get_clean();

			wp_send_json_success( $response );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
		wp_die();
	}

	/**
	 * Add variation via ajax function.
	 */
	public static function add_variation() {
		check_ajax_referer( 'add-variation', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		global $post; // Set $post global so its available, like within the admin screens.

		$product_id       = intval( $_POST['post_id'] );
		$post             = get_post( $product_id );
		$loop             = intval( $_POST['loop'] );
		$product_object   = wc_get_product( $product_id );
		$variation_object = new WC_Product_Variation();
		$variation_object->set_parent_id( $product_id );
		$variation_object->set_attributes( array_fill_keys( array_map( 'sanitize_title', array_keys( $product_object->get_variation_attributes() ) ), '' ) );
		$variation_id   = $variation_object->save();
		$variation      = get_post( $variation_id );
		$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
		include 'admin/meta-boxes/views/html-variation-admin.php';
		wp_die();
	}

	/**
	 * Link all variations via ajax function.
	 */
	public static function link_all_variations() {
		check_ajax_referer( 'link-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		wc_maybe_define_constant( 'WC_MAX_LINKED_VARIATIONS', 49 );
		wc_set_time_limit( 0 );

		$post_id = intval( $_POST['post_id'] );

		if ( ! $post_id ) {
			wp_die();
		}

		$product    = wc_get_product( $post_id );
		$attributes = wc_list_pluck( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_variation' ), 'get_slugs' );

		if ( ! empty( $attributes ) ) {
			// Get existing variations so we don't create duplicates.
			$existing_variations = array_map( 'wc_get_product', $product->get_children() );
			$existing_attributes = array();

			foreach ( $existing_variations as $existing_variation ) {
				$existing_attributes[] = $existing_variation->get_attributes();
			}

			$added               = 0;
			$possible_attributes = array_reverse( wc_array_cartesian( $attributes ) );

			foreach ( $possible_attributes as $possible_attribute ) {
				if ( in_array( $possible_attribute, $existing_attributes ) ) {
					continue;
				}
				$variation = new WC_Product_Variation();
				$variation->set_parent_id( $post_id );
				$variation->set_attributes( $possible_attribute );

				do_action( 'product_variation_linked', $variation->save() );

				if ( ( $added ++ ) > WC_MAX_LINKED_VARIATIONS ) {
					break;
				}
			}

			echo $added;
		}

		$data_store = $product->get_data_store();
		$data_store->sort_all_product_variations( $product->get_id() );
		wp_die();
	}

	/**
	 * Delete download permissions via ajax function.
	 */
	public static function revoke_access_to_download() {
		check_ajax_referer( 'revoke-access', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}
		$download_id   = $_POST['download_id'];
		$product_id    = intval( $_POST['product_id'] );
		$order_id      = intval( $_POST['order_id'] );
		$permission_id = absint( $_POST['permission_id'] );
		$data_store    = WC_Data_Store::load( 'customer-download' );
		$data_store->delete_by_id( $permission_id );

		do_action( 'woocommerce_ajax_revoke_access_to_product_download', $download_id, $product_id, $order_id, $permission_id );

		wp_die();
	}

	/**
	 * Grant download permissions via ajax function.
	 */
	public static function grant_access_to_download() {

		check_ajax_referer( 'grant-access', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		global $wpdb;

		$wpdb->hide_errors();

		$order_id     = intval( $_POST['order_id'] );
		$product_ids  = $_POST['product_ids'];
		$loop         = intval( $_POST['loop'] );
		$file_counter = 0;
		$order        = wc_get_order( $order_id );

		if ( ! is_array( $product_ids ) ) {
			$product_ids = array( $product_ids );
		}

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			$files   = $product->get_downloads();

			if ( ! $order->get_billing_email() ) {
				wp_die();
			}

			if ( ! empty( $files ) ) {
				foreach ( $files as $download_id => $file ) {
					if ( $inserted_id = wc_downloadable_file_permission( $download_id, $product_id, $order ) ) {
						$download = new WC_Customer_Download( $inserted_id );
						$loop ++;
						$file_counter ++;

						if ( $file->get_name() ) {
							$file_count = $file->get_name();
						} else {
							/* translators: %d file count */
							$file_count = sprintf( __( 'File %d', 'woocommerce' ), $file_counter );
						}
						include 'admin/meta-boxes/views/html-order-download-permission.php';
					}
				}
			}
		}
		wp_die();
	}

	/**
	 * Get customer details via ajax.
	 */
	public static function get_customer_details() {
		check_ajax_referer( 'get-customer-details', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$user_id  = absint( $_POST['user_id'] );
		$customer = new WC_Customer( $user_id );

		if ( has_filter( 'woocommerce_found_customer_details' ) ) {
			wc_deprecated_function( 'The woocommerce_found_customer_details filter', '3.0', 'woocommerce_ajax_get_customer_details' );
		}

		$data                  = $customer->get_data();
		$data['date_created']  = $data['date_created'] ? $data['date_created']->getTimestamp() : null;
		$data['date_modified'] = $data['date_modified'] ? $data['date_modified']->getTimestamp() : null;

		$customer_data = apply_filters( 'woocommerce_ajax_get_customer_details', $data, $customer, $user_id );
		wp_send_json( $customer_data );
	}

	/**
	 * Add order item via ajax.
	 */
	public static function add_order_item() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			if ( ! isset( $_POST['order_id'] ) ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			$order_id     = absint( wp_unslash( $_POST['order_id'] ) ); // WPCS: input var ok.
			$order        = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			// If we passed through items it means we need to save first before adding a new one.
			$items = ( ! empty( $_POST['items'] ) ) ? $_POST['items'] : '';

			if ( ! empty( $items ) ) {
				$save_items = array();
				parse_str( $items, $save_items );
				// Save order items.
				wc_save_order_items( $order->get_id(), $save_items );
			}

			$items_to_add = array_filter( wp_unslash( (array) $_POST['data'] ) );

			// Add items to order.
			foreach ( $items_to_add as $item ) {
				if ( ! isset( $item['id'], $item['qty'] ) || empty( $item['id'] ) ) {
					continue;
				}
				$product_id = absint( $item['id'] );
				$qty        = wc_stock_amount( $item['qty'] );
				$product    = wc_get_product( $product_id );

				if ( ! $product ) {
					throw new Exception( __( 'Invalid product ID', 'woocommerce' ) . ' ' . $product_id );
				}

				$item_id                 = $order->add_product( $product, $qty );
				$item                    = apply_filters( 'woocommerce_ajax_order_item', $order->get_item( $item_id ), $item_id );
				$added_items[ $item_id ] = $item;

				do_action( 'woocommerce_ajax_add_order_item_meta', $item_id, $item, $order );
			}

			do_action( 'woocommerce_ajax_order_items_added', $added_items, $order );

			$data = get_post_meta( $order_id );

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';
			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Add order fee via ajax.
	 */
	public static function add_order_fee() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id           = absint( $_POST['order_id'] );
			$amount             = wc_clean( $_POST['amount'] );
			$order              = wc_get_order( $order_id );
			$calculate_tax_args = array(
				'country'  => strtoupper( wc_clean( $_POST['country'] ) ),
				'state'    => strtoupper( wc_clean( $_POST['state'] ) ),
				'postcode' => strtoupper( wc_clean( $_POST['postcode'] ) ),
				'city'     => strtoupper( wc_clean( $_POST['city'] ) ),
			);

			if ( ! $order ) {
				throw new exception( __( 'Invalid order', 'woocommerce' ) );
			}

			if ( strstr( $amount, '%' ) ) {
				$formatted_amount = $amount;
				$percent          = floatval( trim( $amount, '%' ) );
				$amount           = $order->get_total() * ( $percent / 100 );
			} else {
				$amount           = floatval( $amount );
				$formatted_amount = wc_price( $amount, array( 'currency' => $order->get_currency() ) );
			}

			$fee = new WC_Order_Item_Fee();
			$fee->set_amount( $amount );
			$fee->set_total( $amount );
			/* translators: %s fee amount */
			$fee->set_name( sprintf( __( '%s fee', 'woocommerce' ), wc_clean( $formatted_amount ) ) );

			$order->add_item( $fee );
			$order->calculate_taxes( $calculate_tax_args );
			$order->calculate_totals( false );
			$order->save();

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Add order shipping cost via ajax.
	 */
	public static function add_order_shipping() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id         = absint( $_POST['order_id'] );
			$order            = wc_get_order( $order_id );
			$order_taxes      = $order->get_taxes();
			$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();

			// Add new shipping
			$item = new WC_Order_Item_Shipping();
			$item->set_shipping_rate( new WC_Shipping_Rate() );
			$item->set_order_id( $order_id );
			$item_id = $item->save();

			ob_start();
			include 'admin/meta-boxes/views/html-order-shipping.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Add order tax column via ajax.
	 */
	public static function add_order_tax() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id = absint( $_POST['order_id'] );
			$rate_id  = absint( $_POST['rate_id'] );
			$order    = wc_get_order( $order_id );
			$data     = get_post_meta( $order_id );

			// Add new tax
			$item = new WC_Order_Item_Tax();
			$item->set_rate( $rate_id );
			$item->set_order_id( $order_id );
			$item->save();

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Add order discount via ajax.
	 */
	public static function add_coupon_discount() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id = absint( $_POST['order_id'] );
			$order    = wc_get_order( $order_id );
			$result   = $order->apply_coupon( wc_clean( $_POST['coupon'] ) );

			if ( is_wp_error( $result ) ) {
				throw new Exception( html_entity_decode( wp_strip_all_tags( $result->get_error_message() ) ) );
			}

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Remove coupon from an order via ajax.
	 */
	public static function remove_order_coupon() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id = absint( $_POST['order_id'] );
			$order    = wc_get_order( $order_id );

			$order->remove_coupon( wc_clean( $_POST['coupon'] ) );

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Remove an order item.
	 */
	public static function remove_order_item() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id           = absint( $_POST['order_id'] );
			$order_item_ids     = $_POST['order_item_ids'];
			$items              = ( ! empty( $_POST['items'] ) ) ? $_POST['items'] : '';
			$calculate_tax_args = array(
				'country'  => strtoupper( wc_clean( $_POST['country'] ) ),
				'state'    => strtoupper( wc_clean( $_POST['state'] ) ),
				'postcode' => strtoupper( wc_clean( $_POST['postcode'] ) ),
				'city'     => strtoupper( wc_clean( $_POST['city'] ) ),
			);

			if ( ! is_array( $order_item_ids ) && is_numeric( $order_item_ids ) ) {
				$order_item_ids = array( $order_item_ids );
			}

			// If we passed through items it means we need to save first before deleting.
			if ( ! empty( $items ) ) {
				$save_items = array();
				parse_str( $items, $save_items );
				// Save order items
				wc_save_order_items( $order_id, $save_items );
			}

			if ( sizeof( $order_item_ids ) > 0 ) {
				foreach ( $order_item_ids as $id ) {
					wc_delete_order_item( absint( $id ) );
				}
			}

			$order = wc_get_order( $order_id );
			$order->calculate_taxes( $calculate_tax_args );
			$order->calculate_totals( false );

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Remove an order tax.
	 */
	public static function remove_order_tax() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		try {
			$order_id = absint( $_POST['order_id'] );
			$rate_id  = absint( $_POST['rate_id'] );

			wc_delete_order_item( $rate_id );

			$order = wc_get_order( $order_id );
			$order->calculate_totals( false );

			ob_start();
			include 'admin/meta-boxes/views/html-order-items.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Calc line tax.
	 */
	public static function calc_line_taxes() {
		check_ajax_referer( 'calc-totals', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$order_id           = absint( $_POST['order_id'] );
		$calculate_tax_args = array(
			'country'  => strtoupper( wc_clean( $_POST['country'] ) ),
			'state'    => strtoupper( wc_clean( $_POST['state'] ) ),
			'postcode' => strtoupper( wc_clean( $_POST['postcode'] ) ),
			'city'     => strtoupper( wc_clean( $_POST['city'] ) ),
		);

		// Parse the jQuery serialized items
		$items = array();
		parse_str( $_POST['items'], $items );

		// Save order items first
		wc_save_order_items( $order_id, $items );

		// Grab the order and recalculate taxes
		$order = wc_get_order( $order_id );
		$order->calculate_taxes( $calculate_tax_args );
		$order->calculate_totals( false );
		include 'admin/meta-boxes/views/html-order-items.php';
		wp_die();
	}

	/**
	 * Save order items via ajax.
	 */
	public static function save_order_items() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		if ( isset( $_POST['order_id'], $_POST['items'] ) ) {
			$order_id = absint( $_POST['order_id'] );

			// Parse the jQuery serialized items
			$items = array();
			parse_str( $_POST['items'], $items );

			// Save order items
			wc_save_order_items( $order_id, $items );

			// Return HTML items
			$order = wc_get_order( $order_id );
			include 'admin/meta-boxes/views/html-order-items.php';
		}
		wp_die();
	}

	/**
	 * Load order items via ajax.
	 */
	public static function load_order_items() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		// Return HTML items
		$order_id = absint( $_POST['order_id'] );
		$order    = wc_get_order( $order_id );
		include 'admin/meta-boxes/views/html-order-items.php';
		wp_die();
	}

	/**
	 * Add order note via ajax.
	 */
	public static function add_order_note() {
		check_ajax_referer( 'add-order-note', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$post_id   = absint( $_POST['post_id'] );
		$note      = wp_kses_post( trim( wp_unslash( $_POST['note'] ) ) );
		$note_type = $_POST['note_type'];

		$is_customer_note = ( 'customer' === $note_type ) ? 1 : 0;

		if ( $post_id > 0 ) {
			$order      = wc_get_order( $post_id );
			$comment_id = $order->add_order_note( $note, $is_customer_note, true );
			$note       = wc_get_order_note( $comment_id );

			$note_classes   = array( 'note' );
			$note_classes[] = $is_customer_note ? 'customer-note' : '';
			$note_classes   = apply_filters( 'woocommerce_order_note_class', array_filter( $note_classes ), $note );
			?>
			<li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
				<div class="note_content">
					<?php echo wpautop( wptexturize( wp_kses_post( $note->content ) ) ); ?>
				</div>
				<p class="meta">
					<abbr class="exact-date" title="<?php echo $note->date_created->date( 'y-m-d h:i:s' ); ?>">
						<?php
						/* translators: $1: Date created, $2 Time created */
						printf( __( 'added on %1$s at %2$s', 'woocommerce' ), $note->date_created->date_i18n( wc_date_format() ), $note->date_created->date_i18n( wc_time_format() ) );
						?>
					</abbr>
					<?php
					if ( 'system' !== $note->added_by ) :
						/* translators: %s: note author */
						printf( ' ' . __( 'by %s', 'woocommerce' ), $note->added_by );
					endif;
					?>
					<a href="#" class="delete_note" role="button"><?php _e( 'Delete note', 'woocommerce' ); ?></a>
				</p>
			</li>
			<?php
		}
		wp_die();
	}

	/**
	 * Delete order note via ajax.
	 */
	public static function delete_order_note() {
		check_ajax_referer( 'delete-order-note', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$note_id = (int) $_POST['note_id'];

		if ( $note_id > 0 ) {
			wc_delete_order_note( $note_id );
		}
		wp_die();
	}

	/**
	 * Search for products and echo json.
	 *
	 * @param string $term (default: '')
	 * @param bool   $include_variations in search or not
	 */
	public static function json_search_products( $term = '', $include_variations = false ) {
		check_ajax_referer( 'search-products', 'security' );

		$term = wc_clean( empty( $term ) ? wp_unslash( $_GET['term'] ) : $term );

		if ( empty( $term ) ) {
			wp_die();
		}

		if ( ! empty( $_GET['limit'] ) ) {
			$limit = absint( $_GET['limit'] );
		} else {
			$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
		}

		$data_store = WC_Data_Store::load( 'product' );
		$ids        = $data_store->search_products( $term, '', (bool) $include_variations, false, $limit );

		if ( ! empty( $_GET['exclude'] ) ) {
			$ids = array_diff( $ids, (array) $_GET['exclude'] );
		}

		if ( ! empty( $_GET['include'] ) ) {
			$ids = array_intersect( $ids, (array) $_GET['include'] );
		}

		$product_objects = array_filter( array_map( 'wc_get_product', $ids ), 'wc_products_array_filter_readable' );
		$products        = array();

		foreach ( $product_objects as $product_object ) {
			$formatted_name = $product_object->get_formatted_name();
			$managing_stock = $product_object->managing_stock();

			if ( $managing_stock && ! empty( $_GET['display_stock'] ) ) {
				$formatted_name .= ' &ndash; ' . wc_format_stock_for_display( $product_object );
			}

			$products[ $product_object->get_id() ] = rawurldecode( $formatted_name );
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_products', $products ) );
	}

	/**
	 * Search for product variations and return json.
	 *
	 * @see WC_AJAX::json_search_products()
	 */
	public static function json_search_products_and_variations() {
		self::json_search_products( '', true );
	}

	/**
	 * Search for downloadable product variations and return json.
	 *
	 * @see WC_AJAX::json_search_products()
	 */
	public static function json_search_downloadable_products_and_variations() {
		check_ajax_referer( 'search-products', 'security' );

		$term       = (string) wc_clean( wp_unslash( $_GET['term'] ) );
		$data_store = WC_Data_Store::load( 'product' );
		$ids        = $data_store->search_products( $term, 'downloadable', true );

		if ( ! empty( $_GET['exclude'] ) ) {
			$ids = array_diff( $ids, (array) $_GET['exclude'] );
		}

		if ( ! empty( $_GET['include'] ) ) {
			$ids = array_intersect( $ids, (array) $_GET['include'] );
		}

		if ( ! empty( $_GET['limit'] ) ) {
			$ids = array_slice( $ids, 0, absint( $_GET['limit'] ) );
		}

		$product_objects = array_filter( array_map( 'wc_get_product', $ids ), 'wc_products_array_filter_readable' );
		$products        = array();

		foreach ( $product_objects as $product_object ) {
			$products[ $product_object->get_id() ] = rawurldecode( $product_object->get_formatted_name() );
		}

		wp_send_json( $products );
	}

	/**
	 * Search for customers and return json.
	 */
	public static function json_search_customers() {
		ob_start();

		check_ajax_referer( 'search-customers', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$term    = wc_clean( wp_unslash( $_GET['term'] ) );
		$exclude = array();
		$limit   = '';

		if ( empty( $term ) ) {
			wp_die();
		}

		$ids = array();
		// Search by ID.
		if ( is_numeric( $term ) ) {
			$customer = new WC_Customer( intval( $term ) );

			// Customer does not exists.
			if ( 0 !== $customer->get_id() ) {
				$ids = array( $customer->get_id() );
			}
		}

		// Usernames can be numeric so we first check that no users was found by ID before searching for numeric username, this prevents performance issues with ID lookups.
		if ( empty( $ids ) ) {
			$data_store = WC_Data_Store::load( 'customer' );

			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			if ( 3 > strlen( $term ) ) {
				$limit = 20;
			}
			$ids = $data_store->search_customers( $term, $limit );
		}

		$found_customers = array();

		if ( ! empty( $_GET['exclude'] ) ) {
			$ids = array_diff( $ids, (array) $_GET['exclude'] );
		}

		foreach ( $ids as $id ) {
			$customer = new WC_Customer( $id );
			/* translators: 1: user display name 2: user ID 3: user email */
			$found_customers[ $id ] = sprintf(
				/* translators: $1: customer name, $2 customer id, $3: customer email */
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
				$customer->get_first_name() . ' ' . $customer->get_last_name(),
				$customer->get_id(),
				$customer->get_email()
			);
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_customers', $found_customers ) );
	}

	/**
	 * Search for categories and return json.
	 */
	public static function json_search_categories() {
		ob_start();

		check_ajax_referer( 'search-categories', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		if ( ! $search_text = wc_clean( wp_unslash( $_GET['term'] ) ) ) {
			wp_die();
		}

		$found_categories = array();
		$args             = array(
			'taxonomy'   => array( 'product_cat' ),
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => true,
			'fields'     => 'all',
			'name__like' => $search_text,
		);

		if ( $terms = get_terms( $args ) ) {
			foreach ( $terms as $term ) {
				$term->formatted_name = '';

				if ( $term->parent ) {
					$ancestors = array_reverse( get_ancestors( $term->term_id, 'product_cat' ) );
					foreach ( $ancestors as $ancestor ) {
						if ( $ancestor_term = get_term( $ancestor, 'product_cat' ) ) {
							$term->formatted_name .= $ancestor_term->name . ' > ';
						}
					}
				}

				$term->formatted_name              .= $term->name . ' (' . $term->count . ')';
				$found_categories[ $term->term_id ] = $term;
			}
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_categories', $found_categories ) );
	}

	/**
	 * Ajax request handling for categories ordering.
	 */
	public static function term_ordering() {

		// check permissions again and make sure we have what we need
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['id'] ) ) {
			wp_die( -1 );
		}

		$id       = (int) $_POST['id'];
		$next_id  = isset( $_POST['nextid'] ) && (int) $_POST['nextid'] ? (int) $_POST['nextid'] : null;
		$taxonomy = isset( $_POST['thetaxonomy'] ) ? esc_attr( $_POST['thetaxonomy'] ) : null;
		$term     = get_term_by( 'id', $id, $taxonomy );

		if ( ! $id || ! $term || ! $taxonomy ) {
			wp_die( 0 );
		}

		wc_reorder_terms( $term, $next_id, $taxonomy );

		$children = get_terms( $taxonomy, "child_of=$id&menu_order=ASC&hide_empty=0" );

		if ( $term && sizeof( $children ) ) {
			echo 'children';
			wp_die();
		}
	}

	/**
	 * Ajax request handling for product ordering.
	 *
	 * Based on Simple Page Ordering by 10up (https://wordpress.org/plugins/simple-page-ordering/).
	 */
	public static function product_ordering() {
		global $wpdb;

		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['id'] ) ) {
			wp_die( -1 );
		}

		$sorting_id  = absint( $_POST['id'] );
		$previd      = absint( isset( $_POST['previd'] ) ? $_POST['previd'] : 0 );
		$nextid      = absint( isset( $_POST['nextid'] ) ? $_POST['nextid'] : 0 );
		$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'product' ORDER BY menu_order ASC, post_title ASC" ), 'menu_order', 'ID' );
		$index       = 0;

		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			}
			if ( $nextid === $id ) {
				$index ++;
			}
			$index ++;
			$menu_orders[ $id ] = $index;
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) );

			/**
			 * When a single product has gotten it's ordering updated.
			 * $id The product ID
			 * $index The new menu order
			*/
			do_action( 'woocommerce_after_single_product_ordering', $id, $index );
		}

		if ( isset( $menu_orders[ $previd ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] + 1;
		} elseif ( isset( $menu_orders[ $nextid ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] - 1;
		} else {
			$menu_orders[ $sorting_id ] = 0;
		}

		$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) );

		do_action( 'woocommerce_after_product_ordering', $sorting_id, $menu_orders );
		wp_send_json( $menu_orders );
	}

	/**
	 * Handle a refund via the edit order screen.
	 *
	 * @throws Exception To return errors.
	 */
	public static function refund_line_items() {
		ob_start();

		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$order_id               = absint( $_POST['order_id'] );
		$refund_amount          = wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ), wc_get_price_decimals() );
		$refunded_amount        = wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refunded_amount'] ) ), wc_get_price_decimals() );
		$refund_reason          = sanitize_text_field( $_POST['refund_reason'] );
		$line_item_qtys         = json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_qtys'] ) ), true );
		$line_item_totals       = json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_totals'] ) ), true );
		$line_item_tax_totals   = json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_tax_totals'] ) ), true );
		$api_refund             = 'true' === $_POST['api_refund'];
		$restock_refunded_items = 'true' === $_POST['restock_refunded_items'];
		$refund                 = false;
		$response_data          = array();

		try {
			$order       = wc_get_order( $order_id );
			$order_items = $order->get_items();
			$max_refund  = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

			if ( ! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount ) {
				throw new exception( __( 'Invalid refund amount', 'woocommerce' ) );
			}

			if ( $refunded_amount !== wc_format_decimal( $order->get_total_refunded(), wc_get_price_decimals() ) ) {
				throw new exception( __( 'Error processing refund. Please try again.', 'woocommerce' ) );
			}

			// Prepare line items which we are refunding.
			$line_items = array();
			$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys, $line_item_totals ) ) );

			foreach ( $item_ids as $item_id ) {
				$line_items[ $item_id ] = array(
					'qty'          => 0,
					'refund_total' => 0,
					'refund_tax'   => array(),
				);
			}
			foreach ( $line_item_qtys as $item_id => $qty ) {
				$line_items[ $item_id ]['qty'] = max( $qty, 0 );
			}
			foreach ( $line_item_totals as $item_id => $total ) {
				$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			}
			foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
			}

			// Create the refund object.
			$refund = wc_create_refund(
				array(
					'amount'         => $refund_amount,
					'reason'         => $refund_reason,
					'order_id'       => $order_id,
					'line_items'     => $line_items,
					'refund_payment' => $api_refund,
					'restock_items'  => $restock_refunded_items,
				)
			);

			if ( is_wp_error( $refund ) ) {
				throw new Exception( $refund->get_error_message() );
			}

			if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
				$response_data['status'] = 'fully_refunded';
			}

			wp_send_json_success( $response_data );

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Delete a refund.
	 */
	public static function delete_refund() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$refund_ids = array_map( 'absint', is_array( $_POST['refund_id'] ) ? $_POST['refund_id'] : array( $_POST['refund_id'] ) );
		foreach ( $refund_ids as $refund_id ) {
			if ( $refund_id && 'shop_order_refund' === get_post_type( $refund_id ) ) {
				$refund   = wc_get_order( $refund_id );
				$order_id = $refund->get_parent_id();
				$refund->delete( true );
				do_action( 'woocommerce_refund_deleted', $refund_id, $order_id );
			}
		}
		wp_die();
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( -1 );
		}
		update_option( 'woocommerce_admin_footer_text_rated', 1 );
		wp_die();
	}

	/**
	 * Create/Update API key.
	 */
	public static function update_api_key() {
		ob_start();

		global $wpdb;

		check_ajax_referer( 'update-api-key', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( -1 );
		}

		try {
			if ( empty( $_POST['description'] ) ) {
				throw new Exception( __( 'Description is missing.', 'woocommerce' ) );
			}
			if ( empty( $_POST['user'] ) ) {
				throw new Exception( __( 'User is missing.', 'woocommerce' ) );
			}
			if ( empty( $_POST['permissions'] ) ) {
				throw new Exception( __( 'Permissions is missing.', 'woocommerce' ) );
			}

			$key_id      = absint( $_POST['key_id'] );
			$description = sanitize_text_field( wp_unslash( $_POST['description'] ) );
			$permissions = ( in_array( $_POST['permissions'], array( 'read', 'write', 'read_write' ) ) ) ? sanitize_text_field( $_POST['permissions'] ) : 'read';
			$user_id     = absint( $_POST['user'] );

			// Check if current user can edit other users.
			if ( $user_id && ! current_user_can( 'edit_user', $user_id ) ) {
				if ( get_current_user_id() !== $user_id ) {
					throw new Exception( __( 'You do not have permission to assign API Keys to the selected user.', 'woocommerce' ) );
				}
			}

			if ( 0 < $key_id ) {
				$data = array(
					'user_id'     => $user_id,
					'description' => $description,
					'permissions' => $permissions,
				);

				$wpdb->update(
					$wpdb->prefix . 'woocommerce_api_keys',
					$data,
					array( 'key_id' => $key_id ),
					array(
						'%d',
						'%s',
						'%s',
					),
					array( '%d' )
				);

				$data['consumer_key']    = '';
				$data['consumer_secret'] = '';
				$data['message']         = __( 'API Key updated successfully.', 'woocommerce' );
			} else {
				$consumer_key    = 'ck_' . wc_rand_hash();
				$consumer_secret = 'cs_' . wc_rand_hash();

				$data = array(
					'user_id'         => $user_id,
					'description'     => $description,
					'permissions'     => $permissions,
					'consumer_key'    => wc_api_hash( $consumer_key ),
					'consumer_secret' => $consumer_secret,
					'truncated_key'   => substr( $consumer_key, -7 ),
				);

				$wpdb->insert(
					$wpdb->prefix . 'woocommerce_api_keys',
					$data,
					array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
					)
				);

				$key_id                  = $wpdb->insert_id;
				$data['consumer_key']    = $consumer_key;
				$data['consumer_secret'] = $consumer_secret;
				$data['message']         = __( 'API Key generated successfully. Make sure to copy your new keys now as the secret key will be hidden once you leave this page.', 'woocommerce' );
				$data['revoke_url']      = '<a style="color: #a00; text-decoration: none;" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'revoke-key' => $key_id ), admin_url( 'admin.php?page=wc-settings&tab=advanced&section=keys' ) ), 'revoke' ) ) . '">' . __( 'Revoke key', 'woocommerce' ) . '</a>';
			}

			wp_send_json_success( $data );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	/**
	 * Load variations via AJAX.
	 */
	public static function load_variations() {
		ob_start();

		check_ajax_referer( 'load-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		// Set $post global so its available, like within the admin screens
		global $post;

		$loop           = 0;
		$product_id     = absint( $_POST['product_id'] );
		$post           = get_post( $product_id );
		$product_object = wc_get_product( $product_id );
		$per_page       = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$variations     = wc_get_products(
			array(
				'status'  => array( 'private', 'publish' ),
				'type'    => 'variation',
				'parent'  => $product_id,
				'limit'   => $per_page,
				'page'    => $page,
				'orderby' => array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				),
				'return'  => 'objects',
			)
		);

		if ( $variations ) {
			foreach ( $variations as $variation_object ) {
				$variation_id   = $variation_object->get_id();
				$variation      = get_post( $variation_id );
				$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
				include 'admin/meta-boxes/views/html-variation-admin.php';
				$loop++;
			}
		}
		wp_die();
	}

	/**
	 * Save variations via AJAX.
	 */
	public static function save_variations() {
		ob_start();

		check_ajax_referer( 'save-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		$product_id                           = absint( $_POST['product_id'] );
		WC_Admin_Meta_Boxes::$meta_box_errors = array();
		WC_Meta_Box_Product_Data::save_variations( $product_id, get_post( $product_id ) );

		do_action( 'woocommerce_ajax_save_product_variations', $product_id );

		if ( $errors = WC_Admin_Meta_Boxes::$meta_box_errors ) {
			echo '<div class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'woocommerce' ) . '</span></button>';
			echo '</div>';

			delete_option( 'woocommerce_meta_box_errors' );
		}

		wp_die();
	}

	/**
	 * Bulk action - Toggle Enabled.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_enabled( $variations, $data ) {
		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			$variation->set_status( 'private' === $variation->get_status( 'edit' ) ? 'publish' : 'private' );
			$variation->save();
		}
	}

	/**
	 * Bulk action - Toggle Downloadable Checkbox.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_downloadable( $variations, $data ) {
		self::variation_bulk_toggle( $variations, 'downloadable' );
	}

	/**
	 * Bulk action - Toggle Virtual Checkbox.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_virtual( $variations, $data ) {
		self::variation_bulk_toggle( $variations, 'virtual' );
	}

	/**
	 * Bulk action - Toggle Manage Stock Checkbox.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_manage_stock( $variations, $data ) {
		self::variation_bulk_toggle( $variations, 'manage_stock' );
	}

	/**
	 * Bulk action - Set Regular Prices.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_regular_price( $variations, $data ) {
		self::variation_bulk_set( $variations, 'regular_price', $data['value'] );
	}

	/**
	 * Bulk action - Set Sale Prices.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_price( $variations, $data ) {
		self::variation_bulk_set( $variations, 'sale_price', $data['value'] );
	}

	/**
	 * Bulk action - Set Stock Status as In Stock.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock_status_instock( $variations, $data ) {
		self::variation_bulk_set( $variations, 'stock_status', 'instock' );
	}

	/**
	 * Bulk action - Set Stock Status as Out of Stock.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock_status_outofstock( $variations, $data ) {
		self::variation_bulk_set( $variations, 'stock_status', 'outofstock' );
	}

	/**
	 * Bulk action - Set Stock Status as On Backorder.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock_status_onbackorder( $variations, $data ) {
		self::variation_bulk_set( $variations, 'stock_status', 'onbackorder' );
	}

	/**
	 * Bulk action - Set Stock.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock( $variations, $data ) {
		if ( ! isset( $data['value'] ) ) {
			return;
		}

		$quantity = wc_stock_amount( wc_clean( $data['value'] ) );

		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			if ( $variation->managing_stock() ) {
				$variation->set_stock_quantity( $quantity );
			} else {
				$variation->set_stock_quantity( null );
			}
			$variation->save();
		}
	}

	/**
	 * Bulk action - Set Weight.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_weight( $variations, $data ) {
		self::variation_bulk_set( $variations, 'weight', $data['value'] );
	}

	/**
	 * Bulk action - Set Length.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_length( $variations, $data ) {
		self::variation_bulk_set( $variations, 'length', $data['value'] );
	}

	/**
	 * Bulk action - Set Width.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_width( $variations, $data ) {
		self::variation_bulk_set( $variations, 'width', $data['value'] );
	}

	/**
	 * Bulk action - Set Height.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_height( $variations, $data ) {
		self::variation_bulk_set( $variations, 'height', $data['value'] );
	}

	/**
	 * Bulk action - Set Download Limit.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_download_limit( $variations, $data ) {
		self::variation_bulk_set( $variations, 'download_limit', $data['value'] );
	}

	/**
	 * Bulk action - Set Download Expiry.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_download_expiry( $variations, $data ) {
		self::variation_bulk_set( $variations, 'download_expiry', $data['value'] );
	}

	/**
	 * Bulk action - Delete all.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_delete_all( $variations, $data ) {
		if ( isset( $data['allowed'] ) && 'true' === $data['allowed'] ) {
			foreach ( $variations as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				$variation->delete( true );
			}
		}
	}

	/**
	 * Bulk action - Sale Schedule.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_schedule( $variations, $data ) {
		if ( ! isset( $data['date_from'] ) && ! isset( $data['date_to'] ) ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( 'false' !== $data['date_from'] ) {
				$variation->set_date_on_sale_from( wc_clean( $data['date_from'] ) );
			}

			if ( 'false' !== $data['date_to'] ) {
				$variation->set_date_on_sale_to( wc_clean( $data['date_to'] ) );
			}

			$variation->save();
		}
	}

	/**
	 * Bulk action - Increase Regular Prices.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_regular_price_increase( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'regular_price', '+', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Decrease Regular Prices.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_regular_price_decrease( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'regular_price', '-', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Increase Sale Prices.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_price_increase( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'sale_price', '+', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Decrease Sale Prices.
	 *
	 * @param array $variations
	 * @param array $data
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_price_decrease( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'sale_price', '-', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Price.
	 *
	 * @param array  $variations
	 * @param string $operator + or -
	 * @param string $field price being adjusted _regular_price or _sale_price
	 * @param string $value Price or Percent
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_adjust_price( $variations, $field, $operator, $value ) {
		foreach ( $variations as $variation_id ) {
			$variation   = wc_get_product( $variation_id );
			$field_value = $variation->{"get_$field"}( 'edit' );

			if ( '%' === substr( $value, -1 ) ) {
				$percent      = wc_format_decimal( substr( $value, 0, -1 ) );
				$field_value += round( ( $field_value / 100 ) * $percent, wc_get_price_decimals() ) * "{$operator}1";
			} else {
				$field_value += $value * "{$operator}1";
			}

			$variation->{"set_$field"}( $field_value );
			$variation->save();
		}
	}

	/**
	 * Bulk set convenience function.
	 *
	 * @param array  $variations
	 * @param string $field
	 * @param string $value
	 */
	private static function variation_bulk_set( $variations, $field, $value ) {
		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			$variation->{ "set_$field" }( wc_clean( $value ) );
			$variation->save();
		}
	}

	/**
	 * Bulk toggle convenience function.
	 *
	 * @param array  $variations
	 * @param string $field
	 */
	private static function variation_bulk_toggle( $variations, $field ) {
		foreach ( $variations as $variation_id ) {
			$variation  = wc_get_product( $variation_id );
			$prev_value = $variation->{ "get_$field" }( 'edit' );
			$variation->{ "set_$field" }( ! $prev_value );
			$variation->save();
		}
	}

	/**
	 * Bulk edit variations via AJAX.
	 *
	 * @uses WC_AJAX::variation_bulk_set()
	 * @uses WC_AJAX::variation_bulk_adjust_price()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_price_decrease()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_price_increase()
	 * @uses WC_AJAX::variation_bulk_action_variable_regular_price_decrease()
	 * @uses WC_AJAX::variation_bulk_action_variable_regular_price_increase()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_schedule()
	 * @uses WC_AJAX::variation_bulk_action_delete_all()
	 * @uses WC_AJAX::variation_bulk_action_variable_download_expiry()
	 * @uses WC_AJAX::variation_bulk_action_variable_download_limit()
	 * @uses WC_AJAX::variation_bulk_action_variable_height()
	 * @uses WC_AJAX::variation_bulk_action_variable_width()
	 * @uses WC_AJAX::variation_bulk_action_variable_length()
	 * @uses WC_AJAX::variation_bulk_action_variable_weight()
	 * @uses WC_AJAX::variation_bulk_action_variable_stock()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_price()
	 * @uses WC_AJAX::variation_bulk_action_variable_regular_price()
	 * @uses WC_AJAX::variation_bulk_action_toggle_manage_stock()
	 * @uses WC_AJAX::variation_bulk_action_toggle_virtual()
	 * @uses WC_AJAX::variation_bulk_action_toggle_downloadable()
	 * @uses WC_AJAX::variation_bulk_action_toggle_enabled
	 */
	public static function bulk_edit_variations() {
		ob_start();

		check_ajax_referer( 'bulk-edit-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) || empty( $_POST['bulk_action'] ) ) {
			wp_die( -1 );
		}

		$product_id  = absint( $_POST['product_id'] );
		$bulk_action = wc_clean( $_POST['bulk_action'] );
		$data        = ! empty( $_POST['data'] ) ? array_map( 'wc_clean', $_POST['data'] ) : array();
		$variations  = array();

		if ( apply_filters( 'woocommerce_bulk_edit_variations_need_children', true ) ) {
			$variations = get_posts(
				array(
					'post_parent'    => $product_id,
					'posts_per_page' => -1,
					'post_type'      => 'product_variation',
					'fields'         => 'ids',
					'post_status'    => array( 'publish', 'private' ),
				)
			);
		}

		if ( method_exists( __CLASS__, "variation_bulk_action_$bulk_action" ) ) {
			call_user_func( array( __CLASS__, "variation_bulk_action_$bulk_action" ), $variations, $data );
		} else {
			do_action( 'woocommerce_bulk_edit_variations_default', $bulk_action, $data, $product_id, $variations );
		}

		do_action( 'woocommerce_bulk_edit_variations', $bulk_action, $data, $product_id, $variations );
		WC_Product_Variable::sync( $product_id );
		wc_delete_product_transients( $product_id );
		wp_die();
	}

	/**
	 * Handle submissions from assets/js/settings-views-html-settings-tax.js Backbone model.
	 */
	public static function tax_rates_save_changes() {
		if ( ! isset( $_POST['wc_tax_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		$current_class = ! empty( $_POST['current_class'] ) ? $_POST['current_class'] : ''; // This is sanitized seven lines later.

		if ( ! wp_verify_nonce( $_POST['wc_tax_nonce'], 'wc_tax_nonce-class:' . $current_class ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		$current_class = WC_Tax::format_tax_rate_class( $current_class );

		// Check User Caps
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$changes = stripslashes_deep( $_POST['changes'] );
		foreach ( $changes as $tax_rate_id => $data ) {
			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				WC_Tax::_delete_tax_rate( $tax_rate_id );
			}

			$tax_rate = array_intersect_key(
				$data, array(
					'tax_rate_country'  => 1,
					'tax_rate_state'    => 1,
					'tax_rate'          => 1,
					'tax_rate_name'     => 1,
					'tax_rate_priority' => 1,
					'tax_rate_compound' => 1,
					'tax_rate_shipping' => 1,
					'tax_rate_order'    => 1,
				)
			);

			if ( isset( $tax_rate['tax_rate'] ) ) {
				$tax_rate['tax_rate'] = wc_format_decimal( $tax_rate['tax_rate'] );
			}

			if ( isset( $data['newRow'] ) ) {
				$tax_rate['tax_rate_class'] = $current_class;
				$tax_rate_id                = WC_Tax::_insert_tax_rate( $tax_rate );
			} elseif ( ! empty( $tax_rate ) ) {
				WC_Tax::_update_tax_rate( $tax_rate_id, $tax_rate );
			}

			if ( isset( $data['postcode'] ) ) {
				$postcode = array_map( 'wc_clean', $data['postcode'] );
				$postcode = array_map( 'wc_normalize_postcode', $postcode );
				WC_Tax::_update_tax_rate_postcodes( $tax_rate_id, $postcode );
			}
			if ( isset( $data['city'] ) ) {
				WC_Tax::_update_tax_rate_cities( $tax_rate_id, array_map( 'wc_clean', array_map( 'wp_unslash', $data['city'] ) ) );
			}
		}

		WC_Cache_Helper::incr_cache_prefix( 'taxes' );
		WC_Cache_Helper::get_transient_version( 'shipping', true );

		wp_send_json_success(
			array(
				'rates' => WC_Tax::get_rates_for_tax_class( $current_class ),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-zones.js Backbone model.
	 */
	public static function shipping_zones_save_changes() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( $_POST['wc_shipping_zones_nonce'], 'wc_shipping_zones_nonce' ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		// Check User Caps
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$changes = $_POST['changes'];
		foreach ( $changes as $zone_id => $data ) {
			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				WC_Shipping_Zones::delete_zone( $zone_id );
				continue;
			}

			$zone_data = array_intersect_key(
				$data, array(
					'zone_id'    => 1,
					'zone_order' => 1,
				)
			);

			if ( isset( $zone_data['zone_id'] ) ) {
				$zone = new WC_Shipping_Zone( $zone_data['zone_id'] );

				if ( isset( $zone_data['zone_order'] ) ) {
					$zone->set_zone_order( $zone_data['zone_order'] );
				}

				$zone->save();
			}
		}

		wp_send_json_success(
			array(
				'zones' => WC_Shipping_Zones::get_zones( 'json' ),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-zone-methods.js Backbone model.
	 */
	public static function shipping_zone_add_method() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['zone_id'], $_POST['method_id'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( $_POST['wc_shipping_zones_nonce'], 'wc_shipping_zones_nonce' ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		// Check User Caps
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$zone_id     = wc_clean( $_POST['zone_id'] );
		$zone        = new WC_Shipping_Zone( $zone_id );
		$instance_id = $zone->add_shipping_method( wc_clean( $_POST['method_id'] ) );

		wp_send_json_success(
			array(
				'instance_id' => $instance_id,
				'zone_id'     => $zone->get_id(),
				'zone_name'   => $zone->get_zone_name(),
				'methods'     => $zone->get_shipping_methods( false, 'json' ),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-zone-methods.js Backbone model.
	 */
	public static function shipping_zone_methods_save_changes() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['zone_id'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( $_POST['wc_shipping_zones_nonce'], 'wc_shipping_zones_nonce' ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		global $wpdb;

		$zone_id = wc_clean( $_POST['zone_id'] );
		$zone    = new WC_Shipping_Zone( $zone_id );
		$changes = $_POST['changes'];

		if ( isset( $changes['zone_name'] ) ) {
			$zone->set_zone_name( wc_clean( $changes['zone_name'] ) );
		}

		if ( isset( $changes['zone_locations'] ) ) {
			$zone->clear_locations( array( 'state', 'country', 'continent' ) );
			$locations = array_filter( array_map( 'wc_clean', (array) $changes['zone_locations'] ) );
			foreach ( $locations as $location ) {
				// Each posted location will be in the format type:code
				$location_parts = explode( ':', $location );
				switch ( $location_parts[0] ) {
					case 'state':
						$zone->add_location( $location_parts[1] . ':' . $location_parts[2], 'state' );
						break;
					case 'country':
						$zone->add_location( $location_parts[1], 'country' );
						break;
					case 'continent':
						$zone->add_location( $location_parts[1], 'continent' );
						break;
				}
			}
		}

		if ( isset( $changes['zone_postcodes'] ) ) {
			$zone->clear_locations( 'postcode' );
			$postcodes = array_filter( array_map( 'strtoupper', array_map( 'wc_clean', explode( "\n", $changes['zone_postcodes'] ) ) ) );
			foreach ( $postcodes as $postcode ) {
				$zone->add_location( $postcode, 'postcode' );
			}
		}

		if ( isset( $changes['methods'] ) ) {
			foreach ( $changes['methods'] as $instance_id => $data ) {
				$method_id = $wpdb->get_var( $wpdb->prepare( "SELECT method_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE instance_id = %d", $instance_id ) );

				if ( isset( $data['deleted'] ) ) {
					$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );
					$option_key      = $shipping_method->get_instance_option_key();
					if ( $wpdb->delete( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'instance_id' => $instance_id ) ) ) {
						delete_option( $option_key );
						do_action( 'woocommerce_shipping_zone_method_deleted', $instance_id, $method_id, $zone_id );
					}
					continue;
				}

				$method_data = array_intersect_key(
					$data, array(
						'method_order' => 1,
						'enabled'      => 1,
					)
				);

				if ( isset( $method_data['method_order'] ) ) {
					$wpdb->update( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'method_order' => absint( $method_data['method_order'] ) ), array( 'instance_id' => absint( $instance_id ) ) );
				}

				if ( isset( $method_data['enabled'] ) ) {
					$is_enabled = absint( 'yes' === $method_data['enabled'] );
					if ( $wpdb->update( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'is_enabled' => $is_enabled ), array( 'instance_id' => absint( $instance_id ) ) ) ) {
						do_action( 'woocommerce_shipping_zone_method_status_toggled', $instance_id, $method_id, $zone_id, $is_enabled );
					}
				}
			}
		}

		$zone->save();

		wp_send_json_success(
			array(
				'zone_id'   => $zone->get_id(),
				'zone_name' => $zone->get_zone_name(),
				'methods'   => $zone->get_shipping_methods( false, 'json' ),
			)
		);
	}

	/**
	 * Save method settings
	 */
	public static function shipping_zone_methods_save_settings() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['instance_id'], $_POST['data'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( $_POST['wc_shipping_zones_nonce'], 'wc_shipping_zones_nonce' ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$instance_id     = absint( $_POST['instance_id'] );
		$zone            = WC_Shipping_Zones::get_zone_by( 'instance_id', $instance_id );
		$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );
		$shipping_method->set_post_data( $_POST['data'] );
		$shipping_method->process_admin_options();

		WC_Cache_Helper::get_transient_version( 'shipping', true );

		wp_send_json_success(
			array(
				'zone_id'   => $zone->get_id(),
				'zone_name' => $zone->get_zone_name(),
				'methods'   => $zone->get_shipping_methods( false, 'json' ),
				'errors'    => $shipping_method->get_errors(),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-classes.js Backbone model.
	 */
	public static function shipping_classes_save_changes() {
		if ( ! isset( $_POST['wc_shipping_classes_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( $_POST['wc_shipping_classes_nonce'], 'wc_shipping_classes_nonce' ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$changes = $_POST['changes'];

		foreach ( $changes as $term_id => $data ) {
			$term_id = absint( $term_id );

			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				wp_delete_term( $term_id, 'product_shipping_class' );
				continue;
			}

			$update_args = array();

			if ( isset( $data['name'] ) ) {
				$update_args['name'] = wc_clean( $data['name'] );
			}

			if ( isset( $data['slug'] ) ) {
				$update_args['slug'] = wc_clean( $data['slug'] );
			}

			if ( isset( $data['description'] ) ) {
				$update_args['description'] = wc_clean( $data['description'] );
			}

			if ( isset( $data['newRow'] ) ) {
				$update_args = array_filter( $update_args );
				if ( empty( $update_args['name'] ) ) {
					continue;
				}
				$inserted_term = wp_insert_term( $update_args['name'], 'product_shipping_class', $update_args );
				$term_id       = is_wp_error( $inserted_term ) ? 0 : $inserted_term['term_id'];
			} else {
				wp_update_term( $term_id, 'product_shipping_class', $update_args );
			}

			do_action( 'woocommerce_shipping_classes_save_class', $term_id, $data );
		}

		$wc_shipping = WC_Shipping::instance();

		wp_send_json_success(
			array(
				'shipping_classes' => $wc_shipping->get_shipping_classes(),
			)
		);
	}

	/**
	 * Toggle payment gateway on or off via AJAX.
	 *
	 * @since 3.4.0
	 */
	public static function toggle_gateway_enabled() {
		if ( current_user_can( 'manage_woocommerce' ) && check_ajax_referer( 'woocommerce-toggle-payment-gateway-enabled', 'security' ) ) {
			// Load gateways.
			$payment_gateways = WC()->payment_gateways->payment_gateways();

			// Get posted gateway.
			$gateway_id = wc_clean( wp_unslash( $_POST['gateway_id'] ) );

			foreach ( $payment_gateways as $gateway ) {
				if ( ! in_array( $gateway_id, array( $gateway->id, sanitize_title( get_class( $gateway ) ) ), true ) ) {
					continue;
				}
				$enabled = $gateway->get_option( 'enabled', 'no' );

				if ( ! wc_string_to_bool( $enabled ) ) {
					if ( $gateway->needs_setup() ) {
						wp_send_json_error( 'needs_setup' );
						wp_die();
					} else {
						$gateway->update_option( 'enabled', 'yes' );
					}
				} else {
					// Disable the gateway.
					$gateway->update_option( 'enabled', 'no' );
				}

				wp_send_json_success( ! wc_string_to_bool( $enabled ) );
				wp_die();
			}
		}

		wp_send_json_error( 'invalid_gateway_id' );
		wp_die();
	}
}

WC_AJAX::init();
