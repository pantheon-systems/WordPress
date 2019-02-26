<?php

class WPML_Language_Records {

	private $wpdb;

	private $languages;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function is_valid( $code ) {
		if ( ! $this->languages ) {
			$this->load();
		}

		return in_array( $code, $this->languages );
	}

	private function load() {
		$this->languages = $this->wpdb->get_col( "SELECT code FROM {$this->wpdb->prefix}icl_languages " );
	}
}
