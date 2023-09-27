<?php

namespace WPForms\Admin\Settings;

/**
 * CAPTCHA setting page.
 *
 * @since 1.6.4
 */
class Captcha {

	/**
	 * Slug identifier for admin page view.
	 *
	 * @since 1.6.4
	 *
	 * @var string
	 */
	const VIEW = 'captcha';

	/**
	 * The hCaptcha javascript URL-resource.
	 *
	 * @since 1.6.4
	 */
	const HCAPTCHA_API_URL = 'https://hcaptcha.com/1/api.js';

	/**
	 * The reCAPTCHA javascript URL-resource.
	 *
	 * @since 1.6.4
	 */
	const RECAPTCHA_API_URL = 'https://www.google.com/recaptcha/api.js';

	/**
	 * Saved CAPTCHA settings.
	 *
	 * @since 1.6.4
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Initialize class.
	 *
	 * @since 1.6.4
	 */
	public function init() {

		// Only load if we are actually on the settings page.
		if ( ! wpforms_is_admin_page( 'settings' ) ) {
			return;
		}

		// Listen the previous reCAPTCHA page and safely redirect from it.
		if ( wpforms_is_admin_page( 'settings', 'recaptcha' ) ) {
			wp_safe_redirect( add_query_arg( 'view', self::VIEW, admin_url( 'admin.php?page=wpforms-settings' ) ) );
			exit;
		}

		$this->init_settings();
		$this->hooks();
	}

	/**
	 * Init CAPTCHA settings.
	 *
	 * @since 1.6.4
	 */
	public function init_settings() {

		$this->settings = wp_parse_args( wpforms_get_captcha_settings(), [ 'provider' => 'none' ] );
	}

	/**
	 * Hooks.
	 *
	 * @since 1.6.4
	 */
	public function hooks() {

		add_filter( 'wpforms_settings_tabs', [ $this, 'register_settings_tabs' ], 5, 1 );
		add_filter( 'wpforms_settings_defaults', [ $this, 'register_settings_fields' ], 5, 1 );
		add_action( 'wpforms_settings_updated', [ $this, 'updated' ] );
		add_action( 'wpforms_settings_enqueue', [ $this, 'enqueues' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'apply_noconflict' ], 9999 );
	}

	/**
	 * Register CAPTCHA settings tab.
	 *
	 * @since 1.6.4
	 *
	 * @param array $tabs Admin area tabs list.
	 *
	 * @return array
	 */
	public function register_settings_tabs( $tabs ) {

		$captcha = [
			self::VIEW => [
				'name'   => esc_html__( 'CAPTCHA', 'wpforms-lite' ),
				'form'   => true,
				'submit' => esc_html__( 'Save Settings', 'wpforms-lite' ),
			],
		];

		return wpforms_array_insert( $tabs, $captcha, 'email' );
	}

