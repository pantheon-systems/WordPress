<?php
/**
 * AffiliateWP Jigoshop Integration
 *
 * This integrates support for Jigoshop.
 * @since version: 1.0.2
 */

class Affiliate_WP_Jigoshop extends Affiliate_WP_Base {

	/**
	 * The order object
	 *
	 * @access  private
	 * @since   1.3
	*/
	private $order;


	/**
	 * Initiate
	 *
	 * @function init()
	 * @access public
	 */
	public function init() {
		$this->context = 'jigoshop';

		// Actions
		add_action( 'jigoshop_new_order', array( $this, 'add_pending_referral' ), 10, 1 ); // Referral added when order is made.
		add_action( 'jigoshop_order_status_completed', array( $this, 'mark_referral_complete' ), 10 ); // Referral is marked complete when order is completed.
		add_action( 'jigoshop_order_status_completed_to_refunded', array( $this, 'revoke_referral_on_refund' ), 10 ); // Referral is revoked when order has been refunded.
		add_action( 'add_meta_boxes', array( $this, 'add_coupon_meta_box' ) );
		add_action( 'jigoshop_process_shop_coupon_meta', array( $this, 'store_discount_affiliate' ), 1, 2 );

		// Filters
		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
	}


	/**
	 * Add pending referral
	 *
	 * @function add_pending_referral
	 * @access public
	 */
	public function add_pending_referral( $order_id = 0 ) {
		// Check if an affiliate coupon was used
		$affiliate_id = $this->get_coupon_affiliate_id();

		if( $this->was_referred() || $affiliate_id ) {

			if( false !== $affiliate_id ) {
				$this->affiliate_id = $affiliate_id;
			}

			$this->order = apply_filters( 'affwp_get_jigoshop_order', new jigoshop_order( $order_id ) ); // Fetch order

			if ( $this->is_affiliate_email( $this->order->billing_email ) ) {
				return; // Customers cannot refer themselves
			}

			$description = '';
			$items       = $this->order->items;
			foreach( $items as $key => $item ) {
				$description .= $item['name'];
				if( $key + 1 < count( $items ) ) {
					$description .= ', ';
				}
			}

			$amount = $this->order->order_total;

			if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {

				$amount -= $this->order->get_total_tax();

			}

			if( affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

				$amount -= $this->order->order_shipping;

			}

			$referral_total = $this->calculate_referral_amount( $amount, $order_id );

			$this->insert_pending_referral( $referral_total, $order_id, $description );

			$referral = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );
			$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
			$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );

			$this->order->add_order_note( sprintf( __( 'Referral #%d for %s recorded for %s', 'affiliate-wp' ), $referral->referral_id, $amount, $name ) );

		}

	}

	/**
	 * Mark referral complete
	 *
	 * @function mark_referral_complete()
	 * @access public
	 */
	public function mark_referral_complete( $order_id = 0 ) {

		$this->complete_referral( $order_id );

	}

	/**
	 * Revoke referral on refund
	 *
	 * @function revoke_referral_on_refund()
	 * @access public
	 */
	public function revoke_referral_on_refund( $order_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $order_id );

	}

	/**
	 * Reference link
	 *
	 * @function reference_link()
	 * @access public
	 */
	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'jigoshop' != $referral->context ) {

			return $reference;

		}

		$url = get_edit_post_link( $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

	/**
	 * Register coupon meta box
	 *
	 * @access public
	 */
	public function add_coupon_meta_box() {
		add_meta_box( 'jigoshop-coupon-affiliate-data', __( 'Affiliate Data', 'affiliate-wp' ), array( $this, 'display_coupon_meta_box' ), 'shop_coupon', 'side', 'default' );
	}

	/**
	 * Display coupon meta box
	 *
	 * @access public
	 */
	public function display_coupon_meta_box() {
		global $post;

		add_filter( 'affwp_is_admin_page', '__return_true' );
		affwp_admin_scripts();

		$affiliate_id = get_post_meta( $post->ID, 'affwp_discount_affiliate', true );
		$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
		$user         = get_userdata( $user_id );
		$user_name    = $user ? $user->user_login : '';
		?>
		<p class="form-field affwp-jigoshop-coupon-field">
			<label for="user_name"><?php _e( 'If you would like to connect this discount to an affiliate, enter the name of the affiliate it belongs to.', 'affiliate-wp' ); ?></label>
			<span class="affwp-ajax-search-wrap">
				<span class="affwp-jigoshop-coupon-input-wrap">
					<input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search" data-affwp-status="active" autocomplete="off" />
				</span>
			</span>
		</p>
		<?php
	}

	/**
	 * Save coupon meta
	 *
	 * @access public
	 */
	public function store_discount_affiliate( $post_id, $post ) {
		if( empty( $_POST['user_name'] ) ) {
			delete_post_meta( $post_id, 'affwp_discount_affiliate' );
			return;
		}

		if( empty( $_POST['user_id'] ) && empty( $_POST['user_name'] ) ) {
			return;
		}

		$data = affiliate_wp()->utils->process_request_data( $_POST, 'user_name' );

		$affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

		update_post_meta( $post_id, 'affwp_discount_affiliate', $affiliate_id );
	}

	/**
	 * Retrieve the affiliate ID for the coupon used, if any
	 *
	 * @access  public
	 * @since   1.7.5
	*/
	private function get_coupon_affiliate_id() {
		$coupons = jigoshop_cart::get_coupons();

		if( empty( $coupons ) ) {
			return false;
		}

		foreach( $coupons as $code ) {
			$coupon       = JS_Coupons::get_coupon( $code );
			$affiliate_id = get_post_meta( $coupon['id'], 'affwp_discount_affiliate', true );

			if( $affiliate_id ) {

				if( ! affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id ) ) {
					continue;
				}

				return $affiliate_id;

			}

		}

		return false;
	}
}

if ( function_exists( 'jigoshop_init') ) {
	new Affiliate_WP_Jigoshop;
}
