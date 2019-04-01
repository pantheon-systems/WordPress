<?php

class AS3CF_Pro_Plugin_Installer {

	/**
	 * @var string
	 */
	public $process_key;

	/**
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * @var string
	 */
	protected $plugin_file_path;

	/**
	 * @var array
	 */
	protected $plugins_to_install;

	/**
	 * @var array
	 */
	protected $installer_notices;

	/**
	 * @var string
	 */
	protected $installer_action = 'as3cfpro-install-plugins';

	/**
	 * AS3CF_Pro_Plugin_Installer constructor.
	 *
	 * @param string $process_key
	 * @param string $plugin_slug
	 * @param string $plugin_file_path
	 */
	public function __construct( $process_key, $plugin_slug, $plugin_file_path ) {
		$this->process_key      = $process_key;
		$this->plugin_slug      = $plugin_slug;
		$this->plugin_file_path = $plugin_file_path;

		add_action( 'wp_ajax_as3cfpro_install_plugins_' . $this->process_key, array( $this, 'ajax_install_plugins' ) );
		add_action( 'admin_init', array( $this, 'maybe_install_plugins' ) );
		add_action( 'admin_init', array( $this, 'installer_redirect' ) );
		add_action( 'admin_notices', array( $this, 'maybe_display_installer_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'maybe_display_installer_notices' ) );
	}

	/**
	 * Set the plugins to be installed
	 *
	 * @param array $plugins_to_install
	 */
	public function set_plugins_to_install( $plugins_to_install ) {
		set_site_transient( 'as3cfpro_plugins_to_install_' . $this->process_key, $plugins_to_install );
	}

	/**
	 * Get the plugins to be installed
	 *
	 */
	public function get_plugins_to_install() {
		return get_site_transient( 'as3cfpro_plugins_to_install_' . $this->process_key );
	}

	/**
	 * Load the scripts and styles required for the plugin installer
	 */
	function load_installer_assets() {
		$plugin_version = $GLOBALS['aws_meta'][ $this->plugin_slug ]['version'];
		$version        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $plugin_version;
		$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$src = plugins_url( 'assets/js/pro/installer' . $suffix . '.js', $this->plugin_file_path );
		wp_enqueue_script( 'as3cf-pro-installer', $src, array( 'jquery', 'wp-util' ), $version, true );

		$script_args = array(
			'strings' => array(
				'installing'       => __( 'Installing', 'amazon-s3-and-cloudfront' ),
				'error_installing' => __( 'There was an error during the installation', 'amazon-s3-and-cloudfront' ),
			),
			'nonces'  => array(
				'install_plugins' => wp_create_nonce( 'install-plugins' ),
			),
		);

		wp_localize_script( 'as3cf-pro-installer', 'as3cfpro_installer', $script_args );

		// Load thickbox scripts and style so the links work on all pages in dashboard
		add_thickbox();
		wp_enqueue_script( 'plugin-install' );
	}

	/**
	 * Install and activate all required plugins
	 *
	 * @return bool|string|WP_Error
	 */
	function install_plugins() {
		global $as3cfpro_compat_check;
		if ( ! $as3cfpro_compat_check->check_capabilities() ) {
			return new WP_Error( 'exception', __( 'You do not have sufficient permissions to install plugins on this site.' ) );
		}

		$this->installer_notices = array();
		$plugins_to_install      = $this->get_plugins_to_install();

		$plugins_activated = 0;
		foreach ( $plugins_to_install as $slug => $plugin ) {
			if ( $this->install_plugin( $slug ) ) {
				$plugins_activated++;
			}
		}

		set_site_transient( 'as3cfpro_installer_notices', $this->installer_notices, 30 );
		delete_site_transient( 'as3cfpro_plugins_to_install_' . $this->process_key );

		if ( $plugins_activated === count( $this->plugins_to_install ) ) {
			// All plugins installed and activated successfully
			$url = add_query_arg( array( 'as3cfpro-install' => 1 ), network_admin_url( 'plugins.php' ) );

			return esc_url_raw( $url );
		}

		return true;
	}

	/**
	 * Retry to install plugins if there has been a filesystem credential issue
	 */
	function maybe_install_plugins() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		global $pagenow;
		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) || $this->installer_action != $_GET['action'] ) {
			return;
		}

		check_admin_referer( $this->installer_action );

		$this->request_filesystem_credentials();

		$result = $this->install_plugins();

		$redirect = network_admin_url( 'plugins.php' );
		if ( ! is_wp_error( $result ) && $result !== true ) {
			$redirect = $result;
		}

