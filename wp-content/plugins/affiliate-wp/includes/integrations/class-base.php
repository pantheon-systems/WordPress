<?php

abstract class Affiliate_WP_Base {

	/**
	 * The context for referrals. This refers to the integration that is being used.
	 *
	 * @access  public
	 * @since   1.2
	 */
	public $context;

	/**
	 * The ID of the referring affiliate
	 *
	 * @access  public
	 * @since   1.2
	 */
	public $affiliate_id;

	/**
	 * Debug mode
	 *
	 * @access  public
	 * @since   1.8
	 */
	public $debug;

	/**
	 * Logging class object
	 *
	 * @access  public
	 * @since   1.8
	 * @deprecated 2.0.2
	 */
	public $logs;

	/**
	 * Constructor
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {
		// Keep $debug initialization for back-compat.
		$this->debug = affiliate_wp()->settings->get( 'debug_mode', false );

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
	public function init() {}

	/**
	 * Determines if the current session was referred through an affiliate link
	 *
	 * @access  public
	 * @since   1.0
	 * @return  bool
	 */
	public function was_referred() {
		return affiliate_wp()->tracking->was_referred();
	}

	/**
	 * Inserts a pending referral. Used when orders are initially created
	 *
	 * @access  public
	 * @since   1.0
	 * @param   $amount The final referral commission amount
	 * @param   $reference The reference column for the referral per the current context
	 * @param   $description A plaintext description of the referral
	 * @param   $products An array of product details
	 * @param   $data Any custom data that can be passed to and stored with the referral
	 * @return  bool
	 */
	public function insert_pending_referral( $amount = '', $reference = 0, $description = '', $products = array(), $data = array() ) {

		// get affiliate ID
		$this->affiliate_id = isset( $data['affiliate_id'] ) ? $data['affiliate_id'] : $this->get_affiliate_id( $reference, $this->context );

		if ( ! (bool) apply_filters( 'affwp_integration_create_referral', true, array( 'affiliate_id' => $this->affiliate_id, 'amount' => $amount, 'reference' => $reference, 'description' => $description, 'products' => $products, 'data' => $data ) ) ) {

			affiliate_wp()->utils->log( 'Referral not created because integration is disabled via filter' );

			return false; // Allow extensions to prevent referrals from being created
		}

		if ( affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context ) ) {

			affiliate_wp()->utils->log( sprintf( 'Referral for Reference %s already created', $reference ) );

			return false; // Referral already created for this reference
		}

		if ( empty( $amount ) && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

			affiliate_wp()->utils->log( 'Referral not created due to 0.00 amount.' );

			return false; // Ignore a zero amount referral
		}

		$visit_id = affiliate_wp()->tracking->get_visit_id();

		$args = apply_filters( 'affwp_insert_pending_referral', array(
			'amount'       => $amount,
			'reference'    => $reference,
			'description'  => $description,
			'campaign'     => affiliate_wp()->tracking->get_campaign(),
			'affiliate_id' => $this->affiliate_id,
			'visit_id'     => $visit_id,
			'products'     => ! empty( $products ) ? maybe_serialize( $products ) : '',
			'custom'       => ! empty( $data ) ? maybe_serialize( $data ) : '',
			'context'      => $this->context
		), $amount, $reference, $description, $this->affiliate_id, $visit_id, $data, $this->context );

		$referral_id = affiliate_wp()->referrals->add( $args );

		if ( $referral_id ) {
			affiliate_wp()->utils->log( sprintf( 'Pending Referral #%d created successfully.', $referral_id ) );
		} else {
			affiliate_wp()->utils->log( 'Pending referral could not be created due to an error.' );
		}

