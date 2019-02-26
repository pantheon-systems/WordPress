<?php

class WPML_Translator_Admin_Records extends WPML_Translator_Records {

	protected function get_required_wp_roles() {
		return array( 'administrator' );
	}

}