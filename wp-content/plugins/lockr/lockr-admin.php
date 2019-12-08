<?php
/**
 * Admin form and submit handler for adding a key to Lockr.
 *
 * @package Lockr
 */

// Don't call the file directly and give up info!
if ( ! function_exists( 'add_action' ) ) {
	echo 'Lock it up!';
	exit;
}

use Lockr\Exception\LockrClientException;
use Lockr\Exception\LockrServerException;

// Include our admin forms and tables.
require_once LOCKR__PLUGIN_DIR . '/lockr-admin-config.php';
require_once LOCKR__PLUGIN_DIR . '/lockr-admin-add.php';
require_once LOCKR__PLUGIN_DIR . '/lockr-admin-edit.php';
require_once LOCKR__PLUGIN_DIR . '/lockr-admin-override.php';
require_once LOCKR__PLUGIN_DIR . '/class-lockr-key-list.php';

add_action( 'admin_menu', 'lockr_admin_menu' );
add_action( 'admin_init', 'register_lockr_settings' );
add_action( 'admin_post_lockr_admin_submit_add_key', 'lockr_admin_submit_add_key' );
add_action( 'admin_post_lockr_admin_submit_override_key', 'lockr_admin_submit_override_key' );
add_action( 'admin_post_lockr_admin_submit_edit_key', 'lockr_admin_submit_edit_key' );

/**
 * Create our admin pages and put them into the admin menu.
 */
function lockr_admin_menu() {
	$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMCAyMCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjAgMjA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxwYXRoIGlkPSJYTUxJRF8yODlfIiBkPSJNMTUuNSw5LjZoLTIuNFY0LjdjMC0xLjQtMC43LTIuMS0yLTIuMWgtMC44VjAuMmgwLjNjMy4yLDAsNC44LDEuNSw0LjgsNC42VjkuNnoiLz4NCgk8cGF0aCBpZD0iWE1MSURfMjg2XyIgZD0iTTQuNCwxMC4zdjQuNWMwLDMsMS43LDUsNC44LDVoMC4yaDEuMmgwLjFjMy4xLDAsNC43LTEuOSw1LTV2LTQuNUg0LjR6IE0xMC42LDE1LjJ2MS4yDQoJCWMwLDAuNC0wLjMsMC44LTAuNywwLjhjLTAuNCwwLTAuNy0wLjMtMC43LTAuOHYtMS4yYy0wLjMtMC4zLTAuOC0wLjgtMC44LTEuNGMwLTAuOSwwLjctMS42LDEuNi0xLjZjMC45LDAsMS42LDAuNywxLjYsMS42DQoJCUMxMS41LDE0LjMsMTAuOSwxNC45LDEwLjYsMTUuMnoiLz4NCgk8cGF0aCBpZD0iWE1MSURfMjc3XyIgZD0iTTQuNCw0LjdjMC4xLTMsMS43LTQuNiw0LjgtNC42aDAuM3YyLjVIOC44Yy0xLjMsMC0yLDAuNy0yLDIuMXY0LjlINC40VjQuN3oiLz4NCjwvZz4NCjwvc3ZnPg0K';
	add_menu_page( __( 'Lockr Key Storage', 'lockr' ), __( 'Lockr', 'lockr' ), 'manage_options', 'lockr', 'lockr_keys_table', $icon_svg );
	add_submenu_page( 'lockr', __( 'Lockr Key Storage', 'lockr' ), __( 'All Keys', 'lockr' ), 'manage_options', 'lockr' );
	add_submenu_page( 'lockr', __( 'Create Lockr Key', 'lockr' ), __( 'Add Key', 'lockr' ), 'manage_options', 'lockr-add-key', 'lockr_add_form' );
	add_submenu_page( 'lockr', __( 'Override Option', 'lockr' ), __( 'Override Option', 'lockr' ), 'manage_options', 'lockr-override-option', 'lockr_override_form' );
	add_submenu_page( null, __( 'Edit Lockr Key', 'lockr' ), __( 'Edit Key', 'lockr' ), 'manage_options', 'lockr-edit-key', 'lockr_edit_form' );
	add_submenu_page( 'lockr', __( 'Lockr Configuration', 'lockr' ), __( 'Lockr Configuration', 'lockr' ), 'manage_options', 'lockr-site-config', 'lockr_configuration_form' );
}

