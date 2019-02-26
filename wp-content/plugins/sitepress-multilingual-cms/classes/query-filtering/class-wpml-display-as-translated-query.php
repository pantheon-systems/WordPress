<?php

abstract class WPML_Display_As_Translated_Query {

	/** @var wpdb $wpdb */
	protected $wpdb;

	/** @var string $icl_translation_table_alias */
	protected $icl_translation_table_alias;

	/**
	 * WPML_Display_As_Translated_Query constructor.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb, $icl_translation_table_alias = 'wpml_translations' ) {
		$this->wpdb                        = $wpdb;
		$this->icl_translation_table_alias = $icl_translation_table_alias;
	}

	/**
	 * @param string $current_language
	 * @param string $fallback_language
	 * @param array $content_types
	 *
	 * @return string
	 */
	public function get_language_snippet( $current_language, $fallback_language, $content_types ) {
		if ( $content_types && $fallback_language ) {
			$content_types_query                 = $this->get_content_types_query( $content_types );
			$sub_query_no_translation            = $this->get_query_for_no_translation( $current_language );
			$sub_query_translation_not_published = $this->get_query_for_translation_not_published( $current_language );

			return $this->wpdb->prepare(
				"(
					{$this->icl_translation_table_alias}.language_code = %s
					AND {$content_types_query}
					AND ( ( {$sub_query_no_translation} ) OR ( {$sub_query_translation_not_published} ) ) 
				)",
				$fallback_language
			);
		} else {
			return '0';
		}
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	private function get_query_for_no_translation( $language ) {
		return $this->wpdb->prepare( "
			( SELECT COUNT(element_id)
			  FROM {$this->wpdb->prefix}icl_translations
			  WHERE trid = {$this->icl_translation_table_alias}.trid
			  AND language_code = %s
			) = 0
			",
			$language
		);
	}

	/**
	 * @param array $content_types
	 *
	 * @return string
	 */
	abstract protected function get_content_types_query( $content_types );

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	abstract protected function get_query_for_translation_not_published( $language );

}