<?php

class WPML_Translation_Manager_Records extends WPML_Translation_Roles_Records {

	protected function get_capability() {
		return WPML_Manage_Translations_Role::CAPABILITY;
	}

	protected function get_required_wp_roles() {
		return array( 'administrator', 'editor' );
	}

}