<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Compatibility
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Order_Compatibility' ) ) :

/**
 * WooCommerce order compatibility class.
 *
 * @since 4.6.0
 */
class SV_WC_Order_Compatibility extends SV_WC_Data_Compatibility {


	/** @var array mapped compatibility properties, as `$new_prop => $old_prop` */
	protected static $compat_props = array(
		'date_completed' => 'completed_date',
		'date_paid'      => 'paid_date',
		'date_modified'  => 'modified_date',
		'date_created'   => 'order_date',
		'customer_id'    => 'customer_user',
		'discount'       => 'cart_discount',
		'discount_tax'   => 'cart_discount_tax',
		'shipping_total' => 'total_shipping',
		'type'           => 'order_type',
		'currency'       => 'order_currency',
		'version'        => 'order_version',
	);


	/**
	 * Gets an order's created date.
	 *
	 * @since 4.6.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $context if 'view' then the value will be filtered
	 *
	 * @return \WC_DateTime|null
	 */
	public static function get_date_created( WC_Order $order, $context = 'edit' ) {

		return self::get_date_prop( $order, 'created', $context );
	}


	/**
	 * Gets an order's last modified date.
	 *
	 * @since 4.6.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $context if 'view' then the value will be filtered
	 *
	 * @return \WC_DateTime|null
	 */
	public static function get_date_modified( WC_Order $order, $context = 'edit' ) {

		return self::get_date_prop( $order, 'modified', $context );
	}


	/**
	 * Gets an order's paid date.
	 *
	 * @since 4.6.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $context if 'view' then the value will be filtered
	 *
	 * @return \WC_DateTime|null
	 */
	public static function get_date_paid( WC_Order $order, $context = 'edit' ) {

		return self::get_date_prop( $order, 'paid', $context );
	}


	/**
	 * Gets an order's completed date.
	 *
	 * @since 4.6.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $context if 'view' then the value will be filtered
	 *
	 * @return \WC_DateTime|null
	 */
	public static function get_date_completed( WC_Order $order, $context = 'edit' ) {

		return self::get_date_prop( $order, 'completed', $context );
	}


	/**
	 * Gets an order date.
	 *
	 * This should only be used to retrieve WC core date properties.
	 *
	 * @since 4.6.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $type type of date to get
	 * @param string $context if 'view' then the value will be filtered
	 *
	 * @return \WC_DateTime|null
	 */
	public static function get_date_prop( WC_Order $order, $type, $context = 'edit' ) {

		$date = null;
		$prop = "date_{$type}";

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$date = is_callable( array( $order, "get_{$prop}" ) ) ? $order->{"get_{$prop}"}( $context ) : null;

		} else {

			// backport the property name for WC < 3.0
			if ( isset( self::$compat_props[ $prop ] ) ) {
				$prop = self::$compat_props[ $prop ];
			}

			if ( $date = $order->$prop ) {

				try {

					$date = new SV_WC_DateTime( $date, new DateTimeZone( wc_timezone_string() ) );
					$date->setTimezone( new DateTimeZone( wc_timezone_string() ) );

				} catch ( Exception $e ) {

					$date = null;
				}
			}
		}

