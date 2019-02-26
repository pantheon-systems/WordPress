<?php

/**
 * Class WPML_TF_Translation_Service_Change_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Translation_Service_Change_Hooks_Factory implements IWPML_Backend_Action_Loader {

	/** @return WPML_TF_Translation_Service_Change_Hooks */
	public function create() {
		return new WPML_TF_Translation_Service_Change_Hooks(
			new WPML_TF_Settings_Read(),
			new WPML_TF_Settings_Write(),
			new WPML_TF_TP_Ratings_Synchronize_Factory()
		);
	}
}
