<?php
// Disable WordPress ato updates
if( ! defined('WP_AUTO_UPDATE_CORE')) {
	define( 'WP_AUTO_UPDATE_CORE', false );
}
remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );

// Remove WordPress core update nag
add_action('admin_menu','_pantheon_hide_update_nag');
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

	if( null === latest_wp_version ){
		return false;
	}

	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );
	
	// Return true if our version is not the latest
	return $latest_wp_version > $wp_version;

}

// Replace WordPress core update nag EVERYWHERE with our own notice (use git upstream)
function _pantheon_upstream_update_notice() {
	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	$latest_wp_version = _pantheon_get_latest_wordpress_version();
    ?>
    <div class="update-nag">
	<h3>Please check <a href="https://dashboard.pantheon.io/sites/<?php echo $_ENV['PANTHEON_SITE']; ?>"> to see if a WordPress update is available on the platform.</h3>
		Your WordPress version of <?php echo $wp_version; ?> is out of date (the current version is <?php echo $latest_wp_version; ?>)<br />
		If you need help, please see <a href="https://pantheon.io/docs/upstream-updates/">the documentation</a> for details or open a support chat.
	</div>
    <?php
}

// Register our admin notice
add_action( 'admin_init', '_pantheon_register_upstream_update_notice' );
function _pantheon_register_upstream_update_notice(){
	if( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && _pantheon_wordpress_update_available() ){
		add_action( 'admin_notices', '_pantheon_upstream_update_notice' );
	}
}

// Only in Test and Live Environments...
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], Array('test', 'live') ) ) {

	// Disable Plugin Updates
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}


