<?php

class WPML_Elementor_Adjust_Global_Widget_ID_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress;

		$elementor_db_factory = new WPML_Elementor_DB_Factory();
		$data_settings        = new WPML_Elementor_Data_Settings( $elementor_db_factory->create() );

		return new WPML_Elementor_Adjust_Global_Widget_ID(
				$data_settings,
				new WPML_Translation_Element_Factory( $sitepress ),
				$sitepress
			);
	}
}