<?php

class WPML_TM_Translation_Services_Admin_Active_Template_Factory {

	public function create() {
		global $sitepress;

		$paths       = array( WPML_TM_PATH . '/templates/menus/translation-services/'	);
		$twig_loader = new WPML_Twig_Template_Loader( $paths );

		$active_service = $sitepress->get_setting( 'translation_service' );
		$active_service = $active_service ? new WPML_TP_Service( $active_service ) : null;

		return new WPML_TM_Translation_Services_Admin_Active_Template( $twig_loader->get_template(), $active_service );
	}
}
