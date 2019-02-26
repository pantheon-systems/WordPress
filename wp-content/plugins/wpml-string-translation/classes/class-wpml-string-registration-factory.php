<?php

/**
 * Class WPML_String_Registration_Factory
 */
class WPML_String_Registration_Factory {

	private $pb_plugin_name;

	public function __construct( $pb_plugin_name ) {
		$this->pb_plugin_name = $pb_plugin_name;
	}

	/**
	 * @return WPML_PB_String_Registration
	 */
	public function create() {
		global $wpdb;
		global $sitepress;

		$absolute_links = new AbsoluteLinks();
		$permalinks_converter = new WPML_Absolute_To_Permalinks( $sitepress );
		$translate_link_targets = new WPML_Translate_Link_Targets( $absolute_links, $permalinks_converter );
		return new WPML_PB_String_Registration(
			new WPML_PB_API_Hooks_Strategy( $this->pb_plugin_name ),
			new WPML_ST_String_Factory( $wpdb ),
			new WPML_ST_Package_Factory(),
			$translate_link_targets,
			$sitepress->get_active_languages()
		);
	}
}