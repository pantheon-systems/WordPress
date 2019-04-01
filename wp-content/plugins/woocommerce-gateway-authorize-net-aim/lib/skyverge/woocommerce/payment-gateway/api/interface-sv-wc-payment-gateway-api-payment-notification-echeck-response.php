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

if ( ! interface_exists( 'SV_WC_Payment_Gateway_API_Payment_Notification_eCheck_Response' ) ) :

/**
 * WooCommerce Payment Gateway API Payment eCheck Notification Response
 *
 * Represents an IPN or redirect-back eCheck request response
 *
 * @since 2.2.0
 */
interface SV_WC_Payment_Gateway_API_Payment_Notification_eCheck_Response extends SV_WC_Payment_Gateway_API_Payment_Notification_Response {



	/**
	 * Returns the account type, one of 'checking' or 'savings', if available
	 *
	 * @since 2.2.0
	 * @return string account type, one of 'checking' or 'savings'
	 */
	public function get_account_type();


	/**
	 * Returns the check number used, if available
	 *
	 * @since 2.2.0
	 * @return int check number, or null
	 */
	public function get_check_number();


}

endif;  // interface exists check
