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
 * @package   SkyVerge/WooCommerce/Payment-Gateway/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! interface_exists( 'SV_WC_Payment_Gateway_API_Payment_Notification_Response' ) ) :

/**
 * WooCommerce Payment Gateway API Payment Notification Response
 *
 * Represents an IPN or redirect-back request response
 *
 * @since 2.1.0
 */
interface SV_WC_Payment_Gateway_API_Payment_Notification_Response extends SV_WC_Payment_Gateway_API_Response {


	/**
	 * Returns the order id associated with this response
	 *
	 * @since 2.1.0
	 * @return int the order id associated with this response, or null if it could not be determined
	 * @throws Exception if there was a serious error finding the order id
	 */
	public function get_order_id();


	/**
	 * Returns the order associated with this response
	 *
	 * @since 2.1.0
	 * @return WC_Order the order associated with this response, or null if it could not be determined
	 * @throws Exception if there was a serious error finding the order
	 */
	public function get_order();


	/**
	 * Returns true if the transaction was cancelled, false otherwise
	 *
	 * @since 2.1.0
	 * @return bool true if cancelled, false otherwise
	 */
	public function transaction_cancelled();


	/**
	 * Returns the card PAN or checking account number, if available
	 *
	 * @since 2.2.0
	 * @return string PAN or account number or null if not available
	 */
	public function get_account_number();


	/**
	 * Determine if this is an IPN response.
	 *
	 * Intentionally commented out to prevent fatal errors in older plugins
	 *
	 * @since 4.3.0
	 * @return bool
	 */
	// public function is_ipn();


}

endif;  // interface exists check
