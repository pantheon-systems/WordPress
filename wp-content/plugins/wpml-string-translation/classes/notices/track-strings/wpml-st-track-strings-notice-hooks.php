<?php

class WPML_ST_Track_Strings_Notice_Hooks {

	private $notice;

	private $localization_options;

	public function __construct( WPML_ST_Track_Strings_Notice $notice,
		WPML_Save_Themes_Plugins_Localization_Options $localization_options ) {

		$this->notice = $notice;
		$this->localization_options = $localization_options;
	}

	public function add_hooks() {
		add_action( 'wpml_st_strings_tracking_option_saved', array( $this->notice, 'add' ) );
		add_action( 'wpml_st_strings_tracking_option_saved', array( $this, 'save_all_english_setting' ) );

		if ( array_key_exists( 'remove_notice', $_POST ) && $_POST['remove_notice'] ) {
			add_action( 'theme_plugin_localization_settings_saved', array( $this->notice, 'remove' ) );
		}
	}

	public function save_all_english_setting() {
		$this->localization_options->save_settings( array( 'all_strings_are_english' => 0 ) );
	}
}