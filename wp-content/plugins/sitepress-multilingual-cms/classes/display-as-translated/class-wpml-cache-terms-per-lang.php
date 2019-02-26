<?php

class WPML_Cache_Terms_Per_Lang implements IWPML_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_filter( 'get_the_terms', array( $this, 'terms_per_lang' ), 10, 3 );
		add_action( 'clean_object_term_cache', array( $this, 'clear_cache' ), 10, 2 );
	}

	public function terms_per_lang( $terms, $post_id, $taxonomy ) {
		$current_post = get_post( $post_id );
		if ( $this->sitepress->is_display_as_translated_post_type( $current_post->post_type ) ) {
			$current_language = $this->sitepress->get_current_language();
			$new_terms = wp_cache_get( $post_id, "{$taxonomy}_relationships_in_{$current_language}" );
			if ( is_array( $new_terms ) ) {
				$terms = $new_terms;
			} else {
				$new_terms = wp_get_object_terms( $post_id, $taxonomy );
				if ( ! is_wp_error( $new_terms ) ) {
					wp_cache_add( $post_id, $new_terms, "{$taxonomy}_relationships_in_{$current_language}" );
					$terms = $new_terms;
				}
			}
			if ( empty( $terms ) ) {
				return false;
			}
		}

		return $terms;
	}

	public function clear_cache( $object_ids, $object_type ) {
		$taxonomies = get_object_taxonomies( $object_type );

		foreach ( array_keys( $this->sitepress->get_active_languages() ) as $language ) {

			foreach ( $object_ids as $id ) {
				foreach ( $taxonomies as $taxonomy ) {
					wp_cache_delete($id, "{$taxonomy}_relationships_in_{$language}");
				}
			}

		}
	}

}