<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Scripts_Factory {
	private $ate;
	private $ams_api;
	private $auth;
	private $endpoints;
	private $http;
	private $strings;

	public function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'wpml_tm_translators_view_strings', array( $this, 'filter_translators_view_strings' ), 10, 2 );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function admin_enqueue_scripts() {
		$this->register_otgs_notices();

		wp_register_script( 'wpml-tm-settings',
		                    WPML_TM_URL . '/dist/js/settings/app.js',
		                    array(),
		                    WPML_TM_VERSION );
		wp_register_script( 'ate-translation-queue',
		                    WPML_TM_URL . '/dist/js/translationQueue/app.js',
		                    array(),
		                    false,
		                    true );
		wp_register_script( 'ate-translation-editor-classic',
			WPML_TM_URL . '/dist/js/ate-translation-editor-classic/app.js',
			array(),
			false,
			true );

		if ( WPML_TM_Page::is_settings() || WPML_TM_Page::is_tm_translators() ) {
			wp_enqueue_style( 'otgs-notices' );
			$this->localize_script( 'wpml-tm-settings' );
			wp_enqueue_script( 'wpml-tm-settings' );

			$this->create_ate()
			     ->init_hooks();
		}
		if ( WPML_TM_Page::is_translation_queue() && WPML_TM_ATE_Status::is_enabled() ) {
			$this->localize_script( 'ate-translation-queue' );
			wp_enqueue_script( 'ate-translation-queue' );
			wp_enqueue_script( 'ate-translation-editor-classic' );
			wp_enqueue_style( 'otgs-notices' );
		}
	}

	public function register_otgs_notices() {
		if ( ! wp_style_is( 'otgs-notices', 'registered' ) ) {
			wp_register_style( 'otgs-notices',
				ICL_PLUGIN_URL . '/res/css/otgs-notices.css',
				array( 'sitepress-style' ) );
		}
	}

	/**
	 * @param $handle
	 *
	 * @throws \InvalidArgumentException
	 */
	public function localize_script( $handle ) {
		$data = array(
			'hasATEEnabled' => WPML_TM_ATE_Status::is_enabled(),
			'restUrl'       => untrailingslashit( rest_url() ),
			'restNonce'     => wp_create_nonce( 'wp_rest' ),
			'ate'           => $this->create_ate()
			                        ->get_script_data(),
			'currentUser'   => null,
		);

		$current_user = wp_get_current_user();
		if ( $current_user && $current_user->ID > 0 ) {
			$data['currentUser'] = $current_user;
		}

		wp_localize_script( $handle, 'WPML_TM_SETTINGS', $data );
	}

	/**
	 * @return WPML_TM_MCS_ATE
	 * @throws \InvalidArgumentException
	 */
	public function create_ate() {
		if ( ! $this->ate ) {
			$this->ate = new WPML_TM_MCS_ATE( $this->get_authentication(),
			                                  $this->get_endpoints(),
			                                  $this->create_ate_strings() );
		}

		return $this->ate;
	}

	private function get_authentication() {
		if ( ! $this->auth ) {
			$this->auth = new WPML_TM_ATE_Authentication();
		}

		return $this->auth;
	}

	private function get_endpoints() {
		if ( ! $this->endpoints ) {
			$this->endpoints = new WPML_TM_ATE_AMS_Endpoints();
		}

		return $this->endpoints;
	}

	private function create_ate_strings() {
		if ( ! $this->strings ) {
			$this->strings = new WPML_TM_MCS_ATE_Strings( $this->get_authentication(), $this->get_endpoints() );
		}

		return $this->strings;
	}

	/**
	 * @param array $strings
	 * @param bool $all_users_have_subscription
	 *
	 * @return array
	 */
	public function filter_translators_view_strings( array $strings, $all_users_have_subscription ) {
		if ( WPML_TM_ATE_Status::is_enabled() ) {
			$strings['ate'] = $this->create_ate_strings()
			                       ->get_status_HTML(
				                       $this->get_ate_activation_status(),
				                       $all_users_have_subscription
			                       );
		}

		return $strings;
	}

	/**
	 * @return string
	 */
	private function get_ate_activation_status() {
		$status = $this->create_ate_strings()
		               ->get_status();
		if ( $status !== WPML_TM_ATE_Authentication::AMS_STATUS_ACTIVE ) {
			$status = $this->fetch_and_update_ate_activation_status();
		}

		return $status;
	}

	/**
	 * @return string
	 */
	private function fetch_and_update_ate_activation_status() {
		$this->create_ams_api()
		     ->get_status();
		$status = $this->create_ate_strings()
		               ->get_status();

		return $status;
	}

	/**
	 * @return WPML_TM_AMS_API
	 */
	private function create_ams_api() {
		if ( ! $this->ams_api ) {
			$this->ams_api = new WPML_TM_AMS_API( $this->get_http(),
			                                      $this->get_authentication(),
			                                      $this->get_endpoints() );
		}

		return $this->ams_api;
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
}
