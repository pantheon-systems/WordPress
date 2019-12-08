<?php
/**
 * Class Google\Site_Kit_Dev_Settings\Setting
 *
 * @package   Google\Site_Kit_Dev_Settings
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://sitekit.withgoogle.com
 */

namespace Google\Site_Kit_Dev_Settings;

/**
 * Class representing the setting for Site Kit developer settings.
 *
 * @since 0.1.0
 */
class Setting {

	/**
	 * The setting slug.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const OPTION_NAME = 'googlesitekitdev_settings';

	/**
	 * Registers the setting with WordPress.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action(
			'init',
			function() {
				register_setting(
					Admin\Settings_Screen::SLUG,
					self::OPTION_NAME,
					array(
						'type'              => 'object',
						'description'       => __( 'Developer settings for Site Kit.', 'google-site-kit-dev-settings' ),
						'sanitize_callback' => $this->get_sanitize_callback(),
						'default'           => $this->get_default(),
					)
				);
			}
		);

		// Migrate the previous version of the option on the fly.
		add_action(
			'admin_init',
			function() {
				$option = get_option( 'googlesitekit_dev_settings' );
				if ( is_array( $option ) && update_option( self::OPTION_NAME, $option ) ) {
					delete_option( 'googlesitekit_dev_settings' );
				}
			}
		);
	}

	/**
	 * Gets the features list from the option.
	 *
	 * @since 0.1.0
	 *
	 * @return array Associative array of $policy_name => $policy_origins pairs.
	 */
	public function get() {
		return array_filter( (array) get_option( self::OPTION_NAME ) );
	}

	/**
	 * Gets sub settings that the setting should contain.
	 *
	 * @since 0.1.0
	 *
	 * @return array List of associative setting definition arrays.
	 */
	public function get_sub_settings() {
		return array(
			array(
				'id'          => 'site_url',
				'title'       => __( 'Custom Site URL', 'google-site-kit-dev-settings' ),
				'description' => __( 'This will override your actual site URL, causing Site Kit to request and display insights for the site URL you enter here.', 'google-site-kit-dev-settings' ),
				'section'     => 'general',
				'class'       => 'regular-text code',
			),
			array(
				'id'          => 'oauth2_client_id',
				'title'       => __( 'Google OAuth2 Client ID', 'google-site-kit-dev-settings' ),
				'section'     => 'authentication',
				'class'       => 'regular-text code',
			),
			array(
				'id'          => 'oauth2_client_secret',
				'title'       => __( 'Google OAuth2 Client Secret', 'google-site-kit-dev-settings' ),
				'section'     => 'authentication',
				'class'       => 'regular-text code',
			),
		);
	}

	/**
	 * Gets the sanitize callback for the setting.
	 *
	 * @since 0.1.0
	 *
	 * @return callable Sanitize callback.
	 */
	protected function get_sanitize_callback() {
		return function( $value ) {
			$sub_settings = $this->get_sub_settings();

			if ( ! is_array( $value ) ) {
				$value = array();
			}

			foreach ( $sub_settings as $sub_setting ) {
				if ( ! isset( $value[ $sub_setting['id'] ] ) ) {
					$value[ $sub_setting['id'] ] = '';
					continue;
				}

				$value[ $sub_setting['id'] ] = trim( $value[ $sub_setting['id'] ] );
			}

			return $value;
		};
	}

	/**
	 * Gets the default value for the setting.
	 *
	 * @since 0.1.0
	 *
	 * @return array Default value.
	 */
	protected function get_default() {
		$sub_settings = $this->get_sub_settings();

		$value = array();
		foreach ( $sub_settings as $sub_setting ) {
			$value[ $sub_setting['id'] ] = '';
		}

		return $value;
	}
}
