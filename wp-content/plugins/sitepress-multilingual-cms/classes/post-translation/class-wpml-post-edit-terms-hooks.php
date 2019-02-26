<?php

class WPML_Post_Edit_Terms_Hooks implements IWPML_Action {

	const AFTER_POST_DATA_SANITIZED_ACTION = 'init';

	/** @var IWPML_Current_Language $language */
	private $language;

	/** @var wpdb $wpdb */
	private $wpdb;

	public function __construct( IWPML_Current_Language $current_language, wpdb $wpdb ) {
		$this->language = $current_language;
		$this->wpdb     = $wpdb;
	}

	public function add_hooks() {
		add_action( self::AFTER_POST_DATA_SANITIZED_ACTION, array( $this, 'set_tags_input_with_ids' ) );
	}

	public function set_tags_input_with_ids() {
		$tag_names = $this->get_tags_from_tax_input();

		if ( $tag_names ) {
			$sql = "SELECT t.name, t.term_id FROM {$this->wpdb->terms} AS t
				LEFT JOIN {$this->wpdb->term_taxonomy} AS tt
					ON tt.term_id = t.term_id
				LEFT JOIN {$this->wpdb->prefix}icl_translations AS tr
					ON tr.element_id = tt.term_taxonomy_id AND tr.element_type = 'tax_post_tag'
				WHERE tr.language_code = %s AND t.name IN(" . wpml_prepare_in( $tag_names ) . ")";

			$tags = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $this->language->get_current_language() ) );

			foreach ( $tags as $tag ) {
				$_POST['tags_input'][] = (int) $tag->term_id;
			}
		}
	}

	/**
	 * @return array
	 */
	public function get_tags_from_tax_input() {
		if ( ! empty( $_POST['tax_input']['post_tag'] ) ) {
			$tags = $_POST['tax_input']['post_tag'];

			if ( ! is_array( $tags ) ) {
				/**
				 * This code is following the logic from `edit_post()` in core
				 * where the terms name are converted into IDs.
				 *
				 * @see edit_post
				 */
				$delimiter = _x( ',', 'tag delimiter' );
				$tags      = explode( $delimiter, trim( $tags, " \n\t\r\0\x0B," ) );
			}

			return $tags;
		}

		return null;
	}
}
