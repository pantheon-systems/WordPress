<?php
/**
 * WC_CSP_Conditions class
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
 * Conditions class.
 *
 * @class   WC_CSP_Conditions
 * @version 1.3.0
 */
class WC_CSP_Conditions {

	/** @var array Array of registered condition classes. */
	public $conditions;

	public function __construct() {

		$load_conditions = apply_filters( 'woocommerce_csp_conditions', array(
			'WC_CSP_Condition_Cart_Total',
			'WC_CSP_Condition_Order_Total',
			'WC_CSP_Condition_Cart_Item_Quantity',
			'WC_CSP_Condition_Billing_Country',
			'WC_CSP_Condition_Shipping_Country_State',
			'WC_CSP_Condition_Shipping_Postcode',
			'WC_CSP_Condition_Shipping_Method',
			'WC_CSP_Condition_Cart_Category',
			'WC_CSP_Condition_Package_Category',
			'WC_CSP_Condition_Cart_Shipping_Class',
			'WC_CSP_Condition_Package_Shipping_Class',
			'WC_CSP_Condition_Package_Weight',
			'WC_CSP_Condition_Package_Item_Quantity',
			'WC_CSP_Condition_Package_Total',
			'WC_CSP_Condition_Customer',
			'WC_CSP_Condition_Customer_Role',
			'WC_CSP_Condition_Coupon_Code'
		) );

		// Load conditions.
		foreach ( $load_conditions as $condition ) {

			$condition = new $condition();

			$this->conditions[ $condition->id ] = $condition;
		}

		/*---------------------------------------------------*/
		/*  Show Condition fields.                           */
		/*---------------------------------------------------*/

		add_action( 'woocommerce_csp_admin_product_fields', array( $this, 'get_admin_condition_product_fields' ), 10, 3 );
		add_action( 'woocommerce_csp_admin_global_fields', array( $this, 'get_admin_condition_global_fields' ), 10, 3 );

		/*---------------------------------------------------*/
		/*  Process Condition fields.                        */
		/*---------------------------------------------------*/

		add_filter( 'woocommerce_csp_process_admin_product_fields', array( $this, 'process_admin_condition_product_fields' ), 10, 3 );
		add_filter( 'woocommerce_csp_process_admin_global_fields', array( $this, 'process_admin_condition_global_fields' ), 10, 3 );

		/*---------------------------------------------------*/
		/*  Print condition JS templates in footer.          */
		/*---------------------------------------------------*/

		add_action( 'admin_footer', array( $this, 'print_condition_field_scripts' ) );
	}

	/**
	 * Print condition JS templates in footer.
	 */
	public function print_condition_field_scripts() {

		if ( wp_script_is( 'wc-restrictions-writepanel', 'done' ) ) {
			$this->print_js_templates( 'product' );
		} elseif ( wp_script_is( 'wc-global-restrictions-writepanel', 'done'  ) ) {
			$this->print_js_templates( 'global' );
		}
	}

	/**
	 * Prints JS condition templates in footer.
	 *
	 * @param  string $scope
	 * @return void
	 */
	private function print_js_templates( $scope ) {

		if ( ! in_array( $scope, array( 'product', 'global' ) ) ) {
			return;
		}

		$fn_name      = 'get_admin_' . $scope . '_field_restrictions';
		$restrictions = WC_CSP()->restrictions->$fn_name();

		if ( ! empty( $restrictions ) ) {
			foreach ( $restrictions as $restriction_id => $restriction ) {

				$conditions = $this->get_supported_conditions( $restriction_id, $scope );

				if ( empty( $conditions ) ) {
					continue;
				}

				?><script type="text/template" id="tmpl-wc_csp_restriction_<?php echo esc_attr( $restriction_id ); ?>_condition_row">
					<tr class="condition_row" data-condition_index="{{{ data.condition_index }}}">
						<th class="condition_remove">
							<input type="checkbox" class="remove_condition"/>
						</th>
						<td class="condition_select"><?php
							$this->get_conditions_dropdown( $conditions, '' );
						?></td>
						<td class="condition_content">
							{{{ data.condition_content }}}
						</td>
					</tr>
				</script>

				<script type="text/template" id="tmpl-wc_csp_restriction_<?php echo esc_attr( $restriction_id ); ?>_condition_default_content"><?php
					$default_condition = reset( $conditions );
					$default_condition->get_admin_fields_html( '{{{ data.restriction_index }}}', '{{{ data.condition_index }}}', array() );
				?></script><?php

				foreach ( $conditions as $condition_id => $condition ) {

					?><script type="text/template" id="tmpl-wc_csp_restriction_<?php echo esc_attr( $restriction_id ); ?>_condition_<?php echo esc_attr( $condition_id ); ?>_content"><?php

					$condition->get_admin_fields_html( '{{{ data.restriction_index }}}', '{{{ data.condition_index }}}', array() );

					?></script><?php
				}
			}
		}
	}

