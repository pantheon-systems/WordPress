<?php
/**
 * WC_CSP_Restrict_Shipping_Methods class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Restrict Shipping Methods.
 *
 * @class   WC_CSP_Restrict_Shipping_Methods
 * @version 1.2.0
 */
class WC_CSP_Restrict_Shipping_Methods extends WC_CSP_Restriction implements WC_CSP_Checkout_Restriction {

	public function __construct() {

		$this->id                       = 'shipping_methods';
		$this->title                    = __( 'Shipping Methods', 'woocommerce-conditional-shipping-and-payments' );
		$this->description              = __( 'Restrict the available shipping methods based on product-related constraints.', 'woocommerce-conditional-shipping-and-payments' );
		$this->validation_types         = array( 'checkout' );
		$this->has_admin_product_fields = true;
		$this->supports_multiple        = true;

		$this->has_admin_global_fields  = true;
		$this->method_title             = __( 'Shipping Method Restrictions', 'woocommerce-conditional-shipping-and-payments' );

		// Remove shipping methods from packages.
		add_action( 'woocommerce_package_rates', array( $this, 'exclude_package_shipping_methods' ), 10, 2 );

		// Save global settings.
		add_action( 'woocommerce_update_options_restrictions_' . $this->id, array( $this, 'update_global_restriction_data' ) );

		// Initialize global settings.
		$this->init_form_fields();

		// Display shipping method options.
		add_action( 'woocommerce_csp_admin_shipping_method_option', array( $this, 'shipping_method_option' ), 10, 3 );

		// Shows a woocommerce error on the 'woocommerce_review_order_before_cart_contents' hook when shipping method restrictions apply.
		add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'excluded_shipping_methods_notice' ) );

		// Update checkout fields/totals on changing the State.
		add_filter( 'woocommerce_default_address_fields', array( $this, 'update_totals_on_state_change' ) );
	}

	/**
	 * Update checkout fields/totals on changing the State field.
	 *
	 * @param  array  $fields
	 * @return array
	 */
	public function update_totals_on_state_change( $fields ) {

		if ( isset( $fields[ 'state' ][ 'class' ] ) ) {
			if ( false === array_search( 'update_totals_on_change', $fields[ 'state' ][ 'class' ] ) ) {
				$fields[ 'state' ][ 'class' ][] = 'update_totals_on_change';
			}
		}

		return $fields;
	}

	/**
	 * Display shipping method options.
	 *
	 * @param  string              $method_id
	 * @param  WC_Shipping_Method  $method
	 * @param  array               $selected_methods
	 * @return void
	 */
	public function shipping_method_option( $method_id, $method, $selected_methods ) {

		global $wpdb;

		if ( WC_CSP_Core_Compatibility::is_wc_version_gte_2_6() ) {

			if ( $method->supports( 'shipping-zones' ) ) {

				echo '<optgroup label="' . $method->get_method_title() . '">';
				echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . sprintf( __( 'All &quot;%s&quot; Method Instances', 'woocommerce-conditional-shipping-and-payments' ), $method->get_method_title() ) . '</option>';

				$zones = WC_Shipping_Zones::get_zones();

				if ( ! isset( $zones[ 0 ] ) ) {
					$rest_of_world = WC_Shipping_Zones::get_zone_by();
					$zones[ 0 ]                       = $rest_of_world->get_data();
					$zones[ 0 ][ 'shipping_methods' ] = $rest_of_world->get_shipping_methods();
				}

				foreach ( $zones as $zone ) {

					if ( ! empty( $zone[ 'shipping_methods' ] ) ) {

						$zone_name = $zone[ 'zone_name' ];

						foreach ( $zone[ 'shipping_methods' ] as $instance_id => $method_instance ) {

							if ( $method_instance->id !== $method->id ) {
								continue;
							}

							$option_id    = $method_instance->get_rate_id();
							$method_title = sprintf( __( '&quot;%1$s&quot; (Instance ID: %2$s)', 'woocommerce-conditional-shipping-and-payments' ), $method_instance->get_title(), $instance_id );
							$option_name  = sprintf( __( '%1$s &ndash; %2$s', 'woocommerce-conditional-shipping-and-payments' ), $zone_name, $method_title );

							echo '<option value="' . $option_id . '" ' . selected( in_array( $option_id, $selected_methods ), true, false ) . '>' . $option_name . '</option>';
						}
					}
				}

				echo '</optgroup>';

			} else {

				if ( $method_id === 'legacy_flat_rate' ) {

					echo '<optgroup label="' . __( 'Flat Rates (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) . '">';
					echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . $method->get_title() . __( ' (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';
					$this->additional_legacy_flat_rate_options( $method, $selected_methods );
		 			echo '</optgroup>';

		 		} else {

					$is_legacy = ( 0 === strpos( $method_id, 'legacy_' ) );
					$option    = '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . $method->get_title() . ( $is_legacy ? __( ' (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) : '' ) . '</option>';
		 			echo apply_filters( 'woocommerce_csp_admin_shipping_method_option_default', $option, $method_id, $method, $selected_methods );
		 		}
			}

		} else {

			if ( $method_id === 'table_rate' && class_exists( 'WC_Shipping_Table_Rate' ) ) {
				echo '<optgroup label="' . __( 'Table Rates', 'woocommerce-conditional-shipping-and-payments' ) . '">';
				echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . __( 'Table Rate Shipping &ndash; All Rates', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';

				// Get Rates
				$table_rates = array();

				$table_rates = $wpdb->get_results( "
				SELECT rates.rate_id, rates.rate_label, rates.rate_order, rates.shipping_method_id, methods.zone_id, methods.shipping_method_order, zones.zone_name, zones.zone_order FROM {$wpdb->prefix}woocommerce_shipping_table_rates AS rates
				INNER JOIN {$wpdb->prefix}woocommerce_shipping_zone_shipping_methods AS methods ON (rates.shipping_method_id = methods.shipping_method_id)
				LEFT JOIN {$wpdb->prefix}woocommerce_shipping_zones AS zones ON (methods.zone_id = zones.zone_id)
				WHERE methods.shipping_method_type = 'table_rate'
				ORDER BY zones.zone_order ASC, methods.shipping_method_order ASC, rates.rate_order ASC
				" );

				if ( ! empty( $table_rates ) ) {

					$methods = array();

					foreach ( $table_rates as $table_rate ) {

						$table_rate_zone_name = $table_rate->zone_name ? $table_rate->zone_name : __( 'Default Zone', 'woocommerce-conditional-shipping-and-payments' );

						if ( ! array_key_exists( $table_rate->shipping_method_id, $methods ) ) {

							$table_rate_method                          = woocommerce_get_shipping_method_table_rate( $table_rate->shipping_method_id );

							$table_rate_option_id                       = 'table_rate-' . $table_rate->shipping_method_id;
							$table_rate_method_title                    = sprintf( __( '%1$s (ID: %2$s)', 'woocommerce-conditional-shipping-and-payments' ), $table_rate_method->title, $table_rate_option_id );
							$methods[ $table_rate->shipping_method_id ] = $table_rate_method_title;

							$table_rate_option_name                     = sprintf( __( 'Table Rate Shipping &ndash; %1$s: %2$s, All Rates', 'woocommerce-conditional-shipping-and-payments' ), $table_rate_zone_name, $table_rate_method_title );

							echo '<option value="' . $table_rate_option_id . '" ' . selected( in_array( $table_rate_option_id, $selected_methods ), true, false ) . '>' . $table_rate_option_name . '</option>';
						}

						$table_rate_method_title = $methods[ $table_rate->shipping_method_id ];

						$table_rate_option_id    = 'table_rate-' . $table_rate->shipping_method_id . ' : ' . $table_rate->rate_id;
						$table_rate_rate_label   = $table_rate->rate_label ? sprintf( __( '%1$s (ID: %2$s)', 'woocommerce-conditional-shipping-and-payments' ), $table_rate->rate_label, $table_rate->rate_id ) : sprintf( __( 'Unlabelled Rate (ID: %1$s)', 'woocommerce-conditional-shipping-and-payments' ), $table_rate->rate_id );

						$table_rate_option_name  = sprintf( __( 'Table Rate Shipping &ndash; %1$s: %2$s, %3$s', 'woocommerce-conditional-shipping-and-payments' ), $table_rate_zone_name, $table_rate_method_title, $table_rate_rate_label );

						echo '<option value="' . $table_rate_option_id . '" ' . selected( in_array( $table_rate_option_id, $selected_methods ), true, false ) . '>' . $table_rate_option_name . '</option>';
					}
				}

				echo '</optgroup>';

			} elseif ( $method_id === 'flat_rate_boxes' && class_exists( 'WC_Shipping_Flat_Rate_Boxes' ) ) {

				echo '<optgroup label="' . __( 'Flat Rate Boxes', 'woocommerce-conditional-shipping-and-payments' ) . '">';
				echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . __( 'Flat Rate Boxes Shipping &ndash; All Rates', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';

				// Get Box Methods
				$box_methods = array();

				$box_methods = $wpdb->get_results( "
				SELECT methods.shipping_method_id, methods.zone_id, methods.shipping_method_order, zones.zone_name, zones.zone_order FROM {$wpdb->prefix}woocommerce_shipping_zone_shipping_methods AS methods
				LEFT JOIN {$wpdb->prefix}woocommerce_shipping_zones AS zones ON (methods.zone_id = zones.zone_id)
				WHERE methods.shipping_method_type = 'flat_rate_boxes'
				ORDER BY zones.zone_order ASC, methods.shipping_method_order, methods.shipping_method_id ASC
				" );

				if ( ! empty( $box_methods ) ) {

					$methods = array();

					foreach ( $box_methods as $box_method ) {

						$flat_rate_boxes_zone_name = $box_method->zone_name ? $box_method->zone_name : __( 'Default Zone', 'woocommerce-conditional-shipping-and-payments' );

						if ( ! array_key_exists( $box_method->shipping_method_id, $methods ) ) {

							$flat_rate_boxes_method                     = woocommerce_get_shipping_method_flat_rate_boxes( $box_method->shipping_method_id );

							$flat_rate_boxes_option_id                  = 'flat_rate_boxes-' . $box_method->shipping_method_id;
							$flat_rate_boxes_method_title               = sprintf( __( '%1$s (ID: %2$s)', 'woocommerce-conditional-shipping-and-payments' ), $flat_rate_boxes_method->title, $flat_rate_boxes_option_id );
							$methods[ $box_method->shipping_method_id ] = $flat_rate_boxes_method_title;

							$flat_rate_boxes_option_name                = sprintf( __( 'Flat Rate Boxes Shipping &ndash; %1$s: %2$s', 'woocommerce-conditional-shipping-and-payments' ), $flat_rate_boxes_zone_name, $flat_rate_boxes_method_title );

							echo '<option value="' . $flat_rate_boxes_option_id . '" ' . selected( in_array( $flat_rate_boxes_option_id, $selected_methods ), true, false ) . '>' . $flat_rate_boxes_option_name . '</option>';
						}
					}
				}

				echo '</optgroup>';

			} elseif ( $method_id === 'flat_rate' ) {

				echo '<optgroup label="' . __( 'Flat Rates', 'woocommerce-conditional-shipping-and-payments' ) . '">';
				echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . $method->get_title() . '</option>';
				$this->additional_flat_rate_options( $method, $selected_methods );
				echo '</optgroup>';

			} else {

				$option = '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . $method->get_title() . '</option>';
				echo apply_filters( 'woocommerce_csp_admin_shipping_method_option_default', $option, $method_id, $method, $selected_methods );
			}
		}
	}

	/**
	 * Append additional legacy flat rate options.
	 *
	 * @param  WC_Shipping_Method  $method
	 * @param  array               $selected_methods
	 * @return void
	 */
	private function additional_legacy_flat_rate_options( $method, $selected_methods ) {

		$additional_flat_rate_options = (array) explode( "\n", $method->get_option( 'options' ) );

		foreach ( $additional_flat_rate_options as $option ) {

			$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

			if ( sizeof( $this_option ) !== 3 ) {
				continue;
			}

			$option_id = 'legacy_flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

			echo '<option value="' . esc_attr( $option_id ) . '" ' . selected( in_array( $option_id, $selected_methods ), true, false ) . '>' . $this_option[0] . __( ' (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';
		}
	}

	/**
	 * Append additional flat rate options.
	 *
	 * @param  WC_Shipping_Method  $method
	 * @param  array               $selected_methods
	 * @return void
	 */
	private function additional_flat_rate_options( $method, $selected_methods ) {

		$additional_flat_rate_options = (array) explode( "\n", $method->get_option( 'options' ) );

		foreach ( $additional_flat_rate_options as $option ) {

			$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

			if ( sizeof( $this_option ) !== 3 ) {
				continue;
			}

			$option_id = 'flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

			echo '<option value="' . esc_attr( $option_id ) . '" ' . selected( in_array( $option_id, $selected_methods ), true, false ) . '>' . $this_option[0] . '</option>';
		}
	}

	/**
	 * Declare 'admin_global_fields' type, generated by 'generate_admin_global_fields_html'.
	 *
	 * @return void
	 */
	function init_form_fields() {

		$this->form_fields = array(
			'admin_global_fields' => array(
				'type' => 'admin_global_fields'
				)
			);
	}

	/**
	 * Generates the 'admin_global_fields' field type, which is based on metaboxes.
	 *
	 * @return string
	 */
	function generate_admin_global_fields_html() {
		?><p>
			<?php echo __( 'Restrict the shipping methods available at checkout. Complex rules can be created by adding multiple restrictions. Each individual restriction becomes active when all defined conditions match.', 'woocommerce-conditional-shipping-and-payments' ); ?>
		</p><?php

		$this->get_admin_global_metaboxes_html();
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * All fields placed inside an indexed 'restriction[ $index ]' array will be passed to the 'process_admin_product_fields' function for validation.
	 *
	 * @param  int     $index
	 * @param  array   $options
	 * @param  string  $field_type
	 * @return string
	 */
	public function get_admin_fields_html( $index, $options = array(), $field_type = 'global' ) {

		$description        = '';
		$methods            = array();
		$custom_rates_input = '';
		$message            = '';
		$show_excluded      = false;

		if ( isset( $options[ 'description' ] ) ) {
			$description = $options[ 'description' ];
		}

		if ( isset( $options[ 'methods' ] ) ) {
			$methods = $options[ 'methods' ];
		}

		if ( ! empty( $options[ 'message' ] ) ) {
			$message = $options[ 'message' ];
		}

		if ( isset( $options[ 'custom_rates' ] ) ) {
			$custom_rates = $options[ 'custom_rates' ];
			if ( is_array( $custom_rates ) ) {
				$custom_rates_input = esc_attr( implode( ' ' . WC_DELIMITER . ' ', $custom_rates ) );
			}
		}

		if ( isset( $options[ 'show_excluded' ] ) && $options[ 'show_excluded' ] === 'yes' ) {
			$show_excluded = true;
		}

		$shipping_methods = WC()->shipping->load_shipping_methods();

		?>
		<p class="form-field">
			<label>
				<?php _e( 'Short Description', 'woocommerce-conditional-shipping-and-payments' ); ?>:
			</label>
			<textarea class="short_description" name="restriction[<?php echo $index; ?>][description]" id="restriction_<?php echo $index; ?>_message" placeholder="<?php _e( 'Optional short description for this rule&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" rows="1" cols="20"><?php echo $description; ?></textarea>
		</p>
		<p class="form-field">
			<label><?php _e( 'Exclude Shipping Methods', 'woocommerce-conditional-shipping-and-payments' ); ?></label>
			<select name="restriction[<?php echo $index; ?>][methods][]" style="width:80%" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select Shipping Methods&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
				<?php
					foreach ( $shipping_methods as $key => $val ) {
						do_action( 'woocommerce_csp_admin_shipping_method_option', $key, $val, $methods );
					}
				?>
			</select>
		</p>
		<p class="form-field">
			<label><?php _e( 'Exclude Shipping Rate IDs', 'woocommerce-conditional-shipping-and-payments' ); ?></label>
			<input type="text" name="restriction[<?php echo $index; ?>][custom_rates]" style="width:80%" placeholder="<?php _e( 'Shipping rate IDs to exclude, separated by &quot;|&quot;&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" value="<?php echo $custom_rates_input; ?>"/>
			<?php echo WC_CSP_Core_Compatibility::wc_help_tip( __( 'Manually enter shipping rate IDs to exclude. Useful if you are working with shipping methods that retrieve real-time rates, or if you simply require more granular control of the shipping options available during checkout. <strong>Important</strong>: Exclusion rules based on rate IDs are not necessarily upgrade-safe &ndash; use at your own risk!', 'woocommerce-conditional-shipping-and-payments' ) ); ?>
		</p>
		<p class="form-field">
			<label>
				<?php _e( 'Show Excluded', 'woocommerce-conditional-shipping-and-payments' ); ?>
			</label>
			<input type="checkbox" class="checkbox show_excluded_in_checkout" name="restriction[<?php echo $index; ?>][show_excluded]" <?php echo $show_excluded ? 'checked="checked"' : ''; ?>>
			<?php echo WC_CSP_Core_Compatibility::wc_help_tip( __( 'By default, excluded shipping methods are removed from the list of methods available during checkout. Select this option if you prefer to show excluded shipping methods in the checkout options and display a restriction notice when customers attempt to complete an order using an excluded method.', 'woocommerce-conditional-shipping-and-payments' ) ); ?>
		</p>
		<p class="form-field">
			<label>
				<?php _e( 'Custom Checkout Notice', 'woocommerce-conditional-shipping-and-payments' ); ?>:
				<?php

					if ( $field_type === 'global' ) {
						$tiptip = __( 'Defaults to:<br/>&quot;Unfortunately, your order cannot be shipped via {excluded_method}. To complete your order, please select an alternative shipping method.&quot;<br/>When conditions are defined, resolution instructions are added to the default message.', 'woocommerce-conditional-shipping-and-payments' );
					} else {
						$tiptip = __( 'Defaults to:<br/>&quot;Unfortunately, {product} is not eligible for shipping via {excluded_method}. To complete your order, please select an alternative shipping method, or remove {product} from your cart.&quot;<br/>When conditions are defined, resolution instructions are added to the default message.', 'woocommerce-conditional-shipping-and-payments' );
					}
				?>
			</label>
			<textarea class="custom_message" name="restriction[<?php echo $index; ?>][message]" id="restriction_<?php echo $index; ?>_message" placeholder="" rows="2" cols="20"><?php echo $message; ?></textarea>
			<?php
				echo WC_CSP_Core_Compatibility::wc_help_tip( $tiptip );

				if ( $field_type === 'global' ) {
					$tip = __( 'Define a custom checkout error message to show when selecting an excluded shipping method. You may include <code>{excluded_method}</code> and have it substituted by the selected shipping method title.', 'woocommerce-conditional-shipping-and-payments' );
				} else {
					$tip = __( 'Define a custom checkout error message to show when selecting an excluded shipping method. You may include <code>{product}</code> and <code>{excluded_method}</code> and have them substituted by the actual product title and the selected shipping method title.', 'woocommerce-conditional-shipping-and-payments' );
				}

				echo '<span class="description">' . $tip . '</span>';
			?>
		</p>
		<?php
	}

	/**
	 * Display a short summary of the restriction's settings.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function get_options_description( $options ) {

		if ( ! empty( $options[ 'description' ] ) ) {
			return $options[ 'description' ];
		}

		$method_descriptions = array();
		$methods             = array();

		if ( isset( $options[ 'methods' ] ) ) {
			$methods = $options[ 'methods' ];
		}

		$shipping_methods = WC()->shipping->load_shipping_methods();

		foreach ( $shipping_methods as $key => $val ) {

			if ( in_array( $key, $methods ) ) {
				$method_descriptions[] = WC_CSP_Core_Compatibility::is_wc_version_gte_2_6() ? $val->get_method_title() : $val->get_title();
			} else {
				foreach ( $methods as $restricted_method ) {
					if ( 0 === strpos( $restricted_method, $key ) ) {
						$method_descriptions[] = ( WC_CSP_Core_Compatibility::is_wc_version_gte_2_6() ? $val->get_method_title() : $val->get_title() ) . ' (' . $restricted_method . ')';
					}
				}
			}

			if ( $key === 'legacy_flat_rate' ) {

				$additional_flat_rate_options = (array) explode( "\n", $val->get_option( 'options' ) );

				foreach ( $additional_flat_rate_options as $option ) {

					$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

					if ( sizeof( $this_option ) !== 3 ) {
						continue;
					}

					$option_id = 'legacy_flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

					if ( in_array( $option_id, $methods ) ) {
						$method_descriptions[] = $this_option[0];
					}
				}
			}
		}

		return trim( implode( ', ', $method_descriptions ), ', ' );
	}

	/**
	 * Display options on the global Restrictions write-panel.
	 *
	 * @param  int     $index    restriction fields array index
	 * @param  string  $options  metabox options
	 * @return string
	 */
	function get_admin_global_fields_html( $index, $options = array() ) {

		$this->get_admin_fields_html( $index, $options, 'global' );
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * @param  int     $index    restriction fields array index
	 * @param  string  $options  metabox options
	 * @return string
	 */
	function get_admin_product_fields_html( $index, $options = array() ) {
		?><div class="description">
			<em><?php echo __( 'Restrict the available shipping methods when a shipping package contains this product.', 'woocommerce-conditional-shipping-and-payments' ); ?></em>
		</div><?php

		$this->get_admin_fields_html( $index, $options, 'product' );
	}

	/**
	 * Validate, process and return product options.
	 *
	 * @see get_admin_product_fields_html
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_fields( $posted_data ) {

		$processed_data = array();

		$processed_data[ 'methods' ]      = array();
		$processed_data[ 'custom_rates' ] = array();

		if ( ! empty( $posted_data[ 'custom_rates' ] ) ) {
			$processed_data[ 'custom_rates' ] = array_unique( array_map( 'wc_clean', explode( WC_DELIMITER, $posted_data[ 'custom_rates' ] ) ) );
			$processed_data[ 'methods' ]      = $processed_data[ 'custom_rates' ];
		}

		if ( ! empty( $posted_data[ 'methods' ] ) ) {
			$processed_data[ 'methods' ] = array_unique( array_merge( $processed_data[ 'methods' ], array_map( 'stripslashes', $posted_data[ 'methods' ] ) ) );
		} else {
			if ( empty( $processed_data[ 'methods' ] ) ) {
				return false;
			}
		}

		if ( isset( $posted_data[ 'show_excluded' ] ) ) {
			$processed_data[ 'show_excluded' ] = 'yes';
		}

		if ( ! empty( $posted_data[ 'message' ] ) ) {
			$processed_data[ 'message' ] = wp_kses_post( stripslashes( $posted_data[ 'message' ] ) );
		}

		if ( ! empty( $posted_data[ 'description' ] ) ) {
			$processed_data[ 'description' ] = strip_tags( stripslashes( $posted_data[ 'description' ] ) );
		}

		return $processed_data;
	}

	/**
	 * Validate, process and return product metabox options.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_product_fields( $posted_data ) {

		$processed_data = $this->process_admin_fields( $posted_data );

		if ( ! $processed_data ) {

			WC_Admin_Meta_Boxes::add_error( sprintf( __( 'Restriction #%s was not saved. Before saving a &quot;Shipping Method&quot; restriction, remember to add at least one shipping method to the exclusions list.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ) );
			return false;
		}

		return $processed_data;
	}

	/**
	 * Validate, process and return global settings.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_global_fields( $posted_data ) {

		$processed_data = $this->process_admin_fields( $posted_data );

		if ( ! $processed_data ) {

			WC_Admin_Settings::add_error( sprintf( __( 'Restriction #%s was not saved. Before saving a &quot;Shipping Method&quot; restriction, remember to add at least one shipping method to the exclusions list.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ) );
			return false;
		}

		return $processed_data;
	}

	/**
	 * Shows a woocommerce error on the 'woocommerce_review_order_before_cart_contents' hook when shipping method restrictions apply.
	 *
	 * @return void
	 */
	public function excluded_shipping_methods_notice() {

		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) ) {

			$result = $this->validate_checkout( array() );

			if ( $result->has_messages() ) {
				foreach ( $result->get_messages() as $message ) {
					wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
				}
			}
		}
	}

	/**
	 * Clean up excluded rates.
	 *
	 * @param  array  $rates
	 * @param  array  $excluded_rates
	 * @return array
	 */
	private function clean_excluded_rates( $rates, $excluded_rates ) {

		if ( ! empty( $rates ) && ! empty( $excluded_rates ) ) {

			$dirty_rate_ids = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( $this->is_restricted( $rate_id, $excluded_rates, $rate ) ) {
					$dirty_rate_ids[] = $rate_id;
				}
			}

			if ( ! empty( $dirty_rate_ids ) ) {
				foreach ( $dirty_rate_ids as $dirty_rate_id ) {
					if ( isset( $rates[ $dirty_rate_id ] ) ) {
						unset( $rates[ $dirty_rate_id ] );
					}
				}
			}
		}

		return $rates;
	}

	/**
	 * True if a rate is excluded.
	 *
	 * @param  string                  $rate_id
	 * @param  array                   $excluded_rates
	 * @param  WC_Shipping_Rate|false  $rate
	 * @return boolean
	 */
	private function is_restricted( $rate_id, $excluded_rates, $rate = false ) {

		$is_restricted              = false;
		$legacy_flat_rate_method_id = WC_CSP_Core_Compatibility::is_wc_version_gte_2_6() ? 'legacy_flat_rate' : 'flat_rate';

		foreach ( $excluded_rates as $excluded_rate_id ) {

			if ( $rate_id === $excluded_rate_id ) {

				$is_restricted = true;
				break;

			} elseif ( $excluded_rate_id !== $legacy_flat_rate_method_id && 0 === strpos( $rate_id, $excluded_rate_id ) && in_array( substr( $rate_id, strlen( $excluded_rate_id ), 1 ), array( ':', '-' ) ) ) {

				$is_restricted = true;
				break;

			} elseif ( is_object( $rate ) && WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

				$method_id   = $rate->get_method_id();
				$instance_id = $rate->get_instance_id();

				// When a rate is mapped to a known method ID and instance ID (attached to specific Shipping Zones), attempt to construct & evaluate its canonical rate ID.
				if ( $method_id && $instance_id ) {

					$canonical_rate_id = $method_id . ':' . $instance_id;

					if ( self::is_restricted( $canonical_rate_id, $excluded_rates ) ) {
						$is_restricted = true;
						break;
					}
				}
			}
		}

		return $is_restricted;
	}

	/**
	 * Remove shipping methods from packages.
	 *
	 * @return bool
	 */
	public function exclude_package_shipping_methods( $rates, $package ) {

		/* ----------------------------------------------------------------- */
		/* Product Restrictions
		/* ----------------------------------------------------------------- */

		// Loop package contents.
		if ( ! empty( $package[ 'contents' ] ) ) {
			foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

				$product = $cart_item_data[ 'data' ];

				$product_restriction_data = $this->get_product_restriction_data( $product );

				if ( ! empty( $product_restriction_data ) ) {

					// Evaluate all restriction sets for the current product.
					foreach ( $product_restriction_data as $restriction ) {

						// If method exclusions are present and all exclusion conditions apply, remove the excluded methods.
						if ( ! empty( $restriction[ 'methods' ] ) && ! $this->show_excluded( $restriction ) && $this->check_conditions_apply( $restriction, array( 'package' => $package, 'cart_item_data' => $cart_item_data ) ) ) {

							$restricted_methods = $restriction[ 'methods' ];

							$rates = $this->clean_excluded_rates( $rates, $restricted_methods );
						}
					}
				}
			}
		}

		/* ----------------------------------------------------------------- */
		/* Global Restrictions
		/* ----------------------------------------------------------------- */

		$global_restriction_data = $this->get_global_restriction_data();

		if ( ! empty( $global_restriction_data ) ) {

			// Evaluate all restriction sets for the current product.
			foreach ( $global_restriction_data as $restriction ) {

				// If method exclusions are present and all exclusion conditions apply, remove the excluded methods.
				if ( ! empty( $restriction[ 'methods' ] ) && ! $this->show_excluded( $restriction ) && $this->check_conditions_apply( $restriction, array( 'package' => $package ) ) ) {

					$restricted_methods = $restriction[ 'methods' ];

					$rates = $this->clean_excluded_rates( $rates, $restricted_methods );
				}
			}
		}

		return $rates;
	}

	/**
	 * Show excluded methods or validate selection only.
	 *
	 * @param  array  $restriction_data
	 * @return boolean
	 */
	private function show_excluded( $restriction_data ) {

		if ( ! empty( $restriction_data[ 'show_excluded' ] ) && $restriction_data[ 'show_excluded' ] === 'yes' ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate order checkout and return WC_CSP_Check_Result object.
	 *
	 * @param  array  $posted
	 * @return WC_CSP_Check_Result
	 */
	public function validate_checkout( $posted ) {

		$result = new WC_CSP_Check_Result();

		$shipping_packages = WC()->shipping->get_packages();
		$chosen_methods    = WC()->session->get( 'chosen_shipping_methods' );

		if ( ! empty( $shipping_packages ) ) {
			foreach ( $shipping_packages as $i => $package ) {

				if ( empty( $chosen_methods[ $i ] ) || empty( $package[ 'rates' ] ) ) {
					continue;
				}

				$chosen_rate = ! empty( $package[ 'rates' ][ $chosen_methods[ $i ] ] ) ? $package[ 'rates' ][ $chosen_methods[ $i ] ] : false;

				foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

					/* ----------------------------------------------------------------- */
					/* Product Restrictions
					/* ----------------------------------------------------------------- */

					$product = $cart_item_data[ 'data' ];

					$product_restriction_data = $this->get_product_restriction_data( $product );

					if ( ! empty( $product_restriction_data ) ) {

						// Evaluate all restriction sets for the current product.
						foreach ( $product_restriction_data as $restriction ) {

							$restriction_exists = false;

							// If exclusions are present and all defined restriction conditions apply, add validation error.
							if ( ! empty( $restriction[ 'methods' ] ) && $this->check_conditions_apply( $restriction, array( 'package' => $package, 'cart_item_data' => $cart_item_data ) ) ) {

								$restricted_methods = $restriction[ 'methods' ];

								if ( $this->is_restricted( $chosen_methods[ $i ], $restricted_methods, $chosen_rate ) ) {
									$restriction_exists = true;
								}
							}

							if ( $restriction_exists ) {

								if ( ! empty( $restriction[ 'message' ] ) ) {

									$message 	= str_replace( array( '{product}', '{excluded_method}' ), array( '&quot;%1$s&quot;', '%2$s' ), $restriction[ 'message' ] );
									$resolution = '';

								} else {

									$conditions_resolution = $this->get_conditions_resolution( $restriction, array( 'package' => $package, 'package_count' => sizeof( $shipping_packages ), 'cart_item_data' => $cart_item_data ) );

									if ( $conditions_resolution ) {
										$resolution = sprintf( __( 'To have &quot;%1$s&quot; shipped via %2$s, please %3$s. Otherwise, select an alternative payment method, or remove &quot;%1$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $i ] ]->label, $conditions_resolution );
									} else {
										$resolution = sprintf( __( 'To complete your order, please select an alternative shipping method, or remove &quot;%1$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title() );
									}

									$message = __( 'Unfortunately, &quot;%1$s&quot; is not eligible for shipping via &quot;%2$s&quot;. %3$s', 'woocommerce-conditional-shipping-and-payments' );
								}

								$message = sprintf( $message, $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $i ] ]->label, $resolution );

								$result->add( 'shipping_method_excluded_by_product_restriction', $message );
							}

						}

					}
				}

				/* ----------------------------------------------------------------- */
				/* Global Restrictions
				/* ----------------------------------------------------------------- */

				// Grab global restrictions.
				$global_restriction_data = $this->get_global_restriction_data();

				if ( ! empty( $global_restriction_data ) ) {

					// Evaluate all restriction sets for the current cart.
					foreach ( $global_restriction_data as $restriction ) {

						// Check if non cart-item conditions apply.
						if ( ! empty( $restriction[ 'methods' ] ) && $this->check_conditions_apply( $restriction, array( 'package' => $package ) ) ) {

							$restricted_methods = $restriction[ 'methods' ];

							if ( $this->is_restricted( $chosen_methods[ $i ], $restricted_methods, $chosen_rate ) ) {

								if ( ! empty( $restriction[ 'message' ] ) ) {

									$message 	= str_replace( '{excluded_method}', '%1$s', $restriction[ 'message' ] );
									$resolution = '';

								} else {

									$conditions_resolution = $this->get_conditions_resolution( $restriction, array( 'package' => $package, 'package_count' => sizeof( $shipping_packages ) ) );

									if ( $conditions_resolution ) {
										$resolution = sprintf( __( 'To have your order shipped via %1$s, please %2$s. Otherwise, choose an alternative shipping method.', 'woocommerce-conditional-shipping-and-payments' ), $package[ 'rates' ][ $chosen_methods[ $i ] ]->label, $conditions_resolution );
									} else {
										$resolution = __( 'To complete your order, please select an alternative shipping method.', 'woocommerce-conditional-shipping-and-payments' );
									}

									$message = __( 'Unfortunately, your order cannot be shipped via %1$s. %2$s', 'woocommerce-conditional-shipping-and-payments' );
								}

								$message = sprintf( $message, $package[ 'rates' ][ $chosen_methods[ $i ] ]->label, $resolution );

								$result->add( 'shipping_method_excluded_by_product_restriction', $message );
							}
						}
					}
				}
			}
		}

		return $result;
	}

}
