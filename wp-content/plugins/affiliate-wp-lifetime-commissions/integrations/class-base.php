<?php

class Affiliate_WP_Lifetime_Commissions_Base {

	public $context;
	public $email;
	public $customer;

	public function __construct() {

		// filter the affiliate ID
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'get_affiliate_id' ), 99, 3 );

		// Create a customer record when user accounts are registered
		add_action( 'user_register', array( $this, 'create_customer_on_register' ), 10 );

		$this->init();

	}

	/**
	 * Gets things started
	 *
	 * This is for integration-specific initiations and declarations
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function init() {}


	/***********************************************************************
	 * The following methods are typically overwritten by each integration *
	 **********************************************************************/


	/**
	 * Retrieve the email address of a customer from the referrence (order, payment, etc)
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $reference = 0 ) {
		return is_user_logged_in() ? wp_get_current_user()->user_email : '';
	}



	/*************************************************************************
	 * The following methods should typically not be changed by integrations *
	 *************************************************************************/

	public function get_affiliate_id( $affiliate_id, $reference, $context ) {

		if ( $this->context !== $context ) {
			return $affiliate_id;
		}

		if ( ! $this->is_lifetime_customer( $reference ) ) {
			return $affiliate_id;
		}

		$lc_affiliate_id = $this->customer->get_canonical_affiliate_id();

		if ( ! $this->is_lifetime_length_valid( $lc_affiliate_id ) ) {

			affiliate_wp()->utils->log( 'Referral failed to be created. Lifetime limit reached.' );

			return $affiliate_id;
		}

		if ( ! $this->can_receive_lifetime_commissions( $lc_affiliate_id ) ) {
			return $affiliate_id;
		}

		$this->filter_affiliate_rates();

		return $lc_affiliate_id;

	}

	public function filter_affiliate_rates() {

		// filter the affiliate rate
		add_filter( 'affwp_get_affiliate_rate', array( $this, 'set_lifetime_rate' ), 10, 4 );

		// set affiliate rate type
		add_filter( 'affwp_get_affiliate_rate_type', array( $this, 'set_lifetime_rate_type' ), 10, 2 );

		// set a flag for lifetime referrals
		add_filter( 'affwp_insert_pending_referral', array( $this, 'set_lifetime_referral_flag' ), 10, 8 );

	}

	public function is_lifetime_customer( $reference = 0 ) {

		$ret         = false;
		$this->email = $this->get_email( $reference );

		if ( $this->email ) {

			$this->customer = affwp_get_customer( $this->email );

			if ( $this->customer && $this->customer->get_canonical_affiliate_id() ) {

				$ret = true;

			}

		}

		return apply_filters( 'affwp_lc_is_lifetime_customer', $ret, $this->email, $reference );

	}

	/**
	 * Set lifetime referrals rate type
	 *
	 * @since 1.2
	 */
	public function set_lifetime_rate_type( $type, $affiliate_id ) {

		// per-affiliate lifetime referral rate type
		$lifetime_affiliate_rate_type = $this->get_affiliate_lifetime_referral_rate_type( $affiliate_id );

		// lifetime referral rate type from Affiliates -> Settings -> Integrations
		$lifetime_referral_rate_type = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_referral_rate_type' );

		if ( $lifetime_affiliate_rate_type ) {
			$type = $lifetime_affiliate_rate_type;
		} elseif ( $lifetime_referral_rate_type ) {
			$type = $lifetime_referral_rate_type;
		}

		return $type;

	}

	/**
	 * Set a flag for lifetime referrals
	 *
	 * @since 1.2.1
	 */
	public function set_lifetime_referral_flag( $args, $amount, $reference, $description, $affiliate_id, $visit_id, $data, $context ) {

		// create our array of data
		$data = array( 'lifetime_referral' => true );

		// custom data already exists
		if ( $args['custom'] ) {
			// unserialize it so we can add our value to the array
			$args['custom'] = maybe_unserialize( $args['custom'] );

			// merge the two arrays together
			$data = array_merge( $data, $args['custom'] );
		}

		// serialize the $data array
		$args['custom'] = maybe_serialize( $data );

		return $args;

	}

	/**
	 * Determine whether lifetime referral rates are enabled
	 *
	 * @since 1.2
	 */
	public function has_lifetime_referral_rates() {

		$enabled = affiliate_wp()->settings->get( 'lifetime_commissions_enable_lifetime_referral_rates' );

		if ( $enabled ) {
			return true;
		}

		return false;

	}

	/**
	 * Get the affiliate's lifetime rate.
	 *
	 * @since 1.2
	 *
	 * @param $affiliate_id
	 *
	 * @return float
	 */
	public function get_lifetime_rate( $affiliate_id ) {

		// lifetime referral rates must be enabled
		if ( ! $this->has_lifetime_referral_rates() ) {
			return false;
		}

		// get global lifetime rate
		$rate = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_referral_rate' );

		// get per affiliate rate
		$lifetime_affiliate_rate = $this->get_affiliate_lifetime_referral_rate( $affiliate_id );

		if ( ! empty( $lifetime_affiliate_rate ) ) {
			$rate = $lifetime_affiliate_rate;
		}

		$type = affwp_get_affiliate_rate_type( $affiliate_id );

		if ( $type === 'percentage' ) {
			$rate /= 100;
		}

		/**
		 * Filter the lifetime rate for an affiliate.
		 *
		 * This could be used in the future to provide a per affiliate lifetime rate.
		 *
		 * @since 1.2
		 *
		 * @param float $rate
		 * @param int   $affiliate_id
		 */
		$rate = apply_filters( 'affwp_lc_lifetime_referral_rate', $rate, $affiliate_id );

		return $rate;
	}

	/**
	 * Get the lifetime referral rate for an affiliate
	 *
	 * @since 1.2
	 */
	public function get_affiliate_lifetime_referral_rate( $affiliate_id = 0 ) {

		// get per affiliate rate
		$rate = affwp_get_affiliate_meta( $affiliate_id, 'affwp_lc_lifetime_referral_rate', true );

		if ( $rate ) {
			return $rate;
		}

		return false;

	}

	/**
	 * Get the lifetime referral rate type for an affiliate
	 *
	 * @since 1.2
	 */
	public function get_affiliate_lifetime_referral_rate_type( $affiliate_id = 0 ) {

		// get per affiliate rate type
		$rate_type = affwp_get_affiliate_meta( $affiliate_id, 'affwp_lc_lifetime_referral_rate_type', true );

		if ( $rate_type ) {
			return $rate_type;
		}

		return false;

	}

	/**
	 * Get the lifetime length for an affiliate
	 *
	 * @since 1.3
	 */
	public function get_affiliate_lifetime_length( $affiliate_id = 0 ) {

		// get per affiliate lifetime length
		$length = affwp_get_affiliate_meta( $affiliate_id, 'affwp_lc_lifetime_length', true );

		if ( $length !== '' ) {
			return $length;
		}

		return false;

	}

	/**
	 * Change the affiliate's rate if lifetime commissions rate is set for the affiliate
	 *
	 * @since 1.2
	 */
	public function set_lifetime_rate( $rate, $affiliate_id, $type, $reference ) {

		$lifetime_rate = $this->get_lifetime_rate( $affiliate_id );

		// has lifetime rate
		if ( $lifetime_rate ) {
			$rate = $lifetime_rate;
		}

		return $rate;
	}

	/**
	 * Can the affiliate receive lifetime commissions?
	 *
	 * @since  1.0
	 * @todo add to affiliate meta table and provide backwards compatibility
	 */
	public function can_receive_lifetime_commissions( $affiliate_id = 0 ) {

		// get global setting
		$global_lifetime_commissions_enabled = affiliate_wp()->settings->get( 'lifetime_commissions' );

		// all affiliates can earn lifetime commissions
		if ( $global_lifetime_commissions_enabled ) {
			return true;
		}

		return (bool) affwp_get_affiliate_meta( $affiliate_id, 'affwp_lc_enabled', true );

	}

	/**
	 * Get an affiliate's ID from a customer's email address
	 *
	 * @since  1.0
	 * @param  string $email The customer's email address
	 * @return bool
	 *
	 */
	public function get_affiliate_id_from_customer_email( $email = '' ) {

		if ( ! $email ) {
			return;
		}

		$customer = affwp_get_customer( $email );

		if ( $customer ) {
			return $customer->get_canonical_affiliate_id();
		}

		return false;
	}

	/**
	 * Creates a customer record if referred when user account is registered
	 *
	 * @since 1.2.1
	 * @param int $user_id
	 */
	public function create_customer_on_register( $user_id ) {

		// Check if a new customer can be automatically linked to an affiliate on registration.
		if ( ! affiliate_wp()->settings->get( 'lifetime_commissions_link_customers_on_registration' ) ) {
			return;
		}

		// get referring affiliate ID
		$referring_affiliate_id = affiliate_wp()->tracking->get_affiliate_id();

		if ( $referring_affiliate_id ) {

			$user = get_userdata( $user_id );

			// store the affiliate ID with the user.
			$customer = affwp_get_customer( $user->user_email );

			if ( ! $customer ) {

				$args = array(
					'affiliate_id' => $referring_affiliate_id,
					'first_name'   => $user->first_name,
					'last_name'    => $user->last_name,
					'user_id'      => $user->ID,
					'email'        => $user->user_email,
					'ip'           => affiliate_wp()->tracking->get_ip()
				);

				affwp_add_customer( $args );

			}

		}

	}

	/**
	 * Get all customers for an affiliate
	 *
	 * @since  1.3
	 * @param  int   $affiliate_id The affiliate ID
	 * @return array The customer IDs
	 */
	public function get_customers_for_affiliate( $affiliate_id ) {

		global $wpdb;

		$table        = affiliate_wp()->customer_meta->table_name;
		$customers    = array();
		$customer_ids = $wpdb->get_col( $wpdb->prepare( "SELECT affwp_customer_id FROM {$table} WHERE meta_key = 'affiliate_id' AND meta_value = %d ORDER BY meta_id ASC;", $affiliate_id ) );
		$customer_ids = array_map( 'absint', $customer_ids );

		if ( ! empty( $customer_ids ) ) {

			$customer_ids = array_unique( $customer_ids );

			foreach ( $customer_ids as $customer_id ) {
				$customers[] = affwp_get_customer( $customer_id );
			}

		}

		return $customers;

	}

	/**
	 * Get the lifetime commission expiration length
	 *
	 * @since  1.3
	 * @param  int $affiliate_id The affiliate ID
	 * @return int $days The number of days lifetime commissions can be created
	 */
	public function get_lifetime_length( $affiliate_id = 0 ) {

		$length = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_length', 0 );

		// get per affiliate lifetime length
		$lifetime_affiliate_length = $this->get_affiliate_lifetime_length( $affiliate_id );

		if ( $lifetime_affiliate_length !== false ) {
			$length = $lifetime_affiliate_length;
		}

		/**
		 * Filter the lifetime commissions length for an affiliate.
		 *
		 * This could be used in the future to provide a per affiliate lifetime expiration length.
		 *
		 * @since 1.3
		 *
		 * @param int $length The number of days lifetime commissions can be created
		 * @param int $affiliate_id The affiliate ID
		 */
		return apply_filters( 'affwp_lc_expiration_length', $length, $affiliate_id );

	}

	/**
	 * Check if the lifetime commissions length hasn't been reached
	 *
	 * @since  1.3
	 * @param  int $affiliate_id The affiliate ID
	 * @return bool
	 */
	public function is_lifetime_length_valid( $affiliate_id = 0 ) {

		$length = $this->get_lifetime_length( $affiliate_id );

		if ( $length !== '' ) {

			if ( $length == 0 ) {
				$lifetime_commissions_length = '1970-01-01 00:00:00';
			} else {
				$lifetime_commissions_length = date( 'Y-m-d H:i:s', strtotime( '-' . $length . ' days' ) );
			}

			$registration_date = date( 'Y-m-d H:i:s', strtotime( $this->customer->date_created ) );

			if ( $registration_date < $lifetime_commissions_length ) {

				return false;

			}

		}

		return true;

	}

}
