<?php

class AffiliateWP_MLM_Base {

	/**
	 * The context for referrals. This refers to the integration that is being used.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $context;

	/**
	 * The ID of the referring affiliate
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $affiliate_id;
	
	public function __construct() {
	
		$this->affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
		$this->init();
	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function init() {

	}

	/**
	 * Label direct referrals via custom referral data
	 *
	 * @access  public
	 * @since   1.1
	 */
	public function prepare_direct_referral( $data ) {

		$data['custom'] = maybe_unserialize( $data['custom'] );
		
		// Prevent overwriting subscription id or existing referral type
		if ( empty( $data['custom'] ) )
			$data['custom'] = 'direct'; // Add referral type as custom referral data for direct referral
		
		return $data;

	}
	
	/**
	 * Determines if indirect referrals should be created and generates the upline.
	 *
	 * @access  public
	 * @since   1.1
	 */
	public function prepare_indirect_referrals( $referral_id, $data ) {
	
		// Check for the integration
		if ( ( $this->context !== $data['context'] ) ) {
			return;
		}

		$affiliate_id = $data['affiliate_id'];
		$data['custom'] = maybe_unserialize( $data['custom'] );
		$referral = affiliate_wp()->referrals->get_by( 'referral_id', $referral_id, $this->context );
		$referral_type = 'direct';

		if ( empty( $referral->custom ) ) {
			
			// Prevent overwriting subscription id
			if ( empty( $data['custom'] ) ) {
				
				// Add referral type as custom referral data for direct referral
				affiliate_wp()->referrals->update( $referral->referral_id, array( 'custom' => $referral_type ), '', 'referral' );
			
			}
		
		} elseif( $referral->custom == 'indirect' ) {
			return; // Prevent looping through indirect referrals
		}
		
		$upline_basis = affiliate_wp()->settings->get( 'affwp_mlm_upline_basis' );
		
		// Get the affiliate's upline
		$upline = affwp_mlm_get_upline( $affiliate_id, 0, $upline_basis );
		
		if ( $upline ) {
			
			// Filter upline by the default active status (Basic compression)
			$active_upline = affwp_mlm_filter_by_status( $upline );
			
			// Filter upline to allow custom compression
			$parent_affiliates = apply_filters( 'affwp_mlm_indirect_referral_upline', $active_upline, $referral_id, $data, $affiliate_id, $this->context );
			$level_count = 0;
			
			foreach( $parent_affiliates as $parent_affiliate_id ) {
				
				$level_count++;

				// Create the parent affiliate's referral
				$this->create_parent_referral( $parent_affiliate_id, $referral_id, $data, $level_count, $affiliate_id );
			
			}
		
		}
	
	}



	/**
	 * Completes a referral. Used when orders are marked as completed
	 *
	 * @access  public
	 * @since   1.0
	 * @param   $reference The reference column for the referral to complete per the current context
	 * @return  bool
	 */
	public function complete_referral( $referral, $reference ) {
		if ( empty( $reference ) ) {
			return false;
		}
		
		if ( ! $referral ) {
		
			$referral = affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context );
		}

		if ( empty( $referral ) ) {
			return false;
		}

		if ( is_object( $referral ) && $referral->status != 'pending' ) {
			// This referral has already been completed, rejected, or paid
			return false;
		}

		if ( ! apply_filters( 'affwp_auto_complete_referral', true ) )
			return false;

		if ( affwp_set_referral_status( $referral->referral_id, 'unpaid' ) ) {

			do_action( 'affwp_complete_referral', $referral->referral_id, $referral, $reference );
			
			do_action( 'affwp_mlm_complete_referral', $referral->referral_id, $referral, $reference );

			return true;
		}

