<?php
/**
 * WC_CSP_Restrictions class
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
 * Restrictions.
 *
 * Loads restriction classes via hooks and prepares them for use.
 *
 * @class   WC_CSP_Restrictions
 * @version 1.2.0
  */
class WC_CSP_Restrictions {

	/** @var array Array of registered restriction classes. */
	public $restrictions;

	public function __construct() {

		$load_restrictions = apply_filters( 'woocommerce_csp_restrictions', array(
			'WC_CSP_Restrict_Payment_Gateways', 	// Restrict payment gateways based on product constraints.
			'WC_CSP_Restrict_Shipping_Methods', 	// Restrict shipping methods based on product constraints.
			'WC_CSP_Restrict_Shipping_Countries', 	// Restrict shipping countries based on product constraints.
		) );

		// Load cart restrictions.
		foreach ( $load_restrictions as $restriction ) {

			$restriction = new $restriction();

			$this->restrictions[ $restriction->id ] = $restriction;
		}

		// Validate add-to-cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 10, 6 );

		// Validate cart.
		add_action( 'woocommerce_check_cart_items', array( $this, 'validate_cart' ), 10 );

		// Validate cart update.
		add_filter( 'woocommerce_update_cart_validation', array( $this, 'validate_cart_update' ), 10, 4 );

		// Validate checkout.
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout' ), 10 );


	}

	/**
	 * Get restriction class by restriction_id.
	 *
	 * @param  str    $restriction_id
	 * @return WC_CSP_Restriction
	 */
	public function get_restriction( $restriction_id ) {

		if ( ! empty( $this->restrictions[ $restriction_id ] ) ) {
			return $this->restrictions[ $restriction_id ];
		}

		return false;
	}

	/**
	 * Get all registered restrictions by supported validation type.
	 *
	 * @param  string $validation_type
	 * @return array
	 */
	public function get_restrictions( $validation_type = '' ) {

		$restrictions = array();

		foreach ( $this->restrictions as $id => $restriction ) {
			if ( $validation_type === '' || in_array( $validation_type, $restriction->get_validation_types() ) ) {
				$restrictions[ $id ] = $restriction;
			}
		}

		return apply_filters( 'woocommerce_csp_get_restrictions', $restrictions, $validation_type );
	}

	/**
	 * Get all registered restrictions that have admin product metabox options.
	 *
	 * @return array
	 */
	public function get_admin_product_field_restrictions() {

		$restriction_titles = array();

		foreach ( $this->restrictions as $id => $restriction ) {
			if ( $restriction->has_admin_product_fields() ) {
				$restriction_titles[ $id ] = $restriction;
			}
		}

		return apply_filters( 'woocommerce_csp_get_admin_product_field_restrictions', $restriction_titles );
	}

	/**
	 * Get all registered restrictions that have global settings.
	 *
	 * @return array
	 */
	public function get_admin_global_field_restrictions() {

		$restriction_titles = array();

		foreach ( $this->restrictions as $id => $restriction ) {
			if ( $restriction->has_admin_global_fields() ) {
				$restriction_titles[ $id ] = $restriction;
			}
		}

		return apply_filters( 'woocommerce_csp_get_admin_global_field_restrictions', $restriction_titles );
	}

	/**
	 * Add-to-cart validation ('woocommerce_add_to_cart_validation' filter) for all restrictions that implement the 'WC_CSP_Add_To_Cart_Restriction' interface.
	 *
	 * @param  bool   $add
	 * @param  int    $product_id
	 * @param  int    $product_quantity
	 * @param  string $variation_id
	 * @param  array  $variations
	 * @param  array  $cart_item_data
	 * @return bool
	 */
	public function validate_add_to_cart( $add, $product_id, $product_quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		$add_to_cart_restrictions = $this->get_restrictions( 'add-to-cart' );

		if ( ! empty( $add_to_cart_restrictions ) ) {

			foreach ( $add_to_cart_restrictions as $restriction ) {

				$result = $restriction->validate_add_to_cart();

				if ( $result->has_messages() ) {

					foreach ( $result->get_messages() as $message ) {
						wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
					}

					$add = false;
				}
			}
		}

		return $add;
	}


	/**
	 * Cart validation ('check_cart_items' action) for all restrictions that implement the 'WC_CSP_Cart_Restriction' interface.
	 *
	 * @return void
	 */
	public function validate_cart() {

		$cart_restrictions = $this->get_restrictions( 'cart' );

		if ( ! empty( $cart_restrictions ) ) {

			foreach ( $cart_restrictions as $restriction ) {

				$result = $restriction->validate_cart();

				if ( $result->has_messages() ) {

					foreach ( $result->get_messages() as $message ) {
						wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
					}
				}
			}
		}

	}

