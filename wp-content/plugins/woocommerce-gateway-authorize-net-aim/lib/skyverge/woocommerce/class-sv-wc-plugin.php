<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Plugin/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Plugin' ) ) :

/**
 * # WooCommerce Plugin Framework
 *
 * This framework class provides a base level of configurable and overrideable
 * functionality and features suitable for the implementation of a WooCommerce
 * plugin.  This class handles all the "non-feature" support tasks such
 * as verifying dependencies are met, loading the text domain, etc.
 *
 * @version 4.9.0
 */
abstract class SV_WC_Plugin {


	/** Plugin Framework Version */
	const VERSION = '4.9.0';

	/** @var object single instance of plugin */
	protected static $instance;

	/** @var string plugin id */
	private $id;

	/** @var string version number */
	private $version;

	/** @var string plugin path without trailing slash */
	private $plugin_path;

	/** @var string plugin uri */
	private $plugin_url;

	/** @var \WC_Logger instance */
	private $logger;

	/** @var  \SV_WP_Admin_Message_Handler instance */
	private $message_handler;

	/** @var array string names of required PHP extensions */
	private $dependencies = array();

	/** @var array string names of required PHP functions */
	private $function_dependencies = array();

	/** @var string the plugin text domain */
	private $text_domain;

	/** @var SV_WC_Admin_Notice_Handler the admin notice handler class */
	private $admin_notice_handler;

	/** @var bool whether a PHP upgrade notice should be displayed in the admin */
	private $display_php_notice = false;


