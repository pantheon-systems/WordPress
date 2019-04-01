<?php

class WPML_Display_As_Translated_Snippet_Filters_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {
	public function create() {
		return new WPML_Display_As_Translated_Snippet_Filters();
	}
}