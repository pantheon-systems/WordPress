<?php
/**
 * Admin form for registering and managing the Lockr account.
 *
 * @package Lockr
 */

use Lockr\Exception\LockrApiException;

// Don't call the file directly and give up info!
if ( ! function_exists( 'add_action' ) ) {
	echo 'Lock it up!';
	exit;
}

/**
 * Create our admin settings fields.
 */
function register_lockr_settings() {
	register_setting( 'lockr_options', 'lockr_options', 'lockr_options_validate' );
	add_settings_section(
		'lockr_token',
		'Client Token',
		'lockr_token_text',
		'lockr_register_token'
	);

	add_settings_field(
		'lockr_client_token',
		'',
		'lockr_client_token_input',
		'lockr',
		'lockr_token'
	);

	add_settings_field(
		'lockr_client_prod_token',
		'',
		'lockr_client_prod_token_input',
		'lockr',
		'lockr_token'
	);

	add_settings_section(
		'lockr_advanced',
		'Lockr Advanced Settings',
		'lockr_advanced_text',
		'lockr'
	);
	add_settings_field(
		'lockr_region',
		'Lockr Region',
		'lockr_region_input',
		'lockr',
		'lockr_region'
	);
	add_settings_field(
		'lockr_cert_path',
		'Custom Certificate Location',
		'lockr_cert_path_input',
		'lockr',
		'lockr_advanced'
	);

	add_settings_field(
		'lockr_encrypt_posts',
		'Encrypt Password Protected Posts',
		'lockr_encrypt_posts_input',
		'lockr',
		'lockr_encrypt_posts'
	);

	add_settings_field(
		'lockr_hash_pass',
		'Securely Hash Post Passwords',
		'lockr_hash_pass_input',
		'lockr',
		'lockr_hash_pass'
	);
}

/**
 * Create token text field.
 */
function lockr_token_text() {
}

/**
 * Create advanced text field.
 */
function lockr_advanced_text() {
}

/**
 * Create Lockr token text input.
 */
function lockr_client_token_input() {

	?>
<input id="lockr_client_token"
	name="lockr_options[lockr_client_token]"
	size="60"
	type="hidden"
	value="" />

	<?php
}

/**
 * Create Lockr prod token text input.
 */
function lockr_client_prod_token_input() {

	?>
<input id="lockr_client_prod_token"
	name="lockr_options[lockr_client_prod_token]"
	size="60"
	type="hidden"
	value="" />

	<?php
}

/**
 * Create Lockr cert path text field.
 */
function lockr_cert_path_input() {
	if ( ! get_option( 'lockr_partner' ) || get_option( 'lockr_partner' ) === 'custom' ) {
		$partner   = false;
		$cert_path = get_option( 'lockr_cert' );
	} else {
		$partner   = true;
		$cert_path = '';
	}
	?>
	<?php if ( ! $partner ) : ?>
		<p>
			Use the following field to set the location of your custom certificate.
			If you are on a supported hosting provider you do not need to enter any value here.
		</p>
	<?php else : ?>
		<p>
			<strong>Our system has detected that your website is hosted on one of our supported providers,
			this setting is not necessary under regular usage.</strong>
		</p>
		<p>Use the following field to set the location of your custom certificate.</p>
	<?php endif; ?>

	<input id="lockr_cert_path"
		name="lockr_options[lockr_cert_path]"
		size="60"
		type="text"
		value="<?php echo esc_attr( $cert_path ); ?>" />

	<?php
}

/**
 * Create Lockr password text field.
 */
function lockr_account_password_input() {
	$options = get_option( 'lockr_options' );

	?>
	<input id="lockr_account_email"
		name="lockr_options[account_password]"
		size="60"
		type="password"
		value="<?php echo esc_attr( $options['account_password'] ); ?>" />

	<?php
}

/**
 * Get the region input value.
 */
function lockr_region_input() {
	$region = get_option( 'lockr_region' );

	?>
	<p>
	Lockr speeds up key retrievals and helps to achieve compliance with data
	protection laws by providing multiple regions to store your keys in.
	<br><strong>Note: changing this after storing keys will require you to
	re-input all keys stored in Lockr.</strong>
	</p><br>
	<fieldset>
		<label>
		<input type="radio"
			name="lockr_options[lockr_region]"
			id="lockr-region-us"
			<?php if ( 'us' === $region || false === $region ) : ?>
				checked="checked"
			<?php endif; ?>
			value="us" />
		<span>United States</span>
		</label>
		<br>
		<label>
			<input type="radio"
				name="lockr_options[lockr_region]"
				id="lockr-region-eur"
				<?php if ( 'eu' === $region ) : ?>
				checked="checked"
				<?php endif; ?>
				value="eu" />
			<span>European Union</span>
		</label>
	</fieldset>

	<?php
}

/**
 * Get the password hash input value.
 */
