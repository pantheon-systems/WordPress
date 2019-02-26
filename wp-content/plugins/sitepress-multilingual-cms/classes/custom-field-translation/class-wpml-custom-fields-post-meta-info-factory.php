<?php
/**
 * @author OnTheGo Systems
 */
class WPML_Custom_Fields_Post_Meta_Info_Factory implements IWPML_AJAX_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;
		$translatable_element_factory = new WPML_Translation_Element_Factory( $sitepress );
		return new WPML_Custom_Fields_Post_Meta_Info( $translatable_element_factory );
	}
}