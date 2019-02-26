<?php

class WPML_Language_Collection {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var array $languages */
	private $languages = array();

	/**
	 * WPML_Language_Collection constructor.
	 *
	 * @param SitePress $sitepress
	 * @param array $initial_languages Array of language codes
	 */
	public function __construct( SitePress $sitepress, $initial_languages = array() ) {
		$this->sitepress = $sitepress;
		foreach ( $initial_languages as $lang ) {
			$this->add( $lang );
		}
	}

	public function add( $code ) {
		if ( ! isset( $this->languages[ $code ] ) ) {
			$language = new WPML_Language( $this->sitepress, $code );
			if ( $language->is_valid() ) {
				$this->languages[ $code ] = $language;
			}
		}
	}

	public function get( $code ) {
		if( ! isset( $this->languages[ $code ] ) ) {
			$this->add( $code );
		}
		return $this->languages[ $code ];
	}

	public function get_codes() {
		return array_keys( $this->languages );
	}
}