<?php

class WPML_ST_Models_String_Translation {
	/** @var int */
	private $string_id;

	/** @var string */
	private $language;

	/** @var int */
	private $status;

	/** @var string */
	private $value;

	/** @var string */
	private $mo_string;

	/**
	 * @param int $string_id
	 * @param string $language
	 * @param int $status
	 * @param string $value
	 */
	public function __construct( $string_id, $language, $status, $value, $mo_string ) {
		$this->string_id = (int) $string_id;
		$this->language  = (string) $language;
		$this->status    = (int) $status;
		$this->value     = (string) $value;
		$this->mo_string = (string) $mo_string;
	}

	/**
	 * @return int
	 */
	public function get_string_id() {
		return $this->string_id;
	}

	/**
	 * @return string
	 */
	public function get_language() {
		return $this->language;
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
	public function get_value() {
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function get_mo_string() {
		return $this->mo_string;
	}
}