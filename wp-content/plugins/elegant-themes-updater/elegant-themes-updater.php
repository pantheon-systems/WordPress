<?php
/*
 * Plugin Name: Elegant Themes Updater
 * Plugin URI: http://elegantthemes.com
 * Description: Enables automatic updates for all Elegant Themes products
 * Version: 1.2
 * Author: Elegant Themes
 * Author URI: http://elegantthemes.com
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ET_Automatic_Updates {
	/**
	 * Self instance of the object
	 *
	 * @var object
	 */
	private static $_this;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	var $version = '1.2';

	/**
	 * Plugin options
	 *
	 * @var array
	 */
	var $options;

	/**
	 * User's account status
	 *
	 * @var string
	 */
	var $account_status;

	function __construct() {
		// Don't allow more than one instance of the class
		if ( isset( self::$_this ) ) {
			wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'et_automatic_updates' ),
				get_class( $this ) )
			);
		}

		self::$_this = $this;

		$this->get_options();

		add_action( 'admin_init', array( $this, 'revalidate_action' ) );

		add_action( 'plugins_loaded', array( $this, 'localization' ) );

		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_settings_link' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'add_settings' ) );

		add_filter( 'update_option_et_automatic_updates_options', array( $this, 'refresh_update_info' ), 10, 2 );

		add_filter( 'add_option_et_automatic_updates_options', array( $this, 'refresh_update_info' ), 10, 2 );

		add_action( 'after_setup_theme', array( $this, 'remove_default_updates' ), 11 );

		add_action( 'init', array( $this, 'remove_default_plugins_updates' ), 20 );

		register_activation_hook( __FILE__, array( $this, 'init_cron_active_account' ) );

		register_deactivation_hook( __FILE__, array( $this, 'deactivate_cron_active_account' ) );

		add_action( 'et_cron_check_account', array( $this, 'check_is_active_account' ) );

		add_action( 'admin_notices', array( $this, 'maybe_display_expired_message' ) );
	}

	/**
	 * Returns an instance of the object
	 *
	 * @return object
	 */
	static function get_this() {
		return self::$_this;
	}

	/**
	 * Checks if 'Re-Validate' button was clicked, runs account check on success
	 *
	 * @return void
	 */
	function revalidate_action() {
		global $pagenow;

		if ( ! ( 'update-core.php' === $pagenow && isset( $_GET['et_account_action'] ) && 'revalidate' === $_GET['et_account_action'] ) ) return;

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'et_revalidate_subscription' ) ) return;

		$this->check_is_active_account();
	}

	/**
	 * Displays notification to the user if account has expired
	 *
	 * @return void
	 */
	function maybe_display_expired_message() {
		if ( 'expired' === $this->account_status ) {
			printf( __(
				'<div class="error"><p><strong>Your Elegant Themes Account Has Expired!</strong> You must renew your account to retain access to important theme updates. To ensure security and compatibility, it is important to always keep your themes up-to-date.</p><p><a href="https://www.elegantthemes.com/cgi-bin/members/manage.cgi" class="button" target="_blank">Renew</a>&nbsp;&nbsp;<a href="%1$s" class="button">Re-Validate</a></p></div>', 'et_automatic_updates' ),
				esc_url( wp_nonce_url( admin_url( 'update-core.php?et_account_action=revalidate' ), 'et_revalidate_subscription' ) )
			);
		}
	}

	/**
	 * Schedules account check
	 *
	 * @return void
	 */
	function init_cron_active_account() {
		wp_schedule_event( time(), 'daily', 'et_cron_check_account' );

		// Make sure plugin options are added to request information,
		// if plugins options were valid before and the plugin has been deactivated and activated
		// it needs to add saved settings to the request
		$this->force_update();
	}

	/**
	 * Deactivates account check
	 *
	 * @return void
	 */
	function deactivate_cron_active_account() {
		wp_clear_scheduled_hook( 'et_cron_check_account' );
	}

	/**
	 * Checks if the user's account is active, updates account status.
	 * Doesn't attempt to check the status if the Username isn't set
	 *
	 * @return void
	 */
	function check_is_active_account() {
		global $wp_version;

		if ( ! isset( $this->options['username'] ) || '' == trim( $this->options['username'] ) )
			return;

		$send_to_api = array(
			'et_check_account_action' => 'check_active_account',
			'username' => sanitize_text_field( $this->options['username'] ),
			'et_updates_plugin_version' => $this->version,
		);

		$options = array(
			'timeout'    => 30,
			'body'		 => $send_to_api,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);

		$request = wp_remote_post( 'https://www.elegantthemes.com/api/api_downloads.php', $options );

		if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) == 200 ){
			$response = wp_remote_retrieve_body( $request );

			if ( ! empty( $response ) ) {
				if ( in_array( $response, array( 'expired', 'active', 'not_found' ) ) ) {
					$this->account_status = $response;

					update_option( 'et_account_status', $this->account_status );
				}
			}
		}
	}

	/**
	 * Removes default update themes/plugins notifications, provided by the theme
	 * Adds custom messages
	 *
	 * @return void
	 */
	function remove_default_updates() {
		remove_filter( 'pre_set_site_transient_update_themes', 'et_check_themes_updates' );
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'et_check_themes_updates' ) );

		remove_filter( 'site_transient_update_themes', 'et_add_themes_to_update_notification' );
		add_filter( 'site_transient_update_themes', array( $this, 'et_add_themes_to_update_notification' ) );

		remove_filter( 'gettext', 'et_admin_update_theme_message', 20, 3 );
		add_filter( 'gettext', array( $this, 'et_admin_update_theme_message' ), 20, 3 );
	}

	/**
	 * Removes default update functionality, integrated into Elegant Themes plugins
	 * Adds new checks for plugin updates
	 *
	 * @return void
	 */
	function remove_default_plugins_updates() {
		global $ET_Anticipate, $et_mobile;

		// Remove the Anticipate plugin update functions
		if ( isset( $ET_Anticipate ) ) {
			remove_filter( 'pre_set_site_transient_update_plugins', array( $ET_Anticipate, 'check_plugin_updates' ) );
			remove_filter( 'site_transient_update_plugins', array( $ET_Anticipate, 'add_plugin_to_update_notification' ) );
		}

		// Remove the Handheld plugin update functions
		if ( isset( $et_mobile ) ) {
			remove_filter( 'pre_set_site_transient_update_plugins', array( $et_mobile, 'check_plugin_updates' ) );
			remove_filter( 'site_transient_update_plugins', array( $et_mobile, 'add_plugin_to_update_notification' ) );
		}

		// Remove the ET Shortcodes plugin update functions
		remove_filter( 'pre_set_site_transient_update_plugins', 'et_shortcodes_plugin_check_updates' );
		remove_filter( 'site_transient_update_plugins', 'et_shortcodes_plugin_add_to_update_notification' );

		// Remove the Elegant Builder plugin update functions
		remove_filter( 'pre_set_site_transient_update_plugins', 'et_lb_check_plugin_updates' );
		remove_filter( 'site_transient_update_plugins', 'et_lb_add_plugin_to_update_notification' );

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugins_updates' ) );
		add_filter( 'site_transient_update_plugins', array( $this, 'add_plugins_to_update_notification' ) );
	}

	/**
	 * Provides customized messages if an update failed
	 *
	 * @param string $default_translated_text Translated text
	 * @param string $original_text Original text
	 * @param string $domain Localization domain
	 * @return string Error message or Default translated text
	 */
	function et_admin_update_theme_message( $default_translated_text, $original_text, $domain ) {
	    $theme_page_message = 'There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a>. <em>Automatic update is unavailable for this theme.</em>';
		$updates_page_message = 'Update package not available.';

	    if ( is_admin() && in_array( $original_text, array( $theme_page_message, $updates_page_message ) ) ) {
	    	$message = $theme_page_message === $original_text
	    		? __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a>.', 'et_automatic_updates' ) . ' '
	    		: '';

	    	if ( 'expired' !== $this->account_status ) {
	    		// Username or/and API key are not valid
	        	return $message . sprintf( __( 'Your Elegant Themes username or <a href="https://www.elegantthemes.com/members-area/api-key.php" target="_blank">API Key</a> is incorrect. Please update your details in the <a href="%1$s" target="_blank">Settings > General</a> tab. Valid credentials are required to update your theme.', 'et_automatic_updates' ),
	        		esc_url( admin_url( 'options-general.php#et_automatic_updates' ) )
	        	);
	        } else {
	        	// Account has expired
	        	return $message . __( 'Your Elegant Themes account has expired! You must <a href="https://www.elegantthemes.com/cgi-bin/members/manage.cgi" target="_blank">renew your account</a> before updating your theme. To ensure compatibility and security, it is extremely important to keep your themes up-to-date.', 'et_automatic_updates' );
	        }
	    }

	    return $default_translated_text;
	}

	/**
	 * Adds settings link to the plugin on WP-Admin / Plugins page
	 *
	 * @param array $links Default plugin links
	 * @return array Plugin links
	 */
	function add_settings_link( $links ){
		$settings = sprintf( '<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'options-general.php#et_automatic_updates' ) ),
			esc_html__( 'Settings', 'et_automatic_updates' )
		);
		array_push( $links, $settings );

		return $links;
	}

	/**
	 * Adds plugin localization
	 * Domain: et_automatic_updates
	 *
	 * @return void
	 */
	function localization() {
		load_plugin_textdomain( 'et_automatic_updates', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Gets plugin options
	 *
	 * @return void
	 */
	function get_options() {
		$this->options = get_option( 'et_automatic_updates_options' );

		$this->account_status = get_option( 'et_account_status' );
	}

	/**
	 * Adds settings to WP-Admin / Settings / General page
	 *
	 * @return void
	 */
	function add_settings() {
		add_settings_section(
			'et_automatic_updates_section',
			__( 'Elegant Themes Automatic Update Settings', 'et_automatic_updates' ),
			array( $this, 'settings_section_description' ),
			'general'
		);

		add_settings_field(
			'et_automatic_updates_username',
			__( 'Username', 'et_automatic_updates' ),
			array( $this, 'show_username_setting' ),
			'general',
			'et_automatic_updates_section',
			array( 'label_for' => 'et_automatic_updates_options[username]' )
		);

		add_settings_field(
			'et_automatic_updates_api_key',
			__( 'Personal API Key', 'et_automatic_updates' ),
			array( $this, 'show_api_key_setting' ),
			'general',
			'et_automatic_updates_section',
			array( 'label_for' => 'et_automatic_updates_options[api_key]' )
		);

		register_setting( 'general', 'et_automatic_updates_options' );
	}

	/**
	 * Adds div#et_automatic_updates with description, ID attribute allows for scrolling to the plugin options,
	 * when the Settings link is clicked on the Plugin page
	 *
	 * @return void
	 */
	function settings_section_description() {
		printf( '<div id="et_automatic_updates">%1$s</div>',
			__( 'Elegant Themes allows you to <a target="_blank" href="https://www.elegantthemes.com/members-area/documentation.html#update">update your themes</a> via your WordPress Dashboard instead of having to manually download and upload the theme files. Before automatic updates can be enabled, you must first authenticate your Elegant Themes account below.', 'et_automatic_updates' )
		);
	}

	/**
	 * Displays Username option
	 *
	 * @return void
	 */
	function show_username_setting() {
		printf(
			'<input name="et_automatic_updates_options[username]" id="et_automatic_updates_options[username]" type="text" value="%1$s" class="regular-text" />
			<p class="description">%2$s</p>',
			( isset( $this->options['username'] ) ? esc_attr( $this->options['username'] ) : '' ),
			esc_html__( 'Please enter your ElegantThemes.com username.', 'et_automatic_updates' )
		);
	}

	/**
	 * Displays API key option
	 *
	 * @return void
	 */
	function show_api_key_setting() {
		printf(
			'<input name="et_automatic_updates_options[api_key]" id="et_automatic_updates_options[api_key]" type="password" value="%1$s" class="regular-text" />
			<p class="description">%2$s</p>',
			( isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : '' ),
			__( 'Enter your <a href="https://www.elegantthemes.com/members-area/api-key.php" target="_blank">Elegant Themes API Key</a> here.', 'et_automatic_updates' )
		);
	}

	/**
	 * Refreshes 'update transients' when plugin settings are updated or added to the database.
	 * Makes 2 requests to the server to get the right update settings for the current user.
	 *
	 * @param mixed|string $old_value_or_option_name Old option value if option has been updated,
	 * new option name if option has been created
	 * @param mixed $new_value New value
	 * @return void
	 */
	function refresh_update_info( $old_value_or_option_name, $new_value ) {
		$this->options = $new_value;

		$this->force_update();
	}

	/**
	 * Forces themes and plugins update
	 * @return void
	 */
	function force_update() {
		if ( get_site_transient( 'update_themes' ) )
			$this->et_check_themes_updates( get_site_transient( 'update_themes' ) );

		if ( get_site_transient( 'update_plugins' ) )
			$this->check_plugins_updates( get_site_transient( 'update_plugins' ) );
	}

	/**
	 * Adds automatic updates data only if Username and API key options are set
	 *
	 * @param array $send_to_api Data sent to server
	 * @return array Modified data set if Username and API key are set, original data if not
	 */
	function maybe_add_automatic_updates_data( $send_to_api ) {
		if ( $this->options && isset( $this->options['username'] ) && isset( $this->options['api_key'] ) ) {
			$send_to_api['automatic_updates'] = 'on';
			$send_to_api['username'] = urlencode( sanitize_text_field( $this->options['username'] ) );
			$send_to_api['api_key'] = sanitize_text_field( $this->options['api_key'] );

			$send_to_api = apply_filters( 'et_add_automatic_updates_data', $send_to_api );
		}

		return $send_to_api;
	}

	/**
	 * Sends a request to server, gets current themes versions
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function et_check_themes_updates( $update_transient ){
		global $wp_version;

		if ( !isset($update_transient->checked) ) return $update_transient;
		else $themes = $update_transient->checked;

		$send_to_api = array(
			'action' => 'check_theme_updates',
			'installed_themes' => $themes,
			'et_updates_plugin_version' => $this->version,
		);

		// Add automatic updates data if Username and API key are set correctly
		$send_to_api = $this->maybe_add_automatic_updates_data( $send_to_api );

		$options = array(
			'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
			'body'			=> $send_to_api,
			'user-agent'	=> 'WordPress/' . $wp_version . '; ' . home_url()
		);

		$last_update = new stdClass();

		$theme_request = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );

		if ( ! is_wp_error( $theme_request ) && wp_remote_retrieve_response_code( $theme_request ) == 200 ){
			$theme_response = unserialize( wp_remote_retrieve_body( $theme_request ) );

			if ( ! empty( $theme_response ) ) {
				foreach ( $theme_response as $et_theme ) {
					if ( array_key_exists( 'et_expired_subscription', $et_theme ) ) {
						// Set the account status to expired if the response array has 'et_expired_subscription' key
						$this->account_status = 'expired';
					} else {
						$this->account_status = 'active';
					}

					update_option( 'et_account_status', $this->account_status );

					break;
				}

				$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $theme_response );
				$last_update->checked = $themes;
				$last_update->response = $theme_response;
			}
		}

		$last_update->last_checked = time();
		set_site_transient( 'et_update_themes', $last_update );

		return $update_transient;
	}

	/**
	 * Adds updated ET themes to default update transient
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function et_add_themes_to_update_notification( $update_transient ){
		$et_update_themes = get_site_transient( 'et_update_themes' );

		if ( ! is_object( $et_update_themes ) || ! isset( $et_update_themes->response ) )
			return $update_transient;

		// Fix for warning messages on Dashboard / Updates page
		if ( ! is_object( $update_transient ) ) {
			$update_transient = new stdClass();
		}

		$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $et_update_themes->response );

		return $update_transient;
	}

	/**
	 * Sends a request to server, gets current plugins versions
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function check_plugins_updates( $update_transient ){
		global $wp_version;

		if ( ! isset( $update_transient->checked ) ) {
			return $update_transient;
		} else {
			$plugins = $update_transient->checked;
		}

		$send_to_api = array(
			'action' => 'check_all_plugins_updates',
			'installed_plugins' => $plugins,
			'et_updates_plugin_version' => $this->version,
		);

		// Add automatic updates data if Username and API key are set correctly
		$send_to_api = $this->maybe_add_automatic_updates_data( $send_to_api );

		$options = array(
			'timeout'    => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
			'body'       => $send_to_api,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);

		$last_update = new stdClass();

		$plugins_request = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );

		if ( ! is_wp_error( $plugins_request ) && wp_remote_retrieve_response_code( $plugins_request ) == 200 ){
			$plugins_response = unserialize( wp_remote_retrieve_body( $plugins_request ) );

			if ( ! empty( $plugins_response ) ) {
				foreach ( $plugins_response as $et_plugin ) {
					if ( property_exists( $et_plugin, 'et_expired_subscription' ) ) {
						// Set the account status to expired if the response object has 'et_expired_subscription' property
						$this->account_status = 'expired';
					} else {
						$this->account_status = 'active';
					}

					update_option( 'et_account_status', $this->account_status );

					break;
				}

				$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $plugins_response );
				$last_update->checked = $plugins;
				$last_update->response = $plugins_response;
			}
		}

		$last_update->last_checked = time();
		set_site_transient( 'et_update_all_plugins', $last_update );

		return $update_transient;
	}

	/**
	 * Sends a request to server, gets current plugins versions
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function add_plugins_to_update_notification( $update_transient ){
		$et_update_lb_plugin = get_site_transient( 'et_update_all_plugins' );

		if ( ! is_object( $et_update_lb_plugin ) || ! isset( $et_update_lb_plugin->response ) ) return $update_transient;

		if ( ! is_object( $update_transient ) )
			$update_transient = new stdClass();

		$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $et_update_lb_plugin->response );

		return $update_transient;
	}
}

new ET_Automatic_Updates();