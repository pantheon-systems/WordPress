<?php

class Blocksy_Plugin_Manager {
	protected $config = [];

	public function __construct() {
		$this->load_config();
	}

	public function get_config() {
		return $this->config;
	}

	protected function load_config() {
		$this->config = blocksy_akg(
			'plug',
			blocksy_get_variables_from_file(
				dirname( __FILE__ ) . '/config.php',
				[ 'plug' => [] ]
			)
		);
	}

	public function get_plugins() {
		if (isset($this->config)) {
			return $this->config;
		}

		return [];
	}

	public function get_companion_status() {
		$free_status = $this->get_plugin_status('blocksy-companion');
		$status = $this->get_plugin_status('blocksy-companion-pro');

		if ($status !== 'uninstalled') {
			return [
				'slug' => 'blocksy-companion-pro',
				'status' => $status
			];
		}

		return [
			'slug' => 'blocksy-companion',
			'status' => $free_status
		];
	}

	public function get_plugins_api($slug) {
		static $api = []; // Cache received responses.

		if (! isset($api[$slug])) {
			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			require_once dirname(__FILE__) . '/ct-wp-upgrader-skin.php';

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

		if (! class_exists('Blocksy_WP_Upgrader_Skin', false)) {
			require_once dirname(__FILE__) . '/ct-wp-upgrader-skin.php';
		}
	}

	public function prepare_install($plugin) {
		if (! $this->can()) {
			return false;
		}

		$avaible_plugins = $this->get_plugins();

		if (! array_key_exists($plugin, $avaible_plugins)) {
			return $this->download_and_install($plugin);
		}

		$plugin_info = $avaible_plugins[ $plugin ];

		if ( 'premium' === $plugin_info['type'] ) {
			return $this->download_and_install_premium_plugin( $plugin );
		}

		if ( 'web' === $plugin_info['type'] ) {
			return $this->download_and_install( $plugin );
		}
	}

	public function has_direct_access( $context = null ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		/** @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ( $wp_filesystem ) {
			if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				return false;
			} else {
				return $wp_filesystem->method === 'direct';
			}
		}

		if ( get_filesystem_method( [], $context ) === 'direct' ) {
			ob_start();

			{
				$creds = request_filesystem_credentials( admin_url(), '', false, $context, null );
			}

			ob_end_clean();

			if ( WP_Filesystem( $creds ) ) {
				return true;
			}
		}

		return false;
	}

	public function is_plugin_active( $plugin ) {
		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( $plugin );
	}

	public function plugin_activation( $plugin ) {
		$full_name = $this->is_plugin_installed( $plugin );

		if ( $full_name ) {
			if ( ! $this->is_plugin_active( $full_name ) ) {
				return activate_plugin( $full_name, '', false, true );
			}
		}

		return new WP_Error();
	}

	public function plugin_deactivation( $plugin ) {
		$full_name = $this->is_plugin_installed( $plugin );

		if ( $full_name ) {
			if ( is_plugin_active( $full_name ) ) {
				return deactivate_plugins( $full_name );
			}
		}

		return new WP_Error();
	}

	public function uninstall_plugin( $plugin ) {
		$this->init_filesystem();
		$full_name = $this->is_plugin_installed( $plugin );

		if ( $full_name ) {
			if ( ! is_plugin_active( $full_name ) ) {
				return delete_plugins( [ $full_name ] );
			}
		}

		return new WP_Error();
	}

	public function get_premium_plugin_download_url( $slug ) {
		$opts = fw_akg( $slug . '/download', $this->get_plugins() );

		$manager = new CT_Validation_Transients_And_Options_Manager();
		$manager->reset_transient();

		$purchase_code = $manager->get_purchase_code();

		if ( ! $manager->is_activated() ) {
			return null;
		}

		return 'http://updates.creativethemes.com/releases/?' . http_build_query(
			[
				'purchase_code' => $purchase_code['purchase_code'],
				'domain' => $manager->get_home_domain(),
				'action' => 'download_release',
				'repo' => fw_akg( 'repo', $opts ),
				'user' => fw_akg( 'user', $opts ),
			]
		);
	}


    public function get_plugin_status($slug) {
		$full_name = $this->is_plugin_installed( $slug );

		if (!$full_name) {
			return 'uninstalled';
		}

		if (!$this->is_plugin_active($full_name)) {
			return 'installed';
		}

		return 'active';
	}

	public function download_and_install_premium_plugin( $slug ) {
		$this->require_wp_headers();

		if ( $this->is_plugin_installed( $slug ) ) {
			return true;
		}

		// Prep variables for Plugin_Installer_Skin class.
		$source = $this->get_premium_plugin_download_url( $slug );

		if ( ! $source ) {
			return false;
		}

		$skin = new Blocksy_WP_Upgrader_Skin();

		// Create a new instance of Plugin_Upgrader.
		$upgrader = new Plugin_Upgrader( $skin );

		$res = $upgrader->fs_connect( [ WP_CONTENT_DIR, WP_PLUGIN_DIR ] );

		if ( ! $res ) {
			return false;
		}

		$upgrader->install( $source );
	}

	public function download_and_install( $slug ) {
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
		$upgrader = new Plugin_Upgrader($skin);

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

