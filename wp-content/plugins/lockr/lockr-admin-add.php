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

/**
 * Add a key to Lockr from the submitted form.
 */
function lockr_admin_submit_add_key() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to add a key.' );
	}

	check_admin_referer( 'lockr_admin_verify' );

	if ( isset( $_POST['key_label'] ) ) {
		$key_label = sanitize_text_field( wp_unslash( $_POST['key_label'] ) );
	} else {
		$key_label = '';
	}
	// Just incase our javascript didn't clean it up.
	if ( isset( $_POST['key_name'] ) ) {
		$key_name = strtolower( sanitize_text_field( wp_unslash( $_POST['key_name'] ) ) );
		$key_name = preg_replace( '@[^a-z0-9_]+@', '_', $key_name );
	} else {
		$key_name = '';
	}

	$auto_created = false;

	if ( isset( $_POST['create_key'] ) && 'on' === $_POST['create_key'] ) {
		// Create a default encryption key.
		$client       = lockr_client();
		$key_value    = base64_encode( $client->generateKey( 256 ) );
		$auto_created = true;
	} elseif ( isset( $_POST['key_value'] ) ) {
		$key_value = sanitize_text_field( wp_unslash( $_POST['key_value'] ) );
	} else {
		$key_value = '';
	}

	$key_store = lockr_set_key( $key_name, $key_value, $key_label, null, $auto_created );

	if ( false !== $key_store ) {
		// Successfully Added.
		wp_redirect( admin_url( 'admin.php?page=lockr&message=success' ) );
		exit;
	} else {
		// Failed Addition.
		wp_redirect( admin_url( 'admin.php?page=lockr-add-key&message=failed' ) );
		exit;
	}
}

/**
 * Create the form to add a key to Lockr.
 */
function lockr_add_form() {
	$status = lockr_check_registration();
	$exists = $status['keyring_label'] ? true : false;
	?>
	<div class="wrap">
		<?php if ( ! $exists ) : ?>
			<h1>Register Lockr First</h1>
			<p>Before you can add keys, you must first <a href="<?php echo esc_url( admin_url( 'admin.php?page=lockr-site-config' ) ); ?>">register your site</a> with Lockr.</p>
		<?php else : ?>
			<h1>Add a Key to Lockr</h1>
				<?php if ( isset( $_GET['message'] ) && 'failed' === $_GET['message'] ) : ?>
					<div id='message' class='updated fade'><p><strong>There was an issue in saving your key, please try again.</strong></p></div>
				<?php endif; ?>
				<p> Simply fill in the form below and we'll keep the key safe for you in Lockr.</p>
				<form method="post" action="admin-post.php">
					<input type="hidden" name="action" value="lockr_admin_submit_add_key" />
					<?php wp_nonce_field( 'lockr_admin_verify' ); ?>
					<div class="form-item key-label">
						<label for="key_label">Key Name:</label>
						<input type="text" name="key_label" placeholder="Your Key Name"/>
						<span class="machine-name-label">Machine Name:<a href="" class="show-key-name"></a></span>
					</div>
					<div class="form-item machine-name hidden">
						<label for="key_name">Key Machine Name:</label>
						<input type="text" name="key_name" placeholder=""/>
					</div>
					<div class="form-item">
						<label for="key_value">Key Value:</label>
						<input type="text" name="key_value" placeholder="Your Key Value" id="key_value"/>
						<input type="checkbox" name="create_key" id="create_key"/>
						<label for="create_key">Create a secure encryption key for me</label>
					</div>
					<br />
					<input type="submit" value="Add Key" class="button-primary"/>
				</form>
		<?php endif; ?>

	</div>

	<?php

}