	/**
	 * Get condition class by condition_id.
	 *
	 * @param  str    $condition_id
	 * @return WC_CSP_Condition
	 */
	public function get_condition( $condition_id ) {

		if ( ! empty( $this->conditions[ $condition_id ] ) ) {
			return $this->conditions[ $condition_id ];
		}

		return false;
	}

	/**
	 * Get conditions by supported restriction id and scope.
	 *
	 * @param  string $restriction_id
	 * @return array
	 */
	public function get_supported_conditions( $restriction_id = '', $scope = 'global' ) {

		$conditions = array();

		foreach ( $this->conditions as $id => $condition ) {
			if ( $restriction_id === '' || $condition->has_fields( $restriction_id, $scope ) ) {
				$conditions[ $id ] = $condition;
			}
		}

		return apply_filters( 'woocommerce_csp_get_supported_conditions', $conditions, $restriction_id, $scope );
	}

	/**
	 * Get condition fields for admin product restriction metaboxes.
	 *
	 * @param  string $restriction_id
	 * @param  int    $index
	 * @param  array  $options
	 * @return str
	 */
	public function get_admin_condition_product_fields( $restriction_id, $index, $options = array() ) {
		$this->get_admin_condition_fields( 'product', $restriction_id, $index, $options );
	}

	/**
	 * Get condition fields for admin global restriction metaboxes.
	 *
	 * @param  string $restriction_id
	 * @param  int    $index
	 * @param  array  $options
	 * @return str
	 */
	public function get_admin_condition_global_fields( $restriction_id, $index, $options = array() ) {
		$this->get_admin_condition_fields( 'global', $restriction_id, $index, $options );
	}

	/**
	 * Get condition fields for admin restriction metaboxes.
	 *
	 * @param  string $scope
	 * @param  string $restriction_id
	 * @param  int    $index
	 * @param  array  $options
	 * @return str
	 */
	private function get_admin_condition_fields( $scope, $restriction_id, $index, $options = array() ) {

		$conditions = $this->get_supported_conditions( $restriction_id, $scope );

		if ( empty( $conditions ) ) {
			return false;
		}

		?><h4><?php
			echo __( 'Conditions', 'woocommerce-conditional-shipping-and-payments' );
			echo WC_CSP_Core_Compatibility::wc_help_tip( __( 'The restriction becomes active when all defined conditions match.', 'woocommerce-conditional-shipping-and-payments' ) ); ?>
		</h4><?php

		?><table class="restriction_conditions">
			<tbody><?php

				if ( ! empty( $options[ 'conditions' ] ) ) {

					$conditions_data = $options[ 'conditions' ];

					foreach ( $conditions_data as $condition_index => $condition_data ) {

						if ( isset( $condition_data[ 'condition_id' ] ) ) {

							$condition_id = $condition_data[ 'condition_id' ];

							if ( array_key_exists( $condition_id, $conditions ) ) {

								?><tr class="condition_row">
									<th class="condition_remove">
										<input type="checkbox" class="remove_condition"/>
									</th>
									<td class="condition_select"><?php
										$this->get_conditions_dropdown( $conditions, $condition_id );
									?></td>
									<td class="condition_content"><?php
										$conditions[ $condition_id ]->get_admin_fields_html( $index, $condition_index, $condition_data );
									?></td>
								</tr><?php
							}
						}
					}
				}
			?></tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<div class="condition_add_remove">
							<button type="button" class="button button-secondary add_condition"><?php echo __( 'Add Condition', 'woocommerce-conditional-shipping-and-payments' ); ?></button>
							<button type="button" class="button button-secondary remove_conditions"><?php echo __( 'Remove Selected', 'woocommerce-conditional-shipping-and-payments' ); ?></button>
						</div>
					</td>
				</tr>
			</tfoot>
		</table><?php
	}

