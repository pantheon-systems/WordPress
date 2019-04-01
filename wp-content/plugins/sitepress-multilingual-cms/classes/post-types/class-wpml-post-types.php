<?php

class WPML_Post_Types extends WPML_SP_User {

	public function get_translatable() {
		return $this->sitepress->get_translatable_documents( true );
	}

	public function get_readonly() {
		$wp_post_types = $this->sitepress->get_wp_api()->get_wp_post_types_global();

		$types = array();
		$tm_settings = $this->sitepress->get_setting( 'translation-management', array() );
		if ( array_key_exists( 'custom-types_readonly_config', $tm_settings )
		     && is_array( $tm_settings['custom-types_readonly_config'] ) ) {
			foreach ( array_keys( $tm_settings['custom-types_readonly_config'] ) as $cp ) {
				if ( isset( $wp_post_types[ $cp ] ) ) {
					$types[ $cp ] = $wp_post_types[ $cp ];
				}
			}
		}
		return $types;
	}

	public function get_translatable_and_readonly() {
		return $this->get_translatable() + $this->get_readonly();
	}

}
