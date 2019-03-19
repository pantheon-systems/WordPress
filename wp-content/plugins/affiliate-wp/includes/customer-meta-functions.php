<?php

/**
 * Retrieve customer meta field for a customer.
 *
 * @param   int    $customer_id  customer ID.
 * @param   string $meta_key      The meta key to retrieve.
 * @param   bool   $single        Whether to return a single value.
 * @return  mixed                 Will be an array if $single is false. Will be value of meta data field if $single is true.
 *
 * @access  public
 * @since   2.2
 */
function affwp_get_customer_meta( $customer_id = 0, $meta_key = '', $single = false ) {
	return affiliate_wp()->customer_meta->get_meta( $customer_id, $meta_key, $single );
}

/**
 * Add meta data field to a customer.
 *
 * @param   int    $customer_id  customer ID.
 * @param   string $meta_key      Metadata name.
 * @param   mixed  $meta_value    Metadata value.
 * @param   bool   $unique        Optional, default is false. Whether the same key should not be added.
 * @return  bool                  False for failure. True for success.
 *
 * @access  public
 * @since   2.2
 */
function affwp_add_customer_meta( $customer_id = 0, $meta_key = '', $meta_value, $unique = false ) {
	return affiliate_wp()->customer_meta->add_meta( $customer_id, $meta_key, $meta_value, $unique );
}

/**
 * Update customer meta field based on customer ID.
 *
 * @param   int    $customer_id  customer ID.
 * @param   string $meta_key      Metadata key.
 * @param   mixed  $meta_value    Metadata value.
 * @param   mixed  $prev_value    Optional. Previous value to check before removing.
 * @return  bool                  False on failure, true if success.
 *
 * @access  public
 * @since   2.2
 */
function affwp_update_customer_meta( $customer_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
	return affiliate_wp()->customer_meta->update_meta( $customer_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Remove metadata matching criteria from a customer.
 *
 * @param   int    $customer_id  customer ID.
 * @param   string $meta_key      Metadata name.
 * @param   mixed  $meta_value    Optional. Metadata value.
 * @return  bool                  False for failure. True for success.
 *
 * @access  public
 * @since   2.2
 */
function affwp_delete_customer_meta( $customer_id = 0, $meta_key = '', $meta_value = '' ) {
	return affiliate_wp()->customer_meta->delete_meta( $customer_id, $meta_key, $meta_value );
}
