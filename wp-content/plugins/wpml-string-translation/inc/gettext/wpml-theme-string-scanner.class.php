<?php

class WPML_Theme_String_Scanner extends WPML_String_Scanner implements IWPML_ST_String_Scanner {

	public function scan() {
		$this->current_type = 'theme';
		$this->scan_starting( $this->current_type );
		$theme_info  = wp_get_theme();
		$text_domain = $theme_info->get( 'TextDomain' );
		$current_theme_name = array_key_exists( 'theme', $_POST ) ? $_POST['theme'] : '';
		$this->current_path = $current_theme_name ? get_theme_root() . '/' . $current_theme_name : '';
		$current_theme = wp_get_theme( $current_theme_name );
		$this->text_domain = $current_theme->get( 'TextDomain' );
		$this->init_text_domain( $text_domain );
		$this->scan_theme_files();
		$this->set_stats( 'theme_localization_domains', $current_theme_name );

		if ( $theme_info && ! is_wp_error( $theme_info ) ) {
			$this->remove_notice( $theme_info->get( 'Name' ) );
		}

		$this->scan_response();
	}

	private function scan_theme_files() {
		require_once WPML_ST_PATH . '/inc/potx.php';

		if ( array_key_exists( 'files', $_POST ) ) {

			foreach ( $_POST['files'] as $file ) {

				if ( $this->file_hashing->hash_changed( $file ) ) {

					$this->add_stat( sprintf( __( 'Scanning file: %s', 'wpml-string-translation' ), $file ) );
					$this->add_scanned_file( $file );
					_potx_process_file( $file, 0, array( $this, 'store_results' ), '_potx_save_version', $this->get_default_domain() );

				} else {
					$this->add_stat( sprintf( __( 'Skipping file: %s', 'wpml-string-translation' ), $file ) );
				}

			}
		}
	}
}
