<?php
// If on Pantheon
if( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ){
	// Disable WordPress auto updates
	if( ! defined('WP_AUTO_UPDATE_CORE')) {
		define( 'WP_AUTO_UPDATE_CORE', false );
	}

	remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
	// Remove the default WordPress core update nag
    add_action('admin_menu','_pantheon_hide_update_nag');
}

function _pantheon_hide_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
	remove_action( 'network_admin_notices', 'update_nag', 3 );
}

// Get the latest WordPress version
function _pantheon_get_latest_wordpress_version() {
	$core_updates = get_core_updates( array('dismissed' => false) );

	if( ! is_array($core_updates) || empty($core_updates) || ! property_exists($core_updates[0], 'current' ) ){
		return null;
	}

	return $core_updates[0]->current;
}

// Check if WordPress core is at the latest version.
function _pantheon_is_wordpress_core_latest() {
	$latest_wp_version = _pantheon_get_latest_wordpress_version();

	if( null === $latest_wp_version ){
		return true;
	}

	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	// Return true if our version is the latest
	return version_compare( str_replace( '-src', '', $latest_wp_version ), str_replace( '-src', '', $wp_version ), '=' );

}

// Replace WordPress core update nag EVERYWHERE with our own notice (use git upstream)
function _pantheon_upstream_update_notice() {
	// Translators: %s is a URL to the user's Pantheon Dashboard.
	$notice_message = wp_kses_post( sprintf( __( 'Check for updates on <a href="%s">your Pantheon dashboard</a>.', 'pantheon-systems' ), 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] ) );
	// Translators: %s is a URL to Pantheon's upstream updates documentation.
	$upstream_help_message = wp_kses_post( sprintf( __( 'For details on applying updates, see the <a href="%s">Applying Upstream Updates</a> documentation.', 'pantheon-systems' ), 'https://pantheon.io/docs/upstream-updates/' ) );
	$update_help = wp_kses_post( __( 'If you need help, contact an administrator for your Pantheon organization.', 'pantheon-systems' ) );
	$div_class = esc_attr( 'update-nag notice notice-warning' );
	$div_style = esc_attr( 'display: table;' );
	$paragraph_style = esc_attr( 'font-size: 14px; font-weight: bold; margin: 0 0 0.5em 0;' );

	if ( _pantheon_is_wordpress_core_latest() ) {
		// If a WP core update is not detected, only show the nag on the updates page.
		$screen = get_current_screen(); 
		if ( 'update-core' === $screen->id || 'update-core-network' === $screen->id ) { ?>
			<div class="<?php echo $div_class; ?>" style="<?php echo $div_style; ?>">
				<p style="<?php echo $paragraph_style; ?>">
					<?php echo $notice_message; ?>
				</p>
				<?php echo $upstream_help_message; ?>
				<br />
				<?php echo $update_help; ?>
			</div>
			<?php
		}
	} else {
		// If WP core is out of date, alter the message and show the nag everywhere.
		// Translators: %s is a URL to the user's Pantheon Dashboard.
		$notice_message = wp_kses_post( sprintf( __( 'A new WordPress update is available! Please update from <a href="%s">your Pantheon dashboard</a>.', 'pantheon-systems' ), 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] ) );; ?>
		<div class="<?php echo $div_class; ?>" style="<?php echo $div_style; ?>">
			<p style="<?php echo $paragraph_style; ?>">
				<?php echo $notice_message; ?>
			</p>
			<?php echo $upstream_help_message; ?>
			<br />
			<?php echo $update_help; ?>
		</div>
		<?php
	}
}

// Register Pantheon specific WordPress update admin notice
add_action( 'admin_init', '_pantheon_register_upstream_update_notice' );
function _pantheon_register_upstream_update_notice(){
	// but only if we are on Pantheon
	// and this is not a WordPress Ajax request
	if( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && ! wp_doing_ajax() ){
		add_action( 'admin_notices', '_pantheon_upstream_update_notice' );
		add_action( 'network_admin_notices', '_pantheon_upstream_update_notice' );
	}
}

// Return zero updates and current time as last checked time
function _pantheon_disable_wp_updates() {
	include ABSPATH . WPINC . '/version.php';
	return (object) array(
		'updates' => array(),
		'version_checked' => $wp_version,
		'last_checked' => time(),
	);
}

// In the Test and Live environments, clear plugin/theme update notifications.
// Users must check a dev or multidev environment for updates.
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array('test', 'live') ) && (php_sapi_name() !== 'cli') ) {

	// Disable Plugin Updates
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}
