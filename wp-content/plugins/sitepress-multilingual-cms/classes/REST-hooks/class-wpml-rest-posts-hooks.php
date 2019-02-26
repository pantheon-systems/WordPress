<?php

class WPML_REST_Posts_Hooks implements IWPML_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Term_Translation $term_translations */
	private $term_translations;

	public function __construct( SitePress $sitepress, WPML_Term_Translation $term_translations ) {
		$this->sitepress         = $sitepress;
		$this->term_translations = $term_translations;
	}

	public function add_hooks() {
		$post_types = $this->sitepress->get_translatable_documents();

		foreach ( $post_types as $post_type => $post_object ) {
			add_filter( "rest_prepare_$post_type", array( $this, 'prepare_post' ), 10, 2 );
		}
	}

	/**
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Post object.
	 *
	 * @return WP_REST_Response
	 */
	public function prepare_post( $response, $post ) {
		if ( $this->sitepress->get_setting( 'sync_post_taxonomies' ) ) {
			$response = $this->preset_terms_in_new_translation( $response, $post );
		}

		return $response;
	}

	/**
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Post object.
	 *
	 * @return WP_REST_Response
	 */
	private function preset_terms_in_new_translation( $response, $post ) {
		if ( ! isset( $_GET['trid'] ) ) {
			return $response;
		}

		$trid        = filter_var( $_GET['trid'], FILTER_SANITIZE_NUMBER_INT );
		$source_lang = isset( $_GET['source_lang'] )
			? filter_var( $_GET['source_lang'], FILTER_SANITIZE_FULL_SPECIAL_CHARS )
			: $this->sitepress->get_default_language();

		$element_type = 'post_' . $post->post_type;

		if ( $this->sitepress->get_element_trid( $post->ID, $element_type ) ) {
			return $response;
		}

		$translations = $this->sitepress->get_element_translations( $trid, $element_type );

		if ( ! isset( $translations[ $source_lang ] ) ) {
			return $response;
		}

		$current_lang      = $this->sitepress->get_current_language();
		$translatable_taxs = $this->sitepress->get_translatable_taxonomies( true, $post->post_type );
		$all_taxs          = wp_list_filter( get_object_taxonomies( $post->post_type, 'objects' ), array( 'show_in_rest' => true ) );
		$data              = $response->get_data();

		$this->sitepress->switch_lang( $source_lang );

		foreach ( $all_taxs as $tax ) {
			$tax_rest_base = ! empty( $tax->rest_base ) ? $tax->rest_base : $tax->name;

			if ( ! isset( $data[ $tax_rest_base ] ) ) {
				continue;
			}

			$terms = get_the_terms( $translations[ $source_lang ]->element_id, $tax->name );

			if ( ! is_array( $terms ) ) {
				continue;
			}

			$term_ids = $this->get_translated_term_ids( $terms, $tax, $translatable_taxs, $current_lang );
			wp_set_object_terms( $post->ID, $term_ids, $tax->name );
			$data[ $tax_rest_base ] = $term_ids;
		}

		$this->sitepress->switch_lang( null );
		$response->set_data( $data );

		return $response;
	}

	/**
	 * @param array    $terms
	 * @param stdClass $tax
	 * @param array    $translatable_taxs
	 * @param string   $current_lang
	 *
	 * @return array
	 */
	private function get_translated_term_ids( array $terms, $tax, array $translatable_taxs, $current_lang ) {
		$term_ids = array();

		foreach ( $terms as $term ) {
			if ( in_array( $tax->name, $translatable_taxs ) ) {
				$term_ids[] = $this->term_translations->term_id_in( $term->term_id, $current_lang, false );
			} else {
				$term_ids[] = $term->term_id;
			}
		}

		return array_values( array_filter( $term_ids ) );
	}
}
