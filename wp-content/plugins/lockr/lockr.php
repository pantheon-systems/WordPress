<?php
/**
 * Main functions and functionality for Lockr in WordPress.
 *
 * @package Lockr
 */

/*
Plugin Name: Lockr
Plugin URI: https://lockr.io/
Description: Integrate with the Lockr hosted secrets management platform. Secure all your plugin passwords, API tokens and encryption keys according to industry best practices. With Lockr, secrets management is easy.
Version: 3.0
Author: Lockr
Author URI: htts://lockr.io/
License: GPLv2 or later
Text Domain: lockr
*/

// Don't call the file directly and give up info!
if ( ! function_exists( 'add_action' ) ) {
	echo 'Lock it up!';
	exit;
}

define( 'LOCKR__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LOCKR__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Create database table for keys in the system.
 *
 * @file
 */

register_activation_hook( __FILE__, 'lockr_install' );

/**
 * Hook implementations and callbacks for lockr.
 *
 * @file
 */

use Lockr\Exception\LockrApiException;
use Lockr\Lockr;
use Lockr\LockrClient;
use Lockr\LockrSettings;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Encoding;
use Defuse\Crypto\Exception as Ex;

/**
 * Include our autoloader.
 */
require_once LOCKR__PLUGIN_DIR . '/vendor/autoload.php';

/**
 * Include our partners.
 */
require_once LOCKR__PLUGIN_DIR . '/lockr-partners.php';

/**
 * Include our overrides.
 */
require_once LOCKR__PLUGIN_DIR . '/lockr-overrides.php';

/**
 * Include our post encryption filters.
 */
require_once LOCKR__PLUGIN_DIR . '/lockr-secure-posts.php';

/**
 * Include our secret info parser.
 */
require_once LOCKR__PLUGIN_DIR . '/class-lockr-wp-secret-info.php';

/**
 * Include our WP CLI Commands if available.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once dirname( __FILE__ ) . '/lockr-command.php';
}

/**
 * Include our admin functions.
 */
if ( is_admin() ) {
	require_once LOCKR__PLUGIN_DIR . '/lockr-admin.php';
}

/**
 * Set our db version which will be updated should the schema change.
 */
global $lockr_db_version;
$lockr_db_version = '1.2';

/**
 * Initial setup when the plugin is activated.
 */
function lockr_install() {
	global $wpdb;
	global $lockr_db_version;
	$current_lockr_db_version = get_option( 'lockr_db_version' );

	if ( $current_lockr_db_version !== $lockr_db_version ) {
		$table_name      = $wpdb->prefix . 'lockr_keys';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT null AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT null,
			key_name tinytext NOT null,
			key_value text NOT null,
			key_label text NOT null,
			key_abstract text,
			dev_abstract text,
			auto_created tinyint(1),
			option_override text,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		if ( ! $current_lockr_db_version ) {
			update_option( 'lockr_prod_abstract_migrated', true );
			update_option( 'lockr_dev_abstract_migrated', true );
		}
		update_option( 'lockr_db_version', $lockr_db_version );
	}

	$partner = lockr_get_partner();

	if ( $partner ) {
		add_option( 'lockr_partner', $partner['name'] );
	}

}

/**
 * Create Lockr certificate files.
 *
 * @param string $dir The directory to save the files to.
 * @param array  $texts The text of the certificate files.
 */
function lockr_write_cert_pair( $dir, $texts ) {
	@mkdir( $dir, 0700, true );

	$ht_file = fopen( "{$dir}/.htaccess", 'w' );
	fwrite( $ht_file, "Order deny,allow\nDeny from all\n" );
	fclose( $ht_file );

	$key_file = "{$dir}/key.pem";
	$key_fd   = fopen( $key_file, 'w' );
	fwrite( $key_fd, $texts['key_text'] );
	fclose( $key_fd );
	chmod( $key_file, 0600 );

	$cert_file = "{$dir}/crt.pem";
	$cert_fd   = fopen( $cert_file, 'w' );
	fwrite( $cert_fd, $texts['cert_text'] );
	fclose( $cert_fd );
	chmod( $cert_file, 0600 );

	$pair_file = "{$dir}/pair.pem";
	$pair_fd   = fopen( $pair_file, 'w' );
	fwrite( $pair_fd, $texts['key_text'] );
	fwrite( $pair_fd, $texts['cert_text'] );
	fclose( $pair_fd );
	chmod( $pair_file, 0600 );

	return file_exists( $pair_file );
}

/**
 * Check if any DB updates are needed, and if so run install over again.
 */
function lockr_update_db_check() {
	global $lockr_db_version;
	if ( get_option( 'lockr_db_version' ) !== $lockr_db_version ) {
		lockr_install();
	}
}
add_action( 'plugins_loaded', 'lockr_update_db_check' );

/**
 * Returns the Lockr client for this site.
 *
 * @param bool $force If the cached client (if exists) should be recreated.
 */
function lockr_client( $force = false ) {
	static $client;
	if ( ! $client || $force ) {
		$settings = lockr_settings();
		$client   = LockrClient::createFromSettings( $settings );
	}
	$secret_info = new Lockr_WP_Secret_Info();
	return new Lockr( $client, $secret_info );
}

/**
 * Returns the Lockr settings for this site.
 */
function lockr_settings() {

	if ( get_option( 'lockr_cert', false ) ) {
		$cert_path = get_option( 'lockr_cert', null );
	} else {
		$partner = lockr_get_partner();
		if ( ! $partner ) {
			// User is not on any detected partner or custom certificate location.
			$dirname   = ABSPATH . '.lockr';
			$cert_path = null;

			if ( file_exists( $dirname . '/prod/pair.pem' ) ) {
				$cert_path = $dirname . '/prod/pair.pem';
			} elseif ( file_exists( $dirname . '/dev/pair.pem' ) ) {
				$cert_path = $dirname . '/dev/pair.pem';
			} else {
				$cert_path = null;
			}
		} else {
			$cert_path = isset( $partner['cert'] ) ? $partner['cert'] : null;
		}
	}
	return new LockrSettings( $cert_path );
}

/**
 * Returns if this site is currently registered with Lockr.
 *
 * @return array An array of the site status.
 */
function lockr_check_registration() {

	static $status;

	if ( $status ) {
		return $status;
	}
	$status = array(
		'valid_cert'    => false,
		'environment'   => false,
		'client_label'  => null,
		'keyring_label' => null,
		'has_cc'        => false,
		'trial_end'     => null,
		'partner'       => array(),
	);

	$partner           = lockr_get_partner();
	$status['partner'] = $partner;

	$client = lockr_client();

	try {
			$client_info = $client->getInfo();

			$status['valid_cert']    = true;
			$status['environment']   = $client_info['env'];
			$status['client_label']  = $client_info['label'];
			$status['keyring_label'] = $client_info['keyring']['label'];
			$status['keyring_id']    = $client_info['keyring']['id'];
			$status['has_cc']        = $client_info['keyring']['hasCreditCard'];
			$status['trial_end']     = $client_info['keyring']['trialEnd'];
	} catch ( \Exception $e ) {
		return $status;
	}

	return $status;
}

/**
 * Create Lockr client certs.
 *
 * @param string $client_token The client token passed back from accounts.lockr.io .
 * @param array  $dn The dn array for the CSR.
 * @param string $dirname The directory to put the certificates in.
 * @param array  $partner The partner information if it exists.
 * @param bool   $partner_certs If the partner already has certificates provisioned.
 *
 * @return bool If the certs were successfully created.
 */
function create_certs( $client_token, $dn = array(), $dirname = ABSPATH . '.lockr', $partner = array(), $partner_certs = false ) {

	if ( empty( $dn ) ) {
		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Washington',
			'localityName'        => 'Tacoma',
			'organizationName'    => 'Lockr',
		);
	}

	if ( ! $partner_certs ) {

		$client = lockr_client( true );

		try {
			$result = $client->createCertClient( $client_token, $dn );
		} catch ( \Exception $e ) {
			return false;
		}

		if ( ! empty( $result['cert_text'] ) ) {
			$env = $result['env'];
			return lockr_write_cert_pair( $dirname . '/' . $env, $result );
		}
	} else {
		$partner_name = $partner['name'];
		if ( 'pantheon' === $partner_name ) {
			$client = lockr_client( true );
			try {
				$result = $client->createPantheonClient( $client_token );
			} catch ( \Exception $e ) {
				return false;
			}
			return true;
		}
	}
}

