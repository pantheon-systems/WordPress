<?php
// Disable WordPress auto updates
if( ! defined('WP_AUTO_UPDATE_CORE')) {
	define( 'WP_AUTO_UPDATE_CORE', false );
}
remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );

// Remove the default WordPress core update nag if on Pantheon
if( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ){
    add_action('admin_menu','_pantheon_hide_update_nag');
}

function _pantheon_hide_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}

// Get the latest WordPress version
function _pantheon_get_latest_wordpress_version() {
	$core_updates = get_core_updates( array('dismissed' => false) );

	if( ! is_array($core_updates) || empty($core_updates) || ! property_exists($core_updates[0], 'current' ) ){
		return null;
	}

	return $core_updates[0]->current;
}

// Compare the current WordPress version to the latest available
function _pantheon_wordpress_update_available() {
	$latest_wp_version = _pantheon_get_latest_wordpress_version();

	if( null === $latest_wp_version ){
		return false;
	}

	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );
	
	// Return true if our version is not the latest
	return version_compare( $latest_wp_version, $wp_version, '>' );

}

// Replace WordPress core update nag EVERYWHERE with our own notice (use git upstream)
function _pantheon_upstream_update_notice() {
	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	$latest_wp_version = _pantheon_get_latest_wordpress_version();
    ?>
    <div class="update-nag">
		<p style="font-size: 14px; font-weight: bold; margin: 0 0 0.5em 0;">
			WordPress <?php echo $latest_wp_version; ?> is available! Please update from <a href="https://dashboard.pantheon.io/sites/<?php echo $_ENV['PANTHEON_SITE']; ?>">your Pantheon dashboard</a>.
		</p>
		For details on applying updates, see the <a href="https://pantheon.io/docs/upstream-updates/" target="_blank">Applying Upstream Updates</a> documentation. <br />
		If you need help, open a support chat on Pantheon.
	</div>
    <?php
}

// Register Pantheon specific WordPress update admin notice
add_action( 'admin_init', '_pantheon_register_upstream_update_notice' );
function _pantheon_register_upstream_update_notice(){
    // but only if we are on Pantheon and there is a WordPress update available
	if( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && _pantheon_wordpress_update_available() ){
		add_action( 'admin_notices', '_pantheon_upstream_update_notice' );
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
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], Array('test', 'live') ) ) {

	// Disable Plugin Updates
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}


