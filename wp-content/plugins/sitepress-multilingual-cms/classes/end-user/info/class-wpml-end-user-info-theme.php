<?php

class WPML_End_User_Info_Theme implements WPML_End_User_Info {
	/** @var  string */
	private $theme_name;

	/** @var  string */
	private $parent_theme_name;

	/** @var string */
	private $author;

	/**
	 * @param string $theme_name
	 * @param string $parent_theme_name
	 */
	public function __construct( $theme_name, $parent_theme_name = null ) {
		$this->theme_name        = $theme_name;

		if ( empty( $parent_theme_name ) ) {
			$parent_theme_name = null;
		}
		$this->parent_theme_name = $parent_theme_name;
	}

	/**
	 * @return string
	 */
	public function get_theme_name() {
		return $this->theme_name;
	}

	/**
	 * @return string
	 */
	public function get_parent_theme_name() {
		return $this->parent_theme_name;
	}

	/**
	 * @return string
	 */
	public function get_author() {
		return $this->author;
	}

	/**
	 * @param string $author
	 */
	public function set_author( $author ) {
		$this->author = $author;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return array(
			'theme_name'        => $this->get_theme_name(),
			'parent_theme_name' => $this->get_parent_theme_name(),
			'author'            => $this->get_author(),
		);
	}
}