		return $referral_id;

	}

	/**
	 * Completes a referal. Used when orders are marked as completed
	 *
	 * @access  public
	 * @since   1.0
	 * @param   $reference|$referral The reference column for the referral to complete per the current context or a complete referral object
	 * @return  bool
	 */
	public function complete_referral( $reference_or_referral = 0 ) {

		if ( empty( $reference_or_referral ) ) {

			affiliate_wp()->utils->log( 'Empty $reference_or_referral parameter given during complete_referral()' );

			return false;
		}

		if( is_object( $reference_or_referral ) ) {

			$referral = affwp_get_referral( $reference_or_referral );

			if ( empty( $referral ) ) {

				affiliate_wp()->utils->log( 'Referral could not be retrieved during complete_referral(). Value given: ' . print_r( $reference_or_referral, true ) );

				return false;
			}

		} else {

			$referral = affiliate_wp()->referrals->get_by( 'reference', $reference_or_referral, $this->context );

			if ( empty( $referral ) ) {
				// Bail: This is a non-referral sale.
				return false;
			}
		}

		if ( empty( $referral ) ) {

			affiliate_wp()->utils->log( 'Referral could not be retrieved during complete_referral(). Value given: ' . print_r( $reference_or_referral, true ) );

			return false;
		}

		affiliate_wp()->utils->log( 'Referral retrieved successfully during complete_referral()' );

		if ( is_object( $referral ) && $referral->status != 'pending' && $referral->status != 'rejected' ) {
			// This referral has already been completed, or paid
			return false;
		}

		if ( ! apply_filters( 'affwp_auto_complete_referral', true ) ) {

			affiliate_wp()->utils->log( 'Referral not marked as complete because of affwp_auto_complete_referral filter' );

			return false;
		}

		if ( affwp_set_referral_status( $referral->referral_id, 'unpaid' ) ) {

			/**
			 * Fires when completing a referral.
			 *
			 * @param int             $referral_id The referral ID.
			 * @param \AffWP\Referral $referral    The referral object.
			 * @param string          $reference   The referral reference.
			 */
			do_action( 'affwp_complete_referral', $referral->referral_id, $referral, $referral->reference );

			affiliate_wp()->utils->log( sprintf( 'Referral #%d set to Unpaid successfully', $referral->referral_id ) );

			return true;
		}

		affiliate_wp()->utils->log( sprintf( 'Referral #%d failed to be set to Unpaid', $referral->referral_id ) );

		return false;

	}

	/**
	 * Rejects a referal. Used when orders are refunded, deleted, or voided
	 *
	 * @access  public
	 * @since   1.0
	 *
	 * @param string|\AffWP\Referral $reference_or_referral The reference column for the referral to complete
	 *                                                      per the current context or a complete referral object.
	 * @return bool Whether the referral was successfully rejected.
	 */
	public function reject_referral( $reference_or_referral = 0 ) {

		if ( empty( $reference_or_referral ) ) {

			affiliate_wp()->utils->log( 'Empty $reference_or_referral parameter given during complete_referral()' );

			return false;
		}

		if( is_object( $reference_or_referral ) ) {

			$referral = affwp_get_referral( $reference_or_referral );

		} else {

			$referral = affiliate_wp()->referrals->get_by( 'reference', $reference_or_referral, $this->context );

		}

		if ( empty( $referral ) ) {

			affiliate_wp()->utils->log( 'Referral could not be retrieved during reject_referral(). Value given: ' . print_r( $reference_or_referral, true ) );

			return false;
		}

		affiliate_wp()->utils->log( 'Referral retrieved successfully during reject_referral()' );

		if ( is_object( $referral ) && 'paid' == $referral->status ) {
			// This referral has already been paid so it cannot be rejected
			affiliate_wp()->utils->log( sprintf( 'Referral #%d not Rejected because it is already paid', $referral->referral_id ) );
			return false;
		}

		if ( is_object( $referral ) && 'pending' == $referral->status ) {
			// This referral is pending so it cannot be rejected
			affiliate_wp()->utils->log( sprintf( 'Referral #%d not Rejected because it is pending', $referral->referral_id ) );
			return false;
		}

		if ( affwp_set_referral_status( $referral->referral_id, 'rejected' ) ) {

			affiliate_wp()->utils->log( sprintf( 'Referral #%d set to Rejected successfully', $referral->referral_id ) );

			return true;

		}

		affiliate_wp()->utils->log( sprintf( 'Referral #%d failed to be set to Rejected', $referral->referral_id ) );

		return false;

	}

	/**
	 * Retrieves the ID of the referring affiliate
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int
	 */
	public function get_affiliate_id( $reference = 0 ) {
		return absint( apply_filters( 'affwp_get_referring_affiliate_id', $this->affiliate_id, $reference, $this->context ) );
	}

	/**
	 * Retrieves the email address of the referring affiliate
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_affiliate_email() {
		return affwp_get_affiliate_email( $this->get_affiliate_id() );
	}

	/**
	 * Determine if the passed email belongs to the affiliate
	 *
	 * Checks a given email address against the referring affiliate's
	 * user email and payment email addresses to prevent customers from
	 * referring themselves.
	 *
	 * @access  public
	 * @since   1.6
	 * @param   string $email
	 * @return  bool
	 */
	public function is_affiliate_email( $email, $affiliate_id = 0 ) {

		$is_affiliate_email = false;

		// allow an affiliate ID to be passed in
		if( empty( $affiliate_id ) ) {
			$affiliate_id = $this->get_affiliate_id();
		}

		// Get affiliate emails
		$user_email  = affwp_get_affiliate_email( $affiliate_id );

		$payment_email = affwp_get_affiliate_payment_email( $affiliate_id );

		// True if the email is valid and matches affiliate user email or payment email, otherwise false
		$is_affiliate_email = ( is_email( $email ) && ( $user_email === $email || $payment_email === $email ) );

		return (bool) apply_filters( 'affwp_is_customer_email_affiliate_email', $is_affiliate_email, $email, $affiliate_id );

	}

	/**
	 * Retrieves the rate and type for a specific product
	 *
	 * @since 1.2
	 * @access public
	 *
	 * @param string $base_amount      Optional. Base amount to calculate the referral amount from.
	 *                                 Default empty.
	 * @param string|int $reference    Optional. Referral reference (usually the order ID). Default empty.
	 * @param int        $product_id   Optional. Product ID. Default 0.
	 * @param int        $affiliate_id Optional. Affiliate ID.
	 * @return string Referral amount.
	 */
	public function calculate_referral_amount( $base_amount = '', $reference = '', $product_id = 0, $affiliate_id = 0 ) {

		// the affiliate ID can be optionally passed in to override the referral amount
		$affiliate_id = ! empty( $affiliate_id ) ? $affiliate_id : $this->get_affiliate_id( $reference );

		$rate = '';

		if ( ! empty( $product_id ) ) {
			$rate = $this->get_product_rate( $product_id, $args = array( 'reference' => $reference, 'affiliate_id' => $affiliate_id ) );
		}

		$amount = affwp_calc_referral_amount( $base_amount, $affiliate_id, $reference, $rate, $product_id );

		return $amount;

	}

	/**
	 * Retrieves the rate and type for a specific product
	 *
	 * @access  public
	 * @since   1.2
	 * @return  float
	*/
	public function get_product_rate( $product_id = 0, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'reference'    => '',
			'affiliate_id' => 0
		) );

		$affiliate_id = isset( $args['affiliate_id'] ) ? $args['affiliate_id'] : $this->get_affiliate_id( $args['reference'] );

		$rate = get_post_meta( $product_id, '_affwp_' . $this->context . '_product_rate', true );

		/**
		 * Filters the integration product rate.
		 *
		 * @since 1.2
		 *
		 * @param float  $rate         Product-level referral rate.
		 * @param int    $product_id   Product ID.
		 * @param array  $args         Arguments for retrieving the product rate.
		 * @param int    $affiliate_id Affilaite ID.
		 * @param string $context      Order context.
		 */
		return apply_filters( 'affwp_get_product_rate', $rate, $product_id, $args, $affiliate_id, $this->context );
	}

	/**
	 * Retrieves the product details array for the referral
	 *
	 * @access  public
	 * @since   1.6
	 * @return  array
	*/
	public function get_products( $order_id = 0 ) {
		return array();
	}

	/**
	 * Write log message
	 *
	 * @since 1.8
	 */
	public function log( $message = '' ) {

		affiliate_wp()->utils->log( $message );

	}

}
