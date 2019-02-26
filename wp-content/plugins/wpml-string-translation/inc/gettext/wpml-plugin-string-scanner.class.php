<?php

class WPML_Plugin_String_Scanner extends WPML_String_Scanner implements IWPML_ST_String_Scanner {

	private $current_plugin_file;

	public function scan() {
		$plugin_file = $_POST['plugin'];
		$this->current_plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
		$this->current_type = 'plugin';
		$this->current_path = dirname( $this->current_plugin_file );
		$this->text_domain = $this->get_plugin_text_domain();
		$this->scan_starting( $this->current_type );
		$text_domain = $this->get_plugin_text_domain();
		$this->init_text_domain( $text_domain );
		$this->scan_plugin_files();
		$this->current_type = 'plugin';
		$this->set_stats( 'plugin_localization_domains', $plugin_file );
		$this->scan_response();
	}

	private function scan_plugin_files( $dir_or_file = false, $recursion = 0 ) {
		require_once WPML_ST_PATH . '/inc/potx.php';

		foreach ( $_POST['files'] as $file ) {

			if ( $this->file_hashing->hash_changed( $file ) ) {
				_potx_process_file( $file, 0, array( $this, 'store_results' ), '_potx_save_version', $this->get_default_domain() );
				$this->add_scanned_file( $file );
			}

		}
	}
	
	private function get_plugin_text_domain() {
		$text_domain = '';
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		if ( function_exists( 'get_plugin_data' ) ) {
			$plugin_data = get_plugin_data( $this->current_plugin_file );
			if ( isset( $plugin_data[ 'TextDomain' ] ) && $plugin_data[ 'TextDomain' ] != '' ) {
				$text_domain = $plugin_data[ 'TextDomain' ];
	
				return $text_domain;
			}
	
			return $text_domain;
		}
	
		return $text_domain;
	}	
}