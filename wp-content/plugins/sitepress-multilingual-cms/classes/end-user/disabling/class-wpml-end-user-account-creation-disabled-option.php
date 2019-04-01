<?php

class WPML_End_User_Account_Creation_Disabled_Option {

	const OPTION = 'wpml-end-user-disabling';

	/**
	 * @param bool $value
	 */
	public function set_option( $value ) {
		update_option( self::OPTION, (bool) $value );
	}

	/**
	 * @return bool
	 */
	public function is_disabled() {
		return get_option( self::OPTION, false );
	}
}
