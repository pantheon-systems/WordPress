<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Core_Privacy_Content extends WPML_Privacy_Content {

	/**
	 * @return string
	 */
	protected function get_plugin_name() {
		return 'WPML';
	}

	/**
	 * @return string|array
	 */
	protected function get_privacy_policy() {
		return array(
			__( 'WPML uses cookies to identify the visitor’s current language, the last visited language and the language of users who have logged in.', 'sitepress' ),
			__( 'While you use the plugin, WPML will share data regarding the site through Installer. No data from the user itself will be shared.', 'sitepress' ),
		);
	}

}