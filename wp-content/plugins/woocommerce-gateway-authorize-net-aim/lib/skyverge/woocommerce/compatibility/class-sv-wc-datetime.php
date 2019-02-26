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

if ( ! class_exists( 'SV_WC_DateTime' ) ) :

/**
 * Backports the \WC_DateTime class to WooCommerce pre-3.0.0
 *
 * TODO: Remove this when WC 3.x can be required {CW 2017-03-16}
 *
 * @since 4.6.0
 */
class SV_WC_DateTime extends DateTime {


	/**
	 * Outputs an ISO 8601 date string in local timezone.
	 *
	 * @since 4.6.0
	 * @return string
	 */
	public function __toString() {

		return $this->format( DATE_ATOM );
	}


	/**
	 * Gets the UTC timestamp.
	 *
	 * Missing in PHP 5.2.
	 *
	 * @since 4.6.0
	 * @return int
	 */
	public function getTimestamp() {

		return method_exists( 'DateTime', 'getTimestamp' ) ? parent::getTimestamp() : $this->format( 'U' );
	}


	/**
	 * Gets the timestamp with the WordPress timezone offset added or subtracted.
	 *
	 * @since 4.6.0
	 * @return int
	 */
	public function getOffsetTimestamp() {

		return $this->getTimestamp() + $this->getOffset();
	}


	/**
	 * Gets a date based on the offset timestamp.
	 *
	 * @since 4.6.0
	 * @param  string $format date format
	 * @return string
	 */
	public function date( $format ) {

		return gmdate( $format, $this->getOffsetTimestamp() );
	}


	/**
	 * Gets a localised date based on offset timestamp.
	 *
	 * @since 4.6.0
	 * @param  string $format date format
	 * @return string
	 */
	public function date_i18n( $format = 'Y-m-d' ) {

		return date_i18n( $format, $this->getOffsetTimestamp() );
	}


}

endif;
