<?php

class Blocksy_Admin_Dashboard_API_Premium_Plugins extends Blocksy_Admin_Dashboard_API {
	protected $ajax_actions = array(
		'get_premium_plugins_status',

		'premium_plugin_download',
		'premium_plugin_activate',
		'premium_plugin_deactivate',
		'premium_plugin_delete',
	);

	public function get_premium_plugins_status() {
		$this->check_capability( 'edit_plugins' );

		$result = [];
		// Is not installed.
		$status = 'uninstalled';

		$manager = new Blocksy_Plugin_Manager();
		$plugin_manager_config = $manager->get_config();
		$plugins = $plugin_manager_config;
		$installed_plugins = $manager->get_installed_plugins();

		foreach ( array_keys( $plugins ) as $plugin ) {
			$installed_path = $manager->is_plugin_installed( $plugin );

			if (! $installed_path) {
				$status = 'uninstalled'; // Plugin is not installed.
			} else {
				if ( is_plugin_active( $installed_path ) ) {
					$status = 'activated'; // Plugin is active.
				} else {
					$status = 'deactivated'; // Plugin is installed but inactive.
				}
			}

			$result[] = array(
				'name' => $plugin,
				'status' => $status,
			);
		}

		wp_send_json_success( $result );
	}

	public function premium_plugin_download() {
		$this->check_capability( 'install_plugins' );
		$plugin = $this->get_plugin_from_request();

		$manager = new Blocksy_Plugin_Manager();
		$install = $manager->prepare_install( $plugin );

		if ( $install ) {
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	public function premium_plugin_activate() {
		$this->check_capability( 'edit_plugins' );
		$plugin = $this->get_plugin_from_request();

		$manager = new Blocksy_Plugin_Manager();
		$result = $manager->plugin_activation( $plugin );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		wp_send_json_success();
	}

	public function premium_plugin_deactivate() {
		$this->check_capability( 'edit_plugins' );
		$plugin = $this->get_plugin_from_request();

		$manager = new Blocksy_Plugin_Manager();
		$result = $manager->plugin_deactivation( $plugin );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		wp_send_json_success();
	}

	public function premium_plugin_delete() {
		$this->check_capability( 'delete_plugins' );
		$plugin = $this->get_plugin_from_request();

		$manager = new Blocksy_Plugin_Manager();
		$result = $manager->uninstall_plugin( $plugin );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		wp_send_json_success();
	}

	public function check_capability( $cap = 'install_plugins' ) {
		$manager = new Blocksy_Plugin_Manager();
		if ( ! $manager->can( $cap ) ) {
			wp_send_json_error();
		}

		return true;
	}

	public function get_plugin_from_request() {
		if ( ! isset( $_POST['plugin'] ) ) {
			wp_send_json_error();
		}

		return sanitize_text_field(wp_unslash($_POST['plugin']));
	}

}

