<?php

class WPML_TM_ICL20_Migrate_Remote {
	private $container;
	private $progress;

	/**
	 * WPML_TM_ICL20 constructor.
	 *
	 * @param WPML_TM_ICL20_Migration_Progress  $progress
	 * @param WPML_TM_ICL20_Migration_Container $container
	 */
	public function __construct(
		WPML_TM_ICL20_Migration_Progress $progress,
		WPML_TM_ICL20_Migration_Container $container
	) {
		$this->progress  = $progress;
		$this->container = $container;
	}

	/**
	 * @param string $ts_accesskey
	 * @param int    $ts_id
	 *
	 * @return bool
	 *
	 * Note: `ts_id` (aka `website_id`) = `website_id`
	 *
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/icldev-2322
	 */
	public function acknowledge_icl( $ts_id, $ts_accesskey ) {
		$result = false;
		try {
			$result = $this->container->get_acknowledge()->acknowledge_icl( $ts_id, $ts_accesskey );
		} catch ( WPML_TM_ICL20MigrationException $ex ) {
			$this->progress->log_failed_attempt( __METHOD__ . ' - ' . $ex->getCode() . ': ' . $ex->getMessage() );
		}

		if ( $result ) {
			$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_ICL_ACK, true );

			return $result;
		}

		$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_ICL_ACK, false );

		return false;
	}

	/**
	 * @param string $ts_accesskey
	 * @param int    $ts_id
	 *
	 * @return string|null
	 *
	 * Note: `ts_id` (aka `website_id`) = `website_id`
	 *
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/icldev-2285
	 */
	public function get_token( $ts_id, $ts_accesskey ) {
		$token = null;
		try {
			$token = $this->container->get_token()->get_token( $ts_id, $ts_accesskey );
		} catch ( WPML_TM_ICL20MigrationException $ex ) {
			$this->progress->log_failed_attempt( __METHOD__ . ' - ' . $ex->getCode() . ': ' . $ex->getMessage() );
		}

		if ( null !== $token ) {
			$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_TOKEN, $token );

			return $token;
		}

		$this->progress->set_completed_step( 'token', false );

		return null;
	}

	/**
	 * @param int    $project_id
	 * @param string $access_key
	 * @param string $new_token
	 *
	 * @return bool|null
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/tsapi-887
	 *
	 */
	public function migrate_project( $project_id, $access_key, $new_token ) {
		$migrate = null;
		try {
			$migrate = $this->container->get_project()->migrate( $project_id, $access_key, $new_token );
		} catch ( WPML_TM_ICL20MigrationException $ex ) {
			$this->progress->log_failed_attempt( __METHOD__ . ' - ' . $ex->getCode() . ': ' . $ex->getMessage() );
		}

		if ( $migrate ) {
			$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_REMOTE_PROJECT, true );

			return $migrate;
		}

		$this->progress->set_completed_step( WPML_TM_ICL20_Migration_Progress::STEP_MIGRATE_REMOTE_PROJECT, false );

		return false;
	}

	/**
	 * @param int    $project_id
	 * @param string $access_key
	 *
	 * @return bool
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/tsapi-887
	 *
	 */
	public function migrate_project_rollback( $project_id, $access_key ) {
		$result = false;
		try {
			$result = $this->container->get_project()->rollback_migration( $project_id, $access_key );
		} catch ( WPML_TM_ICL20MigrationException $ex ) {
			$this->progress->log_failed_attempt( __METHOD__ . ' - ' . $ex->getCode() . ': ' . $ex->getMessage() );
		}

		return $result;
	}
}