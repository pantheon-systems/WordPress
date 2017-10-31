<?php
// Disable WordPress auto updates
if( ! defined('WP_AUTO_UPDATE_CORE')) {
	define( 'WP_AUTO_UPDATE_CORE', false );
}

// Short circuit WordPress checking WordPress.org for updates
function _pantheon_disable_wp_updates() {
	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	return (object) array(
		'updates' => array(),
		'version_checked' => $wp_version,
		'last_checked' => time(),
	);

}
add_filter( 'pre_site_transient_update_core', '_pantheon_disable_wp_updates' );

// Remove WordPress core update nag
add_action( 'admin_menu', '_pantheon_hide_update_nag', 99 );
function _pantheon_hide_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
	remove_action( 'network_admin_notices', 'update_nag', 3 );
}

// Get the latest WordPress version tagged in the Pantheon upstream
function _pantheon_get_latest_wordpress_version() {
	$pantheon_latest_wp_version = get_transient( 'pantheon_latest_wp_version' );

	if( false === $pantheon_latest_wp_version ){
		$github_api_pantheon_upstream_tags_url = "https://api.github.com/repos/pantheon-systems/WordPress/git/refs/tags";
		$response = wp_remote_get( $github_api_pantheon_upstream_tags_url );

		if( ! is_wp_error( $response ) && 200 === $response['response']['code'] ){
			$latest_tag_obj = end( json_decode( $response['body'] ) );
			$pantheon_latest_wp_version = basename( $latest_tag_obj->ref );
		} else {
			$pantheon_latest_wp_version = $wp_version;
		}

		set_transient( 'pantheon_latest_wp_version', $pantheon_latest_wp_version, HOUR_IN_SECONDS );
	}

	return $pantheon_latest_wp_version;
}

// Compare the current WordPress version to the latest available
function _pantheon_wordpress_update_available() {
	$latest_wp_version = _pantheon_get_latest_wordpress_version();

	// Bail if we don't have a valid WordPress version
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