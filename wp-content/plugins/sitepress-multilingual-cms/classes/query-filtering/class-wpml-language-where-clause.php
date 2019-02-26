<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 27/10/17
 * Time: 4:28 PM
 */

class WPML_Language_Where_Clause {

	/** @var SitePress $sitepress */
	private $sitepress;
	/** @var wpdb $wpdb */
	private $wpdb;
	/** @var WPML_Display_As_Translated_Posts_Query $display_as_translated_query */
	private $display_as_translated_query;

	public function __construct( SitePress $sitepress, wpdb $wpdb, WPML_Display_As_Translated_Posts_Query $display_as_translated_query ) {
		$this->sitepress                   = $sitepress;
		$this->wpdb                        = $wpdb;
		$this->display_as_translated_query = $display_as_translated_query;
	}

	public function get( $post_type ) {

		if ( $this->sitepress->is_translated_post_type( $post_type ) ) {
			$current_language = $this->sitepress->get_current_language();

			if ( $this->sitepress->is_display_as_translated_post_type( $post_type ) ) {
				$default_language              = $this->sitepress->get_default_language();
				$display_as_translated_snippet = $this->display_as_translated_query->get_language_snippet( $current_language, $default_language, array( $post_type ) );
			} else {
				$display_as_translated_snippet = '0';
			}
			return $this->wpdb->prepare( " AND (language_code = '%s' OR {$display_as_translated_snippet} )", $current_language );
		} else {
			return '';
		}
	}

}