		return $date;
	}


	/**
	 * Gets an order property.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $object the order object
	 * @param string $prop the property name
	 * @param string $context if 'view' then the value will be filtered
	 * @return mixed
	 */
	public static function get_prop( $object, $prop, $context = 'edit', $compat_props = array() ) {

		// backport a few specific properties to pre-3.0
		if ( SV_WC_Plugin_Compatibility::is_wc_version_lt_3_0() ) {

			// convert the shipping_total prop for the edit context
			if ( 'shipping_total' === $prop && 'view' !== $context ) {

				$prop = 'order_shipping';

			// get the post_parent and bail early
			} elseif ( 'parent_id' === $prop ) {

				return $object->post->post_parent;
			}
		}

		return parent::get_prop( $object, $prop, $context, self::$compat_props );
	}


	/**
	 * Sets an order's properties.
	 *
	 * Note that this does not save any data to the database.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $object the order object
	 * @param array $props the new properties as $key => $value
	 * @return \WC_Order
	 */
	public static function set_props( $object, $props, $compat_props = array() ) {

		return parent::set_props( $object, $props, self::$compat_props );
	}


	/**
	 * Order item CRUD compatibility method to add a coupon to an order.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $order the order object
	 * @param array $code the coupon code
	 * @param int $discount the discount amount.
	 * @param int $discount_tax the discount tax amount.
	 * @return int the order item ID
	 */
	public static function add_coupon( WC_Order $order, $code = array(), $discount = 0, $discount_tax = 0 ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$item = new WC_Order_Item_Coupon();

			$item->set_props( array(
				'code'         => $code,
				'discount'     => $discount,
				'discount_tax' => $discount_tax,
				'order_id'     => $order->get_id(),
			) );

			$item->save();

			$order->add_item( $item );

			return $item->get_id();

		} else {

			return $order->add_coupon( $code, $discount, $discount_tax );
		}
	}


	/**
	 * Order item CRUD compatibility method to add a fee to an order.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $order the order object
	 * @param object $fee the fee to add
	 * @return int the order item ID
	 */
	public static function add_fee( WC_Order $order, $fee ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$item = new WC_Order_Item_Fee();

			$item->set_props( array(
				'name'      => $fee->name,
				'tax_class' => $fee->taxable ? $fee->tax_class : 0,
				'total'     => $fee->amount,
				'total_tax' => $fee->tax,
				'taxes'     => array(
					'total' => $fee->tax_data,
				),
				'order_id'  => $order->get_id(),
			) );

			$item->save();

			$order->add_item( $item );

			return $item->get_id();

		} else {

			return $order->add_fee( $fee );
		}
	}


	/**
	 * Order item CRUD compatibility method to add a shipping line to an order.
	 *
	 * @since 4.7.0
	 *
	 * @param \WC_Order $order order object
	 * @param \WC_Shipping_Rate $shipping_rate shipping rate to add
	 * @return int the order item ID
	 */
	public static function add_shipping( WC_Order $order, $shipping_rate ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$item = new WC_Order_Item_Shipping();

			$item->set_props( array(
				'method_title' => $shipping_rate->label,
				'method_id'    => $shipping_rate->id,
				'total'        => wc_format_decimal( $shipping_rate->cost ),
				'taxes'        => $shipping_rate->taxes,
				'order_id'     => $order->get_id(),
			) );

			foreach ( $shipping_rate->get_meta_data() as $key => $value ) {
				$item->add_meta_data( $key, $value, true );
			}

			$item->save();

			$order->add_item( $item );

			return $item->get_id();

		} else {

			return $order->add_shipping( $shipping_rate );
		}
	}


	/**
	 * Order item CRUD compatibility method to add a tax line to an order.
	 *
	 * @since 4.7.0
	 *
	 * @param \WC_Order $order order object
	 * @param int $tax_rate_id tax rate ID
	 * @param float $tax_amount cart tax amount
	 * @param float $shipping_tax_amount shipping tax amount
	 * @return int order item ID
	 */
	public static function add_tax( WC_Order $order, $tax_rate_id, $tax_amount = 0, $shipping_tax_amount = 0 ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$item = new WC_Order_Item_Tax();

			$item->set_props( array(
				'rate_id'            => $tax_rate_id,
				'tax_total'          => $tax_amount,
				'shipping_tax_total' => $shipping_tax_amount,
			) );

			$item->set_rate( $tax_rate_id );
			$item->set_order_id( $order->get_id() );
			$item->save();

			$order->add_item( $item );

			return $item->get_id();

		} else {

			return $order->add_tax( $tax_rate_id, $tax_amount, $shipping_tax_amount );
		}
	}


	/**
	 * Order item CRUD compatibility method to update an order coupon.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $order the order object
	 * @param int|\WC_Order_Item $item the order item ID
	 * @param array $args {
	 *     The coupon item args.
	 *
	 *     @type string $code         the coupon code
	 *     @type float  $discount     the coupon discount amount
	 *     @type float  $discount_tax the coupon discount tax amount
	 * }
	 * @return int|bool the order item ID or false on failure
	 */
	public static function update_coupon( WC_Order $order, $item, $args ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			if ( is_numeric( $item ) ) {
				$item = $order->get_item( $item );
			}

			if ( ! is_object( $item ) || ! $item->is_type( 'coupon' ) ) {
				return false;
			}

			if ( ! $order->get_id() ) {
				$order->save();
			}

			$item->set_order_id( $order->get_id() );
			$item->set_props( $args );
			$item->save();

			return $item->get_id();

		} else {

			// convert WC 3.0+ args for backwards compatibility
			if ( isset( $args['discount'] ) ) {
				$args['discount_amount'] = $args['discount'];
			}
			if ( isset( $args['discount_tax'] ) ) {
				$args['discount_amount_tax'] = $args['discount_tax'];
			}

			return $order->update_coupon( $item, $args );
		}
	}


	/**
	 * Order item CRUD compatibility method to update an order fee.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $order the order object
	 * @param int|\WC_Order_Item $item the order item ID
	 * @param array $args {
	 *     The fee item args.
	 *
	 *     @type string $name       the fee name
	 *     @type string $tax_class  the fee's tax class
	 *     @type float  $line_total the fee total amount
	 *     @type float  $line_tax   the fee tax amount
	 * }
	 * @return int|bool the order item ID or false on failure
	 */
	public static function update_fee( WC_Order $order, $item, $args ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			if ( is_numeric( $item ) ) {
				$item = $order->get_item( $item );
			}

			if ( ! is_object( $item ) || ! $item->is_type( 'fee' ) ) {
				return false;
			}

			if ( ! $order->get_id() ) {
				$order->save();
			}

			$item->set_order_id( $order->get_id() );
			$item->set_props( $args );
			$item->save();

			return $item->get_id();

		} else {

			return $order->update_fee( $item, $args );
		}
	}


	/**
	 * Backports wc_reduce_stock_levels() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $order the order object
	 */
	public static function reduce_stock_levels( WC_Order $order ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			wc_reduce_stock_levels( $order->get_id() );
		} else {
			$order->reduce_order_stock();
		}
	}


	/**
	 * Backports wc_update_total_sales_counts() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Order $order the order object
	 */
	public static function update_total_sales_counts( WC_Order $order ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			wc_update_total_sales_counts( $order->get_id() );
		} else {
			$order->record_product_sales();
		}
	}


	/**
	 * Determines if an order has an available shipping address.
	 *
	 * WooCommerce 3.0+ no longer fills the shipping address with the billing if
	 * a shipping address was never set by the customer at checkout, as is the
	 * case with virtual orders. This method is helpful for gateways that may
	 * reject such transactions with blank shipping information.
	 *
	 * TODO: Remove when WC 3.0.4 can be required {CW 2017-04-17}
	 *
	 * @since 4.6.1
	 *
	 * @param \WC_Order $order order object
	 *
	 * @return bool
	 */
	public static function has_shipping_address( WC_Order $order ) {

		return self::get_prop( $order, 'shipping_address_1' ) || self::get_prop( $order, 'shipping_address_2' );
	}


	/**
	 * Gets the formatted meta data for an order item.
	 *
	 * @since 4.6.5
	 *
	 * @param \WC_Order_Item|array $item order item object or array
	 * @param string $hideprefix prefix for meta that is considered hidden
	 * @param bool $include_all whether to include all meta (attributes, etc...), or just custom fields
	 * @return array $item_meta {
	 *     @type string $label meta field label
	 *     @type mixed  $value meta value
 	 * }
	 */
	public static function get_item_formatted_meta_data( $item, $hideprefix = '_', $include_all = false ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_1() && $item instanceof WC_Order_Item ) {

			$meta_data = $item->get_formatted_meta_data( $hideprefix, $include_all );
			$item_meta = array();

			foreach ( $meta_data as $meta ) {

				$item_meta[] = array(
					'label' => $meta->display_key,
					'value' => $meta->value,
				);
			}

		} else {

			$item_meta = new WC_Order_Item_Meta( $item );
			$item_meta = $item_meta->get_formatted( $hideprefix );
		}

		return $item_meta;
	}


	/**
	 * Gets the admin Edit screen URL for an order.
	 *
	 * @since 4.9.0
	 *
	 * @param \WC_Order $order order object
	 * @return string
	 */
	public static function get_edit_order_url( WC_Order $order ) {

		if ( self::is_wc_version_gte( '3.3' ) ) {

			return $order->get_edit_order_url();

		} else {

			return apply_filters( 'woocommerce_get_edit_order_url', get_admin_url( null, 'post.php?post=' . self::get_prop( $order, 'id' ) . '&action=edit' ), $order );
		}
	}


}


endif; // Class exists check
