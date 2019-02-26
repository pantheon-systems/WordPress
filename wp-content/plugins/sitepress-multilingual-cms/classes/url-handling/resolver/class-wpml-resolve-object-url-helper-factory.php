<?php

class WPML_Resolve_Object_Url_Helper_Factory {

	const CURRENT_URL_RESOLVER  = 'current';
	const ABSOLUTE_URL_RESOLVER = 'absolute';

	/**
	 * @return IWPML_Resolve_Object_Url
	 */
	public function create( $type = self::CURRENT_URL_RESOLVER ) {
		global $sitepress, $wp_query, $wpml_term_translations, $wpml_post_translations;

		if ( self::CURRENT_URL_RESOLVER === $type ) {
			return new WPML_Resolve_Object_Url_Helper( $sitepress, $wp_query, $wpml_term_translations, $wpml_post_translations );
		}

		if ( self::ABSOLUTE_URL_RESOLVER === $type ) {
			$absolute_links         = new AbsoluteLinks();
			$absolute_to_permalinks = new WPML_Absolute_To_Permalinks( $sitepress );

			$translate_links_target = new WPML_Translate_Link_Targets( $absolute_links, $absolute_to_permalinks );

			$resolve_url   = new WPML_Resolve_Absolute_Url( $sitepress, $translate_links_target );
			$url_persisted = WPML_Absolute_Url_Persisted::get_instance();

			return new WPML_Resolve_Absolute_Url_Cached( $url_persisted, $resolve_url );
		}

		throw new InvalidArgumentException( 'Unknown Resolve_Object_Url type: ' . $type );
	}
}