<?php
/**
 * AS3CF Pro Licences and Updates Class
 *
 * @package     amazon-s3-and-cloudfront-pro
 * @subpackage  licences
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AS3CF_Pro_Licences_Updates Class
 *
 * This class handles the licencing and plugin updates specific for the plugin
 * using the common Delicious Brains classes
 *
 * @since 1.0
 */
class AS3CF_Pro_Licences_Updates extends Delicious_Brains_API_Licences {

	/**
	 * @var Amazon_S3_And_CloudFront_Pro
	 */
	private $as3cf;

	const MEDIA_USAGE_UNDER = 1;
	const MEDIA_USAGE_APPROACHING = 2;
	const MEDIA_USAGE_REACHED = 3;
	const MEDIA_USAGE_EXCEEDED = 4;

	/**
	 * @param Amazon_S3_And_CloudFront_Pro $as3cf
	 */
	function __construct( Amazon_S3_And_CloudFront_Pro $as3cf ) {
		$this->as3cf = $as3cf;

		$plugin = new Delicious_Brains_API_Plugin();

		$plugin->global_meta_prefix       = 'aws';
		$plugin->slug                     = 'amazon-s3-and-cloudfront-pro';
		$plugin->name                     = 'WP Offload Media';
		$plugin->version                  = $GLOBALS[ $plugin->global_meta_prefix . '_meta' ][ $plugin->slug ]['version'];
		$plugin->basename                 = $this->as3cf->get_plugin_basename();
		$plugin->dir_path                 = $this->as3cf->get_plugin_dir_path();
		$plugin->prefix                   = 'as3cfpro';
		$plugin->settings_url_path        = $this->as3cf->get_plugin_pagenow() . '?page=amazon-s3-and-cloudfront';
		$plugin->settings_url_hash        = '#licence';
		$plugin->hook_suffix              = $as3cf->hook_suffix;
		$plugin->email_address_name       = 'as3cf';
		$plugin->notices_hook             = 'as3cf_pre_settings_render';
		$plugin->load_hook                = 'as3cf_plugin_load';
		$plugin->expired_licence_is_valid = true;
		$plugin->purchase_url             = $this->as3cf->dbrains_url( '/wp-offload-media/pricing/', array(
			'utm_campaign' => 'WP+Offload+S3',
		) );

		parent::__construct( $plugin );

		$this->init();
	}

	/**
	 * Initialize the actions and filters for the class
	 */
	function init() {
		add_action( 'admin_notices', array( $this, 'dashboard_licence_issue_notice' ) );
		add_action( 'network_admin_notices', array( $this, 'dashboard_licence_issue_notice' ) );
		add_action( 'as3cf_pre_settings_render', array( $this, 'licence_issue_notice' ), 11 );

		add_action( 'as3cf_licence_field', array( $this, 'render_licence_settings' ) );
		add_action( 'as3cf_support_pre_debug', array( $this, 'render_licence_info' ) );

		add_filter( 'as3cfpro_js_nonces', array( $this, 'add_licence_nonces' ) );
		add_filter( 'as3cfpro_js_strings', array( $this, 'add_licence_strings' ) );
		add_filter( 'as3cf_addons', array( $this, 'inject_addon_page_links' ) );

		add_action( 'as3cf_plugin_load', array( $this, 'http_dismiss_licence_notice' ) );
		add_action( 'as3cfpro_http_refresh_licence', array( $this, 'do_http_refresh_licence' ) );
		add_action( 'as3cfpro_http_remove_licence', array( $this, 'do_http_remove_licence' ) );
		add_action( 'as3cfpro_activate_licence_response', array( $this, 'refresh_licence_notice' ) );
		add_action( 'as3cfpro_ajax_check_licence_response', array( $this, 'refresh_licence_notice' ) );
		add_filter( 'as3cfpro_licence_status_message', array( $this, 'licence_status_message' ), 10, 2 );
		add_filter( 'as3cfpro_pre_plugin_row_update_notice', array( $this, 'suppress_plugin_row_update_notices' ), 10, 2 );
		add_action( 'check_admin_referer', array( $this, 'block_updates_with_invalid_license' ) );
	}

