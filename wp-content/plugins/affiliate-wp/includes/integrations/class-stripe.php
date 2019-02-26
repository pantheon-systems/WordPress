<?php

class Affiliate_WP_Stripe extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	 */
	public function init() {

		$this->context = 'stripe';

		add_action( 'simpay_subscription_created', array( $this, 'insert_referral' ) );
		add_action( 'simpay_charge_created', array( $this, 'insert_referral' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}


	/**
	 * Create a referral during stripe form submission if customer was referred
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function insert_referral( $object ) {

		if( $this->was_referred() ) {

			switch( $object->object ) {

				case 'subscription' :

					$this->log( 'Processing referral for Stripe subscription.' );

					$stripe_amount = ! empty( $object->plan->trial_period_days ) ? 0 : $object->plan->amount;
					$currency      = $object->plan->currency;
					$description   = $object->plan->name;
					$mode          = $object->plan->livemode;

					break;

				case 'charge' :
				default :

					if( did_action( 'simpay_subscription_created' ) ) {

						$this->log( 'insert_referral() short circuited because simpay_subscription_created already fired.' );

						return; // This was a subscription purchase and we've already processed the referral creation
					}


					$this->log( 'Processing referral for Stripe charge.' );

					$stripe_amount = $object->amount;
					$currency      = $object->currency;
					$description   = ! empty( $object->description ) ? $object->description : '';
					$mode          = $object->livemode;

					break;

			}

			if( $this->is_zero_decimal( $currency ) ) {
				$amount = $stripe_amount;
			} else {
				$amount = round( $stripe_amount / 100, 2 );
			}

			if( is_object( $object->customer ) && ! empty( $object->customer->email ) ) {
				$email = $object->customer->email;
			} else {
				if ( isset( $_POST['stripeEmail'] ) ) {

					// WP Simple Pay < 3.0
					$email = sanitize_text_field( $_POST['stripeEmail'] );
				} elseif ( isset( $_POST['simpay_stripe_email'] ) ) {

					// WP Simple Pay >= 3.0
					$email = sanitize_text_field( $_POST['simpay_stripe_email'] );
				}
			}

			if( $this->is_affiliate_email( $email, $this->affiliate_id ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return;

			}

			$referral_total = $this->calculate_referral_amount( $amount, $object->id );
			$referral_id    = $this->insert_pending_referral( $referral_total, $object->id, $description, array(), array( 'livemode' => $mode ) );

			if( $referral_id ) {

				$this->log( 'Pending referral created successfully during insert_referral()' );

				if( $this->complete_referral( $object->id ) ) {

					$this->log( 'Referral completed successfully during insert_referral()' );

					return;

				}

				$this->log( 'Referral failed to be set to completed with complete_referral()' );

			} else {

				$this->log( 'Pending referral failed to be created during insert_referral()' );

			}

		}

	}

	/**
	 * Determine if this is a zero decimal currency
	 *
	 * @access public
	 * @since  2.0
	 * @param  $currency String The currency code
	 * @return bool
	 */
	public function is_zero_decimal( $currency ) {

		$is_zero = array(
			'BIF',
			'CLP',
			'DJF',
			'GNF',
			'JPY',
			'KMF',
			'KRW',
			'MGA',
			'PYG',
			'RWF',
			'VND',
			'VUV',
			'XAF',
			'XOF',
			'XPF',
		);

		return in_array( strtoupper( $currency ), $is_zero );
	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'stripe' != $referral->context ) {

			return $reference;

		}

		$test = '';

		if( ! empty( $referral->custom ) ) {
			$custom = maybe_unserialize( $referral->custom );
			$test   = empty( $custom['livemode'] ) ? 'test/' : '';
		}

		$endpoint = false !== strpos( $reference, 'sub_' ) ? 'subscriptions' : 'payments';

		$url = 'https://dashboard.stripe.com/' . $test . $endpoint  . '/' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}

if ( class_exists( 'Stripe_Checkout' ) || class_exists( 'Stripe_Checkout_Pro' ) || class_exists( '\SimplePay\Core\SimplePay' ) ) {
	new Affiliate_WP_Stripe;
}
