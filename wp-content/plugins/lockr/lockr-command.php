<?php
/**
 * WPCLI Integration for Lockr.
 *
 * @package Lockr
 */

use Lockr\Exception\LockrClientException;
use Lockr\Exception\LockrServerException;

/**
 * Allow for key retrieval from WP-CLI.
 *
 * @param array $args an array of arguments passed into the command.
 * @param array $assoc_args an array of associated arguments passed into the command.
 */
function lockr_command_get_key( $args, $assoc_args ) {
	// Get our key name from one of 2 ways.
	$key_name = $args[0];
	if ( ! $key_name ) {
		$key_name = $assoc_args['key'];
	}
	if ( ! $key_name ) {
		WP_CLI::error( 'No key name provided' );
	}

	$key = lockr_get_key( $key_name );
	if ( $key ) {
		WP_CLI::success( $key );
	} else {
		WP_CLI::error( 'No Key Found' );
	}
}


WP_CLI::add_command( 'lockr get key', 'lockr_command_get_key' );

/**
 * Register a site from WP-CLI.
 *
 * @param array $args an array of arguments passed into the command.
 * @param array $assoc_args an array of associated arguments passed into the command.
 */
function lockr_command_register_site( $args, $assoc_args ) {
	$status = lockr_check_registration();
	$exists = $status['keyring_label'] ? true : false;

	if ( $exists ) {
		WP_CLI::error( 'This site is already registered with Lockr.' );
	}

	$name = get_bloginfo( 'name', 'display' );

	if ( ! $assoc_args['email'] ) {
		WP_CLI::error( 'No Email Provided' );
	}

	if ( ! filter_var( $assoc_args['email'], FILTER_VALIDATE_EMAIL ) ) {
		WP_CLI::error( $assoc_args['email'] . ' is not a valid email address' );
	}
	try {
		lockr_site_client()->register( $assoc_args['email'], null, $name );
	} catch ( LockrClientException $e ) {
		if ( ! $assoc_args['password'] ) {
			WP_CLI::error( 'Lockr account already exists for this email, please provide a password to authenticate and register site.' );
		} else {
			try {
				lockr_site_client()->register( $assoc_args['email'], $assoc_args['password'], $name );
			} catch ( LockrClientException $e ) {
				WP_CLI::error( 'Login credentials incorrect, please try again.' );
			} catch ( LockrServerException $e ) {
				WP_CLI::error( 'An unknown error has occurred, please try again later.' );
			}
		}
	} catch ( LockrServerException $e ) {
		WP_CLI::error( 'An unknown error has occurred, please try again later.' );
	}
	$status = lockr_check_registration();
	$exists = $status['keyring_label'] ? true : false;

	if ( $exists ) {
		WP_CLI::success( "Site is now registered with Lockr. You're good to start setting keys" );
	} else {
		WP_CLI::error( 'An unknown error has occurred, please try again later.' );
	}
}

WP_CLI::add_command( 'lockr register site', 'lockr_command_register_site' );

/**
 * Set a key from WP CLI.
 *
 * @param array $args an array of arguments passed into the command.
 * @param array $assoc_args an array of associated arguments passed into the command.
 */
function lockr_command_set_key( $args, $assoc_args ) {
	if ( ! $assoc_args['name'] ) {
		WP_CLI::error( 'Please provide a key machine name with --name=[key name]. This must be all lowercase with no spaces or dashes, underscores are ok.' );
	}
	if ( ! $assoc_args['value'] ) {
		WP_CLI::error( 'No key value provided, please provide one with --key=[key value] . ' );
	}
	if ( ! $assoc_args['label'] ) {
		WP_CLI::error( 'No key label provided, please provide one with --label=[key label]. This is the display name for the key.' );
	}

	$key_name  = $assoc_args['name'];
	$key_value = $assoc_args['value'];
	$key_label = $assoc_args['label'];

	// Double check our key name is properly formatted.
	$key_name = strtolower( $key_name );
	$key_name = preg_replace( '@[^a-z0-9_]+@', '_', $key_name );

	$key = lockr_set_key( $key_name, $key_value, $key_label );

	if ( $key ) {
		WP_CLI::success( $key_label . ' added to Lockr.' );
	} else {
		WP_CLI::error( $key_label . ' was not added to Lockr. Please try again.' );
	}
}

