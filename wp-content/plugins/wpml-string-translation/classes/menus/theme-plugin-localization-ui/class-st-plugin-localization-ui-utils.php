<?php

class WPML_ST_Plugin_Localization_Utils {

	/** @return array */
	public function get_plugins() {
		$plugins = get_plugins();
		$mu_plugins = wp_get_mu_plugins();

		if ( $mu_plugins ) {
			foreach ( $mu_plugins as $p ) {
				$plugin_file = basename( $p );
				$plugins[ $plugin_file ] = array( 'Name' => 'MU :: ' . $plugin_file );
			}
		}

		return $plugins;
	}

	/**
	 * @param string $plugin_file
	 *
	 * @return bool
	 */
	public function is_plugin_active( $plugin_file ) {
		$active_plugins = get_option( 'active_plugins', array() );

		if ( in_array( $plugin_file, $active_plugins, true ) ) {
			$is_active = true;
		} else {
			$wpmu_sitewide_plugins = (array) maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) );
			$is_active = array_key_exists( $plugin_file, $wpmu_sitewide_plugins );
		}

		return $is_active;
	}

	public function get_plugins_by_status( $active ) {
		$plugins = $this->get_plugins();

		foreach ( $plugins as $plugin_file => $plugin ) {
			if ( $active && ! $this->is_plugin_active( $plugin_file ) ) {
				unset( $plugins[ $plugin_file ] );
			} elseif ( ! $active && $this->is_plugin_active( $plugin_file ) ) {
				unset( $plugins[ $plugin_file ] );
			}
		}

		return $plugins;
	}
}