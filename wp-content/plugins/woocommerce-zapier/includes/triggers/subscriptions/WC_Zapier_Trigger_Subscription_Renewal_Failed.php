<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Zapier_Trigger_Subscription_Renewal_Failed extends WC_Zapier_Trigger_Subscription {

	public function __construct() {
		$this->trigger_title = __( 'Subscription Renewal Failed', 'wc_zapier' );

		$this->trigger_description = __( 'Triggers when a subscription renewal payment fails.', 'wc_zapier' );

		// Prefix the trigger key with wc. to denote that this is a trigger that relates to a WooCommerce order
		$this->trigger_key = 'wc.subscription_renewal_failed';

		$this->sort_order = 6;

		// This hook accepts 2 parameters, but we only need the first one (the subscription ID).
		// The first parameter is the WC_Subscription object, which we need (and is converted to a subscription ID).
		// The second parameter is a WC_Order object, which we don't need.
		$this->actions['woocommerce_subscription_renewal_payment_failed'] = 1;

		parent::__construct();
	}

}