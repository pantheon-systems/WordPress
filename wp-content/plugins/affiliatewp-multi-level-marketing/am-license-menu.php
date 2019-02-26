<?php
/**
 * @copyright   Copyright (c) Todd Lahman LLC
 *              Author URI:       https://www.toddlahman.com/
 *              Version: 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'AFFWP_MLM_License_Menu' ) ) {
	class AFFWP_MLM_License_Menu {
		/**
		 * Class args.
		 *
		 * @var string
		 */
		public $file             = '';
		public $software_title   = '';
		public $software_version = '';
		public $plugin_or_theme  = '';
		public $api_url          = '';
		public $data_prefix      = '';
		public $slug             = '';
		public $plugin_name      = '';
		public $text_domain      = '';
		public $extra            = '';
		/**
		 * Class properties.
		 *
		 * @var string
		 */
		public $ame_software_product_id;
		public $ame_data_key;
		public $ame_api_key;
		public $ame_activation_email;
		public $ame_product_id_key;
		public $ame_instance_key;
		public $ame_deactivate_checkbox_key;
		public $ame_activated_key;
		public $ame_activation_tab_key;
		public $ame_settings_menu_title;
		public $ame_settings_title;
		public $ame_menu_tab_activation_title;
		public $ame_menu_tab_deactivation_title;
		public $ame_options;
		public $ame_plugin_name;
		public $ame_product_id;
		public $ame_renew_license_url;
		public $ame_instance_id;
		public $ame_domain;
		public $ame_software_version;
		/**
		 * @var null
		 */
		protected static $_instance = null;
		/**
		 * @param string $file             Must be $this->file from the root plugin file, or theme functions file.
		 * @param string $software_title   Must be exactly the same as the Software Title in the product.
		 * @param string $software_version This products current software version.
		 * @param string $plugin_or_theme  'plugin' or 'theme'
		 * @param string $api_url          The URL to the site that is running the API Manager. Example: https://www.toddlahman.com/ Must have a trailing slash.
		 * @param string $text_domain      The text domain for translation. Hardcoding this string is preferred rather than using this argument.
		 * @param string $extra            Extra data. Whatever you want.
		 *
		 * @return \AM_License_Menu|null
		 */
		public static function instance( $file, $software_title, $software_version, $plugin_or_theme, $api_url, $text_domain = '', $extra = '' ) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self( $file, $software_title, $software_version, $plugin_or_theme, $api_url, $text_domain, $extra );
			}
			return self::$_instance;
		}
		public function __construct( $file, $software_title, $software_version, $plugin_or_theme, $api_url, $text_domain, $extra ) {
			$this->file            = $file;
			$this->software_title  = $software_title;
			$this->version         = $software_version;
			$this->plugin_or_theme = $plugin_or_theme;
			$this->api_url         = $api_url;
			$this->text_domain     = $text_domain;
			$this->extra           = $extra;
			$this->data_prefix     = str_ireplace( array( ' ', '_', '&', '?' ), '_', strtolower( $this->software_title ) );
			if ( $this->plugin_or_theme == 'plugin' ) {
				register_activation_hook( $this->file, array( $this, 'activation' ) );
			}
			if ( $this->plugin_or_theme == 'theme' ) {
				add_action( 'admin_init', array( $this, 'activation' ) );
			}
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_init', array( $this, 'load_settings' ) );
			if ( is_admin() ) {
				// Check for external connection blocking
				add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );
				/**
				 * Software Product ID is the product title string
				 * This value must be unique, and it must match the API tab for the product in WooCommerce
				 */
				$this->ame_software_product_id = $this->software_title;
				/**
				 * Set all data defaults here
				 */
				$this->ame_data_key                = $this->data_prefix . '_data';
				$this->ame_api_key                 = 'api_key';
				$this->ame_activation_email        = 'activation_email';
				$this->ame_product_id_key          = $this->data_prefix . '_product_id';
				$this->ame_instance_key            = $this->data_prefix . '_instance';
				$this->ame_deactivate_checkbox_key = $this->data_prefix . '_deactivate_checkbox';
				$this->ame_activated_key           = $this->data_prefix . '_activated';
				/**
				 * Set all admin menu data
				 */
				$this->ame_deactivate_checkbox         = $this->data_prefix . '_deactivate_checkbox';
				$this->ame_activation_tab_key          = $this->data_prefix . '_dashboard';
				$this->ame_deactivation_tab_key        = $this->data_prefix . '_deactivation';
				$this->ame_settings_menu_title         = $this->software_title . __( ' Activation', $this->text_domain );
				$this->ame_settings_title              = $this->software_title . __( ' API Key Activation', $this->text_domain );
				$this->ame_menu_tab_activation_title   = __( 'API Key Activation', $this->text_domain );
				$this->ame_menu_tab_deactivation_title = __( 'API Key Deactivation', $this->text_domain );
				/**
				 * Set all software update data here
				 */
				$this->ame_options           = get_option( $this->ame_data_key );
				$this->ame_plugin_name       = $this->plugin_or_theme == 'plugin' ? untrailingslashit( plugin_basename( $this->file ) ) : get_stylesheet(); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
				$this->ame_product_id        = get_option( $this->ame_product_id_key ); // Software Title
				$this->ame_renew_license_url = $this->api_url . 'my-account'; // URL to renew an API Key. Trailing slash in the upgrade_url is required.
				$this->ame_instance_id       = get_option( $this->ame_instance_key ); // Instance ID (unique to each blog activation)
				/**
				 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
				 * so only the host portion of the URL can be sent. For example the host portion might be
				 * www.example.com or example.com. http://www.example.com includes the scheme http,
				 * and the host www.example.com.
				 * Sending only the host also eliminates issues when a client site changes from http to https,
				 * but their activation still uses the original scheme.
				 * To send only the host, use a line like the one below:
				 *
				 * $this->ame_domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
				 */
				$this->ame_domain           = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
				$this->ame_software_version = $this->version; // The software version
				$options                    = get_option( $this->ame_data_key );
				/**
				 * Check for software updates
				 */
				if ( ! empty( $options ) && $options !== false ) {
					$this->check_for_update();
				}
			}
			if ( get_option( $this->ame_activated_key ) != 'Activated' ) {
				add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
			}
			/**
			 * Deletes all data if plugin deactivated
			 */
			if ( $this->plugin_or_theme == 'plugin' ) {
				register_deactivation_hook( $this->file, array( $this, 'uninstall' ) );
			}
			if ( $this->plugin_or_theme == 'theme' ) {
				add_action( 'switch_theme', array( $this, 'uninstall' ) );
			}
		}
		/**
		 * Register submenu specific to this product.
		 */
		public function register_menu() {
			add_options_page( __( $this->ame_settings_menu_title, $this->text_domain ), __( $this->ame_settings_menu_title, $this->text_domain ), 'manage_options', $this->ame_activation_tab_key, array(
				$this,
				'config_page'
			) );
		}
		/**
		 * Generate the default data arrays
		 */
		public function activation() {
			if ( get_option( $this->ame_data_key ) === false || get_option( $this->ame_instance_key ) === false ) {
				$global_options = array(
					$this->ame_api_key          => '',
					$this->ame_activation_email => '',
				);
				update_option( $this->ame_data_key, $global_options );
				$single_options = array(
					$this->ame_product_id_key          => $this->ame_software_product_id,
					$this->ame_instance_key            => wp_generate_password( 12, false ),
					$this->ame_deactivate_checkbox_key => 'on',
					$this->ame_activated_key           => 'Deactivated',
				);
				foreach ( $single_options as $key => $value ) {
					update_option( $key, $value );
				}
			}
		}
		/**
		 * Deletes all data if plugin deactivated
		 *
		 * @return void
		 */
		public function uninstall() {
			global $blog_id;
			$this->license_key_deactivation();
			// Remove options
			if ( is_multisite() ) {
				switch_to_blog( $blog_id );
				foreach (
					array(
						$this->ame_data_key,
						$this->ame_product_id_key,
						$this->ame_instance_key,
						$this->ame_deactivate_checkbox_key,
						$this->ame_activated_key,
					) as $option
				) {
					delete_option( $option );
				}
				restore_current_blog();
			} else {
				foreach (
					array(
						$this->ame_data_key,
						$this->ame_product_id_key,
						$this->ame_instance_key,
						$this->ame_deactivate_checkbox_key,
						$this->ame_activated_key
					) as $option
				) {
					delete_option( $option );
				}
			}
		}
		/**
		 * Deactivates the license on the API server
		 *
		 * @return void
		 */
		public function license_key_deactivation() {
			$activation_status = get_option( $this->ame_activated_key );
			$api_email         = $this->ame_options[ $this->ame_activation_email ];
			$api_key           = $this->ame_options[ $this->ame_api_key ];
			$args = array(
				'email'       => $api_email,
				'licence_key' => $api_key,
			);
			if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
				$this->deactivate( $args ); // reset API Key activation
			}
		}
		/**
		 * Displays an inactive notice when the software is inactive.
		 */
		public function inactive_notice() { ?>
			<?php if ( ! current_user_can( 'manage_options' ) ) {
				return;
			} ?>
			<?php if ( isset( $_GET[ 'page' ] ) && $this->ame_activation_tab_key == $_GET[ 'page' ] ) {
				return;
			} ?>
            <div class="notice notice-error">
                <p><?php printf( __( 'The <strong>%s</strong> API Key has not been activated, so the %s is inactive! %sClick here%s to activate <strong>%s</strong>.', $this->text_domain ), esc_attr( $this->software_title ), esc_attr( $this->plugin_or_theme ), '<a href="' . esc_url( admin_url( 'options-general.php?page=' . $this->ame_activation_tab_key ) ) . '">', '</a>', esc_attr( $this->software_title ) ); ?></p>
            </div>
			<?php
		}
		/**
		 * Check for external blocking contstant.
		 */
		public function check_external_blocking() {
			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
			if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {
				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->api_url, PHP_URL_HOST );
				if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
                    <div class="notice notice-error">
                        <p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->text_domain ), $this->ame_software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
                    </div>
					<?php
				}
			}
		}
		// Draw option page
		public function config_page() {
			$settings_tabs = array(
				$this->ame_activation_tab_key   => __( $this->ame_menu_tab_activation_title, $this->text_domain ),
				$this->ame_deactivation_tab_key => __( $this->ame_menu_tab_deactivation_title, $this->text_domain )
			);
			$current_tab   = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $this->ame_activation_tab_key;
			$tab           = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $this->ame_activation_tab_key;
			?>
            <div class='wrap'>
                <h2><?php _e( $this->ame_settings_title, $this->text_domain ); ?></h2>
                <h2 class="nav-tab-wrapper">
					<?php
					foreach ( $settings_tabs as $tab_page => $tab_name ) {
						$active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
						echo '<a class="nav-tab ' . $active_tab . '" href="?page=' . $this->ame_activation_tab_key . '&tab=' . $tab_page . '">' . $tab_name . '</a>';
					}
					?>
                </h2>
                <form action='options.php' method='post'>
                    <div class="main">
						<?php
						if ( $tab == $this->ame_activation_tab_key ) {
							settings_fields( $this->ame_data_key );
							do_settings_sections( $this->ame_activation_tab_key );
							submit_button( __( 'Save Changes', $this->text_domain ) );
						} else {
							settings_fields( $this->ame_deactivate_checkbox );
							do_settings_sections( $this->ame_deactivation_tab_key );
							submit_button( __( 'Save Changes', $this->text_domain ) );
						}
						?>
                    </div>
                </form>
            </div>
			<?php
		}
		// Register settings
		public function load_settings() {
			register_setting( $this->ame_data_key, $this->ame_data_key, array( $this, 'validate_options' ) );
			// API Key
			add_settings_section( $this->ame_api_key, __( 'API Key Activation', $this->text_domain ), array(
				$this,
				'wc_am_api_key_text'
			), $this->ame_activation_tab_key );
			add_settings_field( 'status', __( 'API Key Status', $this->text_domain ), array(
				$this,
				'wc_am_api_key_status'
			), $this->ame_activation_tab_key, $this->ame_api_key );
			add_settings_field( $this->ame_api_key, __( 'API Key', $this->text_domain ), array(
				$this,
				'wc_am_api_key_field'
			), $this->ame_activation_tab_key, $this->ame_api_key );
			add_settings_field( $this->ame_activation_email, __( 'API Email', $this->text_domain ), array(
				$this,
				'wc_am_api_email_field'
			), $this->ame_activation_tab_key, $this->ame_api_key );
			// Activation settings
			register_setting( $this->ame_deactivate_checkbox, $this->ame_deactivate_checkbox, array( $this, 'wc_am_license_key_deactivation' ) );
			add_settings_section( 'deactivate_button', __( 'API Deactivation', $this->text_domain ), array(
				$this,
				'wc_am_deactivate_text'
			), $this->ame_deactivation_tab_key );
			add_settings_field( 'deactivate_button', __( 'Deactivate API Key', $this->text_domain ), array(
				$this,
				'wc_am_deactivate_textarea'
			), $this->ame_deactivation_tab_key, 'deactivate_button' );
		}
		// Provides text for api key section
		public function wc_am_api_key_text() { }
		// Returns the API Key status from the WooCommerce API Manager on the server
		public function wc_am_api_key_status() {
			$license_status       = $this->license_key_status();
			$license_status_check = ( ! empty( $license_status[ 'status_check' ] ) && $license_status[ 'status_check' ] == 'active' ) ? 'Activated' : 'Deactivated';
			if ( ! empty( $license_status_check ) ) {
				echo $license_status_check;
			}
		}
		// Returns API Key text field
		public function wc_am_api_key_field() {
			echo "<input id='api_key' name='" . $this->ame_data_key . "[" . $this->ame_api_key . "]' size='25' type='text' value='" . $this->ame_options[ $this->ame_api_key ] . "' />";
			if ( $this->ame_options[ $this->ame_api_key ] ) {
				echo "<span class='dashicons dashicons-yes' style='color: #66ab03;'></span>";
			} else {
				echo "<span class='dashicons dashicons-no' style='color: #ca336c;'></span>";
			}
		}
		// Returns API email text field
		public function wc_am_api_email_field() {
			echo "<input id='activation_email' name='" . $this->ame_data_key . "[" . $this->ame_activation_email . "]' size='25' type='text' value='" . $this->ame_options[ $this->ame_activation_email ] . "' />";
			if ( $this->ame_options[ $this->ame_activation_email ] ) {
				echo "<span class='dashicons dashicons-yes' style='color: #66ab03;'></span>";
			} else {
				echo "<span class='dashicons dashicons-no' style='color: #ca336c;'></span>";
			}
		}
		// Sanitizes and validates all input and output for Dashboard
		public function validate_options( $input ) {
			// Load existing options, validate, and update with changes from input before returning
			$options                                = $this->ame_options;
			$options[ $this->ame_api_key ]          = trim( $input[ $this->ame_api_key ] );
			$options[ $this->ame_activation_email ] = trim( $input[ $this->ame_activation_email ] );
			$api_email                              = trim( $input[ $this->ame_activation_email ] );
			$api_key                                = trim( $input[ $this->ame_api_key ] );
			$activation_status                      = get_option( $this->ame_activated_key );
			$checkbox_status                        = get_option( $this->ame_deactivate_checkbox );
			$current_api_key                        = $this->ame_options[ $this->ame_api_key ];
			// Should match the settings_fields() value
			if ( $_REQUEST[ 'option_page' ] != $this->ame_deactivate_checkbox ) {
				if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key ) {
					/**
					 * If this is a new key, and an existing key already exists in the database,
					 * deactivate the existing key before activating the new key.
					 */
					if ( $current_api_key != $api_key ) {
						$this->replace_license_key( $current_api_key );
					}
					$args             = array(
						'email'       => $api_email,
						'licence_key' => $api_key,
					);
					$activate_results = json_decode( $this->activate( $args ), true );
					if ( $activate_results[ 'activated' ] === true && ! empty( $this->ame_activated_key ) ) {
						add_settings_error( 'activate_text', 'activate_msg', sprintf( __( '%s activated. ', $this->text_domain ), esc_attr( $this->software_title ) ) . "{$activate_results['message']}.", 'updated' );
						update_option( $this->ame_activated_key, 'Activated' );
						update_option( $this->ame_deactivate_checkbox, 'off' );
					}
					if ( $activate_results == false && ! empty( $this->ame_options ) && ! empty( $this->ame_activated_key ) ) {
						add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Try again later.', $this->text_domain ), 'error' );
						$options[ $this->ame_api_key ]          = '';
						$options[ $this->ame_activation_email ] = '';
						update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
					}
					if ( isset( $activate_results[ 'code' ] ) && ! empty( $this->ame_options ) && ! empty( $this->ame_activated_key ) ) {
						switch ( $activate_results[ 'code' ] ) {
							case '100':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_activation_email ] = '';
								$options[ $this->ame_api_key ]          = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
							case '101':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_api_key ]          = '';
								$options[ $this->ame_activation_email ] = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
							case '102':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_api_key ]          = '';
								$options[ $this->ame_activation_email ] = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
							case '103':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_api_key ]          = '';
								$options[ $this->ame_activation_email ] = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
							case '104':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_api_key ]          = '';
								$options[ $this->ame_activation_email ] = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
							case '105':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_api_key ]          = '';
								$options[ $this->ame_activation_email ] = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
							case '106':
								$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
								add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$additional_info}", 'error' );
								$options[ $this->ame_api_key ]          = '';
								$options[ $this->ame_activation_email ] = '';
								update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
								break;
						}
					}
				} // End Plugin Activation
			}
			return $options;
		}
		/**
		 * Returns the API Key status from the WooCommerce API Manager on the server.
		 *
		 * @return array|mixed|object
		 */
		public function license_key_status() {
			$args = array(
				'email'       => $this->ame_options[ $this->ame_activation_email ],
				'licence_key' => $this->ame_options[ $this->ame_api_key ],
			);
			return json_decode( $this->status( $args ), true );
		}
		/**
		 * Deactivate the current API Key before activating the new API Key
		 *
		 * @param string $current_api_key
		 *
		 * @return bool
		 */
		public function replace_license_key( $current_api_key ) {
			$args = array(
				'email'       => $this->ame_options[ $this->ame_activation_email ],
				'licence_key' => $current_api_key,
			);
			$reset = $this->deactivate( $args ); // reset API Key activation
			if ( $reset == true ) {
				return true;
			}
			add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The API Key could not be deactivated. Use the API Key Deactivation tab to manually deactivate the API Key before activating a new API Key.', $this->text_domain ), 'updated' );
			return false;
		}
		// Deactivates the API Key to allow key to be used on another blog
		public function wc_am_license_key_deactivation( $input ) {
			$activation_status = get_option( $this->ame_activated_key );
			$args              = array(
				'email'       => $this->ame_options[ $this->ame_activation_email ],
				'licence_key' => $this->ame_options[ $this->ame_api_key ],
			);
			// For testing activation status_extra data
			// $activate_results = json_decode( $this->status( $args ), true );
			// print_r($activate_results); exit;
			$options = ( $input == 'on' ? 'on' : 'off' );
			if ( $options == 'on' && $activation_status == 'Activated' && $this->ame_options[ $this->ame_api_key ] != '' && $this->ame_options[ $this->ame_activation_email ] != '' ) {
				// deactivates API Key key activation
				$activate_results = json_decode( $this->deactivate( $args ), true );
				// Used to display results for development
				//print_r($activate_results); exit();
				if ( $activate_results[ 'deactivated' ] === true ) {
					$update        = array(
						$this->ame_api_key          => '',
						$this->ame_activation_email => ''
					);
					$merge_options = array_merge( $this->ame_options, $update );
					if ( ! empty( $this->ame_activated_key ) ) {
						update_option( $this->ame_data_key, $merge_options );
						update_option( $this->ame_activated_key, 'Deactivated' );
						add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg', __( 'API Key deactivated. ', $this->text_domain ) . "{$activate_results['activations_remaining']}.", 'updated' );
					}
					return $options;
				}
				if ( isset( $activate_results[ 'code' ] ) && ! empty( $this->ame_options ) && ! empty( $this->ame_activated_key ) ) {
					switch ( $activate_results[ 'code' ] ) {
						case '100':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_activation_email ] = '';
							$options[ $this->ame_api_key ]          = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
						case '101':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_api_key ]          = '';
							$options[ $this->ame_activation_email ] = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
						case '102':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_api_key ]          = '';
							$options[ $this->ame_activation_email ] = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
						case '103':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_api_key ]          = '';
							$options[ $this->ame_activation_email ] = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
						case '104':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_api_key ]          = '';
							$options[ $this->ame_activation_email ] = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
						case '105':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_api_key ]          = '';
							$options[ $this->ame_activation_email ] = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
						case '106':
							$additional_info = ! empty( $activate_results[ 'additional info' ] ) ? esc_attr( $activate_results[ 'additional info' ] ) : '';
							add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$additional_info}", 'error' );
							$options[ $this->ame_api_key ]          = '';
							$options[ $this->ame_activation_email ] = '';
							update_option( $this->ame_options[ $this->ame_activated_key ], 'Deactivated' );
							break;
					}
				}
			} else {
				return $options;
			}
			return false;
		}
		public function wc_am_deactivate_text() { }
		public function wc_am_deactivate_textarea() {
			echo '<input type="checkbox" id="' . $this->ame_deactivate_checkbox . '" name="' . $this->ame_deactivate_checkbox . '" value="on"';
			echo checked( get_option( $this->ame_deactivate_checkbox ), 'on' );
			echo '/>';
			?><span class="description"><?php _e( 'Deactivates an API Key so it can be used on another blog.', $this->text_domain ); ?></span>
			<?php
		}
		/**
		 * Builds the URL containing the API query string for activation, deactivation, and status requests.
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		public function create_software_api_url( $args ) {
			return add_query_arg( 'wc-api', 'am-software-api', $this->api_url ) . '&' . http_build_query( $args );
		}
		/**
		 * Sends the request to activate to the API Manager.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function activate( $args ) {
			$defaults = array(
				'request'          => 'activation',
				'product_id'       => $this->ame_product_id,
				'instance'         => $this->ame_instance_id,
				'platform'         => $this->ame_domain,
				'software_version' => $this->ame_software_version
			);
			$args       = wp_parse_args( $defaults, $args );
			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$request    = wp_safe_remote_get( $target_url );
			// $request = wp_remote_post( $this->api_url . 'wc-api/am-software-api/', array( 'body' => $args ) );
			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}
			$response = wp_remote_retrieve_body( $request );
			return $response;
		}
		/**
		 * Sends the request to deactivate to the API Manager.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function deactivate( $args ) {
			$defaults = array(
				'request'    => 'deactivation',
				'product_id' => $this->ame_product_id,
				'instance'   => $this->ame_instance_id,
				'platform'   => $this->ame_domain
			);
			$args       = wp_parse_args( $defaults, $args );
			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$request    = wp_safe_remote_get( $target_url );
			// $request = wp_remote_post( $this->api_url . 'wc-api/am-software-api/', array( 'body' => $args ) );
			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}
			$response = wp_remote_retrieve_body( $request );
			return $response;
		}
		/**
		 * Sends the status check request to the API Manager.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function status( $args ) {
			$defaults = array(
				'request'    => 'status',
				'product_id' => $this->ame_product_id,
				'instance'   => $this->ame_instance_id,
				'platform'   => $this->ame_domain
			);
			$args       = wp_parse_args( $defaults, $args );
			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$request    = wp_safe_remote_get( $target_url );
			// $request = wp_remote_post( $this->api_url . 'wc-api/am-software-api/', array( 'body' => $args ) );
			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}
			$response = wp_remote_retrieve_body( $request );
			return $response;
		}
		/**
		 * Check for software updates.
		 */
		public function check_for_update() {
			$this->plugin_name = $this->ame_plugin_name;
			// Slug should be the same as the plugin/theme directory name
			if ( strpos( $this->plugin_name, '.php' ) !== 0 ) {
				$this->slug = dirname( $this->plugin_name );
			} else {
				$this->slug = $this->plugin_name;
			}
			/*********************************************************************
			 * The plugin and theme filters should not be active at the same time
			 *********************************************************************/
			/**
			 * More info:
			 * function set_site_transient moved from wp-includes/functions.php
			 * to wp-includes/option.php in WordPress 3.4
			 *
			 * set_site_transient() contains the pre_set_site_transient_{$transient} filter
			 * {$transient} is either update_plugins or update_themes
			 *
			 * Transient data for plugins and themes exist in the Options table:
			 * _site_transient_update_themes
			 * _site_transient_update_plugins
			 */
			// uses the flag above to determine if this is a plugin or a theme update request
			if ( $this->plugin_or_theme == 'plugin' ) {
				/**
				 * Plugin Updates
				 */
				// Check For Plugin Updates
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
				// Check For Plugin Information to display on the update details page
				add_filter( 'plugins_api', array( $this, 'request' ), 10, 3 );
			} else if ( $this->plugin_or_theme == 'theme' ) {
				/**
				 * Theme Updates
				 */
				// Check For Theme Updates
				add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_check' ) );
				// Check For Theme Information to display on the update details page
				//add_filter( 'themes_api', array( $this, 'request' ), 10, 3 );
			}
		}
		/**
		 * Builds the URL containing the API query string for software update requests.
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		private function create_upgrade_api_url( $args ) {
			return add_query_arg( 'wc-api', 'upgrade-api', $this->api_url ) . '&' . http_build_query( $args );
		}
		/**
		 * Check for updates against the remote server.
		 *
		 * @since  1.0.0
		 *
		 * @param  object $transient
		 *
		 * @return object $transient
		 */
		public function update_check( $transient ) {
			if ( empty( $transient->checked ) ) {
				return $transient;
			}
			$args = array(
				'request'          => 'pluginupdatecheck',
				'slug'             => $this->slug,
				'plugin_name'      => $this->plugin_name,
				//'version'			=>	$transient->checked[$this->plugin_name],
				'version'          => $this->ame_software_version,
				'product_id'       => $this->ame_product_id,
				'api_key'          => $this->ame_options[ $this->ame_api_key ],
				'activation_email' => $this->ame_options[ $this->ame_activation_email ],
				'instance'         => $this->ame_instance_id,
				'domain'           => $this->ame_domain,
				'software_version' => $this->ame_software_version,
				'extra'            => $this->extra,
			);
			// Check for a plugin update
			$response = $this->plugin_information( $args );
			// Displays an admin error message in the WordPress dashboard
			$this->check_response_for_errors( $response );
			// Set version variables
			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				// New plugin version from the API
				$new_ver = (string) $response->new_version;
				// Current installed plugin version
				$curr_ver = (string) $this->ame_software_version;
				//$curr_ver = (string)$transient->checked[$this->plugin_name];
			}
			// If there is a new version, modify the transient to reflect an update is available
			if ( isset( $new_ver ) && isset( $curr_ver ) ) {
				if ( $response !== false && version_compare( $new_ver, $curr_ver, '>' ) ) {
					if ( $this->plugin_or_theme == 'plugin' ) {
						$transient->response[ $this->plugin_name ] = $response;
					} else if ( $this->plugin_or_theme == 'theme' ) {
						$transient->response[ $this->plugin_name ][ 'new_version' ] = $response->new_version;
						$transient->response[ $this->plugin_name ][ 'url' ]         = $response->url;
						$transient->response[ $this->plugin_name ][ 'package' ]     = $response->package;
					}
				}
			}
			return $transient;
		}
		/**
		 * Sends and receives data to and from the server API
		 *
		 * @since  1.0.0
		 *
		 * @param array $args
		 *
		 * @return object $response
		 */
		public function plugin_information( $args ) {
			$target_url = esc_url_raw( $this->create_upgrade_api_url( $args ) );
			$request    = wp_safe_remote_get( $target_url );
			//$request = wp_remote_post( $this->api_url . 'wc-api/upgrade-api/', array( 'body' => $args ) );
			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				return false;
			}
			$response = unserialize( wp_remote_retrieve_body( $request ) );
			/**
			 * For debugging errors from the API
			 * For errors like: unserialize(): Error at offset 0 of 170 bytes
			 * Comment out $response above first
			 */
			// $response = wp_remote_retrieve_body( $request );
			// print_r($response); exit;
			if ( is_object( $response ) ) {
				return $response;
			} else {
				return false;
			}
		}
		/**
		 * API request for informatin.
		 *
		 * If `$action` is 'query_plugins' or 'plugin_information', an object MUST be passed.
		 * If `$action` is 'hot_tags` or 'hot_categories', an array should be passed.
		 *
		 * @param false|object|array $result The result object or array. Default false.
		 * @param string             $action The type of information being requested from the Plugin Install API.
		 * @param object             $args
		 *
		 * @return object
		 */
		public function request( $result, $action, $args ) {
			// Is this a plugin or a theme?
			//if ( $this->plugin_or_theme == 'plugin' ) {
			//	$version = get_site_transient( 'update_plugins' );
			//} else if ( $this->plugin_or_theme == 'theme' ) {
			//
			//	$version = get_site_transient( 'update_themes' );
			//}
			// Check if this plugins API is about this plugin
			if ( isset( $args->slug ) ) {
				if ( $args->slug != $this->slug ) {
					return $result;
				}
			} else {
				return $result;
			}
			$args = array(
				'request'          => 'plugininformation',
				'plugin_name'      => $this->plugin_name,
				//'version'			=>	$version->checked[$this->plugin_name],
				'version'          => $this->ame_software_version,
				'product_id'       => $this->ame_product_id,
				'api_key'          => $this->ame_options[ $this->ame_api_key ],
				'activation_email' => $this->ame_options[ $this->ame_activation_email ],
				'instance'         => $this->ame_instance_id,
				'domain'           => $this->ame_domain,
				'software_version' => $this->ame_software_version,
				'extra'            => $this->extra,
			);
			$response = $this->plugin_information( $args );
			// If everything is okay return the $response
			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				return $response;
			}
			return $result;
		}
		/**
		 * Displays an admin error message in the WordPress dashboard
		 *
		 * @param  object $response
		 *
		 * @return string
		 */
		public function check_response_for_errors( $response ) {
			if ( ! empty( $response ) && is_object( $response ) ) {
				if ( isset( $response->errors[ 'no_key' ] ) && $response->errors[ 'no_key' ] == 'no_key' && isset( $response->errors[ 'no_subscription' ] ) && $response->errors[ 'no_subscription' ] == 'no_subscription' ) {
					add_action( 'admin_notices', array( $this, 'no_key_error_notice' ) );
					add_action( 'admin_notices', array( $this, 'no_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'exp_license' ] ) && $response->errors[ 'exp_license' ] == 'exp_license' ) {
					add_action( 'admin_notices', array( $this, 'expired_license_error_notice' ) );
				} else if ( isset( $response->errors[ 'hold_subscription' ] ) && $response->errors[ 'hold_subscription' ] == 'hold_subscription' ) {
					add_action( 'admin_notices', array( $this, 'on_hold_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'cancelled_subscription' ] ) && $response->errors[ 'cancelled_subscription' ] == 'cancelled_subscription' ) {
					add_action( 'admin_notices', array( $this, 'cancelled_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'exp_subscription' ] ) && $response->errors[ 'exp_subscription' ] == 'exp_subscription' ) {
					add_action( 'admin_notices', array( $this, 'expired_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'suspended_subscription' ] ) && $response->errors[ 'suspended_subscription' ] == 'suspended_subscription' ) {
					add_action( 'admin_notices', array( $this, 'suspended_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'pending_subscription' ] ) && $response->errors[ 'pending_subscription' ] == 'pending_subscription' ) {
					add_action( 'admin_notices', array( $this, 'pending_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'trash_subscription' ] ) && $response->errors[ 'trash_subscription' ] == 'trash_subscription' ) {
					add_action( 'admin_notices', array( $this, 'trash_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'no_subscription' ] ) && $response->errors[ 'no_subscription' ] == 'no_subscription' ) {
					add_action( 'admin_notices', array( $this, 'no_subscription_error_notice' ) );
				} else if ( isset( $response->errors[ 'no_activation' ] ) && $response->errors[ 'no_activation' ] == 'no_activation' ) {
					add_action( 'admin_notices', array( $this, 'no_activation_error_notice' ) );
				} else if ( isset( $response->errors[ 'no_key' ] ) && $response->errors[ 'no_key' ] == 'no_key' ) {
					add_action( 'admin_notices', array( $this, 'no_key_error_notice' ) );
				} else if ( isset( $response->errors[ 'download_revoked' ] ) && $response->errors[ 'download_revoked' ] == 'download_revoked' ) {
					add_action( 'admin_notices', array( $this, 'download_revoked_error_notice' ) );
				} else if ( isset( $response->errors[ 'switched_subscription' ] ) && $response->errors[ 'switched_subscription' ] == 'switched_subscription' ) {
					add_action( 'admin_notices', array( $this, 'switched_subscription_error_notice' ) );
				}
			}
		}
		/**
		 * Display license expired error notice
		 */
		public function expired_license_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The license key for %s has expired. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription on-hold error notice
		 */
		public function on_hold_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The subscription for %s is on-hold. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription cancelled error notice
		 */
		public function cancelled_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The subscription for %s has been cancelled. You can renew the subscription from your account <a href="%s" target="_blank">dashboard</a>. A new license key will be emailed to you after your order has been completed.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription expired error notice
		 */
		public function expired_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The subscription for %s has expired. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription expired error notice
		 */
		public function suspended_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The subscription for %s has been suspended. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription expired error notice
		 */
		public function pending_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The subscription for %s is still pending. You can check on the status of the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription expired error notice
		 */
		public function trash_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'The subscription for %s has been placed in the trash and will be deleted soon. You can purchase a new subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display subscription expired error notice
		 */
		public function no_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'A subscription for %s could not be found. You can purchase a subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display missing key error notice
		 */
		public function no_key_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'A license key for %s could not be found. Maybe you forgot to enter a license key when setting up %s, or the key was deactivated in your account. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display missing download permission revoked error notice
		 */
		public function download_revoked_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'Download permission for %s has been revoked possibly due to a license key or subscription expiring. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
		/**
		 * Display no activation error notice
		 */
		public function no_activation_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( '%s has not been activated. Go to the settings page and enter the license key and license email to activate %s.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_attr( $this->ame_software_product_id ) );
		}
		/**
		 * Display switched activation error notice
		 */
		public function switched_subscription_error_notice() {
			echo sprintf( '<div class="notice notice-info"><p>' . __( 'You changed the subscription for %s, so you will need to enter your new API License Key in the settings page. The License Key should have arrived in your email inbox, if not you can get it by logging into your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', esc_attr( $this->ame_software_product_id ), esc_url( $this->ame_renew_license_url ) );
		}
	}
}