function lockr_encrypt_posts_input() {
	$encrypt_posts = get_option( 'lockr_encrypt_posts', false );

	?>
	<p>
	By default, WordPress stores password protected posts in plain text
	in the database. By checking this box, Lockr will store password protected
	posts in the database with AES encryption. This prevents anyone with a copy
	of the database from reading password protected posts.
	</p><br>
	<label>
		<input type="checkbox" id="lockr_encrypt_posts"
			name="lockr_options[lockr_encrypt_posts]"
			<?php if ( $encrypt_posts ) : ?>
				checked="checked"
			<?php endif; ?>
			/>
		<span> Encrypt password protected posts </span>
	</label>
	<?php
}

/**
 * Get the password hash input value.
 */
function lockr_hash_pass_input() {
	$hash_pass       = get_option( 'lockr_hash_pass', false );
	$native_sessions = is_plugin_active( 'wp-native-php-sessions/pantheon-sessions.php' );
	?>
	<p>
	By default, WordPress stores passwords used to protect posts in plain text
	in the database. This creates an issue of password security if anyone re-uses
	passwords for admin login. By checking this box, Lockr will store passwords hashed
	in the database using the same one-way hashing functions WordPress uses for user logins.
	This prevents anyone with a copy of the database from seeing passwords used to protect posts.
	(Requires the <a href="https://wordpress.org/plugins/wp-native-php-sessions/">WordPress Native php
	Sessions Plugin</a>)
	</p><br>
	<label>
		<input type="checkbox" id="lockr_hash_pass"
			name="lockr_options[lockr_hash_pass]"
			<?php if ( $hash_pass ) : ?>
				checked="checked"
			<?php endif; ?>
			<?php if ( ! $native_sessions ) : ?>
				disabled="disabled"
			<?php endif; ?>
			/>
		<span> Hash post passwords </span>
	</label>
	<?php

}

/**
 * Validate the form submissions.
 *
 * @param array $input the input values from the form.
 */
function lockr_options_validate( $input ) {
	$options = get_option( 'lockr_options' );
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	if ( ! isset( $input['lockr_op'] ) ) {
		return $options;
	}
	$op = $input['lockr_op'];

	if ( 'createClient' === $op ) {

		$client_token      = sanitize_key( $input['lockr_client_token'] );
		$client_prod_token = sanitize_key( $input['lockr_client_prod_token'] );
		$partner           = lockr_get_partner();

		if ( empty( $partner ) ) {
			$success = create_certs( $client_token );
		} else {
			$success = lockr_partner_register( $client_token, $client_prod_token, $partner );
		}

		if ( $success ) {
			update_option( 'lockr_partner', 'custom' );
			delete_option( 'lockr_cert' );
		} else {
			add_settings_error(
				'lockr_options',
				'lockr-csr',
				'Lockr encountered an unexpected error, please try again. If you continue to experience this error please contact Lockr support.'
			);
		}
	} elseif ( 'migrate' === $op ) {

		$client_token      = sanitize_key( $input['lockr_client_token'] );
		$client_prod_token = sanitize_key( $input['lockr_client_prod_token'] );
		$partner           = lockr_get_partner();

		if ( empty( $partner ) ) {
			$success = create_certs( $client_token );
		} else {
			$success = lockr_partner_register( $client_token, $client_prod_token, $partner );
		}

		if ( $success ) {
			update_option( 'lockr_partner', 'custom' );
			delete_option( 'lockr_cert' );
			update_option( 'lockr_prod_migrate', true );
		} else {
			add_settings_error(
				'lockr_options',
				'lockr-csr',
				'Lockr encountered an unexpected error, please try again. If you continue to experience this error please contact Lockr support.'
			);
		}
	} elseif ( 'advanced' === $op ) {
		$cert_path = trim( $input['lockr_cert_path'] );

		if ( $cert_path ) {
			if ( '/' !== $cert_path[0] ) {
				$cert_path = ABSPATH . $cert_path;
			}

			if ( ! is_readable( $cert_path ) ) {
				add_settings_error(
					'lockr_options',
					'lockr-cert-path',
					"{$cert_path} must be a readable file."
				);

				return $options;
			}

			update_option( 'lockr_partner', 'custom' );
			update_option( 'lockr_cert', $cert_path );
		} else {
			$partner = lockr_get_partner();
			if ( $partner ) {
				update_option( 'lockr_partner', $partner['name'] );
				delete_option( 'lockr_cert' );
			} else {
				update_option( 'lockr_partner', '' );
				delete_option( 'lockr_cert' );
			}
		}

		update_option( 'lockr_encrypt_posts', $input['lockr_encrypt_posts'] );
		update_option( 'lockr_hash_pass', $input['lockr_hash_pass'] );
		update_option( 'lockr_region', $input['lockr_region'] );
	}
}

/**
 * Create the HTML of the Lockr configuration form.
 */