	/**
	 * Accessor for license key
	 *
	 * @return int|mixed|string|WP_Error
	 */
	protected function get_plugin_licence_key() {
		return $this->as3cf->get_setting( 'licence' );
	}

	/**
	 * Setter for license key
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	protected function set_plugin_licence_key( $key ) {
		$this->as3cf->get_settings();
		$this->as3cf->set_setting( 'licence', $key );
		$this->as3cf->save_settings();
	}

	/**
	 * Display the license form
	 */
	public function render_licence_settings() {
		$this->as3cf->render_view( 'licence-settings', array(
			'is_defined'     => $this->is_licence_constant(),
			'is_set'         => (bool) $this->get_licence_key(),
			'masked_licence' => $this->get_masked_licence(),
		) );
	}

	/**
	 * Display the license details and email support form
	 */
	function render_licence_info() {
		$args = array(
			'licence' => $this->get_licence_key(),
		);
		$this->as3cf->render_view( 'licence-info', $args );
	}

	/**
	 * Add more nonces to the as3cfpro Javascript object
	 *
	 * @param array $nonces
	 *
	 * @return array
	 */
	function add_licence_nonces( $nonces ) {
		$nonces['check_licence']      = wp_create_nonce( 'check-licence' );
		$nonces['activate_licence']   = wp_create_nonce( 'activate-licence' );
		$nonces['remove_licence']     = wp_create_nonce( 'remove-licence' );
		$nonces['reactivate_licence'] = wp_create_nonce( 'reactivate-licence' );

		return $nonces;
	}

	/**
	 * Add more strings to the as3cfpro Javascript object
	 *
	 * @param array $strings
	 *
	 * @return array
	 */
	function add_licence_strings( $strings ) {
		$licence_strings = array(
			'license_check_problem'          => __( 'A problem occurred when trying to check the license, please try again.', 'amazon-s3-and-cloudfront' ),
			'has_licence'                    => esc_html( $this->get_licence_key() == '' ? '0' : '1' ),
			'enter_license_key'              => __( 'Please enter your license key.', 'amazon-s3-and-cloudfront' ),
			'register_license_problem'       => __( 'A problem occurred when trying to register the license, please try again.', 'amazon-s3-and-cloudfront' ),
			'license_registered'             => __( 'Your license has been activated. You will now receive automatic updates and access to email support.', 'amazon-s3-and-cloudfront' ),
			'fetching_license'               => __( 'Fetching license details, please wait&hellip;', 'amazon-s3-and-cloudfront' ),
			'activate_licence_problem'       => __( 'An error occurred when trying to reactivate your license. Please contact support.', 'amazon-s3-and-cloudfront' ),
			'attempting_to_activate_licence' => __( 'Attempting to activate your license, please wait&hellip;', 'amazon-s3-and-cloudfront' ),
			'status'                         => _x( 'Status', 'Current request status', 'amazon-s3-and-cloudfront' ),
			'response'                       => _x( 'Response', 'The message the server responded with', 'amazon-s3-and-cloudfront' ),
			'licence_reactivated'            => __( 'License successfully activated, please wait&hellip;', 'amazon-s3-and-cloudfront' ),
			'temporarily_activated_licence'  => __( "<strong>We've temporarily activated your licence and will complete the activation once the Delicious Brains API is available again.</strong><br />Please refresh this page to continue.", 'amazon-s3-and-cloudfront' ),
		);

		return array_merge( $strings, $licence_strings );
	}

	/**
	 * Inject the install and download links for available addons
	 * to the AWS Addons page
	 *
	 * @param array $addons
	 *
	 * @return array $addons
	 */
	function inject_addon_page_links( $addons ) {
		if ( ! $this->is_valid_licence( true, false ) ) {
			return $addons;
		}

		foreach ( $addons as $slug => &$addon ) {
			$basename = $this->plugin->get_plugin_basename( $slug );

			if ( ! isset( $this->addons[ $basename ] ) ) {
				continue;
			}

			// Default extra link 'My Account' as Upgrade
			$extra_link = array(
				'url'  => $this->plugin->account_url,
				'text' => __( 'Upgrade', 'amazon-s3-and-cloudfront' ),
			);

			if ( $this->addons[ $basename ]['available'] ) {
				// Addon available to be installed
				$addon['install'] = true;
				// and manually downloaded
				$extra_link['url']  = $this->updates->get_plugin_update_download_url( $slug );
				$extra_link['text'] = __( 'Download', 'amazon-s3-and-cloudfront' );
			}

			$addon['links'][] = $extra_link;
		}

		return $addons;
	}

