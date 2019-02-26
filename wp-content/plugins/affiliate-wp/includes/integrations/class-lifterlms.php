<?php
/**
 * LifterLMS integration.
 *
 * @since 1.8.3
 *
 * @see Affiliate_WP_Base
 */
class Affiliate_WP_LifterLMS extends Affiliate_WP_Base {

	/**
	 * Current order.
	 *
	 * @access private
	 * @since  1.8.3
	 * @var stdClass
	 */
	private $order;

	/**
	 * The context for referrals.
	 *
	 * @access public
	 * @since  1.8.3
	 */
	public $context = 'lifterlms';

	/**
	 * Sets up actions and filters.
	 *
	 * @access public
	 * @since  1.8.3
	*/
	public function init() {

		if ( function_exists( 'LLMS' ) ) {

			if ( version_compare( LLMS()->version, '3.0.0', '>=' ) ) { // 3.x

				// Create a pending referral when a new order is pending.
				add_action( 'lifterlms_new_pending_order', array( $this, 'create_pending_referral_300' ), 10, 1 );

				// Complete the pending referral on successes.
				add_action( 'lifterlms_order_status_completed', array( $this, 'complete_pending_referral' ), 10, 1 );
				add_action( 'lifterlms_order_status_active', array( $this, 'complete_pending_referral' ), 10, 1 );

				// Revoke the referral on these statuses.
				add_action( 'lifterlms_order_status_refunded', array( $this, 'revoke_referral' ), 10, 1 );
				add_action( 'lifterlms_order_status_cancelled', array( $this, 'revoke_referral' ), 10, 1 );
				add_action( 'lifterlms_order_status_expired', array( $this, 'revoke_referral' ), 10, 1 );
				add_action( 'lifterlms_order_status_trash', array( $this, 'revoke_referral' ), 10, 1 );

				// Add affiliate product fields to LifterLMS courses and memberships.
				add_filter( 'llms_metabox_fields_lifterlms_course_options', array( $this, 'product_meta_output' ), 77, 1 );
				add_filter( 'llms_metabox_fields_lifterlms_membership', array( $this, 'product_meta_output' ), 77, 1 );

				// Add affiliate coupon fields.
				add_filter( 'llms_metabox_fields_lifterlms_coupon', array( $this, 'coupon_meta_output' ), 10, 1 ); // 3.x

			} else { // 2.x

				/*
				 * Create a pending referral, and then mark it complete immediately after,
				 * because there's no 'pending' order status in LifterLMS.
				 */
				add_action( 'lifterlms_order_complete', array( $this, 'create_pending_referral' ), 10, 1 );

				// Add affiliate product fields to LifterLMS courses and memberships.
				add_filter( 'llms_meta_fields_course_main', array( $this, 'product_meta_output' ), 77, 1 );
				add_filter( 'llms_meta_fields_llms_membership_settings', array( $this, 'product_meta_output' ), 77, 1 );

				// Add affiliate coupon fields.
				add_filter( 'llms_meta_fields_coupon', array( $this, 'coupon_meta_output' ), 10, 1 ); // 2.x

			}

			// Add link to the LifterLMS order on the referral screen.
			add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

			// save affiliate product fields to post meta.
			add_action( 'lifterlms_process_course_meta', array( $this, 'product_meta_save' ), 10, 2 );
			add_action( 'lifterlms_process_llms_membership_meta', array( $this, 'product_meta_save' ), 10, 2 );

			// Save affiliate coupon fields to post meta.
			add_action( 'lifterlms_process_llms_coupon_meta', array( $this, 'coupon_meta_save' ), 10, 2 );

			// add some data to the llms order screen
			add_action( 'lifterlms_after_order_meta_box', array( $this, 'order_meta_output' ) );

		}
	}

	/**
	 * Marks a pending referral as complete upon LifterLMS order success.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param stdClass $order LLMS_Order instance.
	 */
	public function complete_pending_referral( $order ) {

		if ( ! $order instanceof LLMS_Order ) {
			return;
		}

		$this->complete_referral( $order->get( 'id' ) );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $order->get( 'id' ), $this->context );