	/**
	 * Update cart validation ('update_cart_validation' filter) for all restrictions that implement the 'WC_CSP_Update_Cart_Restriction' interface.
	 *
	 * @param  bool   $passed
	 * @param  str    $cart_item_key
	 * @param  str    $cart_item_values
	 * @param  int    $quantity
	 * @return bool
	 */
	public function validate_cart_update( $passed, $cart_item_key, $cart_item_values, $quantity ) {

		$cart_update_restrictions = $this->get_restrictions( 'cart-update' );

		if ( ! empty( $cart_update_restrictions ) ) {

			foreach ( $cart_update_restrictions as $restriction ) {

				$result = $restriction->validate_cart_update( $passed, $cart_item_key, $cart_item_values, $quantity );

				if ( $result->has_messages() ) {

					foreach ( $result->get_messages() as $message ) {
						wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
					}

					$passed = false;
				}
			}
		}

		return $passed;
	}

	/**
	 * Checkout validation ('woocommerce_after_checkout_validation' filter) for all restrictions that implement the 'WC_CSP_Checkout_Restriction' interface.
	 *
	 * @param  array  $posted
	 * @return void
	 */
	public function validate_checkout( $posted ) {

		$checkout_restrictions = $this->get_restrictions( 'checkout' );

		if ( ! empty( $checkout_restrictions ) ) {

			foreach ( $checkout_restrictions as $restriction ) {

				$result = $restriction->validate_checkout( $posted );

				if ( $result->has_messages() ) {

					foreach ( $result->get_messages() as $message ) {
						wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
					}
				}
			}
		}
	}

	/**
	 * Update restriction data routine.
	 *
	 * @param  array  $data
	 * @param  string $scope
	 * @return array
	 */
	public function maybe_update_restriction_data( $data, $scope ) {

		if ( ! empty( $data ) ) {
			if ( $scope === 'global' ) {
				foreach ( $data as $restriction_data_group_key => $restriction_data_group ) {
					foreach ( $restriction_data_group as $restriction_key => $restriction_data ) {
						$data[ $restriction_data_group_key ][ $restriction_key ] = $this->update_restriction_data( $restriction_data );
					}
				}
			} elseif ( $scope === 'product' ) {
				foreach ( $data as $restriction_key => $restriction_data ) {
					$data[ $restriction_key ] = $this->update_restriction_data( $restriction_data );
				}
			}
		}

		return $data;
	}

