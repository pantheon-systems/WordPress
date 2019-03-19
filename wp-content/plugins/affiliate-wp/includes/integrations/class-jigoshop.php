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

		// Jigoshop eCommerce actions
		add_action( 'jigoshop\order\after\jigoshop-pending', array( $this, 'add_pending_referral_new' ), 10, 1 );

		add_action( 'jigoshop\order\after\jigoshop-processing', array( $this, 'mark_referral_complete_new' ), 10 );
		add_action( 'jigoshop\order\after\jigoshop-completed', array( $this, 'mark_referral_complete_new' ), 10 );

		add_action( 'jigoshop\order\after\jigoshop-refunded', array( $this, 'revoke_referral' ), 10 );
		add_action( 'jigoshop\order\after\jigoshop-cancelled', array( $this, 'revoke_referral' ), 10 );

		add_action( 'jigoshop\service\coupon\save',  array( $this, 'store_discount_affiliate_new' ), 1, 1 );
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

			$this->email = $this->order->billing_email;
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

			$this->order->add_order_note( sprintf( __( 'Referral #%1$d for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp' ),
				$referral->referral_id,
				$amount,
				$name,
				$referral->affiliate_id
			) );

		}

	}

	/**
	 * Store a pending referral when a new order is created in Jigoshop eCommerce
	 *
	 * @access  public
	 * @since   2.1.17
	*/
	public function add_pending_referral_new( $order_object ) {

		$this->order = apply_filters( 'affwp_get_jigoshop_order', $order_object );

		// Check if an affiliate coupon was used
		$coupon_affiliate_id = $this->get_coupon_affiliate_id_new();

		if ( $this->was_referred() || $coupon_affiliate_id ) {

			$order_id = $this->order->getId();

			// get affiliate ID
			$affiliate_id = $this->get_affiliate_id( $order_id );

			if ( false !== $coupon_affiliate_id ) {
				$affiliate_id = $coupon_affiliate_id;
			}

			if ( $this->is_affiliate_email( $this->order->getCustomer()->getBillingAddress()->getEmail() ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return false;
			}

			// Check for an existing referral
			$existing = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

			// If an existing referral exists and it is paid or unpaid exit.
			if ( $existing && ( 'paid' == $existing->status || 'unpaid' == $existing->status ) ) {
				return false; // Referral already created for this reference
			}

			$order_total = $this->order->getTotal();

			if ( affiliate_wp()->settings->get( 'exclude_tax' ) ) {

				$order_total -= $this->get_total_tax();

			}

			if ( affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

				$order_total -= $this->order->getShippingPrice();

			}

			$amount = $this->calculate_referral_amount( $order_total, $order_id, '', $affiliate_id );

			if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

				$this->log( 'Referral not created due to 0.00 amount.' );

				return false; // Ignore a zero amount referral
			}

			$description = $this->get_referral_description();

			$visit_id    = affiliate_wp()->tracking->get_visit_id();

			if ( $existing ) {

				// Update the previously created referral
				affiliate_wp()->referrals->update_referral( $existing->referral_id, array(
					'amount'       => $amount,
					'reference'    => $order_id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'context'      => $this->context
				) );

				$this->log( sprintf( 'Jigoshop Referral #%d updated successfully.', $existing->referral_id ) );

			} else {

				// Create a new referral
				$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_insert_pending_referral', array(
					'amount'       => $amount,
					'reference'    => $order_id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'context'      => $this->context
				), $amount, $order_id, $description, $affiliate_id, $visit_id, array(), $this->context ) );

				if ( $referral_id ) {

					$this->log( sprintf( 'Referral #%d created successfully.', $referral_id ) );

					$amount         = affwp_currency_filter( affwp_format_amount( $amount ) );
					$name           = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
					$referral_link  = affwp_admin_link( 'referrals', esc_html( '#' . $referral_id ), array( 'action' => 'edit_referral', 'referral_id' => $referral_id ) );

					$orderService = \Jigoshop\Integration::getOrderService();

					/* translators: 1: Referral link, 2: Amount, 3: Affiliate Name */
					$orderService->addNote( $this->order, sprintf( __( 'Referral %1$s for %2$s recorded for %3$s', 'affiliate-wp' ),
						$referral_link,
						$amount,
						$name
					) );

				} else {

					$this->log( 'Referral failed to be created.' );

				}
			}
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
	 * Marks a referral as complete when payment is completed.
	 *
	 * @since 2.1.17
	 * @access public
	 */
	public function mark_referral_complete_new( $order_object ) {

		$order_id = $order_object->getId();

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
	 * Revoke the referral when the order is refunded
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function revoke_referral( $order_object ) {

		$order_id = $order_object->getId();

		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
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

		$user_name    = '';
		$user_id      = '';
		$affiliate_id = get_post_meta( $post->ID, 'affwp_discount_affiliate', true );

		if ( $affiliate_id ) {
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
			$user         = get_userdata( $user_id );
			$user_name    = $user ? $user->user_login : '';
		}

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
	 * Stores the affiliate ID in the discounts meta if it is an affiliate's discount for Jigoshop eCommerce
	 *
	 * @access  public
	 * @since   2.1.17
	*/
	public function store_discount_affiliate_new( $coupon_object ) {

		$coupon_id = $coupon_object->getId();

		if ( empty( $_POST['user_name'] ) ) {
			delete_post_meta( $coupon_id, 'affwp_discount_affiliate' );
			return;
		}

		if ( empty( $_POST['user_id'] ) && empty( $_POST['user_name'] ) ) {
			return;
		}

		$data = affiliate_wp()->utils->process_request_data( $_POST, 'user_name' );

		$affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

		update_post_meta( $coupon_id, 'affwp_discount_affiliate', $affiliate_id );

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

	/**
	 * Retrieve the affiliate ID for the coupon used, if any in Jigoshop eCommerce
	 *
	 * @access  public
	 * @since   2.1.17
	*/
	private function get_coupon_affiliate_id_new() {

		$coupons = $this->order->getDiscounts();

		if ( empty( $coupons ) ) {
			return false;
		}

		foreach( $coupons as $coupon ) {

			$coupon_meta = json_decode( $coupon->getMeta( 'coupon_data' )->getValue() );

			$coupon_id = $coupon_meta->id;

			$affiliate_id = get_post_meta( $coupon_id, 'affwp_discount_affiliate', true );

			if( $affiliate_id ) {

				if( ! affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id ) ) {
					continue;
				}

				return $affiliate_id;

			}

		}

		return false;
	}

	/**
	 * Retrieves the total tax for the order in Jigoshop eCommerce
	 *
	 * @access  public
	 * @since   2.1.17
	*/
	private function get_total_tax() {

		$taxes = $this->order->getCombinedTax();

		$tax = 0;

		if ( empty( $taxes ) ) {
			return $tax;
		}

		foreach( $taxes as $name => $amount ) {
			$tax += $amount;
		}

		return $tax;
	}

	/**
	 * Retrieves the referral description in Jigoshop eCommerce
	 *
	 * @access  public
	 * @since   2.1.17
	*/
	public function get_referral_description() {

		$items       = $this->order->getItems();
		$description = array();

		foreach ( $items as $item ) {

			$description[] = $item->getName();

		}

		$description = implode( ', ', $description );

		return $description;
	}
}

if ( function_exists( 'jigoshop_init') || class_exists( 'JigoshopInit' ) ) {
	new Affiliate_WP_Jigoshop;
}
