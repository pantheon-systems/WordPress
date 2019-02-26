<?php
/**
 * WC_CSP_Condition_Billing_Country class
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
 * Selected Billing Country Condition.
 *
 * @class   WC_CSP_Condition_Billing_Country
 * @version 1.1.1
 */
class WC_CSP_Condition_Billing_Country extends WC_CSP_Condition {

	public function __construct() {

		$this->id                             = 'billing_country';
		$this->title                          = __( 'Billing Country', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'payment_gateways' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated)
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$billing_country = WC_CSP_Core_Compatibility::is_wc_version_gte_2_7() ? WC()->customer->get_billing_country() : WC()->customer->get_country();

		if ( $data[ 'modifier' ] === 'in' && in_array( $billing_country, $data[ 'value' ] ) ) {
			return __( 'select a different billing country', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $data[ 'modifier' ] === 'not-in' && ! in_array( $billing_country, $data[ 'value' ] ) ) {
			return __( 'select a different billing country', 'woocommerce-conditional-shipping-and-payments' );
		}

		return false;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated)
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$billing_country = WC_CSP_Core_Compatibility::is_wc_version_gte_2_7() ? WC()->customer->get_billing_country() : WC()->customer->get_country();

		if ( $data[ 'modifier' ] === 'in' && in_array( $billing_country, $data[ 'value' ] ) ) {
			return true;
		} elseif ( $data[ 'modifier' ] === 'not-in' && ! in_array( $billing_country, $data[ 'value' ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate, process and return condition field data.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( ! empty( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_map( 'stripslashes', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get billing countries condition content for restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$countries = array();
		$modifier  = '';

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$countries = $condition_data[ 'value' ];
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		$billing_countries = WC()->countries->get_allowed_countries();

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value select-field">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" style="width:80%;" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select billing countries&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
				<?php
					foreach ( $billing_countries as $key => $val ) {
						echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $countries ), true, false ).'>' . $val . '</option>';
					}
				?>
			</select>
			<span class="form_row">
				<a class="wccsp_select_all button" href="#"><?php _e( 'Select all', 'woocommerce' ); ?></a>
				<a class="wccsp_select_none button" href="#"><?php _e( 'Select none', 'woocommerce' ); ?></a>
			</span>
		</div><?php
	}
}
