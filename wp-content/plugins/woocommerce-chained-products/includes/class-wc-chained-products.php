<?php
/**
 * Main class to handle mainly frontend related chained products actions
 *
 * @since       2.5.0
 *
 * @package     woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Chained_Products' ) ) {

	/**
	 * WC Chained Products Frontend
	 *
	 * @author StoreApps
	 */
	class WC_Chained_Products {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->cp_include_files();

			add_action( 'init', array( $this, 'load_chained_products' ) );

			// Filter for validating cart based on availability of chained products.
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woocommerce_chained_add_to_cart_validation' ), 10, 3 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'woocommerce_chained_update_cart_validation' ), 10, 4 );

			// Action to add or remove actions & filter specific to chained products.
			add_action( 'add_chained_products_actions_filters', array( $this, 'add_chained_products_actions_filters' ) );
			add_action( 'remove_chained_products_actions_filters', array( $this, 'remove_chained_products_actions_filters' ) );

			// Action for checking cart items including Chained products.
			add_action( 'woocommerce_check_cart_items', array( $this, 'woocommerce_chained_check_cart_items' ) );

			// Filter to hide "Add to cart" button if chained products are out of stock.
			add_filter( 'woocommerce_get_availability', array( $this, 'woocommerce_get_chained_products_availability' ), 10, 2 );

			// Action to add chained product to cart.
			add_action( 'woocommerce_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 10, 6 );
			add_action( 'woocommerce_mnm_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 10, 7 );
			add_action( 'woocommerce_bundled_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 99, 7 );
			add_action( 'woocommerce_composited_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 10, 7 );

			// Action for updating chained product quantity in cart.
			if ( Chained_Products_WC_Compatibility::is_wc_gte_32() ) {
				add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'sa_after_cart_item_quantity_update' ), 1, 4 );
				add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'sa_before_cart_item_quantity_zero' ), 1, 2 );
			} else {
				add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'sa_after_cart_item_quantity_update' ), 1, 3 );
				add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'sa_before_cart_item_quantity_zero' ), 1, 1 );
			}

			add_action( 'woocommerce_cart_updated', array( $this, 'validate_and_update_chained_product_quantity_in_cart' ) );

			// Don't allow chained products to be removed or change quantity.
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'chained_cart_item_remove_link' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'chained_cart_item_quantity' ), 10, 2 );

			// Filter for getting cart item from session.
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_chained_cart_item_from_session' ), 10, 2 );

			// remove/restore chained cart items when parent is removed/restored.
			add_action( 'woocommerce_cart_item_removed', array( $this, 'chained_cart_item_removed' ), 10, 2 );
			add_action( 'woocommerce_cart_item_restored', array( $this, 'chained_cart_item_restored' ), 10, 2 );

			// Filters for manage stock availability and max value of input args.
			add_filter( 'woocommerce_get_availability', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );
			add_filter( 'woocommerce_quantity_input_max', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_data_max', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );
			add_filter( 'woocommerce_quantity_input_args', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );

			// Action for removing price of chained products before calculating totals.
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_before_chained_calculate_totals' ) );

			// Chained product list on shop page.
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woocommerce_chained_products_for_variable_product' ) );
			add_action( 'wp_ajax_nopriv_get_chained_products_html_view', array( $this, 'get_chained_products_html_view' ) );
			add_action( 'wp_ajax_get_chained_products_html_view', array( $this, 'get_chained_products_html_view' ) );

			// Register Chained Products Shortcode.
			add_action( 'init', array( $this, 'register_chained_products_shortcodes' ) );

			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'sa_cart_chained_item_subtotal' ), 11, 3 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'sa_cart_chained_item_subtotal' ), 11, 3 );

			add_filter( 'woocommerce_cart_item_class', array( $this, 'sa_cart_chained_item_class' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'sa_cart_chained_item_name' ), 10, 3 );
			add_filter( 'woocommerce_admin_html_order_item_class', array( $this, 'sa_admin_html_chained_item_class' ), 10, 2 );

			add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'sa_order_chained_item_subtotal' ), 10, 3 );

			add_filter( 'woocommerce_order_item_class', array( $this, 'sa_order_chained_item_class' ), 10, 3 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'sa_order_chained_item_name' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_visible', array( $this, 'sa_chained_item_visible' ), 10, 3 );
			add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'sa_chained_item_visible' ), 10, 3 );
			add_filter( 'woocommerce_checkout_cart_item_visible', array( $this, 'sa_chained_item_visible' ), 10, 3 );
			add_filter( 'woocommerce_order_item_visible', array( $this, 'sa_order_chained_item_visible' ), 10, 2 );

			add_action( 'admin_footer', array( $this, 'chained_products_admin_css' ) );
			add_action( 'wp_footer', array( $this, 'chained_products_frontend_css' ) );

			add_action( 'get_header', array( $this, 'sa_chained_theme_header' ) );

			$do_housekeeping = get_option( 'sa_chained_products_housekeeping', 'yes' );

			if ( 'yes' === $do_housekeeping ) {
				add_action( 'trashed_post', array( $this, 'sa_chained_on_trash_post' ) );
				add_action( 'untrashed_post', array( $this, 'sa_chained_on_untrash_post' ) );
			}

			add_filter( 'woocommerce_order_get_items', array( $this, 'sa_cp_ignore_chained_child_items_on_manual_pay' ), 99, 2 );

			add_filter( 'woocommerce_coupon_get_items_to_validate', array( $this, 'sa_exclude_chained_items_from_being_validated' ), 15, 2 );

			add_filter( 'woocommerce_cart_item_price', array( $this, 'sa_cp_set_cart_item_price' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'sa_cp_set_cart_item_subtotal' ), 10, 3 );

			add_filter( 'woocommerce_show_variation_price', array( $this, 'sa_cp_show_variation_price' ), 10, 3 );

			add_filter( 'woocommerce_get_price_html', array( $this, 'sa_cp_set_price_html' ), 7, 2 );

		}

		/**
		 * Function to set html price for a Chained Parent Product. When price individually option is checked for a chained item the bundle price
		 * needs to be recalculated.
		 *
		 * @param string     $price Product price html.
		 * @param WC_Product $product Product object.
		 * @return srtring $price
		 */
		public function sa_cp_set_price_html( $price, $product ) {
			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() && ( ! $product instanceof WC_Product_Subscription || ! $product instanceof WC_Product_Variable_Subscription ) ) {
				global $wc_chained_products;

				$product_ids    = array();
				$regular_prices = array();
				$prices         = array();

				if ( $product instanceof WC_Product_Variable ) {
					$product_ids = $product->get_children();
				} else {
					$product_ids[] = $product->get_id();
				}

				$override_price = false;

				foreach ( $product_ids as $product_id ) {
					$chained_items  = $wc_chained_products->get_all_chained_product_details( $product_id );
					$chained_parent = wc_get_product( $product_id );

					$regular_prices[ $product_id ] = $chained_parent->get_price();
					$prices[ $product_id ]         = $chained_parent->get_regular_price();

					if ( ! empty( $chained_items ) ) {

						foreach ( $chained_items as $chained_item_id => $chained_item_data ) {
							$chained_product     = wc_get_product( $chained_item_id );
							$priced_individually = ( ! empty( $chained_item_data['priced_individually'] ) ) ? $chained_item_data['priced_individually'] : 'no';

							if ( $chained_product instanceof WC_Product && 'yes' === $priced_individually ) {
								$regular_prices[ $product_id ] += ( $chained_product->get_regular_price() * $chained_item_data['unit'] );
								$prices[ $product_id ]         += ( $chained_product->get_price() * $chained_item_data['unit'] );

								$override_price = true;
							}
						}
					}
				}

				if ( true === $override_price ) {
					if ( $product instanceof WC_Product_Variable ) {
						$min_price = min( $prices );
						$max_price = max( $prices );

						$product->cp_show_variation_price = true;

						return wc_format_price_range( $min_price, $max_price );
					}

					if ( $product instanceof WC_Product_Variation ) {
						$product_regular_price = $regular_prices[ $product->get_id() ];
						$product_price         = $prices[ $product->get_id() ];

						return ( $product_regular_price > $product_price ) ? wc_format_sale_price( $product_regular_price, $product_price ) : wc_price( $product_price );
					}

					$total_regular_price = array_sum( $regular_prices );
					$total_price         = array_sum( $prices );

					return ( $total_regular_price > $total_price ) ? wc_format_sale_price( $total_regular_price, $total_price ) : wc_price( $total_price );
				}
			}

			return $price;
		}

		/**
		 * Function to show variation price. WooCommerce by default doesn't show the price of a variation if it's set to zero. We need to override this in case any of the variation
		 * has chained items linked to it with priced indivudally option enabled.
		 *
		 * @param bool                 $show_price Show price or not.
		 * @param WC_Product           $product Product object.
		 * @param WC_Product_Variation $variation Variation object.
		 * @return bool $price
		 */
		public function sa_cp_show_variation_price( $show_price, $product, $variation ) {
			if ( $product instanceof WC_Product_Variable && isset( $product->cp_show_variation_price ) && true === $product->cp_show_variation_price ) {
				$show_price = true;
			}

			return $show_price;
		}

		/**
		 * Function to re-calculate Price/Subtotal for Chained Parent Product in cart in case the chained item is priced individually.
		 *
		 * @param string $type Calculation type 'price'|'subtotal'.
		 * @param string $amount_html Cart item amount html.
		 * @param array  $cart_item Cart item data.
		 * @param array  $cart_item_key Cart item key.
		 * @return string $amount_html
		 */
		public function get_cp_cart_item_amount( $type, $amount_html, $cart_item, $cart_item_key ) {
			global $wc_chained_products;

			if ( ! empty( $cart_item ) ) {

				$product    = $cart_item['data'];
				$product_id = $product->get_id();
				$unit       = ( 'subtotal' === $type ) ? $cart_item['quantity'] : 1;

				$chained_items = $wc_chained_products->get_all_chained_product_details( $product_id );

				if ( is_array( $chained_items ) && 0 < count( $chained_items ) ) {

					$value = 0;

					foreach ( $chained_items as $chained_item_id => $chained_item_data ) {
						if ( isset( $chained_item_data['priced_individually'] ) && 'yes' === $chained_item_data['priced_individually'] ) {
							$chained_product = wc_get_product( $chained_item_id );

							if ( $chained_product instanceof WC_Product ) {
								$value += $chained_product->get_price() * $chained_item_data['unit'];
							}
						}
					}

					$amount_html = wc_price( ( $product->get_price() + $value ) * $unit );
				}
			}

			return $amount_html;

		}

		/**
		 * Function to set cart item subtotal.
		 *
		 * @param string $cart_item_subtotal Cart item subtotal html.
		 * @param array  $cart_item Cart item data.
		 * @param array  $cart_item_key Cart item key.
		 * @return string $cart_item_subtotal
		 */
		public function sa_cp_set_cart_item_subtotal( $cart_item_subtotal, $cart_item, $cart_item_key ) {
			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
				$cart_item_subtotal = $this->get_cp_cart_item_amount( 'subtotal', $cart_item_subtotal, $cart_item, $cart_item_key );
			}

			return $cart_item_subtotal;
		}

		/**
		 * Function to set cart item price.
		 *
		 * @param string $cart_item_price Cart item price html.
		 * @param array  $cart_item Cart item data.
		 * @param array  $cart_item_key Cart item key.
		 * @return string $cart_item_price
		 */
		public function sa_cp_set_cart_item_price( $cart_item_price, $cart_item, $cart_item_key ) {
			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
				$cart_item_price = $this->get_cp_cart_item_amount( 'price', $cart_item_price, $cart_item, $cart_item_key );
			}

			return $cart_item_price;
		}

		/**
		 * Function to exclude chained items from being validated while applying coupon
		 *
		 * @param array        $items Items to validate.
		 * @param WC_Discounts $discounts Discounts object.
		 * @return array $items
		 */
		public function sa_exclude_chained_items_from_being_validated( $items, $discounts ) {
			foreach ( $items as $cart_item_key => $item ) {
				$cart_item = $item->object;

				if ( isset( $cart_item['chained_item_of'] ) && ! empty( $cart_item['chained_item_of'] ) ) {
					unset( $items[ $cart_item_key ] );
				}
			}

			return $items;
		}

		/**
		 * Function to load Chained Products
		 */
		public function load_chained_products() {

			$current_db_version = get_option( '_current_chained_product_db_version' );

			if ( version_compare( $current_db_version, '1.3', '<' ) || empty( $current_db_version ) ) {
				$this->cp_do_db_update();
			}
		}

		/**
		 * Function to include requires files
		 */
		public function cp_include_files() {
			include_once 'class-chained-products-wc-compatibility.php';
			include_once 'class-cp-admin-welcome.php';
			include_once 'class-wc-cp-admin-notices.php';

			require 'class-wc-admin-chained-products.php';
		}

		/**
		 * Function for database updation on activation of plugin
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global int $blog_id
		 */
		public function cp_do_db_update() {
			global $wpdb, $blog_id;

			// For multisite table prefix.
			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}", 0 ); // WPCS: cache ok, db call ok, unprepared SQL ok.
			} else {
				$blog_ids = array( $blog_id );
			}

			foreach ( $blog_ids as $id ) {

				if ( is_multisite() ) {
					switch_to_blog( $id );
				}

				if ( false === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_for_1_3();
				}

				if ( '1.3' === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_for_1_3_8();
				}

				if ( '1.3.8' === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_for_1_4();
				}

				if ( '1.4' === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_after_1_3_8();
				}

				if ( is_multisite() ) {
					restore_current_blog();
				}
			}

			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) { // WPCS: CSRF ok.
				set_transient( '_chained_products_activation_redirect', 1, 30 );
			}
		}

		/**
		 * Database updation after version 1.3 for quantity bundle feature
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_for_1_3() {

			global $wpdb, $wc_chained_products;

			$old_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_ids' ), 'ARRAY_A' ); // WPCS: cache ok, db call ok.

			if ( ! empty( $old_results ) ) {

				foreach ( $old_results as $result ) {

					$chained_product_detail = array();

					foreach ( maybe_unserialize( $result['meta_value'] ) as $id ) {

						$product_title = $wc_chained_products->get_product_title( $id );

						if ( empty( $product_title ) ) {
							continue;
						}

						$chained_product_detail[ $id ] = array(
							'unit'         => 1,
							'product_name' => $product_title,
						);

					}

					if ( empty( $chained_product_detail ) ) {
						continue;
					}

					// For variable product - update all variation according to parent product.
					$variable_product = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = %d", $result['post_id'] ), 'ARRAY_A' ); // db call ok; no-cache ok.

					if ( empty( $variable_product ) ) {
						update_post_meta( $result['post_id'], '_chained_product_detail', $chained_product_detail );
					} else {
						foreach ( $variable_product as $value ) {
							update_post_meta( $value['ID'], '_chained_product_detail', $chained_product_detail );
						}
					}
				}
			}

			update_option( '_current_chained_product_db_version', '1.3' );

		}

		/**
		 * Database updation to include shortcode in post_content when activated
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_for_1_3_8() {

			global $wpdb, $wc_chained_products;

			$results  = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ), 'ARRAY_A' ); // WPCS: cache ok, db call ok.
			$post_ids = array_map( 'current', $results );

			if ( ! empty( $post_ids ) ) {

				foreach ( $post_ids as $post_id ) {

					$cp_ids[] = $wc_chained_products->get_parent( $post_id );
				}

				$post_ids = implode( ',', array_unique( $cp_ids ) );

				$shortcode  = '<h3>' . __( 'Included Products', 'woocommerce-chained-products' ) . '</h3><br />';
				$shortcode .= __( 'When you order this product, you get all the following products for free!!', 'woocommerce-chained-products' );
				$shortcode .= '[chained_products]';

				$wpdb->query(
					"UPDATE {$wpdb->prefix}posts
							SET post_content = concat( post_content , '$shortcode')
							WHERE ID IN( $post_ids )"
				); // WPCS: cache ok, db call ok, unprepared SQL ok.
			}

			update_option( '_current_chained_product_db_version', '1.3.8' );
		}

		/**
		 * Database updation to restore shortcode after version 1.3.8
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_after_1_3_8() {

			global $wpdb, $wc_chained_products;

			$cp_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ), 'ARRAY_A' ); // WPCS: cache ok, db call ok.

			if ( ! empty( $cp_results ) ) {

				foreach ( $cp_results as $value ) {

					$cp_ids[] = $wc_chained_products->get_parent( $value['post_id'] );
				}

				if ( ! ( is_array( $cp_ids ) && count( $cp_ids ) > 0 ) ) {
					return;
				}

				$cp_results = array_unique( $cp_ids );
				$sc_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_shortcode' ), 'ARRAY_A' ); // WPCS: cache ok, db call ok.
				$post_ids   = array_intersect( $cp_results, array_map( 'current', $sc_results ) );

				if ( ! empty( $post_ids ) ) {

					foreach ( $post_ids as $post_id ) {

						foreach ( $sc_results as $result ) {

							if ( $result['post_id'] === $post_id ) {

								$shortcode[ $post_id ] = $result['meta_value'];
								break;

							}
						}
					}

					$query_case = array();

					foreach ( $shortcode as $id => $meta_value ) {

						$query_case[] = 'WHEN ' . $id . " THEN CONCAT( post_content, '" . $wpdb->_real_escape( $meta_value ) . "')";

					}

					$shortcode_query = " UPDATE {$wpdb->prefix}posts
									SET post_content = CASE ID " . implode( "\n", $query_case ) . '
									END
									WHERE ID IN ( ' . implode( ',', $post_ids ) . ' )
									';

					$wpdb->query( $shortcode_query ); // WPCS: cache ok, db call ok, unprepared SQL ok.

				}

				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_shortcode' ) ); // WPCS: cache ok, db call ok.
			}

			update_option( '_current_chained_product_db_version', '1.5' );
		}

		/**
		 * Add chained product's parent's information in order containing chained products
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_for_1_4() {

			global $wpdb, $wc_chained_products;

			$cp_results  = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ), 'ARRAY_A' ); // WPCS: cache ok, db call ok.
			$product_ids = array_map( 'current', $cp_results );
			$inserted    = array();

			$order_items = $wpdb->get_results(
				"SELECT order_id, meta_value, order_items.order_item_id
												FROM {$wpdb->prefix}woocommerce_order_items AS order_items
												JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
												WHERE order_items.order_item_id = order_itemmeta.order_item_id
												AND meta_key IN ('_product_id', '_variation_id' )
												AND meta_value", 'ARRAY_A'
			); // WPCS: cache ok, db call ok, unprepared SQL ok.

			if ( ! empty( $order_items ) ) {

				foreach ( $order_items as $value ) {
					$order_unique_products[ $value['order_id'] ][ $value['order_item_id'] ] = $value['meta_value'];
				}

				foreach ( $product_ids as $chained_parent_id ) {

					$chained_product_detail = $wc_chained_products->get_all_chained_product_details( $chained_parent_id );
					$chained_product_ids    = is_array( $chained_product_detail ) ? array_keys( $chained_product_detail ) : array();

					if ( empty( $chained_product_ids ) ) {
						continue;
					}

					$orders_contains_parent_product = array();
					foreach ( $order_unique_products as $order_id => $value ) {

						if ( array_search( $chained_parent_id, $value, true ) !== false ) {
							$orders_contains_parent_product[] = $order_id;
						}
					}

					if ( empty( $orders_contains_parent_product ) ) {
						continue;
					}

					foreach ( $orders_contains_parent_product as $order_id ) {

						foreach ( $chained_product_ids as $chained_product_id ) {

							$order_item_id = array_search( $chained_product_id, $order_unique_products[ $order_id ], true );

							if ( empty( $order_item_id ) || array_search( $order_item_id, $inserted, true ) !== false ) {
								continue;
							}

							$inserted[] = $order_item_id;

							$cp_meta_value = $wpdb->get_var(
								$wpdb->prepare(
									"SELECT meta_id
								FROM {$wpdb->prefix}woocommerce_order_itemmeta
								WHERE meta_key = '_chained_product_of'
								AND order_item_id = %d", $order_item_id
								)
							); // WPCS: cache ok, db call ok.

							if ( ! empty( $cp_meta_value ) ) {
								continue;
							}

							$wpdb->query(
								$wpdb->prepare(
									"INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta
										VALUES ( NULL ,  %d,  '_chained_product_of',  %d)
										", $order_item_id, $chained_parent_id
								)
							); // WPCS: cache ok, db call ok.

						}
					}
				}
			}

			update_option( '_current_chained_product_db_version', '1.4' );

		}

		/**
		 * Function to modify cart count in themes header
		 *
		 * @param string|null $name Name of the specific header file to use. null for the default header.
		 */
		public function sa_chained_theme_header( $name ) {
			global $wc_chained_products;

			$chained_item_visible = $wc_chained_products->is_show_chained_items();

			if ( ! $chained_item_visible ) {
				add_filter( 'woocommerce_cart_contents_count', array( $this, 'sa_cp_get_cart_count' ) );
			}
		}

		/**
		 * Function to modify cart count in cart widget
		 *
		 * @param int $quantity Number of items in the cart.
		 * @return int $quantity Numberof items in the cart excluding chained items.
		 */
		public static function sa_cp_get_cart_count( $quantity ) {

			$cart_contents = WC()->cart->cart_contents;

			if ( ! empty( $cart_contents ) && is_array( $cart_contents ) ) {

				foreach ( $cart_contents as $cart_item_key => $data ) {

					if ( ! empty( $data ) && is_array( $data ) && array_key_exists( 'chained_item_of', $data ) ) {
						$quantity = $quantity - $cart_contents[ $cart_item_key ]['quantity'];
					}
				}
			}

			return $quantity;
		}

		/**
		 * Function to save chained-parent relationship in product when that product is trashed
		 *
		 * @param int $trashed_post_id Post ID being trashed.
		 */
		public function sa_chained_on_trash_post( $trashed_post_id ) {
			global $wpdb;

			$published_chained_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT pm.post_id AS post_id,
								 pm.meta_value AS meta_value
							FROM {$wpdb->prefix}postmeta AS pm
								INNER JOIN {$wpdb->prefix}posts AS p
									ON ( pm.post_id = p.ID )
							WHERE p.post_status = 'publish'
								AND ( p.post_type = 'product' OR p.post_type = 'product_variation' )
								AND pm.meta_key = '_chained_product_detail'
								AND pm.meta_value NOT LIKE %s", 'a:0%'
				), ARRAY_A
			); // WPCS: cache ok, db call ok.

			if ( ! empty( $published_chained_data ) ) {

				foreach ( $published_chained_data as $index => $data ) {
					$product_detail[ $data['post_id'] ] = maybe_unserialize( $data['meta_value'] );
				}

				$product_detail       = array_filter( $product_detail );
				$parent_id_to_restore = array();
				$update               = false;

				foreach ( $product_detail as $post_id => $chained_data ) {

					foreach ( $chained_data as $chained_id => $data ) {

						if ( $chained_id === $trashed_post_id ) {
							$parent_id_to_restore[ $post_id ][ $chained_id ] = $data;
							unset( $product_detail[ $post_id ][ $chained_id ] );
							$update = true;
						}
					}
				}

				if ( $update ) {
					update_post_meta( $trashed_post_id, '_parent_id_restore', $parent_id_to_restore );

					foreach ( $product_detail as $post_id => $values ) {
						update_post_meta( $post_id, '_chained_product_detail', $values );
					}
				}
			}
		}

		/**
		 * Function to restore chained-parent relationship after restoring trashed product
		 *
		 * @param int $untrashed_post_id POST ID being restored.
		 */
		public function sa_chained_on_untrash_post( $untrashed_post_id ) {

			$data_to_restore = get_post_meta( $untrashed_post_id, '_parent_id_restore', true );

			if ( ! empty( $data_to_restore ) ) {

				foreach ( $data_to_restore as $parent_id => $chained_array_data ) {

					foreach ( $chained_array_data as $chained_id => $chained_data ) {
						$present_chained_data                = get_post_meta( $parent_id, '_chained_product_detail', true );
						$present_chained_data[ $chained_id ] = $chained_data;
						update_post_meta( $parent_id, '_chained_product_detail', $present_chained_data );
					}
				}

				delete_post_meta( $untrashed_post_id, '_parent_id_restore' );
			}
		}

		/**
		 * To ignore chained child items when Pay button is clicked
		 * This will prevent adding chained child item twice
		 *
		 * @param  array    $items Cart items.
		 * @param  WC_Order $order Order object.
		 * @return array $items Updated cart items.
		 */
		public function sa_cp_ignore_chained_child_items_on_manual_pay( $items, $order ) {
			if ( isset( $_GET['pay_for_order'] ) && isset( $_GET['key'] ) && ! empty( $items ) ) { // WPCS: input var ok, CSRF ok.
				foreach ( $items as $item_id => $item ) {
					if ( ! empty( $item['chained_product_of'] ) ) {
						unset( $items[ $item_id ] );
					}
				}
			}

			return $items;
		}

		/**
		 * Function for display chained products list for variable products
		 *
		 * @global object $woocommerce WooCommerce's main instance.
		 * @global WC_Product $product WooCommerce product's instance.
		 */
		public function woocommerce_chained_products_for_variable_product() {

			global $woocommerce, $product;

			$children                  = ( Chained_Products_WC_Compatibility::is_wc_gte_30() && $product instanceof WC_Product_Variable ) ? $product->get_visible_children() : $product->get_children( true );
			$is_chained_product_parent = false;
			if ( ! empty( $children ) ) {

				foreach ( $children as $chained_parent_id ) {

					$product_detail = get_post_meta( $chained_parent_id, '_chained_product_detail', true );

					if ( ! empty( $product_detail ) ) {
						$is_chained_product_parent = true;
						break;
					}
				}
			}

			if ( ! ( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) ) || ( $product->is_type( 'variable' ) && ! $is_chained_product_parent ) ) {
				return;
			}

			$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;

			$chained_parent_id = ( ! empty( $chained_parent_id ) ) ? $chained_parent_id : $product_id;

			$chained_item_css_class = apply_filters( 'chained_item_css_class', 'chained_items_container', $chained_parent_id );
			$chained_item_css_class = trim( $chained_item_css_class );
			$js_for_css             = '';
			if ( ! empty( $chained_item_css_class ) ) {
				$js_for_css = "jQuery( '.tab-included-products' ).removeClass( '" . $chained_item_css_class . "' ).addClass( '" . $chained_item_css_class . "' );";
			}

			$js = " var variable_id = '';
					apply_css_property();
					if( jQuery('input[name=variation_id]').length > 0 ) {
						display_chained_products_in_description_tab();
					}

					jQuery('input[name=variation_id]').on('change', function() {

						display_chained_products_in_description_tab();

					});

					function display_chained_products_in_description_tab() {

						setTimeout( function() {
							if( variable_id == jQuery('input[name=variation_id]').val() ) {
								return;
							}
							variable_id 			= jQuery('input[name=variation_id]').val();
							var original_stock      = jQuery( 'div.single_variation p.stock' ).text();
							var form_data           = new Object;
							form_data.variable_id   = variable_id;
							form_data.price         = jQuery( '#show_price' ).val();
							form_data.quantity      = jQuery( '#show_quantity' ).val();
							form_data.style         = jQuery( '#select_style' ).val();

							if( variable_id == undefined || variable_id == '' ) {
								jQuery( '.tab-included-products' ).html( '' );
								return;
							 }

							jQuery( '.tab-included-products' ).html('<img src = \'" . includes_url( 'images/spinner.gif' ) . "\' />');
							jQuery( 'span.price, div.single_variation p.stock' ).css( 'visibility', 'hidden' );
							jQuery.ajax({
								url: '" . admin_url( 'admin-ajax.php' ) . "',
								type: 'POST',
								data: {
									form_value: form_data,
									action: 'get_chained_products_html_view'
								},
								dataType: 'html',
								success:function( result ) {
										if( result ) {
												jQuery( '.tab-included-products' ).html( result );
												apply_css_property();

												if( result.lastIndexOf( '<stock' ) == -1 || result.lastIndexOf( '</stock>' ) == -1 ) {

													jQuery( 'div.single_variation p.stock' ).text( original_stock );

												} else {

													var max_quantity = result.substring( result.lastIndexOf( '<stock' ) + 30, result.lastIndexOf( '</stock>' ) );
													jQuery( 'div.single_variation p.stock' ).text( max_quantity + ' " . __( 'in stock', 'woocommerce-chained-products' ) . "' );
													jQuery( 'input[name=quantity]' ).attr( 'max', max_quantity );
													jQuery( 'input[name=quantity]' ).attr( 'data-max', max_quantity );

												}

										} else {

												jQuery( '.tab-included-products' ).html( '' );
												jQuery( 'div.single_variation p.stock' ).text( original_stock );
										}
									jQuery( 'span.price, div.single_variation p.stock' ).css( 'visibility', 'visible' );
								}
							});

						}, 0 ); //end setTimeout
					}

					function apply_css_property() {

						jQuery( '.tab-included-products' ).find( 'ul.products li' ).addClass( 'product' ).css( 'border-bottom', 'initial' );
						jQuery( '.tab-included-products' ).find( 'h3' ).css( {'line-height': '1.64', 'text-transform': 'initial', 'letter-spacing': 'initial'} );
						jQuery( '.tab-included-products' ).find( 'ul.products li.product a span.onsale' ).css( 'display' , 'none' );
						" . $js_for_css . '

					}
				';

			wc_enqueue_js( $js );

		}

		/**
		 * Function to add actions & filters specific to Chained Products
		 */
		public function add_chained_products_actions_filters() {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_after_shop_loop_chained_item' ) );
			add_filter( 'woocommerce_product_is_visible', array( $this, 'woocommerce_chained_product_is_visible' ), 20, 2 );

		}

		/**
		 * Function to remove action & filters specific to Chained products
		 */
		public function remove_chained_products_actions_filters() {

			remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_after_shop_loop_chained_item' ) );
			remove_filter( 'woocommerce_product_is_visible', array( $this, 'woocommerce_chained_product_is_visible' ), 20, 2 );

		}

		/**
		 * Function to show chained products which are only searchable
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class.
		 * @param boolean $visible Product catalog visibility.
		 * @param int     $product_id Product ID.
		 * @return boolean
		 */
		public function woocommerce_chained_product_is_visible( $visible, $product_id ) {
			global $wc_chained_products;

			$product = wc_get_product( $product_id );

			$parent_product_id  = $wc_chained_products->get_parent( $product_id );
			$is_chained_product = $wc_chained_products->is_chained_product( $parent_product_id );
			$product_visibility = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_catalog_visibility() : $product->visibility;

			if ( $is_chained_product && ( 'search' === $product_visibility || 'hidden' === $product_visibility ) ) {
				return true;
			}

			return $visible;
		}

		/**
		 * Function for removing price of chained products before calculating totals
		 *
		 * @param WC_Cart $cart_object Current cart object.
		 */
		public function woocommerce_before_chained_calculate_totals( $cart_object ) {
			global $wc_chained_products;

			foreach ( $cart_object->cart_contents as $value ) {
				$priced_individually = ( ! empty( $value['priced_individually'] ) ) ? $value['priced_individually'] : 'no';

				if ( isset( $value['chained_item_of'] ) && '' !== $value['chained_item_of'] && 'no' === $priced_individually ) {
					if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
						$value['data']->set_price( 0 );
					} else {
						$value['data']->price = 0;
					}
				}
			}
		}

		/**
		 * Function for making chained product's price to zero
		 *
		 * @param array $session_data Cart item session data.
		 * @param array $values Product data.
		 * @return array $session_data
		 */
		public function get_chained_cart_item_from_session( $session_data, $values ) {
			$priced_individually = ( ! empty( $values['priced_individually'] ) ) ? $values['priced_individually'] : 'no';

			if ( isset( $values['chained_item_of'] ) && '' !== $values['chained_item_of'] && 'no' === $priced_individually ) {
				$session_data['chained_item_of'] = $values['chained_item_of'];

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$session_data['data']->set_price( 0 );
				} else {
					$session_data['data']->price = 0;
				}
			}

			return $session_data;
		}

		/**
		 * Remove chained cart items with parent
		 *
		 * @param string  $cart_item_key Removed cart item key.
		 * @param WC_Cart $cart Cart object.
		 */
		public function chained_cart_item_removed( $cart_item_key, $cart ) {
			if ( ! empty( $cart->removed_cart_contents[ $cart_item_key ] ) ) {

				foreach ( $cart->cart_contents as $item_key => $item ) {

					if ( ! empty( $item['chained_item_of'] ) && $item['chained_item_of'] === $cart_item_key ) {
						$cart->removed_cart_contents[ $item_key ] = $item;
						unset( $cart->cart_contents[ $item_key ] );
						do_action( 'woocommerce_cart_item_removed', $item_key, $cart );
					}
				}
			}
		}

		/**
		 * Restore chained cart items with parent
		 *
		 * @param string  $cart_item_key Restored cart item key.
		 * @param WC_Cart $cart Cart object.
		 */
		public function chained_cart_item_restored( $cart_item_key, $cart ) {
			if ( ! empty( $cart->cart_contents[ $cart_item_key ] ) && ! empty( $cart->removed_cart_contents ) ) {

				foreach ( $cart->removed_cart_contents as $item_key => $item ) {

					if ( ! empty( $item['chained_item_of'] ) && $item['chained_item_of'] === $cart_item_key ) {
						$cart->cart_contents[ $item_key ] = $item;
						unset( $cart->removed_cart_contents[ $item_key ] );
						do_action( 'woocommerce_cart_item_restored', $item_key, $cart );
					}
				}
			}
		}

		/**
		 * Function to validate & update chained product's qty in cart
		 */
		public function validate_and_update_chained_product_quantity_in_cart() {
			$cart_contents_modified = WC()->cart->cart_contents;

			foreach ( $cart_contents_modified as $key => $value ) {

				if ( isset( $value['chained_item_of'] ) && ! isset( $cart_contents_modified[ $value['chained_item_of'] ] ) ) {
					WC()->cart->set_quantity( $key, 0 );
				}
			}
		}

		/**
		 * Function to manage chained product quantity in cart
		 *
		 * @param string  $cart_item_key Cart item key.
		 * @param WC_Cart $cart Cart objet.
		 */
		public function sa_before_cart_item_quantity_zero( $cart_item_key, $cart ) {
			$this->update_chained_product_quantity_in_cart( $cart_item_key );
		}

		/**
		 * Function to manage chained product quantity in cart
		 *
		 * @param string  $cart_item_key Cart item key.
		 * @param int     $quantity New quantity.
		 * @param int     $old_quantity Old quantity.
		 * @param WC_Cart $cart Cart object.
		 */
		public function sa_after_cart_item_quantity_update( $cart_item_key, $quantity, $old_quantity, $cart ) {
			$this->update_chained_product_quantity_in_cart( $cart_item_key, $quantity );
		}

		/**
		 * Function for updating chained product quantity in cart
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param string $cart_item_key Cart item key.
		 * @param int    $quantity Cart item quantity.
		 */
		public function update_chained_product_quantity_in_cart( $cart_item_key, $quantity = 0 ) {
			global $wc_chained_products;

			$cart_contents = WC()->cart->cart_contents;

			if ( isset( $cart_contents[ $cart_item_key ] ) && ! empty( $cart_contents[ $cart_item_key ] ) ) {

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$product_id = $cart_contents[ $cart_item_key ]['data']->get_id();
				} else {
					$product_id = $cart_contents[ $cart_item_key ]['data'] instanceof WC_Product_Variation ? $cart_contents[ $cart_item_key ]['variation_id'] : $cart_contents[ $cart_item_key ]['product_id'];
				}

				$quantity = ( $quantity <= 0 ) ? 0 : $cart_contents[ $cart_item_key ]['quantity'];

				foreach ( $cart_contents as $key => $value ) {
					if ( isset( $value['chained_item_of'] ) && $cart_item_key === $value['chained_item_of'] ) {

						if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
							$parent_product_id = $cart_contents[ $key ]['data']->get_id();
						} else {
							$parent_product_id = $cart_contents[ $key ]['data'] instanceof WC_Product_Variation ? $cart_contents[ $key ]['variation_id'] : $cart_contents[ $key ]['product_id'];
						}

						$bundle_product_data = $wc_chained_products->get_all_chained_product_details( $product_id );
						$chained_product_qty = $bundle_product_data[ $parent_product_id ]['unit'] * $quantity;
						WC()->cart->set_quantity( $key, $chained_product_qty );
					}
				}
			}
		}

		/**
		 * Function for keeping chained products quantity same as parent product
		 *
		 * @param int    $quantity Cart item quantity.
		 * @param string $cart_item_key Cart item key.
		 * @return int $quantity
		 */
		public function chained_cart_item_quantity( $quantity, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ) {
				return '<div class="quantity buttons_added">' . WC()->cart->cart_contents[ $cart_item_key ]['quantity'] . '</div>';
			}

			return $quantity;
		}

		/**
		 * Function for removing delete link for chained products
		 *
		 * @param string $link Cart item remove link.
		 * @param string $cart_item_key Cart item key.
		 * @return string $link
		 */
		public function chained_cart_item_remove_link( $link, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ) {
				return '';
			}

			return $link;
		}

		/**
		 * Function to add chained product to cart
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param string $cart_item_key Cart item key.
		 * @param int    $product_id ID of the product being added to the cart.
		 * @param int    $quantity  Quantity of the item being added to the cart.
		 * @param int    $variation_id ID of the variation being added to the cart.
		 * @param array  $variation Attribute values.
		 * @param array  $cart_item_data Extra cart item data passed to the item.
		 * @param string $parent_cart_key For working with parent/child product types such as MNM.
		 */
		public function add_chained_products_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $parent_cart_key = null ) {
			global $wc_chained_products;

			$product_id              = empty( $variation_id ) ? $product_id : $variation_id;
			$chained_products_detail = $wc_chained_products->get_all_chained_product_details( $product_id );

			if ( $chained_products_detail ) {

				$validation_result = $this->are_chained_products_available( $product_id, $quantity );

				if ( null !== $validation_result ) {
					return;
				}

				$chained_cart_item_data = array(
					'chained_item_of' => $cart_item_key,
				);

				foreach ( $chained_products_detail as $chained_products_id => $chained_products_data ) {

					$_product = wc_get_product( $chained_products_id );

					if ( $_product instanceof WC_Product ) {

						$chained_variation_id = '';

						if ( $_product instanceof WC_Product_Variation ) {
							$chained_variation_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $_product->get_id() : $_product->variation_id;
						}

						$chained_parent_id = ( empty( $chained_variation_id ) ) ? $chained_products_id : $wc_chained_products->get_parent( $chained_products_id );

						$chained_variation_data = ( ! empty( $chained_variation_id ) ) ? $_product->get_variation_attributes() : array();
						$chained_cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $chained_cart_item_data, $chained_parent_id, $chained_variation_id );
						$priced_individually    = ( ! empty( $chained_products_data['priced_individually'] ) ) ? $chained_products_data['priced_individually'] : 'no';

						// Prepare for adding children to cart.
						do_action( 'wc_before_chained_add_to_cart', $chained_parent_id, $quantity * $chained_products_data['unit'], $chained_variation_id, $chained_variation_data, $chained_cart_item_data, $chained_products_data['unit'] );

						$chained_item_cart_key = $this->chained_add_to_cart( $product_id, $chained_parent_id, $quantity * $chained_products_data['unit'], $chained_variation_id, $chained_variation_data, $chained_cart_item_data, $priced_individually );

						// Finish.
						do_action( 'wc_after_chained_add_to_cart', $chained_parent_id, $quantity * $chained_products_data['unit'], $chained_variation_id, $chained_variation_data, $chained_cart_item_data, $cart_item_key );
					}
				}
			}
		}

		/**
		 * Add a chained item to the cart. Must be done without updating session data, recalculating totals or calling 'woocommerce_add_to_cart' recursively.
		 * For the recursion issue, see: https://core.trac.wordpress.org/ticket/17817.
		 *
		 * @param int    $parent_cart_key ID of the product being added to the cart.
		 * @param int    $product_id Parent ID of the chained product.
		 * @param string $quantity Quantity of the chained item being added to the cart.
		 * @param int    $variation_id Variation ID if the chained item is a variation.
		 * @param array  $variation Attribute values of chained item.
		 * @param array  $cart_item_data Extra cart item data passed to the chained item.
		 * @param string $priced_individually Allow chained item to be priced 'yes|no'.
		 * @return string|false
		 */
		public function chained_add_to_cart( $parent_cart_key, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data, $priced_individually = 'no' ) {

			// Load cart item data when adding to cart.
			$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

			// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
			$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// See if this product and its options is already in the cart.
			$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

			// Get the product.
			$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

			// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart().
			if ( ! $cart_item_key ) {

				$cart_item_key = $cart_id;

				// Add item after merging with $cart_item_data - allow plugins and wc_cp_add_cart_item_filter to modify cart item.
				WC()->cart->cart_contents[ $cart_item_key ] = apply_filters(
					'woocommerce_add_cart_item', array_merge(
						$cart_item_data, array(
							'product_id'          => $product_id,
							'variation_id'        => $variation_id,
							'variation'           => $variation,
							'quantity'            => $quantity,
							'data'                => $product_data,
							'priced_individually' => $priced_individually,
						)
					), $cart_item_key
				);

			}

			// use this hook for compatibility instead of the 'woocommerce_add_to_cart' action hook to work around the recursion issue.
			// when the recursion issue is solved, we can simply replace calls to 'mnm_add_to_cart()' with direct calls to 'WC_Cart::add_to_cart()' and delete this function.
			do_action( 'woocommerce_chained_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $parent_cart_key );

			return $cart_item_key;
		}

		/**
		 * Function to remove subtotal for chained items in cart
		 *
		 * @param string $subtotal Cart item price.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return string $subtotal
		 */
		public function sa_cart_chained_item_subtotal( $subtotal = '', $cart_item = null, $cart_item_key = null ) {
			if ( empty( $subtotal ) || empty( $cart_item ) || empty( $cart_item_key ) || empty( $cart_item['chained_item_of'] ) ) {
				return $subtotal;
			}

			global $wc_chained_products;

			if ( $wc_chained_products->is_show_chained_item_price() ) {
				$priced_individually = ( ! empty( $cart_item['priced_individually'] ) ) ? $cart_item['priced_individually'] : 'no';

				if ( 'no' === $priced_individually ) {
					$called_by  = current_filter();
					$product_id = ( ! empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];
					$product    = wc_get_product( $product_id );
					$price      = $product->get_price();
					if ( 'woocommerce_cart_item_subtotal' === $called_by ) {
						$price = $price * $cart_item['quantity'];
					}
					return '<del>' . wc_price( $price ) . '</del>';
				}

				return $subtotal;
			}

			return '';
		}

		/**
		 * Function to add css class for chained items in cart
		 *
		 * @param string $class Default class name for cart item.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return string $class
		 */
		public function sa_cart_chained_item_class( $class = '', $cart_item = null, $cart_item_key = null ) {
			if ( empty( $cart_item ) || empty( $cart_item['chained_item_of'] ) ) {
				return $class;
			}

			return $class . ' chained_item';
		}

		/**
		 * Function to add indent in chained item name in cart
		 *
		 * @param string $item_name Product name.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return string $item_name
		 */
		public function sa_cart_chained_item_name( $item_name = '', $cart_item = null, $cart_item_key = null ) {
			if ( empty( $cart_item ) || empty( $cart_item['chained_item_of'] ) ) {
				return $item_name;
			}

			return '&nbsp;&nbsp;' . $item_name;
		}

		/**
		 * Function to add css class in chained items of order admin page
		 *
		 * @param string                $class Default order item class name.
		 * @param WC_Order_Item_Product $item Order item object.
		 * @return string $class
		 */
		public function sa_admin_html_chained_item_class( $class = '', $item = null ) {
			if ( empty( $item ) || empty( $item['chained_product_of'] ) ) {
				return $class;
			}

			$priced_individually = ( ! empty( $item['cp_priced_individually'] ) ) ? $item['cp_priced_individually'] : 'no';

			if ( 'no' === $priced_individually ) {
				$class = $class . ' cp_hide_line_item_meta';
			}

			return $class . ' chained_item';
		}

		/**
		 * Function to remove subtotal for chained items in order
		 *
		 * @param string   $subtotal Formatted line subtotal.
		 * @param array    $order_item Item to get total from.
		 * @param WC_Order $order Order object.
		 * @return string $subtotal
		 */
		public function sa_order_chained_item_subtotal( $subtotal = '', $order_item = null, $order = null ) {
			global $wc_chained_products;

			if ( empty( $subtotal ) || empty( $order_item ) || empty( $order ) || empty( $order_item['chained_product_of'] ) ) {

				$product = $order->get_product_from_item( $order_item );

				if ( $product instanceof WC_Product ) {

					$product_id     = $product->get_id();
					$chained_items  = $wc_chained_products->get_all_chained_product_details( $product_id );
					$override_total = false;

					if ( is_array( $chained_items ) && 0 < count( $chained_items ) ) {
						$quantity = $order_item['quantity'];
						$value    = 0;

						foreach ( $chained_items as $chained_item_id => $chained_item_data ) {
							$priced_individually = ( ! empty( $chained_item_data['priced_individually'] ) ) ? $chained_item_data['priced_individually'] : 'no';

							if ( 'yes' === $priced_individually ) {
								$chained_product = wc_get_product( $chained_item_id );

								if ( $chained_product instanceof WC_Product ) {
									$value         += $chained_product->get_price() * $chained_item_data['unit'];
									$override_total = true;
								}
							}
						}
					}

					if ( true === $override_total ) {
						return wc_price( ( $product->get_price() + $value ) * $quantity );
					} else {
						return $subtotal;
					}
				}

				return $subtotal;
			}

			if ( $wc_chained_products->is_show_chained_item_price() ) {

				if ( 'no' === $order_item['cp_priced_individually'] ) {
					$product = $order->get_product_from_item( $order_item );
					$price   = $product->get_price();
					$price   = $price * $order_item['qty'];

					return '<del>' . wc_price( $price ) . '</del>';
				}

				return $subtotal;
			}

			return '&nbsp;';
		}

		/**
		 * Function to add css class for chained items in order
		 *
		 * @param string                $class Default class name for order item.
		 * @param WC_Order_Item_Product $order_item Order item object.
		 * @param WC_Order              $order Order object.
		 * @return string $class
		 */
		public function sa_order_chained_item_class( $class = '', $order_item = null, $order = null ) {
			if ( empty( $order_item ) || empty( $order_item['chained_product_of'] ) ) {
				return $class;
			}

			return $class . ' chained_item';
		}

		/**
		 * Function to add indent in chained item name in order
		 *
		 * @param string                $item_name Order item name.
		 * @param WC_Order_Item_Product $order_item Order item object.
		 * @return string $item_name
		 */
		public function sa_order_chained_item_name( $item_name = '', $order_item = null ) {
			if ( empty( $order_item ) || empty( $order_item['chained_product_of'] ) ) {
				return $item_name;
			}

			return '&nbsp;&nbsp;' . $item_name;
		}

		/**
		 * Function to modify visibility of chained items in cart, mini-cart & checkout
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products
		 * @param bool   $is_visible Product visibility.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return bool $is_visible
		 */
		public function sa_chained_item_visible( $is_visible = true, $cart_item = null, $cart_item_key = null ) {
			if ( ! $is_visible || empty( $cart_item ) || empty( $cart_item_key ) || empty( $cart_item['chained_item_of'] ) ) {
				return $is_visible;
			}

			global $wc_chained_products;

			return $wc_chained_products->is_show_chained_items();
		}

		/**
		 * Function to modify visibility of chained items in order
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products.
		 * @param bool                  $is_visible Order item visibility.
		 * @param WC_Order_Item_Product $item Order item object.
		 * @return bool $is_visible
		 */
		public function sa_order_chained_item_visible( $is_visible = true, $item = null ) {
			if ( ! $is_visible || empty( $item ) || empty( $item['chained_product_of'] ) ) {
				return $is_visible;
			}

			global $wc_chained_products;

			return $wc_chained_products->is_show_chained_items();
		}

		/**
		 * Function to add css for admin page
		 */
		public function chained_products_admin_css() {
			global $pagenow, $typenow;

			if ( empty( $pagenow ) || ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) ) {
				return;
			}
			if ( empty( $typenow ) || 'shop_order' !== $typenow ) {
				return;
			}

			?>
			<!-- Chained Products Style Start -->
			<style type="text/css">
				.chained_item td.name {
					font-size: 0.9em;
				}
				.chained_item td.name {
					padding-left: 2em !important;
				}
				.chained_item.cp_hide_line_item_meta td.item_cost div,
				.chained_item.cp_hide_line_item_meta td.line_cost div,
				.chained_item.cp_hide_line_item_meta td.line_tax div {
					display: none;
				}
			</style>
			<!-- Chained Products Style End -->
			<?php

		}

		/**
		 * Function to add css for frontend page
		 */
		public function chained_products_frontend_css() {

			?>
			<!-- Chained Products Style Start -->
			<style type="text/css">
				.chained_item td.product-name {
					font-size: 0.9em;
				}
				.chained_item td.product-name {
					padding-left: 2em !important;
				}
			</style>
			<!-- Chained Products Style End -->
			<?php

		}

		/**
		 * Function to hide "Add to cart" button if chained products are out of stock
		 *
		 * @param boolean    $availability Availability of the product.
		 * @param WC_Product $_product Product object.
		 * @return boolean $availability
		 */
		public function woocommerce_get_chained_products_availability( $availability, $_product ) {
			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
				$product_id = $_product->get_id();
			} else {
				$product_id = $_product instanceof WC_Product_Variation ? $_product->variation_id : $_product->id;
			}

			$validation_result = $this->are_chained_products_available( $product_id );

			if ( null !== $validation_result ) {
				$_product->manage_stock               = 'no';
				$_product->stock_status               = 'outofstock';
				$chained_availability                 = array();
				$chained_availability['availability'] = __( 'Out of stock', 'woocommerce-chained-products' ) . ': ' . implode( ', ', $validation_result['product_titles'] ) . __( ' doesn\'t have sufficient quantity in stock.', 'woocommerce-chained-products' );
				$chained_availability['class']        = 'out-of-stock';

				// Hide parent product if chained product is out of stock.
				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
					if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
						$_product->set_catalog_visibility( 'hidden' );
						$_product->save();
					} else {
						$_product->visibility = 'hidden';
					}
				}

				return $chained_availability;
			}

			return $availability;
		}

		/**
		 * Function to display available variation below Product's name on shop front
		 *
		 * @global WC_Product $product
		 * @global array $variation_titles
		 * @global int $chained_parent_id
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @global array $chained_product_detail
		 * @global array $shortcode_attributes
		 */
		public function woocommerce_after_shop_loop_chained_item() {
			global $product, $variation_titles, $chained_parent_id, $wc_chained_products, $chained_product_detail, $shortcode_attributes;

			$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;

			if ( isset( $variation_titles[ $product_id ] ) ) {

				$chained_product_detail = isset( $chained_product_detail ) ? $chained_product_detail : $wc_chained_products->get_all_chained_product_details( $chained_parent_id );

				foreach ( $variation_titles[ $product_id ] as $product_id => $variation_data ) {

					echo $variation_data; // WPCS: XSS ok.

					if ( isset( $shortcode_attributes['quantity'] ) && 'yes' === $shortcode_attributes['quantity'] ) {
						echo ' ( &times; ' . esc_html( $chained_product_detail[ $product_id ]['unit'] ) . ' )<br />'; // WPCS: XSS ok.
					}
				}
			}
		}

		/**
		 * Function set the max value of quantity input box based on stock availability of chained products
		 *
		 * @global object $post
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param int        $stock Availability of the product.
		 * @param WC_Product $_product Product Object.
		 * @return int $stock
		 */
		public function validate_stock_availability_of_chained_products( $stock, $_product = null ) {
			global $post, $wc_chained_products;

			if ( $_product instanceof WC_Product ) {

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$product_id = $_product->get_id();
				} else {
					$product_id = $_product instanceof WC_Product_Variation ? $_product->variation_id : $post->ID;
				}

				$post_id                  = isset( $_product ) ? $product_id : $post->ID;
				$chained_product_instance = $wc_chained_products->get_product_instance( $post_id );

				if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && 'yes' === get_post_meta( $post_id, '_chained_product_manage_stock', true ) && $chained_product_instance->is_in_stock() ) {
					$max_quantity = $chained_product_instance->get_stock_quantity();

					if ( ! empty( $max_quantity ) ) {
						for ( $max_count = 1; $max_count < $max_quantity; $max_count++ ) {
							$validation_result = $this->are_chained_products_available( $post_id, $max_count );
							if ( null !== $validation_result ) {
								if ( isset( $stock['max_value'] ) ) {
									$stock['max_value'] = $max_count - 1;
								} elseif ( isset( $stock['availability'] ) ) {
									$stock['availability'] = ( $max_count - 1 ) . ' in stock';
								} else {
									$stock = $max_count - 1;
								}
								return $stock;
							}
						}
					}
				}
			}

			return $stock;
		}

		/**
		 * Function to display price of the chained products on shop page
		 *
		 * @global WC_Product $product
		 * @global int $chained_parent_id
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @global array $shortcode_attributes
		 * @global array $chained_product_detail
		 */
		public function woocommerce_template_chained_loop_quantity_and_price() {
			global $product, $chained_parent_id, $wc_chained_products, $shortcode_attributes, $chained_product_detail;

			$html_price = '';

			if ( $product->is_type( 'simple' ) && isset( $shortcode_attributes['quantity'] ) && 'yes' === $shortcode_attributes['quantity'] ) {
				$product_id             = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;
				$chained_product_detail = isset( $chained_product_detail ) ? $chained_product_detail : $wc_chained_products->get_all_chained_product_details( $chained_parent_id );
				echo ' ( &times; ' . esc_html( $chained_product_detail[ $product_id ]['unit'] ) . ' )<br />';
			}

			if ( ! empty( $product_id ) ) {
				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$priced_individually = ( isset( $chained_product_detail[ $product_id ]['priced_individually'] ) ) ? $chained_product_detail[ $product_id ]['priced_individually'] : 'no';
					$html_price          = ( 'no' === $priced_individually ) ? wc_format_sale_price( wc_price( $product->get_price() ), '' ) : wc_price( $product->get_price() );
				} else {
					$html_price = $product->get_price_html_from_to( wc_price( $product->get_price() ), '' );
				}
			}

			if ( isset( $shortcode_attributes['price'] ) && 'yes' === $shortcode_attributes['price'] ) {
				$price      = '';
				$price     .= ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text();
				$price     .= $html_price;
				$price_html = apply_filters( 'woocommerce_free_price_html', $price, $product );
				echo '<span class="price">' . $price_html . '</span>'; // WPCS: XSS ok.
			}
		}

		/**
		 * Function to check whether store has sufficient quantity of chained products
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @global array $chained_product_detail
		 * @param int   $product_id Product ID.
		 * @param int   $main_product_quantity Parent product quantity.
		 * @param array $chained_products_in_cart Chained products already present in cart.
		 * @return mixed
		 */
		public function are_chained_products_available( $product_id, $main_product_quantity = 1, $chained_products_in_cart = array() ) {
			global $wc_chained_products;

			if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && 'yes' === get_post_meta( $product_id, '_chained_product_manage_stock', true ) ) {

				$parent_product         = wc_get_product( $product_id );
				$chained_product_detail = $wc_chained_products->get_all_chained_product_details( $product_id );
				$chained_product_ids    = ( is_array( $chained_product_detail ) ) ? array_keys( $chained_product_detail ) : null;

				if ( null !== $chained_product_ids ) {
					$validation_result   = array();
					$product_titles      = array();
					$chained_add_to_cart = 'yes';

					$chained_product_quantity_in_cart = 0;

					foreach ( $chained_product_ids as $chained_product_id ) {
						$chained_product_instance = $wc_chained_products->get_product_instance( $chained_product_id );

						// Allow adding chained products to cart if backorders is allowed.
						if ( $parent_product->is_in_stock() &&
							$chained_product_instance->backorders_allowed() &&
							$chained_product_instance->is_in_stock()
							) {
							continue;
						}

						if ( array_key_exists( $chained_product_id, $chained_products_in_cart ) ) {
							$chained_product_quantity_in_cart = $chained_products_in_cart[ $chained_product_id ];
						}

						if ( ! $chained_product_instance->is_in_stock() ||
								( $chained_product_instance->managing_stock() &&
								! $chained_product_instance->is_downloadable() &&
								! $chained_product_instance->is_virtual() &&
								( $chained_product_instance->get_stock_quantity() < ( ( $main_product_quantity * $chained_product_detail[ $chained_product_id ]['unit'] ) + $chained_product_quantity_in_cart ) ) )
						) {

							$product_titles[]    = '"' . $wc_chained_products->get_product_title( $chained_product_id ) . '"';
							$chained_add_to_cart = 'no';
						}
					}
					if ( 'no' === $chained_add_to_cart ) {
						$validation_result['product_titles']         = $product_titles;
						$validation_result['chained_cart_validated'] = $chained_add_to_cart;
						return $validation_result;
					}
				}
			}
			return null;
		}

		/**
		 * Function to validate Add to cart based on stock quantity of chained products
		 *
		 * @global object $woocommerce - Main instance of WooCommerce
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param boolean $add_to_cart Add item to cart or not.
		 * @param int     $product_id Product ID.
		 * @param int     $main_product_quantity Parent product quantity.
		 * @return boolean
		 */
		public function woocommerce_chained_add_to_cart_validation( $add_to_cart, $product_id, $main_product_quantity ) {
			global $woocommerce, $wc_chained_products;

			if ( isset( $_GET['order_again'] ) && is_user_logged_in() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-order_again' ) ) {  // WPCS: sanitization ok.
				$order = wc_get_order( absint( $_GET['order_again'] ) );

				foreach ( $order->get_items() as $item ) {

					if ( $item['product_id'] === $product_id && isset( $item['chained_product_of'] ) ) {
						return false;
					}
				}
				return $add_to_cart;
			}

			// Do not add chained products again for a resubscribe order.
			if ( isset( $_GET['resubscribe'] ) && isset( $_GET['_wpnonce'] ) ) {
				$subscription = wcs_get_subscription( $_GET['resubscribe'] );  // WPCS: sanitization ok.

				foreach ( $subscription->get_items() as $item ) {
					if ( $item['product_id'] === $product_id && isset( $item['chained_product_of'] ) ) {
						return false;
					}
				}
				return $add_to_cart;
			}

			$product_id = ( isset( $_REQUEST['variation_id'] ) && $_REQUEST['variation_id'] > 0 ) ? $_REQUEST['variation_id'] : $product_id;  // WPCS: sanitization ok.

			$chained_products_in_cart = $this->get_chained_products_present_in_cart( $product_id );

			$validation_result = $this->are_chained_products_available( $product_id, $main_product_quantity, $chained_products_in_cart );

			if ( null !== $validation_result ) {
				/* translators: 1: Parent product name 2: Chained item name(s) */
				wc_add_notice( sprintf( __( 'Can not add %1$1s to cart as %2$2s doesn\'t have sufficient quantity in stock.', 'woocommerce-chained-products' ), $wc_chained_products->get_product_title( $product_id ), implode( ', ', $validation_result['product_titles'] ) ), 'error' );
				return false;
			}
			return $add_to_cart;
		}

		/**
		 * Function to get quantity chained products already present in cart
		 *
		 * @param int $product_id ID of the parent product that is being added to the cart.
		 * @return array $chained_products_in_cart;
		 */
		public function get_chained_products_present_in_cart( $product_id = '' ) {
			global $wc_chained_products;

			$chained_products_in_cart = array();

			$cart_contents = WC()->cart->cart_contents;

			if ( ! empty( $product_id ) && ! empty( $cart_contents ) ) {

				$chained_product_detail = $wc_chained_products->get_all_chained_product_details( $product_id );

				if ( is_array( $chained_product_detail ) && count( $chained_product_detail ) > 0 ) {

					foreach ( $cart_contents as $cart_item_key => $cart_item ) {

						$in_cart_chained_product_id = ( isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];

						if ( array_key_exists( $in_cart_chained_product_id, $chained_product_detail ) ) {

							if ( array_key_exists( $in_cart_chained_product_id, $chained_products_in_cart ) ) {
								$chained_products_in_cart[ $in_cart_chained_product_id ] += $cart_item['quantity'];
							} else {
								$chained_products_in_cart[ $in_cart_chained_product_id ] = $cart_item['quantity'];
							}
						}
					}
				}
			}

			return $chained_products_in_cart;
		}

		/**
		 * Function to validate updation of cart based on stock quantity of chained products
		 *
		 * @global object $woocommerce - Main instance of WooCommerce
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param boolean $update_cart Passed validation.
		 * @param string  $cart_item_key Cart item key.
		 * @param array   $cart_item Cart item data.
		 * @param int     $main_product_quantity Parent product quantity.
		 * @return boolean $update_cart
		 */
		public function woocommerce_chained_update_cart_validation( $update_cart, $cart_item_key, $cart_item, $main_product_quantity ) {
			global $woocommerce, $wc_chained_products;
			$product_id        = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0 ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			$validation_result = $this->are_chained_products_available( $product_id, $main_product_quantity );
			if ( null !== $validation_result ) {
				/* translators: 1: Parent product name 2: Chained item name(s) */
				wc_add_notice( sprintf( __( 'Can not increase quantity of %1$1s because %2$2s doesn\'t have sufficient quantity in stock.', 'woocommerce-chained-products' ), $wc_chained_products->get_product_title( $product_id ), implode( ', ', $validation_result['product_titles'] ) ), 'error' );
				return false;
			}
			return $update_cart;
		}

		/**
		 * Function to validate cart when it is loaded
		 *
		 * @global object $woocommerce Main instance of WooCommerce
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function woocommerce_chained_check_cart_items() {
			global $woocommerce, $wc_chained_products;
			$message = array();

			$cart = WC()->cart;
			if ( $cart instanceof WC_Cart ) {
				$cart_page_id = wc_get_page_id( 'cart' );
				foreach ( $cart->cart_contents as $cart_item_key => $cart_item_value ) {

					if ( isset( $cart_item_value['chained_item_of'] ) ) {
						continue;
					}

					$product_id        = ( isset( $cart_item_value['variation_id'] ) && $cart_item_value['variation_id'] > 0 ) ? $cart_item_value['variation_id'] : $cart_item_value['product_id'];
					$validation_result = $this->are_chained_products_available( $product_id, $cart_item_value['quantity'] );

					if ( null !== $validation_result ) {
						/* translators: 1: Parent product name 2: Chained item name(s) */
						$message[] = sprintf( __( 'Can not add %1$1s to cart as %2$2s doesn\'t have sufficient quantity in stock.', 'woocommerce-chained-products' ), $wc_chained_products->get_product_title( $cart_item_value['product_id'] ), implode( ', ', $validation_result['product_titles'] ) );
						$cart->set_quantity( $cart_item_key, 0 );
						if ( $cart_page_id ) {
							wp_safe_redirect( apply_filters( 'woocommerce_get_cart_url', get_permalink( $cart_page_id ) ) );
						}
					}
				}
				if ( count( $message ) > 0 ) {
					wc_add_notice( sprintf( __( implode( '. ', $message ), 'woocommerce-chained-products' ) ), 'message' ); // @codingStandardsIgnoreLine
				}
			}
		}

		/**
		 * Function for adding Chained Products Shortcode
		 */
		public function register_chained_products_shortcodes() {

			add_shortcode( 'chained_products', array( $this, 'get_chained_products_html_view' ) );
		}

		/**
		 * Function for Shortcode with included chained product detail and for Ajax response of chained product details in json encoded format
		 *
		 * @global object $post
		 * @global array $variation_titles
		 * @global int $chained_parent_id
		 * @global array $shortcode_attributes
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param array $chained_attributes Chained product attributes.
		 * @return string $chained_product_content
		 */
		public function get_chained_products_html_view( $chained_attributes ) {

			global $post, $variation_titles, $chained_parent_id, $shortcode_attributes, $wc_chained_products;
			$chained_product_content = '';

			if ( isset( $_POST['form_value']['variable_id'] ) && null !== $_POST['form_value']['variable_id'] ) { // WPCS: CSRF ok.

				$chained_parent_id    = wc_clean( wp_unslash( $_POST['form_value']['variable_id'] ) ); // WPCS: sanitization ok, CSRF ok.
				$shortcode_attributes = $_POST['form_value']; // WPCS: sanitization ok, CSRF openssl_free_key(key_identifier).

			} else {

				$chained_parent_id = $post->ID;
				$parent_product    = wc_get_product( $chained_parent_id );

				if ( ! is_object( $parent_product ) ) {
					return;
				}

				$shortcode_attributes['price']     = isset( $chained_attributes['price'] ) ? $chained_attributes['price'] : 'yes';
				$shortcode_attributes['quantity']  = isset( $chained_attributes['quantity'] ) ? $chained_attributes['quantity'] : 'yes';
				$shortcode_attributes['style']     = isset( $chained_attributes['style'] ) ? $chained_attributes['style'] : 'grid';
				$shortcode_attributes['css_class'] = isset( $chained_attributes['css_class'] ) ? $chained_attributes['css_class'] : '';

				$chained_item_css_class = apply_filters( 'chained_item_css_class', 'chained_items_container', $chained_parent_id );
				$chained_item_css_class = trim( $chained_item_css_class );

				$chained_product_content .= '<input type = "hidden" id = "show_price" value = "' . $shortcode_attributes['price'] . '"/>';
				$chained_product_content .= '<input type = "hidden" id = "show_quantity" value = "' . $shortcode_attributes['quantity'] . '"/>';
				$chained_product_content .= '<input type = "hidden" id = "select_style" value = "' . $shortcode_attributes['style'] . '"/>';
				$chained_product_content .= '<div class = "tab-included-products ' . $chained_item_css_class . ' ' . $shortcode_attributes['css_class'] . '">';
				$chained_product_content .= ( $parent_product->is_type( 'variable' ) ) ? '</div>' : '';

			}
			$total_chained_details = $wc_chained_products->get_all_chained_product_details( $chained_parent_id );
			$chained_product_ids   = is_array( $total_chained_details ) ? array_keys( $total_chained_details ) : null;
			if ( $chained_product_ids ) {

				$chained_product_instance = $wc_chained_products->get_product_instance( $chained_parent_id );
				if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && 'yes' === get_post_meta( $chained_parent_id, '_chained_product_manage_stock', true ) && $chained_product_instance->is_in_stock() ) {

					if ( ! $chained_product_instance->backorders_allowed() ) {
						$max_quantity = $chained_product_instance->get_stock_quantity();

						if ( ! empty( $max_quantity ) ) {
							for ( $max_count = 1; $max_count <= $max_quantity; $max_count++ ) {

								$validation_result = $this->are_chained_products_available( $chained_parent_id, $max_count );
								if ( null !== $validation_result ) {
										break;
								}
							}
						}

						$chained_product_content .= empty( $max_quantity ) ? '' : '<stock style = "display:none">' . ( $max_count - 1 ) . '</stock>';
					}
				}

				// For list/grid view of included product.
				if ( isset( $shortcode_attributes['style'] ) && 'list' === $shortcode_attributes['style'] ) {

					$chained_product_content .= '<ul>';

					foreach ( $total_chained_details as $id => $product_data ) {

						$product = wc_get_product( $id );

						if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
							$priced_individually = ( isset( $product_data['priced_individually'] ) ) ? $product_data['priced_individually'] : 'no';
							$price               = ( 'no' === $priced_individually ) ? wc_format_sale_price( wc_price( $product->get_price() ), '' ) : wc_price( $product->get_price() );
						} else {
							$price = $product->get_price_html_from_to( wc_price( $product->get_price() ), '' );
						}

						$price_html = apply_filters( 'woocommerce_free_price_html', $price, $product );

						if ( $product instanceof WC_Product_Simple ) {
							$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;
						} else {
							$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_parent_id() : $product->parent->id;
						}

						$chained_product_content .= "<li><a href='" . get_permalink( $product_id ) . "' style='text-decoration: none;'>" . $product_data['product_name'];
						$chained_product_content .= ( isset( $shortcode_attributes['quantity'] ) && 'yes' === $shortcode_attributes['quantity'] ) ? ' ( &times; ' . $product_data['unit'] . ' )' : '';
						$chained_product_content .= ( isset( $shortcode_attributes['price'] ) && 'yes' === $shortcode_attributes['price'] ) ? " <span class='price'>" . $price_html . '</span>' : '';
						$chained_product_content .= '</a></li>';

					}

					$chained_product_content .= '</ul>';

				} elseif ( isset( $shortcode_attributes['style'] ) && 'grid' === $shortcode_attributes['style'] ) {

					$atts             = array();
					$product_ids      = array();
					$variation_titles = array();

					foreach ( $chained_product_ids as $chained_product_id ) {

						$parent_id = wp_get_post_parent_id( $chained_product_id );

						if ( $parent_id > 0 ) {
							$product_ids[] = $parent_id;
							$_product      = wc_get_product( $chained_product_id );

							if ( $_product instanceof WC_Product_Variation ) {
								$variation_data = $_product->get_variation_attributes();

								if ( '' !== $variation_data ) {
									$variation_titles[ $parent_id ][ $chained_product_id ] = ' ( ' . wc_get_formatted_variation( $variation_data, true ) . ' )';
								}
							}
						} else {
							$product_ids[] = $chained_product_id;
						}
					}

					$atts['ids'] = implode( ',', $product_ids );

					if ( empty( $atts ) ) {
						return;
					}

					$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

					// Get order + orderby args from string.
					$orderby_value = explode( '-', $orderby_value );
					$orderby       = esc_attr( $orderby_value[0] );
					$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : 'asc';

					extract( // @codingStandardsIgnoreLine
						shortcode_atts(
							array(
								'orderby' => strtolower( $orderby ),
								'order'   => strtoupper( $order ),
							),
							$atts
						)
					);

					$args = array(
						'post_type'      => array( 'product' ),
						'orderby'        => $orderby,
						'order'          => $order,
						'posts_per_page' => -1,
					);

					if ( isset( $atts['ids'] ) ) {
							$ids              = explode( ',', $atts['ids'] );
							$ids              = array_map( 'trim', $ids );
							$args['post__in'] = $ids;
					}

					ob_start();

					$alter_shop_loop_item = has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

					remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

					if ( $alter_shop_loop_item ) {
						remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					}

					// For adding all visibility related actions & filters that are specific to Chained Products.
					do_action( 'add_chained_products_actions_filters' );
					add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'woocommerce_template_chained_loop_quantity_and_price' ) );

					if ( version_compare( WOOCOMMERCE_VERSION, '1.6', '<' ) ) {

						query_posts( $args ); // @codingStandardsIgnoreLine
						wc_get_template_part( 'loop', 'shop' ); // Depricated since version 1.6.

					} else {

						$products = new WP_Query( $args );

						if ( $products->have_posts() ) {

							while ( $products->have_posts() ) {
									$products->the_post();
									wc_get_template_part( 'content', 'product' );
							}

							$chained_product_content .= '<ul class="products">' . ob_get_clean() . '</ul>';

						}
					}

					remove_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'woocommerce_template_chained_loop_quantity_and_price' ), 10 );

					// For removing all visibility related actions & filters that are specific to Chained Products.
					do_action( 'remove_chained_products_actions_filters' );
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

					if ( $alter_shop_loop_item ) {
						add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					}

					wp_reset_query(); // @codingStandardsIgnoreLine

				}
			}

			// To prevent return 0 by WordPress ajax response.
			if ( isset( $_POST['form_value']['variable_id'] ) && null !== $_POST['form_value']['variable_id'] ) { // WPCS: CSRF ok.

				echo $chained_product_content; // WPCS: XSS ok.
				exit();

			}
			$chained_product_content .= ( $parent_product->is_type( 'simple' ) ) ? '</div>' : '';
			return $chained_product_content;
		}
	}//end class
}
