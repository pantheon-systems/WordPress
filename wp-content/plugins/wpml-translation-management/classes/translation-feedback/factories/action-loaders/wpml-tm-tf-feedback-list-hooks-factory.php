<?php

/**
 * Class WPML_TM_TF_Feedback_List_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TM_TF_Feedback_List_Hooks_Factory extends WPML_Current_Screen_Loader_Factory {

	/** @return string */
	protected function get_screen_regex() {
		return '/wpml-translation-feedback-list/';
	}

	/** @return WPML_TM_TF_Feedback_List_Hooks */
	protected function create_hooks() {
		return new WPML_TM_TF_Feedback_List_Hooks();
	}
}
