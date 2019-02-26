<?php
/**
 * WooCommerce Compatibility Class
 *
 * @package     woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Chained_Products_WC_Compatibility' ) ) {

	/**
	 * WooCommerce Compatibility Class for Chained Products
	 */
	class Chained_Products_WC_Compatibility {

		/**
		 * Is WooCommerce 2.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_25() {
			return self::is_wc_greater_than( '2.4.13' );
		}

		/**
		 * Is WooCommerce 2.6
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_26() {
			return self::is_wc_greater_than( '2.5.5' );
		}

		/**
		 * Is WooCommerce 3.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_30() {
			return self::is_wc_greater_than( '2.6.14' );
		}

		/**
		 * Is WooCommerce 3.1
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_31() {
			return self::is_wc_greater_than( '3.0.9' );
		}

		/**
		 * Is WooCommerce 3.2
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_32() {
			return self::is_wc_greater_than( '3.1.2' );
		}

		/**
		 * Is WooCommerce 3.3
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_33() {
			return self::is_wc_greater_than( '3.2.6' );
		}

		/**
		 * Is WooCommerce 3.4
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_34() {
			return self::is_wc_greater_than( '3.3.5' );
		}

		/**
		 * Is WooCommerce 3.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_35() {
			return self::is_wc_greater_than( '3.4.7' );
		}

		/**
		 * WooCommerce Current WooCommerce Version
		 *
		 * @return string woocommerce version
		 */
		public static function get_wc_version() {
			if ( defined( 'WC_VERSION' ) && WC_VERSION ) {
				return WC_VERSION;
			}
			if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) {
				return WOOCOMMERCE_VERSION;
			}
			return null;
		}

		/**
		 * Compare passed version with woocommerce current version
		 *
		 * @param string $version Version to compare with.
		 * @return boolean
		 */
		public static function is_wc_greater_than( $version ) {
			return version_compare( self::get_wc_version(), $version, '>' );
		}
	}
}
