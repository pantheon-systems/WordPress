<?php

class WPML_ST_Slug_Translation_Settings_Factory {

	/**
	 * @throws InvalidArgumentException
	 * @param string $element_type
	 *
	 * @return WPML_ST_Slug_Translation_Settings
	 */
	public function create( $element_type = null ) {
		global $sitepress;

		if ( WPML_Slug_Translation_Factory::POST === $element_type ) {
			return new WPML_ST_Post_Slug_Translation_Settings( $sitepress );
		}

		if ( WPML_Slug_Translation_Factory::TAX === $element_type ) {
			return new WPML_ST_Tax_Slug_Translation_Settings();
		}

		if ( ! $element_type ) {
			return new WPML_ST_Slug_Translation_Settings();
		}

		throw new InvalidArgumentException( 'Invalid element type.' );
	}
}
