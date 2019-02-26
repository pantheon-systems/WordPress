<?php

class WPML_TM_REST_ATE_API_Factory extends WPML_REST_Factory_Loader {

	public function create() {
		$endpoints = new WPML_TM_ATE_AMS_Endpoints();
		$http      = new WP_Http();
		$auth      = new WPML_TM_ATE_Authentication();
		$api       = new WPML_TM_ATE_API( $http, $auth, $endpoints );

		return new WPML_TM_REST_ATE_API( $api );
	}
}