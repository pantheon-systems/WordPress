<?php

class WPML_End_User_Info_Site implements WPML_End_User_Info {
	/** @var  string */
	private $site_url;

	/** @var  int */
	private $wpml_client_id;

	/** @var  string */
	private $site_key;

	/**
	 * @param string $site_url
	 * @param int $wpml_client_id
	 * @param string $site_key
	 */
	public function __construct( $site_url, $wpml_client_id, $site_key ) {
		$this->site_url       = $site_url;
		$this->wpml_client_id = $wpml_client_id;
		$this->site_key       = $site_key;
	}

	/**
	 * @return string
	 */
	public function get_site_url() {
		return $this->site_url;
	}

	/**
	 * @return int
	 */
	public function get_wpml_client_id() {
		return $this->wpml_client_id;
	}

	/**
	 * @return string
	 */
	public function get_site_key() {
		return $this->site_key;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return array(
			'site_url'          => $this->get_site_url(),
			'wpml_client_id'    => $this->get_wpml_client_id(),
			'site_key'          => $this->get_site_key(),
		);
	}
}
