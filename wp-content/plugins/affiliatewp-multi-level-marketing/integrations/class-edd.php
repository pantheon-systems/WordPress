<?php

class AffiliateWP_MLM_EDD extends AffiliateWP_MLM_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0.5
	*/
	public function init() {

		$this->context = 'edd';
		
		/* Check for Easy Digital Downloads */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['edd'] ) ) return; // MLM integration for Easy Digital Downloads is disabled 
		
		add_action( 'edd_complete_purchase', array( $this, 'mark_referrals_complete' ), 5 );

		// Handle order updates/cancellations
		add_action( 'edd_update_payment_status', array( $this, 'revoke_referrals_on_refund' ), 10, 3 );
		add_action( 'edd_payment_delete', array( $this, 'revoke_referrals_on_delete' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}

	/**
	 * Process referral
	 *
	 * @since 1.1
	 */
	public function process_referral( $referral_id, $data ) {
		
		$this->prepare_indirect_referrals( $referral_id, $data );

	}

	/**
	 * Creates the referral for parent affiliate
	 *
	 * @since 1.0.5
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process cart and get amount
		$amount = $this->process_cart( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $this->get_referral_description( $level_count, $direct_affiliate, $data['reference'] );
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'edd';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// Create the referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			$amount 	= affwp_currency_filter( affwp_format_amount( $amount ) );
			$name   	= affiliate_wp()->affiliates->get_affiliate_name( $parent_affiliate_id );
			$payment_id = $data['reference'];
			
			edd_insert_payment_note( $payment_id, sprintf( __( 'Indirect Referral #%d for %s recorded for %s', 'affiliatewp-multi-level-marketing' ), $referral_id, $amount, $name ) );

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

		}

	}

	/**
	 * Process cart
	 *
	 * @since 1.0.5
	 */
	public function process_cart( $parent_affiliate_id, $data, $level_count = 0 ) {

		$payment_id = $data['reference'];

		$downloads = apply_filters( 'affwp_get_edd_cart_details', edd_get_payment_meta_cart_details( $payment_id ) );

		// Calculate the referral amount based on product prices
		$referral_total = 0.00;
		
		if ( is_array( $downloads ) ) {

			foreach ( $downloads as $key => $download ) {

				if( get_post_meta( $download['id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
					continue; // Referrals are disabled on this product
				}

				if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
					$amount = $download['price'] - $download['tax'];
				} else {
					$amount = $download['price'];
				}

				if( class_exists( 'EDD_Simple_Shipping' ) ) {

					if( isset( $download['fees'] ) ) {

						foreach( $download['fees'] as $fee_id => $fee ) {

							if( false !== strpos( $fee_id, 'shipping' ) ) {

								if( ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

									$amount += $fee['amount'];

								}

							}

						}

					}

				}

				if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
					return false; // Ignore a zero amount referral
				}
				
				$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $amount, $payment_id, $download['id'], $level_count );

			}

		} else {

			if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {
				$amount = edd_get_payment_subtotal( $payment_id );
			} else {
				$amount = edd_get_payment_amount( $payment_id );
			}

			if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
				return false; // Ignore a zero amount referral
			}

			$referral_total = $this->calculate_referral_amount( $parent_affiliate_id, $amount, $payment_id, $download['id'], $level_count );
		}

		return $referral_total;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.0.5
	 */
	public function mark_referrals_complete( $payment_id = 0 ) {

		if ( empty( $payment_id ) ) {
			return false;
		}

		$reference = $payment_id;
		$referrals = affwp_mlm_get_referrals_for_order( $payment_id, $this->context );
		
		if ( empty( $referrals ) ) {
			return false;
		}

		foreach ( $referrals as $referral ) {
		
			$this->complete_referral( $referral, $reference );
			
		}

	}

	/**
	 * Revoke referrals when a payment is refunded
	 *
	 * @since 1.0.5
	 */
	public function revoke_referrals_on_refund( $payment_id = 0, $new_status, $old_status ) {
	
		if ( empty( $payment_id ) ) {
			return;
		}
		
		if( 'publish' != $old_status && 'revoked' != $old_status ) {
			return;
		}

		if( 'refunded' != $new_status ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$referrals = affwp_mlm_get_referrals_for_order( $payment_id, $this->context );

		if ( empty( $referrals ) ) {
			return;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

	/**
	 * Revokes referrals when a payment is deleted
	 *
	 * @access  public
	 * @since   1.0.5
	*/
	public function revoke_referrals_on_delete( $payment_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$referrals = affwp_mlm_get_referrals_for_order( $payment_id, $this->context );

		if ( empty( $referrals ) ) {
			return;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}

	}

	/**
	 * Retrieve the EDD referral description
	 *
	 * @since   1.0.5
	*/
	public function get_referral_description( $level_count, $direct_affiliate, $payment_id = 0 ) {

		if ( empty( $payment_id ) ) {
			return;
		}
		
		$downloads   = edd_get_payment_meta_downloads( $payment_id );
		$description = array();
		$item_names = array();

		foreach ( $downloads as $key => $item ) {

			if ( get_post_meta( $item['id'], '_affwp_' . $this->context . '_referrals_disabled', true ) ) {
				continue; // Referrals are disabled on this product
			}

			$item_names[] = get_the_title( $item['id'] );

		}
		
		$description[] = $direct_affiliate . ' | Level '. $level_count . ' | ' . implode( ', ', $item_names );
		
		return implode( ', ', $description );

	}

}
new AffiliateWP_MLM_EDD;