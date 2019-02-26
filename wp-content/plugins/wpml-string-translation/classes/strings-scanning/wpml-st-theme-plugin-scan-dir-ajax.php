<?php

class WPML_ST_Theme_Plugin_Scan_Dir_Ajax {

	/** @var WPML_ST_Scan_Dir */
	private $scan_dir;

	/** @var WPML_ST_File_Hashing */
	private $file_hashing;

	/**
	 * WPML_ST_Theme_Plugin_Scan_Dir_Ajax constructor.
	 *
	 * @param WPML_ST_Scan_Dir $scan_dir
	 * @param WPML_ST_File_Hashing $file_hashing
	 */
	public function __construct( WPML_ST_Scan_Dir $scan_dir, WPML_ST_File_Hashing $file_hashing ) {
		$this->scan_dir = $scan_dir;
		$this->file_hashing = $file_hashing;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_get_files_to_scan', array( $this, 'get_files' ) );
	}

	public function get_files() {
		$folders = $this->get_folder();
		$result = array();

		if ( $folders ) {
			$file_type = array( 'php', 'inc' );
			$files_found_chunks = array();

			foreach ( $folders as $folder ) {
				$files_found_chunks[] = $this->scan_dir->scan(
					$folder,
					$file_type,
					$this->is_one_file_plugin(),
					$this->get_folders_to_ignore() );
			}

			$files = call_user_func_array( 'array_merge', $files_found_chunks );
			$files = $this->filter_modified_files( $files );

			if ( ! $files ) {
				$this->clear_items_to_scan_buffer();
			}

			$result = array(
				'files' => $files,
				'no_files_message' => __( 'Files already scanned.', 'wpml-string-translation' ),
			);
		}

		wp_send_json_success( $result );
	}

	private function clear_items_to_scan_buffer() {
		wpml_get_admin_notices()->remove_notice(
			WPML_ST_Themes_And_Plugins_Settings::NOTICES_GROUP,
			WPML_ST_Themes_And_Plugins_Updates::WPML_ST_SCAN_NOTICE_ID
		);
		delete_option( WPML_ST_Themes_And_Plugins_Updates::WPML_ST_ITEMS_TO_SCAN );
	}

	/**
	 * @param array $files
	 *
	 * @return array
	 */
	private function filter_modified_files( $files ) {
		$modified_files = array();
		foreach ( $files as $file ) {
			if ( $this->file_hashing->hash_changed( $file ) ) {
				$modified_files[] = $file;
			}
		}

		return $modified_files;
	}

	/** @return array */
	private function get_folder() {
		$folder = array();

		if ( array_key_exists( 'theme', $_POST ) ) {
			$folder[] = get_theme_root() . '/' . sanitize_text_field( $_POST['theme'] );
		} else if ( array_key_exists( 'plugin', $_POST ) ){
			$plugin_folder = explode( '/', $_POST['plugin'] );
			$folder[] = WP_PLUGIN_DIR . '/' . sanitize_text_field( $plugin_folder[0] );
		} else if ( array_key_exists( 'mu-plugin', $_POST ) ) {
			$folder[] = WPMU_PLUGIN_DIR . '/' . sanitize_text_field( $_POST['mu-plugin'] );
		}

		return $folder;
	}

	private function is_one_file_plugin() {
		$is_one_file_plugin = false;

		if ( array_key_exists( 'plugin', $_POST ) ) {
			$is_one_file_plugin = false === strpos( $_POST['plugin'], 'plugins/' );
		}

		if ( array_key_exists( 'mu-plugin', $_POST ) ) {
			$is_one_file_plugin = false === strpos( $_POST['mu-plugin'], 'mu-plugins/' );
		}

		return $is_one_file_plugin;
	}

	/**
	 * @return array
	 */
	private function get_folders_to_ignore() {
		$folders = array(
			WPML_ST_PATH . '/tests/',
			WPML_PLUGIN_PATH . '/tests/',
		);

		if ( defined( 'WPML_TM_PATH' ) ) {
			$folder[] = WPML_TM_PATH . '/tests/';
		}

		return $folders;
	}
}