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
 * Authorize.Net AIM API Request Class
 *
 * Generates XML required by API specs to perform an API request
 *
 * @link http://www.authorize.net/support/AIM_guide_XML.pdf
 *
 * @since 3.0
 */
class WC_Authorize_Net_AIM_API_Request extends SV_WC_API_XML_Request implements SV_WC_Payment_Gateway_API_Request {


	/** auth/capture transaction type */
	const AUTH_CAPTURE = 'authCaptureTransaction';

	/** authorize only transaction type */
	const AUTH_ONLY = 'authOnlyTransaction';

	/** prior auth-only capture transaction type */
	const PRIOR_AUTH_CAPTURE = 'priorAuthCaptureTransaction';

	/** refund transaction type */
	const REFUND = 'refundTransaction';

	/** void transaction type */
	const VOID = 'voidTransaction';

	/** @var WC_Order optional order object if this request was associated with an order */
	protected $order;

	/** @var string API login ID value */
	private $api_login_id;

	/** @var string API transaction key value */
	private $api_transaction_key;


	/**
	 * Construct request object
	 *
	 * @since 3.0
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
	 * @since 3.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_charge( WC_Order $order ) {

		$this->order = $order;

		$this->create_transaction( self::AUTH_CAPTURE );
	}


	/**
	 * Creates a credit card auth request for the payment method / customer associated with $order
	 *
	 * @since 3.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_auth( WC_Order $order ) {

		$this->order = $order;

		$this->create_transaction( self::AUTH_ONLY );
	}


	/**
	 * Capture funds for a previous credit card authorization
	 *
	 * @since 3.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_capture( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'transactionRequest' => array(
				'transactionType' => self::PRIOR_AUTH_CAPTURE,
				'amount'          => $order->capture->amount,
				'refTransId'      => $order->capture->trans_id,
			),
		);
	}


	/**
	 * Creates a customer check debit request for the given $order
	 *
	 * @since 3.0
	 * @param WC_Order $order the order object
	 */
	public function create_echeck_debit( WC_Order $order ) {

		$this->order = $order;

		$this->create_transaction( self::AUTH_CAPTURE );
	}


