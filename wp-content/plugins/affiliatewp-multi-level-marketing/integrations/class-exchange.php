<?php

class AffiliateWP_MLM_Exchange extends AffiliateWP_MLM_Base {

	/**
	 * The transaction object
	 *
	 * @access  private
	 * @since   1.1
	*/
	private $transaction;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'it-exchange';
		
		/* Check for iThemes Exchange */
		$integrations = affiliate_wp()->settings->get( 'affwp_mlm_integrations' );
				
		if ( ! isset( $integrations['it-exchange'] ) ) return; // MLM integration for iThemes Exchange is disabled 
		
		if( ! class_exists( 'IT_Exchange' ) ) {
			return;
		}

		// Hook in before add_pending_referral() runs to save the data
		add_action( 'it_exchange_add_transaction_success', array( $this, 'get_txn_data' ), -1 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'mark_referrals_complete' ), 10, 4 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'revoke_referrals_on_refund' ), 10, 4 );
		add_action( 'it_exchange_update_transaction_status', array( $this, 'revoke_referrals_on_void' ), 10, 4 );
		add_action( 'wp_trash_post', array( $this, 'revoke_referrals_on_delete' ), 10 );

		// Process referral
		add_action( 'affwp_post_insert_referral', array( $this, 'process_referral' ), 10, 2 );
		
	}

	/**
	 * Get the transaction data
	 *
	 * @since 1.1
	 */
	public function get_txn_data( $transaction_id = 0 ) {

		// Store the transaction data for later
		$this->transaction = apply_filters( 'affwp_get_it_exchange_transaction', get_post_meta( $transaction_id, '_it_exchange_cart_object', true ) );
		
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
	 * @since 1.1
	 */
	public function create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count = 0, $affiliate_id ) {

		$direct_affiliate = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Process order and get amount
		$amount = $this->process_order( $parent_affiliate_id, $data, $level_count );

		$data['affiliate_id'] = $parent_affiliate_id;
		$data['description']  = $direct_affiliate . ' | Level '. $level_count . ' | ' . $this->transaction->description;
		$data['amount']       = $amount;
		$data['custom']       = 'indirect'; // Add referral type as custom referral data
		$data['context']      = 'it-exchange';

		unset( $data['date'] );
		unset( $data['currency'] );
		unset( $data['status'] );

		if ( ! (bool) apply_filters( 'affwp_mlm_create_indirect_referral', true, $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) ) {
			return false; // Allow extensions to prevent indirect referrals from being created
		}
		
		// Create the referral
		$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_mlm_insert_pending_referral', $data, $parent_affiliate_id, $affiliate_id, $referral_id, $level_count ) );

		if ( $referral_id ) {

			do_action( 'affwp_mlm_indirect_referral_created', $referral_id, $data );

			if ( it_exchange_transaction_is_cleared_for_delivery( $data['reference'] ) ) {

				$referral = affiliate_wp()->referrals->get_by( 'referral_id', $referral_id, $this->context );
				$this->complete_referral( $referral, $this->context );
			}
		}

	}

	/**
	 * Process order
	 *
	 * @since 1.1
	 */
	public function process_order( $parent_affiliate_id, $data, $level_count = 0 ) {

		$transaction_id = $data['reference'];

		// Calculate the referral amount based on product prices
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
			$amount += $this->calculate_referral_amount( $parent_affiliate_id, $referral_product_price, $transaction_id, $product['product_id'], $level_count );
		}

		if ( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			return false; // Ignore a zero amount referral
		}

		return $amount;

	}

	/**
	 * Mark referrals as complete
	 *
	 * @since 1.1
	 */
	public function mark_referrals_complete( $transaction, $old_status, $old_status_cleared, $new_status ) {

		if ( empty( $transaction ) ) {
			return;
		}

		$new_status         = it_exchange_get_transaction_status( $transaction->ID );
		$new_status_cleared = it_exchange_transaction_is_cleared_for_delivery( $transaction->ID );
		
		if ( ( $new_status != $old_status ) && ! $old_status_cleared && $new_status_cleared ) {

			$reference = $transaction->ID;
			$referrals = affwp_mlm_get_referrals_for_order( $reference, $this->context );
			
			if ( empty( $referrals ) ) {
				return;
			}
	
			foreach ( $referrals as $referral ) {
			
				$this->complete_referral( $referral, $reference );
				
			}
		}
	}

	/**
	 * Revoke referrals when a transaction is refunded
	 *
	 * @since 1.1
	 */
	public function revoke_referrals_on_refund( $transaction, $old_status, $old_status_cleared, $new_status ) {
	
		if ( empty( $transaction ) ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		if ( 'refunded' == $transaction->get_status() && 'paid' == $old_status ) {
			
			$referrals = affwp_mlm_get_referrals_for_order( $transaction->ID, $this->context );
	
			if ( empty( $referrals ) ) {
				return;
			}
	
			foreach ( $referrals as $referral ) {
	
				$this->reject_referral( $referral );
	
			}
		}
	}

	/**
	 * Revoke referrals when a transaction is voided
	 *
	 * @since 1.1
	 */
	public function revoke_referrals_on_void( $transaction, $old_status, $old_status_cleared, $new_status ) {
	
		if ( empty( $transaction ) ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		if ( 'refunded' == $transaction->get_status() ) {
			
			$referrals = affwp_mlm_get_referrals_for_order( $transaction->ID, $this->context );
	
			if ( empty( $referrals ) ) {
				return;
			}
	
			foreach ( $referrals as $referral ) {
	
				$this->reject_referral( $referral );
	
			}
		}
	}

	/**
	 * Revoke referrals when a transaction is deleted
	 *
	 * @since 1.1
	 */
	public function revoke_referrals_on_delete( $transaction_id = 0 ) {
	
		if ( empty( $transaction_id ) ) {
			return;
		}
		
		if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$post = get_post( $transaction_id );
		
		if ( ! $post ) {
			return;
		}
		
		if( 'it_exchange_tran' != $post->post_type ) {
			return;
		}

		$referrals = affwp_mlm_get_referrals_for_order( $transaction->ID, $this->context );

		if ( empty( $referrals ) ) {
			return;
		}

		foreach ( $referrals as $referral ) {

			$this->reject_referral( $referral );

		}
	}
}
new AffiliateWP_MLM_Exchange;