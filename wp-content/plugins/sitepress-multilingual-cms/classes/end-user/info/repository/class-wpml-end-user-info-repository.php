<?php

interface WPML_End_User_Info_Repository {
	/**
	 * @return mixed
	 */
	public function get_data();

	/**
	 * @return string
	 */
	public function get_data_id();
}
