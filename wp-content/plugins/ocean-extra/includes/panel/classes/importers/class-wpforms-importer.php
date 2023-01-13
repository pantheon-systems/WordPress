<?php
/**
 * Class for the WPForms importer.
 *
 * Thank you very much to SiteGround for the code.
 */

if ( ! class_exists( 'WPForms' ) ) {
	return;
}

class OWP_WPForms_Importer {

	/**
	 * Process import file - this parses the widget data and returns it.
	 *
	 * @param string $file path to json file.
	 * @global string $widget_import_results
	 */
	public function process_import_file( $file ) {

		// Get file contents.
		$data = OWP_Demos_Helpers::get_remote( $file );

		// Return from this function if there was an error.
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		// Decode file contents.
	    $data = json_decode( $data, true );

		// Import the widget data
    	return $this->import_json( $data );

	}

	public function import_json( $forms ) {

        foreach ( $forms as $form ) {

            // Create empty form so we have an ID to work with.
            $form_id = wp_insert_post(
                array(
                    'post_status' => 'publish',
                    'post_type'   => 'wpforms',
                )
            );

            // Bail if post creation has failed.
            if ( empty( $form_id )
                || is_wp_error( $form_id ) ) {
                continue;
            }

            $form['id'] = $form_id;

            // Update the form with all our compiled data.
            wpforms()->form->update( $form['id'], $form );
        }
    }
}