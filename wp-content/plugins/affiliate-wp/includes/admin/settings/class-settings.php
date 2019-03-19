<?php

class Affiliate_WP_Settings {

	private $options;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	*/
	public function __construct() {

		$this->options = get_option( 'affwp_settings', array() );

		// Set up.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_action( 'admin_init', array( $this, 'check_license' ) );

		// Global settings.
		add_action( 'affwp_pre_get_registered_settings', array( $this, 'handle_global_license_setting' ) );
		add_action( 'affwp_pre_get_registered_settings', array( $this, 'handle_global_debug_mode_setting' ) );

		// Sanitization.
		add_filter( 'affwp_settings_sanitize', array( $this, 'sanitize_referral_variable' ), 10, 2 );
		add_filter( 'affwp_settings_sanitize_text', array( $this, 'sanitize_text_fields' ), 10, 2 );
		add_filter( 'affwp_settings_sanitize_url', array( $this, 'sanitize_url_fields' ), 10, 2 );
		add_filter( 'affwp_settings_sanitize_checkbox', array( $this, 'sanitize_cb_fields' ), 10, 2 );
		add_filter( 'affwp_settings_sanitize_number', array( $this, 'sanitize_number_fields' ), 10, 2 );
		add_filter( 'affwp_settings_sanitize_rich_editor', array( $this, 'sanitize_rich_editor_fields' ), 10, 2 );

		// Capabilities
		add_filter( 'option_page_capability_affwp_settings', array( $this, 'option_page_capability' ) );

		// Filter the general settings
		add_filter( 'affwp_settings_general', array( $this, 'required_registration_fields' ) );

		// Filter the email settings
		add_filter( 'affwp_settings_emails', array( $this, 'email_approval_settings' ) );
	}

	/**
	 * Get the value of a specific setting
	 *
	 * Note: By default, zero values are not allowed. If you have a custom
	 * setting that needs to allow 0 as a valid value, but sure to add its
	 * key to the filtered array seen in this method.
	 *
	 * @since  1.0
	 * @param  string  $key
	 * @param  mixed   $default (optional)
	 * @return mixed
	 */
	public function get( $key, $default = false ) {

		// Only allow non-empty values, otherwise fallback to the default
		$value = ! empty( $this->options[ $key ] ) ? $this->options[ $key ] : $default;

		/**
		 * Allow certain settings to accept 0 as a valid value without
		 * falling back to the default.
		 *
		 * @since  1.7
		 * @param  array
		 */
		$zero_values_allowed = (array) apply_filters( 'affwp_settings_zero_values_allowed', array( 'referral_rate' ) );

		// Allow 0 values for specified keys only
		if ( in_array( $key, $zero_values_allowed ) ) {

			$value = isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
			$value = ( ! is_null( $value ) && '' !== $value ) ? $value : $default;

		}

		// Handle network-wide debug mode constant.
		if ( 'debug_mode' === $key ) {
			if ( defined( 'AFFILIATE_WP_DEBUG' ) && AFFILIATE_WP_DEBUG ) {
				$value = true;
			}
		}

		return $value;

	}

	/**
	 * Sets an option (in memory).
	 *
	 * @since 1.8
	 * @access public
	 *
	 * @param array $settings An array of `key => value` setting pairs to set.
	 * @param bool  $save     Optional. Whether to trigger saving the option or options. Default false.
	 * @return bool If `$save` is not false, whether the options were saved successfully. True otherwise.
	 */
	public function set( $settings, $save = false ) {
		foreach ( $settings as $option => $value ) {
			$this->options[ $option ] = $value;
		}

		if ( false !== $save ) {
			return $this->save();
		}

		return true;
	}

	/**
	 * Saves option values queued in memory.
	 *
	 * Note: If posting separately from the main settings submission process, this method should
	 * be called directly for direct saving to prevent memory pollution. Otherwise, this method
	 * is only accessible via the optional `$save` parameter in the set() method.
	 *
	 * @since 1.8
	 * @since 1.8.3 Added the `$options` parameter to facilitate direct saving.
	 * @access protected
	 *
	 * @see Affiliate_WP_Settings::set()
	 *
	 * @param array $options Optional. Options to save/overwrite directly. Default empty array.
	 * @return bool False if the options were not updated (saved) successfully, true otherwise.
	 */
	protected function save( $options = array() ) {
		$all_options = $this->get_all();

		if ( ! empty( $options ) ) {
			$all_options = array_merge( $all_options, $options );
		}

		$updated = update_option( 'affwp_settings', $all_options );

		// Refresh the options array available in memory (prevents unexpected race conditions).
		$this->options = get_option( 'affwp_settings', array() );

		return $updated;	}

