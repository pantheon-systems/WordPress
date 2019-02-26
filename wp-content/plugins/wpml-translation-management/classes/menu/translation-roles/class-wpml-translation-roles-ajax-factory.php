<?php

class WPML_Translation_Roles_Ajax_Factory implements IWPML_AJAX_Action_Loader {

	public function create() {
		global $wpdb, $sitepress;

		$email_twig_factory = new WPML_TM_Email_Twig_Template_Factory();

		$hooks = array(

			new WPML_Translation_Manager_Ajax(
				new WPML_Translation_Manager_View(),
				new WPML_Translation_Manager_Records( $wpdb, new WPML_WP_User_Query_Factory() ),
				new WPML_Super_Globals_Validation(),
				new WPML_WP_User_Factory(),
				new WPML_TM_Email_Notification_View( $email_twig_factory->create() )
			),

			new WPML_Translator_Ajax(
				new WPML_Translator_View(
					apply_filters(
						'wpml_tm_allowed_source_languages',
						new WPML_Language_Collection( $sitepress, array_keys( $sitepress->get_active_languages() ) )
					)
				),
				new WPML_Translator_Records( $wpdb, new WPML_WP_User_Query_Factory() ),
				new WPML_Super_Globals_Validation(),
				new WPML_WP_User_Factory(),
				new WPML_Language_Pair_Records( $wpdb, new WPML_Language_Records( $wpdb ) )
			)

		);

		return $hooks;

	}
}