	/**
	 * Clear the media attachment transients when we refresh the license
	 */
	function do_http_refresh_licence() {
		$this->remove_media_transients();

		// force a check of the license again as we aren't hitting the support tab
		$licence = $this->get_licence_key();
		$this->check_licence( $licence );
	}

	/**
	 * Clear the media attachment transients when we remove the license
	 */
	function do_http_remove_licence() {
		$this->remove_media_transients();
	}

	/**
	 * Remove media related transients
	 */
	function remove_media_transients() {
		delete_site_transient( $this->plugin->prefix . '_media_library_total' );
		delete_site_transient( $this->plugin->prefix . '_licence_media_check' );
	}

	/**
	 * Helper for creating nonced action URLs
	 *
	 * @param string $action
	 * @param bool   $send_to_settings Send back to settings tab
	 * @param bool   $dashboard        Are we displaying elsewhere in the dashboard
	 *
	 * @return string
	 */
	function get_licence_notice_url( $action, $send_to_settings = true, $dashboard = false ) {
		$action     = $this->plugin->prefix . '-' . $action;
		$query_args = array(
			'nonce' => wp_create_nonce( $action ),
			$action => 1,
		);

		if ( $dashboard ) {
			$query_args['sendback'] = urlencode( $_SERVER['REQUEST_URI'] );
		}

		$path = $this->plugin->settings_url_path;
		if ( $send_to_settings ) {
			$path .= $this->plugin->settings_url_hash;
		}

		$url = add_query_arg( $query_args, $this->admin_url( $path ) );

		return $url;
	}

	/**
	 * Display our license issue notice which covers -
	 *  - No license
	 *  - Expired licenses
	 *  - Media library larger than license limit
	 *
	 * @param bool $dashboard Are we displaying across the dashboard?
	 * @param bool $skip_transient
	 */
	public function licence_issue_notice( $dashboard = false, $skip_transient = false ) {
		if ( ! $this->as3cf->is_plugin_setup() ) {
			// Don't show the notice if basic plugin requirements are not met.
			return;
		}

		if ( $dashboard && method_exists( 'AS3CF_Compatibility_Check', 'is_installing_or_updating_plugins' ) && AS3CF_Compatibility_Check::is_installing_or_updating_plugins() ) {
			// Don't show the notice for plugin installs & updates, just too much noise
			return;
		}

		$license_check = $this->is_licence_expired();
		$args          = compact( 'dashboard' );

		if ( ! empty( $license_check['errors']['no_licence'] ) ) {
			$this->display_no_licence_notice( $args );

			return;
		}

		$media_limit_check = $this->check_licence_media_limit( $skip_transient );

		if ( self::MEDIA_USAGE_REACHED <= $media_limit_check['status']['code'] ) {
			$this->display_over_limit_licence_notice( $args );
		} else if ( ! empty( $license_check['errors']['subscription_expired'] ) ) {
			$this->display_expired_licence_notice( $args );
		} else if ( self::MEDIA_USAGE_APPROACHING === $media_limit_check['status']['code'] ) {
			$this->display_near_limit_licence_notice( $args );
		} else if ( ! isset( $license_check['errors'] ) ) {
			$this->clear_licence_issue();
		}
	}

	/**
	 * Display the notice for a missing licence.
	 *
	 * @param $args
	 */
	protected function display_no_licence_notice( $args ) {
		if ( $args['dashboard'] ) {
			return;
		}

		$license_check = $this->is_licence_expired();

		$this->render_licence_notice( array_merge( $args, array(
			'title'   => __( 'Activate Your License', 'amazon-s3-and-cloudfront' ),
			'message' => $license_check['errors']['no_licence'],
			'type'    => 'no_licence',
			'links'   => array( 'check_again' ),
		) ) );
	}