	/** Create a refund for the given $order
	 *
	 * @since 3.3.0
	 * @param WC_Order $order order object
	 */
	public function create_refund( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'refId'              => SV_WC_Order_Compatibility::get_prop( $order, 'id' ),
			'transactionRequest' => array(
				'transactionType' => self::REFUND,
				'amount'          => $order->refund->amount,
				'payment'         => array(
					'creditCard' => array(
						'cardNumber'     => $order->refund->account_four,
						'expirationDate' => $order->refund->expiry_date,
					),
				),
				'refTransId'      => $order->refund->trans_id,
				'order'           => array(
					'invoiceNumber' => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-aim' ) ),
					'description'   => SV_WC_Helper::str_truncate( $this->order->refund->reason, 255 ),
				),
			),
		);
	}


	/** Create a void for the given $order
	 *
	 * @since 3.3.0
	 * @param WC_Order $order order object
	 */
	public function create_void( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'refId'              => SV_WC_Order_Compatibility::get_prop( $order, 'id' ),
			'transactionRequest' => array(
				'transactionType' => self::VOID,
				'refTransId'      => $order->refund->trans_id,
				'order'           => array(
					'invoiceNumber' => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-aim' ) ),
					'description'   => SV_WC_Helper::str_truncate( $this->order->refund->reason, 255 ),
				),
			),
		);
	}


	/** Request Helper Methods ******************************************************/


	/**
	 * Create the transaction XML, this handles all transaction types and both credit card/eCheck transactions
	 *
	 * @since 3.0
	 * @param string $type transaction type
	 */
	private function create_transaction( $type ) {

		$this->request_data = array(
			'refId'              => SV_WC_Order_Compatibility::get_prop( $this->order, 'id' ),
			'transactionRequest' => array(
				'transactionType'     => $type,
				'amount'              => $this->order->payment_total,
				'currencyCode'        => SV_WC_Order_Compatibility::get_prop( $this->order, 'currency', 'view' ),
				'payment'             => $this->get_payment(),
				'solution'            => array(
					'id' => 'A1000065',
				),
				'order'               => array(
					'invoiceNumber' => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-aim' ) ),
					'description'   => SV_WC_Helper::str_truncate( $this->order->description, 255 ),
				),
				'lineItems'           => $this->get_line_items(),
				'tax'                 => $this->get_taxes(),
				'shipping'            => $this->get_shipping(),
				'customer'            => $this->get_customer(),
				'billTo'              => $this->get_address( 'billing' ),
				'shipTo'              => $this->get_address( 'shipping' ),
				'customerIP'          => SV_WC_Order_Compatibility::get_prop( $this->order, 'customer_ip_address' ),
				'transactionSettings' => $this->get_transaction_settings(),
			),
		);

		// remove any empty fields
		foreach ( $this->request_data['transactionRequest'] as $key => $value ) {

			if ( empty( $value ) ) {
				unset( $this->request_data['transactionRequest'][ $key ] );
			}
		}
	}


	/**
	 * Adds payment information to the request
	 *
	 * @since 3.0
	 * @return array
	 */
	private function get_payment() {

		if ( 'credit_card' == $this->order->payment->type ) {

			if ( ! empty( $this->order->payment->opaque_value ) ) {

				$payment = array(
					'opaqueData' => array(
						'dataDescriptor' => $this->order->payment->opaque_descriptor,
						'dataValue'      => $this->order->payment->opaque_value,
					),
				);

			} else {

				$payment = array(
					'creditCard' => array(
						'cardNumber'     => $this->order->payment->account_number,
						'expirationDate' => sprintf( '%s-%s', $this->order->payment->exp_month, $this->order->payment->exp_year ),
					),
				);

				// add CSC is available
				if ( ! empty( $this->order->payment->csc ) ) {
					$payment['creditCard']['cardCode'] = $this->order->payment->csc;
				}
			}

		} else {

			$payment = array(
				'bankAccount' => array(
					'accountType'   => $this->order->payment->account_type,
					'routingNumber' => $this->order->payment->routing_number,
					'accountNumber' => $this->order->payment->account_number,
					'nameOnAccount' => SV_WC_Helper::str_truncate( $this->order->get_formatted_billing_full_name(), 22 ),
					'echeckType'    => 'WEB',
				),
			);
		}

		return $payment;
	}


	/**
	 * Adds line items to the request
	 *
	 * @since 3.0
	 * @return array
	 */
	private function get_line_items() {

		$line_items = array();

		// order line items
		foreach ( SV_WC_Helper::get_order_line_items( $this->order ) as $item ) {

			if ( $item->item_total >= 0 ) {

				$line_items[] = array(
					'itemId'      => $item->id,
					'name'        => SV_WC_Helper::str_to_sane_utf8( SV_WC_Helper::str_truncate( $item->name, 31 ) ),
					'description' => SV_WC_Helper::str_to_sane_utf8( SV_WC_Helper::str_truncate( $item->description, 255 ) ),
					'quantity'    => $item->quantity,
					'unitPrice'   => SV_WC_Helper::number_format( $item->item_total ),
				);
			}
		}

		// order fees
		foreach ( $this->order->get_fees() as $fee_id => $fee ) {

			if ( $this->order->get_item_total( $fee ) >= 0 ) {

				$line_items[] = array(
					'itemId'      => $fee_id,
					'name'        => ! empty( $fee['name'] ) ? SV_WC_Helper::str_truncate( htmlentities( $fee['name'], ENT_QUOTES, 'UTF-8', false ), 31 ) : __( 'Fee', 'woocommerce-gateway-authorize-net-aim' ),
					'description' => __( 'Order Fee', 'woocommerce-gateway-authorize-net-aim' ),
					'quantity'    => 1,
					'unitPrice'   => SV_WC_Helper::number_format( $this->order->get_item_total( $fee ) ),
				);
			}
		}

		// authorize.net only allows 30 line items per order
		if ( count( $line_items ) > 30 ) {
			$line_items = array_slice( $line_items, 0, 30 );
		}

		return array( 'lineItem' => $line_items );
	}


	/**
	 * Adds tax information to the request
	 *
	 * @since 3.0
	 * @return array
	 */
	private function get_taxes() {

		if ( $this->order->get_total_tax() > 0 ) {

			$taxes = array();

			foreach ( $this->order->get_tax_totals() as $tax_code => $tax ) {

				$taxes[] = sprintf( '%s (%s) - %s', $tax->label, $tax_code, $tax->amount );
			}

			return array(
				'amount'      => SV_WC_Helper::number_format( $this->order->get_total_tax() ),
				'name'        => __( 'Order Taxes', 'woocommerce-gateway-authorize-net-aim' ),
				'description' => SV_WC_Helper::str_truncate( implode( ', ', $taxes ), 255 ),
			);

		} else {

			return array();
		}
	}


	/**
	 * Adds shipping information to the request
	 *
	 * @since 3.0
	 * @return array
	 */
	private function get_shipping() {

		if ( $this->order->get_total_shipping() > 0 ) {

			return array(
				'amount'      => SV_WC_Helper::number_format( $this->order->get_total_shipping() ),
				'name'        => __( 'Order Shipping', 'woocommerce-gateway-authorize-net-aim' ),
				'description' => SV_WC_Helper::str_truncate( $this->order->get_shipping_method(), 255 ),
			);

		} else {

			return array();
		}
	}


	/**
	 * Get the customer data for the transaction request
	 *
	 * @since 3.2.0
	 * @return array
	 */
	private function get_customer() {

		$customer = array(
			'id' => $this->order->get_user_id(),
		);

		if ( is_email( SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_email' ) ) ) {
			$customer['email'] = SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_email' );
		}

		return $customer;
	}


	/**
	 * Get the billing or shipping address information for the request
	 *
	 * @since 3.0
	 * @param string $type address type, either `billing` or `shipping`
	 * @return array address data
	 */
	private function get_address( $type ) {

		// address fields
		$fields = array(
			'billing'  => array(
				'firstName'   => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_first_name' ),                                        'limit' => 50 ),
				'lastName'    => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_last_name' ),                                         'limit' => 50 ),
				'company'     => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_company' ),                                           'limit' => 50 ),
				'address'     => array( 'value' => trim( SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_address_1' ) . ' ' . SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_address_2' ) ), 'limit' => 60 ),
				'city'        => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_city' ),                                              'limit' => 40 ),
				'state'       => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_state' ),                                             'limit' => 40 ),
				'zip'         => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_postcode' ),                                          'limit' => 20 ),
				'country'     => array( 'value' => SV_WC_Helper::convert_country_code( SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_country' ) ),     'limit' => 60 ),
				'phoneNumber' => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'billing_phone' ),                                             'limit' => 25 ),
			),
			'shipping' => array(
				'firstName' => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_first_name' ),                                         'limit' => 50 ),
				'lastName'  => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_last_name' ),                                          'limit' => 50 ),
				'company'   => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_company' ),                                            'limit' => 50 ),
				'address'   => array( 'value' => trim( SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_address_1' ) . ' ' . SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_address_2' ) ), 'limit' => 60 ),
				'city'      => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_city' ),                                               'limit' => 40 ),
				'state'     => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_state' ),                                              'limit' => 40 ),
				'zip'       => array( 'value' => SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_postcode' ),                                           'limit' => 20 ),
				'country'   => array( 'value' => SV_WC_Helper::convert_country_code( SV_WC_Order_Compatibility::get_prop( $this->order, 'shipping_country' ) ),      'limit' => 60 ),
			),
		);

		$address = array();

		foreach ( $fields[ $type ] as $field_name => $field ) {

			if ( 'phone' === $field_name ) {

				$value = preg_replace( '/\D/', '', $field['value'] );

			} else {

				// authorize.net claims to support unicode, but not all code points yet. Unrecognized code points will display in their control panel with question marks
				$value = SV_WC_Helper::str_to_sane_utf8( $field['value'] );
			}

			if ( $value ) {
				$address[ $field_name ] = SV_WC_Helper::str_truncate( $value, $field['limit'] );
			}
		}

		return $address;
	}


	/**
	 * Add transactions settings, primarily used for setting the duplicate window check when the CSC is required
	 *
	 * This is important because of this use case:
	 *
	 * 1) Customer enters payment info and accidentally enters an incorrect CVV
	 * 2) Auth.net properly declines the transaction
	 * 3) Customer notices the CVV was incorrect, re-enters the correct CVV and tries to submit order
	 * 4) Auth.net rejects this second transaction attempt as a "duplicate transaction"
	 *
	 * For some reason, Auth.net doesn't consider the CVV changing evidence of a non-duplicate transaction and recommends
	 * changing the `duplicateWindow` transaction option between transactions (https://support.authorize.net/authkb/index?page=content&id=A425&actp=search&viewlocale=en_US&searchid=1375994496602)
	 * to avoid this error. However, simply changing the `duplicateWindow` between transactions *does not* prevent
	 * the "duplicate transaction" error.
	 *
	 * The `duplicateWindow` must actually be set to 0 to suppress this error. However, this has the side affect of
	 * potentially allowing duplicate transactions through.
	 *
	 * @since 3.0
	 */
	private function get_transaction_settings() {

		$settings = array();

		if ( ! empty( $this->order->payment->csc ) ) {

			$settings['duplicateWindow'] = 0;

			return array(
				'setting' => array(
					array(
						'settingName' => 'duplicateWindow',
						'settingValue' => 0,
					),
				),
			);

		} else {

			return array();
		}
	}


	/**
	 * Get the request data to be converted to XML.
	 *
	 * @since 3.6.0
	 * @return array
	 */
	public function get_request_data() {

		// required for every transaction
		$transaction_data = array(
			'@attributes'            => array( 'xmlns' => 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' ),
			'merchantAuthentication' => array(
				'name'           => $this->api_login_id,
				'transactionKey' => $this->api_transaction_key,
			),
		);

		// add required request data
		$this->request_data = array(
			$this->get_root_element() => array_merge( $transaction_data, $this->request_data )
		);

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

		return $this->request_data;
	}


	/** API Helper Methods ******************************************************/


	/**
	 * Returns the string representation of this request with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 3.2.0
	 * @see SV_WC_Payment_Gateway_API_Request::to_string_safe()
	 * @return string the request XML, safe for logging/displaying
	 */
	public function to_string_safe() {

		$string = $this->to_string();

		// API login ID
		if ( preg_match( '/<merchantAuthentication>(\s*)<name>(\w+)<\/name>/', $string, $matches ) ) {
			$string = preg_replace( '/<merchantAuthentication>\s*<name>\w+<\/name>/', "<merchantAuthentication>{$matches[1]}<name>" . str_repeat( '*', strlen( $matches[2] ) ) . '</name>', $string );
		}

		// API transaction key
		if ( preg_match( '/<transactionKey>(\w+)<\/transactionKey>/', $string, $matches ) ) {
			$string = preg_replace( '/<transactionKey>\w+<\/transactionKey>/', '<transactionKey>' . str_repeat( '*', strlen( $matches[1] ) ) . '</transactionKey>', $string );
		}

		// card number
		if ( preg_match( '/<cardNumber>(\d+)<\/cardNumber>/', $string, $matches ) && strlen( $matches[1] ) > 4 ) {
			$string = preg_replace( '/<cardNumber>\d+<\/cardNumber>/', '<cardNumber>' . substr( $matches[1], 0, 1 ) . str_repeat( '*', strlen( $matches[1] ) - 5 ) . substr( $matches[1], -4 ) . '</cardNumber>', $string );
		}

		// real CSC code
		$string = preg_replace( '/<cardCode>\d+<\/cardCode>/', '<cardCode>***</cardCode>', $string );

		// bank account number
		if ( preg_match( '/<accountNumber>(\d+)<\/accountNumber>/', $string, $matches ) ) {
			$string = preg_replace( '/<accountNumber>\d+<\/accountNumber>/', '<accountNumber>' . str_repeat( '*', strlen( $matches[1] ) ) . '</accountNumber>', $string );
		}

		// routing number
		if ( preg_match( '/<routingNumber>(\d+)<\/routingNumber>/', $string, $matches ) ) {
			$string = preg_replace( '/<routingNumber>\d+<\/routingNumber>/', '<routingNumber>' . str_repeat( '*', strlen( $matches[1] ) ) . '</routingNumber>', $string );
		}

		if ( preg_match( '/<dataValue>(\w+)<\/dataValue>/', $string, $matches ) ) {
			$string = preg_replace( '/<dataValue>\w+<\/dataValue>/', '<dataValue>' . str_repeat( '*', 10 ) . '</dataValue>', $string );
		}

		return $this->prettify_xml( $string );
	}


	/**
	 * Returns the order associated with this request, if there was one
	 *
	 * @since 3.0
	 * @return WC_Order the order object
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Get the root element for the XML document.
	 *
	 * @since 3.6.0
	 * @return string
	 */
	protected function get_root_element() {
		return 'createTransactionRequest';
	}


}