	/**
	 * Update single restriction data routine.
	 *
	 * @param  array  $restriction_data
	 * @return array
	 */
	private function update_restriction_data( $restriction_data ) {

		// Convert conditions structure from v1.0 to v1.1.
		if ( ! empty( $restriction_data[ 'conditions' ] ) ) {

			$conditions = $restriction_data[ 'conditions' ];
			$check      = current( $conditions );

			if ( ! isset( $check[ 'condition_id' ] ) ) {
				$restriction_data[ 'conditions' ] = $this->update_legacy_condition_data( $conditions );
			}
		}

		// Add 'legacy_' prefix to pre-2.6 shipping method ids and update rate ids based on modified shipping method id data stored in the 'woocommerce_updated_instance_ids' option.
		if ( WC_CSP_Core_Compatibility::is_wc_version_gte_2_6() ) {
			if ( isset( $restriction_data[ 'restriction_id' ] ) && $restriction_data[ 'restriction_id' ] === 'shipping_methods' ) {
				if ( ! isset( $restriction_data[ 'wc_26_shipping' ] ) ) {

					if ( isset( $restriction_data[ 'methods' ] ) ) {

						foreach ( $restriction_data[ 'methods' ] as $key => $val ) {

							if ( 0 === strpos( $val, 'legacy_' ) ) {
								continue;
							}

							foreach ( WC_CSP_Core_Compatibility::$legacy_methods as $legacy_method_id ) {
								if ( $val === $legacy_method_id ) {
									$restriction_data[ 'methods' ][ $key ] = 'legacy_' . $val;
									continue 2;
								}
							}

							if ( 0 === strpos( $val, 'flat_rate:' ) ) {
								$restriction_data[ 'methods' ][ $key ] = 'legacy_' . $val;
								continue;
							}

							foreach ( WC_CSP_Core_Compatibility::$upgraded_methods as $upgraded_method_id ) {

								if ( 0 === strpos( $val, $upgraded_method_id . '-' ) ) {

									$suffix         = substr( $val, strlen( $upgraded_method_id ) + 1 );
									$suffix_explode = explode( ' : ', $suffix );
									$instance_id    = $suffix_explode[ 0 ];

									if ( isset( WC_CSP_Core_Compatibility::$updated_shipping_method_instance_ids[ $instance_id ] ) ) {
										$instance_id = WC_CSP_Core_Compatibility::$updated_shipping_method_instance_ids[ $instance_id ];
									}

									$new_val = $upgraded_method_id . ':' . $instance_id;

									if ( sizeof( $suffix_explode ) === 2 ) {
										$new_val .= ':' . $suffix_explode[ 1 ];
										if ( ! isset( $restriction_data[ 'custom_rates' ] ) ) {
											$restriction_data[ 'custom_rates' ] = array();
										}
										$restriction_data[ 'custom_rates' ][] = $new_val;
									} else {
										$restriction_data[ 'methods' ][ $key ] = $new_val;
									}

									continue 2;
								}
							}
						}
					}
					$restriction_data[ 'wc_26_shipping' ] = 'yes';
				}
			} elseif ( ! empty( $restriction_data[ 'conditions' ] ) ) {
				foreach ( $restriction_data[ 'conditions' ] as $condition_key => $condition_data ) {

					if ( isset( $condition_data[ 'condition_id' ] ) && $condition_data[ 'condition_id' ] === 'shipping_method' ) {
						if ( ! isset( $restriction_data[ 'wc_26_shipping' ] ) ) {

							if ( ! empty( $condition_data[ 'value' ] ) ) {
								foreach ( $condition_data[ 'value' ] as $key => $val ) {

									if ( 0 === strpos( $val, 'legacy_' ) ) {
										continue;
									}

									foreach ( WC_CSP_Core_Compatibility::$legacy_methods as $legacy_method_id ) {
										if ( $val === $legacy_method_id ) {
											$restriction_data[ 'conditions' ][ $condition_key ][ 'value' ][ $key ] = 'legacy_' . $val;
											continue 2;
										}
									}

									if ( 0 === strpos( $val, 'flat_rate:' ) ) {
										$restriction_data[ 'conditions' ][ $condition_key ][ 'value' ][ $key ] = 'legacy_' . $val;
										continue;
									}

									foreach ( WC_CSP_Core_Compatibility::$upgraded_methods as $upgraded_method_id ) {

										if ( 0 === strpos( $val, $upgraded_method_id . '-' ) ) {

											$suffix         = substr( $val, strlen( $upgraded_method_id ) + 1 );
											$suffix_explode = explode( ' : ', $suffix );
											$instance_id    = $suffix_explode[ 0 ];

											if ( isset( WC_CSP_Core_Compatibility::$updated_shipping_method_instance_ids[ $instance_id ] ) ) {
												$instance_id = WC_CSP_Core_Compatibility::$updated_shipping_method_instance_ids[ $instance_id ];
											}

											$new_val = $upgraded_method_id . ':' . $instance_id;

											// Manual rate ID exclusions not supported in "Shipping Method" condition.
											if ( sizeof( $suffix_explode ) === 1 ) {
												$restriction_data[ 'conditions' ][ $condition_key ][ 'value' ][ $key ] = $new_val;
											}

											continue 2;
										}
									}
								}
							}
							$restriction_data[ 'wc_26_shipping' ] = 'yes';
						}
					}
				}
			}
		}

		// Update 'shipping_countries' conditions to operate at package level:
		// 'shipping_class_in_cart' and 'category_in_cart' condition content to 'shipping_class_in_package' and 'category_in_package'.
		if ( isset( $restriction_data[ 'restriction_id' ] ) && $restriction_data[ 'restriction_id' ] === 'shipping_countries' ) {
			if ( ! empty( $restriction_data[ 'conditions' ] ) ) {
				foreach ( $restriction_data[ 'conditions' ] as $condition_key => $condition_data ) {

					if ( isset( $condition_data[ 'condition_id' ] ) && $condition_data[ 'condition_id' ] === 'shipping_class_in_cart' ) {
						$restriction_data[ 'conditions' ][ $condition_key ][ 'condition_id' ] = 'shipping_class_in_package';
					} elseif ( isset( $condition_data[ 'condition_id' ] ) && $condition_data[ 'condition_id' ] === 'category_in_cart' ) {
						$restriction_data[ 'conditions' ][ $condition_key ][ 'condition_id' ] = 'category_in_package';
					}
				}
			}
		}

		return $restriction_data;
	}


	/**
	 * v1.0 to v1.1 update condition data routine.
	 *
	 * @param  array  $conditions
	 * @return array
	 */
	private function update_legacy_condition_data( $conditions ) {

		$updated_conditions = array();

		foreach ( $conditions as $condition_field => $condition_values ) {

			if ( $condition_field === 'cart_total_max' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'cart_total',
					'modifier'       => 'max',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'cart_total_min' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'cart_total',
					'modifier'       => 'min',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'pkg_weight_min' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'package_weight',
					'modifier'       => 'min',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'pkg_weight_max' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'package_weight',
					'modifier'       => 'max',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'order_total_max' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'order_total',
					'modifier'       => 'max',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'order_total_min' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'order_total',
					'modifier'       => 'min',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'countries' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'shipping_country',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
					'states'         => $condition_values[ 'states' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'billing_countries' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'billing_country',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'methods' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'shipping_method',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'categories' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'category_in_cart',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'package_categories' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'category_in_package',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'shipping_classes' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'shipping_class_in_cart',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'package_shipping_classes' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'shipping_class_in_package',
					'modifier'       => 'in',
					'value'          => $condition_values[ 'value' ],
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'quantity_min' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'cart_item_quantity',
					'modifier'       => 'min',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;

			} elseif ( $condition_field === 'quantity_max' && ! empty( $condition_values[ 'value' ] ) ) {
				$condition_content = array(
					'condition_id'   => 'cart_item_quantity',
					'modifier'       => 'max',
					'value'          => $condition_values[ 'value' ]
				);

				$updated_conditions[] = $condition_content;
			}
		}

		return $updated_conditions;
	}
}
