<?php

/**
 * Retrieve affiliate meta field for a affiliate.
 *
 * @param   int    $affiliate_id  Affiliate ID.
 * @param   string $meta_key      The meta key to retrieve.
 * @param   bool   $single        Whether to return a single value.
 * @return  mixed                 Will be an array if $single is false. Will be value of meta data field if $single is true.
 *
 * @access  public
 * @since   1.6
 */
function affwp_get_affiliate_meta( $affiliate_id = 0, $meta_key = '', $single = false ) {
	return affiliate_wp()->affiliate_meta->get_meta( $affiliate_id, $meta_key, $single );
}

/**
 * Add meta data field to a affiliate.
 *
 * @param   int    $affiliate_id  Affiliate ID.
 * @param   string $meta_key      Metadata name.
 * @param   mixed  $meta_value    Metadata value.
 * @param   bool   $unique        Optional, default is false. Whether the same key should not be added.
 * @return  bool                  False for failure. True for success.
 *
 * @access  public
 * @since   1.6
 */
function affwp_add_affiliate_meta( $affiliate_id = 0, $meta_key = '', $meta_value, $unique = false ) {
	return affiliate_wp()->affiliate_meta->add_meta( $affiliate_id, $meta_key, $meta_value, $unique );
}

/**
 * Update affiliate meta field based on affiliate ID.
 *
 * @param   int    $affiliate_id  Affiliate ID.
 * @param   string $meta_key      Metadata key.
 * @param   mixed  $meta_value    Metadata value.
 * @param   mixed  $prev_value    Optional. Previous value to check before removing.
 * @return  bool                  False on failure, true if success.
 *
 * @access  public
 * @since   1.6
 */
function affwp_update_affiliate_meta( $affiliate_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
	return affiliate_wp()->affiliate_meta->update_meta( $affiliate_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Remove metadata matching criteria from a affiliate.
 *
 * @param   int    $affiliate_id  Affiliate ID.
 * @param   string $meta_key      Metadata name.
 * @param   mixed  $meta_value    Optional. Metadata value.
 * @return  bool                  False for failure. True for success.
 *
 * @access  public
 * @since   1.6
 */
function affwp_delete_affiliate_meta( $affiliate_id = 0, $meta_key = '', $meta_value = '' ) {
	return affiliate_wp()->affiliate_meta->delete_meta( $affiliate_id, $meta_key, $meta_value );
}
