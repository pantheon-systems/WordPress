<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Functionality that is enabled when the Checkout Field Editor plugin is activated.
 *
 * Plugin URL: https://woocommerce.com/products/woocommerce-checkout-field-editor/
 *
 * Class WC_Zapier_Checkout_Field_Editor
 */
class WC_Zapier_Checkout_Field_Editor {

	/**
	 * Option names that store the Checkout Field editor field specification(s).
	 *
	 * @var array
	 */
	private $checkout_field_sections = array(
		'wc_fields_billing',
		'wc_fields_shipping',
		'wc_fields_additional',
	);

	/**
	 * Trigger keys that the checkout field editor data should be added to.
	 *
	 * @var array
	 */
	private $trigger_keys = array(
		'wc.new_order', // New Order
		'wc.order_status_change' // New Order Status Change
	);

	/**
	 * Constructor
	 */
	public function __construct() {

		foreach ( $this->trigger_keys as $trigger_key ) {
			add_filter( "wc_zapier_data_{$trigger_key}", array( $this, 'order_data_override' ), 10, 4 );
		}

		foreach ( $this->checkout_field_sections as $field_section_name ) {
			add_action( "update_option_{$field_section_name}", array( $this, 'checkout_fields_updated' ), 10, 0 );
		}

	}

	/**
	 * When sending WooCommerce Order data to Zapier, also send any additional checkout fields
	 * that have been created by the Checkout Field Editor plugin.
	 *
	 * @param             array  $order_data Order data that will be overridden.
	 * @param WC_Zapier_Trigger  $trigger Trigger that initiated the data send.
	 *
	 * @return mixed
	 */
	public function order_data_override( $order_data, WC_Zapier_Trigger $trigger ) {

		foreach ( $this->checkout_field_sections as $field_section_name ) {
			$field_specification = get_option( $field_section_name, array() );
			foreach ( $field_specification as $field_name => $field_data ) {
				if ( $field_data['enabled'] && ! isset( $order_data[$field_name] ) ) {
					if ( $trigger->is_sample() ) {
						// We're sending sample data.
						// Send the label of the custom checkout field as the field's value.
						$order_data[$field_name] = $field_data['label'];
					} else {
						// We're sending real data.
						// Send the saved value of this checkout field.
						// If the order doesn't contain this custom field, an empty string will be used as the value.
						$order_data[$field_name] = get_post_meta( $order_data['id'], $field_name, true );
					}

				}
			}
		}

		return $order_data;
	}

	/**
	 * Executed whenever the checkout field definitions are updated/saved.
	 *
	 * Schedule the feed refresh to occur asynchronously.
	 *
	 */
	public function checkout_fields_updated( ) {
		WC_Zapier::resend_sample_data_async( $this->trigger_keys );
	}

}