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

if ( ! class_exists( 'SV_WC_Product_Compatibility' ) ) :

/**
 * WooCommerce product compatibility class.
 *
 * @since 4.6.0
 */
class SV_WC_Product_Compatibility extends SV_WC_Data_Compatibility {


	/** @var array mapped compatibility properties, as `$new_prop => $old_prop` */
	protected static $compat_props = array(
		'catalog_visibility' => 'visibility',
		'date_on_sale_from'  => 'sale_price_dates_from',
		'date_on_sale_to'    => 'sale_price_dates_to',
		'gallery_image_ids'  => 'product_image_gallery',
		'cross_sell_ids'     => 'crosssell_ids',
	);


	/**
	 * Gets a product property.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $object the product object
	 * @param string $prop the property name
	 * @param string $context if 'view' then the value will be filtered
	 * @return mixed
	 */
	public static function get_prop( $object, $prop, $context = 'edit', $compat_props = array() ) {

		// backport 'WC_Product::get_parent_id()' to pre-3.0
		if ( SV_WC_Plugin_Compatibility::is_wc_version_lt_3_0() && 'parent_id' === $prop ) {
			$prop    = 'id';
			$context = $object->is_type( 'variation' ) ? 'raw' : $context;
		}

		return parent::get_prop( $object, $prop, $context, self::$compat_props );
	}


	/**
	 * Sets an products's properties.
	 *
	 * Note that this does not save any data to the database.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $object the product object
	 * @param array $props the new properties as $key => $value
	 * @return \WC_Product
	 */
	public static function set_props( $object, $props, $compat_props = array() ) {

		return parent::set_props( $object, $props, self::$compat_props );
	}


	/**
	 * Gets a product's parent product.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @return \WC_Product|bool
	 */
	public static function get_parent( WC_Product $product ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			$parent = wc_get_product( $product->get_parent_id() );
		} else {
			$parent = $product->is_type( 'variation' ) ? wc_get_product( $product->id ) : false;
		}

		return $parent;
	}


	/**
	 * Backports wc_update_product_stock() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @param int $amount Optional. The new stock quantity
	 * @param string $mode Optional. Can be set, add, or subtract
	 * @return int
	 */
	public static function wc_update_product_stock( WC_Product $product, $amount = null, $mode = 'set' ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			return wc_update_product_stock( $product, $amount, $mode );
		} else {
			return $product->set_stock( $amount, $mode );
		}
	}


	/**
	 * Backports wc_get_price_html_from_text() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @return string
	 */
	public static function wc_get_price_html_from_text( WC_Product $product ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			return wc_get_price_html_from_text();
		} else {
			return $product->get_price_html_from_text();
		}
	}


	/**
	 * Backports wc_get_price_including_tax() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @param int $qty Optional. The quantity
	 * @param string $price Optional. The product price
	 * @return string
	 */
	public static function wc_get_price_including_tax( WC_Product $product, $qty = 1, $price = '' ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			return wc_get_price_including_tax( $product, array(
				'qty'   => $qty,
				'price' => $price,
			) );

		} else {

			return $product->get_price_including_tax( $qty, $price );
		}
	}


	/**
	 * Backports wc_get_price_excluding_tax() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @param int $qty Optional. The quantity
	 * @param string $price Optional. The product price
	 * @return string
	 */
	public static function wc_get_price_excluding_tax( WC_Product $product, $qty = 1, $price = '' ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			return wc_get_price_excluding_tax( $product, array(
				'qty'   => $qty,
				'price' => $price,
			) );

		} else {

			return $product->get_price_excluding_tax( $qty, $price );
		}
	}


	/**
	 * Backports wc_get_price_to_display() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @param string $price Optional. The product price
	 * @param int $qty Optional. The quantity
	 * @return string
	 */
	public static function wc_get_price_to_display( WC_Product $product, $price = '', $qty = 1 ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			return wc_get_price_to_display( $product, array(
				'qty'   => $qty,
				'price' => $price,
			) );

		} else {

			return $product->get_display_price( $price, $qty );
		}
	}


	/**
	 * Backports wc_get_product_category_list() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @param string $sep Optional. The list separator
	 * @param string $before Optional. To display before the list
	 * @param string $after Optional. To display after the list
	 * @return string
	 */
	public static function wc_get_product_category_list( WC_Product $product, $sep = ', ', $before = '', $after = '' ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

			return wc_get_product_category_list( $id, $sep, $before, $after );

		} else {

			return $product->get_categories( $sep, $before, $after );
		}
	}


	/**
	 * Backports wc_get_rating_html() to pre-3.0.
	 *
	 * @since 4.6.0
	 * @param \WC_Product $product the product object
	 * @param string $rating Optional. The product rating
	 * @return string
	 */
	public static function wc_get_rating_html( WC_Product $product, $rating = null ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			return wc_get_rating_html( $rating );
		} else {
			return $product->get_rating_html( $rating );
		}
	}


}


endif; // Class exists check
