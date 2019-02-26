<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WC_EBANX_VENDOR_DIR . 'autoload.php';

use Ebanx\Benjamin\Models\Country;

/**
 * Class WC_EBANX_Constants
 */
abstract class WC_EBANX_Constants {

	/**
	 * Countries that EBANX processes
	 */
	const COUNTRY_PERU      = 'pe';
	const COUNTRY_CHILE     = 'cl';
	const COUNTRY_BRAZIL    = 'br';
	const COUNTRY_MEXICO    = 'mx';
	const COUNTRY_COLOMBIA  = 'co';
	const COUNTRY_ARGENTINA = 'ar';
	const COUNTRY_ECUADOR   = 'ec';

	/**
	 * The fixed URL to our settings page, always use this one if you want to redirect to it
	 */
	const SETTINGS_URL = 'admin.php?page=wc-settings&tab=checkout&section=ebanx-global';

	/**
	 * Currencies that EBANX processes
	 */
	const CURRENCY_CODE_BRL = 'BRL'; // Brazil.
	const CURRENCY_CODE_USD = 'USD'; // USA & ECUADOR.
	const CURRENCY_CODE_EUR = 'EUR'; // European Union.
	const CURRENCY_CODE_PEN = 'PEN'; // Peru.
	const CURRENCY_CODE_MXN = 'MXN'; // Mexico.
	const CURRENCY_CODE_COP = 'COP'; // Colombia.
	const CURRENCY_CODE_CLP = 'CLP'; // Chile.
	const CURRENCY_CODE_ARS = 'ARS'; // Argentina.

	/**
	 * Convert Country abbreviation to Country name
	 */
	const COUNTRY_NAME_FROM_ABBREVIATION = [
		WC_EBANX_Constants::COUNTRY_BRAZIL    => Country::BRAZIL,
		WC_EBANX_Constants::COUNTRY_MEXICO    => Country::MEXICO,
		WC_EBANX_Constants::COUNTRY_COLOMBIA  => Country::COLOMBIA,
		WC_EBANX_Constants::COUNTRY_ARGENTINA => Country::ARGENTINA,
	];

	/**
	 * Only the currencies allowed and processed by EBANX
	 *
	 * @var array
	 */
	public static $allowed_currency_codes = array(
		self::CURRENCY_CODE_BRL,
		self::CURRENCY_CODE_USD,
		self::CURRENCY_CODE_EUR,
		self::CURRENCY_CODE_PEN,
		self::CURRENCY_CODE_MXN,
		self::CURRENCY_CODE_COP,
		self::CURRENCY_CODE_CLP,
		self::CURRENCY_CODE_ARS,
	);

	/**
	 *  Local currencies that EBANX processes
	 *
	 * @var array
	 */
	public static $local_currencies = array(
		self::COUNTRY_BRAZIL    => self::CURRENCY_CODE_BRL,
		self::COUNTRY_CHILE     => self::CURRENCY_CODE_CLP,
		self::COUNTRY_COLOMBIA  => self::CURRENCY_CODE_COP,
		self::COUNTRY_MEXICO    => self::CURRENCY_CODE_MXN,
		self::COUNTRY_PERU      => self::CURRENCY_CODE_PEN,
		self::COUNTRY_ARGENTINA => self::CURRENCY_CODE_ARS,
	);

	/**
	 * Minimal instalment value for acquirers to approve based on currency
	 */
	const ACQUIRER_MIN_INSTALMENT_VALUE_MXN = 100;
	const ACQUIRER_MIN_INSTALMENT_VALUE_BRL = 5;
	const ACQUIRER_MIN_INSTALMENT_VALUE_COP = 0;
	const ACQUIRER_MIN_INSTALMENT_VALUE_ARS = 0;

	/**
	 * Max supported credit-card instalments
	 *
	 * @var array
	 */
	public static $max_instalments = array(
		self::COUNTRY_BRAZIL    => 12,
		self::COUNTRY_MEXICO    => 12,
		self::COUNTRY_COLOMBIA  => 36,
		self::COUNTRY_ARGENTINA => 12,
	);

	/**
	* Taxes applied by country
	*/
	const BRAZIL_TAX = 0.0038;

	/**
	 * The list of all countries that EBANX processes
	 *
	 * @var array
	 */
	public static $all_countries = array(
		self::COUNTRY_BRAZIL,
		self::COUNTRY_COLOMBIA,
		self::COUNTRY_MEXICO,
		self::COUNTRY_PERU,
		self::COUNTRY_CHILE,
		self::COUNTRY_ARGENTINA,
	);

	/**
	 * The countries that credit cards are processed by EBANX
	 *
	 * @var array
	 */
	public static $credit_card_countries = array(
		self::COUNTRY_ARGENTINA => self::COUNTRY_ARGENTINA,
		self::COUNTRY_BRAZIL    => self::COUNTRY_BRAZIL,
		self::COUNTRY_COLOMBIA  => self::COUNTRY_COLOMBIA,
		self::COUNTRY_MEXICO    => self::COUNTRY_MEXICO,
	);

