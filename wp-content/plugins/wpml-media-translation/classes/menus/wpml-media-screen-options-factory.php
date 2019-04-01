<?php

/**
 * Class WPML_Media_Screen_Options_Factory
 */
class WPML_Media_Screen_Options_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action|WPML_Media_Screen_Options
	 */
	public function create() {

		$options = array();

		if ( $this->is_translation_dashboard() ) {

			$option_name = 'wpml_media_translation_dashboard_items_per_page';
			$options[]   = array(
				'key'  => 'per_page',
				'args' => array(
					'label'   => __( 'Number of items per page:', 'wpml-media' ),
					'default' => get_option( $option_name, 20 ),
					'option'  => $option_name
				)
			);

		}

		if( $options ){
			return new WPML_Media_Screen_Options( $options );
		}

		return null;

	}

	/**
	 * @return bool
	 */
	private function is_translation_dashboard() {
		return ! isset( $_GET['sm'] ) || 'media-translation' === $_GET['sm'];
	}

}