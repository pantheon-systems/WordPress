<?php
/**
 * Pantheon MU Plugin Updates
 *
 * Handles modifying the default WordPress update behavior on Pantheon.
 */

/*
 * User-meta key storing the WordPress version a user dismissed the update notice for.
 * A newer available version no longer matches the stored value, so the notice returns.
 */
const PANTHEON_UPDATE_NOTICE_DISMISSED_META = 'pantheon_dismissed_update_notice';

// AJAX action + nonce used by the dismiss handler and its front-end script.
const PANTHEON_UPDATE_NOTICE_DISMISS_ACTION = 'pantheon_dismiss_update_notice';

// If on Pantheon...
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
	// Disable WordPress auto updates.
	if ( ! defined( 'WP_AUTO_UPDATE_CORE' ) ) {
		define( 'WP_AUTO_UPDATE_CORE', false );
	}

	remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
	// Remove the default WordPress core update nag.
	add_action( 'admin_menu', '_pantheon_hide_update_nag' );
}

/**
 * Remove the default WordPress core update nag message.
 *
 * @return void
 */
function _pantheon_hide_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
	remove_action( 'network_admin_notices', 'update_nag', 3 );
}

/**
 * Get the latest WordPress version.
 *
 * @return string|null
 */
function _pantheon_get_latest_wordpress_version(): ?string {
	$core_updates = get_core_updates();

	if ( ! is_array( $core_updates ) || empty( $core_updates ) || ! property_exists( $core_updates[0], 'current' ) ) {
		return null;
	}

	return $core_updates[0]->current;
}

/**
 * Check if WordPress core is at the latest version.
 *
 * @return bool
 */
function _pantheon_is_wordpress_core_latest(): bool {
	$latest_wp_version = _pantheon_get_latest_wordpress_version();
	$wp_version = Pantheon\_pantheon_get_current_wordpress_version();

	if ( null === $latest_wp_version ) {
		return true;
	}

	// Return true if our version is the latest.
	return version_compare( str_replace( '-src', '', $latest_wp_version ), str_replace( '-src', '', $wp_version ), '<=' ); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
}

/**
 * Check if WordPress core is a pre-release version.
 *
 * @return bool
 */
function _pantheon_is_wordpress_core_prerelease(): bool {
	$wp_version = Pantheon\_pantheon_get_current_wordpress_version();

	// Return true if our version is a prerelease. Pre-releases are identified by a dash in the version number.
	return false !== strpos( $wp_version, '-' ); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
}

/**
 * Render Pantheon's upstream update notice in place of the WordPress core update nag.
 *
 * Suppressed by the PANTHEON_SHOW_UPDATE_NOTICE constant or the
 * pantheon_show_update_notice filter (see the respective checks below).
 *
 * @return void
 */
function _pantheon_upstream_update_notice() {
	// Allow admins/developers to disable the update notice via constant or filter.
	if ( defined( 'PANTHEON_SHOW_UPDATE_NOTICE' ) && ! PANTHEON_SHOW_UPDATE_NOTICE ) {
		return;
	}

	/**
	 * Filters whether the Pantheon WordPress update notice is shown.
	 *
	 * @param bool $show Whether to show the update notice. Default true.
	 */
	if ( ! apply_filters( 'pantheon_show_update_notice', true ) ) {
		return;
	}

	$screen = get_current_screen();

	// Check if using a pre-release version of WordPress.
	if ( _pantheon_is_wordpress_core_prerelease() ) {
		_pantheon_prerelease_notice();
		return;
	}

	$dashboard_url = Pantheon\_pantheon_get_dashboard_url();
	$is_update_page = 'update-core' === $screen->id || 'update-core-network' === $screen->id;
	$core_update_available = ! _pantheon_is_wordpress_core_latest();

	// If core update is available, show the update notice on ALL pages.
	if ( $core_update_available ) {
		// Skip the notice if this user already dismissed it for the current available version.
		$available_version = _pantheon_get_latest_wordpress_version();
		$dismissed_version = get_user_meta( get_current_user_id(), PANTHEON_UPDATE_NOTICE_DISMISSED_META, true );
		if ( $available_version && $dismissed_version === $available_version ) {
			return;
		}

		$message = sprintf(
			// translators: %s is a link to the Pantheon upstream updates documentation.
			__( 'For details on applying updates, see the <a href="%s">Applying Upstream Updates</a> documentation. If you need help, contact an administrator for your Pantheon organization.', 'pantheon-systems' ),
			'https://docs.pantheon.io/core-updates'
		);

		Pantheon\_pantheon_render_notice( [
			'type'          => 'warning',
			'heading'       => __( 'A new WordPress update is available!', 'pantheon-systems' ),
			'message'       => $message,
			'button_text'   => __( 'Pantheon Dashboard', 'pantheon-systems' ),
			'button_url'    => $dashboard_url,
			'id'            => 'pantheon-update-notice',
			'extra_classes' => 'pantheon-update-notice',
			'dismissible'   => true,
		] );
	} elseif ( $is_update_page ) {
		// If no update is available but we're on the update pages, show the "Check for updates" message.
		$message = sprintf(
			// translators: %s is a link to the Pantheon upstream updates documentation.
			__( 'WordPress core updates can be applied via the Pantheon Dashboard. For details on applying updates, see the <a href="%s">Applying Upstream Updates</a> documentation. If you need help, contact an administrator for your Pantheon organization.', 'pantheon-systems' ),
			'https://docs.pantheon.io/core-updates'
		);

		Pantheon\_pantheon_render_notice( [
			'type'          => 'warning',
			'heading'       => __( 'Check for Updates', 'pantheon-systems' ),
			'message'       => $message,
			'button_text'   => __( 'Pantheon Dashboard', 'pantheon-systems' ),
			'button_url'    => $dashboard_url,
			'id'            => 'pantheon-update-notice',
			'extra_classes' => 'pantheon-update-notice',
		] );
	}
}

