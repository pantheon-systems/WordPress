<?php

class WPML_Post_Slug_Translation_Records extends WPML_Slug_Translation_Records {

	const STRING_NAME = 'URL slug: %s';

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	protected function get_string_name( $slug ) {
		return sprintf( self::STRING_NAME, $slug );
	}

	/** @return string */
	protected function get_element_type() {
		return 'post';
	}
}
