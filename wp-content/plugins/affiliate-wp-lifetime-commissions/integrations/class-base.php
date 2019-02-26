<?php

class Affiliate_WP_Lifetime_Commissions_Base {

	public $context;

	public function __construct() {

		// filter the affiliate ID
		add_filter( 'affwp_get_referring_affiliate_id', array( $this, 'set_affiliate_id' ), 10, 3 );

		// link the customer with the affiliate
		// this fires when the referral's status changes from "pending" to "unpaid"
		add_action( 'affwp_complete_referral', array( $this, 'link_to_affiliate' ), 10, 3 );

		add_action( 'affwp_register_user', array( $this, 'link_affiliate_at_registration' ), 10, 3 );

		$this->init();

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
		$lifetime_referral_rate_type  = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_referral_rate_type' );

		if ( $lifetime_affiliate_rate_type ) {
			$type = $lifetime_affiliate_rate_type;
		} elseif( $lifetime_referral_rate_type ) {
			$type = $lifetime_referral_rate_type;
		}

		return $type;

	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function init() {
		// intentionally left blank
	}

	/**
	 * Retrieves the user's email or ID depending on the referral's context
	 *
	 * @param string $get what to retrieve
	 * @param int $reference Payment reference number
	 *
	 * @since 1.0
	 */
	public function get( $get = '', $reference = 0, $context ) {
		// intentionally left blank
	}

	/**
	 * Sets the affiliate ID to the lifetime affiliate's ID
	 * $affiliate_id will generally be 0 since no affiliate will be tracked.
	 * This changes the affiliate ID from 0 to their lifetime affiliate ID
	 *
	 * @since 1.1
	 */
	public function set_affiliate_id( $affiliate_id = 0, $payment_id = 0, $context ) {

		if ( $this->context !== $context ) {
			return $affiliate_id;
		}

		// integrations that use tracked affiliate coupons bypass this filter and pass their affiliate ID directly into the insert pending referral function

		// get the lifetime affiliate that is connected to the customer

		$lifetime_affiliate_user_id = $this->get_users_lifetime_affiliate( $payment_id, $context );

		if ( $lifetime_affiliate_user_id ) {

			$lifetime_affiliate = (int) $this->get_affiliate_id( $lifetime_affiliate_user_id );

			if ( $this->can_receive_lifetime_commissions( $lifetime_affiliate ) ) {

				// filter the affiliate rate
				add_filter( 'affwp_get_affiliate_rate', array( $this, 'set_lifetime_rate' ), 10, 4 );

				// set affiliate rate type
				add_filter( 'affwp_get_affiliate_rate_type', array( $this, 'set_lifetime_rate_type' ), 10, 2 );

				// set a flag for lifetime referrals
				add_filter( 'affwp_insert_pending_referral', array( $this, 'set_lifetime_referral_flag' ), 10, 8 );

				// lifetime affiliate found
				$affiliate_id = $lifetime_affiliate;

			}

		}

		return $affiliate_id;

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
	 * Change the affiliate's rate if lifetime commissions rate is set for the affiliate
	 *
	 * @since 1.2
	 */
	public function set_lifetime_rate( $rate, $affiliate_id, $type, $reference ) {

		$lifetime_rate = $this->get_lifetime_rate( $affiliate_id );

		// has lifetime rate
		if ( $lifetime_rate ) {
			// connected user has a lifetime affiliate therefore it is a lifetime commissions purchase
			$is_lifetime_purchase = $this->get_users_lifetime_affiliate( $reference, $this->context );

			if ( $is_lifetime_purchase ) {
				$rate = $lifetime_rate;
			}
		}

		return $rate;
	}

	/**
	 * Link the customer and affiliate together
	 * Runs when the referral is complete and the referral status is updated to "unpaid"
	 *
	 * @return void
	 * @since  1.0
	 * @todo make the link when a pending referral is created instead
	 */
	public function link_to_affiliate( $referral_id, $referral, $reference ) {

		// get the context
		$context = $referral->context;

		// return early if the context does not match
		if ( $this->context !== $context ) {
			return;
		}

		// get the affiliate's ID
		$affiliate_id = $referral->affiliate_id;

		// get the user's ID (for logged in users)
		// We don't simply get the currently logged in user ID since the link between customer and affiliate could happen at a later point
		$user_id = $this->get( 'user_id', $reference, $context );

		// get the user's email address from the referral
		// if a user changes their email address at checkout, we'll add this to the $customer_emails array
		$user_email = $this->get( 'email', $reference, $context );

		// is the customer already linked to an affiliate?
		if ( $this->is_customer_linked( $user_id ) ) {

			// if the customer is already linked and logged in, but they used a different email address, add it to the affiliate
			$this->maybe_add_email_to_affiliate( $affiliate_id, $user_email );

			// customer is already linked to an affiliate, no need to go any further
			if ( ! (bool) apply_filters( 'affwp_lc_update_affiliate', false, $affiliate_id, $user_id, $referral, $context ) ) {
				return true; // Allow extensions to update lifetime affiliates
			}

		}

		// continue the process of linking the customer to the affiliate


		// affiliate is allowed to receive lifetime commissions
		if ( $this->can_receive_lifetime_commissions( $affiliate_id ) ) {

			/**
			 * Customer is logged in
			 */
			if ( $user_id && $user_id != -1 ) {

				// get an array of customer's lifetime emails that they use/have used
				$customer_emails = $this->get_customer_emails( $user_id );

				// add the customer's WordPress user ID to the affiliate if it doesn't already exist
				$this->maybe_add_customer_id_to_affiliate( $user_id, $affiliate_id );

				// store the affiliate's ID against the user
				$this->add_affiliate_id_to_customer( $user_id, $affiliate_id );

				// customer has used an email address that they previously have not used
				if ( ! in_array( $user_email, $customer_emails ) ) {

					// store the email address to the customer's known list of email addresses used
					$this->add_email_to_customer( $user_id, $user_email );

					// store it to affiliate's user meta
					$this->maybe_add_email_to_affiliate( $affiliate_id, $user_email );

				}

				// add all the customers emails to the affiliate
				// useful if the customer is de-linked and linked to a new affiliate at a later point
				if ( $customer_emails ) {

					foreach ( $customer_emails as $email ) {

						// loop through and delete all associated email addresses for the old affiliate
						$this->delete_customer_email_from_affiliate( $affiliate_id, $email );

						// Add a customer's email address to the affiliate
						$this->maybe_add_email_to_affiliate( $affiliate_id, $email );

					}

				}

			} else {

				/**
				 * Customer is making a guest purchase (no user account)
				 */

				// add customer's email to affiliate's user meta for future guest purchases
				$this->maybe_add_email_to_affiliate( $affiliate_id, $user_email );

			}

		}

		// customer is now linked to affiliate, huzzah!
	}

	/**
	 * Checks to see if a customer is already linked to an affiliate
	 *
	 * @since 1.1
	 * @return boolean true is customer is linked to affiliate, false otherwise
	 */
	public function is_customer_linked( $user_id = 0 ) {

		if ( $user_id ) {

			// customer has meta key so must be linked
			if ( get_user_meta( $user_id, 'affwp_lc_affiliate_id', true ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Delete a customer email from an affiliate
	 *
	 * @since 1.1
	 */
	public function delete_customer_email_from_affiliate( $affiliate_id = 0, $email = '' ) {

		if ( ! $affiliate_id ) {
			return;
		}

		if ( $email ) {

			// the WordPress user ID of the affiliate
			$affiliate_user_id = affwp_get_affiliate_user_id( $affiliate_id );

			delete_user_meta( $affiliate_user_id, 'affwp_lc_customer_email', $email );

		}

	}

	/**
	 * Get email addresses belonging to a customer
	 * A customer might have more than 1 email address they have used
	 *
	 * @since 1.1
	 * @return array An array of emails
	 */
	public function get_customer_emails( $user_id = 0 ) {

		// get an array of customer's lifetime emails that they use/have used
		$customer_emails = get_user_meta( $user_id, 'affwp_lc_email' );

		return $customer_emails;
	}


	/**
	 * Maybe add email to affiliate
	 *
	 * @since 1.1
	 */
	public function maybe_add_email_to_affiliate( $affiliate_id = 0, $user_email = '' ) {

		if ( ! in_array( $user_email, $this->get_affiliates_customer_emails( $affiliate_id ) ) ) {
			$this->add_email_to_affiliate( $affiliate_id, $user_email );
		}

	}

	/**
	 * Add a customer's email address to the affiliate
	 *
	 * @since 1.1
	 * @todo store email address in affiliate meta table and provide backwards compatibility
	 */
	public function add_email_to_affiliate( $affiliate_id = 0, $user_email = '' ) {

		// the WordPress user ID of the affiliate
		$affiliate_user_id = affwp_get_affiliate_user_id( $affiliate_id );

		add_user_meta( $affiliate_user_id, 'affwp_lc_customer_email', $user_email );

	}

	/**
	 * Add a customer's email address to their user meta
	 * These emails will be checked against an affiliate when making a guest purchase
	 *
	 * @since 1.1
	 */
	public function add_email_to_customer( $user_id = 0, $email = '' ) {

		add_user_meta( $user_id, 'affwp_lc_email', $email );

	}

	/**
	 * Add an affiliate's ID to the customer, in user meta
	 * A user can only have 1 affiliate assigned to them
	 *
	 * @since 1.1
	 */
	public function add_affiliate_id_to_customer( $user_id = 0, $affiliate_id = 0 ) {

		// store the affiliate ID with the user.
		update_user_meta( $user_id, 'affwp_lc_affiliate_id', $affiliate_id );

	}

	/**
	 * Add the customer's WordPress user ID to the affiliate, if it doesn't already exist
	 *
	 * @since 1.1
	 */
	public function maybe_add_customer_id_to_affiliate( $user_id = 0, $affiliate_id = 0 ) {

		// add the customer's WordPress user ID to the affiliate if it doesn't already exist
		if ( ! in_array( $user_id, $this->get_affiliates_customer_ids( $affiliate_id ) ) ) {
			$this->add_customer_id_to_affiliate( $user_id, $affiliate_id );
		}

	}

	/**
	 * Add the customer's WordPress user ID to the affiliate, if it doesn't already exist
	 *
	 * @since 1.1
	 */
	public function add_customer_id_to_affiliate( $user_id = 0, $affiliate_id = 0 ) {

		// the WordPress user ID of the affiliate
		$affiliate_user_id = affwp_get_affiliate_user_id( $affiliate_id );

		// add the customer's WordPress user ID to the affiliate if it doesn't already exist
		add_user_meta( $affiliate_user_id, 'affwp_lc_customer_id', $user_id );

	}

	/**
	 * Get array of affiliate's customer email addresses
	 *
	 * @return array customer emails linked to an affiliate
	 * @since  1.0
	 */
	public function get_affiliates_customer_emails( $affiliate_id = 0 ) {

		if ( ! $affiliate_id ) {
			return;
		}

		$emails = get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_lc_customer_email' );

		return (array) $emails;

	}

	/**
	 * Get array of affiliate's customer IDs
	 *
	 * @return array customer ids linked to an affiliate
	 * @since  1.0
	 */
	public function get_affiliates_customer_ids( $affiliate_id = 0 ) {

		if ( ! $affiliate_id ) {
			return;
		}

		$ids = get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_lc_customer_id' );

		return (array) $ids;

	}

	/**
	 * Retrieves the affiliate ID that should receive a commission
	 *
	 * If a user is logged in, the affiliate ID is looked up via the user's ID
	 * If a user is not logged in, the affiliate ID is looked up via the user's email address
	 *
	 * @return absint $lifetime_affiliate_id ID of affiliate linked to user, false otherwise
	 * @since  1.0
	 */
	public function get_users_lifetime_affiliate( $reference = 0, $context = '' ) {

		// get ID of currently logged in user
		$user_id = get_current_user_id();

		// user is logged in.
		if ( $user_id ) {

			$affiliate_id = get_user_meta( $user_id, 'affwp_lc_affiliate_id', true );

			// user has linked affiliate ID, use that
			if ( $affiliate_id ) {

				$lifetime_affiliate_id = $affiliate_id;

			} else {
				// user is a guest and has a linked affiliate but has created an account at checkout.

				// look up affiliate ID by customer email
				$customer_email_address = $this->get( 'email', $reference, $context );
				$lifetime_affiliate_id  = $this->get_affiliate_id_from_email( $customer_email_address );

				// store the lifetime affiliate ID with the affiliate's user account for later use
				if ( $lifetime_affiliate_id ) {
					update_user_meta( $user_id, 'affwp_lc_affiliate_id', $lifetime_affiliate_id );
				}

				// store their email against their new user account
				update_user_meta( $user_id, 'affwp_lc_email', $customer_email_address );
			}

		} else {
			// must not be logged in, as user ID will be 0

			// get customer's email
			$customer_email_address = $this->get( 'email', $reference, $context );

			// lookup affiliate ID by customer email
			$lifetime_affiliate_id = $this->get_affiliate_id_from_email( $customer_email_address );

		}

		if ( $lifetime_affiliate_id ) {
			return absint( affwp_get_affiliate_user_id( $lifetime_affiliate_id ) );
		}

		return false;

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

		// get user ID of affiliate
		$user_id = affwp_get_affiliate_user_id( $affiliate_id );

		// all affiliates can earn lifetime commissions
		if ( $global_lifetime_commissions_enabled ) {
			return true;
		}

		$allowed = get_user_meta( $user_id, 'affwp_lc_enabled', true );

		if ( $allowed ) {
			return true;
		}

		return false;
	}

	/**
	 * Get an affiliate's ID from a customer's email address
	 *
	 * @param $customer_email_address The customer's email address
	 * @return int affiliate's ID
	 * @since  1.0
	 */
	public function get_affiliate_id_from_email( $customer_email_address = '' ) {

		if ( ! $customer_email_address ) {
			return;
		}

		$args = array(
			'meta_key'   => 'affwp_lc_customer_email',
			'meta_value' => $customer_email_address,
			'fields'     => 'ID',
			'number'     => '1' // there will/can only be one linked customer
		);

		$users = get_users( $args );

		if ( $users ) {
			return (int) $this->get_affiliate_id( $users[0] );
		}

		return false;
	}

	/**
	 * Get an affiliate's ID from user's ID
	 * Based on affwp_get_affiliate_id() but does not return the currently logged in affiliate ID when no user is passed in
	 *
	 * @param $user_id user ID of specified user
	 * @return int affiliate's ID
	 * @since  1.0.1
	 */
	public function get_affiliate_id( $user_id = 0 ) {

		if ( empty( $user_id ) ) {
			return false;
		}

		$affiliate = affiliate_wp()->affiliates->get_by( 'user_id', $user_id );

		if ( $affiliate ) {
			return $affiliate->affiliate_id;
		}

		return false;

	}

	/**
     * Link an affiliate to another affiliate via Lifetime Commissions if the
     * newly registered affiliate uses an affiliate's referral URL. Uses the
     * affwp_register_user action hook
	 *
	 * Supports:
	 *
	 * Affiliate Forms for Ninja Forms
	 * Affiliate Forms for Gravity Forms
	 * The default affiliate registration form
     *
     * @since 1.2.1
     */
    public function link_affiliate_at_registration( $affiliate_id, $status, $args ) {

      // get referring affiliate ID
      $referring_affiliate_id = affiliate_wp()->tracking->get_affiliate_id();

      if ( $referring_affiliate_id ) {
        // get current user since they have just been registered
        $current_user = wp_get_current_user();

        // add the customer's WordPress user ID to the affiliate if it doesn't already exist
        $this->maybe_add_customer_id_to_affiliate( $current_user->ID, $referring_affiliate_id );

        // store the affiliate's ID against the user
        $this->add_affiliate_id_to_customer( $current_user->ID, $referring_affiliate_id );

        // store the newly registered affiliate's email with the referring Affiliate
        $this->maybe_add_email_to_affiliate( $referring_affiliate_id, $args['user_email'] );
      }

    }

}