/**
 * Encrypt plaintext using a key from Lockr.
 *
 * @param string $plaintext The plaintext to be encrypted.
 * @param string $key_name The key name in Lockr.
 *
 * @return string|null
 *   The encrypted and encoded ciphertext or null if encryption fails.
 */
function lockr_encrypt( $plaintext, $key_name = 'lockr_default_key' ) {

	$key = lockr_get_key( $key_name );
	if ( ! $key ) {
		return null;
	}

	$key = base64_decode( $key );

	if ( version_compare( PHP_VERSION, '7.0.0' ) >= 0 ) {

		// Use the defuse library for openssl support.
		try {
			// Defuse PHP-Encryption requires a key object instead of a string.
			$key = Encoding::saveBytesToChecksummedAsciiSafeString( Key::KEY_CURRENT_VERSION, $key );
			$key = Key::loadFromAsciiSafeString( $key );

			$ciphertext = Crypto::encrypt( $plaintext, $key, true );

			// Check if we are disabling base64 encoding.
			$ciphertext = base64_encode( $ciphertext );

			$parts = array(
				'cipher'     => 'openssl',
				'key_name'   => $key_name,
				'ciphertext' => $ciphertext,
			);
		} catch ( Ex $ex ) {
			return null;
		}
	} else {
		$cipher = MCRYPT_RIJNDAEL_256;
		$mode   = MCRYPT_MODE_CBC;

		$iv_len = mcrypt_get_iv_size( $cipher, $mode );
		$iv     = mcrypt_create_iv( $iv_len );

		$ciphertext = mcrypt_encrypt( $cipher, $key, $plaintext, $mode, $iv );
		if ( false === $ciphertext ) {
			return null;
		}

		$iv = base64_encode( $iv );
		if ( false === $iv ) {
			return null;
		}

		$ciphertext = base64_encode( $ciphertext );
		if ( false === $ciphertext ) {
			return null;
		}

		$parts = array(
			'cipher'     => $cipher,
			'mode'       => $mode,
			'key_name'   => $key_name,
			'iv'         => $iv,
			'ciphertext' => $ciphertext,
		);
	}

	$encoded = wp_json_encode( $parts );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return null;
	}

	return $encoded;
}