/**
 * Queue up our stylesheet for the admin interface.
 *
 * @param string $hook The name of the admin page we're on.
 */
function lockr_admin_styles( $hook ) {

	if ( 'lockr' === substr( $hook, 0, 5 ) ) {
		wp_enqueue_style( 'lockrStylesheet', plugins_url( 'css/lockr.css', __FILE__ ), array(), '2.4', 'all' );
		wp_enqueue_script( 'lockrScript', plugins_url( 'js/lockr.js', __FILE__ ), array(), '2.4', true );
		$status           = lockr_check_registration();
		$site_information = array(
			'name'       => get_option( 'blogname' ),
			'force_prod' => isset( $status['partner']['force_prod'] ) ? $status['partner']['force_prod'] : false,
			'keyring_id' => isset( $status['keyring_id'] ) ? $status['keyring_id'] : false,
		);
		wp_localize_script( 'lockrScript', 'lockr_settings', $site_information );
	} elseif ( 'post' === substr( $hook, 0, 4 ) ) {
		wp_enqueue_script( 'lockrScript', plugins_url( 'js/lockr-post.js', __FILE__ ), array(), '2.4', true );
	}

}
add_action( 'admin_enqueue_scripts', 'lockr_admin_styles' );

if ( ! get_option( 'lockr_partner' ) ) {
	$partner = lockr_get_partner();
	if ( $partner ) {
		add_option( 'lockr_partner', $partner['name'] );
	}
}

/**
 * Create a table of all the keys in Lockr.
 */
function lockr_keys_table() {

	global $wpdb;
	$table_name      = $wpdb->prefix . 'lockr_keys';
	$query           = "SELECT * FROM $table_name WHERE key_name = 'lockr_default_key'";
	$default_key     = $wpdb->get_results( $query ); // WPCS: unprepared SQL OK.
	$status          = lockr_check_registration();
	$exists          = $status['keyring_label'] ? true : false;
	$deleted_default = get_option( 'lockr_default_deleted' );
	$auto_created    = (int) $default_key[0]->auto_created;

	if ( $exists && ! $default_key && ! $deleted_default ) {
		// Create a default encryption key.
		$client    = lockr_client();
		$key_value = base64_encode( $client->generateKey( 256 ) );

		lockr_set_key( 'lockr_default_key', $key_value, 'Lockr Default Encryption Key', null, true );
	}
	if ( $default_key && ! $auto_created ) {
		$key_id    = array( 'id' => $default_key[0]->id );
		$key_data  = array( 'auto_created' => true );
		$key_store = $wpdb->update( $table_name, $key_data, $key_id );
	}

	if ( isset( $status['environment'] ) ) {

		if ( 'prod' === $status['environment'] ) {
			$environment = $status['environment'];
		} else {
			$environment = 'dev';
		}
		if ( ! get_option( 'lockr_' . $environment . '_abstract_migrated' ) ) {
			lockr_update_abstracts( $environment );
		}
	}

	$key_table = new Lockr_Key_List();
	$key_table->prepare_items();
	?>
	<div class="wrap">
		<?php if ( ! $exists ) : ?>
			<h1>Register Lockr First</h1>
			<p>Before you can add keys, you must first <a href="<?php echo esc_url( admin_url( 'admin.php?page=lockr-site-config' ) ); ?>">register your site</a> with Lockr.</p>
		<?php else : ?>
			<h1>Lockr Key Storage:</h1>
				<?php if ( isset( $_GET['message'] ) && 'success' === $_GET['message'] ) : ?>
					<div id='message' class='updated fade'><p><strong>You successfully added the key to Lockr.</strong></p></div>
				<?php endif; ?>
				<?php if ( isset( $_GET['message'] ) && 'editsuccess' === $_GET['message'] ) : ?>
					<div id='message' class='updated fade'><p><strong>You successfully edited your key in Lockr.</strong></p></div>
				<?php endif; ?>
				<p> Below is a list of the keys currently stored within Lockr. You may edit/delete from here or <a href="<?php echo esc_url( admin_url( 'admin.php?page=lockr-add-key' ) ); ?>">add one manually</a> for any plugins not yet supporting Lockr. </p>
				<form id="lockr-key-table" method="post">
			<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : ''; ?>" />
			<?php $key_table->display(); ?>
				</form>
		<?php endif; ?>
	</div>
	<?php
}
