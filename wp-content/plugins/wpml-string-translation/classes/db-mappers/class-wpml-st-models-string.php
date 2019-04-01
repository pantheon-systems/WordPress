<?php

class WPML_ST_Models_String {
	/** @var string */
	private $language;

	/** @var string */
	private $domain;

	/** @var string */
	private $context;

	/** @var string */
	private $value;

	/** @var int */
	private $status;

	/** @var string */
	private $name;

	/** @var string */
	private $domain_name_context_md5;

	/**
	 * @param string $language
	 * @param string $domain
	 * @param string $context
	 * @param string $value
	 * @param int $status
	 * @param string|null $name
	 */
	public function __construct( $language, $domain, $context, $value, $status, $name = null ) {
		$this->language = (string) $language;
		$this->domain   = (string) $domain;
		$this->context  = (string) $context;
		$this->value    = (string) $value;
		$this->status   = (int) $status;

		if ( ! $name ) {
			$name = md5( $value );
		}
		$this->name     = (string) $name;

		$this->domain_name_context_md5 = md5( $domain . $name . $context );
	}

	/**
	 * @return string
	 */
	public function get_language() {
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function get_domain() {
		return $this->domain;
	}

	/**
	 * @return string
	 */
	public function get_context() {
		return $this->context;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @return int
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_domain_name_context_md5() {
		return $this->domain_name_context_md5;
	}
}