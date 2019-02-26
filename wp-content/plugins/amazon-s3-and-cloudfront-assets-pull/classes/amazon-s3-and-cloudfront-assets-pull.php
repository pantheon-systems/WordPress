<?php

use DeliciousBrains\WP_Offload_Media_Assets_Pull\Addon_Activation_Data;
use DeliciousBrains\WP_Offload_Media_Assets_Pull\Domain_Check;
use DeliciousBrains\WP_Offload_Media_Assets_Pull\Domain_Check_Controller;
use DeliciousBrains\WP_Offload_Media_Assets_Pull\Domain_Check_Response;
use DeliciousBrains\WP_Offload_Media_Assets_Pull\Exceptions\Domain_Check_Exception;

class Amazon_S3_And_CloudFront_Assets_Pull extends Amazon_S3_And_CloudFront_Pro {

	protected $plugin_slug = 'amazon-s3-and-cloudfront-assets-pull';
	protected $plugin_prefix = 'as3cf_assets_pull';
	protected $default_tab = 'assets_pull';

	/** @var Domain_Check_Controller */
	protected $domain_check_controller;

	/* @var Addon_Activation_Data */
	protected $addon_data;

	const SETTINGS_KEY = 'as3cf_assets_pull';

	/**
	 * @var array
	 */
	protected static $settings_constants = array(
		'AS3CF_ASSETS_PULL_SETTINGS',
		'WPOS3_ASSETS_PULL_SETTINGS',
	);

	/**
	 * Plugin initialization
	 *
	 * @param string $plugin_file_path
	 */
	public function init( $plugin_file_path ) {
		$this->addon_data              = new Addon_Activation_Data( 'assets_pull' );
		$this->domain_check_controller = new Domain_Check_Controller();

		$this->register_text_domain();

		add_action( 'rest_api_init', array( $this->domain_check_controller, 'register_routes' ) );
		add_filter( 'wp_resource_hints', array( $this, 'register_resource_hints' ), 10, 2 );

		// UI Setup filters
		add_action( 'as3cf_plugin_load', array( $this, 'load_settings_page' ) );
		add_filter( 'as3cf_settings_tabs', array( $this, 'settings_tabs' ) );
		add_action( 'as3cf_after_settings', array( $this, 'settings_page' ) );
		add_filter( 'as3cf_diagnostic_info', array( $this, 'diagnostic_info' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'display_activated_notice' ) );
		add_action( 'network_admin_notices', array( $this, 'display_activated_notice' ) );

		// Ajax
		add_action( 'wp_ajax_' . $this->action_key( 'check_assets_domain' ), array( $this, 'ajax_check_assets_domain' ) );
		add_action( 'wp_ajax_' . $this->action_key( 'dismiss_activated_notice' ), array( $this, 'ajax_dismiss_activated_notice' ) );

		// Serve files
		add_filter( 'style_loader_src', array( $this, 'rewrite_src' ), 10, 2 );
		add_filter( 'script_loader_src', array( $this, 'rewrite_src' ), 10, 2 );
		add_filter( 'as3cf_get_asset', array( $this, 'rewrite_src' ) );
	}

	/**
	 * Rewrite an asset's src.
	 *
	 * @param string      $src
	 * @param string|null $handle
	 *
	 * @return string
	 */
	public function rewrite_src( $src, $handle = null ) {
		if ( ! $this->should_rewrite_urls() ) {
			return $src;
		}

		if ( ! static::should_rewrite_src( $src, $handle ) ) {
			return $src;
		}

		return $this->rewrite_url( $src, $handle );
	}

	/**
	 * Rewrite a URL to use the asset's domain and scheme.
	 *
	 * @param string      $url
	 * @param string|null $handle
	 *
	 * @return string
	 */
	protected function rewrite_url( $url, $handle = null ) {
		$rewritten = 'http://' . $this->get_setting( 'domain' );
		$rewritten .= AS3CF_Utils::parse_url( $url, PHP_URL_PATH );
		$query     = AS3CF_Utils::parse_url( $url, PHP_URL_QUERY );
		$rewritten .= $query ? ( '?' . $query ) : '';
		$scheme    = $this->get_setting( 'force-https' ) ? 'https' : null;

		/**
		 * @param string|null $scheme
		 * @param string      $url
		 * @param string      $handle
		 */
		$scheme = apply_filters( 'as3cf_assets_pull_scheme', $scheme, $url, $handle );

		return set_url_scheme( $rewritten, $scheme );
	}

