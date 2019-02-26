<?php

class OTGS_Installer_WP_Components_Storage {

	const COMPONENTS_CACHE_OPTION_KEY = 'otgs_active_components';

	public function refresh_cache() {
		$active_theme      = wp_get_theme();
		$installed_plugins = $this->get_plugins();
		$components        = array();

		foreach ( $installed_plugins as $file => $plugin ) {
			if ( is_plugin_active( $file ) ) {
				$components['plugin'][] = array(
					'File'    => $file,
					'Name'    => $plugin['Name'],
					'Version' => $plugin['Version'],
				);
			}
		}

		$components['theme'][] = array(
			'Template' => $active_theme->get_template(),
			'Name'     => $active_theme->get( 'Name' ),
			'Version'  => $active_theme->get( 'Version' ),
		);

		update_option( self::COMPONENTS_CACHE_OPTION_KEY, $components );
	}

	public function is_outdated() {
		$components = $this->get();

		if ( ! $components ) {
			return true;
		}

		$current_theme     = wp_get_theme();
		$active_plugins    = get_option( 'active_plugins' );

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = $this->get_plugins();

		if ( isset( $components['theme'] ) ) {
			if ( $components['theme'][0]['Template'] !== $current_theme->get_template() ||
			     $components['theme'][0]['Version'] !== $current_theme->get( 'Version' )
			) {
				return true;
			}
		}

		if ( array_key_exists( 'plugin', $components ) ) {
			$cached_activated_plugins = wp_list_pluck( $components['plugin'], 'File' );
			sort( $cached_activated_plugins );
			sort( $active_plugins );

			if ( $cached_activated_plugins !== $active_plugins ) {
				return true;
			}

			foreach ( $components['plugin'] as $plugin ) {
				if ( $plugin['Version'] !== $installed_plugins[ $plugin['File'] ]['Version'] ||
				     ! is_plugin_active( $plugin['File'] )
				) {
					return true;
				}
			}
		}

		return false;
	}

	public function get() {
		return get_option( self::COMPONENTS_CACHE_OPTION_KEY );
	}

	/**
	 * @return array
	 */
	public function get_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins();
	}
}