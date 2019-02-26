<?php

class WPML_TM_ATE_Translator_Message_Classic_Editor_Factory implements IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	public function create() {
		global $wpdb;

		if ( WPML_TM_ATE_Status::is_enabled_and_activated() && ( wpml_is_ajax() || WPML_TM_Page::is_translation_queue() )) {

			$email_twig_factory = new WPML_TM_Email_Twig_Template_Factory();

			return new WPML_TM_ATE_Translator_Message_Classic_Editor(
				new WPML_Translation_Manager_Records(
					$wpdb,
					new WPML_WP_User_Query_Factory()
				),
				new WPML_WP_User_Factory(),
				new WPML_TM_ATE_Request_Activation_Email(
					new WPML_TM_Email_Notification_View( $email_twig_factory->create() )
				)
			);
		}
	}
}