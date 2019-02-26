<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Jobs_Actions_Factory implements IWPML_Backend_Action_Loader {
	private $endpoints;
	private $auth;
	private $http;
	private $current_screen;

	/**
	 * @return IWPML_Action|IWPML_Action[]|null
	 */
	public function create() {
		if ( WPML_TM_ATE_Status::is_enabled() && $this->is_active() ) {
			$wp_http        = $this->get_http();
			$auth           = $this->get_auth();
			$endpoints      = $this->get_endpoints();
			$sitepress      = $this->get_sitepress();
			$current_screen = $this->get_current_screen();

			$ate_api   = new WPML_TM_ATE_API( $wp_http, $auth, $endpoints );
			$records   = new WPML_TM_ATE_Job_Records();
			$ate_jobs  = new WPML_TM_ATE_Jobs( $records );

			$translator_activation_records = new WPML_TM_AMS_Translator_Activation_Records( new WPML_WP_User_Factory() );

			return new WPML_TM_ATE_Jobs_Actions(
				$ate_api,
				$ate_jobs,
				$sitepress,
				$current_screen,
				$translator_activation_records
			);
		}

		return null;
	}

	/**
	 * @return WP_Http
	 */
	private function get_http() {
		if ( ! $this->http ) {
			$this->http = new WP_Http();
		}

		return $this->http;
	}

	/**
	 * @return WPML_TM_ATE_Authentication
	 */
	private function get_auth() {
		if ( ! $this->auth ) {
			$this->auth = new WPML_TM_ATE_Authentication();
		}

		return $this->auth;
	}

	/**
	 * @return WPML_TM_ATE_AMS_Endpoints
	 */
	private function get_endpoints() {
		if ( ! $this->endpoints ) {
			$this->endpoints = new WPML_TM_ATE_AMS_Endpoints();
		}

		return $this->endpoints;
	}

	private function is_active() {
		if ( ! WPML_TM_ATE_Status::is_active() ) {
			$wp_http   = $this->get_http();
			$auth      = $this->get_auth();
			$endpoints = $this->get_endpoints();

			$ams_api = new WPML_TM_AMS_API( $wp_http, $auth, $endpoints );

			try {
				$ams_api->get_status();

				return WPML_TM_ATE_Status::is_active();
			} catch ( Exception $ex ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return SitePress
	 */
	private function get_sitepress() {
		global $sitepress;

		return $sitepress;
	}

	private function get_current_screen() {
		if ( ! $this->current_screen ) {
			$this->current_screen = new WPML_Current_Screen();
		}

		return $this->current_screen;
	}
}