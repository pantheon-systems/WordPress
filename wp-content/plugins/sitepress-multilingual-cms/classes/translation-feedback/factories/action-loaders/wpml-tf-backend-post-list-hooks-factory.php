<?php

/**
 * Class WPML_TF_Backend_Post_List_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Post_List_Hooks_Factory extends WPML_Current_Screen_Loader_Factory {

	/** @return string */
	protected function get_screen_regex() {
		return '/^edit$/';
	}

	/** @return WPML_TF_Backend_Post_List_Hooks */
	protected function create_hooks() {
		global $wpdb, $sitepress;

		$post_rating_metrics  = new WPML_TF_Post_Rating_Metrics( $wpdb );
		$document_information = new WPML_TF_Document_Information( $sitepress );
		$backend_styles       = new WPML_TF_Backend_Styles();

		return new WPML_TF_Backend_Post_List_Hooks( $post_rating_metrics, $document_information, $backend_styles );
	}
}
