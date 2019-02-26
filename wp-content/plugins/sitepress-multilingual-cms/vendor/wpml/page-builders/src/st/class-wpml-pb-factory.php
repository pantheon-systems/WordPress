<?php

class WPML_PB_Factory {

	private $wpdb;
	private $sitepress;
	private $string_translations = array();

	public function __construct( $wpdb, $sitepress ) {
		$this->wpdb      = $wpdb;
		$this->sitepress = $sitepress;
	}

	public function get_wpml_package( $package_id ) {
		return new WPML_Package( $package_id );
	}

	public function get_string_translations( IWPML_PB_Strategy $strategy ) {
		$kind = $strategy->get_package_kind();
		if ( ! array_key_exists( $kind, $this->string_translations ) ) {
			$this->string_translations[ $kind ] = new WPML_PB_String_Translation( $this->wpdb, $this, $strategy );
		}

		return $this->string_translations[ $kind ];
	}

	public function get_shortcode_parser( WPML_PB_Shortcode_Strategy $strategy ) {
		return new WPML_PB_Shortcodes( $strategy );
	}

	/**
	 * @param WPML_PB_Shortcode_Strategy $strategy
	 * @param bool $migration_mode
	 *
	 * @return WPML_PB_Register_Shortcodes
	 */
	public function get_register_shortcodes( WPML_PB_Shortcode_Strategy $strategy, $migration_mode = false ) {
		$absolute_links         = new AbsoluteLinks();
		$permalinks_converter   = new WPML_Absolute_To_Permalinks( $this->sitepress );
		$translate_link_targets = new WPML_Translate_Link_Targets( $absolute_links, $permalinks_converter );

		$string_factory = new WPML_ST_String_Factory( $this->wpdb );

		$string_registration = new WPML_PB_String_Registration(
			$strategy,
			$string_factory,
			new WPML_ST_Package_Factory(),
			$translate_link_targets,
			$this->sitepress->get_active_languages(),
			$migration_mode
		);

		return new WPML_PB_Register_Shortcodes(
			$string_registration,
			$strategy,
			new WPML_PB_Shortcode_Encoding(),
			$migration_mode ? null : new WPML_PB_Reuse_Translations( $strategy, $string_factory )
		);
	}

	public function get_update_post( $package_data, IWPML_PB_Strategy $strategy ) {
		return new WPML_PB_Update_Post( $this->wpdb, $this->sitepress, $package_data, $strategy );
	}

	public function get_shortcode_content_updater( IWPML_PB_Strategy $strategy ) {
		return new WPML_PB_Update_Shortcodes_In_Content( $strategy, new WPML_PB_Shortcode_Encoding() );
	}

	public function get_api_hooks_content_updater( IWPML_PB_Strategy $strategy ) {
		return new WPML_PB_Update_API_Hooks_In_Content( $strategy );
	}

	public function get_package_strings_resave() {
		return new WPML_PB_Package_Strings_Resave( new WPML_ST_String_Factory( $this->wpdb ) );
	}

	public function get_handle_post_body() {
		return new WPML_PB_Handle_Post_Body(
			new WPML_Page_Builders_Page_Built(
				new WPML_Config_Built_With_Page_Builders()
			)
		);
	}

	public function get_last_translation_edit_mode() {
		return new WPML_PB_Last_Translation_Edit_Mode();
	}

	public function get_post_element( $post_id ) {
		$factory = new WPML_Translation_Element_Factory( $this->sitepress );
		return $factory->create_post( $post_id );
	}
}
