<?php

/**
 * Class WPML_Displayed_String_Filter
 *
 * Handles all string translating when rendering translated strings to the user, unless auto-registering is
 * active for strings.
 */
class WPML_Displayed_String_Filter {
	/** @var  SitePress */
	protected $sitepress;

	/** @var wpdb */
	protected $wpdb;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var WPML_ST_DB_Cache_Factory
	 */
	protected $db_cache_factory;

	/**
	 * @var WPML_ST_DB_Cache
	 */
	protected $db_cache;

	/**
	 * @param wpdb $wpdb
	 * @param SitePress $sitepress
	 * @param string $language
	 * @param null|object $existing_filter
	 * @param null|WPML_ST_Db_Cache_Factory
	 */
	public function __construct( $wpdb, $sitepress, $language, $existing_filter = null, $db_cache_factory = null ) {
		$this->sitepress = $sitepress;
		$this->wpdb      = $wpdb;
		$this->language           = $language;

		if ( $db_cache_factory instanceof WPML_ST_DB_Cache_Factory ) {
			$this->db_cache_factory = $db_cache_factory;
		} else {
			$this->db_cache_factory = new WPML_ST_DB_Cache_Factory( $wpdb );
		}

		$this->db_cache = $this->db_cache_factory->create( $language );
	}
	
	public function clear_cache() {
		$this->db_cache->clear_cache();
	}

	/**
	 * @param string       $untranslated_text
	 * @param string       $name
	 * @param string|array $context
	 * @param null|bool    $has_translation
	 *
	 * @return bool|false|string
	 */
	public function translate_by_name_and_context( $untranslated_text, $name, $context = '', &$has_translation = null ) {
		$translation = $this->get_translation( $untranslated_text, $name, $context );

		if ( $translation ) {
			$res             = $translation->get_value();
			$has_translation = $translation->has_translation();
		} else {
			$res             = $untranslated_text;
			$has_translation = false;
		}

		return $res;
	}
	
	/**
	 * @param $name
	 * @param $context
	 *
	 * @return array
	 */
	protected function transform_parameters( $name, $context ) {
		list ( $domain, $gettext_context ) = wpml_st_extract_context_parameters( $context );

		return array( $name, $domain, $gettext_context );
	}

	/**
	 * Truncates a string to the maximum string table column width
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function truncate_long_string( $string ) {
		return strlen( $string ) > WPML_STRING_TABLE_NAME_CONTEXT_LENGTH
			? mb_substr( $string, 0, WPML_STRING_TABLE_NAME_CONTEXT_LENGTH )
			: $string;
	}

	/**
	 * @param $untranslated_text
	 * @param $name
	 * @param $context
	 *
	 * @return WPML_ST_Page_Translation|null
	 */
	protected function get_translation( $untranslated_text, $name, $context ) {
		list ( $name, $domain, $gettext_context ) = $this->transform_parameters( $name, $context );
		$untranslated_text = is_string( $untranslated_text ) ? $untranslated_text : '';

		$translation = $this->db_cache->get_translation( $name, $domain, $untranslated_text, $gettext_context );

		if ( ! $translation ) {
			list( $name, $domain ) = array_map( array( $this, 'truncate_long_string' ), array( $name, $domain ) );
			$translation = $this->db_cache->get_translation( $name, $domain, $untranslated_text, $gettext_context );
		}

		return $translation;
	}
}