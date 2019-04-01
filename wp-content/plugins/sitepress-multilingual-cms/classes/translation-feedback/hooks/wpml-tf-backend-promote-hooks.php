<?php

/**
 * Class WPML_TF_Backend_Promote_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Promote_Hooks implements IWPML_Action {

	const WPML_START_VERSION_KEY = 'wpml_start_version';

	/** @var WPML_TF_Promote_Notices $promote_notices */
	private $promote_notices;

	/** @var WPML_TF_Translation_Service $translation_service */
	private $translation_service;

	/** @var bool $is_setup_complete */
	private $is_setup_complete;

	/**
	 * WPML_TF_Backend_Promote_Hooks constructor.
	 *
	 * @param WPML_TF_Promote_Notices          $promote_notices
	 * @param bool                             $is_setup_complete
	 * @param WPML_TF_Translation_Service $translation_service
	 */
	public function __construct(
		WPML_TF_Promote_Notices $promote_notices,
		$is_setup_complete,
		WPML_TF_Translation_Service $translation_service
	) {
		$this->promote_notices     = $promote_notices;
		$this->is_setup_complete   = $is_setup_complete;
		$this->translation_service = $translation_service;
	}

	public function add_hooks() {
		if ( $this->translation_service->allows_translation_feedback() ) {
			if ( $this->is_setup_complete && get_option( self::WPML_START_VERSION_KEY )	) {
				add_action( 'wpml_pro_translation_completed', array( $this, 'add_notice_for_manager_on_job_completed' ), 10, 3 );
			}
		}

		if ( ! $this->is_setup_complete ) {
			update_option( self::WPML_START_VERSION_KEY, ICL_SITEPRESS_VERSION );
		}
	}

	/**
	 * @param int      $new_post_id
	 * @param array    $fields
	 * @param stdClass $job
	 */
	public function add_notice_for_manager_on_job_completed( $new_post_id, $fields, $job ){
		$this->promote_notices->show_notice_for_new_site( (int) $job->manager_id );
	}
}
