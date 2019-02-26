<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Factory {
	/**
	 * @var WPML_TM_ICL20_Migration_Notices
	 */
	private $notices;
	/**
	 * @var WPML_TM_ICL20_Migration_Progress
	 */
	private $progress;
	/**
	 * @var WPML_TM_ICL20_Migration_Status
	 */
	private $status;
	/**
	 * @var WPML_TP_API
	 */
	private $tp_api;
	/**
	 * @var WP_Http
	 */
	private $wp_http;

	/**
	 * @return WPML_TM_ICL20_Migration_AJAX
	 */
	public function create_ajax() {
		return new WPML_TM_ICL20_Migration_AJAX( $this->create_progress() );
	}

	/**
	 * @return WPML_TM_ICL20_Migration_Locks
	 */
	public function create_locks() {
		return new WPML_TM_ICL20_Migration_Locks( $this->create_progress() );
	}

	/**
	 * @return WPML_TM_ICL20_Migration_Progress
	 */
	public function create_progress() {
		if ( null === $this->progress ) {
			$this->progress = new WPML_TM_ICL20_Migration_Progress();
		}

		return $this->progress;
	}

	/**
	 * @return WPML_TM_ICL20_Migrate
	 */
	public function create_migration() {
		return new WPML_TM_ICL20_Migrate( $this->create_progress(),
		                                  $this->create_status(),
		                                  $this->get_remote_migration(),
		                                  $this->get_local_migration(),
		                                  $this->get_tp_api() );
	}

	/**
	 * @return WPML_TM_ICL20_Migration_Status
	 */
	public function create_status() {
		if ( null === $this->status ) {
			$current_service = $this->get_tp_api()->get_current_service();
			$this->status    = new WPML_TM_ICL20_Migration_Status( $current_service );
		}

		return $this->status;
	}

	/**
	 * @return WPML_TM_ICL20_Migrate_Remote
	 */
	private function get_remote_migration() {
		$http    = $this->get_wp_http();
		$token   = new WPML_TM_ICL20_Token( $http, ICL_API_ENDPOINT );
		$project = new WPML_TM_ICL20_Project( $http, OTG_TRANSLATION_PROXY_URL );
		$ack     = new WPML_TM_ICL20_Acknowledge( $http, ICL_API_ENDPOINT );

		$container = new WPML_TM_ICL20_Migration_Container( $token, $project, $ack );

		return new WPML_TM_ICL20_Migrate_Remote( $this->create_progress(), $container );
	}

	/**
	 * @return WPML_TM_ICL20_Migrate_Local
	 */
	private function get_local_migration() {
		return new WPML_TM_ICL20_Migrate_Local( $this->get_tp_api(),
		                                        $this->create_status(),
		                                        $this->create_progress(),
		                                        $this->get_sitepress() );
	}

	/**
	 * @return WPML_TP_API
	 */
	private function get_tp_api() {
		if ( null === $this->tp_api ) {
			$wpml_tp_communication = new WPML_TP_Communication( OTG_TRANSLATION_PROXY_URL, $this->get_wp_http() );
			$this->tp_api          = new WPML_TP_API( $wpml_tp_communication, '1.1', new WPML_TM_Log() );
		}

		return $this->tp_api;
	}

	/**
	 * @return WP_Http
	 */
	private function get_wp_http() {
		if ( null === $this->wp_http ) {
			$this->wp_http = new WP_Http();
		}

		return $this->wp_http;
	}

	/**
	 * @return SitePress
	 */
	private function get_sitepress() {
		global $sitepress;

		return $sitepress;
	}

	/**
	 * @return WPML_TM_ICL20_Migration_Notices
	 */
	public function create_notices() {
		if ( null === $this->notices ) {
			$this->notices = new WPML_TM_ICL20_Migration_Notices( $this->create_progress(), wpml_get_admin_notices() );
		}

		return $this->notices;
	}

	/**
	 * @return WPML_TM_ICL20_Migration_Support
	 */
	public function create_ui_support() {
		$template_paths = array(
			WPML_TM_PATH . '/templates/support/icl20/migration/',
		);

		$template_loader  = new WPML_Twig_Template_Loader( $template_paths );
		$template_service = $template_loader->get_template();

		return new WPML_TM_ICL20_Migration_Support( $template_service, $this->create_progress(), $this->can_rollback() );
	}

	/**
	 * @return bool
	 */
	public function can_rollback() {
		return defined( 'WP_DEBUG' )
		       && defined( 'WPML_TP_ICL_20_ENABLE_ROLLBACK' )
		       && WP_DEBUG
		       && WPML_TP_ICL_20_ENABLE_ROLLBACK;
	}

}