	/**
	 * Admin condition select dropdown.
	 *
	 * @param  int    $index
	 * @param  string $selected_id
	 * @return void
	 */
	private function get_conditions_dropdown( $conditions, $selected_id ) {

		?><select name="condition_dropdown" class="condition_type"><?php
			foreach ( $conditions as $condition_id => $condition ) {
				?><option value="<?php echo $condition_id ?>" <?php echo $condition_id === $selected_id ? 'selected="selected"' : ''; ?>><?php
					echo $condition->get_title();
				?></option><?php
			}
		?></select><?php
	}

	/**
	 * Process condition fields for admin product restriction metaboxes.
	 *
	 * @param  array  $processed_data
	 * @param  array  $posted_data
	 * @param  string $restriction_id
	 * @return array
	 */
	public function process_admin_condition_product_fields( $processed_data, $posted_data, $restriction_id ) {
		return $this->process_admin_condition_fields( 'product', $restriction_id, $processed_data, $posted_data );
	}

	/**
	 * Process condition fields for admin global restriction metaboxes.
	 *
	 * @param  array  $processed_data
	 * @param  array  $posted_data
	 * @param  string $restriction_id
	 * @return array
	 */
	public function process_admin_condition_global_fields( $processed_data, $posted_data, $restriction_id ) {
		return $this->process_admin_condition_fields( 'global', $restriction_id, $processed_data, $posted_data );
	}

	/**
	 * Process condition fields.
	 *
	 * @param  string $scope
	 * @param  string $restriction_id
	 * @param  array  $processed_data
	 * @param  array  $posted_data
	 * @return array
	 */
	private function process_admin_condition_fields( $scope, $restriction_id, $processed_data, $posted_data ) {

		$conditions = $this->get_supported_conditions( $restriction_id, $scope );

		if ( ! empty( $posted_data[ 'conditions' ] ) && ! empty( $conditions ) ) {

			$conditions_data = $posted_data[ 'conditions' ];

			$processed_data[ 'conditions' ] = array();

			foreach ( $conditions_data as $condition_key => $condition_data ) {

				if ( isset( $condition_data[ 'condition_id' ] ) ) {

					$condition_id = $condition_data[ 'condition_id' ];

					if ( array_key_exists( $condition_id, $conditions ) ) {

						if ( $processed_condition_data = $conditions[ $condition_id ]->process_admin_fields( $condition_data ) ) {
							$processed_data[ 'conditions' ][] = $processed_condition_data;
						}
					}
				}

			}
		}

		return $processed_data;
	}

	/**
	 * Evaluate if a condition is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		if ( isset( $data[ 'condition_id' ] ) ) {

			$condition_id = $data[ 'condition_id' ];

			if ( $condition = $this->get_condition( $condition_id ) ) {
				return $condition->check_condition( $data, $args );
			}
		}

		return true;
	}

	/**
	 * Return condition-field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		if ( isset( $data[ 'condition_id' ] ) ) {

			$condition_id = $data[ 'condition_id' ];

			if ( $condition = $this->get_condition( $condition_id ) ) {
				return $condition->get_condition_resolution( $data, $args );
			}
		}

		return false;
	}
}
