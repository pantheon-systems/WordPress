<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Loader {
	/** @var WPML_TM_ICL20_Migration_Factory */
	private $factory;
	/** @var  WPML_TM_ICL20_Migration_Progress */
	private $progress;
	/** @var  WPML_TM_ICL20_Migration_Status */
	private $status;
	/** @var WPML_WP_API */
	private $wp_api;

	/**
	 * WPML_TM_ICL20_Migration_Loader constructor.
	 *
	 * @param WPML_WP_API                     $wp_api
	 * @param WPML_TM_ICL20_Migration_Factory $factory
	 */
	public function __construct( WPML_WP_API $wp_api, WPML_TM_ICL20_Migration_Factory $factory ) {
		$this->wp_api  = $wp_api;
		$this->factory = $factory;
	}

	/**
	 * This is the main method which deals with the whole logic for handling the migration
	 */
	public function run() {
		$this->status   = $this->factory->create_status();
		$this->progress = $this->factory->create_progress();

		$requires_migration = $this->requires_migration();

		$notices = $this->factory->create_notices();
		$notices->clear_migration_required();
		$notices->run( $requires_migration );

		if ( $requires_migration ) {
			if ( ! $this->progress->get_user_confirmed() ) {
				add_action( 'wp_ajax_' . WPML_TM_ICL20_Migration_Support::PREFIX . 'user_confirm',
				            array( $this->factory->create_ajax(), 'user_confirmation' ) );

				return;
			}

			if ( $this->is_back_end() ) {
				$this->maybe_fix_preferred_service();
				$migration = $this->factory->create_migration();

				if ( $this->factory->can_rollback() ) {
					add_action( 'wpml_tm_icl20_migration_rollback',
					            array( $migration, 'migrate_project_rollback' ) );
				}

				$support = $this->factory->create_ui_support();
				$support->add_hooks();
				$support->parse_request();

				if ( ! $this->progress->are_next_automatic_attempts_locked() ) {
					$notices->run( ! $migration->run() );
				}

				if ( ! $this->progress->is_migration_done() ) {
					$locks = $this->factory->create_locks();
					$locks->add_hooks();
				}
			}
		}
	}

	/** @return bool */
	private function is_back_end() {
		return $this->wp_api->is_admin()
		       && ! $this->wp_api->is_ajax()
		       && ! $this->wp_api->is_cron_job()
		       && ! $this->wp_api->is_heartbeat();
	}

	/** @return bool */
	private function requires_migration() {
		if ( $this->status->has_active_legacy_icl() ) {
			return true;
		}
		if ( $this->status->has_active_icl_20() ) {
			if ( $this->progress->is_migration_incomplete() ) {
				return true;
			}
			$this->progress->set_migration_done();
		}

		return false;
	}

	/**
	 * If the website is set to use a preferred translation service which is the legacy ICL, it will replace it with
	 * ICL2.0
	 */
	private function maybe_fix_preferred_service() {
		if ( $this->status->is_preferred_service_legacy_ICL() ) {
			$this->status->set_preferred_service_to_ICL20();
		}
	}
}