<?php

class WPML_ST_Theme_Plugin_Scan_Files_Ajax implements IWPML_Action {

	/** @var IWPML_ST_String_Scanner */
	private $string_scanner;

	/**
	 * WPML_ST_Theme_Scan_Files_Ajax constructor.
	 *
	 * @param IWPML_ST_String_Scanner $string_scanner
	 */
	public function __construct( IWPML_ST_String_Scanner $string_scanner ) {
		$this->string_scanner = $string_scanner;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_st_scan_chunk', array( $this, 'scan' ) );
	}

	public function scan() {
		wpml_get_admin_notices()->remove_notice(
			WPML_ST_Themes_And_Plugins_Settings::NOTICES_GROUP,
			WPML_ST_Themes_And_Plugins_Updates::WPML_ST_SCAN_NOTICE_ID
		);

		wpml_get_admin_notices()->remove_notice(
			WPML_ST_Themes_And_Plugins_Settings::NOTICES_GROUP,
			WPML_ST_Themes_And_Plugins_Updates::WPML_ST_SCAN_ACTIVE_ITEMS_NOTICE_ID
		);

		$this->clear_items_needs_scan_buffer();
		$this->string_scanner->scan();
	}

	public function clear_items_needs_scan_buffer() {
		delete_option( WPML_ST_Themes_And_Plugins_Updates::WPML_ST_ITEMS_TO_SCAN );
	}
}