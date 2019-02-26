<?php

class WPML_TM_Word_Count_Setters_Factory {

	/**
	 * @return IWPML_TM_Word_Count_Set[]
	 */
	public function create() {
		global $sitepress;

		$records_factory = new WPML_TM_Word_Count_Records_Factory();
		$records         = $records_factory->create();

		$calculator = new WPML_TM_Word_Calculator( new WPML_PHP_Functions() );

		$tm_settings = $sitepress->get_setting( 'translation-management', array() );
		$cf_settings = isset( $tm_settings['custom_fields_translation'] )
			? $tm_settings['custom_fields_translation'] : array();

		$active_langs = array_keys( $sitepress->get_active_languages() );

		$post_calculators = array(
			new WPML_TM_Word_Calculator_Post_Object( $calculator, new WPML_TM_Word_Calculator_Post_Packages( $records ) ),
			new WPML_TM_Word_Calculator_Post_Custom_Fields( $calculator, $cf_settings ),
		);

		$setters = array(
			'post' => new WPML_TM_Word_Count_Set_Post( new WPML_Translation_Element_Factory( $sitepress ), $records, $post_calculators, $active_langs ),
		);

		if ( class_exists( 'WPML_ST_Package_Factory' ) ) {
			$setters['string'] = new WPML_TM_Word_Count_Set_String( $records, $calculator );
			$setters['package'] = new WPML_TM_Word_Count_Set_Package( new WPML_ST_Package_Factory(), $records, $active_langs );
		}

		return $setters;
	}
}