	/**
	 * Ajax handler for checking pull domain configuration.
	 */
	public function ajax_check_assets_domain() {
		check_ajax_referer( $this->action_key( 'check_assets_domain' ) );

		$domain = filter_input( INPUT_POST, 'domain' );
		$check  = new Domain_Check( $domain );

		if ( filter_input( INPUT_POST, 'save_domain' ) ) {
			$this->update_domain( $domain );
		}

		try {
			$this->run_domain_check( $check );
		} catch ( Exception $e ) {
			$this->send_domain_check_failure( $e, $check );
		}

		$this->send_domain_check_success( $check );
	}

	/**
	 * Send a successful response for the given checked domain.
	 *
	 * @param Domain_Check $check
	 */
	protected function send_domain_check_success( $check ) {
		$response = $result = array(
			'domain' => $check->domain,
		);

		$result['timestamp'] = current_time( 'timestamp' );
		$this->save_domain_check_result( true, $result );

		$response['last_checked_at'] = self::last_checked_datetime( $result['timestamp'] );
		wp_send_json_success( $response );
	}

	/**
	 * Prepare and send a response for a failed domain configuration check.
	 *
	 * @param Exception    $exception
	 * @param Domain_Check $check
	 */
	protected function send_domain_check_failure( $exception, $check ) {
		AS3CF_Error::log( $exception->getMessage(), 'Assets Domain Check Error' );

		$response = $result = array(
			'domain'    => $check->domain,
			'message'   => $exception->getMessage(),
			'more_info' => $this->domain_check_more_info( $exception ),
		);

		$result['timestamp'] = current_time( 'timestamp' );
		$this->save_domain_check_result( false, $result );

		$response['last_checked_at'] = self::last_checked_datetime( $result['timestamp'] );
		wp_send_json_error( $response );
	}

	/**
	 * Save the result of a domain check.
	 *
	 * @param       $success
	 * @param array $data
	 */
	protected function save_domain_check_result( $success, $data = array() ) {
		$record = array_merge( $data, array(
			'success' => (bool) $success,
		) );

		set_site_transient( 'as3cf_assets_pull_last_checked', $record );
	}

	/**
	 * Execute the given domain check.
	 *
	 * @param Domain_Check $check
	 *
	 * @throws Exception
	 */
	protected function run_domain_check( $check ) {
		$test_time = microtime();
		$test_key  = base64_encode( $test_time );

		$this->test_assets_endpoint( $check, $test_key, $test_time );
	}

	/**
	 * Send a request to the test endpoint and make assertions about the response.
	 *
	 * @param Domain_Check $check
	 * @param string       $key
	 * @param string       $ver
	 *
	 * @throws Exception
	 */
	protected function test_assets_endpoint( $check, $key, $ver ) {
		$test_endpoint = $this->domain_check_controller->show_url( $key );
		$test_endpoint = add_query_arg( compact( 'ver' ), $test_endpoint );
		$test_endpoint = $this->rewrite_url( $test_endpoint );
		$response      = $check->test_endpoint( $test_endpoint );

		$expected = new Domain_Check_Response( compact( 'key', 'ver' ) );
		$expected->verify_signature( wp_remote_retrieve_header( $response, 'x-as3cf-signature' ) );
	}

