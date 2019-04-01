<?php

class WPML_End_User_Confirmation_Auth {
	/**
	 * @param WPML_End_User_Confirmation_Auth_Data $data
	 * @return bool
	 */
	public function is_valid( WPML_End_User_Confirmation_Auth_Data $data ) {
		$site_key = $this->get_site_key();

		if ( md5( $site_key ) !== $data->get_site_key() ) {
			return false;
		}

		$user = get_userdata( $data->get_user_id() );
		if ( ! $user ) {
			return false;
		}

		return true;
	}

	private function get_site_key() {
		$site_key = false;

		$settings = get_option('wp_installer_settings');
		$settings = base64_decode( $settings );

		if ( function_exists( 'gzuncompress' ) && function_exists( 'gzcompress' ) ) {
			$settings = gzuncompress( $settings );
		}

		$settings = unserialize( $settings );

		if ( isset( $settings['repositories']['wpml']['subscription']['key'] ) ) {
			$site_key =  $settings['repositories']['wpml']['subscription']['key'];
		}

		return $site_key;
	}
}
