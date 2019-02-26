<?php

class WPML_ST_Theme_Localization_Utils {

	/** @return array */
	public function get_theme_data() {
		$themes     = wp_get_themes();
		$theme_data = array();

		foreach ( $themes as $theme_folder => $theme ) {
			$theme_data[ $theme_folder ] = array(
				'path'       => $theme->get_theme_root() . '/' . $theme->get_stylesheet(),
				'name'       => $theme->get( 'Name' ),
				'TextDomain' => $theme->get( 'TextDomain' ),
			);
		}

		return $theme_data;
	}
}