	/**
	 * Perform any plugin settings page-specific actions before it is loaded.
	 */
	public function load_settings_page() {
		$this->enqueue_style( 'as3cf-assets-pull-styles', 'assets/css/styles', array( 'as3cf-styles' ) );
		$this->enqueue_script( 'as3cf-assets-pull-script', 'assets/js/script', array( 'jquery', 'underscore' ) );

		if ( ! $this->get_setting( 'domain' ) ) {
			delete_site_transient( 'as3cf_assets_pull_last_checked' );
		}

		wp_localize_script( 'as3cf-assets-pull-script', 'as3cf_assets_pull', array(
			'strings'       => array(
				'next_prefix'      => _x( 'Next:', 'setup wizard', 'amazon-s3-and-cloudfront-assets-pull' ),
				'previous_step'    => _x( 'Back to Previous Step', 'setup wizard', 'amazon-s3-and-cloudfront-assets-pull' ),
				'skip_to_settings' => _x( 'Skip to Settings', 'setup wizard', 'amazon-s3-and-cloudfront-assets-pull' ),
				'complete_setup'   => _x( 'Save and Complete Set Up', 'setup wizard', 'amazon-s3-and-cloudfront-assets-pull' ),
				'invalid_domain'   => _x( 'Invalid domain. Domain names can only contain letters, numbers, hyphens (-), and periods (.).', 'validation', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			'settings'      => $this->get_settings(),
			'domain_status' => get_site_transient( 'as3cf_assets_pull_last_checked' ),
			'actions'       => array(
				'check_assets_domain' => $this->action_key( 'check_assets_domain' ),
			),
			'nonces'        => array(
				'check_assets_domain' => $this->create_nonce( 'check_assets_domain' ),
			),
			'wizard'        => array(
				'cloudfront' => $this->get_cloudfront_setup_steps(),
			),
		) );

		$this->handle_post_request();
	}

	/**
	 * Register the text domain with WordPress for translations.
	 */
	public function register_text_domain() {
		load_plugin_textdomain( 'amazon-s3-and-cloudfront-assets-pull', false, dirname( plugin_basename( $this->plugin_file_path ) ) . '/languages/' );
	}

	/**
	 * Register a DNS prefetch tag for the pull domain if rewriting is enabled.
	 *
	 * @param array  $hints
	 * @param string $relation_type
	 *
	 * @return array
	 */
	public function register_resource_hints( $hints, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type && $this->should_rewrite_urls() ) {
			$hints[] = '//' . $this->get_setting( 'domain' );
		}

		return $hints;
	}

	/**
	 * Add the Assets Pull tab to the settings tabs.
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function settings_tabs( $tabs ) {
		$new_tabs = array();

		foreach ( $tabs as $slug => $tab ) {
			$new_tabs[ $slug ] = $tab;

			if ( 'media' === $slug ) {
				$new_tabs['assets_pull'] = _x( 'Assets', 'Show the Assets settings tab', 'amazon-s3-and-cloudfront-assets-pull' );
			}
		}

		return $new_tabs;
	}

	/**
	 * Display the settings page content.
	 */
	public function settings_page() {
		$this->render_view( 'settings', array(
			'plugin_slug'        => $this->plugin_slug,
			'settings_nonce_key' => $this->get_settings_nonce_key(),
		) );
	}

	/**
	 * Renders a view and returns the output.
	 *
	 * @param       $view
	 * @param array $args
	 *
	 * @return string
	 */
	public function capture_view( $view, $args = array() ) {
		ob_start();
		$this->render_view( $view, $args );

		return ob_get_clean();
	}

	/**
	 * Render a view template file specific to child class
	 * or use parent view as a fallback
	 *
	 * @param string $view View filename without the extension
	 * @param array  $args Arguments to pass to the view
	 */
	public function render_view( $view, $args = array() ) {
		global $as3cfpro;

		$view_paths = array(
			$this->plugin_dir_path . '/view/' . $view . '.php',
			$as3cfpro->plugin_dir_path . '/view/pro/' . $view . '.php',
			$as3cfpro->plugin_dir_path . '/view/' . $view . '.php',
		);

		foreach ( $view_paths as $view_file ) {
			if ( file_exists( $view_file ) ) {
				extract( $args, EXTR_OVERWRITE );
				include $view_file;
				break;
			}
		}
	}

	/**
	 * Render an individual setting's view partial.
	 *
	 * @param       $setting_key
	 * @param array $data
	 */
	public function render_setting( $setting_key, $data = array() ) {
		$setting_value = $this->get_setting( $setting_key );
		$args          = $this->get_setting_args( $setting_key );
		$this->render_view(
			"setting/$setting_key",
			array_merge( compact( 'setting_key', 'setting_value', 'args' ), $data )
		);
	}

	/**
	 * Get a URL to a step media file.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function get_step_media_url( $path ) {
		$rel_path      = '/view/steps/media/' . ltrim( $path, '\\/' );
		$url           = plugins_url( $rel_path, $this->plugin_file_path );
		$versioned_url = add_query_arg( array(
			'v' => md5_file( $this->plugin_dir_path . $rel_path ),
		), $url );

		return set_url_scheme( $versioned_url, 'relative' );
	}

	/**
	 * Assets more info link
	 *
	 * @param string $hash
	 * @param string $utm_content
	 *
	 * @return string
	 */
	public function assets_more_info_link( $hash, $utm_content = '' ) {
		return $this->more_info_link( '/wp-offload-media/doc/assets-pull-addon-settings/', $utm_content, $hash );
	}

	/**
	 * Accessor for a plugin setting with conditions to defaults and upgrades
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_setting( $key, $default = '' ) {
		$settings = $this->get_settings();

		$value = isset( $settings[ $key ] ) ? $settings[ $key ] : $default;

		if ( 'rewrite-urls' == $key && ! isset( $settings[ $key ] ) ) {
			$value = 0;
		}

		if ( 'force-https' == $key && ! isset( $settings[ $key ] ) ) {
			$value = 0;
		}

		return apply_filters( 'as3cf_assets_pull_setting_' . $key, $value );
	}

	/**
	 * Get a nonce for the given primitive action.
	 *
	 * @param string $action The unprefixed action key.
	 *
	 * @return string
	 */
	protected function create_nonce( $action ) {
		return wp_create_nonce( $this->action_key( $action ) );
	}

	/**
	 * Get a namespaced action key for this plugin.
	 *
	 * @param string $action The unprefixed action key
	 *
	 * @return string
	 */
	protected function action_key( $action ) {
		return "{$this->plugin_prefix}_{$action}";
	}

	/**
	 * Get step configuration for CloudFront setup wizard.
	 *
	 * @return array
	 */
	protected function get_cloudfront_setup_steps() {
		$steps = array(
			array(
				'id'             => 'start',
				'title'          => __( 'Getting Started', 'amazon-s3-and-cloudfront-assets-pull' ),
				'next_step_text' => __( 'Get Started', 'amazon-s3-and-cloudfront-assets-pull' ),
				'icon_url'       => plugins_url( 'assets/img/icon-assets.svg', $this->plugin_file_path ),
			),
			array(
				'id'    => 'configure_domain',
				'title' => __( 'Configuring Your Custom Domain', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'acm_create_https_cert',
				'title' => __( 'Creating an HTTPS Certificate', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'acm_request_cert',
				'title' => __( 'Requesting a Certificate', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'acm_email_validation',
				'title' => __( 'Email Validation', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'acm_cert_approved',
				'title' => __( 'Certificate Approved', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'create_distribution',
				'title' => __( 'Creating a CloudFront Distribution', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'origin_settings',
				'title' => __( 'Origin Settings', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'default_cache_behavior',
				'title' => __( 'Default Cache Behavior Settings', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'distribution_settings',
				'title' => __( 'Distribution Settings', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'cloudfront_domain',
				'title' => __( 'CloudFront Domain', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'cname_setup',
				'title' => __( 'CNAME Setup', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
			array(
				'id'    => 'cors_configuration',
				'title' => __( 'CORS Configuration', 'amazon-s3-and-cloudfront-assets-pull' ),
			),
		);

		foreach ( $steps as &$step ) {
			$step['html'] = $this->capture_view( "steps/{$step['id']}", $step );
		}

		$initial_setup_step = false === $this->get_settings() ? 'start' : '';

		return array(
			'steps'             => $steps,
			'launch_on_load'    => filter_input( INPUT_GET, 'cloudfront_setup_step' ) ?: $initial_setup_step,
			'completed_message' => __(
				"🎉 Woohoo! You've completed your Amazon CloudFront setup. " .
				"We're checking if your assets are ready to serve below. " .
				"If it fails, your DNS likely hasn't propagated yet and you should check again in a few minutes. " .
				"Once you get the green check, you can turn on the Rewrite Asset URLs setting and save to start serving your assets. " .
				"If you have page caching on your site, don't forget to purge the whole cache. ",
				'amazon-s3-and-cloudfront-assets-pull'
			),
		);
	}

	/**
	 * Whether or not asset URL rewriting should be performed.
	 *
	 * @return bool
	 */
	public function should_rewrite_urls() {
		if ( ! $this->get_setting( 'rewrite-urls' ) ) {
			return false;
		}

		if ( ! Domain_Check::is_valid( $this->get_setting( 'domain' ) ) ) {
			return false;
		}

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return apply_filters( 'as3cf_assets_enable_wp_admin_rewrite', false );
		}

		return true;
	}

	/**
	 * Whether or not a URL should be rewritten for the given asset.
	 *
	 * @param string      $src    The asset URL to be rewritten.
	 * @param string|null $handle The asset's registered handle in the WordPress enqueue system.
	 *
	 * @return bool
	 */
	public static function should_rewrite_src( $src, $handle = null ) {
		if ( AS3CF_Utils::is_relative_url( $src ) ) {
			$rewrite = true;
		} elseif ( AS3CF_Utils::url_domains_match( $src, home_url() ) ) {
			$rewrite = true;
		} else {
			$rewrite = false;
		}

		/**
		 * @param bool        $rewrite Whether or not the src should be rewritten.
		 * @param string      $src     The asset URL to be rewritten.
		 * @param string|null $handle  The asset's registered handle in the WordPress enqueue system.
		 */
		return apply_filters( 'as3cf_assets_should_rewrite_src', $rewrite, $src, $handle );
	}

	/**
	 * Whitelisted settings keys for this addon.
	 *
	 * @return array
	 */
	public function get_settings_whitelist() {
		return array(
			'rewrite-urls',
			'domain',
			'force-https',
		);
	}

	/**
	 * Filter in defined settings with sensible defaults.
	 *
	 * @param array $settings
	 *
	 * @return array $settings
	 */
	public function filter_settings( $settings ) {
		$defined_settings = $this->get_defined_settings();

		// Bail early if there are no defined settings
		if ( empty( $defined_settings ) ) {
			return $settings;
		}

		$checkboxes = array(
			'rewrite-urls',
			'force-https',
		);

		foreach ( $defined_settings as $key => $value ) {
			$allowed_values = array();

			if ( in_array( $key, $checkboxes ) ) {
				$allowed_values = array( '0', '1' );
			}

			// Unexpected value, remove from defined_settings array.
			if ( ! empty( $allowed_values ) && ! in_array( $value, $allowed_values ) ) {
				$this->remove_defined_setting( $key );
				continue;
			}

			// Value defined successfully
			$settings[ $key ] = $value;
		}

		return $settings;
	}

	/**
	 * Addon specific diagnostic info.
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	public function diagnostic_info( $output = '' ) {
		$output .= 'Assets Pull Addon:';
		$output .= "\r\n";

		$output .= 'Rewrite URLs: ';
		$output .= $this->on_off( 'rewrite-urls' );
		$output .= "\r\n";

		$output .= 'Domain: ';
		$output .= esc_html( $this->get_setting( 'domain' ) );
		$output .= "\r\n";

		$output .= 'Force HTTPS: ';
		$output .= $this->on_off( 'force-https' );
		$output .= "\r\n";

		$output .= 'Domain Check: ';
		$output .= $this->diagnostic_domain_check();
		$output .= "\r\n";

		return $output;
	}

	/**
	 * Run a domain check on the configured domain for the diagnostic information.
	 *
	 * @return string
	 */
	protected function diagnostic_domain_check() {
		$domain = $this->get_setting( 'domain' );

		if ( ! $domain ) {
			return '(no domain)';
		}

		try {
			$this->run_domain_check( new Domain_Check( $domain ) );
		} catch ( Exception $e ) {
			return $e->getMessage();
		}

		return 'OK';
	}

	/**
	 * Get UTM source for plugin.
	 *
	 * @return string
	 */
	protected function get_utm_source() {
		return 'OS3+Assets+Pull';
	}

	/**
	 * Build a "More info" link for a domain check error message.
	 *
	 * @param string|Exception $message
	 *
	 * @return string
	 */
	protected function domain_check_more_info( $message ) {
		$exception = $message;

		if ( $message instanceof Exception ) {
			$message = $exception->getMessage();
		}

		$more_info_params = array(
			'utm_content' => 'assets+domain+check',
		);

		if ( $exception instanceof Domain_Check_Exception ) {
			return $this->dbrains_url( $exception->more_info(), $more_info_params, $exception->get_key() );
		}

		$more_info_params['swpquery'] = urlencode( $message );

		return $this->dbrains_url( '/wp-offload-media/doc/', $more_info_params );
	}

	/**
	 * Update the saved domain.
	 *
	 * @param string $domain
	 */
	protected function update_domain( $domain ) {
		$this->set_setting( 'domain', $domain );
		$this->save_settings();
	}

	/**
	 * Display the activated notice if necessary.
	 */
	public function display_activated_notice() {
		if ( ! $this->should_show_activated_notice() ) {
			return;
		}

		$notice_id = 'as3cf_assets_pull_activated';
		$message   = '<strong>Assets Pull Addon for WP Offload Media</strong> &mdash; ';
		$message   .= sprintf(
			__( "Now that you've activated, <a href='%s'>get started with set up</a>.", 'amazon-s3-and-cloudfront-assets-pull' ),
			$this->get_plugin_page_url( array( 'cloudfront_setup_step' => 'start' ) )
		);
		$message   .= $this->capture_view( 'script/inline-dismiss', array(
			'localize_data' => array(
				'container_selector' => '#' . $notice_id,
				'action'             => $this->action_key( 'dismiss_activated_notice' ),
				'nonce'              => $this->create_nonce( 'dismiss_activated_notice' ),
			),
		) );

		$this->render_view( 'notice', array(
			'message'     => $message,
			'type'        => 'notice-success',
			'id'          => $notice_id,
			'dismissible' => true,
		) );
	}

	/**
	 * Whether or not the activation notice should be shown for this addon.
	 *
	 * @return bool
	 */
	public function should_show_activated_notice() {
		$screen = get_current_screen();

		// Don't show the notice if we loaded the plugin settings screen with a wizard step.
		if ( $screen->id === $GLOBALS['as3cf']->hook_suffix && filter_input( INPUT_GET, 'cloudfront_setup_step' ) ) {
			return false;
		}

		return current_user_can( 'activate_plugins' ) && $this->addon_data->activated_within( 15 * MINUTE_IN_SECONDS );
	}

	/**
	 * Helper function for formatting the last checked at date time for the given timestamp.
	 *
	 * @param $timestamp
	 *
	 * @return false|string
	 */
	public static function last_checked_datetime( $timestamp ) {
		return date( _x( 'Y-m-d H:i:s', 'last checked datetime', 'amazon-s3-and-cloudfront-assets-pull' ), $timestamp ) . ' ' . get_site_option( 'timezone_string' );
	}

	/**
	 * Ajax handler to dismiss the plugin activated notice.
	 */
	public function ajax_dismiss_activated_notice() {
		check_ajax_referer( $this->action_key( 'dismiss_activated_notice' ) );

		$this->addon_data->update( 'activated_at', null );

		wp_send_json_success();
	}

	/**
	 * Whether or not this addon was recently activated
	 *
	 * @return bool
	 */
	protected function is_just_activated() {
		if ( $this->addon_data->activated_within( 15 * MINUTE_IN_SECONDS ) ) {
			return true;
		}

		return false;
	}
}
