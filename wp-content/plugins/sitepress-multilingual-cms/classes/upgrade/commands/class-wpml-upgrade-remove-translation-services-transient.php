<?php

class WPML_Upgrade_Remove_Translation_Services_Transient implements IWPML_Upgrade_Command {

	/**
	 * @return bool|void
	 */
	public function run_admin() {
		delete_transient( 'wpml_translation_service_list' );
		return true;
	}

	/**
	 * @return bool
	 */
	public function run_ajax() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function run_frontend() {
		return false;
	}

	/**
	 * @return null
	 */
	public function get_results() {
		return array();
	}
}