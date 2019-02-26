<?php

class WPML_Upgrade_Admins_To_Manage_Translations_Factory implements IWPML_Backend_Action_Loader {

	const HAS_RUN_OPTION = 'WPML_Upgrade_Admins_To_Manage_Translations_Has_Run';

	public function create() {
		global $sitepress, $wpdb;

		if ( $sitepress && $sitepress instanceof SitePress && ! get_option( self::HAS_RUN_OPTION, false ) ) {
			return new WPML_Upgrade_Admins_To_Manage_Translations( $sitepress->is_setup_complete(), $wpdb );
		}
	}
}
