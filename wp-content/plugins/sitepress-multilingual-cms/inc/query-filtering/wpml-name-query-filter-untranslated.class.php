<?php

/**
 * Class WPML_Name_Query_Filter_Untranslated
 *
 * @package    wpml-core
 * @subpackage post-translation
 *
 * @since      3.2.3
 */
class WPML_Name_Query_Filter_Untranslated extends WPML_Name_Query_Filter {
	
	protected function select_best_match( $pages_with_name ) {
		if ( ! empty( $pages_with_name['matching_ids'] ) ) {
			return reset( $pages_with_name['matching_ids'] );
		} elseif ( ! empty( $pages_with_name['related_ids'] ) ) {
			return reset( $pages_with_name['related_ids'] );
		}

		return null;
	}

	/**
	 * Returns a SQL snippet for joining the posts table with icl translations filtered for the post_type
	 * of this class.
	 *
	 * @return string
	 */
	protected function get_from_join_snippet() {

		return " FROM {$this->wpdb->posts} p ";
	}
}