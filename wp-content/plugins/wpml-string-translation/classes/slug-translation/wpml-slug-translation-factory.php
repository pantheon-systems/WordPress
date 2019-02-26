<?php

class WPML_Slug_Translation_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	const POST = 'post';
	const TAX  = 'taxonomy';

	const INIT_PRIORITY = -1000;

	public function create() {
		global $sitepress;

		$hooks = array();

		$records_factory  = new WPML_Slug_Translation_Records_Factory();
		$settings_factory = new WPML_ST_Slug_Translation_Settings_Factory();

		$post_records = $records_factory->create( WPML_Slug_Translation_Factory::POST );
		$tax_records  = $records_factory->create( WPML_Slug_Translation_Factory::TAX );

		$global_settings = $settings_factory->create();
		$post_settings   = $settings_factory->create( WPML_Slug_Translation_Factory::POST );
		$tax_settings    = $settings_factory->create( WPML_Slug_Translation_Factory::TAX );

		$term_link_filter  = new WPML_ST_Term_Link_Filter( $tax_records, $sitepress, new WPML_WP_Cache_Factory() );

		$hooks['legacy_class'] = new WPML_Slug_Translation(
			$sitepress,
			$records_factory,
			WPML_Get_LS_Languages_Status::get_instance(),
			$term_link_filter,
			$global_settings
		);

		if ( is_admin() ) {
			$hooks['ui_save_post'] = new WPML_ST_Slug_Translation_UI_Save(
				$post_settings,
				$post_records,
				$sitepress,
				new WPML_WP_Post_Type(),
				WPML_ST_Slug_Translation_UI_Save::ACTION_HOOK_FOR_POST
			);
			$hooks['ui_save_tax'] = new WPML_ST_Slug_Translation_UI_Save(
				$tax_settings,
				$tax_records,
				$sitepress,
				new WPML_WP_Taxonomy(),
				WPML_ST_Slug_Translation_UI_Save::ACTION_HOOK_FOR_TAX
			);

			if ( $global_settings->is_enabled() ) {
				$hooks['sync_strings'] = new WPML_ST_Slug_Translation_Strings_Sync(
					$records_factory,
					$settings_factory
				);
			}
		}

		$hooks['public-api'] = new WPML_ST_Slug_Translation_API(
			$records_factory, $settings_factory, $sitepress, new WPML_WP_API()
		);

		return $hooks;
	}
}
