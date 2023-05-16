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
 * Helper function that returns the current WordPress version.
 *
 * @return string
 */
function _pantheon_get_current_wordpress_version() : string {
	include ABSPATH . WPINC . '/version.php';
	return $wp_version; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
}

/**
 * Get the latest WordPress version.
 *
 * @return string|null
 */
function _pantheon_get_latest_wordpress_version() : ?string {
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
function _pantheon_is_wordpress_core_latest() : bool {
	$latest_wp_version = _pantheon_get_latest_wordpress_version();
	$wp_version = _pantheon_get_current_wordpress_version();

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
function _pantheon_is_wordpress_core_prerelease() : bool {
	$wp_version = _pantheon_get_current_wordpress_version();

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
	$wp_version = _pantheon_get_current_wordpress_version();
	$screen = get_current_screen();
	// Translators: %s is a URL to the user's Pantheon Dashboard.
	$notice_message = sprintf( __( 'Check for updates on <a href="%s">your Pantheon dashboard</a>.', 'pantheon-systems' ), 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] );
	// Translators: %s is a URL to Pantheon's upstream updates documentation.
	$upstream_help_message = sprintf( __( 'For details on applying updates, see the <a href="%s">Applying Upstream Updates</a> documentation.', 'pantheon-systems' ), 'https://docs.pantheon.io/core-updates' );
	$update_help = __( 'If you need help, contact an administrator for your Pantheon organization.', 'pantheon-systems' );
	$div_class = esc_attr( 'update-nag notice notice-warning' );
	$div_style = esc_attr( 'display: table;' );
	$paragraph_style = esc_attr( 'font-size: 14px; font-weight: bold; margin: 0 0 0.5em 0;' );

	if ( ! _pantheon_is_wordpress_core_latest() ) {
		// If WP core is out of date, alter the message and show the nag everywhere.
		// Translators: %s is a URL to the user's Pantheon Dashboard.
		$notice_message = sprintf( __( 'A new WordPress update is available! Please update from <a href="%s">your Pantheon dashboard</a>.', 'pantheon-systems' ), 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] );
	}

	// If WP core is a pre-release, alter the message.
	if ( _pantheon_is_wordpress_core_prerelease() ) {
		$version = '<span style="font-weight: normal;">(' . $wp_version . ')</span>';
		$paragraph_style = esc_attr( 'font-size: 14px;' );
		// Translators: %s is the current WordPress version.
		$notice_message = sprintf( __( '<strong>You are using a development version of WordPress.</strong> %s', 'pantheon-systems' ), $version );
		// If we're on the Updates page, add a note about the Beta Tester plugin.
		if ( 'update-core' === $screen->id || 'update-core-network' === $screen->id ) {
			$notice_message .= '<br /><span style="font-weight: normal;">';
			$notice_message .= __( 'You are responsible for keeping WordPress up-to-date. Pantheon updates to WordPress will not appear in the dashboard as long as you\'re using a pre-release version. If you are using the Beta Tester plugin, you must have your site in SFTP mode to get the latest updates to your Pantheon Dev environment.', 'pantheon-systems' );
		}
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( $div_class ); ?>" style="<?php echo esc_attr( $div_style ); ?>">
		<p style="<?php echo esc_attr( $paragraph_style ); ?>">
			<?php echo wp_kses_post( $notice_message ); ?>
		</p>
		<?php if ( ! _pantheon_is_wordpress_core_prerelease() ) : ?>
			<?php echo wp_kses_post( $upstream_help_message ); ?>
			<br />
			<?php echo wp_kses_post( $update_help ); ?>
		<?php endif; ?>
	</div>
	<?php
	$notice_html = ob_get_clean();
	// If a WP core update is not detected, only show the nag on the updates page.
	if ( ! _pantheon_is_wordpress_core_latest() || 'update-core' === $screen->id || 'update-core-network' === $screen->id ) {
			// Escaping is handled above when we're buffering the output, so we can ignore it here.
			echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
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
function _pantheon_disable_wp_updates() : object {
	$wp_version = _pantheon_get_current_wordpress_version();
	return (object) [
		'updates' => [],
		'version_checked' => $wp_version, // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		'last_checked' => time(),
	];
}

// In the Test and Live environments, clear plugin/theme update notifications.
// Users must check a dev or multidev environment for updates.
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && in_array( $_ENV['PANTHEON_ENVIRONMENT'], [ 'test', 'live' ], true ) && ( php_sapi_name() !== 'cli' ) ) {

	// Disable Plugin Updates.
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates.
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}
