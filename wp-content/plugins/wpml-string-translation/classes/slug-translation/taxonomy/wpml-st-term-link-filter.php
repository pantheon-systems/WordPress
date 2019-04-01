<?php

class WPML_ST_Term_Link_Filter {

	const CACHE_GROUP = 'WPML_ST_Term_Link_Filter::replace_base_in_permalink_structure';

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Tax_Slug_Translation_Records $slug_records */
	private $slug_records;

	/** @var WPML_WP_Cache_Factory $cache_factory */
	private $cache_factory;

	public function __construct(
		WPML_Tax_Slug_Translation_Records $slug_records,
		SitePress $sitepress,
		WPML_WP_Cache_Factory $cache_factory
	) {
		$this->slug_records  = $slug_records;
		$this->sitepress     = $sitepress;
		$this->cache_factory = $cache_factory;
	}

	/**
	 * Filters the permalink structure for a terms before token replacement occurs
	 * with the hook filter `pre_term_link` available since WP 4.9.0
	 *
	 * @see get_term_link
	 *
	 * @param false|string $termlink
	 * @param WP_Term      $term
	 *
	 * @return false|string
	 */
	public function replace_slug_in_termlink( $termlink, $term ) {
		if ( ! $termlink || ! $this->sitepress->is_translated_taxonomy( $term->taxonomy ) ) {
			return $termlink;
		}

		$term_lang  = $this->sitepress->get_language_for_element( $term->term_taxonomy_id, 'tax_' . $term->taxonomy );
		$cache_key  = $termlink . $term_lang;
		$cache_item = $this->cache_factory->create_cache_item( self::CACHE_GROUP, $cache_key );

		if ( $cache_item->exists() ) {
			$termlink = $cache_item->get();
		} else {
			$original_slug   = $this->slug_records->get_original( $term->taxonomy );
			$translated_slug = $this->slug_records->get_translation( $term->taxonomy, $term_lang );

			if ( $original_slug && $translated_slug && $original_slug !== $translated_slug ) {
				$termlink = $this->replace_slug( $termlink, $original_slug, $translated_slug );
			}

			$cache_item->set( $termlink );
		}

		return $termlink;
	}

	/**
	 * @param string $termlink
	 * @param string $original_slug
	 * @param string $translated_slug
	 *
	 * @return string
	 */
	private function replace_slug( $termlink, $original_slug, $translated_slug ) {
		if ( preg_match( '#/?' . preg_quote( $original_slug ) . '/#', $termlink ) ) {
			$termlink = preg_replace(
				'#^(/?)(' . addslashes( $original_slug ) . ')/#',
				"$1$translated_slug/",
				$termlink
			);
		}

		return $termlink;
	}
}
