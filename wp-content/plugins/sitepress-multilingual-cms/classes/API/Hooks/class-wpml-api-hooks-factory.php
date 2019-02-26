<?php

class WPML_API_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		global $wpdb, $sitepress, $wpml_post_translations, $wpml_url_converter;

		$hooks   = array();

		$hooks[] = new WPML_API_Hook_Sync_Custom_Fields(
			new WPML_Sync_Custom_Fields(
				$wpdb,
				new WPML_Translation_Element_Factory( $sitepress ),
				$sitepress->get_custom_fields_translation_settings( WPML_COPY_CUSTOM_FIELD )
			)
		);

		$hooks[] = new WPML_API_Hook_Links( new WPML_Post_Status_Display_Factory( $sitepress ) );

		$hooks[] = new WPML_API_Hook_Translation_Element ( $sitepress,
		                                                   new WPML_Translation_Element_Factory( $sitepress ),
		                                                   new WPML_Flags_Factory( $wpdb ) );

		$hooks[] = new WPML_API_Hook_Translation_Mode( new WPML_Settings_Helper( $wpml_post_translations, $sitepress ) );

		$hooks[] = new WPML_API_Hook_Copy_Post_To_Language( new WPML_Post_Duplication( $wpdb, $sitepress ) );


		$url_resolver_factory = new WPML_Resolve_Object_Url_Helper_Factory();
		$absolute_resolver    = $url_resolver_factory->create( WPML_Resolve_Object_Url_Helper_Factory::ABSOLUTE_URL_RESOLVER );
		$hooks[] = new WPML_API_Hook_Permalink( $wpml_url_converter, $absolute_resolver );

		return $hooks;
	}
}