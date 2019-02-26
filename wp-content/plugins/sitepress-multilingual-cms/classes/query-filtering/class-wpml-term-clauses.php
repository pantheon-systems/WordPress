<?php

class WPML_Term_Clauses {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var WPML_Display_As_Translated_Taxonomy_Query $display_as_translated_query */
	private $display_as_translated_query;

	/** @var WPML_Debug_BackTrace $debug_backtrace */
	private $debug_backtrace;

	public function __construct(
		SitePress $sitepress,
		wpdb $wpdb,
		WPML_Display_As_Translated_Taxonomy_Query $display_as_translated_query,
		WPML_Debug_BackTrace $debug_backtrace
	) {
		$this->sitepress                   = $sitepress;
		$this->wpdb                        = $wpdb;
		$this->display_as_translated_query = $display_as_translated_query;
		$this->debug_backtrace             = $debug_backtrace;
	}

	/**
	 * @param array $clauses
	 * @param array $taxonomies
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter( $clauses, $taxonomies, $args ) {
		// special case for when term hierarchy is cached in wp_options
		if ( (bool) $taxonomies === false
		     || $this->debug_backtrace->is_function_in_call_stack( '_get_term_hierarchy' )
		     || $this->debug_backtrace->is_class_function_in_call_stack( 'WPML_Term_Translation_Utils', 'synchronize_terms' )
		     || $this->debug_backtrace->is_function_in_call_stack( 'wp_get_object_terms' )
		     || $this->debug_backtrace->is_function_in_call_stack( 'get_term_by' )
		) {
			return $clauses;
		}

		$icl_taxonomies = array();
		foreach ( $taxonomies as $tax ) {
			if ( $this->sitepress->is_translated_taxonomy( $tax ) ) {
				$icl_taxonomies[] = $tax;
			}
		}

		if ( (bool) $icl_taxonomies === false ) {
			return $clauses;
		}

		$icl_taxonomies = "'tax_" . join( "','tax_", esc_sql( $icl_taxonomies ) ) . "'";

		$where_lang = $this->get_where_lang();

		$clauses['join']  .= " LEFT JOIN {$this->wpdb->prefix}icl_translations icl_t
                                    ON icl_t.element_id = tt.term_taxonomy_id
                                        AND icl_t.element_type IN ({$icl_taxonomies})";
		$clauses['where'] .= " AND ( ( icl_t.element_type IN ({$icl_taxonomies}) {$where_lang} )
                                    OR icl_t.element_type NOT IN ({$icl_taxonomies}) OR icl_t.element_type IS NULL ) ";

		return $clauses;

	}

	private function get_where_lang() {
		$lang = $this->sitepress->get_current_language();
		if ( $lang === 'all' ) {
			return '';
		} else {
			$display_as_translated_snippet = $this->get_display_as_translated_snippet( $lang, $this->sitepress->get_default_language() );
			return $this->wpdb->prepare( " AND ( icl_t.language_code = %s OR {$display_as_translated_snippet} ) ", $lang );
		}
	}

	private function get_display_as_translated_snippet( $current_language, $fallback_language ) {
		$taxonomies = $this->sitepress->get_display_as_translated_taxonomies();
		if ( $taxonomies && ( ! is_admin() || WPML_Ajax::is_frontend_ajax_request() ) ) {
			return $this->display_as_translated_query->get_language_snippet( $current_language, $fallback_language, $taxonomies );
		} else {
			return '0';
		}
	}

}