/**
 * Decrypt ciphertext using a key from Lockr.
 *
 * @param string $encoded The encrypted and encoded ciphertext.
 *
 * @return string|null The plaintext or null if decryption fails.
 */
function lockr_decrypt( $encoded ) {
	$parts = json_decode( $encoded, true );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return null;
	}

	if ( ! isset( $parts['cipher'] ) ) {
		return null;
	}
	$cipher = $parts['cipher'];

	$key = lockr_get_key( $parts['key_name'] );
	if ( ! $key ) {
		return null;
	}
	$key = base64_decode( $key );

	if ( ! isset( $parts['ciphertext'] ) ) {
		return null;
	}
	$ciphertext = base64_decode( $parts['ciphertext'] );
	if ( false === $ciphertext ) {
		return null;
	}

	if ( MCRYPT_RIJNDAEL_256 === $cipher ) {
		if ( ! isset( $parts['mode'] ) ) {
			return null;
		}
		$mode = $parts['mode'];

		if ( ! isset( $parts['key_name'] ) ) {
			return null;
		}

		if ( ! isset( $parts['iv'] ) ) {
			return null;
		}
		$iv = base64_decode( $parts['iv'] );
		if ( false === $iv ) {
			return null;
		}
		if ( ! isset( $parts['ciphertext'] ) ) {
			return null;
		}

		$plaintext = mcrypt_decrypt( $cipher, $key, $ciphertext, $mode, $iv );
		if ( false === $plaintext ) {
			return null;
		}
	} else {
		try {
			// Use the defuse library for openssl support.
			$key = Encoding::saveBytesToChecksummedAsciiSafeString( Key::KEY_CURRENT_VERSION, $key );
			$key = Key::loadFromAsciiSafeString( $key );

			$plaintext = Crypto::decrypt( $ciphertext, $key, true );
		} catch ( Ex $ex ) {
			return null;
		}
	}

	return trim( $plaintext );
}

