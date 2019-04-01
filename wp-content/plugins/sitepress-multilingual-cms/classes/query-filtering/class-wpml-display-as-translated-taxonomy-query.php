<?php

class WPML_Display_As_Translated_Taxonomy_Query extends WPML_Display_As_Translated_Query {

	/** @var string $term_taxonomy_table */
	private $term_taxonomy_table;

	/**
	 * WPML_Display_As_Translated_Posts_Query constructor.
	 *
	 * @param wpdb $wpdb
	 * @param string $term_taxonomy_table_alias
	 */
	public function __construct( wpdb $wpdb, $term_taxonomy_table_alias = null ) {
		parent::__construct( $wpdb, 'icl_t' );
		$this->term_taxonomy_table = $term_taxonomy_table_alias ? $term_taxonomy_table_alias : $wpdb->term_taxonomy;
	}

	/**
	 * @param array $content_types
	 *
	 * @return string
	 */
	protected function get_content_types_query( $taxonomies ) {
		$taxonomies = wpml_prepare_in( $taxonomies );
		return "{$this->term_taxonomy_table}.taxonomy IN ( {$taxonomies} )";
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	protected function get_query_for_translation_not_published( $language ) {
		return '0';
	}

}