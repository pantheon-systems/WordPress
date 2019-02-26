<?php

class WPML_Compatibility_ElasticPress_Lang {
	/** @var Sitepress */
	private $sitepress;

	/** @var WPML_Translation_Element_Factory */
	private $element_factory;

	/** @var array */
	private $active_languages;

	/**
	 * @param WPML_Translation_Element_Factory $element_factory
	 * @param SitePress $sitepress
	 */
	public function __construct( WPML_Translation_Element_Factory $element_factory, SitePress $sitepress ) {
		$this->element_factory = $element_factory;
		$this->sitepress = $sitepress;
		$this->active_languages = array_keys( $this->sitepress->get_active_languages() );
	}

	/**
	 * @param array $post_args
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function add_lang_info( $post_args, $post_id ) {
		$post_args['post_lang'] = $this->get_post_lang( $post_args, $post_id );

		return $post_args;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_by_lang( $args ) {
		$args['post_filter']['bool']['must'][] = array(
			'term' => array(
				'post_lang' => $this->get_query_lang(),
			),
		);

		return $args;
	}

	/**
	 * @param array $post_args
	 * @param int $post_id
	 *
	 * @return string
	 */
	private function get_post_lang( $post_args, $post_id ) {
		$post_element = $this->element_factory->create( $post_id, 'post' );
		$lang = $post_element->get_language_code();

		if ( ! in_array( $lang, $this->active_languages, true ) ) {
			$lang = 'en';

			$pattern = $this->build_lang_pattern();
			if ( isset( $post_args['guid'] ) && ! empty( $post_args['guid'] ) && preg_match( $pattern, $post_args['guid'], $match ) ) {
				$lang = end( $match );
			}
		}

		return $lang;
	}

	private function build_lang_pattern() {
		$pattern = $this->build_pattern_for_lang_as_directory();
		$pattern .= '|' . $this->build_pattern_for_lang_as_parameter();
		$pattern .= '|' . $this->build_pattern_for_lang_as_subdomain();

		return '/' . $pattern . '/';
	}

	private function get_query_lang() {
		$lang = $this->sitepress->get_current_language();
		if ( isset( $_GET['lang'] ) ) {
			if ( in_array( $_GET['lang'], $this->active_languages, true ) ) {
				$lang = $_GET['lang'];
			}
		}

		return $lang;
	}

	/**
	 * @return string
	 */
	private function build_pattern_for_lang_as_directory() {
		return sprintf( '\/(%s)\/', implode( '|', $this->active_languages ) );
	}

	/**
	 * @return string
	 */
	private function build_pattern_for_lang_as_parameter() {
		$lang_in_params = array();
		foreach ( $this->active_languages as $lang ) {
			$lang_in_params[] = 'lang=(' . $lang . ')';
		}

		return '(' . implode( '|', $lang_in_params ) . ')';
	}

	/**
	 * @return string
	 */
	private function build_pattern_for_lang_as_subdomain() {
		return sprintf( '\/\/(%s)\.', implode( '|', $this->active_languages ) );
	}
}