<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 28/10/17
 * Time: 5:07 PM
 */

class WPML_Fix_Links_In_Display_As_Translated_Content_Factory implements IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WPML_Fix_Links_In_Display_As_Translated_Content(
			$sitepress,
			new WPML_Translate_Link_Targets(
				new AbsoluteLinks(),
				new WPML_Absolute_To_Permalinks( $sitepress )
			)
		);
	}
}
