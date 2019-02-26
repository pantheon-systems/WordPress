<?php

/**
 * Class WPML_TM_TF_AJAX_Feedback_List_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TM_TF_AJAX_Feedback_List_Hooks_Factory implements IWPML_AJAX_Action_Loader {

	/** @return WPML_TM_TF_Feedback_List_Hooks */
	public function create() {
		return new WPML_TM_TF_Feedback_List_Hooks();
	}
}
