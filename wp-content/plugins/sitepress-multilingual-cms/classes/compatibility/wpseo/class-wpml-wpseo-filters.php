<?php

/**
 * Class WPML_ST_WPSEO_Filters
 *
 * Compatibility class for WordPress SEO plugin
 */
class WPML_WPSEO_Filters {

	/* @var WPML_Canonicals $canonicals */
	private $canonicals;

	/**
	 * @var array
	 */
	private $user_meta_fields = array(
		'wpseo_title',
		'wpseo_metadesc',
	);

	/**
	 * WPML_WPSEO_Filters constructor.
	 *
	 * @param WPML_Canonicals $canonicals
	 */
	public function __construct( WPML_Canonicals $canonicals ) {
		$this->canonicals = $canonicals;
	}

	public function init_hooks() {
		add_filter( 'wpml_translatable_user_meta_fields', array( $this, 'translatable_user_meta_fields_filter' ) );
		add_action( 'wpml_before_make_duplicate',         array( $this, 'before_make_duplicate_action' ) );
		add_filter( 'wpseo_canonical',                    array( $this, 'canonical_filter' ) );
		add_filter( 'wpml_must_translate_canonical_url',  array( $this, 'must_translate_canonical_url_filter' ), 10, 2 );
		add_filter( 'wpseo_prev_rel_link',                array( $this, 'rel_link_filter' ) );
		add_filter( 'wpseo_next_rel_link',                array( $this, 'rel_link_filter' ) );
	}

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public function translatable_user_meta_fields_filter( $fields ) {
		return array_merge( $this->user_meta_fields, $fields );
	}

	/**
	 * @return array
	 */
	public function get_user_meta_fields() {
		return $this->user_meta_fields;
	}

	/**
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-2701
	 */
	public function before_make_duplicate_action() {
		add_filter( 'wpseo_premium_post_redirect_slug_change', '__return_true' );
	}

	/**
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-3694
	 *
	 * @param string|bool $url
	 *
	 * @return string
	 */
	public function canonical_filter( $url ) {
		$obj = get_queried_object();

		if ( $obj instanceof WP_Post ) {
			/* @var WP_Post $obj */
			$url = $this->canonicals->get_canonical_url( $url, $obj, '' );
		}

		if ( null === $obj ) {
			$url = $this->canonicals->get_general_canonical_url( $url );
		}

		return $url;
	}

	/**
	 * Filter canonical url. If Yoast canonical is set, returns false, otherwise returns $should_translate.
	 * False is the signal that Yoast canonical exists and we have to stop further processing of url.
	 *
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-5707
	 *
	 * @param bool $should_translate Should translate flag.
	 * @param WPML_Post_Element $post_element Post Element
	 *
	 * @return bool
	 */
	public function must_translate_canonical_url_filter( $should_translate, $post_element ) {
		$post_id = $post_element->get_element_id();
		if ( $post_id && get_post_meta( $post_id, '_yoast_wpseo_canonical', true ) ) {
			return false;
		}

		return $should_translate;
	}

	/**
	 * Prev/next page general link filter.
	 *
	 * @param string $link Link to a prev/next page in archive.
	 *
	 * @return string
	 */
	public function rel_link_filter( $link ) {
		if ( preg_match( '/href="([^"]+)"/', $link, $matches ) ) {
			$canonical_url = $this->canonicals->get_general_canonical_url( $matches[1] );
			$link          = str_replace( $matches[1], $canonical_url, $link );
		}

		return $link;
	}
}
