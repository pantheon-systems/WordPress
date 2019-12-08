<?php
/**
 * Setup Lockr Partner Automation.
 *
 * @package Lockr
 */

use Lockr\Exception\LockrException;
use Lockr\Exception\LockrClientException;
use Lockr\KeyClient;
use Lockr\Lockr;
use Lockr\NullPartner;
use Lockr\Partner;
use Lockr\SiteClient;

/**
 * Returns the detected partner, if available.
 */
function lockr_get_partner() {

	if ( defined( 'PANTHEON_BINDING' ) ) {
		$desc = <<<EOL
			The Pantheor is strong with this one.
			We're detecting you're on Pantheon and a friend of theirs is a friend of ours.
			Welcome to Lockr!
EOL;

		return array(
			'name'          => 'pantheon',
			'title'         => 'Pantheon',
			'description'   => $desc,
			'cert'          => '/srv/bindings/' . PANTHEON_BINDING . '/certs/binding.pem',
			'force_prod'    => false,
			'partner_certs' => true,
		);
	}

	if ( array_key_exists( 'KINSTA_CACHE_ZONE', $_SERVER ) ) {
		$desc = <<<EOL
			We're detecting you're on Kinsta and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;
		if ( defined( 'KINSTA_DEV_ENV' ) && KINSTA_DEV_ENV ) {
			$staging = true;
		}
		$dirname = ABSPATH . '.lockr';

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'California',
			'localityName'        => 'Los Angeles',
			'organizationName'    => 'Kinsta',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'Kinsta',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( defined( 'FLYWHEEL_CONFIG_DIR' ) ) {
		$desc = <<<EOL
			We're detecting you're on Flywheel and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;
		if ( defined( 'WP_CONTENT_URL' ) && false !== strpos( WP_CONTENT_URL, 'flywheelstaging' ) ) {
			$staging = true;
		}
		$dirname = '/www/.lockr';

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Nebraska',
			'localityName'        => 'Omaha',
			'organizationName'    => 'Flywheel',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'Flywheel',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( isset( $_SERVER['IS_WPE'] ) && true == $_SERVER['IS_WPE'] ) {
		$desc = <<<EOL
			We're detecting you're on WP Engine and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;
		$dirname = ABSPATH . '.lockr';

		if ( isset( $_SERVER['WPENGINE_ACCOUNT'] ) ) {
			$account_name = ' - ' . sanitize_text_field( wp_unslash( $_SERVER['WPENGINE_ACCOUNT'] ) );
		} else {
			$account_name = '';
		}

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Texas',
			'localityName'        => 'Austin',
			'organizationName'    => 'WP Engine' . $account_name,
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'WPEngine',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( defined( 'GD_VIP' ) ) {
		$desc = <<<EOL
			We're detecting you're on GoDaddy and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;
		if ( defined( 'GD_STAGING_SITE' ) && GD_STAGING_SITE ) {
			$staging = true;
		}
		$dirname = ABSPATH . '.lockr';

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Arizona',
			'localityName'        => 'Scottsdale',
			'organizationName'    => 'GoDaddy',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'GoDaddy',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( isset( $_SERVER['SERVER_ADMIN'] ) && false !== strpos( 'siteground', sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADMIN'] ) ) ) ) {
		$desc = <<<EOL
			We're detecting you're on Siteground and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;
		$dirname = ABSPATH . '.lockr';

		$dn = array(
			'countryName'         => 'BG',
			'stateOrProvinceName' => 'Sofia City',
			'localityName'        => 'Sofia',
			'organizationName'    => 'Siteground',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'Siteground',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( false !== strpos( gethostname(), 'bluehost' ) ) {
		$desc = <<<EOL
			We're detecting you're on Bluehost and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;

		if ( 'staging' === get_option( 'staging_environment' ) ) {
			$staging = true;
		}

		if ( $staging ) {
			$dirname = ABSPATH . '../../.lockr';
		} else {
			$dirname = ABSPATH . '.lockr';
		}

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Utah',
			'localityName'        => 'Provo',
			'organizationName'    => 'Bluehost',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'Bluehost',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( defined( 'LWMWP_SITE' ) ) {
		$desc = <<<EOL
			We're detecting you're on Liquid Web and a friend of theirs is a friend of ours.
			Welcome to Lockr! We have already setup your connection automatically.
EOL;

		$staging = false;
		if ( defined( 'LWMWP_STAGING_SITE' ) && LWMWP_STAGING_SITE ) {
			$staging = true;
		}

		$dirname = ABSPATH . '.lockr';

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Michigan',
			'localityName'        => 'Lansing',
			'organizationName'    => 'LiquidWeb',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'Liquid Web',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	if ( defined( 'IS_PRESSABLE' ) ) {
		$desc = <<<EOL
			We're detecting you're on Pressable and a friend of theirs is a friend of ours.
			Welcome to Lockr!
EOL;

		$staging = false;
		if ( defined( 'WPMU_PLUGIN_URL' ) && false !== strpos( WPMU_PLUGIN_URL, 'mystagingwebsite.com' ) ) {
			$staging = true;
		}

		$dirname = str_replace( 'wp-content', '.lockr', WP_CONTENT_DIR );

		$dn = array(
			'countryName'         => 'US',
			'stateOrProvinceName' => 'Texas',
			'localityName'        => 'San Antonio',
			'organizationName'    => 'Pressable',
		);

		if ( $staging || ! file_exists( $dirname . '/prod/pair.pem' ) ) {
			$cert = $dirname . '/dev/pair.pem';
		} else {
			$cert = $dirname . '/prod/pair.pem';
		}

		if ( ! file_exists( $cert ) ) {
			$cert = null;
		}

		return array(
			'name'          => 'custom',
			'title'         => 'Pressable',
			'description'   => $desc,
			'cert'          => $cert,
			'dn'            => $dn,
			'dirname'       => $dirname,
			'force_prod'    => true,
			'partner_certs' => false,
		);
	}

	return null;
}

/**
 * Setup the necessary partner registration certs.
 *
 * @param string $client_token The client token given by accounts.lockr.io for authorization.
 * @param string $client_prod_token The production client token given by accounts.lockr.io for authorization.
 * @param array  $partner The Partner array.
 * @param string $env The Envrionment to register.
 *
 * @return bool If the registration was successful.
 */
function lockr_partner_register( $client_token, $client_prod_token, $partner, $env = null ) {

	$dn = array(
		'countryName'         => 'US',
		'stateOrProvinceName' => 'Washington',
		'localityName'        => 'Tacoma',
		'organizationName'    => 'Lockr',
	);

	// Sanitize the $env for use below.
	if ( 'dev' !== $env && 'prod' !== $env && null !== $env ) {
		$env = null;
	}

	$dn            = ( isset( $partner['dn'] ) ) ? $partner['dn'] : $dn;
	$dirname       = ( isset( $partner['dirname'] ) ) ? $partner['dirname'] : ABSPATH . '.lockr';
	$force_prod    = ( isset( $partner['force_prod'] ) ) ? $partner['force_prod'] : false;
	$partner_certs = ( isset( $partner['partner_certs'] ) ) ? $partner['partner_certs'] : false;

	// Now that we have the information, let's create the certs.
	if ( $force_prod ) {
		$dev_cert = create_certs( $client_token, $dn, $dirname, $partner, $partner_certs );
		if ( $dev_cert ) {
			return create_certs( $client_prod_token, $dn, $dirname, $partner, $partner_certs );
		}
	} else {
		return create_certs( $client_token, $dn, $dirname, $partner, $partner_certs );
	}

}