	/**
	 * Get all settings
	 *
	 * @since 1.0
	 * @return array
	*/
	public function get_all() {
		return $this->options;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0
	 * @return void
	*/
	function register_settings() {

		if ( false == get_option( 'affwp_settings' ) ) {
			add_option( 'affwp_settings' );
		}

		foreach( $this->get_registered_settings() as $tab => $settings ) {

			add_settings_section(
				'affwp_settings_' . $tab,
				__return_null(),
				'__return_false',
				'affwp_settings_' . $tab
			);

			foreach ( $settings as $key => $option ) {

				if( $option['type'] == 'checkbox' || $option['type'] == 'multicheck' || $option['type'] == 'radio' ) {
					$name = isset( $option['name'] ) ? $option['name'] : '';
				} else {
					$name = isset( $option['name'] ) ? '<label for="affwp_settings[' . $key . ']">' . $option['name'] . '</label>' : '';
				}

				$callback = ! empty( $option['callback'] ) ? $option['callback'] : array( $this, $option['type'] . '_callback' );

				add_settings_field(
					'affwp_settings[' . $key . ']',
					$name,
					is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
					'affwp_settings_' . $tab,
					'affwp_settings_' . $tab,
					array(
						'id'       => $key,
						'desc'     => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'     => isset( $option['name'] ) ? $option['name'] : null,
						'section'  => $tab,
						'size'     => isset( $option['size'] ) ? $option['size'] : null,
						'max'      => isset( $option['max'] ) ? $option['max'] : null,
						'min'      => isset( $option['min'] ) ? $option['min'] : null,
						'step'     => isset( $option['step'] ) ? $option['step'] : null,
						'options'  => isset( $option['options'] ) ? $option['options'] : '',
						'std'      => isset( $option['std'] ) ? $option['std'] : '',
						'disabled' => isset( $option['disabled'] ) ? $option['disabled'] : '',
					)
				);
			}

		}

		// Creates our settings in the options table
		register_setting( 'affwp_settings', 'affwp_settings', array( $this, 'sanitize_settings' ) );

	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	function sanitize_settings( $input = array() ) {

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$saved    = get_option( 'affwp_settings', array() );
		if( ! is_array( $saved ) ) {
			$saved = array();
		}
		$settings = $this->get_registered_settings();
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

		$input = $input ? $input : array();

		/**
		 * Filters the input value for the AffiliateWP settings tab.
		 *
		 * This filter is appended with the tab name, followed by the string `_sanitize`, for example:
		 *
		 *     `affwp_settings_misc_sanitize`
		 *     `affwp_settings_integrations_sanitize`
		 *
		 * @param mixed $input The settings tab content to sanitize.
		 */
		$input = apply_filters( 'affwp_settings_' . $tab . '_sanitize', $input );

		// Ensure a value is always passed for every checkbox
		if( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $setting ) {

				// Single checkbox
				if ( isset( $settings[ $tab ][ $key ][ 'type' ] ) && 'checkbox' == $settings[ $tab ][ $key ][ 'type' ] ) {
					$input[ $key ] = ! empty( $input[ $key ] );
				}

				// Multicheck list
				if ( isset( $settings[ $tab ][ $key ][ 'type' ] ) && 'multicheck' == $settings[ $tab ][ $key ][ 'type' ] ) {
					if( empty( $input[ $key ] ) ) {
						$input[ $key ] = array();
					}
				}
			}
		}

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Don't overwrite the global license key.
			if ( 'license_key' === $key ) {
				$value = self::get_license_key( $value, true );
			}

			// Get the setting type (checkbox, select, etc)
			$type              = isset( $settings[ $tab ][ $key ][ 'type' ] ) ? $settings[ $tab ][ $key ][ 'type' ] : false;
			$sanitize_callback = isset( $settings[ $tab ][ $key ][ 'sanitize_callback' ] ) ? $settings[ $tab ][ $key ][ 'sanitize_callback' ] : false;
			$input[ $key ]     = $value;

			if ( $type ) {

				if( $sanitize_callback && is_callable( $sanitize_callback ) ) {

					add_filter( 'affwp_settings_sanitize_' . $type, $sanitize_callback, 10, 2 );

				}

				/**
				 * Filters the sanitized value for a setting of a given type.
				 *
				 * This filter is appended with the setting type (checkbox, select, etc), for example:
				 *
				 *     `affwp_settings_sanitize_checkbox`
				 *     `affwp_settings_sanitize_select`
				 *
				 * @param array  $value The input array and settings key defined within.
				 * @param string $key   The settings key.
				 */
				$input[ $key ] = apply_filters( 'affwp_settings_sanitize_' . $type, $input[ $key ], $key );
			}

			/**
			 * General setting sanitization filter
			 *
			 * @param array  $input[ $key ] The input array and settings key defined within.
			 * @param string $key           The settings key.
			 */
			$input[ $key ] = apply_filters( 'affwp_settings_sanitize', $input[ $key ], $key );

			// Now remove the filter
			if( $sanitize_callback && is_callable( $sanitize_callback ) ) {

				remove_filter( 'affwp_settings_sanitize_' . $type, $sanitize_callback, 10 );

			}
		}

		add_settings_error( 'affwp-notices', '', __( 'Settings updated.', 'affiliate-wp' ), 'updated' );

		return array_merge( $saved, $input );

	}

	/**
	 * Sanitize the referral variable on save
	 *
	 * @since 1.7
	 * @return string
	*/
	public function sanitize_referral_variable( $value = '', $key = '' ) {

		if( 'referral_var' === $key ) {

			if( empty( $value ) ) {

				$value = 'ref';

			} else {

				$value = sanitize_key( $value );

			}

			update_option( 'affwp_flush_rewrites', '1' );

		}

		return $value;
	}

	/**
	 * Sanitize text fields
	 *
	 * @since 1.7
	 * @return string
	*/
	public function sanitize_text_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize URL fields
	 *
	 * @since 1.7.15
	 * @return string
	*/
	public function sanitize_url_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize checkbox fields
	 *
	 * @since 1.7
	 * @return int
	*/
	public function sanitize_cb_fields( $value = '', $key = '' ) {
		return absint( $value );
	}

	/**
	 * Sanitize number fields
	 *
	 * @since 1.7
	 * @return int
	*/
	public function sanitize_number_fields( $value = '', $key = '' ) {
		return floatval( $value );
	}

	/**
	 * Sanitize rich editor fields
	 *
	 * @since 1.7
	 * @return int
	*/
	public function sanitize_rich_editor_fields( $value = '', $key = '' ) {
		return wp_kses_post( $value );
	}

