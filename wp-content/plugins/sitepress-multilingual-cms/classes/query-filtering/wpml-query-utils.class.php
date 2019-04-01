<?php

/**
 * Class WPML_Query_Utils
 *
 * @package wpml-core
 */
class WPML_Query_Utils {

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var WPML_WP_API */
	private $wp_api;

	/** @var array $display_as_translated_post_types */
	private $display_as_translated_post_types;

	/**
	 * WPML_Query_Utils constructor.
	 *
	 * @param wpdb $wpdb
	 * @param WPML_WP_API $wp_api
	 * @param array $display_as_translated_post_types
	 */
	public function __construct( wpdb $wpdb, WPML_WP_API $wp_api, $display_as_translated_post_types ) {
		$this->wpdb                             = $wpdb;
		$this->wp_api                           = $wp_api;
		$this->display_as_translated_post_types = $display_as_translated_post_types;
	}

	/**
	 * Returns the number of posts for a given post_type, author and language combination that is published.
	 *
	 * @param array|string $post_type
	 * @param WP_User      $author_data
	 * @param string       $lang language code to check
	 * @param string       $fallback_lang
	 *
	 * @return bool
	 *
	 * @used-by \WPML_Languages::add_author_url_to_ls_lang to determine what languages to show in the Language Switcher
	 */
	public function author_query_has_posts( $post_type, $author_data, $lang, $fallback_lang ) {
		$post_types        = (array) $post_type;
		$post_type_snippet = (bool) $post_types ? " AND post_type IN (" . wpml_prepare_in( $post_types ) . ") " : "";
		$language_snippet  = $this->get_language_snippet( $lang, $fallback_lang, $post_type );

		return (bool) $this->wpdb->get_var( $this->wpdb->prepare(
			"	SELECT COUNT(p.ID) FROM {$this->wpdb->posts} p
				JOIN {$this->wpdb->prefix}icl_translations wpml_translations
					ON p.ID=wpml_translations.element_id AND wpml_translations.element_type = CONCAT('post_', p.post_type)
				WHERE p.post_author=%d
				  " . $post_type_snippet . "
				  AND post_status='publish'
				  " . $language_snippet . " LIMIT 1",
			$author_data->ID
		)
		);
	}

	/**
	 * Returns the number of posts for a given post_type, date and language combination that is published.
	 *
	 * @param string       $lang language code to check
	 * @param string       $fallback_lang
	 * @param null|int     $year
	 * @param null|int     $month
	 * @param null|int     $day
	 * @param string|array $post_type
	 *
	 * @return bool
	 *
	 * @used-by \WPML_Languages::add_date_or_cpt_url_to_ls_lang to determine what languages to show in the Language Switcher
	 */
	public function archive_query_has_posts( $lang, $fallback_lang, $year = null, $month = null, $day = null, $post_type = 'post' ) {
		$cache_args                  = array();
		$cache_args['lang']          = $lang;
		$cache_args['fallback_lang'] = $fallback_lang;
		$cache_args['year']          = $year;
		$cache_args['month']         = $month;
		$cache_args['day']           = $day;
		$cache_args['post_type']     = $post_type;

		$cache_key     = md5( json_encode( $cache_args ) );
		$cache_group   = 'archive_query_has_posts';
		$cache         = new WPML_WP_Cache( $cache_group );
		$found         = false;
		$result       = $cache->get( $cache_key, $found );

		if ( ! $found ) {
			$post_status_snippet = $this->wp_api->current_user_can( 'read' )
				? "p.post_status IN (" . wpml_prepare_in( array( 'publish', 'private' ) ) . ") "
				: "p.post_status = 'publish'";
			$post_type_snippet = is_array( $post_type )
				? " AND post_type IN (" . wpml_prepare_in( $post_type ) . ") "
				: $this->wpdb->prepare( ' AND p.post_type = %s ', $post_type ) ;
			$year_snippet      = (bool) $year === true ? $this->wpdb->prepare( ' AND year(p.post_date) = %d ', $year ) : '';
			$month_snippet     = (bool) $month === true ? $this->wpdb->prepare( ' AND month(p.post_date) = %d ', $month ) : '';
			$day_snippet       = (bool) $day === true ? $this->wpdb->prepare( ' AND day(p.post_date) = %d ', $day ) : '';

			$lang_snippet = $this->get_language_snippet( $lang, $fallback_lang, $post_type );

			$result = $this->wpdb->get_var( "
                        SELECT p.ID FROM {$this->wpdb->posts} p
						JOIN {$this->wpdb->prefix}icl_translations wpml_translations
							ON p.ID = wpml_translations.element_id AND wpml_translations.element_type = CONCAT('post_', p.post_type)
						WHERE " . $post_status_snippet .
			                $year_snippet . $month_snippet . $day_snippet . $post_type_snippet . $lang_snippet . "
						LIMIT 1" );

			$cache->set( $cache_key, $result );
		}

		return (bool) $result;
	}

	private function get_language_snippet( $lang, $fallback_lang, $post_type ) {
		if ( in_array( $post_type, $this->display_as_translated_post_types ) ) {
			$display_as_translated_query   = new WPML_Display_As_Translated_Posts_Query( $this->wpdb, 'p' );
			$display_as_translated_snippet = $display_as_translated_query->get_language_snippet( $lang, $fallback_lang, $this->display_as_translated_post_types );
		} else {
			$display_as_translated_snippet = '0';
		}

		return $this->wpdb->prepare( " AND (wpml_translations.language_code = %s OR {$display_as_translated_snippet}) ", $lang );
	}

}