<?php
namespace AffWP\Integrations\Opt_In;

use AffWP\Integrations\Opt_In;

/**
 * ConvertKit opt-in platform integration.
 *
 * @since 2.2
 * @abstract
 */
class ConvertKit extends Opt_In\Platform {

	/**
	 * Initialize our API keys and platform variables.
	 *
	 * @access public
	 * @since  2.2
	 * @return void
	 */
	public function init() {

		$this->platform_id = 'convertkit';
		$this->api_key     = affiliate_wp()->settings->get( 'convertkit_api_key' );
		$this->list_id     = affiliate_wp()->settings->get( 'convertkit_list_id' );
		$this->api_url     = 'https://api.convertkit.com/v3/forms/' . $this->list_id . '/subscribe?api_key=' . $this->api_key;
		$this->json        = false;
	}

	/**
	 * Subscribe a contact.
	 *
	 * @access public
	 * @since  2.2
	 * @return array|WP_Error
	 */
	public function subscribe_contact() {

		$body = array(
			'email'         => $this->contact['email'],
			'first_name'    => $this->contact['first_name'],
			'fields'        => array(
		    	'last_name' => $this->contact['last_name']
			)
		);

		return $this->call_api( $this->api_url, $body );

	}

	/**
	 * Register our platform settings.
	 *
	 * @access public
	 * @since  2.2
	 * @return array
	 */
	public function settings( $settings ) {

		$settings['convertkit_api_key'] = array(
			'name' => __( 'ConvertKit API Key', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter your ConvertKit API key.', 'affiliate-wp' ),
		);

		$settings['convertkit_list_id'] = array(
			'name' => __( 'ConvertKit Form ID', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter the ID of the form you wish to subscribe contacts to.', 'affiliate-wp' ),
		);

		return $settings;
	}

}