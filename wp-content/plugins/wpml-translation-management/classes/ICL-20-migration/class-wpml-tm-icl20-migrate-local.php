<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migrate_Local {
	const JOBS_TYPES_DOCUMENTS = 'icl_translation_status';
	const JOBS_TYPES_STRINGS   = 'icl_string_translations';
	private $progress;
	private $sitepress;
	private $status;
	private $tp_api;

	/**
	 * WPML_TM_ICL20 constructor.
	 *
	 * @param WPML_TP_API                      $tp_api
	 * @param WPML_TM_ICL20_Migration_Status   $status
	 * @param WPML_TM_ICL20_Migration_Progress $progress
	 * @param SitePress                        $sitepress
	 *
	 * @internal param SitePress $sitepress
	 */
	public function __construct(
		WPML_TP_API $tp_api,
		WPML_TM_ICL20_Migration_Status $status,
		WPML_TM_ICL20_Migration_Progress $progress,
		SitePress $sitepress
	) {
		$this->tp_api    = $tp_api;
		$this->status    = $status;
		$this->progress  = $progress;
		$this->sitepress = $sitepress;
	}

	public function migrate_jobs( $table ) {
		$result = false;

		$current_service = $this->tp_api->get_current_service();

		if ( $this->status->get_ICL_20_TS_ID() === $current_service->id ) {
			$step = null;

			if ( self::JOBS_TYPES_DOCUMENTS === $table ) {
				$step = WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_JOBS_DOCUMENTS;
			}
			if ( self::JOBS_TYPES_STRINGS === $table ) {
				$step = WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_JOBS_STRINGS;
			}

			if ( null !== $step ) {
				$update = $this->update_table( $table );

				$result = false !== $update;
				$this->progress->set_completed_step( $step, $result );
			} else {
				$this->progress->log_failed_attempt( __METHOD__ . ' - ' . 'Wrong "' . $table . '"' );
			}
		}

		return $result;
	}

	/**
	 * @param $table
	 *
	 * @return false|int
	 */
	private function update_table( $table ) {
		$wpdb = $this->sitepress->get_wpdb();

		$update = $wpdb->update( $wpdb->prefix . $table,
		                         array(
			                         'translator_id'       => 0,
			                         'translation_service' => $this->status->get_ICL_20_TS_ID(),
		                         ),
		                         array(
			                         'translation_service' => $this->status->get_ICL_LEGACY_TS_ID(),
		                         ),
		                         array( '%d', '%d' ),
		                         array( '%d' ) );

		if ( false === $update ) {
			$this->progress->log_failed_attempt( __METHOD__ . ' - ' . $wpdb->last_error );
		}

		return $update;
	}

	public function migrate_project() {
		$old_index = $this->progress->get_project_to_migrate();
		//icl_translation_projects
		$migrated = false;

		if ( $old_index ) {
			$current_service = $this->tp_api->get_current_service();

			if ( $current_service && null !== $current_service->id ) {
				$new_index = md5( $current_service->id . serialize( $current_service->custom_fields_data ) );

				$migrated = $this->update_project_index( $old_index, $new_index );
			}
		}

		$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_LOCAL_PROJECT, $migrated );

		return $migrated;
	}

	private function update_project_index( $old_service_index, $new_service_index ) {
		$updated = false;

		$projects = $this->sitepress->get_setting( 'icl_translation_projects', null );

		$old_index_exists          = array_key_exists( $old_service_index, $projects );
		$new_index_does_not_exists = ! array_key_exists( $new_service_index, $projects );

		if ( $projects && $old_index_exists && $new_index_does_not_exists ) {
			$project = $projects[ $old_service_index ];

			$projects[ $new_service_index ] = $project;
			unset( $projects[ $old_service_index ] );

			$this->sitepress->set_setting( 'icl_translation_projects', $projects, true );
			$updated = true;
		}

		if ( false === $updated ) {
			$error = '';
			if ( ! $old_index_exists ) {
				$error = 'The old project does not exists';
			}
			if ( ! $new_index_does_not_exists ) {
				$error = 'The new project index already exists';
			}
			$this->progress->log_failed_attempt( __METHOD__ . ' - ' . $error );
		}

		return $updated;
	}

	public function migrate_service( $new_token ) {
		$current_service = $this->tp_api->get_current_service();

		$migrated = false;

		if ( $current_service ) {
			$old_index = md5( $current_service->id . serialize( $current_service->custom_fields_data ) );
			$this->progress->set_project_to_migrate( $old_index );

			$icl20_service_id = $this->status->get_ICL_20_TS_ID();
			$this->tp_api->select_service( $icl20_service_id,
			                               array(
				                               'api_token' => $new_token,
			                               ) );
			$active_service = $this->tp_api->get_current_service();
			$migrated       = $active_service->id === $icl20_service_id;
		}

		$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_LOCAL_SERVICE, $migrated );

		return $migrated;
	}

	public function rollback_service() {
		$current_service = $this->tp_api->get_current_service();

		$rolled_back = false;

		if ( $current_service ) {
			$this->tp_api->select_service( $this->status->get_ICL_LEGACY_TS_ID() );
			$active_service = $this->tp_api->get_current_service();
			$rolled_back    = $active_service->id === $this->status->get_ICL_LEGACY_TS_ID();
		}
		$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_LOCAL_SERVICE, false );

		return $rolled_back;
	}
}
