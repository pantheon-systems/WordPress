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

if ( ! class_exists( 'SV_WC_Data_Compatibility' ) ) :

/**
 * WooCommerce data compatibility class.
 *
 * @since 4.6.0
 */
abstract class SV_WC_Data_Compatibility {


	/**
	 * Gets an object property.
	 *
	 * @since 4.6.0
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $prop the property name
	 * @param string $context if 'view' then the value will be filtered
	 * @param array $compat_props Compatibility properties.
	 * @return mixed
	 */
	public static function get_prop( $object, $prop, $context = 'edit', $compat_props = array() ) {

		$value = '';

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			if ( is_callable( array( $object, "get_{$prop}" ) ) ) {
 				$value = $object->{"get_{$prop}"}( $context );
 			}

		} else {

			// backport the property name
			if ( isset( $compat_props[ $prop ] ) ) {
				$prop = $compat_props[ $prop ];
			}

			// if this is the 'view' context and there is an accessor method, use it
			if ( is_callable( array( $object, "get_{$prop}" ) ) && 'view' === $context ) {
				$value = $object->{"get_{$prop}"}();
			} else {
				$value = $object->$prop;
			}
		}

		return $value;
	}


	/**
	 * Sets an object's properties.
	 *
	 * Note that this does not save any data to the database.
	 *
	 * @since 4.6.0
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param array $props the new properties as $key => $value
	 * @param array $compat_props Compatibility properties.
	 * @return \WC_Data
	 */
	public static function set_props( $object, $props, $compat_props = array() ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$object->set_props( $props );

		} else {

			foreach ( $props as $prop => $value ) {

				if ( isset( $compat_props[ $prop ] ) ) {
					$prop = $compat_props[ $prop ];
				}

				$object->$prop = $value;
			}
		}

		return $object;
	}


	/**
	 * Gets an object's stored meta value.
	 *
	 * @since 4.6.0
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 * @param bool $single whether to get the meta as a single item. Defaults to `true`
	 * @param string $context if 'view' then the value will be filtered
	 * @return mixed
	 */
	public static function get_meta( $object, $key = '', $single = true, $context = 'edit' ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$value = $object->get_meta( $key, $single, $context );

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;

			$value = get_post_meta( $object_id, $key, $single );
		}

		return $value;
	}


	/**
	 * Stores an object meta value.
	 *
	 * @since 4.6.0
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 * @param string $value the meta value
	 * @param bool $unique Optional. Whether the meta should be unique.
	 */
	public static function add_meta_data( $object, $key, $value, $unique = false ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$object->add_meta_data( $key, $value, $unique );

			$object->save_meta_data();

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;

			add_post_meta( $object_id, $key, $value, $unique );
		}
	}


	/**
	 * Updates an object's stored meta value.
	 *
	 * @since 4.6.0
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 * @param string $value the meta value
	 * @param int|string $meta_id Optional. The specific meta ID to update
	 */
	public static function update_meta_data( $object, $key, $value, $meta_id = '' ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$object->update_meta_data( $key, $value, $meta_id );

			$object->save_meta_data();

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;

			update_post_meta( $object_id, $key, $value );
		}
	}


	/**
	 * Deletes an object's stored meta value.
	 *
	 * @since 4.6.0
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 */
	public static function delete_meta_data( $object, $key ) {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$object->delete_meta_data( $key );

			$object->save_meta_data();

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;

			delete_post_meta( $object_id, $key );
		}
	}


}


endif; // Class exists check
