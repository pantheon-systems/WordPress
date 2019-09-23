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
	remove_action( 'network_admin_notices', 'update_nag', 3 );
}

// Compare the current WordPress version to the latest available
function _pantheon_wordpress_update_available() {

	if ( ! function_exists( 'pantheon_curl' ) ) {
		return false;
	}
	$data = _pantheon_curl_cached( 'https://api.live.getpantheon.com/sites/self/code-upstream-updates' );
	if ( ! empty( $data['dev'] ) && isset( $data['dev']['is_up_to_date_with_upstream'] ) ) {
		return $data['dev']['is_up_to_date_with_upstream'] ? false : true;
	}
	return false;
}

function _pantheon_curl_cached( $api_url ) {
	$cache_key   = 'pantheon_curl_' . md5( $api_url );
	$cache_value = get_transient( $cache_key );
	if ( false !== $cache_value ) {
		return $cache_value;
	}
	$api_response = pantheon_curl( $api_url, null, 8443 );
	$data = $api_response ? json_decode( $api_response['body'], true ) : [];
	set_transient( $cache_key, $data, 2 * MINUTE_IN_SECONDS );
	return $data;
}

// Replace WordPress core update nag EVERYWHERE with our own notice (use git upstream)
function _pantheon_upstream_update_notice() {
	$update_type = 'new WordPress version';
	$update_help = 'If you need help, open a support chat on Pantheon.';
	$data = _pantheon_curl_cached( 'https://api.live.getpantheon.com/sites/self/code-upstream-updates' );
	if ( ! empty( $data['remote_url'] ) && false === stripos( $data['remote_url'], '/pantheon-systems/' ) ) {
		$update_type = 'Pantheon Custom Upstream update';
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
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], Array('test', 'live') ) && (php_sapi_name() !== 'cli') ) {

	// Disable Plugin Updates
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	// Disable Theme Updates
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}


