<?php

class WPML_ST_Taxonomy_Labels_Translation_Factory implements IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	const AJAX_ACTION_BUILD                       = 'wpml_get_terms_and_labels_for_taxonomy_table';
	const AJAX_ACTION_SAVE                        = 'wpml_tt_save_labels_translation';
	const AJAX_ACTION_CHANGE_LANG                 = 'wpml_tt_change_tax_strings_language';
	const AJAX_ACTION_SET_SLUG_TRANSLATION_ENABLE = 'wpml_tt_set_slug_translation_enabled';

	public function create() {
		global $sitepress;

		if ( $this->is_taxonomy_translation_table_action() ) {
			$records_factory  = new WPML_Slug_Translation_Records_Factory();
			$taxonomy_strings = new WPML_ST_Taxonomy_Strings(
				$records_factory->create( WPML_Slug_Translation_Factory::TAX ),
				wpml_st_load_string_factory()
			);
			$hooks[] = new WPML_ST_Taxonomy_Labels_Translation(
				$taxonomy_strings,
				new WPML_ST_Tax_Slug_Translation_Settings(),
				new WPML_Super_Globals_Validation(),
				$sitepress->get_active_languages( true )
			);

			if ( $this->is_wcml_active() ) {
				$hooks[] = new WPML_ST_WCML_Taxonomy_Labels_Translation();
			}

			return $hooks;
		}

		return null;
	}

	private function is_taxonomy_translation_table_action() {
		$allowed_actions = array(
			self::AJAX_ACTION_BUILD,
			self::AJAX_ACTION_SAVE,
			self::AJAX_ACTION_CHANGE_LANG,
			self::AJAX_ACTION_SET_SLUG_TRANSLATION_ENABLE,
		);

		return isset( $_POST['action'] )
		       && in_array( $_POST['action'], $allowed_actions , true );
	}

	private function is_wcml_active() {
		return is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' );
	}
}