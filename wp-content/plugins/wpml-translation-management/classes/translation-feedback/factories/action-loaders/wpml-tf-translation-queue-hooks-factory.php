<?php

/**
 * Class WPML_TF_Translation_Queue_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Translation_Queue_Hooks_Factory extends WPML_Current_Screen_Loader_Factory {

	/** @return string */
	protected function get_screen_regex() {
		return '/translations-queue/';
	}

	/** @return WPML_TF_Translation_Queue_Hooks */
	protected function create_hooks() {
		$feedback_storage = new WPML_TF_Data_Object_Storage( new WPML_TF_Feedback_Post_Convert() );
		return new WPML_TF_Translation_Queue_Hooks( $feedback_storage );
	}
}
