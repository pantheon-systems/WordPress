<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Progress {
	const MAX_AUTOMATIC_ATTEMPTS                   = 5;
	const OPTION_KEY_USER_CONFIRMED                = '_wpml_icl20_migration_user_confirmed';
	const OPTION_KEY_MIGRATION_ATTEMPTS            = '_wpml_icl20_migration_attempts';
	const OPTION_KEY_MIGRATION_LAST_ATTEMPT        = '_wpml_icl20_migration_last_attempt';
	const OPTION_KEY_MIGRATION_LAST_ERROR          = '_wpml_icl20_migration_last_error';
	const OPTION_KEY_MIGRATION_LOCAL_PROJECT_INDEX = '_wpml_icl20_migration_local_project_index';
	const OPTION_KEY_MIGRATION_LOCKED              = '_wpml_icl20_migration_locked';
	const OPTION_KEY_MIGRATION_REQUIRED            = '_wpml_icl20_migration_required';
	const OPTION_KEY_MIGRATION_STEPS               = '_wpml_icl20_migration_step_%s';
	const STEP_ICL_ACK                             = 'icl_ack';
	const STEP_MIGRATE_JOBS_DOCUMENTS              = 'migrate_jobs_doc';
	const STEP_MIGRATE_JOBS_STRINGS                = 'migrate_jobs_strings';
	const STEP_MIGRATE_LOCAL_PROJECT               = 'migrate_local_project';
	const STEP_MIGRATE_LOCAL_SERVICE               = 'migrate_local_service';
	const STEP_MIGRATE_REMOTE_PROJECT              = 'migrate_remote_project';
	const STEP_TOKEN                               = 'token';
	const STEP_FAILED                              = 'failed';
	const STEP_DONE                                = 'done';
	const VALUE_YES                                = 'yes';
	const VALUE_NO                                 = 'no';

	/**
	 * @var array
	 */
	private $steps;

	/**
	 * WPML_TM_ICL20_Migration_Progress constructor.
	 */
	public function __construct() {
		$this->steps = array(
			self::STEP_TOKEN,
			self::STEP_MIGRATE_REMOTE_PROJECT,
			self::STEP_ICL_ACK,
			self::STEP_MIGRATE_LOCAL_SERVICE,
			self::STEP_MIGRATE_LOCAL_PROJECT,
			self::STEP_MIGRATE_JOBS_DOCUMENTS,
			self::STEP_MIGRATE_JOBS_STRINGS,
		);
	}

	/**
	 * @param string $step
	 *
	 * @return string|null
	 */
	public function get_completed_step( $step ) {
		return get_option( sprintf( self::OPTION_KEY_MIGRATION_STEPS, $step ), self::STEP_FAILED );
	}

	/**
	 * @return int|null
	 */
	public function get_last_attempt_timestamp() {
		return get_option( self::OPTION_KEY_MIGRATION_LAST_ATTEMPT, null );
	}

	/**
	 * @return string|null
	 */
	public function get_last_migration_error() {
		return get_option( self::OPTION_KEY_MIGRATION_LAST_ERROR, null );
	}

	/**
	 * @return string|null
	 */
	public function get_project_to_migrate() {
		return get_option( self::OPTION_KEY_MIGRATION_LOCAL_PROJECT_INDEX, null );
	}

	/**
	 * @return array
	 */
	public function get_steps() {
		return $this->steps;
	}

	/**
	 * @param string $message
	 */
	public function log_failed_attempt( $message ) {
		$this->update_last_error( $message );
	}

	/**
	 * @param string $message
	 */
	private function update_last_error( $message ) {
		update_option( self::OPTION_KEY_MIGRATION_LAST_ERROR, $message, false );
	}

	/**
	 * @return bool
	 */
	public function is_migration_incomplete() {
		return $this->get_current_attempts_count() > 0;
	}

	/**
	 * @param string      $step
	 * @param string|bool $value
	 */
	public function set_completed_step( $step, $value ) {
		$step_value = $value;
		if ( is_bool( $value ) ) {
			$step_value = $value ? self::STEP_DONE : self::STEP_FAILED;
		}
		update_option( sprintf( self::OPTION_KEY_MIGRATION_STEPS, $step ), $step_value, false );
	}

	/**
	 * @return bool
	 */
	public function has_migration_ever_started() {
		$this->requires_migration();
		$notoptions = wp_cache_get( 'notoptions', 'options' );
		if ( isset( $notoptions[ self::OPTION_KEY_MIGRATION_REQUIRED ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * It will mark the migration as done
	 */
	public function set_migration_done() {
		update_option( self::OPTION_KEY_MIGRATION_REQUIRED, self::VALUE_NO, false );
		$this->clear_temporary_options();
	}

	/**
	 * It will remove all the temporary options used to store the status of the migration
	 */
	public function clear_temporary_options() {
		$options = array(
			self::OPTION_KEY_MIGRATION_LOCKED,
			self::OPTION_KEY_MIGRATION_LAST_ERROR,
			self::OPTION_KEY_MIGRATION_ATTEMPTS,
			self::OPTION_KEY_MIGRATION_LAST_ATTEMPT,
			self::OPTION_KEY_MIGRATION_LOCAL_PROJECT_INDEX,
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}

		foreach ( $this->steps as $step ) {
			delete_option( sprintf( self::OPTION_KEY_MIGRATION_STEPS, $step ) );
		}
	}

	/**
	 * @return bool
	 */
	public function is_migration_done() {
		$result = true;
		if ( $this->requires_migration() ) {
			foreach ( $this->steps as $step ) {
				if ( self::STEP_DONE !== get_option( sprintf( self::OPTION_KEY_MIGRATION_STEPS, $step ),
				                                     self::STEP_FAILED ) ) {
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	public function requires_migration() {
		return self::VALUE_YES === get_option( self::OPTION_KEY_MIGRATION_REQUIRED, self::VALUE_NO );
	}

	/**
	 * Sets the migration as started so to know, in the next attempts, if the migration was partial or never started.
	 */
	public function set_migration_started() {
		update_option( self::OPTION_KEY_MIGRATION_REQUIRED, self::VALUE_YES, false );
		$this->increase_attempts_count();
	}

	/**
	 * It will increases on every migration attempt
	 */
	private function increase_attempts_count() {
		update_option( self::OPTION_KEY_MIGRATION_ATTEMPTS,
		               $this->get_current_attempts_count() + 1,
		               false );
		update_option( self::OPTION_KEY_MIGRATION_LAST_ATTEMPT,
		               time(),
		               false );
		if ( $this->has_too_many_automatic_attempts() ) {
			$this->block_next_automatic_attempts();
		}
	}

	/**
	 * @return int
	 */
	public function get_current_attempts_count() {
		return (int) get_option( self::OPTION_KEY_MIGRATION_ATTEMPTS, 0 );
	}

	/**
	 * @return bool
	 */
	public function has_too_many_automatic_attempts() {
		return $this->get_current_attempts_count() >= self::MAX_AUTOMATIC_ATTEMPTS
		       || $this->are_next_automatic_attempts_locked();
	}

	/**
	 * Used when too many attempts are made
	 */
	private function block_next_automatic_attempts() {
		update_option( self::OPTION_KEY_MIGRATION_LOCKED, self::VALUE_YES, false );
	}

	/**
	 * @return bool
	 */
	public function are_next_automatic_attempts_locked() {
		return self::VALUE_YES === get_option( self::OPTION_KEY_MIGRATION_LOCKED, self::VALUE_NO );
	}

	/**
	 * @param string $old_index
	 */
	public function set_project_to_migrate( $old_index ) {
		update_option( self::OPTION_KEY_MIGRATION_LOCAL_PROJECT_INDEX, $old_index, false );
	}

	/**
	 * @return bool
	 */
	public function get_user_confirmed() {
		return self::VALUE_YES === get_option( self::OPTION_KEY_USER_CONFIRMED, self::VALUE_NO );
	}

	/**
	 * User as an opt-in action from the user before starting the migration
	 */
	public function set_user_confirmed() {
		update_option( self::OPTION_KEY_USER_CONFIRMED, self::VALUE_YES, false );
	}
}