<?php
namespace AffWP\Integrations\Opt_In;

use AffWP\Integrations\Opt_In;

/**
 * MailChimp opt-in platform integration.
 *
 * @since 2.2
 * @abstract
 */
class MailChimp extends Opt_In\Platform {

	/**
	 * Initialize our API keys and platform variables.
	 *
	 * @access public
	 * @since  2.2
	 * @return void
	 */
	public function init() {

		$this->platform_id = 'mailchimp';
		$this->api_key     = affiliate_wp()->settings->get( 'mailchimp_api_key' );
		$this->list_id     = affiliate_wp()->settings->get( 'mailchimp_list_id' );
		$data_center       = 'us4';

		if( ! empty( $this->api_key ) ) {
			$data_center   = substr( $this->api_key, strpos( $this->api_key, '-' ) + 1 );
		}

		$this->api_url     = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $this->list_id . '/members/';
	}

	/**
	 * Subscribe a contact.
	 *
	 * @access public
	 * @since  2.2
	 * @return array|WP_Error
	 */
	public function subscribe_contact() {

		$headers = array(
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key )
		);

		$exists = $this->already_subscribed();

		if( $exists ) {

			$this->add_error( 'already_subscribed', sprintf( __( '%s is already subscribed to this list.', 'affiliate-wp' ), $this->contact['email'] ) );
			return;

		}

		$body = array(
			'email_address' => $this->contact['email'],
			'status'        => affiliate_wp()->settings->get( 'mailchimp_double_opt_in' ) ? 'pending' : 'subscribed',
			'merge_fields'  => array(
				'FNAME'     => $this->contact['first_name'],
				'LNAME'     => $this->contact['last_name']
			)
		);

		$response = $this->call_api( $this->api_url, $body, $headers );

		if( ! empty ( $this->errors ) ) {

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			$this->errors = null;
			$this->add_error( wp_remote_retrieve_response_code( $response ), $body->detail );			

		}

		return $response;

	}

	/**
	 * Determine if an email is already subscribed
	 *
	 * @access public
	 * @since  2.2
	 * @return true|false
	 */
	public function already_subscribed() {

		$ret  = false;
		$hash = md5( strtolower( $this->contact['email'] ) );
		$url  = $this->api_url . $hash;

		$headers = array(
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key )
		);

		$args = array(
			'timeout'     => 45,
			'sslverify'   => false,
			'httpversion' => '1.1',
			'headers'     => $headers,
		);

		$request = wp_remote_get( $url, $args );

		if( is_wp_error( $request ) ) {

			$this->add_error( $request->get_error_code(), $request->get_error_message() );

		}

		if( 200 === wp_remote_retrieve_response_code( $request ) ) {

			$ret = true;

		}

		return $ret;
	}

	/**
	 * Register our platform settings.
	 *
	 * @access public
	 * @since  2.2
	 * @return array
	 */
	public function settings( $settings ) {

		$settings['mailchimp_api_key'] = array(
			'name' => __( 'MailChimp API Key', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter your MailChimp API key.', 'affiliate-wp' ),
		);

		$settings['mailchimp_list_id'] = array(
			'name' => __( 'MailChimp List ID', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter the ID of the list you wish to subscribe contacts to.', 'affiliate-wp' ),
		);

		$settings['mailchimp_double_opt_in'] = array(
			'name' => __( 'Double Opt-In', 'affiliate-wp' ),
			'type' => 'checkbox',
			'desc' => __( 'Should subscribers need to verify their subscription through a double opt-in email?', 'affiliate-wp' ),
		);

		return $settings;
	}

}