<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class WC_Zapier_Trigger_Subscription extends WC_Zapier_Trigger_Order {

	/**
	 * @var WC_Subscription instance
	 */
	protected $wc_subscription;

	/**
	 * The sample WooCommerce subscription data that is sent to Zapier as sample data.
	 *
	 * @return array
	 */
	public function get_sample_data() {
		$subscription = parent::get_sample_data();

		// Add additional subscription-specific fields to the standard Order fields
		$subscription['start_date']        = date('c', time() - 28 * DAY_IN_SECONDS);
		$subscription['trial_end_date']    = date('c', time() - 21 * DAY_IN_SECONDS);
		$subscription['next_payment_date'] = date('c', time() + 1 * DAY_IN_SECONDS );
		$subscription['end_date ']         = date('c', time() + 1 * YEAR_IN_SECONDS );
		$subscription['last_payment_date'] = date('c', time() - 1 * DAY_IN_SECONDS );
		$subscription['billing_period']    = 'day';
		$subscription['billing_interval']  = '1';

		$subscription['completed_payment_count'] = '20';
		$subscription['failed_payment_count'] = '0';

		$subscription['line_items'][0]['type'] = 'subscription';

		$subscription['view_url'] = 'https://example.com/my-account/view-subscription/123';

		return $subscription;
	}

	/**
	 * The WooCommerce Subscription hooks/actions that we use, provide the WC_Subscription object as the first parameter.
	 *
	 * This object can't reliably be serialized (which wp-cron requires), so instead convert it to a plain old subscription ID,
	 * and assemble_data() can convert it back to an object later.
	 *
	 * @param string $action_name
	 * @param array  $arguments
	 */
	public function __call( $action_name, array $arguments ) {
		if ( isset( $arguments[0] ) && is_a( $arguments[0], 'WC_Subscription' ) ) {
			$arguments[0] = $arguments[0]->get_id();
		}
		parent::__call( $action_name, $arguments );
	}

	public function assemble_data( $args, $action_name ) {

		if ( $this->is_sample() ) {

			// The webhook/trigger is being tested.
			// Send the store's most recent subscription, or if that doesn't exist then send the static hard-coded sample order data

			$subscriptions = wcs_get_subscriptions( array(
				'subscriptions_per_page'   => 1,
				'orderby' => 'start_date',
				'order'   => 'DESC',
			) );

			if ( ! $subscriptions || empty( $subscriptions ) ) {
				// No existing subscriptions found, so send static hard-coded order sample data
				return $this->get_sample_data();
			}

			$args[0] = array_shift( $subscriptions );

		}


		if ( is_a( $args[0], 'WC_Subscription' ) ) {
			// The first argument is the subscription object - unlikely due to the conversion to a subscription ID in WC_Zapier_Trigger_Subscription::__call() above
			$this->wc_subscription = $args[0];
		} else if ( is_numeric( $args[0] ) ) {
			// The first argument is a subscription ID
			$this->wc_subscription = wcs_get_subscription( absint( $args[0] ) );
		} else {
			WC_Zapier()->log( 'Unknown Subscription argument $args[0]: ' . var_dump( $args[0] ), null, 'Subscription' );
		}

		$new_status = '';
		$previous_status = '';

		if ( 'woocommerce_subscription_status_updated' == $action_name ) {
			$new_status      = $args[1];
			$previous_status = $args[2];
		}

		if ( empty( $new_status ) ) {
			$new_status = $this->wc_subscription->get_status();
		}

		// Compile the subscription details/data that will be sent to Zapier

		// WooCommerce Subscriptions are WooCommerce Orders, but with a few extra attributes.


		// Retrieve the basic "order" information first
		$orderargs    = array( $this->wc_subscription->get_id() );
		$subscription = parent::assemble_data( $orderargs, $action_name );

		$subscription['status']          = $new_status;
		$subscription['status_previous'] = $previous_status;


		// Now add the Subscription-specific information
		$subscription['start_date']        = WC_Zapier::format_date( $this->wc_subscription->get_date_created() );
		$subscription['trial_end_date']    = WC_Zapier::format_date( $this->wc_subscription->get_date( 'trial_end_date' ) );
		$subscription['next_payment_date'] = WC_Zapier::format_date( $this->wc_subscription->get_date( 'next_payment_date' ) );
		$subscription['end_date']          = WC_Zapier::format_date( $this->wc_subscription->get_date( 'end_date' ) );
		$subscription['last_payment_date'] = WC_Zapier::format_date( $this->wc_subscription->get_date( 'last_order_date_paid' ) );
		$subscription['billing_period']    = $this->wc_subscription->get_billing_period();
		$subscription['billing_interval']  = $this->wc_subscription->get_billing_interval();

		$subscription['completed_payment_count'] = $this->wc_subscription->get_completed_payment_count();
		// TODO: Add completed payment total?
		$subscription['failed_payment_count'] = $this->wc_subscription->get_failed_payment_count();
		// TODO: Add failed payment total?

		$subscription['view_url'] = $this->wc_subscription->get_view_order_url();

		return $subscription;



	}

	protected function data_sent_to_feed( WC_Zapier_Feed $feed, $result, $action_name, $arguments, $num_attempts = 0 ) {

		$note = '';

		if ( 1 == $num_attempts  ) {
			// Successful on the first attempt
			$note .= sprintf( __( 'Subscription sent to Zapier via the <a href="%1$s">%2$s</a> Zapier feed.', 'wc_zapier' ), $feed->edit_url(), $feed->title() );
		} else {
			// It took more than 1 attempt so add that to the note
			$note .= sprintf( __( 'Subscription sent to Zapier via the <a href="%1$s">%2$s</a> Zapier feed after %3$d attempts.', 'wc_zapier' ), $feed->edit_url(), $feed->title(), $num_attempts );
		}

		$note .= sprintf( __( '<br ><br />Trigger:<br />%1$s<br />%2$s', 'wc_zapier' ), $feed->trigger()->get_trigger_title(), "<small>{$action_name}</small>" );

		$note .= $this->data_sent_note_suffix( $feed, $result, $action_name, $arguments, $num_attempts );
		// Add a private note to this order
		$this->wc_subscription->add_order_note( $note );

		WC_Zapier()->log( $note, $this->wc_subscription->get_id(), 'Subscription' );

	}

}
