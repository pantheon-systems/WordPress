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
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Payment_Gateway_Payment_Token' ) ) :

/**
 * WooCommerce Payment Gateway Token
 *
 * Represents a credit card or check payment token
 */
class SV_WC_Payment_Gateway_Payment_Token {


	/** @var string payment gateway token ID */
	protected $id;

	/**
	 * @var array associated token data
	 */
	protected $data;

	/**
	 * @var string payment type image url
	 */
	protected $img_url;


	/**
	 * Initialize a payment token with associated $data which is expected to
	 * have the following members:
	 *
	 * default      - boolean optional indicates this is the default payment token
	 * type         - string one of 'credit_card' or 'echeck' ('check' for backwards compatibility)
	 * last_four    - string last four digits of account number
	 * card_type    - string credit card type: visa, mc, amex, disc, diners, jcb, etc (credit card only)
	 * exp_month    - string optional expiration month MM (credit card only)
	 * exp_year     - string optional expiration year YYYY (credit card only)
	 * account_type - string one of 'checking' or 'savings' (checking gateway only)
	 *
	 * @since 1.0.0
	 * @param string $id the payment gateway token ID
	 * @param array $data associated data
	 */
	public function __construct( $id, $data ) {

		if ( isset( $data['type'] ) && 'credit_card' == $data['type'] ) {

			// normalize the provided card type to adjust for possible abbreviations if set
			if ( isset( $data['card_type'] ) && $data['card_type'] ) {

				$data['card_type'] = SV_WC_Payment_Gateway_Helper::normalize_card_type( $data['card_type'] );

			// otherwise, get the payment type from the account number
			} elseif ( isset( $data['account_number'] ) ) {

				$data['card_type'] = SV_WC_Payment_Gateway_Helper::card_type_from_account_number( $data['account_number'] );
			}
		}

		// remove account number so it's not saved to the token
		unset( $data['account_number'] );

		$this->id    = $id;
		$this->data  = $data;
	}


	/**
	 * Returns the payment token string
	 *
	 * @since 1.0.0
	 * @deprecated since 4.0.0
	 * @return string payment token string
	 */
	public function get_token() {

		_deprecated_function( 'SV_WC_Payment_Gateway_Payment_Token::get_token()', '4.0.0', 'SV_WC_Payment_Gateway_Payment_Token::get_id()' );

		return $this->get_id();
	}


	/**
	 * Returns the payment token string
	 *
	 * @since 4.0.0
	 * @return string payment token string
	 */
	public function get_id() {

		return $this->id;
	}


	/**
	 * Returns true if this payment token is default
	 *
	 * @since 1.0.0
	 * @return boolean true if this payment token is default
	 */
	public function is_default() {

		return isset( $this->data['default'] ) && $this->data['default'];
	}


	/**
	 * Makes this payment token the default or a non-default one
	 *
	 * @since 1.0.0
	 * @param boolean $default true or false
	 */
	public function set_default( $default ) {

		$this->data['default'] = $default;
	}


	/**
	 * Returns true if this payment token represents a credit card
	 *
	 * @since 1.0.0
	 * @return boolean true if this payment token represents a credit card
	 */
	public function is_credit_card() {

		return 'credit_card' == $this->data['type'];
	}


	/**
	 * Returns true if this payment token represents an eCheck
	 *
	 * @since 1.0.0
	 * @deprecated since 4.0.0
	 * @return boolean true if this payment token represents an eCheck
	 */
	public function is_check() {

		return $this->is_echeck();
	}


	/**
	 * Returns true if this payment token represents an eCheck
	 *
	 * @since 4.0.0
	 * @return boolean true if this payment token represents an eCheck
	 */
	public function is_echeck() {

		return ! $this->is_credit_card();
	}


	/**
	 * Returns the payment type, one of 'credit_card' or 'echeck'
	 *
	 * @since 1.0.0
	 * @return string the payment type
	 */
	public function get_type() {

		return $this->data['type'];
	}


	/**
	 * Returns the card type ie visa, mc, amex, disc, diners, jcb, etc
	 *
	 * Credit card gateway only
	 *
	 * @since 1.0.0
	 * @return string the payment type
	 */
	public function get_card_type() {

		return isset( $this->data['card_type'] ) ? $this->data['card_type'] : null;
	}


	/**
	 * Set the card type
	 *
	 * Credit Card gateway only
	 *
	 * @since 4.0.0
	 * @param string $card_type
	 */
	public function set_card_type( $card_type ) {

		$this->data['card_type'] = $card_type;
	}


