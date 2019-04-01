<?php

/**
 * @author OnTheGo Systems
 */
class WPML_ST_Themes_And_Plugins_Updates {

	const WPML_WP_UPDATED_MO_FILES = 'wpml_wp_updated_mo_files';
	const WPML_ST_ITEMS_TO_SCAN = 'wpml_items_to_scan';
	const WPML_ST_SCAN_NOTICE_ID = 'wpml_st_scan_items';
	const WPML_ST_FASTER_SETTINGS_NOTICE_ID = 'wpml_st_faster_settings';
	const WPML_ST_SCAN_ACTIVE_ITEMS_NOTICE_ID = 'wpml_st_scan_active_items';

	/** @var WPML_Notices */
	private $admin_notices;
	/** @var WPML_ST_Themes_And_Plugins_Settings */
	private $settings;

	/**
	 * @var WPML_ST_Fastest_Settings_Notice
	 */
	private $fastest_settings_notice;

	/**
	 * WPML_ST_Admin_Notices constructor.
	 *
	 * @param WPML_Notices                        $admin_notices
	 * @param WPML_ST_Themes_And_Plugins_Settings $settings
	 */
	public function __construct( WPML_Notices $admin_notices, WPML_ST_Themes_And_Plugins_Settings $settings, WPML_ST_Fastest_Settings_Notice $fastest_settings ) {
		$this->admin_notices           = $admin_notices;
		$this->settings                = $settings;
		$this->fastest_settings_notice = $fastest_settings;
	}

	public function init_hooks() {
		add_action( 'upgrader_process_complete', array( $this, 'store_mo_file_update' ), 10, 2 );
		add_action( 'init', array( $this, 'handle_fastest_settings_notice' ), 10, 2 );
	}

	public function data_is_valid( $thing ) {
		return $thing && ! is_wp_error( $thing );
	}

	public function notices_count() {
		return $this->admin_notices->count();
	}

	public function remove_notice( $id ) {
		$this->admin_notices->remove_notice( $this->settings->get_notices_group(), $id );
	}

	/**
	 * @param WP_Upgrader $upgrader
	 * @param $language_translations
	 *
	 * @return bool
	 */
	public function store_mo_file_update( WP_Upgrader $upgrader, $language_translations ) {
		if ( is_wp_error( $upgrader->result ) ) {
			return false;
		}

		$action = $language_translations['action'];
		if ( in_array( $action, array( 'update', 'install' ), true ) ) {
			if ( 'translation' === $language_translations['type'] ) {
				$last_update = get_option( self::WPML_WP_UPDATED_MO_FILES, array() );
				$translations = $language_translations['translations'];
				foreach ( $translations as $translation ) {
					$last_update[ $translation['type'] ][ $translation['slug'] ] = time();
				}
				update_option( self::WPML_WP_UPDATED_MO_FILES, $last_update, false );
			}
		}
	}

	public function handle_fastest_settings_notice() {
		$this->fastest_settings_notice->add();
	}

	public function remove_fastest_settings_notice() {
		$this->fastest_settings_notice->remove();
	}
}