	/**
	 * Display the notice for an expired licence.
	 *
	 * @param $args
	 */
	protected function display_expired_licence_notice( $args ) {
		if ( $args['dashboard'] ) {
			$title = sprintf( __( 'Your %s License Has Expired', 'amazon-s3-and-cloudfront' ), $this->plugin->name );
		} else {
			$title = __( 'Your License Has Expired', 'amazon-s3-and-cloudfront' );
		}

		$this->render_licence_notice( array_merge( $args, array(
			'title'   => $title,
			'message' => __( 'All features will continue to work, but you won\'t have access to software updates or email support.', 'amazon-s3-and-cloudfront' ),
			'type'    => 'subscription_expired',
			'links'   => array( 'renew_now', 'check_again' ),
		) ) );
	}

	/**
	 * Display the notice for a licence approaching its limit.
	 *
	 * @param $args
	 */
	protected function display_near_limit_licence_notice( $args ) {
		$media_limit_check = $this->check_licence_media_limit();
		$args['message']   = sprintf(
			__( 'The total number of attachments across the media libraries for your installs (%1$s) is approaching the limit for your license (%2$s).', 'amazon-s3-and-cloudfront' ),
			number_format( absint( $media_limit_check['total'] ) ),
			number_format( absint( $media_limit_check['limit'] ) )
		);

		if ( $args['dashboard'] ) {
			$args['title'] = sprintf( __( 'Approaching %s License Limit', 'amazon-s3-and-cloudfront' ), $this->plugin->name );
		} else {
			$args['title'] = __( 'Approaching License Limit', 'amazon-s3-and-cloudfront' );
		}

		$args['type']  = 'near_limit';
		$args['extra'] = sprintf(
			__( 'When you exceed the limit, all essential features will continue to work, but a few <a href="%1$s">non-essential features</a> will be disabled until you <a href="%2$s">upgrade your license</a> or <a href="%3$s">free-up some of your current limit</a>.', 'amazon-s3-and-cloudfront' ),
			$this->non_essential_features_url(),
			$this->as3cf->get_my_account_url(),
			$this->free_up_limit_url()
		);
		$args['links'] = array( 'upgrade_now', 'check_again' );

		$this->render_licence_notice( $args );
	}

	/**
	 * Display the notice for a licence which has exceeded its limit.
	 *
	 * @param $args
	 */
	protected function display_over_limit_licence_notice( $args ) {
		$media_limit_check = $this->check_licence_media_limit();
		$total             = absint( $media_limit_check['total'] );
		$limit             = absint( $media_limit_check['limit'] );

		if ( $args['dashboard'] ) {
			$args['title'] = sprintf( __( 'Upgrade Your %s License', 'amazon-s3-and-cloudfront' ), $this->plugin->name );
		} else {
			$args['title'] = __( 'Upgrade Your License', 'amazon-s3-and-cloudfront' );
		}

		$reached  = __( 'The total number of attachments across the media libraries for your installs (%1$s) has reached the limit for your license (%2$s).', 'amazon-s3-and-cloudfront' );
		$exceeded = __( 'The total number of attachments across the media libraries for your installs (%1$s) has exceeded the limit for your license (%2$s).', 'amazon-s3-and-cloudfront' );

		$args['type']    = 'over_limit';
		$args['message'] = sprintf(
			$total > $limit ? $exceeded : $reached,
			number_format( $total ),
			number_format( $limit )
		);
		$args['extra']   = sprintf(
			__( 'All essential features will continue to work, but a few <a href="%1$s">non-essential features</a> will be disabled until you <a href="%2$s">upgrade your license</a> or <a href="%3$s">free-up some of your current limit</a>.', 'amazon-s3-and-cloudfront' ),
			$this->non_essential_features_url(),
			$this->as3cf->get_my_account_url(),
			$this->free_up_limit_url()
		);
		$args['links']   = array( 'upgrade_now', 'check_again' );

		$this->render_licence_notice( $args );
	}

