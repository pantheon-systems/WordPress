<?php

class WPML_Display_As_Translated_Default_Lang_Messages_Factory extends WPML_Current_Screen_Loader_Factory {

	/**
	 * @return WPML_Display_As_Translated_Default_Lang_Messages
	 */
	public function create_hooks() {
		global $sitepress;

		$template_service_loader = new WPML_Twig_Template_Loader(
			array( WPML_PLUGIN_PATH . '/templates/display-as-translated' )
		);

		return new WPML_Display_As_Translated_Default_Lang_Messages(
			$sitepress,
			new WPML_Display_As_Translated_Default_Lang_Messages_View( $template_service_loader->get_template() )
		);
	}

	/** @return string */
	public function get_screen_regex() {
		return '/^sitepress-multilingual-cms\/menu\/languages$/';
	}
}