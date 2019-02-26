<?php

class WPML_End_User_Info_WP_User implements WPML_End_User_Info {
	/** @var int */
	private $wp_user_id;

	/** @var  bool */
	private $is_end_user;

	/**
	 * @param int $wp_user_id
	 * @param bool $is_end_user
	 */
	public function __construct( $wp_user_id, $is_end_user ) {
		$this->wp_user_id  = (int) $wp_user_id;
		$this->is_end_user = (bool) $is_end_user;
	}

	/**
	 * @return int
	 */
	public function get_wp_user_id() {
		return $this->wp_user_id;
	}

	/**
	 * @return bool
	 */
	public function is_end_user() {
		return $this->is_end_user;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return array(
			'wp_user_id' => $this->get_wp_user_id(),
			'is_end_user' => $this->is_end_user(),
		);
	}
}
