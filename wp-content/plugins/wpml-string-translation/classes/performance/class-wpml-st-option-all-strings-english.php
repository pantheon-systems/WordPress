<?php

class WPML_ST_Options_All_Strings_English implements IWPML_Action {
	/** @var wpdb */
	private $wpdb;

	/** @var string */
	private $default_lang;

	/**
	 * @param wpdb $wpdb
	 * @param string $default_lang
	 */
	public function __construct( wpdb $wpdb, $default_lang ) {
		$this->wpdb = $wpdb;
		$this->default_lang = $default_lang;
	}

	public function add_hooks() {
		$hook = 'default_option_' . WPML_ST_Gettext_Hooks_Factory::ALL_STRINGS_ARE_IN_ENGLISH_OPTION;
		add_filter( $hook, array( $this, 'check_for_non_english_strings' ), 10, 0 );
	}

	/**
	 * @return bool
	 */
	public function check_for_non_english_strings() {
		$value = 1;

		if ( 'en' === $this->default_lang ) {
			$sql   = "SELECT id FROM {$this->wpdb->prefix}icl_strings WHERE language != 'en' LIMIT 1";
			$value = (bool) $this->wpdb->get_var( $sql );
		}

		$this->update_option_with_default_value( $value ? 0 : 1 );

		return ! $value;
	}

	private function update_option_with_default_value( $value ) {
		$hook = 'default_option_' . WPML_ST_Gettext_Hooks_Factory::ALL_STRINGS_ARE_IN_ENGLISH_OPTION;
		remove_filter( $hook, array( $this, 'check_for_non_english_strings' ), 10, 0 );
		update_option( WPML_ST_Gettext_Hooks_Factory::ALL_STRINGS_ARE_IN_ENGLISH_OPTION, $value );
	}
}
