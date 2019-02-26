<?php
/**
 * Class to handle display of Chained Products review notice
 *
 * @package woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_CP_Admin_Notices' ) ) {

	/**
	 * Class to handle display of Chained Products review notice.
	 *
	 * @author StoreApps
	 */
	class WC_CP_Admin_Notices {

		/**
		 * The msg heading for review.
		 *
		 * @var string
		 */
		public $msg = '';

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_notices', array( $this, 'sa_cp_show_review_notice' ) );
			add_action( 'admin_init', array( $this, 'sa_cp_update_notice_action' ) );
		}

		/**
		 * Shows review notice
		 */
		public function sa_cp_show_review_notice() {
			$is_review_page = ( isset( $_GET['post_type'] ) && ( 'shop_order' === $_GET['post_type'] || 'product' === $_GET['post_type'] ) ) ? true : false; // WPCS: CSRF ok.

			if ( true === $this->may_be_show_review_notice() && true === $is_review_page ) {
				$this->cp_show_review_notice();
			}
		}

		/**
		 * Function to decide whether to show the display notice or not.
		 *
		 * @return bool $show_notice
		 */
		public function may_be_show_review_notice() {
			$show_notice = false;

			$cp_show_review_notice = get_option( 'cp_show_review_notice', 'yes' );

			if ( 'no' !== $cp_show_review_notice ) {
				$this->msg = __( 'Glad you are using', 'woocommerce-chained-products' ) . '&nbsp;<strong>' . __( 'WooCommerce Chained Products.', 'woocommerce-chained-products' ) . '</strong><br>'; // Default msg.

				if ( 'yes' === $cp_show_review_notice ) {
					if ( true === $this->orders_has_chained_item() ) {
						$show_notice = true;
						$this->msg   = '<strong>' . __( 'Congratulations!', 'woocommerce-chained-products' ) . '</strong>&nbsp;' . esc_html__( 'You have successfully sold a product using', 'woocommerce_chained_product' ) . '&nbsp;<strong>' . __( 'WooCommerce Chained Products.', 'woocommerce_chained_product' ) . '</strong><br>';
					} elseif ( true === $this->products_has_chained_item() ) {
						$show_notice = true;
					}
				} elseif ( time() >= absint( $cp_show_review_notice ) ) {
					$show_notice = true;
				}
			}

			return $show_notice;
		}

		/**
		 * Function to check if orders contains chained items or not.
		 *
		 * @return bool $has_chained_items
		 */
		public function orders_has_chained_item() {
			global $wpdb;

			$order_count = wp_cache_get( 'wc_cp_order_count', 'woocommerce_chained_product' );

			if ( false === $order_count ) {
				$order_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT count( DISTINCT order_id )
					FROM {$wpdb->prefix}woocommerce_order_items AS o
					JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oi ON ( o.order_item_id = oi.order_item_id AND oi.meta_key = %s )
					JOIN {$wpdb->prefix}posts AS p ON (o.order_id = p.ID AND p.post_type = %s AND p.post_status = %s )",
						'_chained_product_of', 'shop_order', 'wc-completed'
					)
				); // WPCS: db call ok.

				wp_cache_set( 'wc_cp_order_count', $order_count, 'woocommerce_chained_product' );
			}

			$has_chained_items = ( 0 < $order_count ) ? true : false;

			return $has_chained_items;
		}

		/**
		 * Function to check if products contains chained items or not.
		 *
		 * @return bool $has_chained_items
		 */
		public function products_has_chained_item() {
			global $wpdb;

			$product_count = wp_cache_get( 'wc_cp_product_count', 'woocommerce_chained_product' );

			if ( false === $product_count ) {
				$product_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT count( ID )
					FROM {$wpdb->prefix}posts AS p
					JOIN {$wpdb->prefix}postmeta AS pm
					ON ( p.ID = pm.post_id AND pm.meta_key = %s AND p.post_status = %s AND ( p.post_type = %s || p.post_type = %s ) AND DATEDIFF( now(), p.post_date ) > 30 )",
						'_chained_product_detail', 'publish', 'product', 'product_varitation'
					)
				); // WPCS: db call ok.

				wp_cache_set( 'wc_cp_product_count', $product_count, 'woocommerce_chained_product' );
			}

			$has_chained_items = ( 0 < $product_count ) ? true : false;

			return $has_chained_items;
		}


		/**
		 * 5 star review notice content.
		 */
		public function cp_show_review_notice() {
			?>
			<div id="wc_cp_review_notice" class="notice updated fade">
				<div class="wc_cp_review_notice_action" style="float: right;padding: 0.5em 0;text-align: right;font-size: 0.9em;">
					<a href="?cp_notice_action=remind" class="wc_cp_review_notice_remind"><?php echo esc_html__( 'Remind me after a month', 'woocommerce-chained-products' ); ?></a><br>
					<a href="?cp_notice_action=dismiss" class="wc_cp_review_notice_remove"><?php echo esc_html__( 'Never show again', 'woocommerce-chained-products' ); ?></a>
				</div>
				<p>
					<?php echo $this->msg . esc_html__( 'If you are having a great experience using our plugin then consider leaving us a', 'woocommerce-chained-products' ) . ' <a target="__blank" href="' . esc_url( 'https://woocommerce.com/products/chained-products/#comments' ) . '">' . esc_html__( '5 star review.', 'woocommerce-chained-products' ) . '</a>&nbsp;' . esc_html__( 'Thank you in advance 😊', 'woocommerce-chained-products' ); // WPCS: XSS ok. ?>
				</p>
			</div>
			<?php
		}


		/**
		 * Function to update notice action on click of Dismiss/Remind me Later.
		 */
		public function sa_cp_update_notice_action() {
			$action = ( isset( $_GET['cp_notice_action'] ) ) ? wc_clean( wp_unslash( $_GET['cp_notice_action'] ) ) : ''; // WPCS: CSRF ok, sanitization ok.

			if ( ! empty( $action ) ) {
				switch ( $action ) {
					case 'remind':
						$option_value = strtotime( '+1 month' );
						break;
					case 'dismiss':
						$option_value = 'no';
						break;
					default:
						$option_value = 'no';
				}

				update_option( 'cp_show_review_notice', $option_value );

				$referer = wp_get_referer();
				wp_safe_redirect( $referer );
				exit();
			}
		}
	}
}

new WC_CP_Admin_Notices();
