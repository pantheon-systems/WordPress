<?php

class WPML_TM_Serialized_Custom_Field_Package_Handler_Factory implements IWPML_Backend_Action_Loader {

	public function create() {

		$translation_management = wpml_load_core_tm();
		return new WPML_TM_Serialized_Custom_Field_Package_Handler(
			new WPML_Custom_Field_Setting_Factory( $translation_management )
		);
	}
}