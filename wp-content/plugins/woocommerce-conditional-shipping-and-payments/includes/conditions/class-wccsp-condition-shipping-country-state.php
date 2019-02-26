<?php
/**
 * WC_CSP_Condition_Shipping_Country_State class
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
 * Selected Shipping Country/State Condition.
 *
 * @class   WC_CSP_Condition_Shipping_Country_State
 * @version 1.1.1
 */
class WC_CSP_Condition_Shipping_Country_State extends WC_CSP_Condition {

	public function __construct() {

		$this->id                             = 'shipping_country';
		$this->title                          = __( 'Shipping Country', 'woocommerce-conditional-shipping-and-payments' );
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

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$shipping_destinations = array();

		if ( isset( $args[ 'package' ] ) ) {
			$shipping_destinations[] = array( 'country' => $args[ 'package' ][ 'destination' ][ 'country' ], 'state' => $args[ 'package' ][ 'destination' ][ 'state' ] );
		} else {

			$shipping_packages = WC()->shipping->get_packages();

			if ( ! empty( $shipping_packages ) ) {
				foreach ( $shipping_packages as $shipping_package ) {
					$shipping_destinations[] = array( 'country' => $shipping_package[ 'destination' ][ 'country' ], 'state' => $shipping_package[ 'destination' ][ 'state' ] );
				}
			} else {
				$shipping_destinations[] = array( 'country' => WC()->customer->get_shipping_country(), 'state' => WC()->customer->get_shipping_state() );
			}
		}

		foreach ( $shipping_destinations as $shipping_destination ) {

			$shipping_country = $shipping_destination[ 'country' ];
			$shipping_state   = $shipping_destination[ 'state' ];

			if ( $data[ 'modifier' ] === 'in' && in_array( $shipping_country, $data[ 'value' ] ) ) {

				if ( empty( $data[ 'states' ][ $shipping_country ] ) ) {
					return sprintf( __( 'select a different shipping %s', 'woocommerce-conditional-shipping-and-payments' ), __( 'Country', 'woocommerce-conditional-shipping-and-payments' ) );
				} elseif ( in_array( $shipping_state, $data[ 'states' ][ $shipping_country ] ) ) {
					$locale     = WC()->countries->get_country_locale();
					$state_type = isset( $locale[ $shipping_country ][ 'state' ][ 'label' ] ) ? $locale[ $shipping_country ][ 'state' ][ 'label' ] : __( 'State / County', 'woocommerce' );
					return sprintf( __( 'select a different shipping %s', 'woocommerce-conditional-shipping-and-payments' ), $state_type );
				}

			} elseif ( $data[ 'modifier' ] === 'not-in' ) {

				if ( ! in_array( $shipping_country, $data[ 'value' ] ) ) {
					return sprintf( __( 'select a different shipping %s', 'woocommerce-conditional-shipping-and-payments' ), __( 'Country', 'woocommerce-conditional-shipping-and-payments' ) );
				} elseif ( in_array( $shipping_country, $data[ 'value' ] ) && ! empty( $data[ 'states' ][ $shipping_country ] ) && ! in_array( $shipping_state, $data[ 'states' ][ $shipping_country ] ) ) {
					$locale     = WC()->countries->get_country_locale();
					$state_type = isset( $locale[ $shipping_country ][ 'state' ][ 'label' ] ) ? $locale[ $shipping_country ][ 'state' ][ 'label' ] : __( 'State / County', 'woocommerce' );
					return sprintf( __( 'select a different shipping %s', 'woocommerce-conditional-shipping-and-payments' ), $state_type );
				}
			}
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
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$shipping_country = WC()->customer->get_shipping_country();
		$shipping_state   = WC()->customer->get_shipping_state();

		if ( $data[ 'modifier' ] === 'in' && in_array( $shipping_country, $data[ 'value' ] ) ) {

			if ( empty( $data[ 'states' ][ $shipping_country ] ) ) {
				return true;
			} elseif ( in_array( $shipping_state, $data[ 'states' ][ $shipping_country ] ) ) {
				return true;
			}

		} elseif ( $data[ 'modifier' ] === 'not-in' ) {
			if ( ! in_array( $shipping_country, $data[ 'value' ] ) ) {
				return true;
			} elseif ( $shipping_state && in_array( $shipping_country, $data[ 'value' ] ) && ! empty( $data[ 'states' ][ $shipping_country ] ) && ! in_array( $shipping_state, $data[ 'states' ][ $shipping_country ] ) ) {
				return true;
			}
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
			$processed_condition_data[ 'value' ]        = array_map( 'stripslashes', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( ! empty( $posted_condition_data[ 'exclude_states' ] ) && $posted_condition_data[ 'exclude_states' ] === 'specific' && ! empty( $posted_condition_data[ 'states' ] ) ) {

				$processed_condition_data[ 'states' ] = array();

				$country_states = array_map( 'stripslashes', $posted_condition_data[ 'states' ] );

				foreach ( $country_states as $country_state_key ) {
					$country_state_key = explode( ':', $country_state_key );
					$country_key       = current( $country_state_key );
					$state_key         = end( $country_state_key );

					if ( in_array( $country_key, $processed_condition_data[ 'value' ] ) ) {
						$processed_condition_data[ 'states' ][ $country_key ][] = $state_key;
					}
				}
			}

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get shipping countries condition content for product-level restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$countries = array();
		$states    = array();
		$modifier  = '';

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$countries = $condition_data[ 'value' ];
		}

		if ( ! empty( $condition_data[ 'states' ] ) ) {
			$states = $condition_data[ 'states' ];
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		$shipping_countries = WC()->countries->get_shipping_countries();

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
					<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				</select>
			</div>
			<div class="condition_value select-field">
				<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" style="width:80%;" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select shipping countries&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $shipping_countries as $key => $val ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $countries ), true, false ).'>' . $val . '</option>';
						}
					?>
				</select>
				<span class="form_row">
					<a class="wccsp_select_all button" href="#"><?php _e( 'Select all', 'woocommerce' ); ?></a>
					<a class="wccsp_select_none button" href="#"><?php _e( 'Select none', 'woocommerce' ); ?></a>
				</span>
			</div>
		</div>

		<div class="condition_row_inner">
			<div class="condition_modifier exclude_states">
				<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][exclude_states]">
					<?php
						echo '<option value="all" ' . selected( empty( $states ), true, false ) . '>' . __( 'all States', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';
						echo '<option value="specific" ' . selected( ! empty( $states ), true, false ) . '>' . __( 'specific States', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';
					?>
				</select>
			</div>
			<div class="condition_value excluded_states select-field" <?php echo empty( $states ) ? 'style="display:none;"': ''; ?>><?php
				if ( ! empty( $countries ) ) {
					?><select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][states][]" style="width:80%;" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select States / Regions&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
						<?php
						if ( ! empty( $countries ) ) {
							foreach ( $countries as $country_key ) {

								if ( ! isset( $shipping_countries[ $country_key ] ) ) {
									continue;
								}

								$country_value = $shipping_countries[ $country_key ];

								if ( $country_states = WC()->countries->get_states( $country_key ) ) {
									echo '<optgroup label="' . esc_attr( $country_value ) . '">';
										foreach ( $country_states as $state_key => $state_value ) {
											echo '<option value="' . esc_attr( $country_key ) . ':' . $state_key . '"';
											if ( ! empty( $states[ $country_key ] ) && in_array( $state_key, $states[ $country_key ] ) ) {
												echo ' selected="selected"';
											}
											echo '>' . $country_value . ' &mdash; ' . $state_value . '</option>';
										}
									echo '</optgroup>';
								}
							}
						}
						?>
					</select>
					<span class="form_row">
						<a class="wccsp_select_all button" href="#"><?php _e( 'Select all', 'woocommerce' ); ?></a>
						<a class="wccsp_select_none button" href="#"><?php _e( 'Select none', 'woocommerce' ); ?></a>
					</span>
					<?php
				} else {
					?><span class="condition_description"><?php
					 echo __( 'To select specific States/Regions, first add some countries with States/Regions and then save your changes.', 'woocommerce-conditional-shipping-and-payments' );
					?></span><?php
				}
			?></div>
		</div><?php
	}
}
