<?php

class WPML_Term_Adjust_Id {

	private $debug_backtrace;
	private $term_translation;
	private $post_translation;
	private $adjust_id_url_filter_off;
	private $sitepress;

	public function __construct(
		WPML_Debug_BackTrace $debug_backtrace,
		WPML_Term_Translation $term_translation,
		WPML_Post_Translation $post_translation,
		$adjust_id_url_filter_off,
		SitePress $sitepress
	) {
		$this->debug_backtrace          = $debug_backtrace;
		$this->term_translation         = $term_translation;
		$this->post_translation         = $post_translation;
		$this->adjust_id_url_filter_off = $adjust_id_url_filter_off;
		$this->sitepress                = $sitepress;
	}

	/**
	 * @param WP_Term $term
	 *
	 * @return WP_Term
	 */
	public function filter( WP_Term $term ) {
		if ( $this->adjust_id_url_filter_off
		     || ! $this->sitepress->get_setting( 'auto_adjust_ids' )
		     || $this->debug_backtrace->is_function_in_call_stack( 'get_category_parents' )
		     || $this->debug_backtrace->is_function_in_call_stack( 'get_permalink' )
		     || ( $this->debug_backtrace->is_function_in_call_stack( 'wp_update_post' )
		          && $this->debug_backtrace->is_function_in_call_stack( 'get_term' ) )
		     || $this->is_ajax_add_term_translation()
		) {
			return $term;
		}

		$translated_id = $this->term_translation->element_id_in( $term->term_taxonomy_id, $this->sitepress->get_current_language() );

		if ( $translated_id && (int) $translated_id !== (int) $term->term_taxonomy_id ) {
			$object_id = isset( $term->object_id ) ? $term->object_id : false;
			$term      = get_term_by( 'term_taxonomy_id', $translated_id, $term->taxonomy );
			if ( $object_id ) {
				$translated_object_id = $this->post_translation->element_id_in( $object_id, $this->sitepress->get_current_language() );
				if ( $translated_object_id ) {
					$term->object_id = $translated_object_id;
				} else if ( $this->sitepress->is_display_as_translated_post_type( $this->post_translation->get_type( $object_id ) ) ) {
					$term->object_id = $this->post_translation->element_id_in( $object_id, $this->sitepress->get_default_language() );
				}
			}
		}

		return $term;
	}

	private function is_ajax_add_term_translation() {
		$taxonomy = isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : false;
		if ( $taxonomy ) {
			return isset( $_POST['action'] ) && 'add-tag' === $_POST['action'] && ! empty( $_POST[ 'icl_tax_' . $taxonomy . '_language' ] );
		}

		return false;
	}
}