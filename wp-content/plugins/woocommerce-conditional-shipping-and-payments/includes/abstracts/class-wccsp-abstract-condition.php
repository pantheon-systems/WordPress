<?php
/**
 * WC_CSP_Condition class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Condition class.
 *
 * @class   WC_CSP_Condition
 * @version 1.3.0
 */
class WC_CSP_Condition {

	/** @var string Unique ID for the condition - must be set. */
	var $id;

	/** @var string Condition title - must be set. */
	var $title;

	/** @var array Supported global restriction ids - must be set. */
	var $supported_global_restrictions = array();

	/** @var array Supported global restriction ids - must be set. */
	var $supported_product_restrictions = array();

	/**
	 * Validate, process and return condition fields. Must be overriden to save condition data.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {
		return false;
	}

	/**
	 * Get condition admin html content. Must be overriden to display condition fields.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {
		return '';
	}

	/**
	 * Evaluate if a condition field is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {
		return true;
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {
		return false;
	}

	/**
	 * Get title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Indicates the existence of fields for a restriction type and scope.
	 *
	 * @param  string $restriction_id
	 * @param  string $scope
	 * @return boolean
	 */
	public function has_fields( $restriction_id, $scope = 'global' ) {

		if ( $scope === 'global' ) {
			if ( in_array( $restriction_id, $this->get_supported_global_restrictions() ) ) {
				return true;
			}
		} elseif ( $scope === 'product' ) {
			if ( in_array( $restriction_id, $this->get_supported_product_restrictions() ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get supported global restriction ids.
	 *
	 * @return array
	 */
	public function get_supported_global_restrictions() {

		return apply_filters( 'woocommerce_csp_condition_get_supported_global_restrictions', $this->supported_global_restrictions, $this->id );
	}

	/**
	 * Get supported product restriction ids.
	 *
	 * @return array
	 */
	public function get_supported_product_restrictions() {

		return apply_filters( 'woocommerce_csp_condition_get_supported_product_restrictions', $this->supported_product_restrictions, $this->id );
	}

	/**
	* Merge strings to create resolution message.
	*
	* @param  array  $qualifying_items
	* @return string $merged_titles
	* @since  1.3.0
	*/
	protected function merge_titles( $qualifying_items ) {

		$merged_titles = sprintf( __( '&quot;%s&quot;', 'woocommerce-conditional-shipping-and-payments' ), $qualifying_items[ 0 ] );

		for ( $i = 1; $i < count( $qualifying_items ) - 1; $i++ ) {

			/* translators: Used to stitch together product names */
			$merged_titles = sprintf( __( '%1$s, &quot;%2$s&quot;', 'woocommerce-conditional-shipping-and-payments' ), $merged_titles, $qualifying_items[ $i ] );
		}

		if ( count( $qualifying_items ) > 1 ) {
			$merged_titles = sprintf( __( '%1$s and &quot;%2$s&quot;', 'woocommerce-conditional-shipping-and-payments' ), $merged_titles, end( $qualifying_items ) );
		}

		return $merged_titles;
	}

}