		if ( ! $referral ) {
			return;
		}

		$note = sprintf( __( 'Referral #%d completed', 'affiliate-wp' ), $referral->referral_id );

		$order->add_note( $note );

		$this->log( $note );

	}

	/**
	 * Creates a pending referral (and marks it complete).
	 *
	 * LifterLMS doesn't have a 'pending' order status.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param int $order_id WP Post ID of the LifterLMS Order.
	 */
	public function create_pending_referral( $order_id ) {

		$order = $this->get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		// if this was a referral or we have a coupon and a coupon affiliate id.
		if ( $this->was_referred() || ( $order->coupon_id && $order->coupon_affiliate_id ) ) {

			/*
			 * If WooCommerce is being use as the LLMS payment method for the order skip referrals
			 * for the order because WooCommerce methods will handle the affiliate stuff.
			 */
			if ( 'woocommerce' === $order->payment_type ) {

				$this->log( __( 'Referral not created because WooCommerce was used for payment.', 'affiliate-wp' ) );

				return;
			}

			// If referrals are disabled for the LLMS product, don't create a referral.
			if ( get_post_meta( $order->product_id, '_affwp_disable_referrals', true ) ) {
				return;
			}

			// Check for an existing referral.
			$existing = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

			// If an existing referral exists and it is paid or unpaid exit.
			if ( $existing && ( 'paid' === $existing->status || 'unpaid' === $existing->status ) ) {
				return;
			}

			// Get the referring affiliate's affiliate id.
			$affiliate_id = $this->get_affiliate_id( $order_id );

			// Use the coupon affiliate if there is one.
			if ( false !== $order->coupon_affiliate_id ) {
				$affiliate_id = $order->coupon_affiliate_id;
			}

			// Customers cannot refer themselves.
			if ( $this->is_affiliate_email( $order->user_data->user_email, $affiliate_id ) ) {

				$this->log( __( 'Referral not created because affiliate\'s own account was used.', 'affiliate-wp' ) );

				return;
			}

			$amount = $this->calculate_referral_amount( $order->total, $order->id, $order->product_id, $affiliate_id );

			// Ignore a zero amount referral.
			if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

				$this->log( __( 'Referral not created due to 0.00 amount.', 'affiliate-wp' ) );

				return;
			}


			$description = apply_filters( 'affwp_llms_get_referral_description', $order->product_title, $order, $affiliate_id );
			$visit_id    = affiliate_wp()->tracking->get_visit_id();

			/*
			 * Update existing referral if it exists.
			 *
			 * This isn't currently ever going to happen with LifterLMS but leaving it here for future use.
			 */
			if ( $existing ) {

				// Update the previously created referral.
				affiliate_wp()->referrals->update_referral( $existing->referral_id, array(
					'amount'       => $amount,
					'reference'    => $order->id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'products'     => $this->get_products( $order->id ),
					'context'      => $this->context
				) );

				/*
				 * Complete the referral automatically because we don't have a pending status
				 * will update in the future when / if the status becomes available
				 */
				$this->complete_referral( $order->id );

				$this->log( sprintf( __( 'LifterLMS Referral #%d updated successfully.', 'affiliate-wp' ), $existing->referral_id ) );

			} else { // No referral exists, so create a new one.

				// Create a new referral.
				$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_insert_pending_referral', array(
					'amount'       => $amount,
					'reference'    => $order->id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'products'     => $this->get_products( $order->id ),
					'context'      => $this->context
				), $amount, $order_id, $description, $affiliate_id, $visit_id, array(), $this->context ) ); // what's this array for?

				if ( $referral_id ) {

					/*
					 * Complete referral automatically because we don't have pending status
					 * will update in the future when / if the status becomes available
					 */
					$this->complete_referral( $order->id );

					$this->log( sprintf( __( 'Referral #%d created successfully.', 'affiliate-wp' ), $referral_id ) );

				} else {

					$this->log( __( 'Referral failed to be created.', 'affiliate-wp' ) );

				}
			}
		}
	}


	/**
	 * Creates a pending referral.
	 *
	 * Compatible with LifterLMS 3.x+.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param stdClass $order LLMS_Order instance.
	 */
	public function create_pending_referral_300( $order ) {

		if ( ! $order instanceof LLMS_Order ) {
			return;
		}

		$order_id = $order->get( 'id' );
		$coupon_affiliate_id = ( $order->has_coupon() ) ? $this->get_order_coupon_affiliate_id( $order->get( 'coupon_id' ) ) : false;

		// If this was a referral or we have a coupon and a coupon affiliate id.
		if ( $this->was_referred() || $coupon_affiliate_id ) {

			// If referrals are disabled for the LLMS product, don't create a referral.
			if ( get_post_meta( $order->get( 'product_id' ), '_affwp_disable_referrals', true ) ) {
				return;
			}

			// Check for an existing referral.
			$existing = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

			// If an existing referral exists and it is paid or unpaid exit.
			if ( $existing && ( 'paid' === $existing->status || 'unpaid' === $existing->status ) ) {
				return;
			}

			// Get the referring affiliate's affiliate id.
			$affiliate_id = $this->get_affiliate_id( $order_id );

			// Use our coupon affiliate if we have one.
			if ( false !== $coupon_affiliate_id ) {
				$affiliate_id = $coupon_affiliate_id;
			}

			// Customers cannot refer themselves.
			if ( $this->is_affiliate_email( $order->get( 'billing_email', $affiliate_id ) ) ) {

				$this->log( __( 'Referral not created because affiliate\'s own account was used.', 'affiliate-wp' ) );

				return;
			}

			$amount = $this->calculate_referral_amount( $order->get( 'total' ), $order_id, $order->get( 'product_id' ), $affiliate_id );

			// Ignore a zero amount referral.
			if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

				$this->log( __( 'Referral not created due to 0.00 amount.', 'affiliate-wp' ) );

				return;
			}

			$description = apply_filters( 'affwp_llms_get_referral_description', $order->get( 'product_title' ), $order, $affiliate_id );
			$visit_id    = affiliate_wp()->tracking->get_visit_id();

			// Update existing referral if it exists
			if ( $existing ) {

				// Update the previously created referral.
				affiliate_wp()->referrals->update_referral( $existing->referral_id, array(
					'amount'       => $amount,
					'reference'    => $order_id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'products'     => $this->get_products( $order ),
					'context'      => $this->context
				) );

				$note = sprintf( __( 'Referral #%d updated successfully.', 'affiliate-wp' ), $existing->referral_id );;

				$order->add_note( $note );

				$this->log( $note );

			} else { // No referral exists, so create a new one.

				// Create a new referral.
				$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_insert_pending_referral', array(
					'amount'       => $amount,
					'reference'    => $order_id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'products'     => $this->get_products( $order->id ),
					'context'      => $this->context
				), $amount, $order_id, $description, $affiliate_id, $visit_id, array(), $this->context ) ); // what's this array for?

				if ( $referral_id ) {

					$note = sprintf( __( 'Pending referral #%d created successfully.', 'affiliate-wp' ), $referral_id );;

					$order->add_note( $note );

					$this->log( $note );

				} else {

					$this->log( __( 'LifterLMS Referral failed to be created.', 'affiliate-wp' ) );

				}
			}
		}

	}


	/**
	 * Adds an AffiliateWP Tab to LifterLMS Coupon Admin screen.
	 *
	 * Allow users to associate a coupon with a specific affiliate.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param array $fields An associate array of LifterLMS settings.
	 * @return array Coupon meta data.
	 */
	public function coupon_meta_output( $fields ) {

		global $post;

		add_filter( 'affwp_is_admin_page', '__return_true' );
		affwp_admin_scripts();


		$user_id      = 0;
		$user_name    = '';
		$affiliate_id = get_post_meta( $post->ID, '_affwp_affiliate_id', true );
		if( $affiliate_id ) {
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
			$user         = get_userdata( $user_id );
			$user_name    = $user ? $user->user_login : '';
		}

		$html = '
			<span class="affwp-ajax-search-wrap">
				<span class="affwp-llms-coupon-input-wrap">
					<input type="text" name="_affwp_affiliate_user_name" id="user_name" value="' . esc_attr( $user_name ) . '" class="affwp-user-search input-full" data-affwp-status="active" autocomplete="off" />
					<img class="affwp-ajax waiting" src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" style="display: none;"/>
				</span>
				<span id="affwp_user_search_results"></span>
			</span>
			<em>' . __( 'Search for an affiliate by username or email.', 'affiliate-wp' ) . '</em>
		';

		$fields[] = array(
			'title' => 'AffiliateWP',
			'fields' => array(
				array(
					'type'	 	 => 'custom-html',
					'label'		 => __( 'Affiliate Discount', 'affiliate-wp' ),
					'desc'		 => __( 'Connect this coupon with an affiliate.', 'affiliate-wp' ),
					'id'		 => '_affwp_affiliate_user_id',
					'value' 	 => $html,
					'desc_class' => 'd-all',
				),
			),
		);

		return apply_filters( 'affwp_llms_meta_fields_coupon' , $fields );

	}


	/**
	 * Saves the related coupon fields during coupon post type save actions.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param int     $post_id Coupon post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function coupon_meta_save( $post_id, $post ) {

		// remove the affiliate id if the username is cleared
		if ( empty( $_POST['_affwp_affiliate_user_name'] ) ) {

			delete_post_meta( $post_id, '_affwp_affiliate_id' );
			return;
		}

		/*
		 * We need either a username, or a user ID to locate the affiliate.
		 * so don't continue without at least one of them (i guess)
		 */
		if ( empty( $_POST['_affwp_affiliate_user_id'] ) && empty( $_POST['_affwp_affiliate_user_name'] ) ) {
			return;
		}

		$data = affiliate_wp()->utils->process_request_data( $_POST, '_affwp_affiliate_user_name' );

		/*
		 * Locate an affiliate, looks like this returns null if the
		 * user is not a valid affiliate.
		 */
		$affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

		// $affiliate_id is null if none found so update regardless of the value
		update_post_meta( $post_id, '_affwp_affiliate_id', $affiliate_id );

		/**
		 * Fires when processing LifterLMS coupon meta within the LifterLMS integration.
		 *
		 * @param int      $post_id The post ID.
		 * @param stdClass $post    The post object.
		 */
		do_action( 'affwp_lifterlms_process_llms_coupon_meta', $post_id, $post );

	}



	/**
	 * Retrieves order details for an order by ID.
	 *
	 * @access private
	 * @since  1.8.3
	 *
	 * @param int  $order_id LifterLMS Order ID.
	 * @param bool $force    Whether to force skipping the cached data.
	 * @return mixed Object of order-related data, or false if no order is found.
	 */
	private function get_order( $order_id, $force = false ) {

		// Only perform lookups once, unless forced.
		if ( ! $this->order || $force ) {

			$post = get_post( $order_id );

			if( ! $post ) {

				return false;

			}

			$order = new stdClass();

			$order->id = absint( $order_id );

			// WP Post
			$order->post = $post;

			// payment
			$order->payment_type = get_post_meta( $order->id, '_llms_payment_type', true );
			$order->total = get_post_meta( $order->id, '_llms_order_total', true );

			// Coupon post meta.
			$order->coupon_id = get_post_meta( $order->id , '_llms_order_coupon_id', true );
			// Affiliate ID for the coupon.
			$order->coupon_affiliate_id = ( $order->coupon_id ) ? $this->get_order_coupon_affiliate_id( $order->coupon_id ) : false;

			// user related
			$order->user_id = get_post_meta( $order->id , '_llms_user_id', true );
			$order->user_data = get_userdata( $order->user_id );

			// product related
			$order->product_id = get_post_meta( $order->id , '_llms_order_product_id', true );
			$order->product_title = get_post_meta( $order->id, '_llms_order_product_title', true );

			// "cache"
			$this->order = $order;

		}

		return $this->order;

	}


	/**
	 * Retrieves the affiliate ID associated with a LifterLMS Coupon.
	 *
	 * @access private
	 * @since  1.8.3
	 *
	 * @param int $coupon_id LifterLMS Coupon ID.
	 * @return mixed|int|bool The affiliate id, or false if no affiliate is found.
	 */
	private function get_order_coupon_affiliate_id( $coupon_id ) {

		$affiliate_id = get_post_meta( $coupon_id, '_affwp_affiliate_id', true );

		if ( $affiliate_id && affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id ) ) {
			return $affiliate_id;
		}

		return false;
	}


	/**
	 * Retrieves an array of product information to pass to AffiliateWP when creating a referral.
	 *
	 * LifterLMS doesn't have the ability to purchase multiple products simultaneously,
	 * but this is still returning an array of arrays, in case it's needed in the future.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param int|LLMS_Order $order_id LifterLMS Order ID or LLMS_Order object.
	 * @return array Products.
	 */
	public function get_products( $order_id = 0 ) {

		// 2.x
		if ( is_numeric( $order_id ) ) {

			$order = $this->get_order( $order_id );

			if ( $order ) {

				return array( array(
					'name'            => $order->product_title,
					'id'              => $order->product_id,
					'price'           => $order->total,
					'referral_amount' => $this->calculate_referral_amount( $order->total, $order->id, $order->product_id )
				) );

			} else {

				return array( array(
					'id' => $order_id,
				) );
			}

		}
		// 3.x
		elseif ( $order_id instanceof LLMS_Order ) {

			return array( array(
				'name'            => $order_id->get( 'product_title' ),
				'id'              => $order_id->get( 'product_id' ),
				'price'           => $order_id->get( 'total' ),
				'referral_amount' => $this->calculate_referral_amount( $order_id->get( 'total' ), $order_id->get( 'id' ), $order_id->get( 'product_id' ) )
			) );

		} else {

			return array();

		}

	}

	/**
	 * Outputs some AffiliateWP data on the LifterLMS Order post edit screen.
	 *
	 * @access public
	 * @since  1.8.3
	 */
	public function order_meta_output( ) {

		global $post;

		$referral = affiliate_wp()->referrals->get_by( 'reference', $post->ID, $this->context );

		if ( ! $referral ) {
			return;
		}

		$affiliate_name = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$referral_amount = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$referral_status = affwp_get_referral_status_label( $referral->referral_id );

		// 3.x
		if ( version_compare( LLMS()->version, '3.0.0', '>=' ) ) {
			?>
			<div class="llms-metabox-section d-all">

				<h4><?php _e( 'Referral Details', 'affiliate-wp' ); ?></h4>

				<div class="llms-metabox-field d-1of4">
					<label><?php _e( 'Amount', 'affiliate-wp' ); ?></label>
					<?php echo $referral_amount; ?>  (<?php echo $referral_status; ?>)
				</div>

				<div class="llms-metabox-field d-1of4">
					<label><?php _e( 'Affiliate', 'affiliate-wp' ); ?></label>
					<?php echo affwp_admin_link( 'referrals', $affiliate_name, array( 'affiliate_id' => $referral->affiliate_id ) ); ?>
				</div>

				<div class="llms-metabox-field d-1of4">
					<label><?php _e( 'Reference', 'affiliate-wp' ); ?></label>
					<?php echo affwp_admin_link( 'referrals', $referral->referral_id, array('action' => 'edit_referral', 'referral_id' => $referral->referral_id ) ); ?>
				</div>

			</div>
			<?php
		}
		// 2.x
		else {
			?>
			<table class="form-table">
			<tbody>
				<tr>
					<th><label><?php _e( 'Referral Details', 'affiliate-wp' ); ?></label></th>
					<td>
						<table class="form-table">
							<tr>
								<td><label><?php _e( 'Amount', 'affiliate-wp' ); ?></label></td>
								<td><?php echo $referral_amount; ?>  (<?php echo $referral_status; ?>)</td>
							</tr>
							<tr>
								<td><label><?php _e( 'Affiliate', 'affiliate-wp' ); ?></label></td>
								<td><?php echo affwp_admin_link( 'referrals', $affiliate_name, array( 'affiliate_id' => $referral->affiliate_id ) ); ?></td>
							</tr>
							<tr>
								<td><label><?php _e( 'Reference', 'affiliate-wp' ); ?></label></td>
								<td><?php echo affwp_admin_link( 'referrals', $referral->referral_id, array( 'action' => 'edit_referral', 'referral_id' => $referral->referral_id ) ); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
			</table>
			<?php
		}

	}

	/**
	 * Adds an AffiliateWP Tab to LifterLMS Course & Membership Admin screen.
	 *
	 * Allow users to disable referrals for the product. Allow users to define
	 * custom referral rates for the product
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param array $fields Associative array of LifterLMS settings.
	 * @return array Product meta fields.
	 */
	public function product_meta_output( $fields ) {

		add_filter( 'affwp_is_admin_page', '__return_true' );

		// Inject inline LifterLMS javascript.
		add_action( 'admin_print_footer_scripts', array( $this, 'inline_js' ) );

		global $post;

		$product_type = str_replace( 'llms_', '', $post->post_type );

		$fields[] = array(

			'title' => 'AffiliateWP',
			'fields' => array(
				array(
					'type'		 => 'checkbox',
					'label'		 => __( 'Disable Referrals', 'affiliate-wp' ),
					'desc' 		 => sprintf( __( 'Check this box to prevent orders for this %s from generating referral commissions for affiliates.', 'affiliate-wp' ), $product_type ),
					'desc_class' => 'd-3of4 t-3of4 m-1of2',
					'id' 		 => '_affwp_disable_referrals',
					'value' 	 => '1',
					'group'      => '_affwp_enable_referral_overrides-hide'
				),
				array(
					'type'		 => 'checkbox',
					'label'		 => sprintf( __( 'Enable %s Referral Rate', 'affiliate-wp' ), ucfirst( $product_type ) ),
					'desc' 		 => sprintf( __( 'Check this box to enable %s referral rate overrides', 'affiliate-wp' ), $product_type ),
					'desc_class' => 'd-3of4 t-3of4 m-1of2',
					'id' 		 => '_affwp_enable_referral_overrides',
					'value' 	 => '1',
					'group'      => 'llms-affwp-disable-fields',
				),
				array(
					'type'		 => 'number',
					'label'		 => sprintf( __( '%s Referral Rate', 'affiliate-wp' ), ucfirst( $product_type ) ),
					'desc' 		 => sprintf( __( 'Enter a referral rate for this %s', 'affiliate-wp' ), $product_type ),
					'id' 		 => '_affwp_' . $this->context . '_product_rate',
					'class'  	 => 'input-full',
					'desc_class' => 'd-all',
					'group'      => '_affwp_enable_referral_overrides-show',
				),
				// JS uses this to only bind on llms pages
				array(
					'type' => 'custom-html',
					'id' => 'affwp_llms_enabled',
					'label' => '',
					'value' => '<div id="affwp-llms-enabled"></div>',
				),

			),

		);

		return apply_filters( 'affwp_llms_meta_fields_product', $fields );

	}

	/**
	 * Saves the related product fields during course & membership post type save actions.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param int     $post_id Coupon ID.
	 * @param WP_Post $post    Post object.
	 */
	public function product_meta_save( $post_id, $post ) {

		$overrides = '';
		$disable = '';
		$rate = '';

		// if disable is set, clear everything else and update disable postmeta
		if ( isset( $_POST['_affwp_disable_referrals'] ) ) {

			$disable = 1;

		}

		// If overrides are set, update the override-related fields.
		elseif ( isset( $_POST['_affwp_enable_referral_overrides'] ) ) {

			$overrides = 1;
			$rate = $_POST['_affwp_' . $this->context . '_product_rate'];

		}

		// Update post meta
		update_post_meta( $post_id, '_affwp_enable_referral_overrides', $overrides );
		update_post_meta( $post_id, '_affwp_disable_referrals', $disable );
		update_post_meta( $post_id, '_affwp_' . $this->context . '_product_rate', $rate );

	}

	/**
	 * Links the Reference column on the AffWp screen to a LifterLMS Order.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param int             $reference Optional. LifterLMS order ID to user for the referencel ink.
	 * @param \AffWP\Referral $referral  Referral object.
	 * @return string Reference link HTML markup.
	*/
	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'lifterlms' != $referral->context ) {

			return $reference;

		}

		return '<a href="' . esc_url( get_edit_post_link( $reference ) ) . '">' . $reference . '</a>';

	}

	/**
	 * Revokes a referral on various orde status changes
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @param LLMS_Order $order LifterLMS order object.
	 */
	public function revoke_referral( $order ) {

		if ( ! $order instanceof LLMS_Order ) {
			return;
		}

		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $order->get( 'id' ) );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $order->get( 'id' ), $this->context );

		if ( ! $referral ) {
			return;
		}

		$note = sprintf( __( 'Referral #%d rejected', 'affiliate-wp' ), $referral->referral_id );
		$order->add_note( $note );

		$this->log( $note );

	}


	/**
	 * Provides static js for the LifterLMS AffiliateWP integration.
	 *
	 * @access public
	 * @since  1.8.3
	 *
	 * @return string Static JavaScript, specific to the LifterLMS integration.
	 */
	public function inline_js() { ?>
		<script>
		( function( $ ) {

			window.llms = window.llms || {};

			/**
			 * Handle the AffiliateWP Tab JS interaction
			 * @return obj
			 */
			window.llms.metabox_product_affwp = function() {

				/**
				 * Initialize and Bind events if our check element is found
				 * @return void
				 */
				this.init = function() {

					// only bind if our hidden input exists in the dom
					if ( $( '#affwp-llms-enabled' ).length ) {

						this.bind();

					}

				};

				/**
				 * Bind dom events
				 * @return void
				 */
				this.bind = function() {

					this.bind_disable_field();
					$( '#_affwp_disable_referrals' ).trigger( 'change' );

					this.bind_override_field();
					$( '#_affwp_enable_referral_overrides' ).trigger( 'change' );

				};

				/**
				 * Bind thie "disable referrals" fields
				 * @return void
				 */
				this.bind_disable_field = function() {

					$( '#_affwp_disable_referrals' ).on( 'change', function() {

						var $group = $( '.llms-affwp-disable-fields');

						if ( $(this).is( ':checked' ) ) {

							$group.hide( 200 );
							$( '#_affwp_enable_referral_overrides' ).removeAttr( 'checked' ).trigger( 'change' );

						} else {

							$group.show( 200 );

						}

					} );

				};

				/**
				 * Bind the "enable overrides" field
				 * @return void
				 */
				this.bind_override_field = function() {

					$( '#_affwp_enable_referral_overrides' ).on( 'change', function() {

						var $show = $( '._affwp_enable_referral_overrides-show'),
							$hide = $( '._affwp_enable_referral_overrides-hide');

						if ( $(this).is( ':checked' ) ) {

							$show.show( 200 );
							$hide.hide( 200 );
							$( '#_affwp_disable_referrals' ).removeAttr( 'checked' ).trigger( 'change' ).hide( 200 );

						} else {

							$show.hide( 200 );
							$hide.show( 200 );

						}

					} );

				};

				// go
				this.init();

				// return, just bc
				return this;

			};

			// instatiate the class
			var a = new window.llms.metabox_product_affwp();

		} )( jQuery );
		</script>
	<?php }

}

if ( function_exists( 'LLMS' ) ) {
	new Affiliate_WP_LifterLMS;
}
