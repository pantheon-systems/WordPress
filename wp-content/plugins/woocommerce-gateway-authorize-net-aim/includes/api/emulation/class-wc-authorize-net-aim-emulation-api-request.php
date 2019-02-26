<?php
/**
 * WooCommerce Authorize.Net AIM Gateway
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net AIM Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net AIM Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-aim/
 *
 * @package   WC-Gateway-Authorize-Net-AIM/API/Request
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Authorize.Net AIM Emulation API Request Class
 *
 * Generates name/value pair data required by the legacy AIM API
 *
 * @link http://www.authorize.net/support/AIM_guide.pdf
 *
 * @since 3.8.0
 */
class WC_Authorize_Net_AIM_Emulation_API_Request implements SV_WC_Payment_Gateway_API_Request {


	/** auth/capture transaction type */
	const AUTH_CAPTURE = 'AUTH_CAPTURE';

	/** authorize only transaction type */
	const AUTH_ONLY = 'AUTH_ONLY';

	/** prior auth-only capture transaction type */
	const PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

	/** @var WC_Order optional order object if this request was associated with an order */
	protected $order;

	/** @var string API login ID value */
	protected $api_login_id;

	/** @var string API transaction key value */
	protected $api_transaction_key;

	/** @var array request data */
	protected $request_data;


	/**
	 * Construct request object
	 *
	 * @since 3.8.0
	 * @param string $api_login_id API login ID
	 * @param string $api_transaction_key API transaction key
	 */
	public function __construct( $api_login_id, $api_transaction_key ) {

		$this->api_login_id        = $api_login_id;
		$this->api_transaction_key = $api_transaction_key;
	}


	/**
	 * Creates a credit card charge request for the payment method / customer associated with $order
	 *
	 * @since 3.8.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_charge( WC_Order $order ) {

		$this->order = $order;

		$this->create_transaction( self::AUTH_CAPTURE );
	}


	/**
	 * Creates a credit card auth request for the payment method / customer associated with $order
	 *
	 * @since 3.8.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_auth( WC_Order $order ) {

		$this->order = $order;

		$this->create_transaction( self::AUTH_ONLY );
	}


	/**
	 * Capture funds for a previous credit card authorization
	 *
	 * @since 3.8.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_capture( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'x_type'     => self::PRIOR_AUTH_CAPTURE,
			'x_amount'   => $order->capture->amount,
			'x_trans_id' => $order->capture->trans_id,
		);
	}


	/** Request Helper Methods ******************************************************/


