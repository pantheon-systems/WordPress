<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Zapier_Trigger_New_Customer extends WC_Zapier_Trigger {

	public function __construct() {

		// Prefix the trigger key with wc. to denote that this is a trigger that relates to a WooCommerce order
		$this->trigger_key         = 'wc.new_customer';

		$this->trigger_title       = __( 'New Customer', 'wc_zapier' );

		$checkout_signup_enabled = get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) == 'yes' ? true : false;
		$my_account_signup_enabled = get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' ? true : false;

		$this->trigger_description = __( 'Triggers if a customer chooses to register for an account.', 'wc_zapier' );
		if ( $checkout_signup_enabled ) {
			$this->trigger_description .= __( '<br />Occurs if a customer registers during the checkout process when placing an order.', 'wc_zapier' );
		}

		if ( $my_account_signup_enabled ) {
			$this->trigger_description .= __( '<br />Occurs if a customer registers via the my account page.', 'wc_zapier' );
		}

		// Registration is completely disabled, so show a warning message
		if ( !$checkout_signup_enabled && !$my_account_signup_enabled ) {
			$this->trigger_description .= sprintf( __( '<br />Warning: this trigger can only occur if your <a href="%s">WooCommerce settings</a> have the <em>Enable registration on the "Checkout" page</em> and/or <em>Enable registration on the "My Account" page</em> setting(s) enabled.', 'wc_zapier' ), admin_url( 'admin.php?page=wc-settings&tab=account' ) ) . '</span>';
		}

		$this->sort_order = 2;

		// WooCommerce action(s)
		// This hook accepts 3 parameters, but we only need the first one.
		// The first parameter is the customer ID (an integer).
		// The second parameter is an array of the new customer's data, which we don't need.
		// The third parameter is a boolean for whether or not a new password was generated, which we don't need.
		$this->actions['woocommerce_created_customer'] = 1;

		parent::__construct();
	}

	public function assemble_data( $args, $action_name ) {

		if ( $this->is_sample() ) {
			// The webhook/trigger is being tested.
			// Send the store's most recent customer, or if that doesn't exist then send the currently logged in user's details.
			$customers = get_users( 'role=customer&orderby=ID&order=DESC&number=1' );
			if ( empty( $customers ) ) {
				// Use the currently logged in user's details
				$customer_id = wp_get_current_user()->ID;
			} else {
				// Use previous customer
				$customer_id = $customers[0]->ID;
			}
		} else {
			$customer_id = $args[0];
		}

		$customer_data = get_user_by( 'id', $customer_id );

		if ( ! $customer_data ) {
			// No user/customer information found
			return false;
		}

		$customer = array();

		// Gather customer's data so it can be sent to Zapier
		$customer['id']              = $customer_data->ID;
		$customer['first_name']      = $customer_data->first_name;
		$customer['last_name']       = $customer_data->last_name;
		$customer['email_address']   = $customer_data->user_email;
		$customer['username']        = $customer_data->user_login;
		$customer['paying_customer'] = (bool) $customer_data->paying_customer;

		// Important: the following fields WILL be empty if this customer hasn't placed an order yet, or hasn't added address details to their account
		$woocommerce_usermeta_fields = array(
				'billing_first_name',
				'billing_last_name',
				'billing_company',
			// 'billing_address', Only available for orders via WC_Orders::get_billing_address()
				'billing_email',
				'billing_phone',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_postcode',
				'billing_country', // Two letter country code
				'billing_country_name', // Country Name
				'billing_state',
				'billing_state_name',
				'shipping_first_name',
				'shipping_last_name',
				'shipping_company',
			// 'shipping_address', Only available for orders via WC_Orders::get_shipping_address()
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_postcode',
				'shipping_country', // Two letter country code
				'shipping_country_name', // Country Name
				'shipping_state',
				'shipping_state_name'
		);

		$customer_meta = get_user_meta( $customer_id );

		foreach ( $woocommerce_usermeta_fields as $woocommerce_usermeta_field ) {
			$customer[ $woocommerce_usermeta_field ] = isset( $customer_meta[$woocommerce_usermeta_field][0] ) ? $customer_meta[$woocommerce_usermeta_field][0] : '';
		}

		// Country name conversions
		if ( !empty( $customer['billing_country'] ) ) {
			$customer['billing_country_name'] = WC()->countries->countries[$customer['billing_country']];
		}
		if ( !empty( $customer['shipping_country'] ) ) {
			$customer['shipping_country_name'] = WC()->countries->countries[$customer['shipping_country']];
		}

		// State name conversions
		if ( !empty( $customer['billing_state'] ) && !empty( $customer['billing_country'] ) ) {
			$customer['billing_state_name'] = WC()->countries->states[$customer['billing_country']][$customer['billing_state']];
		}
		if ( !empty( $customer['shipping_state'] ) && !empty( $customer['shipping_country'] ) ) {
			$customer['shipping_state_name'] = WC()->countries->states[$customer['shipping_country']][$customer['shipping_state']];
		}

		WC_Zapier()->log( "Assembled customer data.", $customer['id'], 'Customer' );

		return $customer;

	}

}
