<?php
namespace AffWP\Integrations\Opt_In;

use AffWP\Integrations\Opt_In;

/**
 * ActiveCampaign opt-in platform integration.
 *
 * @since 2.2
 * @abstract
 */
class ActiveCampaign extends Opt_In\Platform {

	/**
	 * Initialize our API keys and platform variables.
	 *
	 * @access public
	 * @since  2.2
	 * @return void
	 */
	public function init() {

		$this->json        = false;
		$this->platform_id = 'activecampaign';
		$this->api_key     = affiliate_wp()->settings->get( 'activecampaign_api_key' );
		$this->list_id     = affiliate_wp()->settings->get( 'activecampaign_list_id' );
		$this->api_url     = trailingslashit( affiliate_wp()->settings->get( 'activecampaign_api_url' ) ) . 'admin/api.php?api_action=contact_add&api_output=json&api_key=' . $this->api_key;
	}

	/**
	 * Subscribe a contact.
	 *
	 * @access public
	 * @since  2.2
	 * @return array|WP_Error
	 */
	public function subscribe_contact() {

		$exists = $this->already_subscribed();

		if( $exists ) {

			$this->add_error( 'already_subscribed', sprintf( __( '%s is already subscribed to this list.', 'affiliate-wp' ), $this->contact['email'] ) );
			return;

		}

		$body = array(
			'email'                          => $this->contact['email'],
			'first_name'                     => $this->contact['first_name'],
			'last_name'                      => $this->contact['last_name'],
			'ip4'                            => affiliate_wp()->tracking->get_ip(),
			'p[' . $this->list_id . ']'      => $this->list_id,
			'status[' . $this->list_id . ']' => 1,
		);

		$response = $this->call_api( $this->api_url, $body );
		$response = json_decode( $response['body'] );

		if( empty( $response->result_code ) ) {
			$this->add_error( 'affwp_active_campaign_error', $response->result_message );
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
		$url  = $this->api_url . '&email=' . $this->contact['email'];
		$args = array(
			'timeout'     => 45,
			'sslverify'   => false,
			'httpversion' => '1.1',
		);

		$request = wp_remote_get( $url, $args );

		if( is_wp_error( $request ) ) {

			$this->add_error( $request->get_error_code(), $request->get_error_message() );

		}

		$response = json_decode( $request['body'] );

		if( ! empty( $response->result_code ) ) {

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

		$settings['activecampaign_api_url'] = array(
			'name' => __( 'ActiveCampaign API URL', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter your ActiveCampaign API URL.', 'affiliate-wp' ),
		);

		$settings['activecampaign_api_key'] = array(
			'name' => __( 'ActiveCampaign API Key', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter your ActiveCampaign API key.', 'affiliate-wp' ),
		);

		$settings['activecampaign_list_id'] = array(
			'name' => __( 'ActiveCampaign List ID', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter the ID of the list you wish to subscribe contacts to.', 'affiliate-wp' ),
		);

		return $settings;
	}

}