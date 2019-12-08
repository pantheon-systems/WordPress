<?php
/**
 * Create the form and validate requests to override any option in WordPress with a value stored in Lockr.
 *
 * @package Lockr
 */

// Don't call the file directly and give up info!
if ( ! function_exists( 'add_action' ) ) {
	echo 'Lock it up!';
	exit;
}

/**
 * Submit handler for creating an override of a WordPress option.
 */
function lockr_admin_submit_override_key() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to add a key.' );
	}

	if ( isset( $_POST ) && ! empty( $_POST ) && check_admin_referer( 'lockr_admin_verify' ) ) {
		if ( isset( $_POST['option_total_number'] ) && is_numeric( sanitize_key( $_POST['option_total_number'] ) ) ) {

			$total_option_value = intval( $_POST['option_total_number'] );
			$option_path        = '';

			for ( $i = 1; $i < $total_option_value + 1; $i++ ) {
				if ( isset( $_POST[ 'option_value_' . $i ] ) ) {
					$option_path .= sanitize_key( $_POST[ 'option_value_' . $i ] ) . ':';
				}
			}
		} else {
			wp_die( 'This form has been tampered with.' );
		}

		$option_path = substr( $option_path, 0, -1 );
		$key_label   = str_replace( ':', ' - ', $option_path );
		$key_label   = ucwords( str_replace( '_', ' ', $key_label ) );

		$key_name = preg_replace( '@[^a-z0-9_]+@', '_', $option_path );

		if ( isset( $_POST['create_key'] ) && 'on' === $_POST['create_key'] ) {
			// Create a default encryption key.
			$client       = lockr_client();
			$key_value    = $client->generateKey( 256 );
			$auto_created = true;
		} else {
			$auto_created = false;
			if ( isset( $_POST['key_value'] ) ) {
				$key_value = sanitize_text_field( wp_unslash( $_POST['key_value'] ) );
			} else {
				$key_value = '';
			}
		}

		$key_store = lockr_set_key( $key_name, $key_value, $key_label, $option_path, $auto_created );

		if ( $key_store ) {
			// Successfully Added so save the option to replace the value.
			if ( isset( $_POST['option_value_1'] ) ) {
				$option_name = sanitize_key( $_POST['option_value_1'] );
			} else {
				$option_name = '';
			}

			$existing_option = get_option( $option_name );
			if ( $existing_option ) {
				if ( is_array( $existing_option ) ) {
					$new_option_array = explode( ':', $option_path );
					array_shift( $new_option_array );

					$serialized_data_ref = &$existing_option;
					foreach ( $new_option_array as $option_key ) {
						$serialized_data_ref = &$serialized_data_ref[ $option_key ];
					}
					$serialized_data_ref = 'lockr_' . $key_name;

					update_option( $option_name, $existing_option, false );
				} else {
					update_option( $option_name, 'lockr_' . $key_name, false );
				}
			}

			wp_safe_redirect( admin_url( 'admin.php?page=lockr&message=success' ) );
			exit;
		} else {
			// Failed Addition.
			wp_safe_redirect( admin_url( 'admin.php?page=lockr-override-option&message=failed' ) );
			exit;
		}
	}
}

/**
 * Form builder for creating an override of a WordPress option.
 */
function lockr_override_form() {
	$status    = lockr_check_registration();
	$exists    = $status['keyring_label'] ? true : false;
	$blacklist = array(
		'active_plugins',
		'cron',
		'auto_core_update_notified',
		'recently_activated',
		'rewrite_rules',
		'uninstall_plugins',
		'wp_user_roles',
	);

	$options = array();

	global $wpdb;
	$query       = "SELECT * FROM $wpdb->options ORDER BY option_name";
	$options_raw = $wpdb->get_results( $query ); // WPCS: unprepared SQL OK.

	foreach ( (array) $options_raw as $option_raw ) {
		$serialized = false;
		$value      = '';
		if ( '' === $option_raw->option_name ) {
			continue;
		}
		if ( is_serialized( $option_raw->option_value ) ) {
			if ( is_serialized_string( $option_raw->option_value ) ) {
				$value = maybe_unserialize( $option_raw->option_value );
				if ( 'lockr' === substr( $value, 0, 5 ) ) {
					$value = false;
				}
			} else {
				$value           = array();
				$serialized_data = maybe_unserialize( $option_raw->option_value );
				foreach ( $serialized_data as $serial_key => $serial_value ) {
					if ( is_string( $serial_value ) ) {
						if ( 'lockr' !== substr( $serial_value, 0, 5 ) ) {
							$value[ $serial_key ] = $serial_value;
						}
					} else {
						$value[ $serial_key ] = $serial_value;
					}
				}
			}
		} else {
			if ( substr( 'lockr' !== $option_raw->option_value, 0, 5 ) ) {
				$value = $option_raw->option_value;
			} else {
				$value = false;
			}
		}
		$name = esc_attr( $option_raw->option_name );
		if ( $value && '_site' !== substr( $name, 0, 5 ) && 'lockr' !== substr( $name, 0, 5 ) && '_transient' !== substr( $name, 0, 10 ) && ! in_array( $name, $blacklist ) ) {
			$options[ $name ] = $value;
		}
	}
	wp_enqueue_script( 'lockrjs' );
	?>
	<div class="wrap">
		<?php if ( ! $exists ) : ?>
			<h1>Register Lockr First</h1>
			<p>Before you can add keys, you must first <a href="<?php echo esc_url( admin_url( 'admin.php?page=lockr-site-config' ) ); ?>">register your site</a> with Lockr.</p>
		<?php else : ?>
			<h1>Override an option with Lockr</h1>
				<?php if ( isset( $_GET['message'] ) && 'failed' === $_GET['message'] ) : ?>
					<div id='message' class='updated fade'><p><strong>There was an issue in saving your key, please try again.</strong></p></div>
				<?php endif; ?>
				<p> With Lockr you can override any value in the options table with a value in Lockr. This allows you to store any secrets or passwords from plugins safely out of your database.</p>
				<form method="post" action="admin-post.php">
					<input type="hidden" name="action" value="lockr_admin_submit_override_key" />
					<input type="hidden" name="option_total_number" id="option-total-number" value="1" />
					<?php wp_nonce_field( 'lockr_admin_verify' ); ?>
					<div class="form-item option-name">
						<label for="option_value_1">Option to override:</label>
						<select name="option_value_1" class="option-override-select" id="option-override-1">
							<option value="">Select an Option</option>
						<?php
						foreach ( $options as $option => $value ) {
							$value = wp_json_encode( $value );
							print( '<option value ="' . esc_attr( $option ) . '" data-option-value="' . esc_attr( htmlentities( $value ) ) . '" >' . esc_html( $option ) . '</option>' );
						}
						?>
						</select>
					</div>
					<div class="form-item">
						<label for="key_value">Key Value:</label>
						<input type="text" name="key_value" placeholder="Your Key Value" id="key_value"/>
						<input type="checkbox" name="create_key" id="create_key"/>
						<label for="create_key">Create a secure encryption key for me</label>
					</div>
					<br />
					<input type="submit" value="Add Override" class="button-primary"/>
				</form>
		<?php endif; ?>

	</div>
	<?php
}
