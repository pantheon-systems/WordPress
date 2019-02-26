<?php

class WPML_PB_Elementor_Handle_Custom_Fields_Factory implements IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		$elementor_db_factory = new WPML_Elementor_DB_Factory();
		$data_settings        = new WPML_Elementor_Data_Settings( $elementor_db_factory->create() );

		return new WPML_PB_Handle_Custom_Fields( $data_settings );
	}
}