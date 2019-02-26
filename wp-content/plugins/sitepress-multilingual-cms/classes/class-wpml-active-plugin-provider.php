<?php

class WPML_Active_Plugin_Provider {
	/**
	 * @return array
	 */
	public function get_active_plugins() {
		$active_plugin_names = array();
		if ( function_exists( 'get_plugins' ) ) {
			foreach ( get_plugins() as $plugin_file => $plugin_data ) {
				if ( is_plugin_active( $plugin_file ) ) {
					$active_plugin_names[] = $plugin_data;
				}
			}
		}

		return $active_plugin_names;
	}

	/**
	 * @return array
	 */
	public function get_active_plugin_names() {
		return wp_list_pluck( $this->get_active_plugins(), 'Name' );
	}
}