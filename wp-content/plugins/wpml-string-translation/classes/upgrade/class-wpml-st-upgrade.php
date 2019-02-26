<?php

class WPML_ST_Upgrade {

	const TRANSIENT_UPGRADE_IN_PROGRESS = 'wpml_st_upgrade_in_progress';

	/** @var SitePress $sitepress */
	private $sitepress;
	
	private $string_settings;

	/** @var  WPML_ST_Upgrade_Command_Factory */
	private $command_factory;

	/** @var bool $upgrade_in_progress */
	private $upgrade_in_progress;

	/**
	 * @param SitePress $sitepress
	 * @param WPML_ST_Upgrade_Command_Factory $command_factory
	 */
	public function __construct( $sitepress, WPML_ST_Upgrade_Command_Factory $command_factory ) {
		$this->sitepress = $sitepress;
		$this->string_settings = $this->sitepress->get_setting( 'st', array() );
		$this->command_factory = $command_factory;
	}
	
	public function run() {
		if ( get_transient( self::TRANSIENT_UPGRADE_IN_PROGRESS ) ) {
			return;
		}

		if ( $this->sitepress->get_wp_api()->is_admin() ) {
			if ( $this->sitepress->get_wp_api()->constant( 'DOING_AJAX' ) ) {
				$this->run_ajax();
			} else {
				$this->run_admin();
			}
		} else {
			$this->run_front_end();
		}

		$this->set_upgrade_completed();
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml-st-upgrade-db-cache-command', array( $this, 'run_st_db_cache_command' ) );
	}

	public function run_st_db_cache_command() {
		$class = 'WPML_ST_Upgrade_Db_Cache_Command';
		$this->run_ajax_command( $class );
	}

	private function run_admin() {
		$this->maybe_run( 'WPML_ST_Upgrade_Migrate_Originals' );
		$this->maybe_run( 'WPML_ST_Upgrade_Db_Cache_Command' );
		$this->maybe_run( 'WPML_ST_Upgrade_Display_Strings_Scan_Notices' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_String_Packages' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_String_Location' );
		$this->maybe_run( 'WPML_ST_Upgrade_MO_Scanning' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_String_Name_Index' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_Longtext_String_Value' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_Strings_Add_Translation_Priority_Field' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_String_Packages_Word_Count' );
	}

	private function run_ajax() {
		$this->maybe_run_ajax( 'WPML_ST_Upgrade_Migrate_Originals' );

		// it has to be maybe_run
		$this->maybe_run( 'WPML_ST_Upgrade_Db_Cache_Command' );
		$this->maybe_run( 'WPML_ST_Upgrade_MO_Scanning' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_String_Packages_Word_Count' );
	}

	private function run_front_end() {
		$this->maybe_run( 'WPML_ST_Upgrade_Db_Cache_Command' );
		$this->maybe_run( 'WPML_ST_Upgrade_MO_Scanning' );
		$this->maybe_run( 'WPML_ST_Upgrade_DB_String_Packages_Word_Count' );
	}
	
	private function maybe_run( $class ) {
		if ( ! $this->has_been_command_executed( $class ) ) {
			$this->set_upgrade_in_progress();
			$upgrade = $this->command_factory->create( $class );
			if ( $upgrade->run() ) {
				$this->mark_command_as_executed( $class );
			}
		}
	}

	private function maybe_run_ajax( $class ) {
		if ( ! $this->has_been_command_executed( $class ) ) {
			$this->run_ajax_command( $class );
		}
	}

	/**
	 * @param $class
	 */
	private function run_ajax_command( $class ) {
		if ( $this->nonce_ok( $class ) ) {
			$upgrade = $this->command_factory->create( $class );
			if ( $upgrade->run_ajax() ) {
				$this->mark_command_as_executed( $class );
				$this->sitepress->get_wp_api()->wp_send_json_success( '' );
			}
		}
	}

	private function nonce_ok( $class ) {
		$ok = false;
		
		$class = strtolower( $class );
		$class = str_replace( '_', '-', $class );
		if ( isset( $_POST['action'] ) && $_POST['action'] === $class ) {
			$nonce = $this->filter_nonce_parameter();
			if ( $this->sitepress->get_wp_api()->wp_verify_nonce( $nonce, $class . '-nonce' ) ) {
				$ok = true;
			}
		}
		return $ok;
	}

	/**
	 * @param string $class
	 *
	 * @return bool
	 */
	private function has_been_command_executed( $class ) {
		$id = call_user_func( array( $class, 'get_command_id' ) );
		return isset( $this->string_settings[ $id . '_has_run' ] );
	}

	/**
	 * @param string $class
	 */
	private function mark_command_as_executed( $class ) {
		$id = call_user_func( array( $class, 'get_command_id' ) );
		$this->string_settings[ $id . '_has_run' ] = true;
		$this->sitepress->set_setting( 'st', $this->string_settings, true );
		wp_cache_flush();
	}

	/**
	 * @return mixed
	 */
	protected function filter_nonce_parameter() {
		return filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	}

	private function set_upgrade_in_progress() {
		if ( ! $this->upgrade_in_progress ) {
			$this->upgrade_in_progress = true;
			set_transient( self::TRANSIENT_UPGRADE_IN_PROGRESS, true, MINUTE_IN_SECONDS );
		}
	}

	private function set_upgrade_completed() {
		if ( $this->upgrade_in_progress ) {
			$this->upgrade_in_progress = false;
			delete_transient( self::TRANSIENT_UPGRADE_IN_PROGRESS );
		}
	}
}