	/**
	 * Render a licence notice.
	 *
	 * @param array $args
	 */
	public function render_licence_notice( $args = array() ) {
		$args = array_merge( array(
			'title'       => '',
			'type'        => '',
			'message'     => '',
			'extra'       => '',
			'links'       => array(),
			'dashboard'   => false,
			'dismissible' => false,
			'dismiss_url' => '',
		), $args );

		// Don't show if current user has dismissed notice
		if ( $args['dashboard'] && get_user_meta( get_current_user_id(), $this->plugin->prefix . '-dismiss-licence-notice' ) ) {
			return;
		}

		if ( $args['dashboard'] ) {
			$args['dismissible'] = true;
			$args['dismiss_url'] = $this->get_licence_notice_url( 'dismiss-licence-notice', false, true );
		}

		$link_map = array(
			'upgrade_now' => sprintf( '<a href="%s" class="as3cf-pro-upgrade-now">%s</a>', $this->as3cf->get_my_account_url(), __( 'Upgrade Your License Now', 'amazon-s3-and-cloudfront' ) ),
			'renew_now'   => sprintf( '<a href="%s" class="as3cf-pro-renew-now">%s</a>', $this->as3cf->get_my_account_url(), __( 'Renew Your License Now', 'amazon-s3-and-cloudfront' ) ),
			'check_again' => sprintf( '<a href="%s" class="as3cf-pro-check-again">%s</a>', $this->get_licence_notice_url( 'check-licence', true, $args['dashboard'] ), __( 'Check again', 'amazon-s3-and-cloudfront' ) ),
		);

		if ( ! empty( $args['links'] ) ) {
			$args['links'] = array_map( function ( $link ) use ( $link_map ) {
				return isset( $link_map[ $link ] ) ? $link_map[ $link ] : $link;
			}, $args['links'] );
		}

		$this->as3cf->render_view( 'licence-notice', $args );
		$this->update_licence_issue( $args['type'] );
	}

	/**
	 * Update the saved license issue type.
	 *
	 * @param string $type
	 */
	protected function update_licence_issue( $type ) {
		if ( $type !== get_site_option( $this->plugin->prefix . '_licence_issue_type' ) ) {
			// Delete the dismissed flag for the user
			delete_user_meta( get_current_user_id(), $this->plugin->prefix . '-dismiss-licence-notice' );

			// Store the type of issue for comparison later
			update_site_option( $this->plugin->prefix . '_licence_issue_type', $type );
		}
	}

	/**
	 * Clear the saved licence issue type.
	 */
	protected function clear_licence_issue() {
		delete_site_option( $this->plugin->prefix . '_licence_issue_type' );
	}

	/**
	 * Get the document href for non-essential features.
	 *
	 * @return string
	 */
	public function non_essential_features_url() {
		return $this->as3cf->dbrains_url( '/wp-offload-media/doc/non-essential-features/', array(
			'utm_campaign' => 'error+messages',
		) );
	}

	/**
	 * Get the document href for details about freeing up licence limit.
	 *
	 * @return string
	 */
	public function free_up_limit_url() {
		return $this->as3cf->dbrains_url( '/wp-offload-media/pricing/', array(
			'utm_campaign' => 'error+messages',
		), 'free-up-limit' );
	}

	/**
	 * Dismiss the license issue notice
	 */
	function http_dismiss_licence_notice() {
		if ( isset( $_GET[ $this->plugin->prefix . '-dismiss-licence-notice' ] ) && wp_verify_nonce( $_GET['nonce'], $this->plugin->prefix . '-dismiss-licence-notice' ) ) { // input var okay
			$hash = ( isset( $_GET['hash'] ) ) ? '#' . sanitize_title( $_GET['hash'] ) : ''; // input var okay

			// Store the dismissed flag against the user
			update_user_meta( get_current_user_id(), $this->plugin->prefix . '-dismiss-licence-notice', true );

			$sendback = filter_input( INPUT_GET, 'sendback' ) ?: $this->admin_url( $this->plugin->settings_url_path . $hash );

			// redirecting because we don't want to keep the query string in the web browsers address bar
			wp_safe_redirect( $sendback );
			exit;
		}
	}

