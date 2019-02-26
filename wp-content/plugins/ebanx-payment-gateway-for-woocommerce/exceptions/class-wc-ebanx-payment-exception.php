<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Payment_Exception
 */
class WC_EBANX_Payment_Exception extends Exception {
	/**
	 *
	 * @var string
	 */
	protected $code;

	/**
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * WC_EBANX_Payment_Exception constructor.
	 *
	 * @param string         $message
	 * @param string         $code
	 * @param Throwable|null $previous
	 */
	public function __construct( $message, $code, Throwable $previous = null ) {
		parent::__construct( $code, 0, $previous );
		$this->code    = $code;
		$this->message = $message;
	}
}
