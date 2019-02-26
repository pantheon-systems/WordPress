<?php

class WPML_ST_Slug_Translation_API implements IWPML_Action {

	/**
	 * The section indexes are hardcoded in `sitepress-multilingual-cms/menu/_custom_types_translation.php`
	 */
	const SECTION_INDEX_POST = 7;
	const SECTION_INDEX_TAX  = 8;

	/** @var WPML_Slug_Translation_Records_Factory $records_factory */
	private $records_factory;

	/** @var WPML_ST_Slug_Translation_Settings_Factory $settings_factory */
	private $settings_factory;

	/** @var IWPML_Current_Language $current_language */
	private $current_language;

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	public function __construct(
		WPML_Slug_Translation_Records_Factory $records_factory,
		WPML_ST_Slug_Translation_Settings_Factory $settings_factory,
		IWPML_Current_Language $current_language,
		WPML_WP_API $wp_api
	) {
		$this->records_factory  = $records_factory;
		$this->settings_factory = $settings_factory;
		$this->current_language = $current_language;
		$this->wp_api           = $wp_api;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'init' ), WPML_Slug_Translation_Factory::INIT_PRIORITY );
	}

	public function init() {
		if ( $this->settings_factory->create()->is_enabled() ) {
			add_filter( 'wpml_get_translated_slug', array( $this, 'get_translated_slug_filter' ), 1, 4 );
			add_filter(
				'wpml_get_slug_translation_languages', array( $this, 'get_slug_translation_languages_filter' ), 1, 3
			);
			add_filter( 'wpml_type_slug_is_translated', array( $this, 'type_slug_is_translated_filter' ), 10, 3 );
		}

		add_filter( 'wpml_slug_translation_available', '__return_true', 1 );
		add_action( 'wpml_activate_slug_translation', array( $this, 'activate_slug_translation_action' ), 1, 3 );
		add_filter( 'wpml_get_slug_translation_url', array( $this, 'get_slug_translation_url_filter' ), 1, 3 );
	}

	/**
	 * @param string      $slug_value
	 * @param string      $type
	 * @param string|bool $language
	 * @param string      $element_type WPML_Slug_Translation_Factory::POST|WPML_Slug_Translation_Factory::TAX
	 *
	 * @return string
	 */
	public function get_translated_slug_filter(
		$slug_value,
		$type,
		$language = false,
		$element_type = WPML_Slug_Translation_Factory::POST
	) {
		if ( $type ) {
			$slug = $this->records_factory->create( $element_type )->get_slug( $type );

			if ( ! $language ) {
				$language = $this->current_language->get_current_language();
			}

			return $slug->filter_value( $slug_value, $language );
		}

		return $slug_value;
	}

	/**
	 * @param string $languages
	 * @param string $type
	 * @param string $element_type WPML_Slug_Translation_Factory::POST|WPML_Slug_Translation_Factory::TAX
	 *
	 * @return array
	 */
	public function get_slug_translation_languages_filter(
		$languages,
		$type,
		$element_type = WPML_Slug_Translation_Factory::POST
	) {
		return $this->records_factory->create( $element_type )->get_slug_translation_languages( $type );
	}

	/**
	 * @param string      $type
	 * @param string|null $slug_value
	 * @param string      $element_type WPML_Slug_Translation_Factory::POST|WPML_Slug_Translation_Factory::TAX
	 */
	public function activate_slug_translation_action(
		$type,
		$slug_value = null,
		$element_type = WPML_Slug_Translation_Factory::POST
	) {
		if ( ! $slug_value ) {
			$slug_value = $type;
		}

		$records  = $this->records_factory->create( $element_type );
		$settings = $this->settings_factory->create( $element_type );

		$slug = $records->get_slug( $type );

		if ( ! $slug->get_original_id() ) {
			$records->register_slug( $type, $slug_value );
		}

		if ( ! $settings->is_enabled() || ! $settings->is_translated( $type ) ) {
			$settings->set_enabled( true );
			$settings->set_type( $type, true );
			$settings->save();
		}
	}

	/**
	 * @param string $url
	 * @param string $element_type WPML_Slug_Translation_Factory::POST or WPML_Slug_Translation_Factory::TAX
	 *
	 * @return string
	 */
	public function get_slug_translation_url_filter( $url, $element_type = WPML_Slug_Translation_Factory::POST ) {
		$index = self::SECTION_INDEX_POST;

		if ( WPML_Slug_Translation_Factory::TAX === $element_type ) {
			$index = self::SECTION_INDEX_TAX;
		}

		$page = $this->wp_api->constant( 'WPML_PLUGIN_FOLDER' )
		        . '/menu/translation-options.php#ml-content-setup-sec-' . $index;

		if ( $this->wp_api->defined( 'WPML_TM_VERSION' ) ) {
			$page = $this->wp_api->constant( 'WPML_TM_FOLDER' )
			        . '/menu/settings&sm=mcsetup#ml-content-setup-sec-' . $index;
		}

		return admin_url( 'admin.php?page=' . $page );
	}

	/**
	 * @param bool   $is_translated
	 * @param string $type
	 * @param string $element_type WPML_Slug_Translation_Factory::POST or WPML_Slug_Translation_Factory::TAX
	 *
	 * @return bool
	 */
	public function type_slug_is_translated_filter(
		$is_translated,
		$type,
		$element_type = WPML_Slug_Translation_Factory::POST
	) {
		return $this->settings_factory->create( $element_type )->is_translated( $type );
	}
}
