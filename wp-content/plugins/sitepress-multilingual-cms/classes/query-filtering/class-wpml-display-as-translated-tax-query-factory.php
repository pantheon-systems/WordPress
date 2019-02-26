<?php

class WPML_Display_As_Translated_Tax_Query_Factory implements IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress, $wpml_term_translations;
		return new WPML_Display_As_Translated_Tax_Query( $sitepress, $wpml_term_translations );
	}
}