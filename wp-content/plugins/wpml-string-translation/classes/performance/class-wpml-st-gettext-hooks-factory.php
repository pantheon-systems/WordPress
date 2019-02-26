<?php

class WPML_ST_Gettext_Hooks_Factory {
	const ALL_STRINGS_ARE_IN_ENGLISH_OPTION = 'wpml-st-all-strings-are-in-english';

	/** @var SitePress */
	private $sitepress;

	/** @var WPML_String_Translation  */
	private $string_translation;

	/**
	 * @var bool
	 */
	private $translate_with_st;

	/**
	 * @param SitePress $sitepress
	 * @param WPML_String_Translation $string_translation
	 */
	public function __construct( SitePress $sitepress, WPML_String_Translation $string_translation, $translate_with_st ) {
		$this->sitepress          = $sitepress;
		$this->string_translation = $string_translation;
		$this->translate_with_st  = $translate_with_st;
	}

	/**
	 * @return WPML_ST_Gettext_Hooks
	 */
	public function create() {
		return new WPML_ST_Gettext_Hooks(
			$this->string_translation,
			$this->get_current_language(),
			'en' === $this->sitepress->get_default_language() && get_option( self::ALL_STRINGS_ARE_IN_ENGLISH_OPTION ),
			$this->translate_with_st
		);
	}

	public function get_current_language() {
		if ( is_admin() && ! $this->is_ajax_request_coming_from_frontend() ) {
			$current_lang = $this->sitepress->get_admin_language();
		} else {
			$current_lang = $this->sitepress->get_current_language();
		}

		if ( ! $current_lang ) {
			$current_lang = $this->sitepress->get_default_language();
			if ( ! $current_lang ) {
				$current_lang = 'en';
			}
		}

		return $current_lang;
	}

	protected function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	private function is_ajax_request_coming_from_frontend() {
		if ( ! $this->is_ajax() ) {
			return false;
		}

		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			return false;
		}

		return false === strpos( $_SERVER['HTTP_REFERER'], admin_url() );
	}
}
