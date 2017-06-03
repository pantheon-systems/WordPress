<?php
/*
 * Plugin Name: Divi Builder
 * Plugin URI: http://elegantthemes.com
 * Description: A drag and drop page builder for any WordPress theme.
 * Version: 1.3.4
 * Author: Elegant Themes
 * Author URI: http://elegantthemes.com
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'ET_BUILDER_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ET_BUILDER_PLUGIN_URI', plugins_url('', __FILE__) );
define( 'ET_BUILDER_PLUGIN_VERSION', '1.3.4' );

if ( ! class_exists( 'ET_Dashboard_v2' ) ) {
	require_once( ET_BUILDER_PLUGIN_DIR . 'dashboard/dashboard.php' );
}

class ET_Builder_Plugin extends ET_Dashboard_v2 {
	var $plugin_version = ET_BUILDER_PLUGIN_VERSION;
	var $_options_pagename = 'et_builder_options';
	var $menu_page;
	private static $_this;

	function __construct() {
		// Don't allow more than one instance of the class
		if ( isset( self::$_this ) ) {
			wp_die( sprintf( esc_html__( '%s is a singleton class and you cannot create a second instance.', 'et_builder' ),
				get_class( $this ) )
			);
		}

		if ( ( defined( 'ET_BUILDER_THEME' ) && ET_BUILDER_THEME ) || function_exists( 'et_divi_fonts_url' ) ) {
			return; // Disable the plugin, if the theme comes with the Builder
		}

		self::$_this = $this;

		$this->protocol = is_ssl() ? 'https' : 'http';

		$this->et_plugin_setup_builder();

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

		add_action( 'admin_init', array( $this, 'construct_dashboard' ) );

		add_action( 'wp_ajax_et_builder_save_settings', array( $this, 'builder_save_settings' ) );

		add_action( 'wp_ajax_et_builder_authorize_aweber', array( $this, 'authorize_aweber' ) );

		add_action( 'wp_ajax_et_builder_refresh_lists', array( $this, 'refresh_lists' ) );

		add_action( 'wp_ajax_et_builder_save_updates_settings', array( $this, 'save_updates_settings' ) );

		add_filter( 'et_pb_builder_authorization_verdict', array( $this, 'is_aweber_authorized' ) );

		add_filter( 'body_class', array( $this, 'add_body_class' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'et_pb_hide_options_menu' ) );

		add_filter( 'the_content', array( $this, 'add_builder_content_wrapper' ) );

		add_filter( 'et_builder_inner_content_class', array( $this, 'add_builder_inner_content_class' ) );

		add_filter( 'et_pb_builder_options_array', array( $this, 'get_builder_options' ) );
	}

	static function add_updates() {
		require_once( ET_BUILDER_PLUGIN_DIR . 'core/updates_init.php' );

		et_core_enable_automatic_updates( ET_BUILDER_PLUGIN_URI, ET_BUILDER_PLUGIN_VERSION );
	}

	function add_builder_content_wrapper( $content ) {
		if ( ! et_pb_is_pagebuilder_used( get_the_ID() ) && ! is_et_pb_preview() ) {
			return $content;
		}

		// Divi builder layout should only be used in singular template
		if ( ! is_singular() ) {
			// get_the_excerpt() for excerpt retrieval causes infinite loop; thus we're using excerpt from global $post variable
			global $post;

			$read_more = sprintf(
				' <a href="%1$s" title="%2$s" class="more-link">%3$s</a>',
				esc_url( get_permalink() ),
				sprintf( esc_attr__( 'Read more on %1%s', 'et_builder' ), esc_html( get_the_title() ) ),
				esc_html__( 'read more', 'et_builder' )
			);

			// Use post excerpt if there's any. If there is no excerpt defined,
			// Generate from post content by stripping all shortcode first
			if ( ! empty( $post->post_excerpt ) ) {
				return wpautop( $post->post_excerpt . $read_more );
			} else {
				$shortcodeless_content = preg_replace( '/\[[^\]]+\]/', '', $content );
				return wpautop( et_wp_trim_words( $shortcodeless_content, 270, $read_more ) );
			}
		}

		$outer_class   = apply_filters( 'et_builder_outer_content_class', array( 'et_builder_outer_content' ) );
		$outer_classes = implode( ' ', $outer_class );

		$outer_id      = apply_filters( "et_builder_outer_content_id", "et_builder_outer_content" );

		$inner_class   = apply_filters( 'et_builder_inner_content_class', array( 'et_builder_inner_content' ) );
		$inner_classes = implode( ' ', $inner_class );

		$content = sprintf(
			'<div class="%2$s" id="%4$s">
				<div class="%3$s">
					%1$s
				</div>
			</div>',
			$content,
			esc_attr( $outer_classes ),
			esc_attr( $inner_classes ),
			esc_attr( $outer_id )
		);

		return $content;
	}

	function add_body_class( $classes ) {
		$classes[] = 'et_divi_builder';

		return $classes;
	}

	function add_builder_inner_content_class( $classes ) {
		$classes[] = 'et_pb_gutters3';

		return $classes;
	}

	function construct_dashboard() {
		$dashboard_args = array(
			'et_dashboard_options_pagename'  => $this->_options_pagename,
			'et_dashboard_plugin_name'       => 'pb_builder',
			'et_dashboard_save_button_text'  => esc_html__( 'Save', 'et_builder' ),
			'et_dashboard_options_path'      => ET_BUILDER_PLUGIN_DIR . '/dashboard/includes/options.php',
			'et_dashboard_options_page'      => 'toplevel_page',
			'et_dashboard_options_pagename'  => 'et_divi_options',
			'et_dashboard_plugin_class_name' => 'et_builder',
		);

		parent::__construct( $dashboard_args );
	}

	function builder_save_settings() {
		self::dashboard_save_settings();
	}

	/**
	 * Retrieves the Builder options from DB
	 * @return array
	 */
	function get_builder_options() {
		$auto_updates_settings = get_option( 'et_automatic_updates_options' ) ? get_option( 'et_automatic_updates_options' ) : array();
		$builder_options = get_option( 'et_pb_builder_options' ) ? get_option( 'et_pb_builder_options' ) : array();
		$processed_updates_settings = array();

		// prepare array of Auto Updates settings
		$processed_updates_settings['updates_main_updates_username'] = isset( $auto_updates_settings['username'] ) ? $auto_updates_settings['username'] : '';
		$processed_updates_settings['updates_main_updates_api_key'] = isset( $auto_updates_settings['api_key'] ) ? $auto_updates_settings['api_key'] : '';

		$complete_options_set = array_merge( $builder_options, $processed_updates_settings );
		return $complete_options_set;
	}

	function options_page() {
		// display wp error screen if plugin options disabled for current user
		if ( ! et_pb_is_allowed( 'theme_options' ) ) {
			wp_die( esc_html__( "You don't have sufficient permissions to access this page", 'et_builder_plugin' ) );
		}

		printf(
			'<div class="et_pb_save_settings_button_wrapper">
				<a href="#" id="et_pb_save_plugin" class="button button-primary button-large">%1$s</a>
				<h3 class="et_pb_settings_title">
					%2$s
				</h3>
			</div>',
			esc_html__( 'Save Settings', 'et_builder_plugin' ),
			esc_html__( 'Divi Builder Options', 'et_builder_plugin' )
		);

		self::generate_options_page();
	}

	function et_plugin_setup_builder() {
		define( 'ET_BUILDER_PLUGIN_ACTIVE', true );

		define( 'ET_BUILDER_VERSION', ET_BUILDER_PLUGIN_VERSION );

		define( 'ET_BUILDER_DIR', ET_BUILDER_PLUGIN_DIR . 'includes/builder/' );
		define( 'ET_BUILDER_URI', trailingslashit( plugins_url( '', __FILE__ ) ) . 'includes/builder' );
		define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );
		define( 'ET_CORE_VERSION', $this->plugin_version );

		load_theme_textdomain( 'et_builder', ET_BUILDER_DIR . 'languages' );

		load_plugin_textdomain( 'et_builder_plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		require ET_BUILDER_PLUGIN_DIR . 'functions.php';
		require ET_BUILDER_PLUGIN_DIR . 'theme-compat.php';
		require ET_BUILDER_DIR . 'framework.php';
		require_once( ET_BUILDER_PLUGIN_DIR . 'core/init.php' );

		et_core_setup( ET_BUILDER_PLUGIN_URI );

		et_pb_register_posttypes();

		add_action( 'admin_menu', array( $this, 'add_divi_menu' ));
	}

	function add_divi_menu() {
		add_menu_page( 'Divi', 'Divi', 'switch_themes', 'et_divi_options', array( $this, 'options_page' ) );

		// Add Theme Options menu only if it's enabled for current user
		if ( et_pb_is_allowed( 'theme_options' ) ) {
			add_submenu_page( 'et_divi_options', esc_html__( 'Plugin Options', 'et_builder_plugin' ), esc_html__( 'Plugin Options', 'et_builder_plugin' ), 'manage_options', 'et_divi_options', array( $this, 'options_page' ) );
		}
		// Add Divi Library menu only if it's enabled for current user
		if ( et_pb_is_allowed( 'divi_library' ) ) {
			add_submenu_page( 'et_divi_options', esc_html__( 'Divi Library', 'et_builder' ), esc_html__( 'Divi Library', 'et_builder' ), 'manage_options', 'edit.php?post_type=et_pb_layout' );
		}
		add_submenu_page( 'et_divi_options', esc_html__( 'Divi Role Editor', 'et_builder' ), esc_html__( 'Divi Role Editor', 'et_builder' ), 'manage_options', 'et_divi_role_editor', 'et_pb_display_role_editor' );
	}

	/**
	 *
	 * Adds js script which removes the top menu item from Divi menu if it's disabled
	 *
	 */
	function et_pb_hide_options_menu() {
		// do nothing if plugin options should be displayed in the menu
		if ( et_pb_is_allowed( 'theme_options' ) ) {
			return;
		}

		wp_enqueue_script( 'et-builder-custom-admin-menu', ET_BUILDER_PLUGIN_URI . '/js/menu_fix.js', array( 'jquery' ), $this->plugin_version, true );
	}

	function register_scripts( $hook ) {
		if ( "toplevel_page_et_divi_options" !== $hook ) {
			return;
		}

		wp_enqueue_style( 'et-builder-css', ET_BUILDER_PLUGIN_URI . '/css/admin.css', array(), $this->plugin_version );
		wp_enqueue_script( 'et-builder-js', ET_BUILDER_PLUGIN_URI . '/js/admin.js', array( 'jquery' ), $this->plugin_version, true );
		wp_localize_script( 'et-builder-js', 'builder_settings', array(
			'et_builder_nonce'           => wp_create_nonce( 'et_builder_nonce' ),
			'ajaxurl'                    => admin_url( 'admin-ajax.php', $this->protocol ),
			'authorize_text'             => esc_html__( 'Authorize', 'et_builder_plugin' ),
			'reauthorize_text'           => esc_html__( 'Re-Authorize', 'et_builder_plugin' ),
			'authorization_successflull' => esc_html__( 'AWeber successfully authorized', 'et_builder_plugin' ),
			'save_settings'              => wp_create_nonce( 'save_settings' ),
		) );
	}

	function authorize_aweber() {
		if ( ! wp_verify_nonce( $_POST['et_builder_nonce'] , 'et_builder_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$api_key = ! empty( $_POST['et_builder_api_key'] ) ? sanitize_text_field( $_POST['et_builder_api_key'] ) : '';

		$error_message = '' !== $api_key ? $this->aweber_authorization( $api_key ) : esc_html__( 'please paste valid authorization code', 'et_builder_plugin' );

		$result = 'success' == $error_message ?
			$error_message
			: esc_html__( 'Authorization failed: ', 'et_builder_plugin' ) . $error_message;

		die( $result );
	}

	function refresh_lists() {
		if ( ! wp_verify_nonce( $_POST['et_builder_nonce'] , 'et_builder_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$service = ! empty( $_POST['et_builder_mail_service'] ) ? sanitize_text_field( $_POST['et_builder_mail_service'] ) : '';
		self::process_and_update_options( $_POST['et_builder_form_options'] );

		switch ( $service ) {
			case 'mailchimp':
				$result = et_pb_get_mailchimp_lists( 'on' );
				break;
			case 'aweber':
				$result = et_pb_get_aweber_lists( 'on' );
				break;
		}

		if ( false === $result ) {
			$result = sprintf( esc_html__( 'Error: Please make sure %1$s', 'et_builder_plugin' ), 'mailchimp' === $service ? esc_html__( 'MailChimp API key is correct', 'et_builder_plugin' ) : esc_html__( 'AWeber is authorized', 'et_builder_plugin' ) );
		} else {
			$result = esc_html__( 'Lists have been successfully regenerated', 'et_builder_plugin' );
		}

		die( $result );
	}

	/**
	 * Retrieves the tokens from AWeber
	 * @return string
	 */
	function aweber_authorization( $api_key ) {

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
		}

		try {
			$auth = AWeberAPI::getDataFromAweberID( $api_key );

			if ( ! ( is_array( $auth ) && 4 === count( $auth ) ) ) {
				$error_message = esc_html__( 'Authorization code is invalid. Try regenerating it and paste in the new code.', 'et_builder_plugin' );
			} else {
				$error_message = 'success';
				list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = $auth;

				self::update_option( array(
					'newsletter_main_aweber_key' => sanitize_text_field( $api_key ),
					'aweber_consumer_key'        => sanitize_text_field( $consumer_key ),
					'aweber_consumer_secret'     => sanitize_text_field( $consumer_secret ),
					'aweber_access_key'          => sanitize_text_field( $access_key ),
					'aweber_access_secret'       => sanitize_text_field( $access_secret ),
				) );
			}
		} catch ( AWeberAPIException $exc ) {
			$error_message = sprintf(
				'<p>%4$s</p>
				<ul>
					<li>%5$s: %1$s</li>
					<li>%6$s: %2$s</li>
					<li>%7$s: %3$s</li>
				</ul>',
				esc_html( $exc->type ),
				esc_html( $exc->message ),
				esc_html( $exc->documentation_url ),
				esc_html__( 'AWeberAPIException.', 'et_builder_plugin' ),
				esc_html__( 'Type', 'et_builder_plugin' ),
				esc_html__( 'Message', 'et_builder_plugin' ),
				esc_html__( 'Documentation', 'et_builder_plugin' )
			);
		}

		return $error_message;
	}

	/**
	 * Checks whether Aweber is authorized or not.
	 * Used to determine whether to display "Authorize" or "Re-Authorize" text on butoton
	 */
	function is_aweber_authorized( $network ) {
		$builder_settings = $this->get_builder_options();

		// Consider aweber authorized if all 4 fields are not empty
		if ( ! empty( $builder_settings ) && ! empty( $builder_settings['aweber_consumer_key'] ) && ! empty( $builder_settings['aweber_consumer_secret'] ) && ! empty( $builder_settings['aweber_access_key'] ) && ! empty( $builder_settings['aweber_access_secret'] ) ) {
			return true;
		}
	}

	function save_updates_settings() {
		if ( ! wp_verify_nonce( $_POST['et_builder_nonce'] , 'et_builder_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$username = ! empty( $_POST['et_builder_updates_username'] ) ? sanitize_text_field( $_POST['et_builder_updates_username'] ) : '';
		$api_key = ! empty( $_POST['et_builder_updates_api_key'] ) ? sanitize_text_field( $_POST['et_builder_updates_api_key'] ) : '';

		update_option( 'et_automatic_updates_options', array(
			'username' => $username,
			'api_key' => $api_key,
		) );

		die();
	}
}

function et_divi_builder_init_plugin() {
	new ET_Builder_Plugin();
}
add_action( 'init', 'et_divi_builder_init_plugin' );

function et_divi_builder_add_updates() {
	// Plugins Updates system should be loaded before a theme core loads
	ET_Builder_Plugin::add_updates();
}
add_action( 'plugins_loaded', 'et_divi_builder_add_updates' );