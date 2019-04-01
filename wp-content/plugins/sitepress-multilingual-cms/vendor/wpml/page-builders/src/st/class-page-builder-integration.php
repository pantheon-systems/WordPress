<?php

/**
 * Class WPML_Page_Builders_Integration
 */
class WPML_Page_Builders_Integration {

	const STRINGS_TRANSLATED_PRIORITY = 10;

	/** @var WPML_Page_Builders_Register_Strings */
	private $register_strings;

	/** @var WPML_Page_Builders_Update_Translation */
	private $update_translation;

	/** @var IWPML_Page_Builders_Data_Settings */
	private $data_settings;

	/**
	 * WPML_Page_Builders_Integration constructor.
	 *
	 * @param WPML_Page_Builders_Register_Strings $register_strings
	 * @param WPML_Page_Builders_Update_Translation $update_translation
	 * @param IWPML_Page_Builders_Data_Settings $data_settings
	 */
	public function __construct(
		WPML_Page_Builders_Register_Strings $register_strings,
		WPML_Page_Builders_Update_Translation $update_translation,
		IWPML_Page_Builders_Data_Settings $data_settings
	) {
		$this->register_strings = $register_strings;
		$this->update_translation = $update_translation;
		$this->data_settings = $data_settings;
	}

	public function add_hooks() {
		add_filter( 'wpml_page_builder_support_required', array( $this, 'support_required' ) );
		add_action( 'wpml_page_builder_register_strings', array( $this, 'register_pb_strings' ), 10, 2 );
		add_action( 'wpml_page_builder_string_translated', array( $this, 'update_translated_post' ), self::STRINGS_TRANSLATED_PRIORITY, 5 );
		add_filter( 'wpml_get_translatable_types', array( $this, 'remove_shortcode_strings_type_filter' ), 12, 1 );

		$this->data_settings->add_hooks();
	}

	/**
	 * @param array $page_builder_plugins
	 *
	 * @return array
	 */
	public function support_required( array $page_builder_plugins ) {
		$page_builder_plugins[] = $this->data_settings->get_pb_name();
		return $page_builder_plugins;
	}

	/**
	 * @param $post
	 * @param $package_key
	 */
	public function register_pb_strings( $post, $package_key ) {
		if ( $this->data_settings->get_pb_name() === $package_key['kind'] ) {
			$this->register_strings->register_strings( $post, $package_key );
		}
	}

	/**
	 * @param string $kind
	 * @param int $translated_post_id
	 * @param WP_Post $original_post
	 * @param $string_translations
	 * @param string $lang
	 */
	public function update_translated_post( $kind, $translated_post_id, WP_Post $original_post, $string_translations, $lang ) {
		if ( $this->data_settings->get_pb_name() === $kind ) {
			$this->update_translation->update( $translated_post_id, $original_post, $string_translations, $lang );
		}
	}

	public function remove_shortcode_strings_type_filter( $types ) {
		unset( $types[ sanitize_title_with_dashes( $this->data_settings->get_pb_name() ) ] );
		return $types;
	}

}