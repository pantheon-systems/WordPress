<?php

/**
 * Class WPML_TF_Common_Hooks_Factory
 * @author OnTheGoSystems
 */
class WPML_TF_Common_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	/**
	 * @return WPML_TF_Common_Hooks
	 */
	public function create() {
		$feedback_storage = new WPML_TF_Data_Object_Storage( new WPML_TF_Feedback_Post_Convert() );

		return new WPML_TF_Common_Hooks( $feedback_storage );
	}
}