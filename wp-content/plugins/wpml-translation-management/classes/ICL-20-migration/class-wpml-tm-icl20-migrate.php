<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migrate {
	private $local_migration;
	private $progress;
	private $remote_migration;
	private $status;
	private $tp_api;

	public function __construct(
		WPML_TM_ICL20_Migration_Progress $progress,
		WPML_TM_ICL20_Migration_Status $status,
		WPML_TM_ICL20_Migrate_Remote $remote_migration,
		WPML_TM_ICL20_Migrate_Local $local_migration,
		WPML_TP_API $tp_api
	) {
		$this->progress         = $progress;
		$this->status           = $status;
		$this->remote_migration = $remote_migration;
		$this->local_migration  = $local_migration;
		$this->tp_api           = $tp_api;
	}

	public function migrate_project_rollback() {
		if ( ! $this->status->has_active_legacy_icl() ) {
			return false;
		}

		$project = $this->tp_api->get_current_project();
		$token   = $this->get_token( $project );
		if ( $token ) {
			return $this->remote_migration->migrate_project_rollback( $project->id, $project->access_key );
		}

		return false;
	}

	public function run() {
		$this->progress->set_migration_started();

		$project = $this->tp_api->get_current_project();
		$token   = $project ? $this->get_token( $project ) : null;

		if ( (bool) $token
		     && $this->migrate_project( $project, $token )
		     && $this->acknowledge_icl( $project )
		     && $this->migrate_local_service( $token )
		     && $this->migrate_local_project()
		     && $this->migrate_local_jobs( WPML_TM_ICL20_Migrate_Local::JOBS_TYPES_DOCUMENTS,
		                                   WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_JOBS_DOCUMENTS )
		     && $this->migrate_local_jobs( WPML_TM_ICL20_Migrate_Local::JOBS_TYPES_STRINGS,
		                                   WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_JOBS_STRINGS ) ) {

			$this->progress->set_migration_done();

			return true;
		}

		return false;
	}

	/**
	 * @param $project
	 *
	 * @return string
	 */
	private function get_token( $project ) {
		$token = $this->progress->get_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_TOKEN );
		if ( WPML_TM_ICL20_Migration_Progress::STEP_FAILED === $token ) {
			$token = $this->remote_migration->get_token( $project->ts_id, $project->ts_access_key );
		}

		return $token;
	}

	/**
	 * @param $project
	 * @param $token
	 *
	 * @return bool
	 */
	private function migrate_project( $project, $token ) {
		$project_migrated = $this->progress->get_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_REMOTE_PROJECT );
		if ( WPML_TM_ICL20_Migration_Progress::STEP_FAILED === $project_migrated ) {
			$project_migrated = $this->remote_migration->migrate_project( $project->id, $project->access_key, $token );
		}

		return (bool) $project_migrated;
	}

	/**
	 * @param $project
	 *
	 * @return bool
	 */
	private function acknowledge_icl( $project ) {
		$icl_acknowledged = $this->progress->get_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_ICL_ACK );
		if ( WPML_TM_ICL20_Migration_Progress::STEP_FAILED === $icl_acknowledged ) {
			$icl_acknowledged = $this->remote_migration->acknowledge_icl( $project->ts_id, $project->ts_access_key );
		}

		return (bool) $icl_acknowledged;
	}

	/**
	 * @param $token
	 *
	 * @return bool
	 */
	private function migrate_local_service( $token ) {
		$service_migrated = $this->progress->get_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_LOCAL_SERVICE );
		if ( WPML_TM_ICL20_Migration_Progress::STEP_DONE === $service_migrated ) {
			$current_service  = $this->tp_api->get_current_service();
			$service_migrated = $current_service && $this->status->get_ICL_20_TS_ID() === $current_service->id;
		}
		if ( WPML_TM_ICL20_Migration_Progress::STEP_FAILED === $service_migrated ) {
			$service_migrated = $this->local_migration->migrate_service( $token );
		}

		return (bool) $service_migrated;
	}

	/**
	 * @return bool
	 */
	private function migrate_local_project() {
		$project_migrated = $this->progress->get_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_LOCAL_PROJECT );
		if ( WPML_TM_ICL20_Migration_Progress::STEP_FAILED === $project_migrated ) {
			$project_migrated = $this->local_migration->migrate_project();
		}

		return (bool) $project_migrated;
	}

	/**
	 * @param string $table
	 * @param string $step
	 *
	 * @return bool
	 */
	private function migrate_local_jobs( $table, $step ) {
		$job_migrated = $this->progress->get_completed_step( $step );

		if ( WPML_TM_ICL20_Migration_Progress::STEP_FAILED === $job_migrated ) {
			$job_migrated = $this->local_migration->migrate_jobs( $table );
		}

		return (bool) $job_migrated;
	}
}