	/**
	 * The countries that credit cards are processed by EBANX
	 *
	 * @var array
	 */
	public static $credit_card_currencies = array(
		self::CURRENCY_CODE_BRL,
		self::CURRENCY_CODE_MXN,
		self::CURRENCY_CODE_USD,
		self::CURRENCY_CODE_COP,
		self::CURRENCY_CODE_EUR,
		self::CURRENCY_CODE_ARS,
	);

	/**
	 * The cash payments processed by EBANX
	 *
	 * @var array
	 */
	public static $cash_payment_gateways_code = array(
		'ebanx-banking-ticket',
		'ebanx-oxxo',
		'ebanx-spei',
		'ebanx-pagoefectivo',
		'ebanx-sencillito',
		'ebanx-safetypay-cash',
		'ebanx-baloto',
		'ebanx-efectivo',
		'ebanx-banktransfer',
	);

	/**
	 * The banks that EBANX process in Brazil
	 *
	 * @var array
	 */
	public static $banks_tef_allowed = array(
		self::COUNTRY_BRAZIL => array( 'bancodobrasil', 'itau', 'bradesco', 'banrisul' ),
	);

	/**
	 * The banks that EBANX process in Colombia
	 *
	 * @var array
	 */
	public static $banks_eft_allowed = array(
		self::COUNTRY_COLOMBIA => array(
			'banco_agrario'                 => 'Banco Agrario',
			'banco_av_villas'               => 'Banco AV Villas',
			'banco_bbva_colombia_s.a.'      => 'Banco BBVA Colombia',
			'banco_caja_social'             => 'Banco Caja Social',
			'banco_colpatria'               => 'Banco Colpatria',
			'banco_cooperativo_coopcentral' => 'Banco Cooperativo Coopcentral',
			'banco_corpbanca_s.a'           => 'Banco CorpBanca Colombia',
			'banco_davivienda'              => 'Banco Davivienda',
			'banco_de_bogota'               => 'Banco de Bogotá',
			'banco_de_occidente'            => 'Banco de Occidente',
			'banco_falabella_'              => 'Banco Falabella',
			'banco_gnb_sudameris'           => 'Banco GNB Sudameris',
			'banco_pichincha_s.a.'          => 'Banco Pichincha',
			'banco_popular'                 => 'Banco Popular',
			'banco_procredit'               => 'Banco ProCredit',
			'bancolombia'                   => 'Bancolombia',
			'bancoomeva_s.a.'               => 'Bancoomeva',
			'citibank_'                     => 'Citibank',
			'helm_bank_s.a.'                => 'Helm Bank',
		),
	);

	/**
	 *
	 * @var array
	 */
	public static $vouchers_efectivo_allowed = array(
		'rapipago',
		'pagofacil',
		'cupon',
	);

	/**
	 * Payment type API codes for each plugin payment gateway
	 *
	 * @var array
	 */
	public static $gateway_to_payment_type_code = array(
		'ebanx-bank_transfer'  => '_bank_transfer',
		'ebanx-banking-ticket' => '_boleto',
		'ebanx-credit-card-br' => '_creditcard',
		'ebanx-credit-card-mx' => '_creditcard',
		'ebanx-debit-card'     => 'debitcard',
		'ebanx-oxxo'           => '_oxxo',
		'ebanx-sencillito'     => '_sencillito',
		'ebanx-servipag'       => 'servipag',
		'ebanx-tef'            => '_tef',
		'ebanx-pagoefectivo'   => '_pagoefectivo',
		'ebanx-safetypay'      => '_safetypay',
		'ebanx-eft'            => 'eft',
		'ebanx-baloto'         => '_baloto',
		// 'ebanx-account' => '_ebanxaccount'
	);

	/**
	 * The Brazil taxes available options that EBANX process
	 *
	 * @var array
	 */
	public static $brazil_taxes_allowed = array( 'cpf', 'cnpj' );

	/**
	 * The gateways that plugin uses as identification
	 *
	 * @var array
	 */
	public static $ebanx_gateways_by_country = array(
		self::COUNTRY_BRAZIL    => array(
			'ebanx-banking-ticket',
			'ebanx-credit-card-br',
			'ebanx-tef',
			'ebanx-account',
			'ebanx-banktransfer',
		),
		self::COUNTRY_CHILE     => array(
			'ebanx-webpay',
			'ebanx-multicaja',
			'ebanx-sencillito',
			'ebanx-servipag',
		),
		self::COUNTRY_COLOMBIA  => array(
			'ebanx-credit-card-co',
			'ebanx-baloto',
			'ebanx-eft',
		),
		self::COUNTRY_PERU      => array(
			'ebanx-pagoefectivo',
			'ebanx-safetypay',
		),
		self::COUNTRY_MEXICO    => array(
			'ebanx-credit-card-mx',
			'ebanx-debit-card',
			'ebanx-oxxo',
			'ebanx-spei',
		),
		self::COUNTRY_ARGENTINA => array(
			'ebanx-efectivo',
		),
	);

	/**
	 * Types allowed by SafetyPay
	 *
	 * @var array
	 */
	public static $safetypay_allowed_types = array(
		'cash',
		'online',
	);
}
