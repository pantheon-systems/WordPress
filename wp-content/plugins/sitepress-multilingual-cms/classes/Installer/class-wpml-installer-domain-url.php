<?php

class WPML_Installer_Domain_URL {

	private $site_url_in_default_lang;

	public function __construct( $site_url_in_default_lang ) {
		$this->site_url_in_default_lang = $site_url_in_default_lang;
	}

	public function add_hooks() {
		add_filter( 'otgs_installer_site_url', array( $this, 'get_site_url_in_default_lang' ) );
	}

	public function get_site_url_in_default_lang() {
		return $this->site_url_in_default_lang;
	}
}