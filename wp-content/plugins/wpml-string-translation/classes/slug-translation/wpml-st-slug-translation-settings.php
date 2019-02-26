<?php

class WPML_ST_Slug_Translation_Settings {

	const KEY_ENABLED_GLOBALLY = 'wpml_base_slug_translation';

	/** @param bool $enabled */
	public function set_enabled( $enabled ) {
		update_option( self::KEY_ENABLED_GLOBALLY, (int) $enabled );
	}

	/** @return bool */
	public function is_enabled() {
		return (bool) get_option( self::KEY_ENABLED_GLOBALLY );
	}

	public function is_translated( $type_name ) {
		throw new Exception( 'Use a child class with the proper element type: post or taxonomy.' );
	}

	public function set_type( $type, $is_type_enabled ) {
		throw new Exception( 'Use a child class with the proper element type: post or taxonomy.' );
	}

	public function save() {
		throw new Exception( 'Use a child class with the proper element type: post or taxonomy.' );
	}
}
