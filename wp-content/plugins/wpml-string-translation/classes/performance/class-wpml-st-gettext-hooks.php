<?php

class WPML_ST_Gettext_Hooks {
	/** @var WPML_String_Translation  */
	private $string_translation;

	/** @var string */
	private $current_lang;

	/** @var string */
	private $initial_language;

	/** @var bool */
	private $all_strings_are_in_english;

	/**
	 * @var bool
	 */
	private $translate_with_st;

	/** @var array  */
	private $filters = array();

	private $hooks = array(
		array( 'gettext', 'icl_sw_filters_gettext', 9, 3 ),
		array( 'gettext_with_context', 'icl_sw_filters_gettext_with_context', 1, 4 ),
		array( 'ngettext', 'icl_sw_filters_ngettext', 9, 5 ),
		array( 'ngettext_with_context', 'icl_sw_filters_nxgettext', 9, 6 ),
	);

	/**
	 * WPML_ST_Gettext_Hooks constructor.
	 *
	 * @param WPML_String_Translation $string_translation
	 * @param string $current_lang
	 * @param bool $all_strings_are_in_english
	 * @param bool $translate_with_st
	 */
	public function __construct(
		WPML_String_Translation $string_translation,
		$current_lang,
		$all_strings_are_in_english,
		$translate_with_st
	) {
		$this->string_translation         = $string_translation;
		$this->initial_language           = $this->current_lang = $current_lang;
		$this->all_strings_are_in_english = $all_strings_are_in_english;
		$this->translate_with_st          = $translate_with_st;
	}

	public function init_hooks() {
		if ( ! $this->translate_with_st ) {
			return;
		}

		if ( $this->all_strings_are_in_english ) {
			add_action( 'wpml_language_has_switched', array( $this, 'switch_language_hook' ), 10, 1 );
		}

		if ( $this->should_gettext_filters_be_turned_on() ) {
			add_action( 'plugins_loaded', array( $this, 'init_gettext_hooks' ), 2 );
		}
	}

	public function init_gettext_hooks() {
		foreach ( $this->hooks as $hook ) {
			call_user_func_array( 'add_filter', $hook );
		}
	}

	/**
	 * @param string $lang
	 */
	public function switch_language_hook( $lang ) {
		if ( $this->string_translation->should_use_admin_language() ) {
			$this->current_lang = $this->string_translation->get_admin_language();
		} elseif ( $lang ) {
			$this->current_lang = $lang;
		} else {
			$this->current_lang = $this->initial_language;
		}

		if ( $this->should_gettext_filters_be_turned_on() ) {
			$this->init_gettext_hooks();
		} else {
			$this->remove_hooks();
		}
	}

	private function remove_hooks() {
		foreach ( $this->hooks as $hook ) {
			array_pop( $hook );
			call_user_func_array( 'remove_filter', $hook );
		}
	}

	/**
	 * @param string $lang
	 * @return bool
	 */
	public function should_gettext_filters_be_turned_on( $lang = null ) {
		global $sitepress_settings;

		$lang = $lang ? $lang : $this->current_lang;

		return ( 'en' !== $lang || ! $this->all_strings_are_in_english ) && isset( $sitepress_settings['setup_complete'] ) && $sitepress_settings['setup_complete'];
	}

	/**
	 * @param string $lang
	 * @param string $name
	 *
	 * @return WPML_Displayed_String_Filter|null
	 */
	public function get_filter( $lang = null, $name ) {
		if ( ! $lang ) {
			$lang = $this->current_lang;
			if ( ! $this->all_strings_are_in_english ) {
				$lang = $this->string_translation->get_current_string_language( $name );
			}
		}

		if ( ! $lang ) {
			return null;
		}

		if ( ! ( array_key_exists( $lang, $this->filters ) && $this->filters[ $lang ] ) ) {
			$this->filters[ $lang ] = $this->string_translation->get_string_filter( $lang );
		}

		return $this->filters[ $lang ];
	}

	public function clear_filters() {
		$this->filters = array();
	}
}