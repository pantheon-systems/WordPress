<?php

class WPML_ST_Slug {

	/** @var int $original_id */
	private $original_id;

	/** @var string $original_lang */
	private $original_lang;

	/** @var string $original_value */
	private $original_value;

	/** @var array $langs */
	private $langs = array();

	/** @param stdClass $data */
	public function set_lang_data( stdClass $data ) {
		if ( isset( $data->language ) ) {
			$this->langs[ $data->language ] = $data;
		}

		// Case of original string language
		if ( isset( $data->id ) ) {
			$this->original_id    = $data->id;
			$this->original_lang  = $data->language;
			$this->original_value = $data->value;
		}
	}

	/** @return string */
	public function get_original_lang() {
		return $this->original_lang;
	}

	/** @return string */
	public function get_original_value() {
		return $this->original_value;
	}

	/** @return int */
	public function get_original_id() {
		return (int) $this->original_id;
	}

	/**
	 * @param string $lang
	 *
	 * @return string
	 */
	public function get_value( $lang ) {
		if ( isset( $this->langs[ $lang ]->value ) ) {
			return $this->langs[ $lang ]->value;
		}

		return $this->get_original_value();
	}

	/**
	 * @param string $lang
	 *
	 * @return int
	 */
	public function get_status( $lang ) {
		if ( isset( $this->langs[ $lang ]->status ) ) {
			return (int) $this->langs[ $lang ]->status;
		}

		return ICL_TM_NOT_TRANSLATED;
	}

	/**
	 * @param string $lang
	 *
	 * @return bool
	 */
	public function is_translation_complete( $lang ) {
		return ICL_TM_COMPLETE === $this->get_status( $lang );
	}

	/** @return string|null */
	public function get_context() {
		if ( isset( $this->langs[ $this->original_lang ]->context ) ) {
			return $this->langs[ $this->original_lang ]->context;
		}

		return null;
	}

	/** @return string|null */
	public function get_name() {
		if ( isset( $this->langs[ $this->original_lang ]->name ) ) {
			return $this->langs[ $this->original_lang ]->name;
		}

		return null;
	}

	/** @return array */
	public function get_language_codes() {
		return array_keys( $this->langs );
	}

	/**
	 * This method is used as a filter which returns the initial `$slug_value`
	 * if no better value was found.
	 *
	 * @param string $slug_value
	 * @param string $lang
	 *
	 * @return string
	 */
	public function filter_value( $slug_value, $lang ) {
		if ( $this->original_lang === $lang ) {
			return $this->original_value;
		} elseif ( in_array( $lang, $this->get_language_codes(), true ) && $this->is_translation_complete( $lang ) ) {
			return $this->get_value( $lang );
		}

		return $slug_value;
	}
}