	/**
	 * Initialize the plugin.
	 *
	 * Child plugin classes may add their own optional arguments.
	 *
	 * @since 2.0.0
	 * @param string $id plugin id
	 * @param string $version plugin version number
	 * @param array $args {
	 *     optional plugin arguments
	 *
	 *     @type string $text_domain the plugin textdomain, used to set up translations
	 *     @type array  $dependencies {
	 *          the plugin's PHP dependencies
	 *
	 *          $type array $extensions the required PHP extensions
	 *          $type array $functions  the required PHP functions
	 *          $type array $settings   the required PHP settings, as `$setting => $value`
	 *                                  String values are treated with direct
	 *                                  comparison, integers as minimums
	 *     }
	 * }
	 */
	public function __construct( $id, $version, $args = array() ) {

		// required params
		$this->id          = $id;
		$this->version     = $version;

		$dependencies = isset( $args['dependencies'] ) ? $args['dependencies'] : array();

		// for backwards compatibility
		if ( empty( $dependencies['functions'] ) && ! empty( $args['function_dependencies'] ) ) {
			$dependencies['functions'] = $args['function_dependencies'];
		}

		$this->set_dependencies( $dependencies );

		if ( isset( $args['text_domain'] ) ) {
			$this->text_domain = $args['text_domain'];
		}

		if ( isset( $args['display_php_notice'] ) ) {
			$this->display_php_notice = $args['display_php_notice'];
		}

		// include library files after woocommerce is loaded
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'lib_includes' ) );

		// includes that are required to be available at all times
		$this->includes();

		$this->load_hook_deprecator();

		// Admin
		if ( is_admin() && ! is_ajax() ) {

			// admin message handler
			require_once( $this->get_framework_path() . '/class-sv-wp-admin-message-handler.php' );

			// render any admin notices, delayed notices, and
			add_action( 'admin_notices', array( $this, 'add_admin_notices'            ), 10 );
			add_action( 'admin_footer',  array( $this, 'add_delayed_admin_notices'    ), 10 );

			// add a 'Configure' link to the plugin action links
			add_filter( 'plugin_action_links_' . plugin_basename( $this->get_file() ), array( $this, 'plugin_action_links' ) );

			// defer until WP/WC has fully loaded
			add_action( 'wp_loaded', array( $this, 'do_install' ) );

			// register activation/deactivation hooks for convenience
			register_activation_hook(   $this->get_file(), array( $this, 'activate' ) );
			register_deactivation_hook( $this->get_file(), array( $this, 'deactivate' ) );
		}

		// automatically log HTTP requests from SV_WC_API_Base
		$this->add_api_request_logging();

		// Load translation files
		add_action( 'init', array( $this, 'load_translations' ) );

		// add any PHP incompatibilities to the system status report
		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			add_filter( 'woocommerce_system_status_environment_rows', array( $this, 'add_system_status_php_information' ) );
		} else {
			add_filter( 'woocommerce_debug_posting', array( $this, 'add_system_status_php_information' ) );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 3.1.0
	 */
	public function __clone() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'woocommerce-plugin-framework' ), $this->get_plugin_name() ), '3.1.0' );
	}

	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 3.1.0
	 */
	public function __wakeup() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'woocommerce-plugin-framework' ), $this->get_plugin_name() ), '3.1.0' );
	}


	/**
	 * Load plugin & framework text domains
	 *
	 * @since 4.2.0
	 */
	public function load_translations() {

		$this->load_framework_textdomain();

		// if this plugin passes along its text domain, load its translation files
		if ( $this->text_domain ) {

			$this->load_plugin_textdomain();

		// otherwise, use the backwards compatibile method
		} elseif ( is_callable( array( $this, 'load_translation' ) ) ) {

			$this->load_translation();
		}
	}


	/**
	 * Loads the framework textdomain.
	 *
	 * @since 4.5.0
	 */
	protected function load_framework_textdomain() {
		$this->load_textdomain( 'woocommerce-plugin-framework', dirname( plugin_basename( $this->get_framework_file() ) ) );
	}


	/**
	 * Loads the plugin textdomain.
	 *
	 * @since 4.5.0
	 */
	protected function load_plugin_textdomain() {
		$this->load_textdomain( $this->text_domain, dirname( plugin_basename( $this->get_file() ) ) );
	}


	/**
	 * Loads the plugin textdomain.
	 *
	 * @since 4.5.0
	 * @param string $textdomain the plugin textdomain
	 * @param string $path the i18n path
	 */
	protected function load_textdomain( $textdomain, $path ) {

		// user's locale if in the admin for WP 4.7+, or the site locale otherwise
		$locale = is_admin() && is_callable( 'get_user_locale' ) ? get_user_locale() : get_locale();

		$locale = apply_filters( 'plugin_locale', $locale, $textdomain );

		load_textdomain( $textdomain, WP_LANG_DIR . '/' . $textdomain . '/' . $textdomain . '-' . $locale . '.mo' );

		load_plugin_textdomain( $textdomain, false, untrailingslashit( $path ) . '/i18n/languages' );
	}


	/**
	 * Include required library files
	 *
	 * @since 2.0.0
	 */
	public function lib_includes() {

		if ( is_admin() ) {
			// instantiate the admin notice handler
			$this->get_admin_notice_handler();
		}
	}


	/**
	 * Include any critical files which must be available as early as possible
	 *
	 * @since 2.0.0
	 */
	private function includes() {

		$framework_path = $this->get_framework_path();

		// common exception class
		require_once(  $framework_path . '/class-sv-wc-plugin-exception.php' );

		// common utility methods
		require_once( $framework_path . '/class-sv-wc-helper.php' );

		// backwards compatibility for older WC versions
		require_once( $framework_path . '/class-sv-wc-plugin-compatibility.php' );
		require_once( $framework_path . '/compatibility/abstract-sv-wc-data-compatibility.php' );
		require_once( $framework_path . '/compatibility/class-sv-wc-order-compatibility.php' );
		require_once( $framework_path . '/compatibility/class-sv-wc-product-compatibility.php' );

		// TODO: Remove this when WC 3.x can be required {CW 2017-03-16}
		require_once( $framework_path . '/compatibility/class-sv-wc-datetime.php' );

		// generic API base
		require_once( $framework_path . '/api/class-sv-wc-api-exception.php' );
		require_once( $framework_path . '/api/class-sv-wc-api-base.php' );
		require_once( $framework_path . '/api/interface-sv-wc-api-request.php' );
		require_once( $framework_path . '/api/interface-sv-wc-api-response.php' );

		// XML API base
		require_once( $framework_path . '/api/abstract-sv-wc-api-xml-request.php' );
		require_once( $framework_path . '/api/abstract-sv-wc-api-xml-response.php' );

		// JSON API base
		require_once( $framework_path . '/api/abstract-sv-wc-api-json-request.php' );
		require_once( $framework_path . '/api/abstract-sv-wc-api-json-response.php' );
	}


	/**
	 * Load and instantiate the hook deprecator class
	 *
	 * @since 4.3.0
	 */
	private function load_hook_deprecator() {

		require_once( $this->get_framework_path() . '/class-sv-wc-hook-deprecator.php' );

		$this->hook_deprecator = new SV_WC_Hook_Deprecator( $this->get_plugin_name(), $this->get_deprecated_hooks() );
	}


	/**
	 * Return deprecated/removed hooks. Implementing classes should override this
	 * and return an array of deprecated/removed hooks in the following format:
	 *
	 * $old_hook_name = array {
	 *   @type string $version version the hook was deprecated/removed in
	 *   @type bool $removed if present and true, the message will indicate the hook was removed instead of deprecated
	 *   @type string|bool $replacement if present and a string, the message will indicate the replacement hook to use,
	 *     otherwise (if bool and false) the message will indicate there is no replacement available.
	 * }
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		// stub method
		return array();
	}


	/** Admin methods ******************************************************/


	/**
	 * Returns true if on the admin plugin settings page, if any
	 *
	 * @since 2.0.0
	 * @return boolean true if on the admin plugin settings page
	 */
	public function is_plugin_settings() {
		// optional method, not all plugins *have* a settings page
		return false;
	}


	/**
	 * Checks if required PHP extensions are loaded and adds an admin notice
	 * for any missing extensions.  Also plugin settings can be checked
	 * as well.
	 *
	 * @since 3.0.0
	 */
	public function add_admin_notices() {

		// notices for any missing dependencies
		$this->add_dependencies_admin_notices();
	}


	/**
	 * Convenience method to add delayed admin notices, which may depend upon
	 * some setting being saved prior to determining whether to render
	 *
	 * @since 3.0.0
	 */
	public function add_delayed_admin_notices() {
		// stub method
	}


	/**
	 * Checks if required PHP extensions are not loaded and adds a dismissible admin
	 * notice if so.  Notice will not be rendered to the admin user once dismissed
	 * unless on the plugin settings page, if any
	 *
	 * @since 3.0.0
	 */
	protected function add_dependencies_admin_notices() {
		global $sv_wc_php_notice_added;

		// report any missing extensions
		$missing_extensions = $this->get_missing_extension_dependencies();

		if ( count( $missing_extensions ) > 0 ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - plugin name, %2$s - a PHP extension/comma-separated list of PHP extensions */
				_n(
					'%1$s requires the %2$s PHP extension to function. Contact your host or server administrator to install and configure the missing extension.',
					'%1$s requires the following PHP extensions to function: %2$s. Contact your host or server administrator to install and configure the missing extensions.',
					count( $missing_extensions ),
					'woocommerce-plugin-framework'
				),
				$this->get_plugin_name(),
				'<strong>' . implode( ', ', $missing_extensions ) . '</strong>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'missing-extensions', array(
				'notice_class' => 'error',
			) );

		}

		// report any missing functions
		$missing_functions = $this->get_missing_function_dependencies();

		if ( count( $missing_functions ) > 0 ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - plugin name, %2$s - a PHP function/comma-separated list of PHP functions */
				_n(
					'%1$s requires the %2$s PHP function to exist.  Contact your host or server administrator to install and configure the missing function.',
					'%1$s requires the following PHP functions to exist: %2$s.  Contact your host or server administrator to install and configure the missing functions.',
					count( $missing_functions ),
					'woocommerce-plugin-framework'
				),
				$this->get_plugin_name(),
				'<strong>' . implode( ', ', $missing_functions ) . '</strong>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'missing-functions', array(
				'notice_class' => 'error',
			) );

		}

		// if on the settings page, report any incompatible PHP settings
		if ( $this->is_plugin_settings() || ( ! $this->get_settings_url() && $this->is_general_configuration_page() ) ) {

			$bad_settings = $this->get_incompatible_php_settings();

			if ( count( $bad_settings ) > 0 ) {

				$message = sprintf(
					/* translators: Placeholders: %s - plugin name */
					__( '%s may behave unexpectedly because the following PHP configuration settings are required:' ),
					'<strong>' . $this->get_plugin_name() . '</strong>'
				);

				$message .= '<ul>';

					foreach ( $bad_settings as $setting => $values ) {

						$setting_message = '<code>' . $setting . ' = ' . $values['expected'] . '</code>';

						if ( ! empty( $values['type'] ) && 'min' === $values['type'] ) {

							$setting_message = sprintf(
								/** translators: Placeholders: %s - a PHP setting value */
								__( '%s or higher', 'woocommerce-plugin-framework' ),
								$setting_message
							);
						}

						$message .= '<li>' . $setting_message . '</li>';
					}

				$message .= '</ul>';

				$message .= __( 'Please contact your hosting provider or server administrator to configure these settings.', 'woocommerce-plugin-framework' );

				$this->get_admin_notice_handler()->add_admin_notice( $message, 'bad-php-configuration', array(
					'notice_class' => 'error',
				) );
			}
		}

		// add the PHP 5.6+ notice
		if ( ! $sv_wc_php_notice_added && $this->display_php_notice && version_compare( PHP_VERSION, '5.6.0', '<' ) ) {

			$message = '<p>';

			$message .= sprintf(
				/* translators: Placeholders: %1$s - <strong>, %2$s - </strong> */
				__( 'Hey there! We\'ve noticed that your server is running %1$san outdated version of PHP%2$s, which is the programming language that WooCommerce and its extensions are built on.
					The PHP version that is currently used for your site is no longer maintained, nor %1$sreceives security updates%2$s; newer versions are faster and more secure.', 'woocommerce-plugin-framework' ),
				'<strong>', '</strong>'
			);

			$message .= '</p><p>';

			$deadline = strtotime( 'May 2018' );

			if ( time() < $deadline ) {

				$message .= sprintf(
					/* translators: Placeholders: %1$s - WooCommerce plugin name, %2$s - a month and year, such as May 2018 */
					__( 'As a result, %1$s will no longer support this version starting %2$s and you should upgrade PHP prior to this date.', 'woocommerce-plugin-framework' ),
					$this->get_plugin_name(),
					'<strong>' . date_i18n( 'F Y', $deadline ) . '</strong>'
				);

			} else {

				$message .= sprintf(
					/* translators: Placeholders: %s - WooCommerce plugin name */
					__( 'As a result, %s no longer supports this version and you should upgrade PHP as soon as possible.', 'woocommerce-plugin-framework' ),
					$this->get_plugin_name()
				);
			}

			$message .= ' ' . sprintf(
				/* translators: Placeholders: %1$s - <a>, %2$s - </a> */
				__( 'Your hosting provider can do this for you. %1$sHere are some resources to help you upgrade%2$s and to explain PHP versions further.', 'woocommerce-plugin-framework' ),
				'<a href="http://skyver.ge/upgradephp">', '</a>'
			);

			$message .= '</p>';

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'sv-wc-outdated-php-version', array(
				'notice_class' => 'error',
			) );

			$sv_wc_php_notice_added = true;
		}
	}


	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 2.0.0
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function plugin_action_links( $actions ) {

		$custom_actions = array();

		// settings url(s)
		if ( $this->get_settings_link( $this->get_id() ) ) {
			$custom_actions['configure'] = $this->get_settings_link( $this->get_id() );
		}

		// documentation url if any
		if ( $this->get_documentation_url() ) {
			/* translators: Docs as in Documentation */
			$custom_actions['docs'] = sprintf( '<a href="%s" target="_blank">%s</a>', $this->get_documentation_url(), esc_html__( 'Docs', 'woocommerce-plugin-framework' ) );
		}

		// support url if any
		if ( $this->get_support_url() ) {
			$custom_actions['support'] = sprintf( '<a href="%s">%s</a>', $this->get_support_url(), esc_html_x( 'Support', 'noun', 'woocommerce-plugin-framework' ) );
		}

		// add the links to the front of the actions list
		return array_merge( $custom_actions, $actions );
	}


	/** Helper methods ******************************************************/


	/**
	 * Automatically log API requests/responses when using SV_WC_API_Base
	 *
	 * @since 2.2.0
	 * @see SV_WC_API_Base::broadcast_request()
	 */
	public function add_api_request_logging() {

		if ( ! has_action( 'wc_' . $this->get_id() . '_api_request_performed' ) ) {
			add_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10, 2 );
		}
	}


	/**
	 * Log API requests/responses
	 *
	 * @since 2.2.0
	 * @param array $request request data, see SV_WC_API_Base::broadcast_request() for format
	 * @param array $response response data
	 * @param string|null $log_id log to write data to
	 */
	public function log_api_request( $request, $response, $log_id = null ) {

		$this->log( "Request\n" . $this->get_api_log_message( $request ), $log_id );

		if ( ! empty( $response ) ) {
			$this->log( "Response\n" . $this->get_api_log_message( $response ), $log_id );
		}
	}


	/**
	 * Transform the API request/response data into a string suitable for logging
	 *
	 * @since 2.2.0
	 * @param array $data
	 * @return string
	 */
	public function get_api_log_message( $data ) {

		$messages = array();

		$messages[] = isset( $data['uri'] ) && $data['uri'] ? 'Request' : 'Response';

		foreach ( (array) $data as $key => $value ) {
			$messages[] = trim( sprintf( '%s: %s', $key, is_array( $value ) || ( is_object( $value ) && 'stdClass' == get_class( $value ) ) ? print_r( (array) $value, true ) : $value ) );
		}

		return implode( "\n", $messages ) . "\n";
	}


	/**
	 * Gets the string name of any required PHP extensions that are not loaded.
	 *
	 * @deprecated since 4.5.0
	 *
	 * @since 2.0.0
	 * @return array of missing dependencies
	 */
	public function get_missing_dependencies() {

		return $this->get_missing_extension_dependencies();
	}


	/**
	 * Gets the string name of any required PHP extensions that are not loaded
	 *
	 * @since 4.5.0
	 * @return array of missing dependencies
	 */
	public function get_missing_extension_dependencies() {

		$missing_extensions = array();

		foreach ( $this->get_extension_dependencies() as $ext ) {

			if ( ! extension_loaded( $ext ) ) {
				$missing_extensions[] = $ext;
			}
		}

		return $missing_extensions;
	}


	/**
	 * Gets the string name of any required PHP functions that are not loaded
	 *
	 * @since 2.1.0
	 * @return array of missing functions
	 */
	public function get_missing_function_dependencies() {

		$missing_functions = array();

		foreach ( $this->get_function_dependencies() as $fcn ) {

			if ( ! function_exists( $fcn ) ) {
				$missing_functions[] = $fcn;
			}
		}

		return $missing_functions;
	}


	/**
	 * Gets the string name of any required PHP extensions that are not loaded
	 *
	 * @since 4.5.0
	 * @return array of missing dependencies
	 */
	public function get_incompatible_php_settings() {

		$incompatible_settings = array();

		$dependences = $this->get_php_settings_dependencies();

		if ( function_exists( 'ini_get' ) && ! empty( $dependences ) ) {

			foreach ( $dependences as $setting => $expected ) {

				$actual = ini_get( $setting );

				if ( ! $actual ) {
					continue;
				}

				if ( is_integer( $expected ) ) {

					// determine if this is a size string, like "10MB"
					$is_size = ! is_numeric( substr( $actual, -1 ) );

					$actual_num = $is_size ? wc_let_to_num( $actual ) : $actual;

					if ( $actual_num < $expected ) {

						$incompatible_settings[ $setting ] = array(
							'expected' => $is_size ? size_format( $expected ) : $expected,
							'actual'   => $is_size ? size_format( $actual_num ) : $actual,
							'type'     => 'min',
						);
					}

				} elseif ( $actual !== $expected ) {

					$incompatible_settings[ $setting ] = array(
						'expected' => $expected,
						'actual'   => $actual,
					);
				}
			}
		}

		return $incompatible_settings;
	}


	/**
	 * Adds any PHP incompatibilities to the system status report.
	 *
	 * @since 4.5.0
	 */
	public function add_system_status_php_information( $rows ) {

		foreach ( $this->get_incompatible_php_settings() as $setting => $values ) {

			if ( isset( $values['type'] ) && 'min' === $values['type'] ) {

				// if this setting already has a higher minimum from another plugin, skip it
				if ( isset( $rows[ $setting ]['expected'] ) && $values['expected'] < $rows[ $setting ]['expected'] ) {
					continue;
				}

				$note = __( '%1$s - A minimum of %2$s is required.', 'woocommerce-plugin-framework' );

			} else {

				// if this requirement is already listed, skip it
				if ( isset( $rows[ $setting ] ) ) {
					continue;
				}

				$note = __( 'Set as %1$s - %2$s is required.', 'woocommerce-plugin-framework' );
			}

			$note = sprintf( $note, $values['actual'], $values['expected'] );

			$rows[ $setting ] = array(
				'name'     => $setting,
				'note'     => $note,
				'success'  => false,
				'expected' => $values['expected'], // WC doesn't use this, but it's useful for us
			);
		}

		return $rows;
	}


	/**
	 * Sets the plugin dependencies.
	 *
	 * @since 4.5.0
	 * @param array $dependencies the environment dependencies
	 */
	protected function set_dependencies( $dependencies = array() ) {

		$default_dependencies = array(
			'extensions' => array(),
			'functions'  => array(),
			'settings'   => array(
				'suhosin.post.max_array_index_length'    => 256,
				'suhosin.post.max_totalname_length'      => 65535,
				'suhosin.post.max_vars'                  => 1024,
				'suhosin.request.max_array_index_length' => 256,
				'suhosin.request.max_totalname_length'   => 65535,
				'suhosin.request.max_vars'               => 1024,
			),
		);

		if ( isset( $dependencies[0] ) ) {

			$dependencies = array(
				'extensions' => $dependencies,
			);
		}

		// override any default settings requirements if the plugin specifies them
		if ( ! empty( $dependencies['settings'] ) ) {
			$dependencies['settings'] = array_merge( $default_dependencies['settings'], $dependencies['settings'] );
		}

		$this->dependencies = wp_parse_args( $dependencies, $default_dependencies );
	}


	/**
	 * Saves errors or messages to WooCommerce Log (woocommerce/logs/plugin-id-xxx.txt)
	 *
	 * @since 2.0.0
	 * @param string $message error or message to save to log
	 * @param string $log_id optional log id to segment the files by, defaults to plugin id
	 */
	public function log( $message, $log_id = null ) {

		if ( is_null( $log_id ) ) {
			$log_id = $this->get_id();
		}

		if ( ! is_object( $this->logger ) ) {
			$this->logger = new WC_Logger();
		}

		$this->logger->add( $log_id, $message );
	}


	/**
	 * Require and instantiate a class
	 *
	 * @since 4.2.0
	 * @param string $local_path path to class file in plugin, e.g. '/includes/class-wc-foo.php'
	 * @param string $class_name class to instantiate
	 * @return object instantiated class instance
	 */
	public function load_class( $local_path, $class_name ) {

		require_once( $this->get_plugin_path() . $local_path );

		return new $class_name;
	}


	/** Getter methods ******************************************************/


	/**
	 * The implementation for this abstract method should simply be:
	 *
	 * return __FILE__;
	 *
	 * @since 2.0.0
	 * @return string the full path and filename of the plugin file
	 */
	abstract protected function get_file();


	/**
	 * Returns the plugin id
	 *
	 * @since 2.0.0
	 * @return string plugin id
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Returns the plugin id with dashes in place of underscores, and
	 * appropriate for use in frontend element names, classes and ids
	 *
	 * @since 2.0.0
	 * @return string plugin id with dashes in place of underscores
	 */
	public function get_id_dasherized() {
		return str_replace( '_', '-', $this->get_id() );
	}


	/**
	 * Returns the plugin full name including "WooCommerce", ie
	 * "WooCommerce X".  This method is defined abstract for localization purposes
	 *
	 * @since 2.0.0
	 * @return string plugin name
	 */
	abstract public function get_plugin_name();


	/**
	 * Returns the admin notice handler instance
	 *
	 * @since 3.0.0
	 */
	public function get_admin_notice_handler() {

		if ( ! is_null( $this->admin_notice_handler ) ) {
			return $this->admin_notice_handler;
		}

		require_once( $this->get_framework_path() . '/class-sv-wc-admin-notice-handler.php' );

		return $this->admin_notice_handler = new SV_WC_Admin_Notice_Handler( $this );
	}


	/**
	 * Returns the plugin version name.  Defaults to wc_{plugin id}_version
	 *
	 * @since 2.0.0
	 * @return string the plugin version name
	 */
	protected function get_plugin_version_name() {
		return 'wc_' . $this->get_id() . '_version';
	}


	/**
	 * Returns the current version of the plugin
	 *
	 * @since 2.0.0
	 * @return string plugin version
	 */
	public function get_version() {
		return $this->version;
	}


	/**
	 * Get the PHP dependencies.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_dependencies() {
		return $this->dependencies;
	}


	/**
	 * Get the PHP extension dependencies.
	 *
	 * @since 4.5.0
	 * @return array
	 */
	protected function get_extension_dependencies() {
		return $this->dependencies['extensions'];
	}


	/**
	 * Get the PHP function dependencies.
	 *
	 * @since 2.1.0
	 * @return array
	 */
	protected function get_function_dependencies() {
		return $this->dependencies['functions'];
	}


	/**
	 * Get the PHP settings dependencies.
	 *
	 * @since 4.5.0
	 * @return array
	 */
	protected function get_php_settings_dependencies() {
		return $this->dependencies['settings'];
	}


	/**
	 * Returns the "Configure" plugin action link to go directly to the plugin
	 * settings page (if any)
	 *
	 * @since 2.0.0
	 * @see SV_WC_Plugin::get_settings_url()
	 * @param string $plugin_id optional plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin configure link
	 */
	public function get_settings_link( $plugin_id = null ) {

		$settings_url = $this->get_settings_url( $plugin_id );

		if ( $settings_url ) {
			return sprintf( '<a href="%s">%s</a>', $settings_url, esc_html__( 'Configure', 'woocommerce-plugin-framework' ) );
		}

		// no settings
		return '';
	}


	/**
	 * Gets the plugin configuration URL
	 *
	 * @since 2.0.0
	 * @see SV_WC_Plugin::get_settings_link()
	 * @param string $plugin_id optional plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {

		// stub method
		return '';
	}


	/**
	 * Returns true if the current page is the admin general configuration page
	 *
	 * @since 3.0.0
	 * @return boolean true if the current page is the admin general configuration page
	 */
	public function is_general_configuration_page() {

		return isset( $_GET['page'] ) && 'wc-settings' == $_GET['page'] && ( ! isset( $_GET['tab'] ) || 'general' == $_GET['tab'] );
	}


	/**
	 * Returns the admin configuration url for the admin general configuration page
	 *
	 * @since 3.0.0
	 * @return string admin configuration url for the admin general configuration page
	 */
	public function get_general_configuration_url() {

		return admin_url( 'admin.php?page=wc-settings&tab=general' );
	}


	/**
	 * Gets the plugin documentation url, used for the 'Docs' plugin action
	 *
	 * @since 2.0.0
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return null;
	}


	/**
	 * Gets the support URL, used for the 'Support' plugin action link
	 *
	 * @since 4.0.0
	 * @return string support url
	 */
	public function get_support_url() {

		return null;
	}


	/**
	 * Returns the plugin's path without a trailing slash, i.e.
	 * /path/to/wp-content/plugins/plugin-directory
	 *
	 * @since 2.0.0
	 * @return string the plugin path
	 */
	public function get_plugin_path() {

		if ( $this->plugin_path ) {
			return $this->plugin_path;
		}

		return $this->plugin_path = untrailingslashit( plugin_dir_path( $this->get_file() ) );
	}


	/**
	 * Returns the plugin's url without a trailing slash, i.e.
	 * http://skyverge.com/wp-content/plugins/plugin-directory
	 *
	 * @since 2.0.0
	 * @return string the plugin URL
	 */
	public function get_plugin_url() {

		if ( $this->plugin_url ) {
			return $this->plugin_url;
		}

		return $this->plugin_url = untrailingslashit( plugins_url( '/', $this->get_file() ) );
	}


	/**
	 * Returns the woocommerce uploads path, without trailing slash.  Oddly WooCommerce
	 * core does not provide a way to get this
	 *
	 * @since 2.0.0
	 * @return string upload path for woocommerce
	 */
	public static function get_woocommerce_uploads_path() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/woocommerce_uploads';
	}


	/**
	 * Returns the loaded framework __FILE__
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_framework_file() {

		return __FILE__;
	}


	/**
	 * Returns the loaded framework path, without trailing slash. Ths is the highest
	 * version framework that was loaded by the bootstrap.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_framework_path() {

		return untrailingslashit( plugin_dir_path( $this->get_framework_file() ) );
	}


	/**
	 * Returns the absolute path to the loaded framework image directory, without a
	 * trailing slash
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_framework_assets_path() {

		return $this->get_framework_path() . '/assets';
	}


	/**
	 * Returns the loaded framework assets URL without a trailing slash
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_framework_assets_url() {

		return untrailingslashit( plugins_url( '/assets', $this->get_framework_file() ) );
	}


	/**
	 * Returns the WP Admin Message Handler instance for use with
	 * setting/displaying admin messages & errors
	 *
	 * @since 2.0.0
	 * @return SV_WP_Admin_Message_Handler
	 */
	public function get_message_handler() {

		if ( is_object( $this->message_handler ) ) {

			return $this->message_handler;
		}

		return $this->message_handler = new SV_WP_Admin_Message_Handler( $this->get_id() );
	}


	/**
	 * Helper function to determine whether a plugin is active
	 *
	 * @since 2.0.0
	 * @param string $plugin_name plugin name, as the plugin-filename.php
	 * @return boolean true if the named plugin is installed and active
	 */
	public function is_plugin_active( $plugin_name ) {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		$plugin_filenames = array();

		foreach ( $active_plugins as $plugin ) {

			if ( SV_WC_Helper::str_exists( $plugin, '/' ) ) {

				// normal plugin name (plugin-dir/plugin-filename.php)
				list( , $filename ) = explode( '/', $plugin );

			} else {

				// no directory, just plugin file
				$filename = $plugin;
			}

			$plugin_filenames[] = $filename;
		}

		return in_array( $plugin_name, $plugin_filenames );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Handles version checking
	 *
	 * @since 2.0.0
	 */
	public function do_install() {

		$installed_version = get_option( $this->get_plugin_version_name() );

		// installed version lower than plugin version?
		if ( version_compare( $installed_version, $this->get_version(), '<' ) ) {

			if ( ! $installed_version ) {
				$this->install();
			} else {
				$this->upgrade( $installed_version );
			}

			// new version number
			update_option( $this->get_plugin_version_name(), $this->get_version() );
		}
	}


	/**
	 * Helper method to install default settings for a plugin
	 *
	 * @since 4.2.0
	 * @param array $settings array of settings in format required by WC_Admin_Settings
	 */
	public function install_default_settings( array $settings ) {

		foreach ( $settings as $setting ) {

			if ( isset( $setting['id'] ) && isset( $setting['default'] ) ) {

				update_option( $setting['id'], $setting['default'] );
			}
		}
	}


	/**
	 * Plugin install method.  Perform any installation tasks here
	 *
	 * @since 2.0.0
	 */
	protected function install() {
		// stub
	}


	/**
	 * Plugin upgrade method.  Perform any required upgrades here
	 *
	 * @since 2.0.0
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {
		// stub
	}


	/**
	 * Plugin activated method. Perform any activation tasks here.
	 * Note that this _does not_ run during upgrades.
	 *
	 * @since 4.2.0
	 */
	public function activate() {
		// stub
	}


	/**
	 * Plugin deactivation method. Perform any deactivation tasks here.
	 *
	 * @since 4.2.0
	 */
	public function deactivate() {
		// stub
	}


}

endif; // Class exists check
