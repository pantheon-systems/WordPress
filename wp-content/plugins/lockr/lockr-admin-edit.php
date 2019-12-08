<?php
/**
 * Create the form and validate requests to edit a key stored in Lockr.
 *
 * @package Lockr
 */

// Don't call the file directly and give up info!
if ( ! function_exists( 'add_action' ) ) {
	echo 'Lock it up!';
	exit;
}

/**
 * Submit handler for editing an existing key in Lockr.
 */
function lockr_admin_submit_edit_key() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to edit a key.' );
	}

	check_admin_referer( 'lockr_admin_verify' );

	if ( isset( $_POST['key_label'] ) ) {
		$key_label = sanitize_text_field( wp_unslash( $_POST['key_label'] ) );
	} else {
		$key_label = '';
	}

	if ( isset( $_POST['key_name'] ) ) {
		$key_name = sanitize_key( wp_unslash( $_POST['key_name'] ) );
	} else {
		$key_name = '';
	}

	if ( isset( $_POST['key_value'] ) ) {
		$key_value = sanitize_text_field( wp_unslash( $_POST['key_value'] ) );
	} else {
		$key_value = '';
	}

	$key_store = lockr_set_key( $key_name, $key_value, $key_label );

	if ( false !== $key_store ) {
		// Successfully Added.
		wp_redirect( admin_url( 'admin.php?page=lockr&message=editsuccess' ) );
		exit;
	} else {
		// Failed Addition.
		wp_redirect( admin_url( 'admin.php?page=lockr-edit-key&key=' . $key_name . '&message=failed' ) );
		exit;
	}
}

/**
 * Constructs a form to edit an existing key in Lockr.
 */
function lockr_edit_form() {
	$status = lockr_check_registration();
	$exists = $status['keyring_label'] ? true : false;

	global $wpdb;
	$table_name = $wpdb->prefix . 'lockr_keys';
	if ( isset( $_GET['key'] ) ) {
		$key_name = sanitize_key( $_GET['key'] );
	} else {
		$key_name = '';
	}

	$key = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE key_name = %s", array( $key_name ) ) ); // WPCS: unprepared SQL OK.

	?>
	<div class="wrap">
		<?php if ( ! $exists ) : ?>
			<h1>Register Lockr First</h1>
			<p>Before you can add keys, you must first <a href="<?php echo esc_url( admin_url( 'admin.php?page=lockr-site-config' ) ); ?>">register your site</a> with Lockr.</p>
		<?php else : ?>
			<h1>Edit <?php print esc_attr( $key->key_label ); ?> Key in Lockr</h1>
				<?php if ( isset( $_GET['message'] ) && 'failed' === $_GET['message'] ) : ?>
					<div id='message' class='updated fade'><p><strong>There was an issue editing your key, please try again.</strong></p></div>
				<?php endif; ?>
				<p> Simply edit your key below and we'll update and store it safe for you in Lockr.</p>
				<form method="post" action="admin-post.php">
					<input type="hidden" name="action" value="lockr_admin_submit_edit_key" />
					<?php wp_nonce_field( 'lockr_admin_verify' ); ?>
					<div class="form-item key-label">
						<label for="key_label">Key Name:</label>
						<input type="text" name="key_label" placeholder="Your Key Name" value="<?php print esc_attr( $key->key_label ); ?>" />
						<?php if ( isset( $key->key_name ) ) : ?>
						<span class="machine-name-label">Machine Name: <?php print esc_attr( $key->key_name ); ?></span>
						<?php else : ?>
						<span class="machine-name-label">Machine Name:</span>
						<?php endif; ?>
					</div>
					<div class="form-item machine-name hidden disabled">
						<label for="key_name">Key Machine Name:</label>
						<input type="text" name="key_name" placeholder="" value="<?php print esc_attr( $key->key_name ); ?>"/>
					</div>
					<div class="form-item">
						<label for="key_value">Key Value:</label>
						<input type="text" name="key_value" placeholder="Your Key Value" value="<?php print esc_attr( $key->key_abstract ); ?>"/>
					</div>
					<br />
					<input type="submit" value="Save Key" class="button-primary"/>
				</form>
		<?php endif; ?>

	</div>

	<?php
}
