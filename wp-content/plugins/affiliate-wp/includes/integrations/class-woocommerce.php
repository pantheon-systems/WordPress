<?php

class Affiliate_WP_WooCommerce extends Affiliate_WP_Base {

	/**
	 * The order object
	 *
	 * @access  private
	 * @since   1.1
	*/
	private $order;

	/**
	 * Setup actions and filters
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'woocommerce';

		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_pending_referral' ), 10 );

		// Add an order note if a contained referral is updated.
		add_action( 'affwp_updated_referral', array( $this, 'updated_referral_note' ), 10, 3 );

		// There should be an option to choose which of these is used.
		add_action( 'woocommerce_order_status_completed', array( $this, 'mark_referral_complete' ), 10 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'mark_referral_complete' ), 10 );
		add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'revoke_referral_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'revoke_referral_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'revoke_referral_on_refund' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'revoke_referral' ), 10 );
		add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'revoke_referral' ), 10 );
		add_action( 'woocommerce_order_status_pending_to_cancelled', array( $this, 'revoke_referral' ), 10 );
		add_action( 'woocommerce_order_status_processing_to_failed', array( $this, 'revoke_referral' ), 10 );
		add_action( 'woocommerce_order_status_completed_to_failed', array( $this, 'revoke_referral' ), 10 );
		add_action( 'woocommerce_order_status_pending_to_failed', array( $this, 'revoke_referral' ), 10 );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'revoke_referral' ), 10 );
		add_action( 'wc-on-hold_to_trash', array( $this, 'revoke_referral' ), 10 );
		add_action( 'wc-processing_to_trash', array( $this, 'revoke_referral' ), 10 );
		add_action( 'wc-completed_to_trash', array( $this, 'revoke_referral' ), 10 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

		add_action( 'woocommerce_coupon_options', array( $this, 'coupon_option' ) );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'store_discount_affiliate' ) );

		// Per product referral rates.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_settings' ) );
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variation_settings' ), 100, 3 );
		add_action( 'save_post', array( $this, 'save_meta' ) );
		add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'save_variation_data' ) );

		add_action( 'affwp_pre_flush_rewrites', array( $this, 'skip_generate_rewrites' ) );

		// Shop page.
		add_action( 'pre_get_posts', array( $this, 'force_shop_page_for_referrals' ), 5 );
		add_action( 'init', array( $this, 'wc_300__product_base_rewrites' ) );

		// Affiliate Area link in My Account menu.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'my_account_affiliate_area_link' ), 100 );
		add_filter( 'woocommerce_get_endpoint_url',   array( $this, 'my_account_endpoint_url' ), 100, 2 );
		add_filter( 'woocommerce_get_settings_account', array( $this, 'account_settings' ) );

		// Per-category referral rates.
		add_action( 'product_cat_add_form_fields', array( $this, 'add_product_category_rate' ), 10, 2 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_product_category_rate' ), 10 );
		add_action( 'edited_product_cat', array( $this, 'save_product_category_rate' ) );
		add_action( 'create_product_cat', array( $this, 'save_product_category_rate' ) );

		// Filter Orders list table to add a referral column.
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_orders_column' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'render_orders_referral_column' ), 10, 2 );

		// Per product referral rate types
		add_filter( 'affwp_calc_referral_amount', array( $this, 'calculate_referral_amount_type' ), 10, 5 );

		// Filter Order preview to display referral
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( $this, 'order_preview_get_referral' ), 10, 2 );
		add_action( 'woocommerce_admin_order_preview_end', array( $this, 'render_order_preview_referral' ) );

	}

	/**
	 * Store a pending referral when a new order is created
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add_pending_referral( $order_id = 0 ) {

		$this->order = apply_filters( 'affwp_get_woocommerce_order', new WC_Order( $order_id ) );

		// Check if an affiliate coupon was used
		$coupon_affiliate_id = $this->get_coupon_affiliate_id();

		if ( $this->was_referred() || $coupon_affiliate_id ) {

			// get affiliate ID
			$affiliate_id = $this->get_affiliate_id( $order_id );

			if ( false !== $coupon_affiliate_id ) {
				$affiliate_id = $coupon_affiliate_id;
			}

			if ( true === version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$this->email = $this->order->get_billing_email();
			} else {
				$this->email = $this->order->billing_email;
			}

			// Customers cannot refer themselves
			if ( $this->is_affiliate_email( $this->email, $affiliate_id ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return false;
			}

			// Check for an existing referral
			$existing = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

			// If an existing referral exists and it is paid or unpaid exit.
			if ( $existing && ( 'paid' == $existing->status || 'unpaid' == $existing->status ) ) {
				return false; // Completed Referral already created for this reference
			}

			$cart_shipping = $this->order->get_total_shipping();

			if ( ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$cart_shipping += $this->order->get_shipping_tax();
			}

			$items = $this->order->get_items();

			// Calculate the referral amount based on product prices
			$amount = 0.00;

			foreach ( $items as $product ) {

				if ( get_post_meta( $product['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
					continue; // Referrals are disabled on this product
				}

				if( ! empty( $product['variation_id'] ) && get_post_meta( $product['variation_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
					continue; // Referrals are disabled on this variation
				}

				// Get the categories associated with the download.
				$categories = get_the_terms( $product['product_id'], 'product_cat' );

				// Get the first category ID for the product.
				$category_id = $categories && ! is_wp_error( $categories ) ? $categories[0]->term_id : 0;

				// The order discount has to be divided across the items
				$product_total = $product['line_total'];
				$shipping      = 0;

				if ( $cart_shipping > 0 && ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {
					$shipping       = $cart_shipping / count( $items );
					$product_total += $shipping;
				}

				if ( ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {
					$product_total += $product['line_tax'];
				}

				if ( $product_total <= 0 && 'flat' !== affwp_get_affiliate_rate_type( $affiliate_id ) ) {
					continue;
				}

				$product_id_for_rate = $product['product_id'];
				if( ! empty( $product['variation_id'] ) && $this->get_product_rate( $product['variation_id'] ) ) {
					$product_id_for_rate = $product['variation_id'];
				}
				$amount += $this->calculate_referral_amount( $product_total, $order_id, $product_id_for_rate, $affiliate_id, $category_id );

			}

			if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

				$this->log( 'Referral not created due to 0.00 amount.' );

				return false; // Ignore a zero amount referral
			}

			$description = $this->get_referral_description();

			if ( empty( $description ) ) {

				$this->log( 'Referral not created due to empty description.' );

				return;
			}

			$visit_id = affiliate_wp()->tracking->get_visit_id();

			if ( $existing ) {

				// Update the previously created referral
				affiliate_wp()->referrals->update_referral( $existing->referral_id, array(
					'amount'       => $amount,
					'reference'    => $order_id,
					'description'  => $description,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'affiliate_id' => $affiliate_id,
					'visit_id'     => $visit_id,
					'products'     => $this->get_products(),
					'customer'     => $this->get_customer( $order_id ),
					'context'      => $this->context
				) );

				$this->log( sprintf( 'WooCommerce Referral #%d updated successfully.', $existing->referral_id ) );

			} else {

				// Insert a pending referral
				$referral_id = $this->insert_pending_referral( $amount, $order_id, $description, $this->get_products(), array( 'affiliate_id' => $affiliate_id ) );

				if ( $referral_id ) {

					$this->log( sprintf( 'Referral #%d created successfully.', $referral_id ) );

					$amount         = affwp_currency_filter( affwp_format_amount( $amount ) );
					$name           = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
					$referral_link  = affwp_admin_link( 'referrals', esc_html( '#' . $referral_id ), array( 'action' => 'edit_referral', 'referral_id' => $referral_id ) );

					/* translators: 1: Referral link, 2: Amount, 3: Affiliate Name, 4: Affiliate ID */
					$this->order->add_order_note( sprintf( __( 'Referral %1$s for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp' ),
						$referral_link,
						$amount,
						$name,
						$affiliate_id
					) );

				} else {

					$this->log( 'Referral failed to be created.' );

				}
			}

		}

	}

	/**
	 * Adds a note to an order if an associated referral's amount is updated.
	 *
	 * @since 2.1.9
	 *
	 * @param \AffWP\Referral $updated_referral Updated referral object.
	 * @param \AffWP\Referral $referral         Old referral object.
	 * @param bool            $update           Whether the referral was successfully updated.
	 */
	public function updated_referral_note( $updated_referral, $referral, $updated ) {

		if ( $updated && 'woocommerce' === $updated_referral->context && ! empty( $updated_referral->reference ) ) {

			$order = wc_get_order( $updated_referral->reference );

			if ( false !== $order && $updated_referral->amount != $referral->amount ) {

				$amount        = affwp_currency_filter( affwp_format_amount( $updated_referral->amount ) );
				$name          = affiliate_wp()->affiliates->get_affiliate_name( $updated_referral->affiliate_id );
				$referral_link = affwp_admin_link( 'referrals', esc_html( '#' . $updated_referral->ID ), array( 'action' => 'edit_referral', 'referral_id' => $updated_referral->ID ) );

				/* translators: 1: Referral link, 2: Amount, 3: Affiliate Name */
				$order->add_order_note( sprintf( __( 'Referral %1$s updated. Amount %2$s recorded for %3$s', 'affiliate-wp' ),
					$referral_link,
					$amount,
					$name
				) );

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
		$items     = $this->order->get_items();
		foreach( $items as $key => $product ) {

			if( get_post_meta( $product['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}


			if( ! empty( $product['variation_id'] ) && get_post_meta( $product['variation_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this variation
			}

			if( ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$amount = $product['line_total'] + $product['line_tax'];
			} else {
				$amount = $product['line_total'];
			}

			if( ! empty( $product['variation_id'] ) ) {
				$product['name'] .= ' ' . sprintf( __( '(Variation ID %d)', 'affiliate-wp' ), $product['variation_id'] );
			}

			/**
			 * Filters an individual WooCommerce products line as stored in the referral record.
			 *
			 * @since 1.9.5
			 *
			 * @param array $line {
			 *     A WooCommerce product data line.
			 *
			 *     @type string $name            Product name.
			 *     @type int    $id              Product ID.
			 *     @type float  $amount          Product amount.
			 *     @type float  $referral_amount Referral amount.
			 * }
			 * @param array $product  Product data.
			 * @param int   $order_id Order ID.
			 */
			$products[] = apply_filters( 'affwp_woocommerce_get_products_line', array(
				'name'            => $product['name'],
				'id'              => $product['product_id'],
				'price'           => $amount,
				'referral_amount' => $this->calculate_referral_amount( $amount, $order_id, $product['product_id'] )
			), $product, $order_id );

		}

		return $products;

	}

	/**
	 * Retrieves the customer details for an order.
	 *
	 * @since 2.2
	 *
	 * @param int $order_id The ID of the order to retrieve customer details for.
	 * @return array An array of the customer details
	 */
	public function get_customer( $order_id = 0 ) {

		$customer = array();

		if ( function_exists( 'wc_get_order' ) ) {

			$order = wc_get_order( $order_id );

			if ( $order ) {

				if ( true === version_compare( WC()->version, '3.0.0', '>=' ) ) {
					$email      = $order->get_billing_email();
					$first_name = $order->get_billing_first_name();
					$last_name  = $order->get_billing_last_name();
					$ip         = $order->get_customer_ip_address();
				} else {
					$email      = $order->billing_email;
					$first_name = $order->billing_first_name;
					$last_name  = $order->billing_last_name;
					$ip         = $order->customer_ip_address;
				}

				$customer['user_id']    = $order->get_user_id();
				$customer['email']      = $email;
				$customer['first_name'] = $first_name;
				$customer['last_name']  = $last_name;
				$customer['ip']         = $ip;

			}
		}

		return $customer;
	}

	/**
	 * Marks a referral as complete when payment is completed.
	 *
	 * @since 1.0
	 * @since 2.0 Orders that are COD and transitioning from `wc-processing` to `wc-complete` stati are now able to be completed.
	 * @access public
	 */
	public function mark_referral_complete( $order_id = 0 ) {

		$this->order = apply_filters( 'affwp_get_woocommerce_order', new WC_Order( $order_id ) );

		if ( true === version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$payment_method = $this->order->get_payment_method();
		} else {
			$payment_method = get_post_meta( $order_id, '_payment_method', true );
		}

		// If the WC status is 'wc-processing' and a COD order, leave as 'pending'.
		if ( 'wc-processing' == $this->order->get_status() && 'cod' === $payment_method ) {
			return;
		}

		$this->complete_referral( $order_id );
	}

	/**
	 * Revoke the referral associated with the given order ID
	 *
	 * @access  public
	 * @since   2.1
	*/
	public function revoke_referral( $order_id = 0 ) {

		if ( is_a( $order_id, 'WP_Post' ) ) {
			$order_id = $order_id->ID;
		}

		if( 'shop_order' != get_post_type( $order_id ) ) {
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
	public function revoke_referral_on_refund( $order_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->revoke_referral( $order_id );

	}

	/**
	 * Setup the reference link
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'woocommerce' != $referral->context ) {

			return $reference;

		}

		$url   = get_edit_post_link( $reference );
		$order = wc_get_order( $reference );

		$reference = is_a( $order, 'WC_Order' ) ? $order->get_order_number() : $reference;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

	/**
	 * Shows the affiliate drop down on the discount edit / add screens
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function coupon_option() {

		global $post;

		add_filter( 'affwp_is_admin_page', '__return_true' );
		affwp_admin_scripts();

		$user_name    = '';
		$user_id      = '';
		$affiliate_id = get_post_meta( $post->ID, 'affwp_discount_affiliate', true );
		if( $affiliate_id ) {
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
			$user         = get_userdata( $user_id );
			$user_name    = $user ? $user->user_login : '';
		}
?>
		<p class="form-field affwp-woo-coupon-field">
			<label for="user_name"><?php _e( 'Affiliate Discount?', 'affiliate-wp' ); ?></label>
			<span class="affwp-ajax-search-wrap">
				<span class="affwp-woo-coupon-input-wrap">
					<input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search" data-affwp-status="active" autocomplete="off" />
				</span>
				<img class="help_tip" data-tip='<?php _e( 'If you would like to connect this discount to an affiliate, enter the name of the affiliate it belongs to.', 'affiliate-wp' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
			</span>
		</p>
<?php
	}

	/**
	 * Stores the affiliate ID in the discounts meta if it is an affiliate's discount
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function store_discount_affiliate( $coupon_id = 0 ) {

		if( empty( $_POST['user_name'] ) ) {

			delete_post_meta( $coupon_id, 'affwp_discount_affiliate' );
			return;

		}

		if( empty( $_POST['user_id'] ) && empty( $_POST['user_name'] ) ) {
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
	 * @since   1.1
	*/
	private function get_coupon_affiliate_id() {

		$coupons = $this->order->get_used_coupons();

		if ( empty( $coupons ) ) {
			return false;
		}

		foreach ( $coupons as $code ) {
			$coupon = new WC_Coupon( $code );

			if ( true === version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$coupon_id = $coupon->get_id();
			} else {
				$coupon_id = $coupon->id;
			}

			$affiliate_id = get_post_meta( $coupon_id, 'affwp_discount_affiliate', true );

			if ( $affiliate_id ) {

				if ( ! affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id ) ) {
					continue;
				}

				return $affiliate_id;

			}

		}

		return false;
	}

	/**
	 * Retrieves the referral description
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function get_referral_description() {

		$items       = $this->order->get_items();
		$description = array();

		foreach ( $items as $key => $item ) {

			if ( get_post_meta( $item['product_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			if( ! empty( $item['variation_id'] ) && get_post_meta( $item['variation_id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this variation
			}

			if( ! empty( $item['variation_id'] ) ) {
				$item['name'] .= ' ' . sprintf( __( '(Variation ID %d)', 'affiliate-wp' ), $item['variation_id'] );
			}

			$description[] = $item['name'];

		}

		$description = implode( ', ', $description );

		return $description;

	}

	/**
	 * Register the product settings tab
	 *
	 * @access  public
	 * @since   1.8.6
	*/
	public function product_tab( $tabs ) {

		$tabs['affiliate_wp'] = array(
			'label'  => __( 'AffiliateWP', 'affiliate-wp' ),
			'target' => 'affwp_product_settings',
			'class'  => array( ),
		);

		return $tabs;

	}

	/**
	 * Adds per-product referral rate settings input fields
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function product_settings() {

		global $post;

?>
		<div id="affwp_product_settings" class="panel woocommerce_options_panel">

			<div class="options_group">
				<p><?php _e( 'Configure affiliate rates for this product. These settings will be used to calculate affiliate earnings per-sale.', 'affiliate-wp' ); ?></p>
<?php
				woocommerce_wp_select( array(
					'id'          => '_affwp_woocommerce_product_rate_type',
					'label'       => __( 'Affiliate Rate Type', 'affiliate-wp' ),
					'options'     => array_merge( array( '' => __( 'Site Default', 'affiliate-wp' ) ),affwp_get_affiliate_rate_types() ),
					'desc_tip'    => true,
					'description' => __( 'Earnings can be based on either a percentage or a flat rate amount.', 'affiliate-wp' ),
				) );
				woocommerce_wp_text_input( array(
					'id'          => '_affwp_woocommerce_product_rate',
					'label'       => __( 'Affiliate Rate', 'affiliate-wp' ),
					'desc_tip'    => true,
					'description' => __( 'Leave blank to use default affiliate rates.', 'affiliate-wp' )
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_affwp_woocommerce_referrals_disabled',
					'label'       => __( 'Disable referrals', 'affiliate-wp' ),
					'description' => __( 'This will prevent this product from generating referral commissions for affiliates.', 'affiliate-wp' ),
					'cbvalue'     => 1
				) );

				wp_nonce_field( 'affwp_woo_product_nonce', 'affwp_woo_product_nonce' );
?>
			</div>

			<?php

				/**
				 * Fires at the bottom of the AffiliateWP product tab in WooCommerce.
				 *
				 * Use this hook to register extra product based settings to AffiliateWP product tab in WooCommerce.
				 *
				 * @ since 2.2.9
				 */
				do_action( 'affwp_' . $this->context . '_product_settings' );

			?>

		</div>
<?php

	}

	/**
	 * Adds per-product variation referral rate settings input fields
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function variation_settings( $loop, $variation_data, $variation ) {

		$rate      = $this->get_product_rate( $variation->ID );
		$rate_type = get_post_meta( $variation->ID, '_affwp_' . $this->context . '_product_rate_type', true );
		$disabled  = get_post_meta( $variation->ID, '_affwp_woocommerce_referrals_disabled', true );
?>

		<div id="affwp_product_variation_settings">

			<p class="form-row form-row-full">
				<?php _e( 'Configure affiliate rates for this product variation', 'affiliate-wp' ); ?>
			</p>

			<p class="form-row form-row-full">
				<label for="_affwp_woocommerce_variation_rate_types[<?php echo $variation->ID; ?>]"><?php echo __( 'Referral Rate Type', 'affiliate-wp' ); ?></label>
				<select name="_affwp_woocommerce_variation_rate_types[<?php echo $variation->ID; ?>]" id="_affwp_woocommerce_variation_rate_types[<?php echo $variation->ID; ?>]">
					<option value=""><?php _e( 'Site Default', 'affiliate-wp' ); ?></option>
					<?php foreach( affwp_get_affiliate_rate_types() as $key => $type ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $rate_type, $key ); ?>><?php echo esc_html( $type ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="form-row form-row-full">
				<label for="_affwp_woocommerce_variation_rates[<?php echo $variation->ID; ?>]"><?php echo __( 'Referral Rate', 'affiliate-wp' ); ?></label>
				<input type="text" size="5" name="_affwp_woocommerce_variation_rates[<?php echo $variation->ID; ?>]" value="<?php echo esc_attr( $rate ); ?>" class="wc_input_price" id="_affwp_woocommerce_variation_rates[<?php echo $variation->ID; ?>]" placeholder="<?php esc_attr_e( 'Referral rate (optional)', 'affiliate-wp' ); ?>" />
			</p>

			<p class="form-row form-row-full options">
				<label for="_affwp_woocommerce_variation_referrals_disabled[<?php echo $variation->ID; ?>]">
					<input type="checkbox" class="checkbox" name="_affwp_woocommerce_variation_referrals_disabled[<?php echo $variation->ID; ?>]" id="_affwp_woocommerce_variation_referrals_disabled[<?php echo $variation->ID; ?>]" <?php checked( $disabled, true ); ?> /> <?php _e( 'Disable referrals for this product variation', 'affiliate-wp' ); ?>
				</label>
			</p>

		</div>

		<?php

			/**
			 * Fires after the affiliate rates section in a WooCommerce product variation.
			 *
			 * Use this hook to register extra variation product based settings in WooCommerce.
			 *
			 * @since 2.2.9
			 *
			 * @param int     $loop
			 * @param array   $variation_data
			 * @param WP_Post $variation
			 */
			do_action( 'affwp_' . $this->context . '_variation_settings', $loop, $variation_data, $variation );

		?>

<?php

	}

	/**
	 * Saves per-product referral rate settings input fields
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function save_meta( $post_id = 0 ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		if( empty( $_POST['affwp_woo_product_nonce'] ) || ! wp_verify_nonce( $_POST['affwp_woo_product_nonce'], 'affwp_woo_product_nonce' ) ) {
			return $post_id;
		}

		$post = get_post( $post_id );

		if( ! $post ) {
			return $post_id;
		}

		// Check post type is product
		if ( 'product' != $post->post_type ) {
			return $post_id;
		}

		// Check user permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if( ! empty( $_POST['_affwp_' . $this->context . '_product_rate'] ) ) {

			$rate = sanitize_text_field( $_POST['_affwp_' . $this->context . '_product_rate'] );
			update_post_meta( $post_id, '_affwp_' . $this->context . '_product_rate', $rate );

		} else {

			delete_post_meta( $post_id, '_affwp_' . $this->context . '_product_rate' );

		}

		if( ! empty( $_POST['_affwp_' . $this->context . '_product_rate_type'] ) ) {

			$rate_type = sanitize_text_field( $_POST['_affwp_' . $this->context . '_product_rate_type'] );
			update_post_meta( $post_id, '_affwp_' . $this->context . '_product_rate_type', $rate_type );

		} else {

			delete_post_meta( $post_id, '_affwp_' . $this->context . '_product_rate_type' );

		}

		$this->save_variation_data( $post_id );

		if( isset( $_POST['_affwp_' . $this->context . '_referrals_disabled'] ) ) {

			update_post_meta( $post_id, '_affwp_' . $this->context . '_referrals_disabled', 1 );

		} else {

			delete_post_meta( $post_id, '_affwp_' . $this->context . '_referrals_disabled' );

		}

	}

	/**
	 * Saves variation data
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function save_variation_data( $product_id = 0 ) {

		if( ! empty( $_POST['variable_post_id'] ) && is_array( $_POST['variable_post_id'] ) ) {

			foreach( $_POST['variable_post_id'] as $variation_id ) {

				$variation_id = absint( $variation_id );

				if( ! empty( $_POST['_affwp_woocommerce_variation_rates'] ) && ! empty( $_POST['_affwp_woocommerce_variation_rates'][ $variation_id ] ) ) {

					$rate = sanitize_text_field( $_POST['_affwp_woocommerce_variation_rates'][ $variation_id ] );
					update_post_meta( $variation_id, '_affwp_' . $this->context . '_product_rate', $rate );

				} else {

					delete_post_meta( $variation_id, '_affwp_' . $this->context . '_product_rate' );

				}

				if( ! empty( $_POST['_affwp_woocommerce_variation_rate_types'] ) && ! empty( $_POST['_affwp_woocommerce_variation_rate_types'][ $variation_id ] ) ) {

					$rate_type = sanitize_text_field( $_POST['_affwp_woocommerce_variation_rate_types'][ $variation_id ] );
					update_post_meta( $variation_id, '_affwp_' . $this->context . '_product_rate_type', $rate_type );

				} else {

					delete_post_meta( $variation_id, '_affwp_' . $this->context . '_product_rate_type' );

				}

				if( ! empty( $_POST['_affwp_woocommerce_variation_referrals_disabled'] ) && ! empty( $_POST['_affwp_woocommerce_variation_referrals_disabled'][ $variation_id ] ) ) {

					update_post_meta( $variation_id, '_affwp_' . $this->context . '_referrals_disabled', 1 );

				} else {

					delete_post_meta( $variation_id, '_affwp_' . $this->context . '_referrals_disabled' );

				}

			}

		}

	}

	/**
	 * Calculate new referral amount based on product rate type
	 *
	 * @access  public
	 * @since   2.1.15
	*/
	public function calculate_referral_amount_type( $referral_amount, $affiliate_id, $amount, $reference, $product_id ) {

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {

			return $referral_amount;

		}

		$rate = '';

		if ( ! empty( $product_id ) ) {
			$rate = $this->get_product_rate( $product_id, $args = array( 'reference' => $reference, 'affiliate_id' => $affiliate_id ) );
		}

		if ( ! is_numeric( $rate ) ) {
			// Global referral rate setting, fallback to 20
			$default_rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
			$rate = affwp_abs_number_round( $default_rate );
		}

		$type = get_post_meta( $product_id, '_affwp_' . $this->context . '_product_rate_type', true );

		if ( $type ) {

			$decimals = affwp_get_decimal_count();

			// Format percentage rates
			$rate = ( 'percentage' === $type ) ? $rate / 100 : $rate;

			$referral_amount = ( 'percentage' === $type ) ? round( $amount * $rate, $decimals ) : $rate;

		}

		return $referral_amount;

	}

	/**
	 * Prevent WooCommerce from fixing rewrite rules when AffiliateWP runs affiliate_wp()->rewrites->flush_rewrites()
	 *
	 * See https://github.com/affiliatewp/AffiliateWP/issues/919
	 *
	 * @access  public
	 * @since   1.7.8
	*/
	public function skip_generate_rewrites() {
		remove_filter( 'rewrite_rules_array', 'wc_fix_rewrite_rules', 10 );
	}

	/**
	 * Forces the WC shop page to recognize it as such, even when accessed via a referral URL.
	 *
	 * @since 1.8
	 * @access public
	 *
	 * @param WP_Query $query Current query.
	 */
	public function force_shop_page_for_referrals( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( function_exists( 'wc_get_page_id' ) ) {
			$ref = affiliate_wp()->tracking->get_referral_var();

			if ( ( isset( $query->queried_object_id ) && wc_get_page_id( 'shop' ) == $query->queried_object_id )
				&& ! empty( $query->query_vars[ $ref ] )
			) {
				// Force WC to recognize that this is the shop page.
				$GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
			}
		}
	}

	/**
	 * Sets up verbose rewrites for the product base in conjunction with pretty affiliate URLs.
	 *
	 * @access public
	 * @since  2.0.9
	 *
	 * @see wc_get_permalink_structure()
	 */
	public function wc_300__product_base_rewrites() {

		if ( $shop_page_id = wc_get_page_id( 'shop' ) ) {

			$uri = get_page_uri( $shop_page_id );
			$ref = affiliate_wp()->tracking->get_referral_var();

			add_rewrite_rule( $uri . '/' . $ref . '(/(.*))?/?$', 'index.php?post_type=product&' . $ref . '=$matches[2]', 'top' );
		}
	}

	/**
	 * Strips pretty referral bits from pagination links on the Shop page.
	 *
	 * @since 1.8
	 * @since 1.8.1 Skipped for product taxonomies and searches
	 * @deprecated 1.8.3
	 * @see Affiliate_WP_Tracking::strip_referral_from_paged_urls()
	 * @access public
	 *
	 * @param string $link Pagination link.
	 * @return string (Maybe) filtered pagination link.
	 */
	public function strip_referral_from_paged_urls( $link ) {
		return affiliate_wp()->tracking->strip_referral_from_paged_urls( $link );
	}

	/**
	 * Inserts a link to the Affiliate Area in the My Account menu.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param array $items My Account menu items.
	 * @return array (Maybe) modified menu items.
	 */
	public function my_account_affiliate_area_link( $items ) {

		// Only add the link if enabled in WooCommerce > Settings > Accounts settings.
		if ( 'yes' !== get_option( 'affwp_woocommerce_affiliate_area_link' ) ) {
			return $items;
		}

		if ( affwp_is_affiliate() ) {

			$affiliate_area_page = affwp_get_affiliate_area_page_id();

			if ( $affiliate_area_page ) {

				/**
				 * Filters the title used for the Affiliate Area page in the WooCommerce My Account navigation.
				 *
				 * The page title is used by default.
				 *
				 * @since 2.1
				 *
				 * @param string $title               Affiliate Area page title.
				 * @param int    $affiliate_area_page Affiliate Area page ID.
				 */
				$title = apply_filters( 'affwp_woocommerce_affiliate_area_title', get_the_title( $affiliate_area_page ), $affiliate_area_page );

				/*
				 * Normally this would be $slug => $title, but we're going to intercept the 'affiliate-area'
				 * value directly when overriding the endpoint URL in the 'woocommerce_get_endpoint_url' hook.
				 */
				$affiliate_area = array( 'affiliate-area' => $title );

				$last_link = array();

				if ( array_key_exists( 'customer-logout', $items ) ) {

					// Grab the last link (probably the logout link).
					$last_link = array_slice( $items, count( $items ) - 1, 1, true );

					// Pop the last link off the end.
					array_pop( $items );

				}

				// Inject the Affiliate Area link 2nd to last, reinserting the last link.
				$items = array_merge( $items, $affiliate_area, $last_link );
			}

		}

		return $items;

	}

	/**
	 * Overrides the WooCommerce My Account endpoint URL for the affiliate area link.
	 *
	 * @access public
	 * @since  2.1.3
	 *
	 * @param string $url      My Account endpoint URL.
	 * @param string $endpoint Endpoint slug.
	 * @return string (Maybe) filtered endpoint URL.
	 */
	public function my_account_endpoint_url( $url, $endpoint ) {
		if ( 'affiliate-area' === $endpoint ) {
			$url = affwp_get_affiliate_area_page_url();
		}

		return $url;
	}

	/**
	 * Adds AffiliateWP-specific settings to the WooCommerce > Settings > Accounts settings page.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $settings Account settings.
	 * @return array Modified Account settings.
	 */
	public function account_settings( $settings ) {

		/**
		 * Filters the AffiliateWP-specific settings for the WooCommerce > Settings > Accounts settings screen.
		 *
		 * @since 2.1
		 *
		 * @param array $affwp_settings AffiliateWP settings.
		 */
		$affwp_settings = apply_filters( 'affwp_woocommerce_accounts_settings', array(
			array(
				'title' => __( 'AffiliateWP', 'affiliate-wp' ),
				'desc'  => __( 'AffiliateWP settings for the My Account page.', 'affiliate-wp' ),
				'id'    => 'affwp_account_settings',
				'type'  => 'title',
			),

			array(
				'title'         => __( 'Affiliate Area Link', 'affiliate-wp' ),
				'desc'          => __( 'Display a link to the Affiliate Area in the My Account navigation.', 'affiliate-wp' ),
				'id'            => 'affwp_woocommerce_affiliate_area_link',
				'default'       => 'no',
				'type'          => 'checkbox',
				'autoload'      => false,
			),

			array(
				'type' => 'sectionend',
				'id'   => 'affwp_account_settings'
			),

		) );

		$settings = array_merge( $settings, $affwp_settings );

		return $settings;
	}

	/**
	 * Add product category referral rate field.
	 *
	 * @access  public
	 * @since   2.2
	 */
	public function add_product_category_rate( $category ) {
		?>
		<div class="form-field">
			<label for="product-category-rate"><?php _e( 'Referral Rate', 'affiliate-wp' ); ?></label>
			<input type="text" class="small-text" name="_affwp_<?php echo $this->context; ?>_category_rate" id="product-category-rate">
			<p class="description"><?php _e( 'The referral rate for this category.', 'affiliate-wp' ); ?></p>
		</div>
	<?php
	}

	/**
	 * Edit product category referral rate field.
	 *
	 * @access  public
	 * @since   2.2
	 */
	public function edit_product_category_rate( $category ) {
		$category_id   = $category->term_id;
		$category_rate = get_term_meta( $category_id, '_affwp_' . $this->context . '_category_rate', true );
		?>
		<tr class="form-field">
			<th><label for="product-category-rate"><?php _e( 'Referral Rate', 'affiliate-wp' ); ?></label></th>

			<td>
				<input type="text" name="_affwp_<?php echo $this->context; ?>_category_rate" id="product-category-rate" value="<?php echo $category_rate ? esc_attr( $category_rate ) : ''; ?>">
				<p class="description"><?php _e( 'The referral rate for this category.', 'affiliate-wp' ); ?></p>
			</td>
		</tr>
	<?php
	}

	/**
	 * Save product category referral rate field.
	 *
	 * @access  public
	 * @since   2.2
	 */
	public function save_product_category_rate( $category_id ) {

		if ( isset( $_POST['_affwp_'. $this->context . '_category_rate'] ) ) {

			$rate     = $_POST['_affwp_' . $this->context . '_category_rate'];
			$meta_key = '_affwp_' . $this->context . '_category_rate';

			if ( $rate ) {
				update_term_meta( $category_id, $meta_key, $rate );
			} else {
				delete_term_meta( $category_id, $meta_key );
			}
		}
	}

	/**
	 * Register "Affiliate Referral" column in the Orders list table.
	 *
	 * @access public
	 * @since  2.1.11
	 *
	 * @param array  $columns Table columns.
	 * @return array $columns Modified columns.
	 */
	public function add_orders_column( $columns ) {
		$columns['referral'] = __( 'Affiliate Referral', 'affiliate-wp' );
		return $columns;
	}

	/**
	 * Render the "Affiliate Referral" column in the Orders list table for orders that have a referral associated with them.
	 *
	 * @access public
	 * @since  2.1.11
	 *
	 * @param string $column_name Name of column being rendered.
	 * @return void.
	 */
	public function render_orders_referral_column( $column_name, $order_id ) {

		if ( get_post_type( $order_id ) == 'shop_order' && 'referral' === $column_name ) {

			$referral = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

			if ( $referral ) {
				echo '<a href="' . affwp_admin_url( 'referrals', array( 'referral_id' => $referral->referral_id, 'action' => 'edit_referral' ) ) . '">#' . $referral->referral_id . '</a>';
			} else {
				echo '<span aria-hidden="true">&mdash;</span>';

			}

		}

	}

	/**
	 * Add "Affiliate Referral" to the Order preview screen.
	 *
	 * @access public
	 * @since  2.1.16
	 *
	 * @param array $order_details Order details to send to the preview screen.
	 * @param WC_Order $order Order object.
	 * @return array $order_details Modified order details.
	 */
	public function order_preview_get_referral( $order_details, $order ) {

		$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

		$referral = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

		if( $referral ) {

			$referral_html = '<div class="wc-order-preview-affwp-referral">';
			$referral_html .= '<strong>'. __( 'Affiliate Referral', 'affiliate_wp' ) . '</strong>';
			$referral_html .= '<a href="' . affwp_admin_url( 'referrals', array( 'referral_id' => $referral->referral_id, 'action' => 'edit_referral' ) ) . '">#' . $referral->referral_id . '</a>';
			$referral_html .= '</div>';

			$order_details['referral'] = $referral_html;

		}

		return $order_details;

	}

	/**
	 * Render the "Affiliate Referral" section in the order preview screen.
	 *
	 * @access public
	 * @since  2.1.16
	 *
	 */
	public function render_order_preview_referral() {

?>
		<# if ( data.referral ) { #>
			{{{ data.referral }}}
		<# } #>
<?php

	}
}

if ( class_exists( 'WooCommerce' ) ) {
	new Affiliate_WP_WooCommerce;
}
