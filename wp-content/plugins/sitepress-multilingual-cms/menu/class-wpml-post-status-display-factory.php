<?php

class WPML_Post_Status_Display_Factory {

	/** @var SitePress $sitepress */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function create() {
		return new WPML_Post_Status_Display( $this->sitepress->get_active_languages() );
	}
}
