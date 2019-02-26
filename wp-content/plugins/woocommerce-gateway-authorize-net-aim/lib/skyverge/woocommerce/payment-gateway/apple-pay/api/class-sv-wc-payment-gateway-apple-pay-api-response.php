<?php
/**
 * WooCommerce Payment Gateway Framework
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
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Apple-Pay
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Payment_Gateway_Apple_Pay_API_Response' ) ) :

/**
 * The Apple Pay API response object.
 *
 * @since 4.7.0
 */
class SV_WC_Payment_Gateway_Apple_Pay_API_Response extends SV_WC_API_JSON_Response {


	/**
	 * Gets the status code.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_status_code() {

		return $this->statusCode;
	}


	/**
	 * Gets the status message.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_status_message() {

		return $this->statusMessage;
	}


	/**
	 * Gets the validated merchant session.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_merchant_session() {

		return $this->raw_response_json;
	}


	/**
	 * Get the string representation of this response with any and all sensitive
	 * elements masked or removed.
	 *
	 * No strong indication from the Apple documentation that these _need_ to be
	 * masked, but they don't provide any useful info and only make the debug
	 * logs unnecessarily huge.
	 *
	 * @since 4.7.0
	 * @see SV_WC_API_Response::to_string_safe()
	 *
	 * @return string
	 */
	public function to_string_safe() {

		$string = $this->to_string();

		// mask the merchant session ID
		$string = str_replace( $this->merchantSessionIdentifier, str_repeat( '*', 10 ), $string );

		// mask the merchant ID
		$string = str_replace( $this->merchantIdentifier, str_repeat( '*', 10 ), $string );

		// mask the signature
		$string = str_replace( $this->signature, str_repeat( '*', 10 ), $string );

		return $string;
	}


}

endif;