/**
 * Display notice for WordPress pre-release/development versions
 *
 * @return void
 */
function _pantheon_prerelease_notice() {
	$screen = get_current_screen();
	$wp_version = Pantheon\_pantheon_get_current_wordpress_version();
	$message = sprintf(
		// Translators: %s is the current WordPress version.
		__( 'You are using a development version of WordPress (%s).', 'pantheon-systems' ),
		$wp_version
	);

	// Add extra info on the updates page.
	if ( 'update-core' === $screen->id || 'update-core-network' === $screen->id ) {
		$message .= ' ' . __( 'You are responsible for keeping WordPress up-to-date. Pantheon updates to WordPress will not appear in the dashboard as long as you\'re using a pre-release version. If you are using the Beta Tester plugin, you must have your site in SFTP mode to get the latest updates to your Pantheon Dev environment.', 'pantheon-systems' );
	}

	Pantheon\_pantheon_render_notice( [
		'type'    => 'info',
		'heading' => __( 'Development Version', 'pantheon-systems' ),
		'message' => $message,
	] );
}

/**
 * Register Pantheon specific WordPress update admin notice.
 *
 * @return void
 */
function _pantheon_register_upstream_update_notice() {
	// Only register notice if we are on Pantheon and this is not a WordPress Ajax request.
	if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && ! wp_doing_ajax() ) {
		add_action( 'admin_notices', '_pantheon_upstream_update_notice' );
		add_action( 'network_admin_notices', '_pantheon_upstream_update_notice' );
	}
}
add_action( 'admin_init', '_pantheon_register_upstream_update_notice' );

/**
 * AJAX handler: record that the current user dismissed the update notice for the
 * current available WordPress version. The version is resolved server-side so the
 * client cannot influence which version the dismissal applies to.
 *
 * @return void
 */
function _pantheon_dismiss_update_notice() {
	check_ajax_referer( PANTHEON_UPDATE_NOTICE_DISMISS_ACTION, 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( 'not_logged_in', 403 );
	}

	$available_version = _pantheon_get_latest_wordpress_version();
	if ( ! $available_version ) {
		wp_send_json_error( 'no_available_version' );
	}

	update_user_meta( get_current_user_id(), PANTHEON_UPDATE_NOTICE_DISMISSED_META, $available_version );
	wp_send_json_success();
}
add_action( 'wp_ajax_' . PANTHEON_UPDATE_NOTICE_DISMISS_ACTION, '_pantheon_dismiss_update_notice' );

/**
 * Enqueue the dismiss script and pass it the AJAX URL, action, and nonce.
 *
 * @return void
 */
function _pantheon_enqueue_update_notice_dismiss() {
	wp_enqueue_script(
		'pantheon-update-notice-dismiss',
		plugin_dir_url( __FILE__ ) . 'assets/js/pantheon-update-notice-dismiss.js',
		[],
		PANTHEON_MU_PLUGIN_VERSION,
		true
	);

	wp_localize_script(
		'pantheon-update-notice-dismiss',
		'pantheonUpdateNotice',
		[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => PANTHEON_UPDATE_NOTICE_DISMISS_ACTION,
			'nonce'   => wp_create_nonce( PANTHEON_UPDATE_NOTICE_DISMISS_ACTION ),
		]
	);
}
add_action( 'admin_enqueue_scripts', '_pantheon_enqueue_update_notice_dismiss' );

/**
 * Return zero updates and current time as last checked time.
 *
 * @return object
 */
function _pantheon_disable_wp_updates(): object {
	$wp_version = Pantheon\_pantheon_get_current_wordpress_version();
	return (object) [
		'updates' => [],
		'version_checked' => $wp_version, // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		'last_checked' => time(),
	];
}

/**
 * In the Test and Live environments, clear plugin/theme update notifications.
 * Users must check a dev or multidev environment for updates.
 */
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && in_array( $_ENV['PANTHEON_ENVIRONMENT'], [ 'test', 'live' ], true ) && ( php_sapi_name() !== 'cli' ) ) {

	// Disable Plugin Updates.
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates.
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}
