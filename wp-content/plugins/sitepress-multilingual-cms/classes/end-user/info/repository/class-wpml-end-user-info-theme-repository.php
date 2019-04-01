<?php

class WPML_End_User_Info_Theme_Repository implements WPML_End_User_Info_Repository {
	const ONTHEGOSYSTEMS = 'OnTheGoSystems';

	/**
	 * @return WPML_End_User_Info_Theme|null
	 */
	public function get_data() {
		$current_theme = wp_get_theme();
		$parent_theme_name = $current_theme->parent_theme;
		$current_theme_name = $current_theme->get( 'Name' );

		$author = $current_theme->get( 'Author' );
		if ( self::ONTHEGOSYSTEMS === $author ) {
			return null;
		}

		$theme = new WPML_End_User_Info_Theme( $current_theme_name, $parent_theme_name );
		$theme->set_author( $author );

		return $theme;
	}

	/**
	 * @return string
	 */
	public function get_data_id() {
		return 'theme_info';
	}
}
