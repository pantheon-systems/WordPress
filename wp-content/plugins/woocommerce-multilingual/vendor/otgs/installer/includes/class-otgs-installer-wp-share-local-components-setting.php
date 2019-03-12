<?php

class OTGS_Installer_WP_Share_Local_Components_Setting {

	const OPTION_KEY = 'otgs_share_local_components';

	public function save( array $repos ) {
		$settings = array_merge( $this->get(), $repos );
		update_option( self::OPTION_KEY, $settings );
	}

	/**
	 * @param string $repo
	 *
	 * @return bool
	 */
	public function is_repo_allowed( $repo ) {
		$allowed_repos = $this->get();

		return isset( $allowed_repos[ $repo ] ) && $allowed_repos[ $repo ];
	}

	public function has_setting( $repo ) {
		$current_value = $this->get();

		return $current_value
		       && array_key_exists( $repo, $current_value );
	}

	private function get() {
		$setting = get_option( self::OPTION_KEY );
		return $setting ? $setting : array();
	}
}