		return false;

	}

	/**
	 * Rejects a referal. Used when orders are refunded, deleted, or voided
	 *
	 * @access  public
	 * @since   1.0
	 * @param   $reference The reference column for the referral to reject per the current context
	 * @return  bool
	 */
	public function reject_referral( $referral ) {

		if ( empty( $referral ) ) {
			return false;
		}

		if ( is_object( $referral ) && 'paid' == $referral->status ) {
			// This referral has already been paid so it cannot be rejected
			return false;
		}

		if ( affiliate_wp()->referrals->update( $referral->referral_id, array( 'status' => 'rejected' ), '', 'referral' ) ) {

			return true;

		}

		return false;

	}

	/**
	 * Calculate referral amount
	 *
	 * @since 1.0
	 */
	public function calculate_referral_amount( $parent_affiliate_id = 0, $base_amount = '', $reference = 0, $product_id = 0, $level_count = 0 ) {

		$rate = '';
		$type = '';

		$rate = $this->get_parent_rate( $parent_affiliate_id, $product_id, $level_count, $args = array( 'reference' => $reference ) );
		$type = $this->get_parent_rate_type( $parent_affiliate_id, $product_id, $args = array( 'reference' => $reference ) );

		if ( 'percentage' == $type ) {
			// Sanitize the rate and ensure it's in the proper format
			if ( $rate > 0 ) {
				$rate = $rate / 100;
			}
		}

		$amount = $this->calc_parent_referral_amount( $base_amount, $parent_affiliate_id, $reference, $rate, $product_id, $type, $level_count );

		return $amount;

	}

	/**
	 * Get the Rates for each Level
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_level_rates() {
		$rates = affiliate_wp()->settings->get( 'mlm_rates', array() );
		
		// Match the level count by offseting array values to start from 1
		array_unshift( $rates, '' );
		
		return apply_filters( 'affwp_mlm_level_rates', array_values( $rates ) );
	}
	
	/**
	 * Get parent rate while tracking sub-affiliate
	 *
	 * @since 1.0
	 */
	public function get_parent_rate( $parent_affiliate_id = 0, $product_id = 0, $level_count = 0, $args = array() ) {

		// Should be used when the sub-affiliate has made a referral

		$rates = $this->get_level_rates();
		
		// 1. The per-affiliate setting in Affiliates -> Affiliates -> Edit
		// $affiliate_level_rate = affiliate_wp()->affiliates->get_column( 'rate', $parent_affiliate_id );
		
		// 2. The global per level setting in Affiliates -> Settings -> MLM
		$level_rate = $rates[ $level_count ]['rate'];
		
		// 3. The global setting for all levels in Affiliates -> Settings -> MLM
		$mlm_rate = affiliate_wp()->settings->get( 'affwp_mlm_referral_rate' );

		$rate = empty( $affiliate_level_rate ) ? $level_rate : $affiliate_level_rate;

		if ( empty( $rate ) ){
			$rate = $mlm_rate;
		}
		
		$reference = isset( $args['reference'] ) ? $args['reference'] : '';

		$type = $this->get_parent_rate_type( $parent_affiliate_id, $product_id, $args = array( 'reference' => $reference ) );
		
		return apply_filters( 'affwp_mlm_get_affiliate_rate', (float) $rate, $product_id, $args, $this->affiliate_id, $this->context, $parent_affiliate_id, $level_count );
	} 

	/**
	 * Get parent rate type
	 *
	 * @since 1.0
	 */
	public function get_parent_rate_type( $parent_affiliate_id = 0, $product_id = 0, $args = array() ) {
	
		// Should be used when the sub-affiliate has made a referral
		
		// 1. The per-affiliate setting in Affiliates -> Affiliates -> Edit
		// $affiliate_level_rate_type = affiliate_wp()->affiliates->get_column( 'rate_type', $parent_affiliate_id );
		
		// 2. The global setting in Affiliates -> Settings -> MLM
		$mlm_rate_type = affiliate_wp()->settings->get( 'affwp_mlm_referral_rate_type' );
		
		/* Per Affiliate Level Rates
		if( empty( $affiliate_level_rate_type ) ) {
			$type = $mlm_rate_type;
		} else{
			$type = $affiliate_level_rate_type;
		}
		*/
		
		$type = $mlm_rate_type;
		
		return apply_filters( 'affwp_mlm_get_affiliate_rate_type', (string) $type, $product_id, $args, $this->affiliate_id, $this->context, $parent_affiliate_id );

	}

	/**
	 * Calculate parent referral amount
	 *
	 * @since 1.0
	 */
	public function calc_parent_referral_amount( $amount = '', $parent_affiliate_id = 0, $reference = 0, $rate = '', $product_id = 0, $type = '', $level_count = 0 ) {
	
		if ( empty( $rate ) ) {
		
			// 3. The global fallback setting in Affiliates -> Settings -> MLM	
			$rate = affiliate_wp()->settings->get( 'affwp_mlm_referral_rate' );
			
			// 3. The global fallback setting in Affiliates -> Settings -> General	
			//$rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
			
		}

		if( empty( $type ) ) {
		
			// 3. The global fallback setting in Affiliates -> Settings -> General
			$type = affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );
			
		}

		if ( 'percentage' == $type ) {
			
			$decimals = function_exists( 'affwp_get_decimal_count' ) ? affwp_get_decimal_count() : 2;
			$referral_amount = round( $amount * $rate, $decimals );
			
		} else {
		
			$referral_amount = $rate;
			
		}
		
		if( $referral_amount < 0 ) {
		
			$referral_amount = 0;
			
		}
		
		return apply_filters( 'affwp_mlm_calc_referral_amount', (string) $referral_amount, $amount, $parent_affiliate_id, $reference, $rate, $product_id, $type, $level_count );

	}
	
}