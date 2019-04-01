<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 28/10/17
 * Time: 5:07 PM
 */

class WPML_Fix_Links_In_Display_As_Translated_Content implements IWPML_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Translate_Link_Targets $translate_link_targets */
	private $translate_link_targets;

	public function __construct( SitePress $sitepress, WPML_Translate_Link_Targets $translate_link_targets ) {
		$this->sitepress              = $sitepress;
		$this->translate_link_targets = $translate_link_targets;
	}

	public function add_hooks() {
		add_filter( 'the_content', array(
			$this,
			'fix_fallback_links'
		), WPML_LS_Render::THE_CONTENT_FILTER_PRIORITY - 1 );
	}

	public function fix_fallback_links( $content ) {
		if ( stripos( $content, '<a' ) !== false ) {
			if ( $this->is_display_as_translated_content_type() ) {
				list( $content, $encoded_ls_links ) = $this->encode_language_switcher_links( $content );
				$content = $this->translate_link_targets->convert_text( $content );
				$content = $this->decode_language_switcher_links( $content, $encoded_ls_links );
			}
		}

		return $content;
	}

	private function is_display_as_translated_content_type() {
		$queried_object = get_queried_object();
		if ( isset( $queried_object->post_type ) ) {
			return $this->sitepress->is_display_as_translated_post_type( $queried_object->post_type );
		} else {
			return false;
		}
	}

	private function encode_language_switcher_links( $content ) {
		$encoded_ls_links = array();

		if ( preg_match_all( '/<a\s[^>]*class\s*=\s*"([^"]*)"[^>]*>/', $content, $matches ) ) {
			foreach ( $matches[1] as $index => $match ) {
				if ( strpos( $match, WPML_LS_Model_Build::LINK_CSS_CLASS ) !== false ) {
					$link                              = $matches[0][ $index ];
					$encoded_link                      = md5( $link );
					$encoded_ls_links[ $encoded_link ] = $link;
					$content                           = str_replace( $link, $encoded_link, $content );
				}
			}
		}

		return array( $content, $encoded_ls_links );
	}

	private function decode_language_switcher_links( $content, $encoded_ls_links ) {
		foreach ( $encoded_ls_links as $encoded => $link ) {
			$content = str_replace( $encoded, $link, $content );
		}

		$this->encoded_ls_links = array();

		return $content;
	}
}

