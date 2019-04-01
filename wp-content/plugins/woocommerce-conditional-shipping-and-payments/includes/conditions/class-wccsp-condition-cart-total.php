<?php
/**
 * WC_CSP_Condition_Cart_Total class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart Total Condition.
 *
 * @class    WC_CSP_Condition_Cart_Total
 * @version  1.2.7
 */
class WC_CSP_Condition_Cart_Total extends WC_CSP_Condition {

	public function __construct() {

		$this->id                             = 'cart_total';
		$this->title                          = __( 'Cart Total', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'shipping_countries' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'shipping_countries' );
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
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return false;
		}

		$cart_contents_total = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_cart_contents_total() : WC()->cart->cart_contents_total;
		$cart_contents_taxes = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_cart_contents_taxes() : WC()->cart->taxes;

		$cart_contents_tax   = apply_filters( 'woocommerce_csp_cart_total_condition_incl_tax', true, $data, $args ) ? array_sum( $cart_contents_taxes ) : 0.0;
		$cart_total          = $cart_contents_total + $cart_contents_tax;

		if ( $data[ 'modifier' ] === 'min' && wc_format_decimal( $data[ 'value' ] ) <= $cart_total ) {
			return sprintf( __( 'decrease your cart total below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		}

		if ( $data[ 'modifier' ] === 'max' && wc_format_decimal( $data[ 'value' ] ) > $cart_total ) {
			return sprintf( __( 'increase your cart total above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		}

		return false;
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
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return true;
		}

		$cart_contents_total = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_cart_contents_total() : WC()->cart->cart_contents_total;
		$cart_contents_taxes = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_cart_contents_taxes() : WC()->cart->taxes;

		$cart_contents_tax   = apply_filters( 'woocommerce_csp_cart_total_condition_incl_tax', true, $data, $args ) ? array_sum( $cart_contents_taxes ) : 0.0;
		$cart_total          = $cart_contents_total + $cart_contents_tax;

		if ( $data[ 'modifier' ] === 'min' && wc_format_decimal( $data[ 'value' ] ) <= $cart_total ) {
			return true;
		}

		if ( $data[ 'modifier' ] === 'max' && wc_format_decimal( $data[ 'value' ] ) > $cart_total ) {
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
			$processed_condition_data[ 'value' ]        = $posted_condition_data[ 'value' ] !== '0' ? wc_format_decimal( stripslashes( $posted_condition_data[ 'value' ] ), '' ) : 0;
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 || $processed_condition_data[ 'value' ] === 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
	}

	/**
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_ndex
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier   = '';
		$cart_total = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		} else {
			$modifier = 'max';
		}

		if ( isset( $condition_data[ 'value' ] ) ) {
			$cart_total = wc_format_localized_price( $condition_data[ 'value' ] );
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="max" <?php selected( $modifier, 'max', true ) ?>><?php echo __( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="min" <?php selected( $modifier, 'min', true ) ?>><?php echo __( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value">
			<input type="text" class="wc_input_price short" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $cart_total; ?>" placeholder="" step="any" min="0"/>
		</div>
		<?php
	}
}
