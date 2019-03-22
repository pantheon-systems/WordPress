<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Update converted value via ajax.
add_action( 'wp_ajax_nopriv_ebanx_update_converted_value', 'ebanx_update_converted_value' );
add_action( 'wp_ajax_ebanx_update_converted_value', 'ebanx_update_converted_value' );

/**
 * It's a just a method to call `ebanx_update_converted_value`
 * to avoid WordPress hooks problem
 *
 * @return void
 * @throws Exception Param not found.
 */
function ebanx_update_converted_value() {
	$country = WC_EBANX_Request::read( 'country' );
	$gateway_class_name = 'WC_EBANX_Credit_Card_' . strtoupper( $country ) . '_Gateway';
	$gateway = new $gateway_class_name();

	echo $gateway->checkout_rate_conversion( // phpcs:ignore WordPress.XSS.EscapeOutput
		WC_EBANX_Request::read( 'currency' ),
		false,
		$country, // phpcs:ignore WordPress.XSS.EscapeOutput
		WC_EBANX_Request::read( 'instalments' ) // phpcs:ignore WordPress.XSS.EscapeOutput
	);

	wp_die();
}

/**
 * Class WC_EBANX_Gateway
 */
class WC_EBANX_Gateway extends WC_Payment_Gateway {

	/**
	 *
	 * @var $ebanx_params
	 */
	protected static $ebanx_params = array();

	/**
	 *
	 * @var int
	 */
	protected static $initialized_gateways = 0;

	/**
	 *
	 * @var int
	 */
	protected static $total_gateways = 0;

	/**
	 * Current user id
	 *
	 * @var int
	 */
	public $user_id;

	const REQUIRED_MARK = ' <abbr class="required" title="required">*</abbr>';

	/**
	 * Constructor
	 */
	public function __construct() {
		self::$total_gateways++;

		$this->user_id = get_current_user_id();

		$this->configs = new WC_EBANX_Global_Gateway();

		$this->is_sandbox_mode = ( 'yes' === $this->configs->settings['sandbox_mode_enabled'] );

		$this->private_key = $this->is_sandbox_mode ? $this->configs->settings['sandbox_private_key'] : $this->configs->settings['live_private_key'];

		$this->public_key = $this->is_sandbox_mode ? $this->configs->settings['sandbox_public_key'] : $this->configs->settings['live_public_key'];

		if ( 'yes' === $this->configs->settings['debug_enabled'] ) {
			$this->log = new WC_Logger();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'checkout_assets' ), 100 );

