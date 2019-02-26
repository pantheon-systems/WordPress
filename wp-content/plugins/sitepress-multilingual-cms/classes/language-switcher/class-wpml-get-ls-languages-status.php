<?php

class WPML_Get_LS_Languages_Status {
	private static $the_instance;

	private $in_get_ls_languages = false;

	public function is_getting_ls_languages() {
		return $this->in_get_ls_languages;
	}

	public function start() {
		$this->in_get_ls_languages = true;
	}

	public function end() {
		$this->in_get_ls_languages = false;
	}

	/**
	 * @return WPML_Get_LS_Languages_Status
	 */
	public static function get_instance() {
		if ( ! self::$the_instance ) {
			self::$the_instance = new WPML_Get_LS_Languages_Status();
		}
		return self::$the_instance;
	}

	/**
	 * @param WPML_Get_LS_Languages_Status $instance
	 */
	public static function set_instance( $instance ) {
		self::$the_instance = $instance;
	}
}