<?php

class WPML_Gutenberg_Integration_Factory {

	public function create() {
		/** @var SitePress $sitepress */
		global $sitepress;

		$config_option = new WPML_Gutenberg_Config_Option();

		return new WPML_Gutenberg_Integration(
			new WPML_Gutenberg_Strings_In_Block( $config_option ),
			$config_option,
			$sitepress
		);
	}
}