	/**
	 * Register CAPTCHA settings fields.
	 *
	 * @since 1.6.4
	 *
	 * @param array $settings Admin area settings list.
	 *
	 * @return array
	 */
	public function register_settings_fields( $settings ) {

		$settings[ self::VIEW ] = [
			self::VIEW . '-heading'  => [
				'id'       => self::VIEW . '-heading',
				'content'  => '<h4>' . esc_html__( 'CAPTCHA', 'wpforms-lite' ) . '</h4><p>' . esc_html__( 'A CAPTCHA is an anti-spam technique which helps to protect your website from spam and abuse while letting real people pass through with ease. WPForms supports two popular services.', 'wpforms-lite' ) . '</p>',
				'type'     => 'content',
				'no_label' => true,
				'class'    => [ 'wpforms-setting-captcha-heading', 'section-heading' ],
			],
			self::VIEW . '-provider' => [
				'id'      => self::VIEW . '-provider',
				'type'    => 'radio',
				'default' => 'none',
				'options' => [
					'hcaptcha'  => esc_html__( 'hCaptcha', 'wpforms-lite' ),
					'recaptcha' => esc_html__( 'reCAPTCHA', 'wpforms-lite' ),
					'none'      => esc_html__( 'None', 'wpforms-lite' ),
				],
				'desc'    => wp_kses(
					/* translators: %s - WPForms.com CAPTCHA comparison page URL. */
					__( 'Not sure which service is right for you? <a href="https://wpforms.com/docs/setup-captcha-wpforms/" target="_blank" rel="noopener noreferrer">Check out our comparison</a> for more details.', 'wpforms-lite' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
			],
			'recaptcha-heading'      => [
				'id'       => 'recaptcha-heading',
				'content'  => $this->get_recaptcha_field_desc(),
				'type'     => 'content',
				'no_label' => true,
				'class'    => [ 'wpforms-setting-recaptcha', 'section-heading' ],
			],
			'hcaptcha-heading'       => [
				'id'       => 'hcaptcha-heading',
				'content'  => $this->get_hcaptcha_field_desc(),
				'type'     => 'content',
				'no_label' => true,
				'class'    => [ 'section-heading' ],
			],
			'recaptcha-type'         => [
				'id'      => 'recaptcha-type',
				'name'    => esc_html__( 'Type', 'wpforms-lite' ),
				'type'    => 'radio',
				'default' => 'v2',
				'options' => [
					'v2'        => esc_html__( 'Checkbox reCAPTCHA v2', 'wpforms-lite' ),
					'invisible' => esc_html__( 'Invisible reCAPTCHA v2', 'wpforms-lite' ),
					'v3'        => esc_html__( 'reCAPTCHA v3', 'wpforms-lite' ),
				],
				'class'   => [ 'wpforms-setting-recaptcha' ],
			],
			'recaptcha-site-key'     => [
				'id'   => 'recaptcha-site-key',
				'name' => esc_html__( 'Site Key', 'wpforms-lite' ),
				'type' => 'text',
			],
			'hcaptcha-site-key'      => [
				'id'   => 'hcaptcha-site-key',
				'name' => esc_html__( 'Site Key', 'wpforms-lite' ),
				'type' => 'text',
			],
			'recaptcha-secret-key'   => [
				'id'   => 'recaptcha-secret-key',
				'name' => esc_html__( 'Secret Key', 'wpforms-lite' ),
				'type' => 'text',
			],
			'hcaptcha-secret-key'    => [
				'id'   => 'hcaptcha-secret-key',
				'name' => esc_html__( 'Secret Key', 'wpforms-lite' ),
				'type' => 'text',
			],
			'recaptcha-fail-msg'     => [
				'id'      => 'recaptcha-fail-msg',
				'name'    => esc_html__( 'Fail Message', 'wpforms-lite' ),
				'desc'    => esc_html__( 'Displays to users who fail the verification process.', 'wpforms-lite' ),
				'type'    => 'text',
				'default' => esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'wpforms-lite' ),
			],
			'hcaptcha-fail-msg'      => [
				'id'      => 'hcaptcha-fail-msg',
				'name'    => esc_html__( 'Fail Message', 'wpforms-lite' ),
				'desc'    => esc_html__( 'Displays to users who fail the verification process.', 'wpforms-lite' ),
				'type'    => 'text',
				'default' => esc_html__( 'hCaptcha verification failed, please try again later.', 'wpforms-lite' ),
			],
			'recaptcha-v3-threshold' => [
				'id'      => 'recaptcha-v3-threshold',
				'name'    => esc_html__( 'Score Threshold', 'wpforms-lite' ),
				'desc'    => esc_html__( 'reCAPTCHA v3 returns a score (1.0 is very likely a good interaction, 0.0 is very likely a bot). If the score less than or equal to this threshold, the form submission will be blocked and the message above will be displayed.', 'wpforms-lite' ),
				'type'    => 'number',
				'attr'    => [
					'step' => '0.1',
					'min'  => '0.0',
					'max'  => '1.0',
				],
				'default' => esc_html__( '0.4', 'wpforms-lite' ),
				'class'   => 'recaptcha' === $this->settings['provider'] && 'v3' === $this->settings['recaptcha_type'] ? [ 'wpforms-setting-recaptcha' ] : [ 'wpforms-setting-recaptcha', 'wpforms-hidden' ],
			],
			'recaptcha-noconflict'   => [
				'id'   => 'recaptcha-noconflict',
				'name' => esc_html__( 'No-Conflict Mode', 'wpforms-lite' ),
				'desc' => esc_html__( 'Check this option to forcefully remove other CAPTCHA occurrences in order to prevent conflicts. Only enable this option if your site is having compatibility issues or instructed by support.', 'wpforms-lite' ),
				'type' => 'checkbox',
			],
			self::VIEW . '-preview'  => [
				'id'      => self::VIEW . '-preview',
				'name'    => esc_html__( 'Preview', 'wpforms-lite' ),
				'content' => '<p class="desc">' . esc_html__( 'Please save settings to generate a preview of your CAPTCHA here.', 'wpforms-lite' ) . '</p>',
				'type'    => 'content',
				'class'   => [ 'wpforms-hidden' ],
			],
		];

		if (
			'hcaptcha' === $this->settings['provider'] ||
			( 'recaptcha' === $this->settings['provider'] && 'v2' === $this->settings['recaptcha_type'] )
		) {
			$data = apply_filters( 'wpforms_admin_pages_settings_captcha_data', [ 'sitekey' => $this->settings['site_key'] ] );

			// Prepare HTML for CAPTCHA preview.
			$placeholder_descr = $settings[ self::VIEW ][ self::VIEW . '-preview' ]['content'];
			$captcha_descr     = esc_html__( 'This CAPTCHA is generated using your site and secret keys. If an error is displayed, please double-check your keys.', 'wpforms-lite' );
			$captcha_preview   = sprintf( '<div class="wpforms-captcha-container" style="pointer-events:none!important;cursor:default!important;"><div %s></div><input type="text" name="wpforms-captcha-hidden" class="wpforms-recaptcha-hidden" style="position:absolute!important;clip:rect(0,0,0,0)!important;height:1px!important;width:1px!important;border:0!important;overflow:hidden!important;padding:0!important;margin:0!important;"></div>', wpforms_html_attributes( '', [ 'wpforms-captcha' ], $data ) );

			$settings[ self::VIEW ][ self::VIEW . '-preview' ]['content'] = sprintf( '<div class="wpforms-captcha-preview">%1$s <p class="desc">%2$s</p></div><div class="wpforms-captcha-placeholder wpforms-hidden">%3$s</div>', $captcha_preview, $captcha_descr, $placeholder_descr );
			$settings[ self::VIEW ][ self::VIEW . '-preview' ]['class']   = [];
		}

		return $settings;
	}

	/**
	 * Re-init CAPTCHA settings when plugin settings were updated.
	 *
	 * @since 1.6.4
	 */
	public function updated() {

		$this->init_settings();
		$this->notice();
	}

	/**
	 * Display notice about the CAPTCHA preview.
	 *
	 * @since 1.6.4
	 */
	protected function notice() {

		if (
			! wpforms_is_admin_page( 'settings', self::VIEW ) ||
			! $this->is_captcha_preview_ready()
		) {
			return;
		}

		\WPForms\Admin\Notice::info( esc_html__( 'A preview of your CAPTCHA is displayed below. Please view to verify the CAPTCHA settings are correct.', 'wpforms-lite' ) );
	}

	/**
	 * Enqueue assets for the CAPTCHA settings page.
	 *
	 * @since 1.6.4
	 */
	public function enqueues() {

		if (
			! $this->is_captcha_preview_ready() ||
			(bool) apply_filters( 'wpforms_admin_settings_captcha_enqueues_disable', false )
		) {
			return;
		}

		$api_url = $this->get_api_url();
		$api_var = 'hcaptcha' === $this->settings['provider'] ? 'hcaptcha' : 'grecaptcha';

		wp_enqueue_script( "wpforms-settings-{$this->settings['provider']}", $api_url, [ 'jquery' ], null, true );
		wp_add_inline_script( "wpforms-settings-{$this->settings['provider']}", "var wpformsSettingsCaptchaLoad = function(){jQuery('.wpforms-captcha').each(function(index, el){var widgetID = {$api_var}.render(el);jQuery(el).attr('data-captcha-id', widgetID);});jQuery(document).trigger('wpformsSettingsCaptchaLoaded');};" );
	}

	/**
	 * Use the CAPTCHA no-conflict mode.
	 *
	 * When enabled in the WPForms settings, forcefully remove all other
	 * CAPTCHA enqueues to prevent conflicts. Filter can be used to target
	 * specific pages, etc.
	 *
	 * @since 1.6.4
	 */
	public function apply_noconflict() {

		if (
			! wpforms_is_admin_page( 'settings', self::VIEW ) ||
			empty( wpforms_setting( 'recaptcha-noconflict' ) ) ||
			! apply_filters( 'wpforms_admin_settings_captcha_apply_noconflict', true )
		) {
			return;
		}

		$scripts = wp_scripts();
		$urls    = [ 'google.com/recaptcha', 'gstatic.com/recaptcha', 'hcaptcha.com/1' ];

		foreach ( $scripts->queue as $handle ) {

			// Skip the WPForms JavaScript assets.
			if (
				! isset( $scripts->registered[ $handle ] ) ||
				false !== strpos( $scripts->registered[ $handle ]->handle, 'wpforms' )
			) {
				return;
			}

			foreach ( $urls as $url ) {
				if ( false !== strpos( $scripts->registered[ $handle ]->src, $url ) ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
					break;
				}
			}
		}
	}

	/**
	 * Check if CAPTCHA config is ready to display a preview.
	 *
	 * @since 1.6.4
	 *
	 * @return bool
	 */
	protected function is_captcha_preview_ready() {

		return (
			( 'hcaptcha' === $this->settings['provider'] || ( 'recaptcha' === $this->settings['provider'] && 'v2' === $this->settings['recaptcha_type'] ) ) &&
			! empty( $this->settings['site_key'] ) &&
			! empty( $this->settings['secret_key'] )
		);
	}

	/**
	 * Retrieve the CAPTCHA provider API URL.
	 *
	 * @since 1.6.4
	 *
	 * @return string
	 */
	protected function get_api_url() {

		$api_url = '';

		if ( 'hcaptcha' === $this->settings['provider'] ) {
			$api_url = self::HCAPTCHA_API_URL;
		}

		if ( 'recaptcha' === $this->settings['provider'] ) {
			$api_url = self::RECAPTCHA_API_URL;
		}

		if ( ! empty( $api_url ) ) {
			$api_url = add_query_arg( $this->get_api_url_query_arg(), $api_url );
		}

		return apply_filters( 'wpforms_admin_settings_captcha_get_api_url', $api_url, $this->settings );
	}

	/**
	 * Retrieve query arguments for the CAPTCHA API URL.
	 *
	 * @since 1.6.4
	 *
	 * @return array
	 */
	protected function get_api_url_query_arg() {

		return (array) apply_filters(
			'wpforms_admin_settings_captcha_get_api_url_query_arg',
			[
				'onload' => 'wpformsSettingsCaptchaLoad',
				'render' => 'explicit',
			],
			$this->settings
		);
	}

	/**
	 * Some heading descriptions, like for hCaptcha, are long so we define them separately.
	 *
	 * @since 1.6.4
	 *
	 * @return string
	 */
	private function get_hcaptcha_field_desc() {

		return wpforms_render( 'admin/settings/hcaptcha-description' );
	}

	/**
	 * Some heading descriptions, like for reCAPTCHA, are long so we define them separately.
	 *
	 * @since 1.6.4
	 *
	 * @return string
	 */
	private function get_recaptcha_field_desc() {

		return wpforms_render( 'admin/settings/recaptcha-description' );
	}
}
