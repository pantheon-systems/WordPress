<?php

namespace Blocksy;


class DemoInstallPluginsInstaller {
	protected $has_streaming = true;
	protected $plugins = null;

	public function __construct($args = []) {
		$args = wp_parse_args($args, [
			'has_streaming' => true,
			'plugins' => null
		]);

		if (
			!$args['plugins']
			&&
			isset($_REQUEST['plugins'])
			&&
			$_REQUEST['plugins']
		) {
			$args['plugins'] = $_REQUEST['plugins'];
		}

		$this->has_streaming = $args['has_streaming'];
		$this->plugins = $args['plugins'];
	}

	public function import() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->start_streaming();

			if (! current_user_can('edit_theme_options')) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'complete',
					'error' => false,
				]);
				exit;
			}

			if (! isset($_REQUEST['plugins']) || !$_REQUEST['plugins']) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'complete',
					'error' => false,
				]);
				exit;
			}
		}

		$plugins = explode(':', $this->plugins);

		foreach ($plugins as $single_plugin) {
			if ($single_plugin === 'woocommerce') {
				if (empty(get_option('woocommerce_db_version'))) {
					update_option('woocommerce_db_version', '0.0.0');
				}
			}

			if ($single_plugin === 'stackable-ultimate-gutenberg-blocks') {
				$stackable_pro_status = $this->get_plugin_status(
					'stackable-ultimate-gutenberg-blocks-premium'
				);

				if ($stackable_pro_status === 'active') {
					continue;
				}
			}

			if ($this->has_streaming) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'install_plugin',
					'name' => $single_plugin
				]);
			}

			$this->prepare_install($single_plugin);

			echo $single_plugin;

			if ($this->has_streaming) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'activate_plugin',
					'name' => $single_plugin
				]);
			}

			$this->plugin_activation($single_plugin);
		}

		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => false,
			]);

			exit;
		}
	}

	public function get_plugins_api($slug) {
		static $api = []; // Cache received responses.

		if (! isset($api[$slug])) {
			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			$response = plugins_api(
				'plugin_information',
				[
					'slug' => $slug,
					'fields' => [
						'sections' => false,
					],
				]
			);

			$api[$slug] = false;

			if (is_wp_error($response)) {
			} else {
				$api[$slug] = $response;
			}
		}

		return $api[$slug];
	}

	/**
	 * Wrapper around the core WP get_plugins function,
	 * making sure it's actually available.
	 */
	public function get_installed_plugins($plugin_folder = '') {
		// https://github.com/WordPress/WordPress/blob/ba92ed7615dec870a363bc99d6668faedfa77415/wp-admin/includes/plugin.php#L2254
		wp_cache_delete('plugins', 'plugins');

		if (! function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins($plugin_folder);
	}

	public function is_plugin_installed($slug) {
		$installed_plugins = $this->get_installed_plugins();

		foreach ($installed_plugins as $plugin => $data) {
			$parts = explode('/', $plugin);
			$plugin_first_part = $parts[0];

			if (strtolower($slug) === strtolower($plugin_first_part)) {
				return $plugin;
			}
		}

		return false;
	}

	public function can($capability = 'install_plugins') {
		if (defined('WP_CLI') && WP_CLI) {
			return true;
		}

		if (is_multisite()) {
			// Only network admin can change files that affects the entire network.
			$can = current_user_can_for_blog(get_current_blog_id(), $capability);
		} else {
			$can = current_user_can($capability);
		}

		if ($can) {
			// Also you can use this method to get the capability.
			$can = $capability;
		}

		return $can;
	}

	protected function require_wp_headers() {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if (! class_exists('Plugin_Upgrader', false)) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		if (! class_exists('\Blocksy\Blocksy_WP_Upgrader_Skin')) {
			require_once dirname( __FILE__ ) . '/upgrader-skin.php';
		}
	}

	public function prepare_install($plugin) {
		if (! $this->can()) {
			return false;
		}

        return $this->download_and_install($plugin);
	}

	public function has_direct_access( $context = null ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		/** @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ($wp_filesystem) {
			if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				return false;
			} else {
				return $wp_filesystem->method === 'direct';
			}
		}

		if (get_filesystem_method([], $context) === 'direct') {
			ob_start();

			{
				$creds = request_filesystem_credentials( admin_url(), '', false, $context, null );
			}

			ob_end_clean();

			if (WP_Filesystem($creds)) {
				return true;
			}
		}

		return false;
	}

	public function is_plugin_active($plugin) {
		if (! function_exists('activate_plugin')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active($plugin);
	}

	public function plugin_activation($plugin) {
		$full_name = $this->is_plugin_installed($plugin);

		if ($full_name) {
			if (! $this->is_plugin_active($full_name)) {
				return activate_plugin($full_name, '', false, true);
			}
		}

		return new \WP_Error();
	}

    public function get_plugin_status($slug) {
		$full_name = $this->is_plugin_installed( $slug );

		if (! $full_name) {
			return 'uninstalled';
		}

		if (! $this->is_plugin_active($full_name)) {
			return 'installed';
		}

		return 'active';
	}

	public function download_and_install($slug) {
		$this->require_wp_headers();

		if ($this->is_plugin_installed($slug)) {
			return true;
		}

		$api = $this->get_plugins_api($slug);

		if (! isset($api->download_link)) {
			return true;
		}

		// Prep variables for Plugin_Installer_Skin class.
		$source = $api->download_link;

		if (! $source) {
			return false;
		}

		$skin = new Blocksy_WP_Upgrader_Skin();

		// Create a new instance of Plugin_Upgrader.
		$upgrader = new \Plugin_Upgrader($skin);

		$res = $upgrader->fs_connect([WP_CONTENT_DIR, WP_PLUGIN_DIR]);

		if (! $res) {
			return false;
		}

		$upgrader->install($source);
	}

	public function init_filesystem() {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}
}

