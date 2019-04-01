<?php

interface IWPML_TM_Admin_Section {

	/**
	 * @return string
	 */
	public function get_slug();

	/**
	 * @return string|array
	 */
	public function get_capabilities();

	/**
	 * @return string
	 */
	public function get_caption();

	/**
	 * @return callable
	 */
	public function get_callback();

	/**
	 * @return bool
	 */
	public function is_visible();
}