		add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ) );

		$this->supports = array( 'refunds' );

		$this->icon = $this->show_icon();

		$this->names = $this->get_billing_field_names();

		$this->merchant_currency = strtoupper( get_woocommerce_currency() );
	}

	/**
	 * Check if the method is available to show to the users
	 *
	 * @return boolean
	 */
	public function is_available() {
		return parent::is_available()
			&& 'yes' === $this->enabled
			&& ! empty( $this->public_key )
			&& ! empty( $this->private_key );
	}

	/**
	 * General method to check if the currency is USD or EUR. These currencies are accepted by all payment methods.
	 *
	 * @param  string $currency Possible currencies: USD, EUR.
	 * @return boolean          Return true if EBANX process the currency.
	 */
	public function currency_is_usd_eur( $currency ) {
		return in_array( $currency, array( WC_EBANX_Constants::CURRENCY_CODE_USD, WC_EBANX_Constants::CURRENCY_CODE_EUR ) );
	}

	/**
	 * Insert custom billing fields on checkout page
	 *
	 * @param  array $fields WooCommerce's fields.
	 * @return array         The new fields.
	 */
	public function checkout_fields( $fields ) {
		$fields_options = array();
		if ( isset( $this->configs->settings['brazil_taxes_options'] ) && is_array( $this->configs->settings['brazil_taxes_options'] ) ) {
			$fields_options = $this->configs->settings['brazil_taxes_options'];
		}

		$disable_own_fields = isset( $this->configs->settings['checkout_manager_enabled'] ) && 'yes' === $this->configs->settings['checkout_manager_enabled'];

		$cpf = get_user_meta( $this->user_id, '_ebanx_billing_brazil_document', true );

		$cnpj = get_user_meta( $this->user_id, '_ebanx_billing_brazil_cnpj', true );

		$rut = get_user_meta( $this->user_id, '_ebanx_billing_chile_document', true );

		$dni = get_user_meta( $this->user_id, '_ebanx_billing_colombia_document', true );

		$dni_pe = get_user_meta( $this->user_id, '_ebanx_billing_peru_document', true );

		$cdi = get_user_meta( $this->user_id, '_ebanx_billing_argentina_document', true );

		$ebanx_billing_brazil_person_type = array(
			'type'    => 'select',
			'label'   => __( 'Select an option', 'woocommerce-gateway-ebanx' ),
			'default' => 'cpf',
			'class'   => array( 'ebanx_billing_brazil_selector', 'ebanx-select-field' ),
			'options' => array(
				'cpf'  => __( 'CPF - Individuals', 'woocommerce-gateway-ebanx' ),
				'cnpj' => __( 'CNPJ - Companies', 'woocommerce-gateway-ebanx' ),
			),
		);

		$ebanx_billing_argentina_document_type = array(
			'type'    => 'select',
			'label'   => __( 'Select a document type', 'woocommerce-gateway-ebanx' ),
			'default' => 'ARG_CUIT',
			'class'   => array( 'ebanx_billing_argentina_selector', 'ebanx-select-field' ),
			'options' => array(
				'ARG_CUIT' => __( 'CUIT', 'woocommerce-gateway-ebanx' ),
				'ARG_CUIL' => __( 'CUIL', 'woocommerce-gateway-ebanx' ),
				'ARG_CDI'  => __( 'CDI', 'woocommerce-gateway-ebanx' ),
				'ARG_DNI'  => __( 'DNI', 'woocommerce-gateway-ebanx' ),
			),
		);

		$ebanx_billing_brazil_document = array(
			'type'    => 'text',
			'label'   => 'CPF' . self::REQUIRED_MARK,
			'class'   => array( 'ebanx_billing_brazil_document', 'ebanx_billing_brazil_cpf', 'ebanx_billing_brazil_selector_option', 'form-row-wide' ),
			'default' => isset( $cpf ) ? $cpf : '',
		);

		$ebanx_billing_brazil_cnpj = array(
			'type'    => 'text',
			'label'   => 'CNPJ' . self::REQUIRED_MARK,
			'class'   => array( 'ebanx_billing_brazil_cnpj', 'ebanx_billing_brazil_cnpj', 'ebanx_billing_brazil_selector_option', 'form-row-wide' ),
			'default' => isset( $cnpj ) ? $cnpj : '',
		);

		$ebanx_billing_chile_document     = array(
			'type'    => 'text',
			'label'   => 'RUT' . self::REQUIRED_MARK,
			'class'   => array( 'ebanx_billing_chile_document', 'form-row-wide' ),
			'default' => isset( $rut ) ? $rut : '',
		);

		$ebanx_billing_colombia_document_type = array(
			'type'    => 'select',
			'label'   => __( 'Select a document type', 'woocommerce-gateway-ebanx' ),
			'default' => 'COL_CDI',
			'class'   => array( 'ebanx_billing_colombia_selector', 'ebanx-select-field' ),
			'options' => array(
				'COL_CDI' => __( 'Cédula de Ciudadania', 'woocommerce-gateway-ebanx' ),
				'COL_NIT' => __( 'NIT', 'woocommerce-gateway-ebanx' ),
				'COL_CEX'  => __( 'Cédula de Extranjeria', 'woocommerce-gateway-ebanx' ),
			),
		);

		$ebanx_billing_colombia_document  = array(
			'type'    => 'text',
			'label'   => 'Document' . self::REQUIRED_MARK,
			'class'   => array( 'ebanx_billing_colombia_document', 'form-row-wide' ),
			'default' => isset( $dni ) ? $dni : '',
		);
		$ebanx_billing_peru_document      = array(
			'type'    => 'text',
			'label'   => 'DNI' . self::REQUIRED_MARK,
			'class'   => array( 'ebanx_billing_peru_document', 'form-row-wide' ),
			'default' => isset( $dni_pe ) ? $dni_pe : '',
		);
		$ebanx_billing_argentina_document = array(
			'type'    => 'text',
			'label'   => __( 'Document', 'woocommerce-gateway-ebanx' ) . self::REQUIRED_MARK,
			'class'   => array( 'ebanx_billing_argentina_document', 'form-row-wide' ),
			'default' => isset( $cdi ) ? $cdi : '',
		);

		if ( ! $disable_own_fields ) {
			// CPF and CNPJ are enabled.
			if ( in_array( 'cpf', $fields_options ) && in_array( 'cnpj', $fields_options ) ) {
				$fields['billing']['ebanx_billing_brazil_person_type'] = $ebanx_billing_brazil_person_type;
			}

			// CPF is enabled.
			if ( in_array( 'cpf', $fields_options ) ) {
				$fields['billing']['ebanx_billing_brazil_document'] = $ebanx_billing_brazil_document;
			}

			// CNPJ is enabled.
			if ( in_array( 'cnpj', $fields_options ) ) {
				$fields['billing']['ebanx_billing_brazil_cnpj'] = $ebanx_billing_brazil_cnpj;
			}

			// For Chile.
			$fields['billing']['ebanx_billing_chile_document'] = $ebanx_billing_chile_document;

			// For Colombia.
			$fields['billing']['ebanx_billing_colombia_document_type'] = $ebanx_billing_colombia_document_type;
			$fields['billing']['ebanx_billing_colombia_document'] = $ebanx_billing_colombia_document;

			// For Argentina.
			$fields['billing']['ebanx_billing_argentina_document_type'] = $ebanx_billing_argentina_document_type;
			$fields['billing']['ebanx_billing_argentina_document']      = $ebanx_billing_argentina_document;

			// For Peru.
			$fields['billing']['ebanx_billing_peru_document'] = $ebanx_billing_peru_document;

		}

		return $fields;
	}

	/**
	 * Fetches the billing field names for compatibility with checkout managers
	 *
	 * @return array
	 */
	public function get_billing_field_names() {
		return array(
			// Brazil General.
			'ebanx_billing_brazil_person_type'      => $this->get_checkout_manager_settings_or_default( 'checkout_manager_brazil_person_type', 'ebanx_billing_brazil_person_type' ),

			// Brazil CPF.
			'ebanx_billing_brazil_document'         => $this->get_checkout_manager_settings_or_default( 'checkout_manager_cpf_brazil', 'ebanx_billing_brazil_document' ),

			// Brazil CNPJ.
			'ebanx_billing_brazil_cnpj'             => $this->get_checkout_manager_settings_or_default( 'checkout_manager_cnpj_brazil', 'ebanx_billing_brazil_cnpj' ),

			// Chile Fields.
			'ebanx_billing_chile_document'          => $this->get_checkout_manager_settings_or_default( 'checkout_manager_chile_document', 'ebanx_billing_chile_document' ),

			// Colombia Fields.
			'ebanx_billing_colombia_document_type'  => $this->get_checkout_manager_settings_or_default( 'checkout_manager_colombia_document_type', 'ebanx_billing_colombia_document_type' ),
			'ebanx_billing_colombia_document'       => $this->get_checkout_manager_settings_or_default( 'checkout_manager_colombia_document', 'ebanx_billing_colombia_document' ),

			// Argentina Fields.
			'ebanx_billing_argentina_document_type' => $this->get_checkout_manager_settings_or_default( 'checkout_manager_argentina_document_type', 'ebanx_billing_argentina_document_type' ),
			'ebanx_billing_argentina_document'      => $this->get_checkout_manager_settings_or_default( 'checkout_manager_argentina_document', 'ebanx_billing_argentina_document' ),

			// Peru Fields.
			'ebanx_billing_peru_document'           => $this->get_checkout_manager_settings_or_default( 'checkout_manager_peru_document', 'ebanx_billing_peru_document' ),
		);
	}

	/**
	 * Fetches a single checkout manager setting from the gateway settings if found, otherwise it returns an optional default value
	 *
	 * @param  string $name    The setting name to fetch.
	 * @param  mixed  $default The default value in case setting is not present.
	 * @return mixed
	 */
	private function get_checkout_manager_settings_or_default( $name, $default = null ) {
		if ( ! isset( $this->configs->settings['checkout_manager_enabled'] ) || 'yes' !== $this->configs->settings['checkout_manager_enabled'] ) {
			return $default;
		}

		return $this->get_setting_or_default( $name, $default );
	}

	/**
	 * Fetches a single setting from the gateway settings if found, otherwise it returns an optional default value
	 *
	 * @param  string $name    The setting name to fetch.
	 * @param  mixed  $default The default value in case setting is not present.
	 * @return mixed
	 */
	public function get_setting_or_default( $name, $default = null ) {
		return $this->configs->get_setting_or_default( $name, $default );
	}

	/**
	 * The icon on the right of the gateway name on checkout page
	 *
	 * @return string The URI of the icon
	 */
	public function show_icon() {
		return plugins_url( '/assets/images/' . $this->id . '.png', plugin_basename( dirname( __FILE__ ) ) );
	}

	/**
	 * Output the admin settings in the correct format.
	 *
	 * @return void
	 */
	public function admin_options() {
		include WC_EBANX_TEMPLATES_DIR . 'views/html-admin-page.php';
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  array $data
	 * @return void
	 */
	public static function thankyou_page( $data ) {
		$file_name = "{$data['method']}/payment-{$data['order_status']}.php";

		if ( file_exists( WC_EBANX::get_templates_path() . $file_name ) ) {
			wc_get_template(
				$file_name,
				$data['data'],
				'woocommerce/ebanx/',
				WC_EBANX::get_templates_path()
			);
		}
	}

	/**
	 * Clean the cart and dispatch the data to request
	 *
	 * @param  array $data  The checkout's data.
	 * @return array
	 */
	protected function dispatch( $data ) {
		WC()->cart->empty_cart();

		return $data;
	}

	/**
	 * Save order's meta fields for future use
	 *
	 * @param  WC_Order $order The order created.
	 * @param  Object   $request The request from EBANX success response.
	 * @return void
	 */
	protected function save_order_meta_fields( $order, $request ) {
		// To save only on DB to internal use.
		update_post_meta( $order->id, '_ebanx_payment_hash', $request->payment->hash );
		update_post_meta( $order->id, '_ebanx_payment_open_date', $request->payment->open_date );

		if ( WC_EBANX_Request::has( 'billing_email' ) ) {
			update_post_meta( $order->id, '_ebanx_payment_customer_email', sanitize_email( WC_EBANX_Request::read( 'billing_email' ) ) );
		}

		if ( WC_EBANX_Request::has( 'billing_phone' ) ) {
			update_post_meta( $order->id, '_ebanx_payment_customer_phone', sanitize_text_field( WC_EBANX_Request::read( 'billing_phone' ) ) );
		}

		if ( WC_EBANX_Request::has( 'billing_address_1' ) ) {
			update_post_meta( $order->id, '_ebanx_payment_customer_address', sanitize_text_field( WC_EBANX_Request::read( 'billing_address_1' ) ) );
		}
	}

	/**
	 * Generates the checkout message
	 *
	 * @param int    $amount The total price of the order.
	 * @param  string $currency Possible currencies: BRL, USD, EUR, PEN, CLP, COP, MXN.
	 * @param string $country The country code.
	 * @return string
	 */
	public function get_checkout_message( $amount, $currency, $country ) {
		$price    = wc_price( $amount, array( 'currency' => $currency ) );
		$language = $this->get_language_by_country( $country );

		$texts = array(
			'pt-br' => array(
				'INTRO'                               => 'Total a pagar ',
				WC_EBANX_Constants::CURRENCY_CODE_BRL => WC_EBANX_Helper::should_apply_taxes() ? 'com IOF (0.38%)' : 'em Reais',
			),
			'es'    => array(
				'INTRO'                               => 'Total a pagar en ',
				WC_EBANX_Constants::CURRENCY_CODE_MXN => 'Peso mexicano',
				WC_EBANX_Constants::CURRENCY_CODE_CLP => 'Peso chileno',
				WC_EBANX_Constants::CURRENCY_CODE_PEN => 'Sol peruano',
				WC_EBANX_Constants::CURRENCY_CODE_COP => 'Peso colombiano',
				WC_EBANX_Constants::CURRENCY_CODE_ARS => 'Peso argentino',
				WC_EBANX_Constants::CURRENCY_CODE_BRL => 'Real brasileño',
			),
		);

		$message  = $texts[ $language ]['INTRO'];
		$message .= ! empty( $texts[ $language ][ $currency ] ) ? $texts[ $language ][ $currency ] : $currency;
		$message .= ': <strong class="ebanx-amount-total">' . $price . '</strong>';

		return $message;
	}

	/**
	 *
	 * @param string $country
	 *
	 * @return string
	 */
	protected function get_language_by_country( $country ) {
		$languages = array(
			'ar' => 'es',
			'mx' => 'es',
			'cl' => 'es',
			'pe' => 'es',
			'co' => 'es',
			'ec' => 'es',
			'br' => 'pt-br',
		);
		if ( ! array_key_exists( $country, $languages ) ) {
			return 'pt-br';
		}
		return $languages[ $country ];
	}

	/**
	 *
	 * @param string $country
	 *
	 * @return string
	 */
	protected function get_sandbox_form_message( $country ) {
		$messages = array(
			'pt-br' => 'Ainda estamos testando esse tipo de pagamento. Por isso, a sua compra não será cobrada nem enviada.',
			'es'    => 'Todavia estamos probando este método de pago. Por eso su compra no sera cobrada ni enviada.',
		);

		return $messages[ $this->get_language_by_country( $country ) ];
	}

	/**
	 * @param array $instalment_terms
	 * @param int   $instalment
	 *
	 * @return mixed
	 */
	public static function get_instalment_term( $instalment_terms, $instalment ) {
		foreach ( $instalment_terms as $instalment_term ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			if ( $instalment_term->instalmentNumber == $instalment ) {
				return $instalment_term;
			}
		}
	}
}
