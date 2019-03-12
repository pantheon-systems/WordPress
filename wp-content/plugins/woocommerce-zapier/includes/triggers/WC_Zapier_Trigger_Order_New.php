<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Zapier_Trigger_Order_New extends WC_Zapier_Trigger_Order {

	/**
	 * A (temporary) list of order IDs that have been sent to Zapier via the 'New Order' trigger.
	 * @var array
	 */
	protected static $orders_sent_to_zapier_via_new_order = array();

	public function __construct() {
		$this->status_slug = 'processing';
		$this->trigger_title       = __( 'New Order', 'wc_zapier' );

		$this->trigger_description = __( 'Triggers when an order\'s payment is completed, or when an order has its status changed to Processing.', 'wc_zapier' );

		// Prefix the trigger key with wc. to denote that this is a trigger that relates to a WooCommerce order
		$this->trigger_key         = 'wc.new_order';

		$this->sort_order = 1;

		// WooCommerce action(s) that apply to this trigger event

		// Add the supported hooks for all the possible payment status transitions to processing
		foreach ( WC_Zapier::get_order_statuses() as $status ) {
			if ( $status != $this->status_slug )
				// This hook accepts 1 parameter (the order ID).
				$this->actions[ "woocommerce_order_status_{$status}_to_{$this->status_slug}" ] = 1;
		}

		// Ensure virtual-only orders also get sent to Zapier (they typically skip the "processing" status and go straight to "completed")
		// This hook accepts 1 parameter (the order ID).
		$this->actions['woocommerce_payment_complete'] = 1;

		if ( $this->send_asynchronously() ) {
			add_filter( 'wc_zapier_scheduled_event', array( $this, 'wc_zapier_scheduled_event' ), 10, 3 );
			add_filter( 'wc_zapier_data_sent_to_zapier_successfully', array(
				$this,
				'wc_zapier_data_sent_to_zapier_successfully'
			), 10, 4 );
		}

		parent::__construct();
	}

	/**
	 * Executed whenever an order is scheduled to be sent to Zapier (via any Trigger).
	 *
	 * @param string            $action_name Hook name
	 * @param array             $arguments   Hook arguments
	 * @param WC_Zapier_Trigger $trigger     The trigger initiating the send
	 */
	public function wc_zapier_scheduled_event ( $action_name, $arguments, $trigger ) {
		_deprecated_function( 'WC_Zapier_Trigger_Order_New::wc_zapier_scheduled_event', '1.7' );

		$this->maybe_record_send( $action_name, $arguments, $trigger );
	}


	/**
	 * Executed whenever an order is successfully sent to Zapier (via any Trigger).
	 *
	 * @param string            $json_data   The JSON data sent to Zapier
	 * @param WC_Zapier_Trigger $trigger     The trigger initiating the send
	 * @param string            $action_name Hook name
	 * @param array             $arguments   Hook arguments
	 */
	public function wc_zapier_data_sent_to_zapier_successfully( $json_data, $trigger, $action_name, $arguments ) {
		_deprecated_function( 'WC_Zapier_Trigger_Order_New::wc_zapier_data_sent_to_zapier_successfully', '1.7' );

		$this->maybe_record_send( $action_name, $arguments, $trigger );
	}

	/**
	 * Attempt to prevent an order from being sent to Zapier twice via the "New Order" trigger when it has the
	 * payment_complete() function called on it.
	 *
	 * In that case, it will be sent via the 'woocommerce_order_status_pending_to_processing' hook, and not by the
	 * 'woocommerce_payment_complete' hook.
	 *
	 * See also the should_schedule_event() function below.
	 *
	 * @param string            $action_name Hook name
	 * @param array             $arguments   Hook arguments
	 * @param WC_Zapier_Trigger $trigger     The trigger initiating the send
	 */
	protected function maybe_record_send( $action_name, $arguments, $trigger ) {
		_deprecated_function( 'WC_Zapier_Trigger_Order_New::maybe_record_send', '1.7' );

		if ( $trigger->is_sample() || !is_a( $trigger, 'WC_Zapier_Trigger_Order_New' ) ) {
			// Data being sent is either sample data, or it isn't being sent by a "New Order" trigger.
			return;
		}

		if ( false !== strpos( $action_name, 'woocommerce_order_status_' )  && array_key_exists( $action_name , $this->actions ) ) {
			$order_id = intval( $arguments[0] );
			self::$orders_sent_to_zapier_via_new_order[ $order_id ] = true;
			WC_Zapier()->log( "Recorded Order #$order_id as being sent/scheduled to Zapier via a New Order trigger." );
			WC_Zapier()->log( "  hook: $action_name" );
			WC_Zapier()->log( "  arguments: " . print_r( $arguments, true ) );
		}

	}

	/**
	 * Ensure that an order isn't sent to Zapier twice as part of the "New Order" trigger event.
	 *
	 * This can happen for orders that the WC_Order::payment_complete() function called on it (most automatic payment gateways),
	 * where the order is also set to processing (rather than completed).
	 *
	 * The most common scenario is for orders that contain physical products that need shipping.
	 *
	 * Orders that contain virtual & downloadable products only will not have this problem because their status never
	 * gets set to processing.
	 *
	 * See also the maybe_record_send() function above.
	 *
	 * @param string $action_name Hook name
	 * @param array  $args        Hook arguments
	 *
	 * @return bool true
	 */
	protected function should_schedule_event( $action_name, $args ) {

		// Check to see if this order ID has already been sent to Zapier via the order status change to processing (woocommerce_order_status_pending_to_processing hook)
		// If it has, don't send it again via the 'woocommerce_payment_complete' hook as well
		$order_id = intval( $args[0] );
		if ( isset( self::$orders_sent_to_zapier_via_new_order[ $order_id ] ) ) {
			WC_Zapier()->log( "Order #$order_id has already been sent to Zapier via a New Order trigger, so don't send it a second time via the '$action_name' hook.\n\n" );
			return false;
		}
		// Otherwise we'll send it to Zapier

		return parent::should_schedule_event( $action_name, $args );
	}

}