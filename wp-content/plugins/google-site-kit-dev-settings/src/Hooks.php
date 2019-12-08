<?php
/**
 * Class Google\Site_Kit_Dev_Settings\Hooks
 *
 * @package   Google\Site_Kit_Dev_Settings
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://sitekit.withgoogle.com
 */

namespace Google\Site_Kit_Dev_Settings;

/**
 * Class controlling the hooks to adjust Site Kit functionality.
 *
 * @since 0.1.0
 */
class Hooks {

	/**
	 * Setting instance.
	 *
	 * @since 0.1.0
	 * @var Setting
	 */
	protected $setting;

	/**
	 * Filter values, for internal storage to reduce regeneration overhead.
	 *
	 * @since 0.1.0
	 * @var array
	 */
	private $filter_values = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Setting $setting Setting instance.
	 */
	public function __construct( Setting $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Registers the setting with WordPress.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		$filter_callbacks = array(
			'googlesitekit_site_url'     => function() {
				$option = $this->setting->get();
				if ( empty( $option['site_url'] ) ) {
					return '';
				}

				return esc_url( $option['site_url'] );
			},
			'googlesitekit_oauth_secret' => function() {
				$option = $this->setting->get();
				if ( empty( $option['oauth2_client_id'] ) || empty( $option['oauth2_client_secret'] ) ) {
					return '';
				}

				$redirect_uri = untrailingslashit( home_url( '', 'https' ) ) . '?oauth2callback=1';

				return '{"web":{"client_id":"' . $option['oauth2_client_id'] . '","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://oauth2.googleapis.com/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_secret":"' . $option['oauth2_client_secret'] . '","redirect_uris":["' . $redirect_uri . '"]}}';
			},
		);

		foreach ( $filter_callbacks as $filter_name => $filter_callback ) {
			add_filter(
				$filter_name,
				function( $value ) use ( $filter_name, $filter_callback ) {
					if ( ! isset( $this->filter_values[ $filter_name ] ) ) {
						$this->filter_values[ $filter_name ] = call_user_func( $filter_callback );
					}

					if ( ! empty( $this->filter_values[ $filter_name ] ) ) {
						return $this->filter_values[ $filter_name ];
					}

					return $value;
				},
				1
			);
		}
	}
}
