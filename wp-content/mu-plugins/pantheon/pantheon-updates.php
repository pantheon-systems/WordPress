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

// Return upstream's org and repository names, or false if not a custom upstream
// (i.e. Pantheon WordPress or wordpress-network )
function _pantheon_fetch_custom_upstream_info() {
	$data = _pantheon_curl_cached( 'https://api.live.getpantheon.com/sites/self/code-upstream-updates' );
	if ( empty( $data['remote_url'] ) || false !== stripos( $data['remote_url'], '/pantheon-systems/' )) {
		// remote_url was missing or this is not a custom upstream
		return false;
	}
	$url_path = ltrim( parse_url( $data['remote_url'], PHP_URL_PATH ), '/' );
	return str_replace( '.git', '', $url_path );
}

// Check if Pantheon upstream updates are available.
function _pantheon_wordpress_update_available() {

	if ( ! function_exists( 'pantheon_curl_timeout' ) ) {
		return false;
	}

	/**
	 * If the site is using the default WordPress upstream and
	 * WordPress is up to date, do not show the update notice
	 */
	if( ! _pantheon_fetch_custom_upstream_info() && _pantheon_is_wordpress_core_latest() ) {
		return false;
	}

	$upstream_updates_api_url = 'https://api.live.getpantheon.com/sites/self/code-upstream-updates';
	if ( 'dev' != $_ENV['PANTHEON_ENVIRONMENT'] ) {
		$upstream_updates_api_url .= '?base_branch=refs%2Fheads%2F'.$_ENV['PANTHEON_ENVIRONMENT'];
	}

	$data = _pantheon_curl_cached( $upstream_updates_api_url );
	if ( empty( $data['update_log'] ) ) {
		return false;
	}
	return true;
}

function _pantheon_curl_cached( $api_url ) {
	$cache_key   = 'pantheon_curl_' . md5( $api_url );
	$cache_value = get_transient( $cache_key );
	if ( false !== $cache_value ) {
		return $cache_value;
	}
	$api_response = pantheon_curl_timeout( $api_url, null, 8443 );
	$data = $api_response ? json_decode( $api_response['body'], true ) : [];
	set_transient( $cache_key, $data, 2 * MINUTE_IN_SECONDS );
	return $data;
}

// Replace WordPress core update nag EVERYWHERE with our own notice (use git upstream)
function _pantheon_upstream_update_notice() {
	$update_type = 'new WordPress version';
	$update_help = 'If you need help, open a support chat on Pantheon.';
	$upstream_path = _pantheon_fetch_custom_upstream_info();
	if ( ! empty( $upstream_path ) ) {
		$update_type = 'Pantheon Custom Upstream update from "'.$upstream_path.'"';
		$update_help = 'If you need help, contact an administrator for your Pantheon organization.';
	}

    ?>
    <div class="update-nag">
		<p style="font-size: 14px; font-weight: bold; margin: 0 0 0.5em 0;">
			A <?php echo $update_type; ?> is available! Please update from <a href="https://dashboard.pantheon.io/sites/<?php echo $_ENV['PANTHEON_SITE']; ?>">your Pantheon dashboard</a>.
		</p>
		For details on applying updates, see the <a href="https://pantheon.io/docs/upstream-updates/" target="_blank">Applying Upstream Updates</a> documentation. <br />
		<?php echo $update_help; ?>
	</div>
    <?php
}

// Register Pantheon specific WordPress update admin notice
add_action( 'admin_init', '_pantheon_register_upstream_update_notice' );
function _pantheon_register_upstream_update_notice(){
	// but only if we are on Pantheon
	// and this is not a WordPress Ajax request
	// and there is a WordPress update available
	if( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && ! wp_doing_ajax() && _pantheon_wordpress_update_available() ){
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
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array('test', 'live') ) && (php_sapi_name() !== 'cli') ) {

	// Disable Plugin Updates
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}
