<?php

class WPML_Jobs_Data_To_Send {
	/** @var string */
	private $translate_from;

	/** @var array */
	private $batch_options;

	/** @var string */
	private $batch_name;

	/** @var array */
	private $selected_languages;

	/** @var array */
	private $selected_posts;

	/** @var array */
	private $selected_translators;

	/**
	 * WPML_Jobs_Data_To_Send constructor.
	 *
	 * @param string $translate_from
	 * @param array $batch_options
	 * @param string $batch_name
	 * @param array $selected_languages
	 * @param array $selected_posts
	 * @param array $selected_translators
	 */
	private function __construct(
		$translate_from,
		array $batch_options,
		$batch_name,
		array $selected_languages,
		array $selected_posts,
		array $selectec_translators
	) {
		$this->translate_from       = $translate_from;
		$this->batch_options        = $batch_options;
		$this->batch_name           = $batch_name;
		$this->selected_languages   = $selected_languages;
		$this->selected_posts       = $selected_posts;
		$this->selected_translators = $selectec_translators;
	}

	public static function build_from_array( array $data ) {
		$translate_from       = TranslationProxy_Basket::get_source_language();
		$batch_options        = isset( $data['batch_options'] ) ? $data['batch_options'] : array();
		$selected_translators = isset( $data['translators'] ) ? $data['translators'] : array();

		$batch_name = false;
		if ( isset( $data['batch_name'] ) ) {
			$batch_name = $data['batch_name'];
		} elseif ( isset( $batch_options['basket_name'] ) ) {
			$batch_name = $batch_options['basket_name'];
		}

		if ( isset( $data['tr_action'] ) ) {
			$selected_languages = $data['tr_action'];
		} elseif ( isset( $data['translate_to'] ) ) {
			$selected_languages = $data['translate_to'];
		} else {
			return false;
		}

		if ( isset( $data['post'] ) ) {
			$selected_posts = $data['post'];
		} elseif ( isset( $data['iclpost'] ) ) {
			$selected_posts = $data['iclpost'];
		} elseif ( isset( $data['posts_to_translate'] ) ) {
			$selected_posts = $data['posts_to_translate'];
		} else {
			return false;
		}

		return new WPML_Jobs_Data_To_Send(
			$translate_from,
			$batch_options,
			$batch_name,
			$selected_languages,
			$selected_posts,
			$selected_translators
		);
	}

	/**
	 * @return string
	 */
	public function get_translate_from() {
		return $this->translate_from;
	}

	/**
	 * @return array
	 */
	public function get_batch_options() {
		return $this->batch_options;
	}

	/**
	 * @return string
	 */
	public function get_batch_name() {
		return $this->batch_name;
	}

	/**
	 * @return array
	 */
	public function get_selected_languages() {
		return $this->selected_languages;
	}

	/**
	 * @return array
	 */
	public function get_selected_posts() {
		return $this->selected_posts;
	}

	/**
	 * @param string $lang
	 *
	 * @return mixed
	 */
	public function get_translator( $lang ) {
		if ( isset( $this->selected_translators[ $lang ] ) ) {
			return $this->selected_translators[ $lang ];
		}

		return get_current_user_id();
	}
}