		wp_redirect( $redirect );
		exit;
	}

	/**
	 * AJAX handler for installing the required plugins.
	 *
	 */
	public function ajax_install_plugins() {
		check_ajax_referer( 'install-plugins', 'nonce' );

		$response = array(
			'redirect' => network_admin_url( 'plugins.php' ), // redirect to the plugins page by default
		);

		$result = $this->install_plugins();

		if ( is_wp_error( $result ) ) {
			$response['error'] = $result->get_error_message();
			wp_send_json_error( $response );
		}

		if ( $result !== true ) {
			$response['redirect'] = $result;
		}

		wp_send_json_success( $response );
	}

	/**
	 * Redirect to the AWS or Offload Media page after successfully installing the plugins
	 */
	function installer_redirect() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		global $pagenow;
		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		if ( ! isset( $_GET['as3cfpro-install'] ) ) {
			return;
		}

		global $as3cf_compat_check;
		if ( ! $as3cf_compat_check->is_compatible( false ) ) {
			// Do not redirect if the pro plugin is not compatible
			return;
		}

		delete_site_transient( 'as3cfpro_installer_notices' );

		$url = add_query_arg(
			array( 'page' => 'amazon-s3-and-cloudfront' ),
			network_admin_url( is_multisite() ? 'settings.php' : 'options-general.php' )
		);

		wp_redirect( esc_url_raw( $url ) );
		exit();
	}

	/**
	 * Install and activate a plugin
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	protected function install_plugin( $slug ) {
		$status = array( 'slug' => $slug );

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$api = plugins_api( 'plugin_information', array(
			'slug'   => $slug,
			'fields' => array( 'sections' => false ),
		) );

		if ( is_wp_error( $api ) ) {
			$status['error'] = $api->get_error_message();
			$this->end_install( $status );
		}

		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			$status['error'] = $result->get_error_message();
			$this->end_install( $status );

			return false;
		} else if ( is_null( $result ) ) {
			$status['error'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

			$this->installer_notices['filesystem_error'] = true;
			$this->end_install( $status );

			return false;
		}

		$installed_plugin = get_plugins( '/' . $slug );

		if ( ! empty( $installed_plugin ) ) {
			$key  = array_keys( $installed_plugin );
			$key  = reset( $key );
			$file = $slug . '/' . $key;

			$network_wide = is_multisite();
			$activated    = activate_plugin( $file, '', $network_wide );
		} else {
			$activated = false;
		}

		$plugin_activated = false;
		if ( false === $activated || is_wp_error( $activated ) ) {
			$warning = ' ' . __( 'but not activated', 'amazon-s3-and-cloudfront' );
			if ( is_wp_error( $activated ) ) {
				$warning .= ': ' . $activated->get_error_message();
			}

			$status['warning'] = $warning;
		} else {
			$plugin_activated = true;
		}

		$this->end_install( $status );

		return $plugin_activated;
	}

	/**
	 * Add the outcome of the install to the installer notices array to be set in a transient
	 *
	 * @param array $status
	 */
	protected function end_install( $status ) {
		$plugins = $this->get_plugins_to_install();

		$class   = 'updated';
		$message = sprintf( __( '%s installed successfully', 'amazon-s3-and-cloudfront' ), $plugins[ $status['slug'] ] );
		if ( isset( $status['error'] ) ) {
			$class   = 'error';
			$message = sprintf( __( '%s not installed', 'amazon-s3-and-cloudfront' ), $plugins[ $status['slug'] ] );
			$message .= ': ' . $status['error'];
		}

		if ( isset( $status['warning'] ) ) {
			$message .= $status['warning'];
		}

		$this->installer_notices['notices'][] = array( 'message' => $message, 'class' => $class );
	}

	/**
	 * Get the request filesystem credentials form
	 *
	 * @return string Form HTML
	 */
	protected function request_filesystem_credentials() {
		$url = wp_nonce_url( 'plugins.php?action=' . $this->installer_action, $this->installer_action );
		ob_start();
		request_filesystem_credentials( $url );
		$data = ob_get_contents();
		ob_end_clean();

		return $data;
	}

	/**
	 * Display plugin installer notices
	 */
	public function maybe_display_installer_notices() {
		if ( false === ( $notices = get_site_transient( 'as3cfpro_installer_notices' ) ) ) {
			return;
		}

		if ( ! isset( $notices['notices'] ) ) {
			return;
		}

		global $as3cf_compat_check;
		if ( ! $as3cf_compat_check->check_capabilities() ) {
			// User can't install plugins anyway, bail.
			return;
		}

		foreach ( $notices['notices'] as $notice ) {
			print '<div class="as3cf-pro-installer-notice ' . $notice['class'] . '"><p>' . $notice['message'] . '</p></div>';
		}

		delete_site_transient( 'as3cfpro_installer_notices' );

		if ( isset( $notices['filesystem_error'] ) ) {
			$data = $this->request_filesystem_credentials();
			if ( ! empty( $data ) ) {
				echo '<div class="as3cfpro-installer-filesystem-creds">';
				echo $data;
				echo '</div>';
			}
		}
	}

}
