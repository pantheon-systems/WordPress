<?php
/**
 * Class for the settings importer.
 */

class OWP_Settings_Importer {

	/**
	 * Process import file - this parses the settings data and returns it.
	 *
	 * @param string $file path to json file.
	 */
	public function process_import_file( $file ) {

		// Get file contents.
		$data = OWP_Demos_Helpers::get_remote( $file );

		// Return from this function if there was an error.
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		// Get file contents and decode
		$raw  = file_get_contents( $file );
		$data = @unserialize( $raw );

		// Delete import file
		unlink( $file );

		// If wp_css is set then import it.
		if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
			wp_update_custom_css_post( $data['wp_css'] );
		}

		// Import the data
    	return $this->import_data( $data['mods'] );

	}

	/**
	 * Sanitization callback
	 *
	 * @since 1.0.5
	 */
	private function import_data( $file ) {

		// Import the file
		if ( ! empty( $file ) ) {

			if ( '0' == json_last_error() ) {

				// Loop through mods and add them
				foreach ( $file as $mod => $value ) {
					set_theme_mod( $mod, $value );
				}

			}

		}

		// Return file
		return $file;

	}
}