function lockr_configuration_form() {
	require_once LOCKR__PLUGIN_DIR . '/class-lockr-status.php';

	$status      = lockr_check_registration();
	$errors      = get_settings_errors();
	$error_codes = array();
	foreach ( $errors as $error ) {
		$error_codes[] = $error['code'];
	}

	?>
	<div class="wrap lockr-config">
		<h1>Lockr Setup</h1>

		<?php

		settings_errors();

		$cert_valid   = $status['valid_cert'];
		$partner      = lockr_get_partner();
		$prod_migrate = get_option( 'lockr_prod_migrate', false );

		if ( null === $partner ) {
			if ( file_exists( ABSPATH . '.lockr/prod/pair.pem' ) ) {
				$migrate_possible = false;
				$partner_certs    = false;
			} else {
				$migrate_possible = true;
				$partner_certs    = false;
			}
		}

		if ( $partner ) {
			$migrate_possible = ! $partner['force_prod'];
			$partner_certs    = $partner['partner_certs'];
		}

		if ( $partner ) {
			?>

			<h2>Hello <?php echo esc_attr( $partner['title'] ); ?> Customer!</h2>
			<p><?php echo esc_attr( $partner['description'] ); ?></p>
			<?php

		}
		if ( $cert_valid ) {

			?>
			<p>
			All systems are go!
			Your site is connected to a KeyRing, your certificate is valid, and everything seems
			good on our end.
			To make things simple we've laid out a few key elements (pun intended)
			that the system requires in order to run.
			Should anything look out of the ordinary just let us know on the
			<a href="http://slack.lockr.io">Slack</a>
			channel and we'd be happy to help.
			Happy Keying!
			</p>
			<h2>Status Table</h2>
			<?php
		} else {
			?>
			<p>
			Welcome to Lockr!
			You're just a few steps away from storing your keys safe and secure.
			To make things simple we've laid out a few key elements (pun intended)
			that the system requires in order to run.
			Fill out the forms below and once all rows on the table are green,
			you're good to go!
			It's that simple!
			If you'd like assistance in getting up and running be sure to
			<a href="mailto:support@lockr.io">email us</a>
			or connect with us on <a href="http://slack.lockr.io">Slack</a>.
			</p>
			<h2>Status Table</h2>
			<?php
		}
		$status_table = new Lockr_Status();
		$status_table->prepare_items();
		$status_table->display();

		?>

		<form id="client-token" method="post" action="options.php">

		<?php
		settings_fields( 'lockr_options' );

		if ( ! $cert_valid ) {
			?>
			<p>
			You're one click away from getting everything setup! Click on the button below and we'll
			pop up a window that will help you to create a new KeyRing (or connect to an existing one).
			Simply follow the prompts in that window and we'll do the rest.
			</p>
			<button type="button" id="token-button" class="button button-primary">Connect Site to a KeyRing</button>
			<?php do_settings_fields( 'lockr', 'lockr_token' ); ?>
			<input id="lockr_op"
				name="lockr_options[lockr_op]"
				type="hidden"
				value="createClient" />
			<?php submit_button( 'Create KeyRing Client' ); ?>
			<?php
		} elseif ( 'dev' === $status['environment'] && $migrate_possible && ! $partner_certs && ! $prod_migrate ) {
			?>
			<p>
			Click the button below to deploy this site to production.
			This should only be done in your production environment as it writes
			a new certificate to the file system.
			</p>
			<button type="button" id="token-button" class="button button-primary">Migrate to Production</button>
			<?php do_settings_fields( 'lockr', 'lockr_token' ); ?>
			<input id="lockr_op"
				name="lockr_options[lockr_op]"
				type="hidden"
				value="migrate" />
			<?php submit_button( 'Migrate to Production' ); ?>
			<?php
		}
		?>
		</form>
		<hr>
		<hr>
		<h1>Advanced Configuration</h1>
		<form method="post" action="options.php">
		<?php settings_fields( 'lockr_options' ); ?>
		<table class="form-table">
			<?php do_settings_fields( 'lockr', 'lockr_advanced' ); ?>
			<?php do_settings_fields( 'lockr', 'lockr_region' ); ?>
			<?php do_settings_fields( 'lockr', 'lockr_encrypt_posts' ); ?>
			<?php do_settings_fields( 'lockr', 'lockr_hash_pass' ); ?>
		</table>
		<input id="lockr_op"
				name="lockr_options[lockr_op]"
				type="hidden"
				value="advanced" />
		<?php submit_button( 'Save Advanced Settings', 'secondary' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Remove a directory recursively.
 *
 * @param string $path The name of the path to delete.
 */
function _lockr_rmtree( $path ) {
	if ( is_dir( $path ) ) {
		foreach ( scandir( $path, SCANDIR_SORT_NONE ) as $name ) {
			if ( '.' === $name || '..' === $name ) {
				continue;
			}

			_lockr_rmtree( "{$path}/{$name}" );
		}
	}
}
