<?php

class WPML_Home_Url_Filter_Context {

	const REST_REQUEST = 'rest-request';
	const REWRITE_RULES = 'rewrite-rules';
	const PAGINATION = 'pagination';

	/**
	 * @var int
	 */
	private $language_negotiation_type;

	/**
	 * @var string
	 */
	private $orig_scheme;

	/**
	 * @var WPML_Debug_BackTrace
	 */
	private $debug_backtrace;

	public function __construct( $language_negotiation_type, $orig_scheme, WPML_Debug_BackTrace $debug_backtrace ) {
		$this->language_negotiation_type = $language_negotiation_type;
		$this->orig_scheme               = $orig_scheme;
		$this->debug_backtrace           = $debug_backtrace;
	}

	/**
	 * @return bool
	 */
	public function should_not_filter() {
		return $this->is_rest_request()
			|| $this->rewriting_rules()
			|| $this->pagination_link();
	}

	/**
	 * @return bool
	 */
	private function is_rest_request() {
		return in_array( $this->orig_scheme, array( 'json', 'rest' ), true );
	}

	/**
	 * @return bool
	 */
	private function rewriting_rules() {
		return $this->debug_backtrace->is_class_function_in_call_stack( 'WP_Rewrite', 'rewrite_rules' );
	}

	/**
	 * @return bool
	 */
	private function pagination_link() {
		return WPML_LANGUAGE_NEGOTIATION_TYPE_PARAMETER === $this->language_negotiation_type
		       && $this->debug_backtrace->is_function_in_call_stack( 'get_pagenum_link' );
	}
}