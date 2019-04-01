<?php

class WPML_Inactive_Content {

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var string $current_language */
	private $current_language;

	/** @var array $content_types */
	private $content_types;

	/** @var array $inactive */
	private $inactive;

	public function __construct( wpdb $wpdb, $current_language ) {
		$this->wpdb             = $wpdb;
		$this->current_language = $current_language;
	}

	/** @return bool */
	public function has_entries() {
		return (bool) $this->get_inactive();
	}

	/** @return array */
	public function get_content_types() {

		foreach ( $this->get_inactive() as $types ) {

			foreach ( $types as $type => $slugs ) {

				foreach ( $slugs as $slug => $count ) {
					$this->content_types[ $type ][ $slug ] = $this->get_label( $type, $slug );
				}
			}
		}

		return $this->content_types;
	}

	/** @return array */
	public function get_languages() {
		return array_keys( $this->get_inactive() );
	}

	/** @return array */
	public function get_language_counts_rows() {
		$counts = array();

		foreach ( $this->get_languages() as $language ) {

			foreach ( $this->get_content_types() as $type => $slugs ) {

				foreach ( $slugs as $slug => $label ) {

					$counts[ $language ][] = $this->count( $language, $type, $slug );
				}
			}
		}

		return $counts;
	}

	/** @return array */
	public function get_total_counts() {
		$total_counts = array();

		foreach ( $this->get_language_counts_rows() as $lang_counts ) {

			for ( $i = 0; $i < count( $lang_counts ); $i++ ) {

				if ( ! isset( $total_counts[ $i ] ) ) {
					$total_counts[ $i ] = 0;
				}

				$total_counts[ $i ] += $lang_counts[ $i ];
			}
		}

		return $total_counts;
	}

	/**
	 * @param string $lang
	 * @param string $type
	 * @param string $slug
	 *
	 * @return int
	 */
	private function count( $lang, $type, $slug ) {

		if ( isset( $this->inactive[ $lang ][ $type ][ $slug ] ) ) {
			return (int) $this->inactive[ $lang ][ $type ][ $slug ];
		}

		return 0;
	}

	/** @return array */
	private function get_inactive() {

		if ( null === $this->inactive ) {
			$this->inactive   = array();
			$post_query       = $this->wpdb->prepare( "
					SELECT COUNT(posts.ID) AS c, posts.post_type, languages_translations.name AS language
					FROM {$this->wpdb->prefix}icl_translations translations
					JOIN {$this->wpdb->posts} posts
						ON translations.element_id = posts.ID AND translations.element_type LIKE %s
					JOIN {$this->wpdb->prefix}icl_languages languages
						ON translations.language_code = languages.code AND languages.active = 0
					JOIN {$this->wpdb->prefix}icl_languages_translations languages_translations
						ON languages_translations.language_code = languages.code
							AND languages_translations.display_language_code = %s
					GROUP BY posts.post_type, translations.language_code
				", array( wpml_like_escape( 'post_' ) . '%', $this->current_language )
			);

			$post_results = $this->wpdb->get_results( $post_query );

			if ( $post_results ) {
				foreach ( $post_results as $r ) {
					$this->inactive[ $r->language ]['post'][ $r->post_type ] = $r->c;
				}
			}

			$tax_query = $this->wpdb->prepare( "
				   SELECT COUNT(posts.term_taxonomy_id) AS c, posts.taxonomy, languages_translations.name AS language
				   FROM {$this->wpdb->prefix}icl_translations translations
					JOIN {$this->wpdb->term_taxonomy} posts
						ON translations.element_id = posts.term_taxonomy_id
					JOIN {$this->wpdb->prefix}icl_languages languages
						ON translations.language_code = languages.code AND languages.active = 0
					JOIN {$this->wpdb->prefix}icl_languages_translations languages_translations
						ON languages_translations.language_code = languages.code
							AND languages_translations.display_language_code = %s
					WHERE translations.element_type LIKE %s
					GROUP BY posts.taxonomy, translations.language_code
				", $this->current_language, wpml_like_escape(  'tax_') . '%' );

			$tax_results = $this->wpdb->get_results( $tax_query );

			if ( $tax_results ) {
				foreach ( $tax_results as $r ) {
					if ( ! $this->is_only_default_category( $r ) ) {
						$this->inactive[ $r->language ]['taxonomy'][ $r->taxonomy ] = $r->c;
					}
				}
			}
		}

		return $this->inactive;
	}

	/**
	 * @param stdClass $r
	 *
	 * @return bool
	 */
	private function is_only_default_category( $r ) {
		return $r->taxonomy === 'category' && $r->c == 1;
	}


	/**
	 * @param string $type
	 * @param string $slug
	 *
	 * @return null|string
	 */
	private function get_label( $type, $slug ) {
		if ( 'post' === $type ) {
			$type_object = get_post_type_object( $slug );
		} else {
			$type_object = get_taxonomy( $slug );
		}

		if ( isset( $type_object->label ) ) {
			return $type_object->label;
		}

		return null;
	}
}