/**
 * Gets a key from Lockr.
 *
 * @param string $key_name The key name.
 * @return string | false
 * Returns the key value, or false on failure.
 */
function lockr_get_key( $key_name ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'lockr_keys';
	$key_store  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE key_name = %s", array( $key_name ) ) ); // WPCS: unprepared SQL OK.

	if ( null === $key_store ) {
		return false;
	}

	$client = lockr_client();

	try {
		if ( $client ) {
			return $client->getSecretValue( $key_name ) ?: false;
		} else {
			return false;
		}
	} catch ( \Exception $e ) {
		// if 404 do the following.
		if ( 404 === $e->getCode() ) {
			$auto_created = $key_store[0]->auto_created;
			if ( $auto_created ) {
				$status = lockr_check_registration();
				if ( isset( $status['environment'] ) ) {
					$key_value = base64_encode( $client->generateKey( 256 ) );
					$key_set   = lockr_set_key( $key_name, $key_value, $key_store[0]->key_label, $key_store[0]->option_override, true );
					return $key_value;
				}
			}
		} else {
			return false;
		}
	}
}

/**
 * Sets a key value in lockr.
 *
 * @param string      $key_name The key name.
 * @param string      $key_value The key value.
 * @param string      $key_label The key label.
 * @param string|bool $option_override The exisiting key metadata if it exists.
 * @param bool        $auto_created if the key was programatically created by Lockr.
 *
 * @return bool       true if the key set successfully, false if not.
 */
function lockr_set_key( $key_name, $key_value, $key_label, $option_override = null, $auto_created = false ) {

	$client      = lockr_client();
	$sovereignty = get_option( 'lockr_region', null );

	if ( false === $client ) {
		return false;
	}

	try {
		$key_remote = $client->createSecretValue( $key_name, $key_value, $key_label, $sovereignty ) ?: false;
	} catch ( \Exception $e ) {
		return false;
	}

	if ( false !== $key_remote ) {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'lockr_keys';
		$existing_key = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE key_name = %s", array( $key_name ) ) ); // WPCS: unprepared SQL OK.
		$key_id       = isset( $existing_key[0]->id ) ? array( 'id' => $existing_key[0]->id ) : null;
		if ( $key_id ) {
			$key_abstract = '**************' . substr( $key_value, -4 );
			// Setup our storage array.
			$key_data = array(
				'key_name'  => $key_name,
				'key_label' => $key_label,
			);

			if ( null !== $option_override && $option_override !== $existing_key[0]->option_override ) {
				$key_data['option_override'] = $option_override;
			}

			if ( ! $existing_key[0]->auto_created && $auto_created !== $existing_key[0]->auto_created ) {
				$key_data['auto_created'] = $auto_created;
			}

			$status = lockr_check_registration();

			if ( isset( $status['environment'] ) && 'prod' !== $status['environment'] ) {
				$key_data['dev_abstract'] = $key_abstract;
			} else {
				$key_data['key_abstract'] = $key_abstract;
			}

			$key_store = $wpdb->update( $table_name, $key_data, $key_id );
			return $key_store;
		}
	}

	return false;
}

