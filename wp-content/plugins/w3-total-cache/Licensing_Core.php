<?php
namespace W3TC;



class Licensing_Core {
	/**
	 * Activates a license
	 */
	static public function activate_license( $license, $version ) {
		$state = Dispatcher::config_state_master();

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' => $license,   // legacy
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( W3TC_PURCHASE_PRODUCT_NAME ), // the name of our product in EDD
			'plugin_install_date' => gmdate( 'Y-m-d\\TH:i:s\\Z', $state->get_integer( 'common.install' ) ),
			'r' => rand(),
			'version' => $version
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, W3TC_LICENSE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		return $license_data;
	}

	/**
	 * Deactivates a license
	 */
	static public function deactivate_license( $license ) {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' => $license,   // legacy
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( W3TC_PURCHASE_PRODUCT_NAME ), // the name of our product in EDD,
			'r' => rand()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, W3TC_LICENSE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		return $license_data->license == 'deactivated';
	}

	/**
	 * Checks if a license key is still valid
	 */
	static public function check_license( $license, $version ) {
		global $wp_version;

		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,   // legacy
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( W3TC_PURCHASE_PRODUCT_NAME ),
			'r' => rand(),
			'version' => $version
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, W3TC_LICENSE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		return $license_data;
	}

	static public function reset_rooturi( $license, $version ) {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'reset_rooturi',
			'license_key' => $license,
			'home_url' => network_home_url(),
			'item_name' => urlencode( W3TC_PURCHASE_PRODUCT_NAME ), // the name of our product in EDD
			'r' => rand(),
			'version' => $version
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, W3TC_LICENSE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$status = json_decode( wp_remote_retrieve_body( $response ) );
		return $status;
	}

	static public function terms_accept() {
		$c = Dispatcher::config();
		if ( !Util_Environment::is_w3tc_pro( $c ) ) {
			$state_master = Dispatcher::config_state_master();
			$state_master->set( 'license.community_terms', 'accept' );
			$state_master->save();

			$c->set( 'common.track_usage', true );
			$c->save();
		} else {
			// not called in this mode
		}
	}
}
