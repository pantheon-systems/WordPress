<?php

class WPML_Language_Domains {

	private $domains;

	public function __construct(
		SitePress $sitepress,
		WPML_URL_Converter_Url_Helper $converter_url_helper
	) {
		$this->domains = $sitepress->get_setting( 'language_domains' );

		$this->domains[ $sitepress->get_default_language() ] = wpml_parse_url( $converter_url_helper->get_abs_home(), PHP_URL_HOST );
	}

	public function get( $lang ) {
		return $this->domains[ $lang ];
	}

}