	/**
	 * Determine the credit card type from the full account number
	 *
	 * @since 1.0.0
	 * @deprecated since 4.0.0 in favor of SV_WC_Payment_Gateway_Helper::card_type_from_account_number()
	 * @param string $account_number the credit card account number
	 * @return string the credit card type
	 */
	public static function type_from_account_number( $account_number ) {

		return SV_WC_Payment_Gateway_Helper::card_type_from_account_number( $account_number );
	}


	/**
	 * Returns the bank account type, one of 'checking' or 'savings'
	 *
	 * eCheck gateway only
	 *
	 * @since 1.0.0
	 * @return string the payment type
	 */
	public function get_account_type() {

		return isset( $this->data['account_type'] ) ? $this->data['account_type'] : null;
	}


	/**
	 * Set the account type
	 *
	 * eCheck gateway only
	 *
	 * @since 4.0.0
	 * @param string $account_type
	 */
	public function set_account_type( $account_type ) {

		$this->data['account_type'] = $account_type;
	}


	/**
	 * Returns the full payment type, ie Visa, MasterCard, American Express,
	 * Discover, Diners, JCB, eCheck, etc
	 *
	 * @since 1.0.0
	 * @return string the payment type
	 */
	public function get_type_full() {

		if ( $this->is_credit_card() ) {
			$type = $this->get_card_type() ? $this->get_card_type() : 'card';
		} else {
			$type = $this->get_account_type() ? $this->get_account_type() : 'bank';
		}

		return SV_WC_Payment_Gateway_Helper::payment_type_to_name( $type );
	}


	/**
	 * Returns the last four digits of the credit card or check account number
	 *
	 * @since 1.0.0
	 * @return string last four of account
	 */
	public function get_last_four() {

		return isset( $this->data['last_four'] ) ? $this->data['last_four'] : null;
	}


	/**
	 * Set the account last four
	 *
	 * @since 4.0.0
	 * @param string $last_four
	 */
	public function set_last_four( $last_four ) {

		$this->data['last_four'] = $last_four;
	}


	/**
	 * Returns the expiration month of the credit card.  This should only be
	 * called for credit card tokens
	 *
	 * @since 1.0.0
	 * @return string expiration month as a two-digit number
	 */
	public function get_exp_month() {

		return isset( $this->data['exp_month'] ) ? $this->data['exp_month'] : null;
	}


	/**
	 * Set the expiration month
	 *
	 * @since 4.0.0
	 * @param string $month
	 */
	public function set_exp_month( $month ) {

		$this->data['exp_month'] = $month;
	}


	/**
	 * Returns the expiration year of the credit card.  This should only be
	 * called for credit card tokens
	 *
	 * @since 1.0.0
	 * @return string expiration year as a four-digit number
	 */
	public function get_exp_year() {

		return isset( $this->data['exp_year'] ) ? $this->data['exp_year'] : null;
	}


	/**
	 * Set the expiration year
	 *
	 * @since 4.0.0
	 * @param string $year
	 */
	public function set_exp_year( $year ) {

		$this->data['exp_year'] = $year;
	}


	/**
	 * Returns the expiration date in the format MM/YY, suitable for use
	 * in order notes or other customer-facing areas
	 *
	 * @since 1.0.0
	 * @return string formatted expiration date
	 */
	public function get_exp_date() {

		return $this->get_exp_month() . '/' . substr( $this->get_exp_year(), -2 );
	}


	/**
	 * Set the full image URL based on the token payment type.  Note that this
	 * is available for convenience during a single request and will not be
	 * included in persistent storage
	 *
	 * @see SV_WC_Payment_Gateway_Payment_Token::get_image_url()
	 * @since 1.0.0
	 * @param string $url the full image URL
	 */
	public function set_image_url( $url ) {

		$this->img_url = $url;
	}


	/**
	 * Get the full image URL based on teh token payment type.
	 *
	 * @see SV_WC_Payment_Gateway_Payment_Token::set_image_url()
	 * @since 1.0.0
	 * @return string the full image URL
	 */
	public function get_image_url() {

		return $this->img_url;
	}


	/**
	 * Returns a representation of this token suitable for persisting to a
	 * datastore
	 *
	 * @since 1.0.0
	 * @return mixed datastore representation of token
	 */
	public function to_datastore_format() {

		return $this->data;
	}


}

endif;  // class exists check
