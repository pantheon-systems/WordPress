<?php

class WPML_ST_MO_Translation {
	/** @var string */
	private $original;

	/** @var string */
	private $translation;

	/** @var string */
	private $context;

	/**
	 * @param string $original
	 * @param string $translation
	 * @param string $context
	 */
	public function __construct( $original, $translation, $context = '' ) {
		$this->original    = $original;
		$this->translation = $translation;
		$this->context     = $context;
	}

	/**
	 * @return string
	 */
	public function get_original() {
		return $this->original;
	}

	/**
	 * @return string
	 */
	public function get_translation() {
		return $this->translation;
	}

	/**
	 * @return string
	 */
	public function get_context() {
		return $this->context;
	}
}
