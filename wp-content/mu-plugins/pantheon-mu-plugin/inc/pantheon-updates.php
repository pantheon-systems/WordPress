<?php
/**
 * Pantheon MU Plugin Updates
 *
 * Handles modifying the default WordPress update behavior on Pantheon.
 */

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
 * Replace WordPress core update nag EVERYWHERE with our own notice.
 * Use git upstream instead
 *
 * @return void
 */
function _pantheon_upstream_update_notice() {
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
		$message = sprintf(
			// translators: %s is a link to the Pantheon upstream updates documentation.
			__( 'For details on applying updates, see the <a href="%s">Applying Upstream Updates</a> documentation. If you need help, contact an administrator for your Pantheon organization.', 'pantheon-systems' ),
			'https://docs.pantheon.io/core-updates'
		);

		Pantheon\_pantheon_render_notice( [
			'type'        => 'warning',
			'heading'     => __( 'A new WordPress update is available!', 'pantheon-systems' ),
			'message'     => $message,
			'button_text' => __( 'Pantheon Dashboard', 'pantheon-systems' ),
			'button_url'  => $dashboard_url,
		] );
	} elseif ( $is_update_page ) {
		// If no update is available but we're on the update pages, show the "Check for updates" message.
		$message = sprintf(
			// translators: %s is a link to the Pantheon upstream updates documentation.
			__( 'WordPress core updates can be applied via the Pantheon Dashboard. For details on applying updates, see the <a href="%s">Applying Upstream Updates</a> documentation. If you need help, contact an administrator for your Pantheon organization.', 'pantheon-systems' ),
			'https://docs.pantheon.io/core-updates'
		);

		Pantheon\_pantheon_render_notice( [
			'type'        => 'warning',
			'heading'     => __( 'Check for Updates', 'pantheon-systems' ),
			'message'     => $message,
			'button_text' => __( 'Pantheon Dashboard', 'pantheon-systems' ),
			'button_url'  => $dashboard_url,
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
