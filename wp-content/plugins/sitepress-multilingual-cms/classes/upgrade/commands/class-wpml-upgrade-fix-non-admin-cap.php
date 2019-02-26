<?php

class WPML_Upgrade_Fix_Non_Admin_With_Admin_Cap implements IWPML_Upgrade_Command {

	private $results = array();

	/**
	 * @return bool|void
	 */
	public function run_admin() {
		$user = new WP_User( 'admin' );

		if( $user->exists() && ! is_super_admin( $user->get( 'ID' ) ) ) {
			$wpml_capabilities = array_keys( wpml_get_capabilities() );
			foreach( $wpml_capabilities as $capability ) {
				$user->remove_cap( $capability );
			}
		}

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
		return $this->results;
	}
}