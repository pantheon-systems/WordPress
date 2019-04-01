<?php

class Affiliate_WP_EDD extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'edd';

		add_action( 'edd_insert_payment', array( $this, 'add_pending_referral' ), 99999, 2 );

		// Integration with EDD - Free Downloads to create referrals.
		add_action( 'edd_free_downloads_post_complete_payment', array( $this, 'add_referral_edd_free_downloads' ) );

		add_action( 'edd_complete_purchase', array( $this, 'track_discount_referral' ), 9 );
		add_action( 'edd_complete_purchase', array( $this, 'mark_referral_complete' ) );
		add_action( 'edd_complete_purchase', array( $this, 'insert_payment_note' ), 11 );

		add_action( 'edd_update_payment_status', array( $this, 'revoke_referral_on_refund' ), 10, 3 );
		add_action( 'edd_payment_delete', array( $this, 'revoke_referral_on_delete' ), 10 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

		// Discount code tracking actions and filters
		add_action( 'edd_add_discount_form_bottom', array( $this, 'discount_edit' ) );
		add_action( 'edd_edit_discount_form_bottom', array( $this, 'discount_edit' ) );
		add_action( 'edd_post_update_discount', array( $this, 'store_discount_affiliate' ), 10, 2 );
		add_action( 'edd_post_insert_discount', array( $this, 'store_discount_affiliate' ), 10, 2 );

		// Integration with EDD commissions to adjust commission rates if a referral is present
		add_filter( 'eddc_calc_commission_amount', array( $this, 'commission_rate' ), 10, 2 );
		add_filter( 'affwp_settings_integrations', array( $this, 'commission_settings' ), 10 );
		add_filter( 'affwp_settings_integrations', array( $this, 'renewal_settings' ), 10 );

		// Per product referral rates
		add_action( 'edd_meta_box_settings_fields', array( $this, 'download_settings' ), 100 );
		add_filter( 'edd_metabox_fields_save', array( $this, 'download_save_fields' ) );

		// Per category referral rates
		add_action( 'download_category_add_form_fields', array( $this, 'add_download_category_rate' ), 10, 2 );
		add_action( 'download_category_edit_form_fields', array( $this, 'edit_download_category_rate' ), 10 );
		add_action( 'edited_download_category', array( $this, 'save_download_category_rate' ) );  
		add_action( 'create_download_category', array( $this, 'save_download_category_rate' ) );
	}

	/**
	 * Records a pending referral when a pending payment is created
	 *
	 * @since 1.0
	 *
	 * @param int   $payment_id   Optional. Payment ID. Defualt 0.
	 * @param array $payment_data Optional. Payment data. Default empty array.
	*/
	public function add_pending_referral( $payment_id = 0, $payment_data = array() ) {

		if ( $this->was_referred() ) {

			// get affiliate ID
			$affiliate_id = $this->get_affiliate_id( $payment_id );

			// get customer email
			$customer_email = edd_get_payment_user_email( $payment_id );

			// Customers cannot refer themselves
			if ( $this->is_affiliate_email( $customer_email, $affiliate_id ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return false;
			}

			// Check for an existing referral
			$existing = affiliate_wp()->referrals->get_by( 'reference', $payment_id, $this->context );

			// If an existing referral exists and it is paid or unpaid exit.
			if ( $existing && ( 'paid' == $existing->status || 'unpaid' == $existing->status ) ) {
				return false; // Completed Referral already created for this reference
			}

			if ( affiliate_wp()->settings->get( 'edd_disable_on_renewals' ) ) {

				$was_renewal = get_post_meta( $payment_id, '_edd_sl_is_renewal', true );

				if ( $was_renewal ) {

					$this->log( 'Referral not created because order was a renewal.' );

					return;
				}

			}

			if ( affiliate_wp()->settings->get( 'edd_disable_on_upgrades' ) ) {

				$cart_contents = edd_get_cart_contents();

				if ( is_array( $cart_contents ) ) {

					foreach ( $cart_contents as $item ) {

						if ( ! empty( $item['options']['is_upgrade'] ) ) {

							$this->log( 'Referral not created because order was an upgrade.' );

							return;
						}

					}

				}

			}

			// get referral total
			$referral_total = $this->get_referral_total( $payment_id, $affiliate_id );

			// Referral description
			$desc = $this->get_referral_description( $payment_id );

			if ( empty( $desc ) ) {

				$this->log( 'Referral not created due to empty description.' );

				return;
			}

			if ( $existing ) {

				// Update the previously created referral
				affiliate_wp()->referrals->update_referral( $existing->referral_id, array(
					'amount'       => $referral_total,
					'reference'    => $payment_id,
					'description'  => $desc,
					'currency'     => $existing->currency,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'products'     => $this->get_products( $payment_id ),
					'context'      => $this->context
				) );

				$this->log( sprintf( 'EDD Referral #%d updated successfully.', $existing->referral_id ) );

			} else {

				// insert a pending referral
				$referral_id = $this->insert_pending_referral( $referral_total, $payment_id, $desc, $this->get_products( $payment_id ) );

			}

		}

	}

	/**
	 * Records a referral when a free product is purchased using EDD - Free Downloads extension.
	 *
	 * @since 2.2.9
	 *
	 * @param int $payment_id Optional. Payment ID. Default 0.
	 *
	 * @return boolean
	 */
	public function add_referral_edd_free_downloads( $payment_id = 0 ) {

		if ( $this->was_referred() ) {

			// Get affiliate ID.
			$affiliate_id = $this->get_affiliate_id( $payment_id );

			// Get customer email.
			$customer_email = edd_get_payment_user_email( $payment_id );

			// Customers cannot refer themselves.
			if ( $this->is_affiliate_email( $customer_email, $affiliate_id ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return false;
			}

			// Check for an existing referral.
			$existing = affiliate_wp()->referrals->get_by( 'reference', $payment_id, $this->context );

			// If an existing referral exists and it is paid or unpaid exit.
			if ( $existing && ( 'paid' == $existing->status || 'unpaid' == $existing->status ) ) {
				return false; // Completed Referral already created for this reference
			}

			// get referral total.
			$referral_total = $this->get_referral_total( $payment_id, $affiliate_id );

			// Referral description.
			$desc = $this->get_referral_description( $payment_id );

			if ( empty( $desc ) ) {

				$this->log( 'Referral not created due to empty description.' );

				return false;
			}

			if ( $existing ) {

				$referral_id = $existing->referral_id;

				// Update the previously created referral.
				affiliate_wp()->referrals->update_referral( $referral_id, array(
					'amount'       => $referral_total,
					'reference'    => $payment_id,
					'description'  => $desc,
					'currency'     => $existing->currency,
					'campaign'     => affiliate_wp()->tracking->get_campaign(),
					'products'     => $this->get_products( $payment_id ),
					'context'      => $this->context
				) );

				$this->log( sprintf( 'EDD Referral #%d updated successfully.', $existing->referral_id ) );

			} else {

				// Insert a pending referral.
				$referral_id = $this->insert_pending_referral( $referral_total, $payment_id, $desc, $this->get_products( $payment_id ) );

			}

			if ( $referral_id ) {

				$this->complete_referral( $payment_id );

			}

		}

	}

	/**
	 * Records referrals for the affiliate if a discount code belonging to the affiliate is used
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function track_discount_referral( $payment_id = 0 ) {

		$user_info = edd_get_payment_meta_user_info( $payment_id );

		if ( isset( $user_info['discount'] ) && $user_info['discount'] != 'none' ) {

			if ( affiliate_wp()->settings->get( 'edd_disable_on_renewals' ) ) {

				$was_renewal = get_post_meta( $payment_id, '_edd_sl_is_renewal', true );

				if ( $was_renewal ) {

					$this->log( 'Referral not created because order was a renewal.' );

					return;
				}

			}

			if ( affiliate_wp()->settings->get( 'edd_disable_on_upgrades' ) ) {

				$was_upgrade = get_post_meta( $payment_id, '_edd_sl_upgraded_payment_id', true );

				if ( $was_upgrade ) {

					$this->log( 'Referral not created because order was an upgrade.' );

					return;
				}

			}

			$discounts = array_map( 'trim', explode( ',', $user_info['discount'] ) );

			if ( empty( $discounts ) ) {
				return;
			}

			foreach ( $discounts as $code ) {

				$discount_id  = edd_get_discount_id_by_code( $code );
				$affiliate_id = get_post_meta( $discount_id, 'affwp_discount_affiliate', true );

				if ( ! $affiliate_id ) {
					continue;
				}

				$this->affiliate_id = $affiliate_id;

				if ( ! affiliate_wp()->tracking->is_valid_affiliate( $this->affiliate_id ) ) {

					$this->log( 'Referral not created because affiliate is invalid.' );

					continue;
				}

				$existing = affiliate_wp()->referrals->get_by( 'reference', $payment_id, $this->context );

				// calculate the referral total
				$referral_total = $this->get_referral_total( $payment_id, $this->affiliate_id );

				// referral already exists, update it
				if ( ! empty( $existing->referral_id ) ) {

					// If a referral was already recorded, overwrite it with the linked discount affiliate
					affiliate_wp()->referrals->update( $existing->referral_id, array( 'affiliate_id' => $this->affiliate_id, 'status' => 'unpaid', 'amount' => $referral_total ), '', 'referral' );

					$this->log( sprintf( 'Referral #%d updated successfully.', $existing->referral_id ) );

				} else {
					// new referral

					if ( 0 == $referral_total && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

						$this->log( 'Referral not created due to 0.00 amount.' );

						return false; // Ignore a zero amount referral
					}

					$desc = $this->get_referral_description( $payment_id );

					if ( empty( $desc ) ) {

						$this->log( 'Referral not created due to empty description.' );

						return false;
					}

					$referral_id = affiliate_wp()->referrals->add(
						array(
							'amount'       => $referral_total,
							'reference'    => $payment_id,
							'description'  => $desc,
							'campaign'     => affiliate_wp()->tracking->get_campaign(),
							'affiliate_id' => $this->affiliate_id,
							'context'      => $this->context,
							'products'     => $this->get_products( $payment_id )
						)
					);

					$this->log( sprintf( 'Referral #%d created successfully.', $referral_id ) );
				}
			}
		}

	}

	/**
	 * Get the referral total
	 *
	 * @access  public
	 * @since   1.3.1
	*/
	public function get_referral_total( $payment_id = 0, $affiliate_id = 0 ) {

		$downloads = apply_filters( 'affwp_get_edd_cart_details', edd_get_payment_meta_cart_details( $payment_id ) );

		if ( is_array( $downloads ) ) {

			// Calculate the referral amount based on product prices
			$referral_total = 0.00;

			foreach ( $downloads as $key => $download ) {

				// Get the categories associated with the download.
				$categories = get_the_terms( $download['id'], 'download_category' );
				
				// Get the first category ID for the download.
				$category_id = $categories && ! is_wp_error( $categories ) ? $categories[0]->term_id : 0;

				if ( get_post_meta( $download['id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
					continue; // Referrals are disabled on this product
				}

				if ( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
					$amount = $download['price'] - $download['tax'];
				} else {
					$amount = $download['price'];
				}

				if ( class_exists( 'EDD_Simple_Shipping' ) ) {

					if ( isset( $download['fees'] ) ) {

						foreach ( $download['fees'] as $fee_id => $fee ) {

							if ( false !== strpos( $fee_id, 'shipping' ) ) {

								if ( ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {
									$amount += $fee['amount'];
								}

							}

						}

					}

				}

				if ( class_exists( 'edd_dp' ) ) {

					if( isset( $download['fees']['dp_'.$download['id']] ) ) {
						$amount += $download['fees']['dp_'.$download['id']]['amount'];
					}

				}

				// Check for Recurring Payments signup fee
				if( ! empty( $download['item_number']['options']['recurring']['signup_fee'] ) ) {
					$amount += $download['item_number']['options']['recurring']['signup_fee'];
				}

				$referral_total += $this->calculate_referral_amount( $amount, $payment_id, $download['id'], $affiliate_id, $category_id );
			}

		} else {

			if ( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$amount = edd_get_payment_subtotal( $payment_id );
			} else {
				$amount = edd_get_payment_amount( $payment_id );
			}

			$referral_total = $this->calculate_referral_amount( $amount, $payment_id, '', $affiliate_id, $category_id );
		}

		return $referral_total;

	}

	/**
	 * Retrieves the product details array for the referral
	 *
	 * @access  public
	 * @since   1.6
	 * @return  array
	*/
	public function get_products( $payment_id = 0 ) {

		$products  = array();
		$downloads = edd_get_payment_meta_cart_details( $payment_id );
		foreach( $downloads as $key => $item ) {

			if( get_post_meta( $item['id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$amount = $item['price'] - $item['tax'];
			} else {
				$amount = $item['price'];
			}

			$products[] = array(
				'name'            =>  get_the_title( $item['id'] ),
				'id'              => $item['id'],
				'price'           => $amount,
				'referral_amount' => $this->calculate_referral_amount( $amount, $payment_id, $item['id'] )
			);

		}

		return $products;

	}

	/**
	 * Retrieves the customer details for an order.
	 *
	 * @since 2.2
	 *
	 * @param int $payment_id Optional. Payment ID. Default 0.
	 * @return array Customer details.
	*/
	public function get_customer( $payment_id = 0 ) {

		$customer = array();

		if ( class_exists( 'EDD_Customer' ) ) {

			$edd_customer = new EDD_Customer( edd_get_payment_customer_id( $payment_id ) );
			$names        = explode( ' ', $edd_customer->name );
			$first_name   = $names[0];
			$last_name    = '';
			if( ! empty( $names[1] ) ) {
				unset( $names[0] );
				$last_name = implode( ' ', $names );
			}

			$customer['user_id']    = $edd_customer->user_id;
			$customer['email']      = $edd_customer->email;
			$customer['first_name'] = $first_name;
			$customer['last_name']  = $last_name;
			$customer['ip']         = edd_get_payment_user_ip( $payment_id );

		}

		return $customer;
	}

	/**
	 * Insert payment note
	 *
	 * @access  public
	 * @since   1.3.1
	*/
	public function insert_payment_note( $payment_id = 0 ) {

		$referral = affiliate_wp()->referrals->get_by( 'reference', $payment_id, $this->context );

		if ( empty( $referral ) ) {
			return;
		}

		$amount         = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$affiliate_id   = $referral->affiliate_id;
		$name           = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
		$referral_link  = affwp_admin_link( 'referrals', esc_html( '#' . $referral->referral_id ), array( 'action' => 'edit_referral', 'referral_id' => $referral->referral_id ) );

		/* translators: 1: Referral link, 2: Amount, 3: Affiliate Name, 4: Affiliate ID */
		edd_insert_payment_note( $payment_id, sprintf( __( 'Referral %1$s for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp' ),
			$referral_link,
			$amount,
			$name,
			$affiliate_id
		) );

	}

	/**
	 * Sets a referral to unpaid when payment is completed
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function mark_referral_complete( $payment_id = 0 ) {

		$this->complete_referral( $payment_id );
	}

	/**
	 * Revokes a referral when payment is refunded
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function revoke_referral_on_refund( $payment_id = 0, $new_status, $old_status ) {

		if( 'publish' != $old_status && 'revoked' != $old_status ) {
			return;
		}

		if( 'refunded' != $new_status ) {
			return;
		}

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $payment_id );

	}

	/**
	 * Revokes a referral when a payment is deleted
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function revoke_referral_on_delete( $payment_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $payment_id );

	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'edd' != $referral->context ) {

			return $reference;

		}

		$url = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

	/**
	 * Retrieves the referral description
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function get_referral_description( $payment_id = 0 ) {

		$description = array();
		$downloads   = edd_get_payment_meta_downloads( $payment_id );

		foreach ( $downloads as $key => $item ) {

			$download_id = $item['id'];
			$download    = new EDD_Download( $download_id );

			if ( get_post_meta( $download_id, '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			$desc = get_the_title( $download_id );

			if ( $download->has_variable_prices() ) {
				$desc .= ' - ' . edd_get_price_option_name( $download_id, $item['options']['price_id'] );
			}

			$description[] = $desc;
		}

		return implode( ', ', $description );
	}

	/**
	 * Shows the affiliate drop down on the discount edit / add screens
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function discount_edit( $discount_id = 0 ) {

		add_filter( 'affwp_is_admin_page', '__return_true' );
		affwp_admin_scripts();

		$user_name    = '';
		$user_id      = 0;
		$affiliate_id = get_post_meta( $discount_id, 'affwp_discount_affiliate', true );
		if( $affiliate_id ) {
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
			$user         = get_userdata( $user_id );
			$user_name    = $user ? $user->user_login : '';
		}
?>
		<table class="form-table">
			<tbody>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="user_name"><?php _e( 'Affiliate Discount?', 'affiliate-wp' ); ?></label>
					</th>
					<td>
						<span class="affwp-ajax-search-wrap">
							<input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search" data-affwp-status="active" autocomplete="off" style="width: 300px;" />
						</span>
						<p class="description"><?php _e( 'If you would like to connect this discount to an affiliate, enter the name of the affiliate it belongs to.', 'affiliate-wp' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
<?php
	}

	/**
	 * Stores the affiliate ID in the discounts meta if it is an affiliate's discount
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function store_discount_affiliate( $details, $discount_id = 0 ) {

		if ( empty( $_POST['user_name'] ) ) {
			delete_post_meta( $discount_id, 'affwp_discount_affiliate' );
			return;
		}

		if( empty( $_POST['user_id'] ) && empty( $_POST['user_name'] ) ) {
			return;
		}

		$data = affiliate_wp()->utils->process_request_data( $_POST, 'user_name' );

		$affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

		update_post_meta( $discount_id, 'affwp_discount_affiliate', $affiliate_id );
	}

	/**
	 * Adjust the commission rate recorded if a referral is present
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function commission_rate( $amount, $args ) {

		if( ! affiliate_wp()->settings->get( 'edd_adjust_commissions' ) ) {
			return $amount;
		}

		$referral = affiliate_wp()->referrals->get_by( 'reference', $args['payment_id']  );

		if( ! empty( $referral->products ) ) {
			$products = maybe_unserialize( maybe_unserialize( $referral->products ) );
			foreach( $products as $product ) {

				if( (int) $product['id'] !== (int) $args['download_id'] ) {
					continue;
				}

				if( 'flat' == $args['type'] ) {
					return $args['rate'] - $product['referral_amount'];
				}

				$args['price'] -= $product['referral_amount'];

				if ( $args['rate'] >= 1 ) {
					$amount = $args['price'] * ( $args['rate'] / 100 ); // rate format = 10 for 10%
				} else {
					$amount = $args['price'] * $args['rate']; // rate format set as 0.10 for 10%
				}

			}

		}

		return $amount;
	}

	/**
	 * Add a setting to toggle whether referrals adjust EDD commissions
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function commission_settings( $settings ) {

		if( function_exists( 'eddc_record_commission' ) ) {

			$settings[ 'edd_adjust_commissions' ] = array(
				'name' => __( 'Adjust EDD Commissions', 'affiliate-wp' ),
				'desc' => __( 'Should AffiliateWP adjust the commission amounts recorded for purchases that include affiliate referrals? This will subtract the referral amount from the base amount used to calculate the commission total.', 'affiliate-wp' ),
				'type' => 'checkbox'
			);

		}

		return $settings;
	}

	/**
	 * Add a setting to toggle whether referrals adjust EDD commissions
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function renewal_settings( $settings ) {

		if( function_exists( 'EDD_Software_Licensing' ) ) {

			$settings[ 'edd_disable_on_renewals' ] = array(
				'name' => __( 'Disable Renewal Referrals', 'affiliate-wp' ),
				'desc' => __( 'Should AffiliateWP prevent referral commissions from being recorded on renewal purchases with EDD Software Licensing?', 'affiliate-wp' ),
				'type' => 'checkbox'
			);

			$settings[ 'edd_disable_on_upgrades' ] = array(
				'name' => __( 'Disable Referrals on Upgrades', 'affiliate-wp' ),
				'desc' => __( 'Should AffiliateWP prevent referral commissions from being recorded on license upgrade purchases with EDD Software Licensing?', 'affiliate-wp' ),
				'type' => 'checkbox'
			);

		}

		return $settings;
	}

	/**
	 * Adds per-product referral rate settings input fields
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function download_settings( $download_id = 0 ) {

		$rate     = get_post_meta( $download_id, '_affwp_' . $this->context . '_product_rate', true );
		$disabled = get_post_meta( $download_id, '_affwp_' . $this->context . '_referrals_disabled', true );
?>
		<p>
			<strong><?php _e( 'Affiliate Rates:', 'affiliate-wp' ); ?></strong>
		</p>

		<p>
			<label for="affwp_product_rate">
				<input type="text" name="_affwp_edd_product_rate" id="affwp_product_rate" class="small-text" value="<?php echo esc_attr( $rate ); ?>" />
				<?php _e( 'Referral Rate', 'affiliate-wp' ); ?>
			</label>
		</p>

		<p>
			<label for="affwp_disable_referrals">
				<input type="checkbox" name="_affwp_edd_referrals_disabled" id="affwp_disable_referrals" value="1"<?php checked( $disabled, true ); ?> />
				<?php printf( __( 'Disable referrals on this %s', 'affiliate-wp' ), edd_get_label_singular() ); ?>
			</label>
		</p>

		<p><?php _e( 'These settings will be used to calculate affiliate earnings per-sale. Leave blank to use the site default referral rate.', 'affiliate-wp' ); ?></p>
<?php
	}

	/**
	 * Tells EDD to save our product settings
	 *
	 * @access  public
	 * @since   1.2
	 * @return  array
	*/
	public function download_save_fields( $fields = array() ) {
		$fields[] = '_affwp_edd_product_rate';
		$fields[] = '_affwp_edd_referrals_disabled';
		return $fields;
	}

	/**
	 * Add download_category referral rate field.
	 * 
	 * @access  public
	 * @since   2.2
	 */
	public function add_download_category_rate( $category ) {
		?>
		<div class="form-field">
			<label for="download-category-rate"><?php _e( 'Referral Rate', 'affiliate-wp' ); ?></label>
			<input type="text" class="small-text" name="_affwp_<?php echo $this->context; ?>_category_rate" id="download-category-rate">
			<p class="description"><?php printf( __( 'The referral rate for this %s category.', 'affiliate-wp' ), strtolower( edd_get_label_singular() ) ); ?></p>
		</div>
	<?php
	}

	/**
	 * Edit download_category referral rate field.
	 * 
	 * @access  public
	 * @since   2.2
	 */
	public function edit_download_category_rate( $category ) {
		$category_id   = $category->term_id;
		$category_rate = get_term_meta( $category_id, '_affwp_' . $this->context . '_category_rate', true ); 
		?>
		<tr class="form-field">
			<th><label for="download-category-rate"><?php _e( 'Referral Rate', 'affiliate-wp' ); ?></label></th>
			<td>
				<input type="text" name="_affwp_<?php echo $this->context; ?>_category_rate" id="download-category-rate" value="<?php echo $category_rate ? esc_attr( $category_rate ) : ''; ?>">
				<p class="description"><?php printf( __( 'The referral rate for this %s category.', 'affiliate-wp' ), strtolower( edd_get_label_singular() ) ); ?></p>
			</td>
		</tr>
	<?php
	}
	
	/**
	 * Save download_category referral rate field.
	 *
	 * @access  public
	 * @since   2.2
	 */
	public function save_download_category_rate( $category_id ) {

		if ( isset( $_POST['_affwp_' . $this->context . '_category_rate'] ) ) {

			$rate     = $_POST['_affwp_' . $this->context . '_category_rate'];
			$meta_key = '_affwp_' . $this->context . '_category_rate';

			if ( $rate ) {
				update_term_meta( $category_id, $meta_key, $rate );
			} else {
				delete_term_meta( $category_id, $meta_key );
			}

		}

	}

}

if ( class_exists( 'Easy_Digital_Downloads' ) ) {
	new Affiliate_WP_EDD;
}
