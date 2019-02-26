<?php

class WPML_Display_As_Translated_Posts_Query extends WPML_Display_As_Translated_Query {

	/** @var string $post_table */
	private $post_table;

	/**
	 * WPML_Display_As_Translated_Posts_Query constructor.
	 *
	 * @param wpdb $wpdb
	 * @param string $post_table_alias
	 */
	public function __construct( wpdb $wpdb, $post_table_alias = null ) {
		parent::__construct( $wpdb );
		$this->post_table = $post_table_alias ? $post_table_alias : $wpdb->posts;
	}

	/**
	 * @param array $post_types
	 *
	 * @return string
	 */
	protected function get_content_types_query( $post_types ) {
		$post_types = wpml_prepare_in( $post_types );
		return "{$this->post_table}.post_type IN ( {$post_types} )";
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	protected function get_query_for_translation_not_published( $language ) {
		return $this->wpdb->prepare( "
			( SELECT COUNT(element_id)
				FROM {$this->wpdb->prefix}icl_translations t2
				JOIN {$this->wpdb->posts} p ON p.id = t2.element_id
				WHERE t2.trid = {$this->icl_translation_table_alias}.trid
				AND t2.language_code = %s
				AND (
					p.post_status = 'publish' OR 
					p.post_type='attachment' AND p.post_status = 'inherit'
				)
			) = 0",
			$language );
	}

}