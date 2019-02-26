<?php
/**
 * WC_CSP_Condition_Customer_Role class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.1.10
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customer Role Condition.
 *
 * @class   WC_CSP_Condition_Customer_Role
 * @version 1.1.10
 */
class WC_CSP_Condition_Customer_Role extends WC_CSP_Condition {

	public function __construct() {

		$this->id                             = 'customer_role';
		$this->title                          = __( 'Customer Role', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_product_restrictions = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
		$this->supported_global_restrictions  = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
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

		$check_roles       = array();
		$restricted_roles  = array_map( 'wc_clean', $data[ 'value' ] );

		if ( is_user_logged_in() ) {
			$current_user  = wp_get_current_user();
			$check_roles   = $current_user->roles;
		} else {
			$check_roles[] = 'guest';
		}

		$identified_role = false;

		if ( ! empty( $check_roles ) ) {
			foreach ( $check_roles as $check_role ) {
				if ( in_array( $check_role, $restricted_roles ) ) {
					$identified_role = true;
					break;
				}
			}
		}

		if ( $data[ 'modifier' ] === 'in' && $identified_role ) {
			return __( 'check out using an account with elevated permissions', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $data[ 'modifier' ] === 'not-in' && ! $identified_role ) {
			return __( 'check out using an account with elevated permissions', 'woocommerce-conditional-shipping-and-payments' );
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

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$check_roles       = array();
		$restricted_roles  = array_map( 'wc_clean', $data[ 'value' ] );

		if ( is_user_logged_in() ) {
			$current_user  = wp_get_current_user();
			$check_roles   = $current_user->roles;
		} else {
			$check_roles[] = 'guest';
		}

		$identified_role = false;

		if ( ! empty( $check_roles ) ) {
			foreach ( $check_roles as $check_role ) {
				if ( in_array( $check_role, $restricted_roles ) ) {
					$identified_role = true;
					break;
				}
			}
		}

		if ( $data[ 'modifier' ] === 'in' && $identified_role ) {
			return true;
		} elseif ( $data[ 'modifier' ] === 'not-in' && ! $identified_role ) {
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

		if ( ! empty( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_map( 'wc_clean', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get categories-in-cart condition content for global restrictions.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = '';
		$roles    = array();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$roles = $condition_data[ 'value' ];
		}

		$wp_roles                 = wp_roles();
		$wp_role_names[ 'guest' ] = __( 'Guest', 'woocommerce-conditional-shipping-and-payments' );
		$wp_role_names            = array_merge( $wp_role_names, $wp_roles->get_names() );

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" style="width:80%;" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select roles&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
				<?php
					foreach ( $wp_role_names as $role_slug => $role_name )
						echo '<option value="' . $role_slug . '" ' . selected( in_array( $role_slug, $roles ), true, false ) . '>' . $role_name . '</option>';
				?>
			</select>
		</div><?php
	}
}
