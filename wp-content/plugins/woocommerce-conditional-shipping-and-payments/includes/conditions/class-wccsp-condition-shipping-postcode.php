<?php
/**
 * WC_CSP_Condition_Shipping_Postcode class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Zip Code Condition.
 *
 * @class    WC_CSP_Condition_Shipping_Postcode
 * @version  1.3.0
 */
class WC_CSP_Condition_Shipping_Postcode extends WC_CSP_Condition {

	public function __construct() {
		$this->id                             = 'zip_code';
		$this->title                          = __( 'Shipping Postcode', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods' );
		$this->supported_product_restrictions = array( 'shipping_methods' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return true;
		}

		$postcode         = wc_normalize_postcode( wc_clean( $package[ 'destination' ][ 'postcode' ] ) );
		$postcode_objects = array();

		foreach ( $data[ 'value' ] as $validation_postcode ) {
			$postcode_object                = new stdClass();
			$postcode_object->location_code = $validation_postcode;
			$postcode_object->value         = 0;
			$postcode_objects[]             = $postcode_object;
		}

		$matches = wc_postcode_location_matcher( $postcode, $postcode_objects, 'value', 'location_code' );

		if ( $data[ 'modifier' ] === 'in' && ! empty( $matches ) ) {
			return sprintf( __( 'choose a different shipping Postcode', 'woocommerce-conditional-shipping-and-payments' ), $postcode );
		}

		if ( $data[ 'modifier' ] === 'not-in' && empty( $matches ) ) {
			return sprintf( __( 'choose a valid shipping Postcode', 'woocommerce-conditional-shipping-and-payments' ), $postcode );
		}
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  string $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return true;
		}

		if ( empty( $package[ 'destination' ][ 'postcode' ] ) ) {
			return true;
		}

		$postcode         = wc_normalize_postcode( wc_clean( $package[ 'destination' ][ 'postcode' ] ) );
		$postcode_objects = array();

		foreach ( $data[ 'value' ] as $validation_postcode ) {
			$postcode_object                = new stdClass();
			$postcode_object->location_code = trim( strtoupper( str_replace( chr( 226 ) . chr( 128 ) . chr( 166 ), '...', $validation_postcode ) ) );
			$postcode_object->value         = 0;
			$postcode_objects[]             = $postcode_object;
		}

		$matches = wc_postcode_location_matcher( $postcode, $postcode_objects, 'value', 'location_code' );

		if ( $data[ 'modifier' ] === 'in' && ! empty( $matches ) ) {
			return true;
		}

		if ( $data[ 'modifier' ] === 'not-in' && empty( $matches ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( isset( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_filter( array_map( 'strtoupper', array_map( 'wc_clean', explode( "\n", $posted_condition_data[ 'value' ] ) ) ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}
	/**
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier  = '';
		$zip_codes = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$zip_codes = implode( "\n", $condition_data[ 'value' ] );
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value">
			<textarea class="input-text" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" placeholder="<?php _e( 'List 1 postcode per line&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" cols="25" rows="5"><?php echo $zip_codes; ?></textarea>
			<span class="description"><?php _e( 'Postcodes containing wildcards (e.g. CB23*) and fully numeric ranges (e.g. <code>90210...99000</code>) are also supported.', 'woocommerce' ) ?></span>
		</div>
		<?php
	}
}
