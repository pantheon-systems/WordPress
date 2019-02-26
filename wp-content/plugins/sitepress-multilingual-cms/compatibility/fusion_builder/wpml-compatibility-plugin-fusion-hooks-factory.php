<?php

class WPML_Compatibility_Plugin_Fusion_Hooks_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WPML_Compatibility_Plugin_Fusion_Global_Element_Hooks(
			$sitepress,
			new WPML_Translation_Element_Factory( $sitepress ),
			new WPML_Custom_Columns( $sitepress )
		);
	}
}
