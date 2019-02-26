<?php

class WPML_Tax_Permalink_Filters implements IWPML_Action {

	/** @var WPML_Translation_Element_Factory */
	private $term_element_factory;

	/** @var WPML_URL_Converter */
	private $url_converter;

	/** @var WPML_WP_Cache_Factory $cache_factory */
	private $cache_factory;

	/** @var WPML_Get_LS_Languages_Status  */
	private $ls_languages_status;

	public function __construct(
		WPML_URL_Converter $url_converter,
		WPML_WP_Cache_Factory $cache_factory,
		WPML_Translation_Element_Factory $term_element_factory,
		WPML_Get_LS_Languages_Status $ls_language_status
	) {
		$this->term_element_factory = $term_element_factory;
		$this->url_converter        = $url_converter;
		$this->cache_factory        = $cache_factory;
		$this->ls_languages_status  = $ls_language_status;
	}

	public function add_hooks() {
		add_filter( 'term_link', array( $this, 'cached_filter_tax_permalink' ), 1, 3 );
	}

	public function cached_filter_tax_permalink( $permalink, $tag, $taxonomy ) {
		$tag    = is_object( $tag ) ? $tag : get_term( $tag, $taxonomy );
		$tag_id = $tag ? $tag->term_id : 0;

		$cache = $this->cache_factory->create_cache_item( 'icl_tax_permalink_filter', array( $tag_id, $taxonomy, $this->is_link_for_language_switcher() ) );
		if ( $cache->exists() ) {
			return $cache->get();
		}

		$permalink = $this->filter_tax_permalink( $permalink, $tag_id );

		$cache->set( $permalink );

		return $permalink;
	}

	/**
	 * Filters the permalink pointing at a taxonomy archive to correctly reflect the language of its underlying term
	 *
	 * @param string $permalink url pointing at a term's archive
	 * @param int $tag_id
	 *
	 * @return string
	 */
	private function filter_tax_permalink( $permalink, $tag_id ) {
		if ( $tag_id ) {
			$term_element = $this->term_element_factory->create( $tag_id, 'term' );

			if ( ! $this->is_display_as_translated_and_in_default_lang( $term_element )
				|| $this->is_link_for_language_switcher()
			) {
				$term_language = $term_element->get_language_code();

				if ( (bool) $term_language ) {
					$permalink = $this->url_converter->convert_url( $permalink, $term_language );
				}
			}
		}

		return $permalink;
	}

	private function is_display_as_translated_and_in_default_lang( WPML_Translation_Element $element ) {
		return $element->is_display_as_translated()
		       && $element->is_in_default_language();
	}

	private function is_link_for_language_switcher() {
		return $this->ls_languages_status->is_getting_ls_languages();
	}


}
