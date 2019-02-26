<?php

class WPML_Display_As_Translated_Tax_Query implements IWPML_Action {

	// Regex to find the term query.
	// eg. term_taxonomy_id IN (8)
	// We then add the fallback term to the query
	// eg. term_taxonomy_id IN (8,9)
	const TERM_REGEX = '/term_taxonomy_id\s+(IN|in)\s*\(([^\)]+)\)/';

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Term_Translation $term_translation */
	private $term_translation;

	public function __construct( SitePress $sitepress, WPML_Term_Translation $term_translatoin ) {
		$this->sitepress        = $sitepress;
		$this->term_translation = $term_translatoin;
	}

	public function add_hooks() {
		add_filter( 'posts_where', array( $this, 'posts_where_filter' ), 10, 2 );
	}

	/**
	 * @param string $where
	 * @param WP_Query $q
	 *
	 * @return string
	 */
	public function posts_where_filter( $where, WP_Query $q ) {
		if ( $this->is_not_the_default_language() && $this->is_taxonomy_archive( $q ) ) {
			$post_types = $this->get_linked_post_types( $q );
			if ( $this->is_display_as_translated_mode( $post_types ) ) {
				$terms          = $this->find_terms( $where );
				$fallback_terms = $this->get_fallback_terms( $terms );
				$where          = $this->add_fallback_terms_to_where_clause( $where, $fallback_terms );
			}
		}

		return $where;
	}

	/**
	 * @return bool
	 */
	private function is_not_the_default_language() {
		return $this->sitepress->get_default_language() !== $this->sitepress->get_current_language();
	}

	/**
	 * @param WP_Query $q
	 *
	 * @return bool
	 */
	private function is_taxonomy_archive( WP_Query $q ) {
		return $q->is_archive() && ( $q->is_category() || $q->is_tax() || $q->is_tag() );
	}

	/**
	 * @param WP_Query $q
	 *
	 * @return array
	 */
	private function get_linked_post_types( WP_Query $q ) {
		$post_types = array();
		foreach ( $q->tax_query->queries as $tax_query ) {
			if ( isset( $tax_query['taxonomy'] ) ) {
				$post_types = array_unique( array_merge( $post_types, WPML_WP_Taxonomy::get_linked_post_types( $tax_query['taxonomy'] ) ) );
			}
		}

		return $post_types;
	}

	/**
	 * @param array $post_types
	 *
	 * @return bool
	 */
	private function is_display_as_translated_mode( $post_types ) {
		foreach ( $post_types as $post_type ) {
			if ( $this->sitepress->is_display_as_translated_post_type( $post_type ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $where
	 *
	 * @return array
	 */
	private function find_terms( $where ) {
		$terms = array();
		if ( preg_match_all( self::TERM_REGEX, $where, $matches ) ) {
			foreach ( $matches[2] as $terms_string ) {
				$terms_parts = explode( ',', $terms_string );
				$terms       = array_unique( array_merge( $terms, $terms_parts ) );
			}
		}

		return $terms;
	}

	/**
	 * @param array $terms
	 *
	 * @return array
	 */
	private function get_fallback_terms( $terms ) {
		$default_language = $this->sitepress->get_default_language();
		$fallback_terms   = array();
		foreach ( $terms as $term ) {
			$translations = $this->term_translation->get_element_translations( (int) $term );
			if ( isset( $translations[ $default_language ] ) && ! in_array( $translations[ $default_language ], $fallback_terms ) ) {
				$fallback_terms[ $term ] = $translations[ $default_language ];
			}
		}

		return $fallback_terms;
	}

	/**
	 * @param string $where
	 * @param $fallback_terms
	 *
	 * @return string
	 */
	private function add_fallback_terms_to_where_clause( $where, $fallback_terms ) {
		if ( preg_match_all( self::TERM_REGEX, $where, $matches ) ) {
			foreach ( $matches[2] as $index => $terms_string ) {
				$new_terms_string = $this->add_fallback_terms( $terms_string, $fallback_terms );
				$original_block   = $matches[0][ $index ];
				$new_block        = str_replace( '(' . $terms_string . ')', '(' . $new_terms_string . ')', $original_block );
				$where            = str_replace( $original_block, $new_block, $where );
			}
		}

		return $where;
	}

	/**
	 * @param string $terms_string
	 * @param array $fallback_terms
	 *
	 * @return string
	 */
	private function add_fallback_terms( $terms_string, $fallback_terms ) {
		$terms = explode( ',', $terms_string );
		foreach ( $terms as $term ) {
			if ( isset( $fallback_terms[ $term ] ) ) {
				$terms[] = $fallback_terms[ $term ];
			}
		}

		return implode( ',', $terms );
	}
}