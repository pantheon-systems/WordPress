<?php

class WPML_Translator_Records extends WPML_Translation_Roles_Records {

	protected function get_capability() {
		return WPML_Translator_Role::CAPABILITY;
	}

	protected function get_required_wp_roles() {
		return array( );
	}

}