/**
 * Deletes a key from Lockr.
 *
 * @param string $key_name The key name.
 */
function lockr_delete_key( $key_name ) {

	$key_value = lockr_get_key( $key_name );

	$client = lockr_client();
	if ( $client ) {

		try {
			$client->deleteSecretValue( $key_name );
		} catch ( \Exception $e ) {
			return false;
		}

		global $wpdb;
		global $lockr_all_keys;
		$table_name = $wpdb->prefix . 'lockr_keys';

		if ( isset( $lockr_all_keys[ $key_name ] ) ) {
			$key = $lockr_all_keys[ $key_name ];
			// Set the value back into the option value.
			$new_option_array = explode( ':', $key->option_override );
			$option_name      = array_shift( $new_option_array );
			$existing_option  = get_option( $option_name );

			if ( $existing_option ) {
				if ( is_array( $existing_option ) ) {

					$serialized_data_ref = &$existing_option;
					foreach ( $new_option_array as $option_key ) {
						$serialized_data_ref = &$serialized_data_ref[ $option_key ];
					}
					$serialized_data_ref = $key_value;
					unset( $lockr_all_keys[ $key_name ] );
					update_option( $option_name, $existing_option );
				} else {
					unset( $lockr_all_keys[ $key_name ] );
					update_option( $option_name, $key_value );
				}
			} else {
				unset( $lockr_all_keys[ $key_name ] );
				update_option( $key->option_override, $key_value );
			}
		}

		$key_store  = array( 'key_name' => $key_name );
		$key_delete = $wpdb->delete( $table_name, $key_store );
		if ( ! empty( $key_delete ) ) {
			return true;
		}
	}
}

/**
 * Migrate the abstracts into their correct environment display.
 *
 * @param string $environment What environment the site is in.
 */
function lockr_update_abstracts( $environment ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'lockr_keys';
	$query      = "SELECT * FROM $table_name";
	$keys       = $wpdb->get_results( $query ); // WPCS: unprepared SQL OK.

	foreach ( $keys as $key ) {
		$key_value = lockr_get_key( $key->key_name );

		if ( $key_value ) {
			$key_abstract = '**************' . substr( $key_value, -4 );
			$key_id       = array( 'id' => $key->id );

			if ( 'prod' !== $environment ) {
				$key_data = array( 'dev_abstract' => $key_abstract );
			} else {
				$key_data = array( 'key_abstract' => $key_abstract );
			}

			$key_store = $wpdb->update( $table_name, $key_data, $key_id );
		}
	}
	update_option( 'lockr_' . $environment . '_abstract_migrated', true );
}

/**
 * Performs a generic option-override.
 *
 * @param string $option_name The name of the option to override.
 * @param string $key_name The key name in Lockr to override with.
 * @param string $key_desc The description of the key to be stored.
 */
function lockr_override_option( $option_name, $key_name, $key_desc ) {
	$option_value = get_option( $option_name );

	if ( '' === $option_value || substr( $option_value, 0, 5 ) === 'lockr' ) {
		return;
	}

	if ( lockr_set_key( $key_name, $option_value, $key_desc ) ) {
		update_option( $option_name, $key_name );
	}
}

/**
 * Gets a possibly overridden option value.
 *
 * @param string $option_name The name of the overridden option.
 */
function lockr_get_override_value( $option_name ) {
	$option_value = get_option( $option_name );

	if ( substr( $option_value, 0, 5 ) !== 'lockr' ) {
		return $option_value;
	}

	$lockr_key = lockr_get_key( $option_value );
	if ( $lockr_key ) {
		return $lockr_key;
	}

	return $option_value;
}
