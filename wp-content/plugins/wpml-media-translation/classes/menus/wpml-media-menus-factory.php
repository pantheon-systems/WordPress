<?php

/**
 * Class WPML_Media_Menus_Factory
 */
class WPML_Media_Menus_Factory {

	public function create() {
		global $sitepress, $wpdb;

		$wpml_wp_api     = $sitepress->get_wp_api();
		$wpml_media_path = $wpml_wp_api->constant( 'WPML_MEDIA_PATH' );

		$template_service_loader = new WPML_Twig_Template_Loader( array( $wpml_media_path . '/templates/menus/' ) );
		$pagination              = new WPML_Admin_Pagination();

		return new WPML_Media_Menus( $template_service_loader, $sitepress, $wpdb, $pagination );
	}

}