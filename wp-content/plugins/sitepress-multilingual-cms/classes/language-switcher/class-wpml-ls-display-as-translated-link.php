<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 17/10/17
 * Time: 10:56 PM
 */

class WPML_LS_Display_As_Translated_Link {

	/** @var SitePress $sitepress */
	private $sitepress;
	/** @var IWPML_URL_Converter_Strategy $url_converter */
	private $url_converter;
	/** @var WP_Query $wp_query */
	private $wp_query;
	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;
	/** @var string $default_language */
	private $default_language;
	/** @var string $processed_language */
	private $processed_language;

	public function __construct(
		SitePress $sitepress,
		IWPML_URL_Converter_Strategy $url_converter,
		WP_Query $wp_query,
		WPML_Translation_Element_Factory $element_factory
	) {
		$this->sitepress        = $sitepress;
		$this->url_converter    = $url_converter;
		$this->wp_query         = $wp_query;
		$this->element_factory  = $element_factory;
		$this->default_language = $sitepress->get_default_language();
	}

	public function get_url( $translations, $lang ) {
		$queried_object = $this->wp_query->get_queried_object();
		if ( $queried_object instanceof WP_Post ) {
			return $this->get_post_url( $translations, $lang, $queried_object );
		} elseif ( $queried_object instanceof WP_Term ) {
			return $this->get_term_url( $translations, $lang, $queried_object );
		} else {
			return null;
		}
	}

	private function get_post_url( $translations, $lang, $queried_object ) {
		$url = null;

		if ( $this->sitepress->is_display_as_translated_post_type( $queried_object->post_type ) &&
		     isset( $translations[ $this->default_language ] ) ) {

			$this->sitepress->switch_lang( $this->default_language );
			$this->processed_language = $lang;
			add_filter( 'post_link_category', array( $this, 'adjust_category_in_post_permalink' ) );
			$url = get_permalink( $translations[ $this->default_language ]->element_id );
			remove_filter( 'post_link_category', array( $this, 'adjust_category_in_post_permalink' ) );
			$this->sitepress->switch_lang();
			$url = $this->url_converter->convert_url_string( $url, $lang );
		}

		return $url;
	}

	private function get_term_url( $translations, $lang, $queried_object ) {
		$url = null;

		if ( $this->sitepress->is_display_as_translated_taxonomy( $queried_object->taxonomy ) &&
		     isset( $translations[ $this->default_language ] ) ) {

			$url = get_term_link( (int) $translations[ $this->default_language ]->term_id, $queried_object->taxonomy );
			$url = $this->url_converter->convert_url_string( $url, $lang );
		}

		return $url;
	}

	/**
	 * The permalink needs to be adjusted when the URL structure contains the category tag (%category%).
	 *
	 * @param WP_Term $cat
	 *
	 * @return WP_Term
	 */
	public function adjust_category_in_post_permalink( $cat ) {
		$cat_element = $this->element_factory->create( $cat->term_id, 'term' );
		$translation = $cat_element->get_translation( $this->processed_language );

		if ( $translation ) {
			$cat = $translation->get_wp_object();
		}

		return $cat;
	}

}