	/**
	 * Set the capability needed to save affiliate settings
	 *
	 * @since 1.9
	 * @return string
	*/
	public function option_page_capability( $capability ) {
		return 'manage_affiliate_options';
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	function get_registered_settings() {

		// get currently logged in username
		$user_info = get_userdata( get_current_user_id() );
		$username  = $user_info ? esc_html( $user_info->user_login ) : '';

		/**
		 * Fires before attempting to retrieve registered settings.
		 *
		 * @since 1.9
		 *
		 * @param Affiliate_WP_Settings $this Settings instance.
		 */
		do_action( 'affwp_pre_get_registered_settings', $this );

		$emails_tags_list = affwp_get_emails_tags_list();

		$referral_pretty_urls_desc = sprintf( __( 'Show pretty affiliate referral URLs to affiliates. For example: <strong>%s or %s</strong>', 'affiliate-wp' ),
			home_url( '/' ) . affiliate_wp()->tracking->get_referral_var() . '/1',
			home_url( '/' ) . trailingslashit( affiliate_wp()->tracking->get_referral_var() ) . $username
		);

		/*
		 * If both WooCommerce and Polylang are active, show a modified
		 * description for the pretty affiliate URLs setting.
		 */
		if ( function_exists( 'WC' ) && class_exists( 'Polylang' ) ) {
			$referral_pretty_urls_desc .= '<p>' . __( 'Note: Pretty affiliate URLs may not always work as expected when using AffiliateWP in combination with WooCommerce and Polylang.', 'affiliate-wp' ) . '</p>';
		}

		$settings = array(
			/**
			 * Filters the default "General" settings.
			 *
			 * @param array $settings General settings.
			 */
			'general' => apply_filters( 'affwp_settings_general',
				array(
					'license' => array(
						'name' => '<strong>' . __( 'License Settings', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'license_key' => array(
						'name' => __( 'License Key', 'affiliate-wp' ),
						'desc' => sprintf( __( 'Please enter your license key. An active license key is needed for automatic plugin updates and <a href="%s" target="_blank">support</a>.', 'affiliate-wp' ), 'https://affiliatewp.com/support/' ),
						'type' => 'license',
						'sanitize_callback' => 'sanitize_text_field'
					),
					'pages' => array(
						'name' => '<strong>' . __( 'Pages', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'affiliates_page' => array(
						'name' => __( 'Affiliate Area', 'affiliate-wp' ),
						'desc' => __( 'This is the page where affiliates will manage their affiliate account.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => affwp_get_pages(),
						'sanitize_callback' => 'absint'
					),
					'terms_of_use' => array(
						'name' => __( 'Terms of Use', 'affiliate-wp' ),
						'desc' => __( 'Select the page that shows the terms of use for Affiliate Registration.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => affwp_get_pages(),
						'sanitize_callback' => 'absint'
					),
					'terms_of_use_label' => array(
						'name' => __( 'Terms of Use Label', 'affiliate-wp' ),
						'desc' => __( 'Enter the text you would like shown for the Terms of Use checkbox.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => __( 'Agree to our Terms of Use and Privacy Policy', 'affiliate-wp' )
					),
					'referrals' => array(
						'name' => '<strong>' . __( 'Referral Settings', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'referral_var' => array(
						'name' => __( 'Referral Variable', 'affiliate-wp' ),
						'desc' => sprintf( __( 'The URL variable for referral URLs. For example: <strong>%s</strong>.', 'affiliate-wp' ), esc_url( add_query_arg( affiliate_wp()->tracking->get_referral_var(), '1', home_url( '/' ) ) ) ),
						'type' => 'text',
						'std' => 'ref'
					),
					'referral_format' => array(
						'name' => __( 'Default Referral Format', 'affiliate-wp' ),
						'desc' => sprintf( __( 'Show referral URLs to affiliates with either their affiliate ID or Username appended.<br/> For example: <strong>%s or %s</strong>.', 'affiliate-wp' ), esc_url( add_query_arg( affiliate_wp()->tracking->get_referral_var(), '1', home_url( '/' ) ) ), esc_url( add_query_arg( affiliate_wp()->tracking->get_referral_var(), $username, home_url( '/' ) ) ) ),
						'type' => 'select',
						/**
						 * The referral format (such as ID or Username)
						 *
						 * @param array The available referring formats.
						 */
						'options' => apply_filters( 'affwp_settings_referral_format',
							array(
								'id'       => __( 'ID', 'affiliate-wp' ),
								'username' => __( 'Username', 'affiliate-wp' ),
							)
						),
						'std' => 'id'
					),
					'referral_pretty_urls' => array(
						'name' => __( 'Pretty Affiliate URLs', 'affiliate-wp' ),
						'desc' => $referral_pretty_urls_desc,
						'type' => 'checkbox'
					),
					'referral_credit_last' => array(
						'name' => __( 'Credit Last Referrer', 'affiliate-wp' ),
						'desc' => __( 'Credit the last affiliate who referred the customer.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'referral_rate_type' => array(
						'name' => __( 'Referral Rate Type', 'affiliate-wp' ),
						'desc' => __( 'Choose a referral rate type. Referrals can be based on either a percentage or a flat rate amount.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => affwp_get_affiliate_rate_types()
					),
					'referral_rate' => array(
						'name' => __( 'Referral Rate', 'affiliate-wp' ),
						'desc' => __( 'The default referral rate. A percentage if the Referral Rate Type is set to Percentage, a flat amount otherwise. Referral rates can also be set for each individual affiliate.', 'affiliate-wp' ),
						'type' => 'number',
						'size' => 'small',
						'step' => '0.01',
						'std' => '20'
					),
					'exclude_shipping' => array(
						'name' => __( 'Exclude Shipping', 'affiliate-wp' ),
						'desc' => __( 'Exclude shipping costs from referral calculations.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'exclude_tax' => array(
						'name' => __( 'Exclude Tax', 'affiliate-wp' ),
						'desc' => __( 'Exclude taxes from referral calculations.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'cookie_exp' => array(
						'name' => __( 'Cookie Expiration', 'affiliate-wp' ),
						'desc' => __( 'Enter how many days the referral tracking cookie should be valid for.', 'affiliate-wp' ),
						'type' => 'number',
						'size' => 'small',
						'std' => '1'
					),
					'cookie_sharing' => array(
						'name' => __( 'Cookie Sharing', 'affiliate-wp' ),
						'desc' => __( 'Share tracking cookies with sub-domains in a multisite install. When enabled, tracking cookies created on domain.com will also be available on sub.domain.com. Note: this only applies to WordPress Multisite installs.', 'affiliate-wp' ),
						'type' => 'checkbox',
					),
					'currency_settings' => array(
						'name' => '<strong>' . __( 'Currency Settings', 'affiliate-wp' ) . '</strong>',
						'desc' => __( 'Configure the currency options', 'affiliate-wp' ),
						'type' => 'header'
					),
					'currency' => array(
						'name' => __( 'Currency', 'affiliate-wp' ),
						'desc' => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => affwp_get_currencies()
					),
					'currency_position' => array(
						'name' => __( 'Currency Symbol Position', 'affiliate-wp' ),
						'desc' => __( 'Choose the location of the currency symbol.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => array(
							'before' => __( 'Before - $10', 'affiliate-wp' ),
							'after' => __( 'After - 10$', 'affiliate-wp' )
						)
					),
					'thousands_separator' => array(
						'name' => __( 'Thousands Separator', 'affiliate-wp' ),
						'desc' => __( 'The symbol (usually , or .) to separate thousands', 'affiliate-wp' ),
						'type' => 'text',
						'size' => 'small',
						'std' => ','
					),
					'decimal_separator' => array(
						'name' => __( 'Decimal Separator', 'affiliate-wp' ),
						'desc' => __( 'The symbol (usually , or .) to separate decimal points', 'affiliate-wp' ),
						'type' => 'text',
						'size' => 'small',
						'std' => '.'
					),
					'form_settings' => array(
						'name' => '<strong>' . __( 'Affiliate Form Settings', 'affiliate-wp' ) . '</strong>',
						'type' => 'header'
					),
					'affiliate_area_forms' => array(
						'name' => __( 'Affiliate Area Forms', 'affiliate-wp' ),
						'desc' => sprintf( __( 'Select which form(s) to show on the Affiliate Area page. The affiliate registration form will only show if <a href="%s">Allow Affiliate Registration</a> is enabled.', 'affiliate-wp' ), admin_url( 'admin.php?page=affiliate-wp-settings&tab=misc' ) ),
						'type' => 'select',
						'options' => array(
							'both'         => __( 'Affiliate Registration Form and Affiliate Login Form', 'affiliate-wp' ),
							'registration' => __( 'Affiliate Registration Form Only', 'affiliate-wp' ),
							'login'        => __( 'Affiliate Login Form Only', 'affiliate-wp' ),
							'none'         => __( 'None', 'affiliate-wp' )

						)
					),
				)
			),
			/** Integration Settings */

			/**
			 * Filters the default integration settings.
			 *
			 * @param array $integrations The enabled integrations. Defaults to `affiliate_wp()->integrations->get_integrations()`.
			 */
			'integrations' => apply_filters( 'affwp_settings_integrations',
				array(
					'integrations' => array(
						'name' => __( 'Integrations', 'affiliate-wp' ),
						'desc' => sprintf( __( 'Choose the integrations to enable. If you are not using any of these, you may use the <strong>[affiliate_conversion_script]</strong> shortcode to track and create referrals. Refer to the <a href="%s" target="_blank">documentation</a> for help using this.', 'affiliate-wp' ), 'http://docs.affiliatewp.com/article/66-generic-referral-tracking-script' ),
						'type' => 'multicheck',
						'options' => affiliate_wp()->integrations->get_integrations()
					),
				)
			),
			/** Opt-In Settings */

			/**
			 * Filters the default opt-in settings.
			 *
			 * @param array $opt_in_forms The opt in form settings.
			 */
			'opt_in_forms' => apply_filters( 'affwp_settings_opt_in_forms',
				array(
					'opt_in_referral_amount' => array(
						'name' => __( 'Opt-In Referral Amount', 'affiliate-wp' ),
						'type' => 'number',
						'size' => 'small',
						'step' => '0.01',
						'std'  => '0.00',
						'desc' => __( 'Enter the amount affiliates should receive for each opt-in referral. Default is 0.00.', 'affiliate-wp' ),
					),
					'opt_in_referral_status' => array(
						'name' => __( 'Opt-In Referral Status', 'affiliate-wp' ),
						'type' => 'radio',
						'options'  => array(
							'pending' => __( 'Pending', 'affiliate-wp' ),
							'unpaid'  => __( 'Unpaid', 'affiliate-wp' ),
						),
						'std' => 'pending',
						'desc' => __( 'Select the status that should be assigned to opt-in referrals by default.', 'affiliate-wp' ),
					),
					'opt_in_success_message' => array(
						'name' => __( 'Message shown upon opt-in success', 'affiliate-wp' ),
						'type' => 'rich_editor',
						'std'  => 'You have subscribed successfully.',
						'desc' => __( 'Enter the message you would like to show subscribers after they have opted-in successfully.', 'affiliate-wp' ),
					),
					'opt_in_platform' => array(
						'name' => __( 'Platform', 'affiliate-wp' ),
						'desc' => __( 'Select the opt-in platform provider you wish to use then click Save Changes to configure the settings. The opt-in form can be displayed on any page using the [opt_in] shortcode. <a href="https://docs.affiliatewp.com/article/2034-optin-opt-in-form">Learn more</a>.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => array_merge( array( '' => __( '(select one)', 'affiliate-wp' ) ), affiliate_wp()->integrations->opt_in->platforms )
					)
					// Individual platform settings are registered through their platform classes in includes/integrations/opt-in-platforms/
				)
			),
			/** Email Settings */

			/**
			 * Filters the default "Email" settings.
			 *
			 * @param array $settings Array of email settings.
			 */
			'emails' => apply_filters( 'affwp_settings_emails',
				array(
					'email_options_header' => array(
						'name' => '<strong>' . __( 'Email Options', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'email_logo' => array(
						'name' => __( 'Logo', 'affiliate-wp' ),
						'desc' => __( 'Upload or choose a logo to be displayed at the top of emails.', 'affiliate-wp' ),
						'type' => 'upload'
					),
					'email_template' => array(
						'name' => __( 'Email Template', 'affiliate-wp' ),
						'desc' => __( 'Choose a template to use for email messages.', 'affiliate-wp' ),
						'type' => 'select',
						'options' => affwp_get_email_templates()
					),
					'from_name' => array(
						'name' => __( 'From Name', 'affiliate-wp' ),
						'desc' => __( 'The name that emails come from. This is usually your site name.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => get_bloginfo( 'name' )
					),
					'from_email' => array(
						'name' => __( 'From Email', 'affiliate-wp' ),
						'desc' => __( 'The email address to send emails from. This will act as the "from" and "reply-to" address.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => get_bloginfo( 'admin_email' )
					),
					'email_notifications' => array(
						'name' => __( 'Email Notifications', 'affiliate-wp' ),
						'desc' => __( 'The email notifications sent to the admin and affiliate.', 'affiliate-wp' ),
						'type' => 'multicheck',
						'options' => $this->email_notifications()
					),
					'registration_options_header' => array(
						'name' => '<strong>' . __( 'Registration Email Admin Options', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'registration_subject' => array(
						'name' => __( 'Registration Email Admin Subject', 'affiliate-wp' ),
						'desc' => __( 'Enter the subject line for the registration email sent to admins when new affiliates register.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => __( 'New Affiliate Registration', 'affiliate-wp' )
					),
					'registration_email' => array(
						'name' => __( 'Registration Email Admin Content', 'affiliate-wp' ),
						'desc' => __( 'Enter the email to send when a new affiliate registers. HTML is accepted. Available template tags:', 'affiliate-wp' ) . '<br />' . $emails_tags_list,
						'type' => 'rich_editor',
						'std' => sprintf( __( 'A new affiliate has registered on your site, %s', 'affiliate-wp' ), home_url() ) . "\n\n" . __( 'Name: ', 'affiliate-wp' ) . "{name}\n\n{website}\n\n{promo_method}"
					),
					'new_admin_referral_options_header' => array(
						'name' => '<strong>' . __( 'New Referral Admin Email Options', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'new_admin_referral_subject' => array(
						'name' => __( 'New Referral Admin Email Subject', 'affiliate-wp' ),
						'desc' => __( 'Enter the subject line for the email sent to site the site administrator when affiliates earn referrals.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => __( 'Referral Earned!', 'affiliate-wp' )
					),
					'new_admin_referral_email' => array(
						'name' => __( 'New Referral Admin Email Content', 'affiliate-wp' ),
						'desc' => __( 'Enter the email to send to site administrators when new referrals are earned. HTML is accepted. Available template tags:', 'affiliate-wp' ) . '<br />' . $emails_tags_list,
						'type' => 'rich_editor',
						'std' => __( '{name} has been awarded a new referral of {amount} on {site_name}.', 'affiliate-wp' )
					),
					'new_referral_options_header' => array(
						'name' => '<strong>' . __( 'New Referral Email Options', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'referral_subject' => array(
						'name' => __( 'New Referral Email Subject', 'affiliate-wp' ),
						'desc' => __( 'Enter the subject line for new referral emails sent when affiliates earn referrals.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => __( 'Referral Awarded!', 'affiliate-wp' )
					),
					'referral_email' => array(
						'name' => __( 'New Referral Email Content', 'affiliate-wp' ),
						'desc' => __( 'Enter the email to send on new referrals. HTML is accepted. Available template tags:', 'affiliate-wp' ) . '<br />' . $emails_tags_list,
						'type' => 'rich_editor',
						'std' => __( 'Congratulations {name}!', 'affiliate-wp' ) . "\n\n" . __( 'You have been awarded a new referral of', 'affiliate-wp' ) . ' {amount} ' . sprintf( __( 'on %s!', 'affiliate-wp' ), home_url() ) . "\n\n" . __( 'Log into your affiliate area to view your earnings or disable these notifications:', 'affiliate-wp' ) . ' {login_url}'
					),
					'accepted_options_header' => array(
						'name' => '<strong>' . __( 'Application Accepted Email Options', 'affiliate-wp' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'accepted_subject' => array(
						'name' => __( 'Application Accepted Email Subject', 'affiliate-wp' ),
						'desc' => __( 'Enter the subject line for accepted application emails sent to affiliates when their account is approved.', 'affiliate-wp' ),
						'type' => 'text',
						'std' => __( 'Affiliate Application Accepted', 'affiliate-wp' )
					),
					'accepted_email' => array(
						'name' => __( 'Application Accepted Email Content', 'affiliate-wp' ),
						'desc' => __( 'Enter the email to send when an application is accepted. HTML is accepted. Available template tags:', 'affiliate-wp' ) . '<br />' . $emails_tags_list,
						'type' => 'rich_editor',
						'std' => __( 'Congratulations {name}!', 'affiliate-wp' ) . "\n\n" . sprintf( __( 'Your affiliate application on %s has been accepted!', 'affiliate-wp' ), home_url() ) . "\n\n" . __( 'Log into your affiliate area at', 'affiliate-wp' ) . ' {login_url}'
					)
				)
			),
			/** Misc Settings */

			/**
			 * Filters the default "Misc" settings.
			 *
			 * @param array $settings Array of misc settings.
			 */
			'misc' => apply_filters( 'affwp_settings_misc',
				array(
					'allow_affiliate_registration' => array(
						'name' => __( 'Allow Affiliate Registration', 'affiliate-wp' ),
						'desc' => __( 'Allow users to register affiliate accounts for themselves.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'require_approval' => array(
						'name' => __( 'Require Approval', 'affiliate-wp' ),
						'desc' => __( 'Require that Pending affiliate accounts must be approved before they can begin earning referrals.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'auto_register' => array(
						'name' => __( 'Auto Register New Users', 'affiliate-wp' ),
						'desc' => __( 'Automatically register new users as affiliates.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'logout_link' => array(
						'name' => __( 'Logout Link', 'affiliate-wp' ),
						'desc' => __( 'Add a logout link to the Affiliate Area.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'default_referral_url' => array(
						'name' => __( 'Default Referral URL', 'affiliate-wp' ),
						'desc' => __( 'The default referral URL shown in the Affiliate Area. Also changes the URL shown in the Referral URL Generator and the {referral_url} email tag.', 'affiliate-wp' ),
						'type' => 'url'
					),
					'recaptcha_enabled' => array(
						'name' => __( 'Enable reCAPTCHA', 'affiliate-wp' ),
						'desc' => __( 'Prevent bots from registering affiliate accounts using Google reCAPTCHA.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'recaptcha_site_key' => array(
						'name' => __( 'reCAPTCHA Site Key', 'affiliate-wp' ),
						'desc' => __( 'This is used to identify your site to Google reCAPTCHA.', 'affiliate-wp' ),
						'type' => 'text'
					),
					'recaptcha_secret_key' => array(
						'name' => __( 'reCAPTCHA Secret Key', 'affiliate-wp' ),
						'desc' => __( 'This is used for communication between your site and Google reCAPTCHA. Be sure to keep it a secret.', 'affiliate-wp' ),
						'type' => 'text'
					),
					'revoke_on_refund' => array(
						'name' => __( 'Reject Unpaid Referrals on Refund', 'affiliate-wp' ),
						'desc' => __( 'Automatically reject Unpaid referrals when the originating purchase is refunded or revoked.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'tracking_fallback' => array(
						'name' => __( 'Use Fallback Referral Tracking Method', 'affiliate-wp' ),
						'desc' => __( 'The method used to track referral links can fail on sites that have jQuery errors. Enable Fallback Tracking if referrals are not being tracked properly.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'ignore_zero_referrals' => array(
						'name' => __( 'Ignore Referrals with Zero Amount', 'affiliate-wp' ),
						'desc' => __( 'Ignore referrals with a zero amount. This can be useful for multi-price products that start at zero, or if a discount was used which resulted in a zero amount. NOTE: If this setting is enabled and a visit results in a zero referral, the visit will be considered not converted.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'disable_ip_logging' => array(
						'name' => __( 'Disable IP Address Logging', 'affiliate-wp' ),
						'desc' => __( 'Disable logging of the customer IP address.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'debug_mode' => array(
						'name' => __( 'Enable Debug Mode', 'affiliate-wp' ),
						'desc' => __( 'Enable debug mode. This will turn on error logging for the referral process to help identify issues.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'referral_url_blacklist' => array(
						'name' => __( 'Referral URL Blacklist', 'affiliate-wp' ),
						'desc' => __( 'URLs placed here will be blocked from generating referrals. Enter one URL per line. NOTE: This will only apply to new visits after the URL has been saved.', 'affiliate-wp' ),
						'type' => 'textarea'
					),
					'betas' => array(
						'name' => __( 'Opt into Beta Versions', 'affiliate-wp' ),
						'desc' => __( 'Receive update notifications for beta releases. When beta versions are available, an update notification will be shown on your Plugins page.', 'affiliate-wp' ),
						'type' => 'checkbox'
					),
					'uninstall_on_delete' => array(
						'name' => __( 'Remove Data on Uninstall', 'affiliate-wp' ),
						'desc' => __( 'Remove all saved data for AffiliateWP when the plugin is deleted.', 'affiliate-wp' ),
						'type' => 'checkbox'
					)
				)
			)
		);

		/**
		 * Filters the entire default settings array.
		 *
		 * @param array $settings Array of default settings.
		 */
		return apply_filters( 'affwp_settings', $settings );
	}

	/**
	 * Required Registration Fields
	 *
	 * @since 2.0
	 * @param array $general_settings
	 * @return array
	 */
	function required_registration_fields( $general_settings ) {

		if ( ! affiliate_wp()->settings->get( 'allow_affiliate_registration' ) ) {
			return $general_settings;
		}

		$new_general_settings = array(
			'required_registration_fields' => array(
				'name' => __( 'Required Registration Fields', 'affiliate-wp' ),
				'desc' => __( 'Select which fields should be required for affiliate registration. The <strong>Username</strong> and <strong>Account Email</strong> form fields are always required. The <strong>Password</strong> form field will be removed if not required.', 'affiliate-wp' ),
				'type' => 'multicheck',
				'options' => array(
					'password'         => __( 'Password', 'affiliate-wp' ),
					'your_name'        => __( 'Your Name', 'affiliate-wp' ),
					'website_url'      => __( 'Website URL', 'affiliate-wp' ),
					'payment_email'    => __( 'Payment Email', 'affiliate-wp' ),
					'promotion_method' => __( 'How will you promote us?', 'affiliate-wp' ),
				)
			)

		);

		return array_merge( $general_settings, $new_general_settings );

	}

	/**
	 * Email notifications
	 *
	 * @since 2.2
	 * @param boolean $install Whether or not the install script has been run.
	 *
	 * @return array $emails
	 */
	public function email_notifications( $install = false ) {

		$emails = array(
			'admin_affiliate_registration_email'   => __( 'Notify admin when a new affiliate has registered', 'affiliate-wp' ),
			'admin_new_referral_email'             => __( 'Notify admin when a new referral has been created', 'affiliate-wp' ),
			'affiliate_new_referral_email'         => __( 'Notify affiliate when they earn a new referral', 'affiliate-wp' ),
			'affiliate_application_accepted_email' => __( 'Notify affiliate when their affiliate application is accepted', 'affiliate-wp' ),
		);

		if ( $this->get( 'require_approval' ) || true === $install ) {
			$emails['affiliate_application_pending_email']  = __( 'Notify affiliate when their affiliate application is pending', 'affiliate-wp' );
			$emails['affiliate_application_rejected_email'] = __( 'Notify affiliate when their affiliate application is rejected', 'affiliate-wp' );
		}

		return $emails;

	}

	/**
	 * Affiliate application approval settings
	 *
	 * @since 1.6.1
	 * @param array $email_settings
	 * @return array
	 */
	function email_approval_settings( $email_settings ) {

		if ( ! affiliate_wp()->settings->get( 'require_approval' ) ) {
			return $email_settings;
		}

		$emails_tags_list = affwp_get_emails_tags_list();

		$new_email_settings = array(
			'pending_options_header' => array(
				'name' => '<strong>' . __( 'Application Pending Email Options', 'affiliate-wp' ) . '</strong>',
				'desc' => '',
				'type' => 'header'
			),
			'pending_subject' => array(
				'name' => __( 'Application Pending Email Subject', 'affiliate-wp' ),
				'desc' => __( 'Enter the subject line for pending affiliate application emails.', 'affiliate-wp' ),
				'type' => 'text',
				'std' => __( 'Your Affiliate Application Is Being Reviewed', 'affiliate-wp' )
			),
			'pending_email' => array(
				'name' => __( 'Application Pending Email Content', 'affiliate-wp' ),
				'desc' => __( 'Enter the email to send when an application is pending. HTML is accepted. Available template tags:', 'affiliate-wp' ) . '<br />' . $emails_tags_list,
				'type' => 'rich_editor',
				'std' => __( 'Hi {name}!', 'affiliate-wp' ) . "\n\n" . __( 'Thanks for your recent affiliate registration on {site_name}.', 'affiliate-wp' ) . "\n\n" . __( 'We&#8217;re currently reviewing your affiliate application and will be in touch soon!', 'affiliate-wp' ) . "\n\n"
			),
			'rejection_options_header' => array(
				'name' => '<strong>' . __( 'Application Rejection Email Options', 'affiliate-wp' ) . '</strong>',
				'desc' => '',
				'type' => 'header'
			),
			'rejection_subject' => array(
				'name' => __( 'Application Rejection Email Subject', 'affiliate-wp' ),
				'desc' => __( 'Enter the subject line for rejected affiliate application emails.', 'affiliate-wp' ),
				'type' => 'text',
				'std' => __( 'Your Affiliate Application Has Been Rejected', 'affiliate-wp' )
			),
			'rejection_email' => array(
				'name' => __( 'Application Rejection Email Content', 'affiliate-wp' ),
				'desc' => __( 'Enter the email to send when an application is rejected. HTML is accepted. Available template tags:', 'affiliate-wp' ) . '<br />' . $emails_tags_list,
				'type' => 'rich_editor',
				'std' => __( 'Hi {name},', 'affiliate-wp' ) . "\n\n" . __( 'We regret to inform you that your recent affiliate registration on {site_name} was rejected.', 'affiliate-wp' ) . "\n\n"
			)

		);

		return array_merge( $email_settings, $new_email_settings );
	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function header_callback( $args ) {
		echo '<hr/>';
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function checkbox_callback( $args ) {

		$checked  = isset( $this->options[ $args['id'] ] ) ? checked( 1, $this->options[ $args['id'] ], false) : '';
		$disabled = $this->is_setting_disabled( $args ) ? disabled( $args['disabled'], true, false ) : '';

		$html = '<label for="affwp_settings[' . $args['id'] . ']">';
		$html .= '<input type="checkbox" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" value="1" ' . $checked . ' ' . $disabled . '/>&nbsp;';
		$html .= $args['desc'];
		$html .= '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function multicheck_callback( $args ) {

		if ( ! empty( $args['options'] ) ) {
			foreach( $args['options'] as $key => $option ) {
				if( isset( $this->options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
				echo '<label for="affwp_settings[' . $args['id'] . '][' . $key . ']">';
				echo '<input name="affwp_settings[' . $args['id'] . '][' . $key . ']" id="affwp_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
				echo $option . '</label><br/>';
			}
			echo '<p class="description">' . $args['desc'] . '</p>';
		}
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function radio_callback( $args ) {

		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $this->options[ $args['id'] ] ) && $this->options[ $args['id'] ] == $key )
				$checked = true;
			elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) )
				$checked = true;

			echo '<label for="affwp_settings[' . $args['id'] . '][' . $key . ']">';
			echo '<input name="affwp_settings[' . $args['id'] . ']"" id="affwp_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
			echo $option . '</label><br/>';
		endforeach;

		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function text_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) && ! empty( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		// Must use a 'readonly' attribute over disabled to ensure the value is passed in $_POST.
		$readonly = $this->is_setting_disabled( $args ) ? __checked_selected_helper( $args['disabled'], true, false, 'readonly' ) : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" ' . $readonly . '/>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * URL Callback
	 *
	 * Renders URL fields.
	 *
	 * @since 1.7.15
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function url_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="url" class="' . $size . '-text" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * License Callback
	 *
	 * Renders license key fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function license_callback( $args ) {
		$status = $this->get( 'license_status' );
		$status = is_object( $status ) ? $status->license : $status;

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = '';
		}

		$license_key = self::get_license_key( $value );

		// If the license is active and valid, set the field to disabled (readonly).
		if ( 'valid' === $status && ! empty( $license_key ) ) {
			$args['disabled'] = true;

			if ( self::global_license_set() ) {
				$args['desc'] = __( 'Your license key is globally defined via <code>AFFILIATEWP_LICENSE_KEY</code> set in <code>wp-config.php</code>.<br />It cannot be modified from this screen.', 'affiliate-wp' );
			} else {
				$args['desc'] = __( 'Deactivate your license key to make changes to this setting.', 'affiliate-wp' );
			}
		}

		// Must use a 'readonly' attribute over disabled to ensure the value is passed in $_POST.
		$readonly = $this->is_setting_disabled( $args ) ? __checked_selected_helper( $args['disabled'], true, false, 'readonly' ) : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $license_key ) ) . '" ' . $readonly . '/>';

		if( 'valid' === $status && ! empty( $license_key ) ) {
			$html .= get_submit_button( __( 'Deactivate License', 'affiliate-wp' ), 'secondary', 'affwp_deactivate_license', false );
			$html .= '<span style="color:green;">&nbsp;' . __( 'Your license is valid!', 'affiliate-wp' ) . '</span>';
		} elseif( 'expired' === $status && ! empty( $license_key ) ) {
			$renewal_url = esc_url( add_query_arg( array( 'edd_license_key' => $license_key, 'download_id' => 17 ), 'https://affiliatewp.com/checkout' ) );
			$html .= '<a href="' . esc_url( $renewal_url ) . '" class="button-primary">' . __( 'Renew Your License', 'affiliate-wp' ) . '</a>';
			$html .= '<br/><span style="color:red;">&nbsp;' . __( 'Your license has expired, renew today to continue getting updates and support!', 'affiliate-wp' ) . '</span>';
		} else {
			$html .= get_submit_button( __( 'Activate License', 'affiliate-wp' ), 'secondary', 'affwp_activate_license', false );
		}

		$html .= '<br/><p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since 1.9
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function number_callback( $args ) {

		// Get value, with special consideration for 0 values, and never allowing negative values
		$value = isset( $this->options[ $args['id'] ] ) ? $this->options[ $args['id'] ] : null;
		$value = ( ! is_null( $value ) && '' !== $value && floatval( $value ) >= 0 ) ? floatval( $value ) : null;

		// Saving the field empty will revert to std value, if it exists
		$std   = ( isset( $args['std'] ) && ! is_null( $args['std'] ) && '' !== $args['std'] && floatval( $args['std'] ) >= 0 ) ? $args['std'] : null;
		$value = ! is_null( $value ) ? $value : ( ! is_null( $std ) ? $std : null );
		$value = affwp_abs_number_round( $value );

		// Other attributes and their defaults
		$max  = isset( $args['max'] )  ? $args['max']  : 999999;
		$min  = isset( $args['min'] )  ? $args['min']  : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;
		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

		$html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function textarea_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<textarea class="large-text" cols="50" rows="5" id="affwp_settings_' . $args['id'] . '" name="affwp_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @since 1.3
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function password_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="password" class="' . $size . '-text" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since 1.3.1
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function missing_callback($args) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'affiliate-wp' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @return void
	 */
	function select_callback($args) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$html = '<select id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the AffiliateWP Options
	 * @global $wp_version WordPress Version
	 */
	function rich_editor_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		ob_start();
		wp_editor( stripslashes( $value ), 'affwp_settings_' . $args['id'], array( 'textarea_name' => 'affwp_settings[' . $args['id'] . ']' ) );
		$html = ob_get_clean();

		$html .= '<br/><p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Upload Callback
	 *
	 * Renders file upload fields.
	 *
	 * @since 1.6
	 * @param array $args Arguements passed by the setting
	 */
	function upload_callback( $args ) {
		if( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="affwp_settings[' . $args['id'] . ']" name="affwp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<span>&nbsp;<input type="button" class="affwp_settings_upload_button button-secondary" value="' . __( 'Upload File', 'affiliate-wp' ) . '"/></span>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Handles overriding and disabling the license key setting if a global key is defined.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function handle_global_license_setting() {
		if ( self::global_license_set() ) {
			$this->options['license_key'] = self::get_license_key();

			add_filter( 'affwp_settings_general', function ( $general_settings ) {
				$general_settings['license_key']['disabled'] = true;
				$general_settings['license_key']['desc']     = sprintf( __( 'Your license key is globally defined via <code>AFFILIATEWP_LICENSE_KEY</code> set in <code>wp-config.php</code>.<br />It cannot be modified from this screen.<br />An active license key is needed for automatic plugin updates and <a href="%s" target="_blank">support</a>.', 'affiliate-wp' ), 'https://affiliatewp.com/support/' );

				return $general_settings;
			} );
		}
	}

	/**
	 * Handles overriding and disabling the debug mode setting if globally enabled.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function handle_global_debug_mode_setting() {
		if ( defined( 'AFFILIATE_WP_DEBUG' ) && true === AFFILIATE_WP_DEBUG ) {
			$this->options['debug_mode'] = 1;

			// Globally enabled.
			add_filter( 'affwp_settings_misc', function( $misc_settings ) {
				$misc_settings['debug_mode']['disabled'] = true;
				$misc_settings['debug_mode']['desc']     = __( 'Debug mode is globally enabled via <code>AFFILIATE_WP_DEBUG</code> set in <code>wp-config.php</code>. This setting cannot be modified from this screen.', 'affiliate-wp' );

				return $misc_settings;
			} );
		}
	}

	/**
	 * Determines whether a setting is disabled.
	 *
	 * @since 1.8.3
	 * @access public
	 *
	 * @param array $args Setting arguments.
	 * @return bool True or false if the setting is disabled, otherwise false.
	 */
	public function is_setting_disabled( $args ) {
		if ( isset( $args['disabled'] ) ) {
			return $args['disabled'];
		}
		return false;
	}

	public function activate_license() {

		if( ! isset( $_POST['affwp_settings'] ) ) {
			return;
		}

		if( ! isset( $_POST['affwp_activate_license'] ) ) {
			return;
		}

		if( ! isset( $_POST['affwp_settings']['license_key'] ) ) {
			return;
		}

		// Retrieve the license status from the database.
		$status = $this->get( 'license_status' );

		if( is_object( $status ) ) {
			$status = $status->license;
		}

		if( 'valid' == $status ) {
			return; // license already activated and valid
		}

		$license_key = sanitize_text_field( $_POST['affwp_settings']['license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license_key,
			'item_name' => 'AffiliateWP',
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( 'https://affiliatewp.com', array( 'timeout' => 35, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {

			wp_safe_redirect( affwp_admin_url( 'settings', array( 'affwp_notice' => 'license-http-failure', 'affwp_message' => $response->get_error_message(), 'affwp_success' => 'no' ) ) );
			exit;

		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		$this->save( array(
			'license_status' => $license_data,
			'license_key'    => $license_key
		) );

		set_transient( 'affwp_license_check', $license_data->license, DAY_IN_SECONDS );

		if( 'valid' !== $license_data->license || empty( $license_data->success ) ) {

			wp_safe_redirect( affwp_admin_url( 'settings', array( 'affwp_notice' => 'license-' . $license_data->error, 'affwp_success' => 'no' ) ) );
			exit;

		}

		wp_safe_redirect( affwp_admin_url( 'settings' ) );
		exit;

	}

	public function deactivate_license() {

		if( ! isset( $_POST['affwp_settings'] ) ) {
			return;
		}

		if( ! isset( $_POST['affwp_deactivate_license'] ) ) {
			return;
		}

		if( ! isset( $_POST['affwp_settings']['license_key'] ) ) {
			return;
		}

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $_POST['affwp_settings']['license_key'],
			'item_name' => 'AffiliateWP',
			'url'       => home_url()
		);

		// Call the custom API.
		$message  = '';
		$success  = true;
		$response = wp_remote_post( 'https://affiliatewp.com', array( 'timeout' => 35, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {

			$success = false;
			$message = $response->get_error_message();

			wp_safe_redirect( affwp_admin_url( 'settings', array( 'message' => $message, 'success' => $success ) ) );
			exit;

		}

		$this->save( array( 'license_status' => 0 ) );

	}

	public function check_license( $force = false ) {

		if( ! empty( $_POST['affwp_settings'] ) ) {
			return; // Don't fire when saving settings
		}

		$status = get_transient( 'affwp_license_check' );

		$request_url = 'https://affiliatewp.com';

		// Run the license check a maximum of once per day
		if( ( false === $status || $force ) && site_url() !== $request_url ) {
			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'check_license',
				'license' 	=> self::get_license_key(),
				'item_name' => 'AffiliateWP',
				'url'       => home_url()
			);

			/**
			 * Filters whether to send site data.
			 *
			 * @param bool $send Whether to send site data. Default true.
			 */
			if( apply_filters( 'affwp_send_site_data', true ) ) {

				// Send checkins once per week
				$last_checked = get_option( 'affwp_last_checkin', false );

				if( ! is_numeric( $last_checked ) || $last_checked < strtotime( '-1 week', current_time( 'timestamp' ) ) ) {

					$api_params['site_data'] = $this->get_site_data();

				}
			}

			// Call the custom API.
			$response = wp_remote_post( $request_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {

				// Connection failed, try again in three hours
				set_transient( 'affwp_license_check', $response, 3 * HOUR_IN_SECONDS );

				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			$this->save( array( 'license_status' => $license_data ) );

			set_transient( 'affwp_license_check', $license_data->license, DAY_IN_SECONDS );

			if( ! empty( $api_params['site_data'] ) ) {

				update_option( 'affwp_last_checkin', current_time( 'timestamp' ) );

			}

			$status = $license_data->license;

		}

		return $status;

	}

	public function is_license_valid() {
		return $this->check_license() == 'valid';
	}

	/**
	 * Retrieves the license key.
	 *
	 * If the `AFFILIATEWP_LICENSE_KEY` constant is defined, it will override values
	 * stored in the database.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param string $key    Optional. License key to check. Default empty.
	 * @param bool   $saving Optional. Whether a saving operation is being performed. If true,
	 *                       the already-saved key value will be ignored. Default false.
	 * @return string License key.
	 */
	public static function get_license_key( $key = '', $saving = false ) {
		if ( self::global_license_set() ) {
			$license = AFFILIATEWP_LICENSE_KEY;
		} elseif ( ! empty( $key ) || true === $saving ) {
			$license = $key;
		} else {
			$license = affiliate_wp()->settings->get( 'license_key' );
		}

		return trim( $license );
	}

	/**
	 * Determines whether the global license key has been defined.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @return bool True if the global license has been defined, otherwise false.
	 */
	public static function global_license_set() {
		if ( defined( 'AFFILIATEWP_LICENSE_KEY' ) && AFFILIATEWP_LICENSE_KEY ) {
			return true;
		}
		return false;
	}

	/**
	 * Retrieves site data (plugin versions, integrations, etc) to be sent along with the license check.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array
	 */
	public function get_site_data() {

		$data = array();

		$theme_data = wp_get_theme();
		$theme      = $theme_data->Name . ' ' . $theme_data->Version;

		$data['php_version']  = phpversion();
		$data['affwp_version']  = AFFILIATEWP_VERSION;
		$data['wp_version']   = get_bloginfo( 'version' );
		$data['server']       = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';
		$data['install_date'] = get_post_field( 'post_date', affwp_get_affiliate_area_page_id() );
		$data['multisite']    = is_multisite();
		$data['url']          = home_url();
		$data['theme']        = $theme;

		// Retrieve current plugin information
		if( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins        = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				// Remove active plugins from list so we can show active and inactive separately
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins']   = $active_plugins;
		$data['inactive_plugins'] = $plugins;
		$data['locale']           = get_locale();
		$data['integrations']     = affiliate_wp()->integrations->get_enabled_integrations();
		$data['affiliates']       = affiliate_wp()->affiliates->count( array( 'number' => -1 ) );
		$data['creatives']        = affiliate_wp()->creatives->count( array( 'number' => -1 ) );
		$data['payouts']          = affiliate_wp()->affiliates->payouts->count( array( 'number' => -1 ) );
		$data['referrals']        = affiliate_wp()->referrals->count( array( 'number' => -1 ) );
		$data['consumers']        = affiliate_wp()->REST->consumers->count( array( 'number' => -1 ) );
		$data['visits']           = affiliate_wp()->visits->count( array( 'number' => -1 ) );
		$data['referral_rate']    = $this->get( 'referral_rate' );
		$data['rate_type']        = $this->get( 'referral_rate_type' );

		return $data;
	}
}
