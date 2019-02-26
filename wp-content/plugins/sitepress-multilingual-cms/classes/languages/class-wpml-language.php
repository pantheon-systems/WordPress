<?php

class WPML_Language {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var string $code */
	private $code;

	private $lang_details;

	public function __construct( SitePress $sitepress, $code ) {
		$this->sitepress = $sitepress;
		$this->code = $code;
		$this->lang_details = $sitepress->get_language_details( $code );
	}

	public function is_valid() {
		return (bool) $this->lang_details;
	}

	public function get_code() {
		return $this->lang_details['code'];
	}

	public function get_display_name() {
		return $this->lang_details['display_name'];
	}

	public function get_flag_url() {
		return $this->sitepress->get_flag_url( $this->code );
	}

}