	/**
	 * Create the transaction XML, this handles all transaction types and both credit card/eCheck transactions
	 *
	 * @since 3.8.0
	 * @param string $type transaction type
	 */
	private function create_transaction( $type ) {

		$this->request_data = array(
			'x_amount'        => $this->get_order()->payment_total,
			'x_currency_code' => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'currency', 'view' ),
			'x_card_num'      => $this->order->payment->account_number,
			'x_exp_date'      => sprintf( '%s-%s', $this->get_order()->payment->exp_month, $this->get_order()->payment->exp_year ),
			'x_card_code'     => $this->order->payment->csc,
			'x_invoice_num'   => $this->get_order()->get_order_number(),
			'x_description'   => SV_WC_Helper::str_truncate( $this->get_order()->description, 255 ),
			'x_line_item'     => $this->get_line_items(),
			'x_tax'           => $this->get_order()->get_total_tax(),
			'x_freight'       => $this->get_order()->get_total_shipping(),
			'x_email'         => is_email( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_email' ) ) ? SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_email' ) : '',
			'x_cust_id'       => $this->order->get_user_id(),
			'x_customer_ip'   => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'customer_ip_address' ),
		);

		$this->set_addresses();
	}


	/**
	 * Set the billing and shipping address information for the request
	 *
	 * @since 3.8.0
	 */
	private function set_addresses() {

		// address fields
		$fields = array(
			'billing'  => array(
				'first_name'  => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_first_name' ),                                        'limit' => 50 ),
				'last_name'   => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_last_name' ),                                         'limit' => 50 ),
				'company'     => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_company' ),                                           'limit' => 50 ),
				'address'     => array( 'value' => trim( SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_address_1' ) . ' ' . SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_address_2' ) ), 'limit' => 60 ),
				'city'        => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_city' ),                                              'limit' => 40 ),
				'state'       => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_state' ),                                             'limit' => 40 ),
				'zip'         => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_postcode' ),                                          'limit' => 20 ),
				'country'     => array( 'value' => SV_WC_Helper::convert_country_code( SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_country' ) ),     'limit' => 60 ),
				'phone'       => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_phone' ),                                             'limit' => 25 ),
			),
			'shipping' => array(
				'first_name' => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_first_name' ),                                         'limit' => 50 ),
				'last_name'  => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_last_name' ),                                          'limit' => 50 ),
				'company'    => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_company' ),                                            'limit' => 50 ),
				'address'    => array( 'value' => trim( SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_address_1' ) . ' ' . SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_address_2' ) ), 'limit' => 60 ),
				'city'       => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_city' ),                                               'limit' => 40 ),
				'state'      => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_state' ),                                              'limit' => 40 ),
				'zip'        => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_postcode' ),                                           'limit' => 20 ),
				'country'    => array( 'value' => SV_WC_Helper::convert_country_code( SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_country' ) ),      'limit' => 60 ),
			),
		);

		foreach ( array( 'billing', 'shipping' ) as $type ) {

			foreach ( $fields[ $type ] as $field_name => $field ) {

				if ( 'phone' === $field_name ) {

					$value = preg_replace( '/\D/', '', $field['value'] );

				} else {

					// authorize.net claims to support unicode, but not all code points yet. Unrecognized code points will display in their control panel with question marks
					$value = SV_WC_Helper::str_to_sane_utf8( $field['value'] );
				}

				if ( $value ) {

					$key = 'billing' === $type ? 'x_'  . $field_name : 'x_ship_to_' . $field_name;

					$this->request_data[ $key ] = SV_WC_Helper::str_truncate( $value, $field['limit'] );
				}
			}
		}
	}


	/**
	 * Adds line items to the request
	 *
	 * @since 3.8.0
	 * @return array
	 */
	protected function get_line_items() {

		$line_items = array();

		// order line items
		foreach ( SV_WC_Helper::get_order_line_items( $this->get_order() ) as $item ) {

			if ( $item->item_total >= 0 ) {

				// in order: item ID, nam, description, quantity, unit price, taxable or not
				$line_items[] = implode( '<|>', array(
					$item->id,
					SV_WC_Helper::str_to_sane_utf8( SV_WC_Helper::str_truncate( $item->name, 31 ) ),
					SV_WC_Helper::str_to_sane_utf8( SV_WC_Helper::str_truncate( $item->description, 255 ) ),
					$item->quantity,
					SV_WC_Helper::number_format( $item->item_total ),
					is_callable( array( $item->product, 'is_taxable' ) ) ? $item->product->is_taxable() : false
				) );
			}
		}

		// authorize.net only allows 30 line items per order
		if ( count( $line_items ) > 30 ) {
			$line_items = array_slice( $line_items, 0, 30 );
		}

		return $line_items;
	}


	/**
	 * Get the request data to be converted to XML
	 *
	 * @since 3.8.0
	 * @return array
	 */
	public function get_request_data() {

		// required for every transaction
		$transaction_data = array(
			'x_login'           => $this->api_login_id,
			'x_tran_key'        => $this->api_transaction_key,
			'x_relay_response'  => 'FALSE', // does not accept a boolean
			'x_response_format' => '2',
			'x_delim_data'      => 'TRUE', // does not accept a boolean
			'x_delim_char'      => '|',
			'x_encap_char'      => ':',
			'x_solution_id'     => 'A1000065',
			'x_version'         => '3.1',
			'x_method'          => 'CC',
		);

		// add request data
		$this->request_data = array_merge( $transaction_data, $this->request_data );

		/**
		 * API Request Data
		 *
		 * Allow actors to modify the request data before it's sent to Authorize.Net
		 *
		 * @since 3.2.0
		 * @param array $data request data to be filtered
		 * @param \WC_Order $order order instance
		 * @param \WC_Authorize_Net_AIM_API_Request $this, API request class instance
		 */
		$this->request_data = apply_filters( 'wc_authorize_net_aim_api_request_data', $this->request_data, $this->order, $this );

		// remove any empty fields
		foreach ( $this->request_data as $key => $value ) {

			if ( '' === $value || null === $value ) {
				unset( $this->request_data[ $key ] );
			}
		}

		return $this->request_data;
	}


	/** API Helper Methods ******************************************************/


	/**
	 * Returns the string representation of the request
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Request::to_string()
	 * @return string
	 */
	public function to_string() {

		return http_build_query( $this->get_request_data(), '', '&' );
	}


	/**
	 * Returns the string representation of this request with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Request::to_string_safe()
	 * @return string the request XML, safe for logging/displaying
	 */
	public function to_string_safe() {

		$this->request_data = $this->get_request_data();

		// login ID/transaction key
		$this->request_data['x_login']    = str_repeat( '*', strlen( $this->request_data['x_login'] ) );
		$this->request_data['x_tran_key'] = str_repeat( '*', strlen( $this->request_data['x_tran_key'] ) );

		// credit card number
		$this->request_data['x_card_num'] = substr( $this->request_data['x_card_num'], 0, 1 ) . str_repeat( '*', strlen( $this->request_data['x_card_num'] ) - 5 ) . substr( $this->request_data['x_card_num'], -4 );

		// credit card CSC
		$this->request_data['x_card_code']  = str_repeat( '*', strlen( $this->request_data['x_card_code'] ) );

		return rawurldecode( http_build_query( $this->request_data, '', '&' ) );
	}


	/**
	 * Returns the order associated with this request, if there was one
	 *
	 * @since 3.8.0
	 * @return WC_Order the order object
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * The request method is always POST
	 *
	 * @since 3.8.0
	 * @return string
	 */
	public function get_method() {

		return 'POST';
	}


	/**
	 * Authorize.Net AIM emulation does not vary the request path per request.
	 *
	 * @since 3.8.0
	 * @return string
	 */
	public function get_path() {
		return '';
	}


}
