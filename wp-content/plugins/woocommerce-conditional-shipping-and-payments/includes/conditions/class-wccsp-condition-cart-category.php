<?php
/**
 * WC_CSP_Condition_Cart_Category class
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
 * Category in Cart Condition.
 *
 * @class   WC_CSP_Condition_Cart_Category
 * @version 1.3.0
 */
class WC_CSP_Condition_Cart_Category extends WC_CSP_Condition {

	public function __construct() {

		$this->id                            = 'category_in_cart';
		$this->title                         = __( 'Category', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions = array( 'payment_gateways' );
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

		$cart_contents = WC()->cart->get_cart();

		if ( empty( $cart_contents ) ) {
			return false;
		}

		$contains_qualifying_products = false;
		$all_products_qualify         = true;

		foreach ( $cart_contents as $cart_item_key => $cart_item_data ) {

			$product_category_terms = get_the_terms( $cart_item_data[ 'product_id' ], 'product_cat' );

			if ( $product_category_terms && ! is_wp_error( $product_category_terms ) ) {
				foreach ( $product_category_terms as $product_category_term ) {
					if ( in_array( $product_category_term->term_id, $data[ 'value' ] ) ) {

						$contains_qualifying_products = true;

						if ( $data[ 'modifier' ] === 'not-in' ) {
							break 2;
						}
					} else {

						$all_products_qualify = false;

						if ( $data[ 'modifier' ] === 'not-all-in' ) {
							break 2;
						}
					}
				}
			}
		}

		if ( ( $data[ 'modifier' ] === 'in' && $contains_qualifying_products ) || ( $data[ 'modifier' ] === 'all-in' && $all_products_qualify ) ) {

			$term_names = array();

			foreach ( $data[ 'value' ] as $term_id ) {

				$term = get_term_by( 'id', $term_id, 'product_cat' );

				if ( $term ) {
					$term_names[] = $term->name;
				}
			}

			$string = WC_CSP_Condition::merge_titles( $term_names );
			return sprintf( __( 'remove all products in the %s categories from your cart', 'woocommerce-conditional-shipping-and-payments' ), $string );


		} elseif ( ( $data[ 'modifier' ] === 'not-in' && ! $contains_qualifying_products ) || ( $data[ 'modifier' ] === 'not-all-in' && ! $all_products_qualify ) ) {
			return __( 'purchase a qualifying set of products', 'woocommerce-conditional-shipping-and-payments' );
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

		$contains_qualifying_products = false;
		$all_products_qualify         = true;

		if ( is_checkout_pay_page() ) {

			global $wp;

			if ( isset( $wp->query_vars[ 'order-pay' ] ) ) {

				$order_id = $wp->query_vars[ 'order-pay' ];
				$order    = wc_get_order( $order_id );

				if ( $order ) {

					$order_items = $order->get_items( 'line_item' );

					if ( ! empty( $order_items ) ) {

						foreach ( $order_items as $order_item ) {

							$product_category_terms = get_the_terms( $order_item[ 'product_id' ], 'product_cat' );

							if ( $product_category_terms && ! is_wp_error( $product_category_terms ) ) {
								foreach ( $product_category_terms as $product_category_term ) {
									if ( in_array( $product_category_term->term_id, $data[ 'value' ] ) ) {

										$contains_qualifying_products = true;

										if ( $data[ 'modifier' ] === 'in' ) {
											break 2;
										}

									} else {

										$all_products_qualify = false;

										if ( $data[ 'modifier' ] === 'all-in' || $data[ 'modifier' ] === 'not-all-in' ) {
											break 2;
										}
									}
								}
							}
						}
					}
				}
			}

		} else {

			$cart_contents = WC()->cart->get_cart();

			if ( ! empty( $cart_contents ) ) {
				foreach ( $cart_contents as $cart_item_key => $cart_item_data ) {
					$product_category_terms = get_the_terms( $cart_item_data[ 'product_id' ], 'product_cat' );

					if ( $product_category_terms && ! is_wp_error( $product_category_terms ) ) {
						foreach ( $product_category_terms as $product_category_term ) {
							if ( in_array( $product_category_term->term_id, $data[ 'value' ] ) ) {

								$contains_qualifying_products = true;

								if ( $data[ 'modifier' ] === 'in' ) {
									break 2;
								}

							} else {

								$all_products_qualify = false;

								if ( $data[ 'modifier' ] === 'all-in' || $data[ 'modifier' ] === 'not-all-in' ) {
									break 2;
								}
							}
						}
					}
				}
			}
		}

		if ( $data[ 'modifier' ] === 'in' && $contains_qualifying_products ) {
			return true;
		} elseif ( $data[ 'modifier' ] === 'not-in' && ! $contains_qualifying_products ) {
			return true;
		} elseif ( $data[ 'modifier' ] === 'all-in' && $all_products_qualify ) {
			return true;
		} elseif ( $data[ 'modifier' ] === 'not-all-in' && ! $all_products_qualify ) {
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
			$processed_condition_data[ 'value' ]        = array_map( 'intval', $posted_condition_data[ 'value' ] );
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

		$modifier   = '';
		$categories = array();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$categories = $condition_data[ 'value' ];
		}

		$product_categories = ( array ) get_terms( 'product_cat', array( 'get' => 'all' ) );

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'in cart', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'not in cart', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="all-in" <?php selected( $modifier, 'all-in', true ) ?>><?php echo __( 'every cart item', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ) ?>><?php echo __( 'not every cart item', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" style="width:80%;" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select categories&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
				<?php
					foreach ( $product_categories as $product_category )
						echo '<option value="' . $product_category->term_id . '" ' . selected( in_array( $product_category->term_id, $categories ), true, false ) . '>' . $product_category->name . '</option>';
				?>
			</select>
		</div><?php
	}
}
