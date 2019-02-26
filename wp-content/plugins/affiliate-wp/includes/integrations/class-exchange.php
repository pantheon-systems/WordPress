<?php

class Affiliate_WP_Exchange extends Affiliate_WP_Base {

	/**
	 * The transaction object
	 *
	 * @access  private
	 * @since   1.3
	*/
	private $transaction;

	public function init() {

		$this->context = 'it-exchange';

		if( ! class_exists( 'IT_Exchange' ) ) {
			return;
		}

		add_filter( 'it_exchange_generate_transaction_object', array( $this, 'add_affiliate_id_to_txn_object' ) );
		add_action( 'it_exchange_add_transaction_success', array( $this, 'add_pending_referral' ), 10 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'mark_referral_complete' ), 10, 4 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'revoke_referral_on_refund' ), 10, 4 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'revoke_referral_on_void' ), 10, 4 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'revoke_referral_on_cancelled' ), 10, 4 );
		add_action( 'wp_trash_post', array( $this, 'revoke_referral_on_delete' ), 10 );

		// coupon code tracking actions and filters
		add_action( 'it_libraries_loaded', array( $this, 'add_coupon_edit_hooks' ) );
		add_action( 'it_exchange_basic_coupons_saved_coupon', array( $this, 'store_coupon_affiliate' ), 10, 2 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

		add_action( 'it_libraries_loaded', array( $this, 'load_product_feature' ) );

		add_action( 'init', array( $this, 'page_rewrites' ) );
	}

	/**
	 * Add the referred affiliate ID to the transaction object
	 *
	 * @access  public
	 * @since   1.7.7
	 * @param object $transaction_object the transaction object right before being added to database
	 * @return object
	 */
	public function add_affiliate_id_to_txn_object( $transaction_object ) {

		if ( $this->was_referred() ) {
			$transaction_object->affwp_affiliate_id = $this->get_affiliate_id();
		}

		return $transaction_object;
	}

	/**
	 * Records a pending referral when a pending payment is created
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add_pending_referral( $transaction_id = 0 ) {

		// get transaction object
		$this->transaction = apply_filters( 'affwp_get_it_exchange_transaction', get_post_meta( $transaction_id, '_it_exchange_cart_object', true ) );

		// get affiliate ID from the transaction object if set
		$affiliate_id = isset( $this->transaction->affwp_affiliate_id ) ? $this->transaction->affwp_affiliate_id : $this->get_affiliate_id( $transaction_id );

		// set affiliate ID to that of tracked affiliate coupon (if any)
		if ( $this->transaction->coupons && is_array( $this->transaction->coupons ) ) {

			if ( ! empty( $this->transaction->coupons['cart'] ) ) {

				foreach ( $this->transaction->coupons['cart'] as $coupon ) {

					$coupon_affiliate_id = get_post_meta( $coupon['id'], 'affwp_coupon_affiliate', true );

					if ( ! $coupon_affiliate_id ) {

						$this->log( 'Referral not created because of missing affiliate ID.' );

						continue;
					}

					if ( ! affiliate_wp()->tracking->is_valid_affiliate( $coupon_affiliate_id ) ) {

						$this->log( 'Referral not created because affiliate is invalid.' );

						continue;
					}

					if ( false !== $coupon_affiliate_id ) {
						$affiliate_id = $coupon_affiliate_id;
					}

					break;

				}
			}
		}

		if ( $affiliate_id ) {

			$guest_checkout_email = it_exchange_get_transaction_customer_email( $transaction_id );
			$email                = isset( $guest_checkout_email ) ? $guest_checkout_email : $this->transaction->shipping_address['email'];

			if ( $this->is_affiliate_email( $email, $affiliate_id ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return; // Customers cannot refer themselves
			}

			$sub_total      = 0;
			$total          = floatval( $this->transaction->total );
			$total_taxes    = floatval( $this->transaction->taxes_raw );
			$shipping       = floatval( $this->transaction->shipping_total );

			foreach ( $this->transaction->products as $product ) {
				$sub_total += $product['product_subtotal'];
			}

			$referral_total = $total;

			if ( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$referral_total -= $total_taxes;
			}

			if ( affiliate_wp()->settings->get( 'exclude_shipping' ) && $shipping > 0 ) {
				$referral_total -= $shipping / 100;
			}

			$amount = 0;

			foreach ( $this->transaction->products as $product ) {

				if ( get_post_meta( $product['product_id'], "_affwp_{$this->context}_referrals_disabled", true ) ) {
					continue;
				}

				$product_percent_of_cart = (float) $product['product_subtotal'] / $sub_total;
				$referral_product_price  = (float) $product_percent_of_cart * (float) $referral_total;

				$amount += $this->calculate_referral_amount( $referral_product_price, $transaction_id, $product['product_id'], $affiliate_id );
			}

			$this->insert_pending_referral( $amount, $transaction_id, $this->transaction->description, $this->get_products( $transaction_id ), array( 'affiliate_id' => $affiliate_id ) );

			if ( it_exchange_transaction_is_cleared_for_delivery( $transaction_id ) ) {
				$this->complete_referral( $transaction_id );
			}

		}

	}

	/**
	 * Retrieves the product details array for the referral
	 *
	 * @access  public
	 * @since   1.6
	 * @return  array
	*/
	public function get_products( $order_id = 0 ) {

		$products  = array();
		$items     = $this->transaction->products;
		foreach( $items as $key => $product ) {

			if( get_post_meta( $product['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			$products[] = array(
				'name'            => $product['product_name'],
				'id'              => $product['product_id'],
				'price'           => $product['product_subtotal'],
				'referral_amount' => $this->calculate_referral_amount( $product['product_subtotal'], $order_id, $product['product_id'] )
			);

		}

		return $products;

	}

	public function mark_referral_complete( $transaction, $old_status, $old_status_cleared, $new_status ) {

		$new_status         = it_exchange_get_transaction_status( $transaction->ID );
		$new_status_cleared = it_exchange_transaction_is_cleared_for_delivery( $transaction->ID );

		if ( ( $new_status != $old_status ) && ! $old_status_cleared && $new_status_cleared ) {

			$this->complete_referral( $transaction->ID );

		}

	}

	public function revoke_referral_on_refund( $transaction, $old_status, $old_status_cleared, $new_status ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		if( 'refunded' == strtolower( $transaction->get_status() ) && ( in_array( strtolower( $old_status ), array( 'completed', 'paid', 'succeeded' ), true ) ) ) {

			$this->reject_referral( $transaction->ID );

		}

	}

	public function revoke_referral_on_void( $transaction, $old_status, $old_status_cleared, $new_status ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		if( 'voided' == strtolower( $transaction->get_status() ) ) {

			$this->reject_referral( $transaction->ID );

		}

	}

	public function revoke_referral_on_cancelled( $transaction, $old_status, $old_status_cleared, $new_status ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		if( 'cancelled' == strtolower( $transaction->get_status() ) ) {

			$this->reject_referral( $transaction->ID );

		}

	}

	public function revoke_referral_on_delete( $transaction_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$post = get_post( $transaction_id );

		if( ! $post ) {
			return;
		}

		if( 'it_exchange_tran' != $post->post_type ) {
			return;
		}

		$this->reject_referral( $transaction_id );

	}

	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'it-exchange' != $referral->context ) {

			return $reference;

		}

		$url = get_edit_post_link( $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

	/**
	 * Add coupon edit hooks depending on version of Exchange active.
	 *
	 * The 'General' tab did not become active until version 1.33
	 *
	 * @access public
	 * @since 1.8
	 */
	public function add_coupon_edit_hooks() {

		if ( version_compare( $GLOBALS['it_exchange']['version'], '1.33.0', '>' ) ) {
			add_action( 'it_exchange_basic_coupons_coupon_edit_tab_general', array( $this, 'coupon_edit' ) );
		} else {
			add_action( 'it_exchange_basics_coupon_coupon_edit_screen_end_fields', array( $this, 'coupon_edit' ) );
		}
	}

	/**
	 * Shows the affiliate drop down on the coupon edit / add screens
	 *
	 * @access  public
	 * @since   1.3
	*/
	public function coupon_edit( $form ) {

		add_filter( 'affwp_is_admin_page', '__return_true' );
		affwp_admin_scripts();
		
		$user_id      = 0;
		$user_name    = '';
		$coupon_id    = ! empty( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : 0;
		$affiliate_id = get_post_meta( $coupon_id, 'affwp_coupon_affiliate', true );
		
		if( $affiliate_id ) {
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
			$user         = get_userdata( $user_id );
			$user_name    = $user ? $user->user_login : '';
		}
?>
		<div class="field affwp-coupon">
			<th scope="row" valign="top">
				<label for="user_name"><?php _e( 'Affiliate coupon?', 'affiliate-wp' ); ?></label>
			</th>
			<td>
				<span class="affwp-ajax-search-wrap">
					<input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search" data-affwp-status="active" autocomplete="off" />
				</span>
				<p class="description"><?php _e( 'If you would like to connect this coupon to an affiliate, enter the name of the affiliate it belongs to.', 'affiliate-wp' ); ?></p>
			</td>
		</div>
<?php
	}

	/**
	 * Stores the affiliate ID in the coupons meta if it is an affiliate's coupon
	 *
	 * @access  public
	 * @since   1.3
	*/
	public function store_coupon_affiliate( $coupon_id = 0, $data = array() ) {

		if ( empty( $_POST['user_name'] ) ) {
			delete_post_meta( $coupon_id, 'affwp_coupon_affiliate' );
			return;
		}

		if( empty( $_POST['user_id'] ) && empty( $_POST['user_name'] ) ) {
			return;
		}

		$data = affiliate_wp()->utils->process_request_data( $_POST, 'user_name' );

		$affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

		update_post_meta( $coupon_id, 'affwp_coupon_affiliate', $affiliate_id );

	}

	/**
	 * Load the product feature for controlling per-product rates.
	 * @access  public
	 * @since   1.5
	 */
	public function load_product_feature() {
		if( class_exists( 'IT_Exchange_Product_Feature_Abstract' ) ) {
			require_once ( AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/extras/class-exchange-feature.php' );
		}
	}

	/**
	 * Register rewrites for custom pages
	 *
	 * @access  public
	 * @since   1.7.8
	 */
	public function page_rewrites() {

		$pages = it_exchange_get_pages( true, array( 'type' => 'exchange' ) );

		// Add rewrite rules for each page
		foreach( $pages as $page => $data ) {

			if( 'login' == $page || 'logout' == $page ) {
				continue;
			}

			$ref  = affiliate_wp()->tracking->get_referral_var();
			$slug = str_replace( home_url( '/' ), '', it_exchange_get_core_page_urls( $page ) );

			add_rewrite_rule( $slug . $ref . '(/(.*))?/?$', 'index.php?' . $page . '=1&' . $ref . '=$matches[3]', 'top' );

		}
	}
}

if ( class_exists( 'IT_Exchange' ) ) {
	new Affiliate_WP_Exchange;
}
