<?php

class WPML_ST_Theme_Plugin_Localization_Options_UI {
	/** @var array */
	private $st_settings;

	/** @var string */
	private $default_lang;

	/**
	 * WPML_ST_Theme_Plugin_Localization_Options_UI constructor.
	 *
	 * @param array $st_settings
	 * @param string $default_lang
	 */
	public function __construct( $st_settings, $default_lang ) {
		$this->st_settings = $st_settings;
		$this->default_lang = $default_lang;
	}

	public function add_hooks() {
		add_filter( 'wpml_localization_options_ui_model', array( $this, 'add_st_options' ) );
	}

	/**
	 * @param array $model
	 *
	 * @return array
	 */
	public function add_st_options( $model ) {
		$is_all_strings_option_active = 'en' === $this->default_lang && get_option( WPML_ST_Gettext_Hooks_Factory::ALL_STRINGS_ARE_IN_ENGLISH_OPTION );

		$model['bottom_tittle']  = __( 'Other options:', 'wpml-string-translation' );
		$model['bottom_options'] = array(
			array(
				'name'    => 'use_theme_plugin_domain',
				'value'   => 1,
				'label'   => __( 'Use theme or plugin text domains when gettext calls do not use a string literal', 'wpml-string-translation' ),
				'tooltip' => __( "Some themes and plugins don't properly set the textdomain (second argument) in GetText calls. When you select this option, WPML will assume that the strings found in GetText calls in the PHP files of the theme and plugin should have the textdomain with the theme/plugin's name.", 'wpml-string-translation' ),
				'checked' => checked( true, ! empty( $this->st_settings['use_header_text_domains_when_missing'] ), false ),
			),
			array(
				'name'    => WPML_ST_Gettext_Hooks_Factory::ALL_STRINGS_ARE_IN_ENGLISH_OPTION,
				'value'   => 1,
				'label'   => self::get_all_strings_option_text(),
				'checked' => checked( true, $is_all_strings_option_active, false ),
				'disabled' => 'en' !== $this->default_lang,
			),
		);

		return $model;
	}

	/**
	 * @return string
	 */
	public static function get_all_strings_option_text() {
		return __( 'Assume that the original language of all strings is English', 'wpml-string-translation' );
	}
}
