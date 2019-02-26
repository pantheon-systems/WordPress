<?php
/**
 * WC_CSP_Condition_Package_Weight class
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
 * Package Weight Condition.
 *
 * @class   WC_CSP_Condition_Package_Weight
 * @version 1.1.0
 */
class WC_CSP_Condition_Package_Weight extends WC_CSP_Condition {

	public function __construct() {

		$this->id                            = 'package_weight';
		$this->title                         = __( 'Package Weight', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions = array( 'shipping_methods' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return false;
		}

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return false;
		}

		$pkg_weight = 0;

		foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

			$product = $cart_item_data[ 'data' ];

			if ( ! $product->needs_shipping() ) {
				continue;
			}

			$cart_item_weight = $product->get_weight();

			if ( $cart_item_weight ) {
				$pkg_weight += $cart_item_weight * $cart_item_data[ 'quantity' ];
			}
		}

		if ( $data[ 'modifier' ] === 'min' && $data[ 'value' ] <= $pkg_weight ) {
			return sprintf( __( 'decrease the package weight below %1$s%2$s', 'woocommerce-conditional-shipping-and-payments' ), $data[ 'value' ], get_option( 'woocommerce_weight_unit' ) );
		}

		if ( $data[ 'modifier' ] === 'max' && $data[ 'value' ] > $pkg_weight ) {
			return sprintf( __( 'increase the package weight above %1$s%2$s', 'woocommerce-conditional-shipping-and-payments' ), $data[ 'value' ], get_option( 'woocommerce_weight_unit' ) );
		}

		return false;
	}

	/**
	 * Evaluate if a condition field is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return true;
		}

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return true;
		}

		$pkg_weight = 0;

		foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

			$product = $cart_item_data[ 'data' ];

			if ( ! $product->needs_shipping() ) {
				continue;
			}

			$cart_item_weight = $product->get_weight();

			if ( $cart_item_weight ) {
				$pkg_weight += $cart_item_weight * $cart_item_data[ 'quantity' ];
			}
		}

		if ( $data[ 'modifier' ] === 'min' && $data[ 'value' ] <= $pkg_weight ) {
			return true;
		}

		if ( $data[ 'modifier' ] === 'max' && $data[ 'value' ] > $pkg_weight ) {
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
			$processed_condition_data[ 'value' ]        = $posted_condition_data[ 'value' ] !== '0' ? floatval( stripslashes( $posted_condition_data[ 'value' ] ) ) : 0;
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 || $processed_condition_data[ 'value' ] === 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
	}

	/**
	 * Get package weight condition content for admin global restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier       = '';
		$package_weight = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( isset( $condition_data[ 'value' ] ) ) {
			$package_weight = $condition_data[ 'value' ];
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
			<input type="number" class="wc_input_decimal short" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $package_weight; ?>" placeholder="" step="any" min="0"/>
		</div>
		<?php
	}
}