WP_CLI::add_command( 'lockr set key', 'lockr_command_set_key' );

/**
 * Apply patches to plugins for Lockr.
 *
 * @param array $args an array of arguments passed into the command.
 * @param array $assoc_args an array of associated arguments passed into the command.
 */
function lockr_command_lockdown( $args, $assoc_args ) {
	$raw_path = 'https://raw.githubusercontent.com/CellarDoorMedia/Lockr-Patches/wp';

	$reg_file = "{$raw_path}/registry.json";
	WP_CLI::log( "Downloading registry file: {$reg_file}." );
	$registry = file_get_contents( $reg_file );
	$registry = json_decode( $registry, true );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		WP_CLI::error( 'There was an error downloading the patch registry.' );
	}

	$names = implode( ', ', array_keys( $registry ) );
	WP_CLI::log( "Patches available for: {$names}." );

	$plugin_dir = WP_PLUGIN_DIR;
	$plugins    = get_plugins();

	foreach ( $registry as $name => $patches ) {
		if ( ! isset( $plugins[ $name ] ) ) {
			WP_CLI::log( "Plugin not found: {$name}." );
			continue;
		}

		$plugin_version = $plugins[ $name ]['Version'];
		if ( ! in_array( $plugin_version, array_keys( $patches ) ) ) {
			WP_CLI::log( "Plugin version not supported: {$name} ({$plugin_version})." );
			continue;
		}

		$path = $patches[ $plugin_version ];

		$plugin_path = dirname( "{$plugin_dir}/{$name}" );

		if ( ! is_dir( $plugin_path ) ) {
			WP_CLI::log( "Plugin path does not exist: {$plugin_path}." );
			continue;
		}

		// The lockfile prevents double-patching a plugin if lockdown is
		// called more than once. Applying a patch more than once can be
		// disastrous, and we don't want that.
		$lockfile = "{$plugin_path}/.lockr-patched";
		if ( is_file( $lockfile ) ) {
			WP_CLI::log( "{$name} already patched." );
			WP_CLI::log( "Remove {$lockfile} to patch again." );
			WP_CLI::log( 'Do so at your own peril.' );
			continue;
		}

		$patch_path   = "{$plugin_path}/key-integration.patch";
		$patch_remote = "{$raw_path}/{$path}";
		WP_CLI::log( "Downloading {$patch_remote}." );
		copy( $patch_remote, $patch_path );

		WP_CLI::log( "Patching {$name}." );
		$cmd = implode(
			' ',
			array(
				'patch',
				// We do not need a backup because reverting the patch can be done
				// via the user's version control system.
				'--no-backup-if-mismatch',
				'-N',
				'-p1',
				'-d',
				escapeshellarg( $plugin_path ),
				'<',
				escapeshellarg( $patch_path ),
			)
		);
		WP_CLI::log( "Running `{$cmd}`." );
		ob_start();
		passthru( $cmd, $return_code );
		WP_CLI::log( ob_get_clean() );

		if ( 0 === $return_code ) {
			// Patch is OK, go ahead and write the lockfile and remove the
			// downloaded patch.
			WP_CLI::log( 'Patch successful, writing lockfile.' );
			file_put_contents( $lockfile, '' );
			unlink( $patch_path );
		} else {
			WP_CLI::error( "Failed to patch {$name}.", false );
			WP_CLI::error( "Patch file left at '{$patch_path}'.", false );
		}
	}
}

WP_CLI::add_command( 'lockr lockdown', 'lockr_command_lockdown' );

