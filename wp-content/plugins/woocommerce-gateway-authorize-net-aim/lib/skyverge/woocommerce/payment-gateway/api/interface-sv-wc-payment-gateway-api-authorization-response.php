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

if ( ! interface_exists( 'SV_WC_Payment_Gateway_API_Authorization_Response' ) ) :

/**
 * WooCommerce Direct Payment Gateway API Authorization Response
 *
 * Represents a Payment Gateway Credit Card Authorization response.  This should
 * also be used as the parent class for credit card charge (authorization +
 * capture) responses.
 */
interface SV_WC_Payment_Gateway_API_Authorization_Response extends SV_WC_Payment_Gateway_API_Response {


	/**
	 * The authorization code is returned from the credit card processor to
	 * indicate that the charge will be paid by the card issuer.
	 *
	 * @since 1.0.0
	 * @return string credit card authorization code
	 */
	public function get_authorization_code();


	/**
	 * Returns the result of the AVS check
	 *
	 * @since 1.0.0
	 * @return string result of the AVS check, if any
	 */
	public function get_avs_result();


	/**
	 * Returns the result of the CSC check
	 *
	 * @since 1.0.0
	 * @return string result of CSC check
	 */
	public function get_csc_result();


	/**
	 * Returns true if the CSC check was successful
	 *
	 * @since 1.0.0
	 * @return boolean true if the CSC check was successful
	 */
	public function csc_match();


} // SV_WC_Payment_Gateway_API_Authorization_Response

endif;  // interface exists check
