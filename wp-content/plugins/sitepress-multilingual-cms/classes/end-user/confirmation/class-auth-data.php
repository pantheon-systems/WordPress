<?php

class WPML_End_User_Confirmation_Auth_Data {
	/** @var string */
	private $site_key;

	/** @var int */
	private $user_id;

	/**
	 * @param string $site_key
	 * @param int $user_id
	 */
	public function __construct( $site_key, $user_id ) {
		$this->site_key = $site_key;
		$this->user_id  = (int) $user_id;
	}

	/**
	 * @return string
	 */
	public function get_site_key() {
		return $this->site_key;
	}

	/**
	 * @return int
	 */
	public function get_user_id() {
		return $this->user_id;
	}
}