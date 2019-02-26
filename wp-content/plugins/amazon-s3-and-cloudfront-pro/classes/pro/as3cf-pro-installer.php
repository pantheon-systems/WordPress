<?php

class AS3CF_Pro_Installer extends AS3CF_Compatibility_Check {

	/**
	 * @var
	 */
	protected $required_plugins;

	/**
	 * @var AS3CF_Pro_Plugin_Installer
	 */
	protected $plugin_installer;

	/**
	 * AS3CF_Pro_Installer constructor.
	 *
	 * @param $plugin_file_path
	 */
	public function __construct( $plugin_file_path ) {
		parent::__construct(
			'WP Offload Media',
			'amazon-s3-and-cloudfront-pro',
			$plugin_file_path
		);

		// Fire up the plugin installer
		if ( is_admin() ) {
			$this->plugin_installer = new AS3CF_Pro_Plugin_Installer( 'installer', $this->plugin_slug, $this->plugin_file_path );
			$this->plugin_installer->set_plugins_to_install( $this->required_plugins_not_installed() );
		}
	}

	/**
	 * Are all the required plugins installed?
	 *
	 * @return bool
	 */
	public function is_setup() {
		$plugins_not_installed = $this->required_plugins_not_installed();

		return empty( $plugins_not_installed );
	}

	/**
	 * Are all the required plugins activated?
	 *
	 * @return bool
	 */
	public function are_required_plugins_activated() {
		$plugins_not_activated = $this->required_plugins_not_activated();

		return empty( $plugins_not_activated );
	}

	/**
	 * If the plugin is setup use the default compatible check
	 *
	 * @param bool $installed Check to see if the plugins are installed
	 *
	 * @return bool
	 */
	function is_compatible( $installed = true ) {
		if ( $this->is_setup() || false === $installed ) {
			return parent::is_compatible();
		}

		return false;
	}

	/**
	 * The required plugins for this plugin
	 *
	 * @return array
	 */
	public function get_required_plugins() {
		if ( is_null( $this->required_plugins ) ) {
			$this->required_plugins = array(
			);
		}

		return $this->required_plugins;
	}

	/**
	 * Check if any of the required plugins are installed
	 *
	 * @return array
	 */
	public function required_plugins_not_installed() {
		$plugins          = array();
		$required_plugins = $this->get_required_plugins();

		foreach ( $required_plugins as $slug => $plugin ) {
			$filename = ( isset( $plugin['file'] ) ) ? $plugin['file'] : $slug;
			if ( ! class_exists( $plugin['class'] ) && ! file_exists( WP_PLUGIN_DIR . '/' . $slug . '/' . $filename . '.php' ) ) {
				$plugins[ $slug ] = $plugin['name'];
			}
		}

		return $plugins;
	}


	/**
	 * Check if any of the required plugins are activated
	 *
	 * @return array
	 */
	public function required_plugins_not_activated() {
		$plugins          = array();
		$required_plugins = $this->get_required_plugins();

		foreach ( $required_plugins as $slug => $plugin ) {
			if ( ! class_exists( $plugin['class'] ) ) {
				$plugins[ $slug ] = $plugin['name'];
			}
		}

		return $plugins;
	}

	/**
	 * Generate the plugin info URL for thickbox
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	function get_plugin_info_url( $slug ) {
		return self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $slug . '&amp;TB_iframe=true&amp;width=600&amp;height=800' );
	}

	/**
	 * Display a custom install notice if the plugin is not setup
	 */
	function get_admin_notice() {
		$plugins_not_installed = $this->required_plugins_not_installed();
		if ( empty( $plugins_not_installed ) ) {
			parent::get_admin_notice();

			return;
		}

		$this->plugin_installer->load_installer_assets();

		if ( $notices = get_site_transient( 'as3cfpro_installer_notices' ) ) {
			if ( isset( $notices['filesystem_error'] ) ) {
				// Don't show the installer notice if we have filesystem credential issues
				return;
			}
		}

		$install_notice = untrailingslashit( plugin_dir_path( $this->plugin_file_path ) ) . '/view/pro/install-notice.php';
		include $install_notice;
	}
}