	/**
	 * Display the license issue notice site wide except on our plugin page
	 */
	function dashboard_licence_issue_notice() {
		if ( isset( $_GET['page'] ) && 'amazon-s3-and-cloudfront' === $_GET['page'] ) {
			return;
		}

		global $as3cf_compat_check;
		if ( ! $as3cf_compat_check->check_capabilities() ) {
			return;
		}

		return $this->licence_issue_notice( true );
	}

	/**
	 * Check the license is not over its limit for media library items
	 *
	 * @param bool $skip_transient
	 *
	 * @return bool|array
	 */
	public function check_licence_media_limit( $skip_transient = false ) {
		$media_limit_check = get_site_transient( $this->plugin->prefix . '_licence_media_check' );

		if ( $skip_transient || false === $media_limit_check || isset( $media_limit_check['errors'] ) ) {

			if ( ! ( $licence_key = $this->get_licence_key() ) ) {
				return false;
			}

			$args = array(
				'licence_key'   => $licence_key,
				'site_url'      => $this->home_url,
				'library_total' => $this->as3cf->get_media_library_provider_total( $skip_transient ),
			);

			$response = $this->api_request( 'check_licence_media_limit', $args );

			$media_limit_check = json_decode( $response, true );

			// Can't decode json so assume ok, but don't cache response
			if ( ! $media_limit_check ) {
				return array();
			}

			set_site_transient( $this->plugin->prefix . '_licence_media_check', $media_limit_check, MINUTE_IN_SECONDS * 10 );
		}

		return $media_limit_check;
	}

	/**
	 * Check if the license is under the media limit
	 *
	 * @return bool
	 */
	public function is_licence_over_media_limit() {
		$media_limit_check = $this->check_licence_media_limit();

		if ( ! isset( $media_limit_check['status'] ) ) {
			return false;
		}

		if ( $media_limit_check['status']['code'] < self::MEDIA_USAGE_REACHED ) {
			return false;
		}

		return true;
	}

	/**
	 * Return the custom license error to the API when activating / checking a license
	 *
	 * @param array $decoded_response
	 *
	 * @return array
	 */
	public function refresh_licence_notice( $decoded_response ) {
		ob_start();
		$this->licence_issue_notice( false, true );
		$licence_error = ob_get_contents();
		ob_end_clean();

		if ( $licence_error ) {
			$license                       = $this->is_licence_expired();
			$decoded_response['errors']    = empty( $license['errors'] ) ? '' : $license['errors'];
			$decoded_response['pro_error'] = $licence_error;
		}

		return $decoded_response;
	}

	/**
	 * Override the default license expired message for the email support section
	 *
	 * @param string $message
	 * @param array  $errors
	 *
	 * @return string
	 */
	function licence_status_message( $message, $errors ) {
		if ( isset( $errors['subscription_expired'] ) ) {
			$check_licence_again_url = $this->admin_url( $this->plugin->settings_url_path . '&nonce=' . wp_create_nonce( $this->plugin->prefix . '-check-licence' ) . '&' . $this->plugin->prefix . '-check-licence=1' . $this->plugin->settings_url_hash );

			$url     = $this->as3cf->dbrains_url( '/my-account/', array(
				'utm_campaign' => 'error+messages',
			) );
			$message = sprintf( __( '<strong>Your License Has Expired</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to renew your license and continue receiving access to email support.' ), $url ) . ' ';
			$message .= sprintf( '<a href="%s">%s</a>', $check_licence_again_url, __( 'Check again' ) );
		}

		return $message;
	}

	/**
	 * Don't show plugin row update notices when AWS not set up
	 *
	 * @param bool  $pre
	 * @param array $licence_response
	 *
	 * @return bool
	 */
	function suppress_plugin_row_update_notices( $pre, $licence_response ) {
		global $amazon_web_services;

		if ( isset( $licence_response['errors']['no_licence'] ) && ! $amazon_web_services->are_access_keys_set() ) {
			// Don't show the activate license notice if we haven't set up AWS keys
			return true;
		}

		return $pre;
	}

	/**
	 * Throw a nonce error if trying to update the plugin or addons
	 * with a missing or invalid license
	 *
	 * @param string     $action
	 * @param bool|false $result
	 *
	 * @return bool
	 */
	function block_updates_with_invalid_license( $action, $result = false ) {
		if ( 'bulk-update-plugins' !== $action ) {
			return $result;
		}

		if ( ! isset( $_GET['plugins'] ) && ! isset( $_POST['checked'] ) ) {
			return $result;
		}

		if ( isset( $_GET['plugins'] ) ) {
			$plugins = explode( ',', stripslashes( $_GET['plugins'] ) );
		} elseif ( isset( $_POST['checked'] ) ) {
			$plugins = (array) $_POST['checked'];
		} else {
			// No plugins selected at all, move on
			return $result;
		}

		$plugins          = array_map( 'urldecode', $plugins );
		$our_plugins      = array_keys( $this->addons );
		$our_plugins[]    = $this->plugin->basename;
		$matching_plugins = array_intersect( $plugins, $our_plugins );

		if ( empty( $matching_plugins ) ) {
			// None of our addons or plugin are being updated
			return $result;
		}

		$licence_check = $this->is_licence_expired( true );

		foreach ( $plugins as $plugin ) {
			if ( in_array( $plugin, $our_plugins ) ) {
				$plugin_name   = $this->plugin->name;
				$parent_plugin = '';
				if ( isset( $this->addons[ $plugin ] ) ) {
					$plugin_name   = $this->addons[ $plugin ]['name'];
					$parent_plugin = ' ' . sprintf( __( 'for %s', 'amazon-s3-and-cloudfront' ), $this->plugin->name );
				}

				if ( isset( $licence_check['errors']['no_licence'] ) ) {
					$html = sprintf( __( '<strong>Activate Your License</strong> &mdash; You can only update %1$s with a valid license key%2$s.', 'amazon-s3-and-cloudfront' ), $plugin_name, $parent_plugin );
					$html .= '</p><p><a target="_parent" href="' . $this->admin_url( $this->plugin->settings_url_path ) . $this->plugin->settings_url_hash . '">' . _x( 'Activate', 'Activate license', 'amazon-s3-and-cloudfront' ) . '</a> | ';
					$html .= '<a target="_parent" href="' . $this->plugin->purchase_url . '">' . _x( 'Purchase', 'Purchase license', 'amazon-s3-and-cloudfront' ) . '</a>';
				} else if ( isset( $licence_check['errors']['subscription_expired'] ) ) {
					$html = sprintf( __( '<strong>Your License Has Expired</strong> &mdash; You can only update %1$s with a valid license key%2$s. Please visit <a href="%3$s" target="_parent">My Account</a> to renew your license and continue receiving plugin updates.', 'amazon-s3-and-cloudfront' ), $plugin_name, $parent_plugin, $this->plugin->account_url );
				} else {
					// License valid, move along
					return $result;
				}

				if ( isset( $_GET['plugins'] ) ) {
					$clean_plugin = addslashes( urlencode( $plugin ) );

					// Check for assortment of versions of plugin, with leading commas
					$needles = array(
						',' . $plugin,
						',' . $clean_plugin,
						$plugin,
						$clean_plugin,
					);

					// Remove plugin from the global var
					$_GET['plugins'] = str_replace( $needles, '', $_GET['plugins'] );

					if ( '' === $_GET['plugins'] ) {
						// No plugins, remove the var
						unset( $_GET['plugins'] );
					}
				} elseif ( isset( $_POST['checked'] ) ) {
					foreach ( $_POST['checked'] as $key => $checked_plugin ) {
						if ( in_array( $checked_plugin, array( $plugin, urlencode( $plugin ) ) ) ) {
							// Remove plugin from the global var
							unset( $_POST['checked'][ $key ] );
						}

						if ( empty( $_POST['checked'] ) ) {
							// No plugins, remove the var
							unset( $_POST['checked'] );
						}
					}
				}

				// Display license error notice
				$this->as3cf->render_view( 'error-fatal', array( 'message' => $html ) );
			}
		}

		return $result;
	}

	/**
	 * Error log method
	 *
	 * @param mixed $error
	 * @param bool  $additional_error_var
	 */
	function log_error( $error, $additional_error_var = false ) {
		AS3CF_Error::log( $error, 'PRO' );

		if ( false !== $additional_error_var ) {
			AS3CF_Error::log( $additional_error_var, 'PRO' );
		}
	}
}
