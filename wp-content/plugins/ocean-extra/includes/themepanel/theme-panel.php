<?php

/**
 * Scripts Panel
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Extra_New_Theme_Panel {

	/**
	 * Start things up
	 */
	public function __construct() {

		require_once OE_PATH . 'includes/themepanel/includes/metabox-descriptions.php';
		require_once OE_PATH . 'includes/themepanel/includes/classes/class-system-status.php';

		$oe_svg_support_active_status = get_option( 'oe_svg_support_active_status', 'no' );
		if ( $oe_svg_support_active_status == 'yes' ) {
			require_once OE_PATH . 'includes/themepanel/includes/classes/class-svg-sanitizer.php';
		}

		if ( is_admin() ) {
			// Add custom scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		add_action( 'wp_ajax_oceanwp_cp_save_customizer_settings', array( $this, 'save_customizer_settings' ) );
		add_action( 'wp_ajax_oceanwp_cp_save_panel_settings', array( $this, 'save_panel_settings' ) );
		add_action( 'wp_ajax_oceanwp_cp_save_integrations_settings', array( $this, 'save_integrations_settings' ) );

		add_action( 'wp_ajax_oceanwp_cp_save_single_option', array( $this, 'save_single_option' ) );

		add_action( 'wp_ajax_oceanwp_cp_customizer_reset', array( $this, 'customizer_reset' ) );
		add_action( 'wp_ajax_oceanwp_cp_customizer_export', array( $this, 'customizer_export' ) );
		add_action( 'wp_ajax_oceanwp_cp_customizer_import', array( $this, 'customizer_import' ) );

		add_action( 'wp_ajax_oceanwp_cp_child_theme_install', array( $this, 'child_theme_install' ) );

		add_filter( 'oceanwp_theme_panel_pane_quick_settings', array( $this, 'quick_settings_panel' ) );

		add_filter( 'oceanwp_theme_panel_pane_customizer_search', array( $this, 'customizer_search_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_customizer_reset', array( $this, 'customizer_reset_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_customizer_import_export', array( $this, 'customizer_import_export_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_customizer_controls', array( $this, 'customizer_controls_part' ) );

		add_filter( 'oceanwp_theme_panel_pane_extra_settings_adobe_fonts', array( $this, 'extra_settings_adobe_fonts_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_extra_settings_metaboxes', array( $this, 'extra_settings_metaboxes_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_extra_settings_widgets', array( $this, 'extra_settings_widgets_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_extra_settings_my_library', array( $this, 'extra_settings_my_library_part' ) );

		add_filter( 'oceanwp_theme_panel_pane_install_demos_switcher', array( $this, 'install_demos_switcher_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_install_demos_catalog', array( $this, 'install_demos_catalog_part' ) );

		add_filter( 'oceanwp_theme_panel_pane_integration_svg', array( $this, 'integration_svg_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_integration_mailchimp', array( $this, 'integration_mailchimp_part' ) );

		add_filter( 'oceanwp_theme_panel_pane_integration_google_maps', array( $this, 'integration_google_maps_part' ) );
		add_filter( 'oceanwp_theme_panel_pane_integration_google_recaptcha', array( $this, 'integration_google_recaptcha_part' ) );

		add_filter( 'oceanwp_theme_panel_pane_system_info_details', array( $this, 'system_info_details_part' ) );

		add_filter( 'ocean_main_metaboxes_post_types', array( 'Ocean_Extra_New_Theme_Panel', 'control_metaboxes' ), 9999 );
		add_filter( 'ocean_custom_widgets', array( 'Ocean_Extra_New_Theme_Panel', 'control_widgets' ), 9999 );
		add_filter( 'upload_mimes', array( $this, 'control_svg_mime_type' ), 9999 );

		add_action( 'customize_register', array( $this, 'customizer_controll' ), 100 );

		add_action( 'deactivated_plugin', array( $this, 'deactive_plugins_controll' ), 10, 2 );
	}

	/**
	 * Admin Scripts.
	 */
	public static function admin_scripts( $hook ) {
		// CSS
		wp_enqueue_style( 'oe-themepanel-customizer-style', plugins_url( '/assets/css/theme-panel-customizer.css', __FILE__ ) );

		$current_screen = get_current_screen();
		// Only load scripts when needed
		if ( 'toplevel_page_oceanwp' != $current_screen->id ) {
			return;
		}

		// JS
		wp_enqueue_script( 'oceanwp-scripts-themepanel', plugins_url( '/assets/js/theme-panel.js', __FILE__ ), OE_VERSION, true );

		wp_localize_script(
			'oceanwp-scripts-themepanel',
			'ExtraThemePanelOptions',
			array(
				'nonce'                          => wp_create_nonce( 'oceanwp_theme_panel' ),
				'customizer_reset_nonce'         => wp_create_nonce( 'customizer_reset' ),
				'customizer_export_nonce'        => wp_create_nonce( 'customizer_export' ),
				'customizer_import_nonce'        => wp_create_nonce( 'customizer_import' ),
				'ocean_save_single_option_nonce' => wp_create_nonce( 'ocean_save_single_option' ),
				'customizer_export_filename'     => self::get_customizer_export_filename(),
			)
		);
	}

	public function save_customizer_settings() {
		$params = array();
		parse_str( $_POST['form_fields'], $params );

		OceanWP_Theme_Panel::check_ajax_access( $params['customizer_control_nonce'], 'customizer_control' );

		if ( empty( $params['option_name'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		$option = trim( $params['option_name'] );
		$value  = null;
		if ( isset( $params[ $option ] ) ) {
			$value = $params[ $option ];
			if ( ! is_array( $value ) ) {
				$value = trim( $value );
			}
			$value = wp_unslash( $value );
			$value = self::validate_panels( $value );
		}
		update_option( $option, $value );

		wp_send_json_success(
			array(
				'option'  => $option,
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
			)
		);
	}

	public function save_panel_settings() {
		$params = array();
		parse_str( $_POST['form_fields'], $params );

		OceanWP_Theme_Panel::check_ajax_access( $_POST['nonce'], 'oceanwp_theme_panel' );

		if ( empty( $params['option_name'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		$option = trim( $params['option_name'] );
		$value  = array();
		if ( isset( $params[ $option ] ) ) {
			$value = $params[ $option ];
			$value = wp_unslash( $value );
		}
		update_option( $option, $value );

		wp_send_json_success(
			array(
				'option'  => $option,
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
			)
		);
	}

	public function save_integrations_settings() {
		$params = array();
		parse_str( $_POST['form_fields'], $params );

		OceanWP_Theme_Panel::check_ajax_access( $_POST['nonce'], 'oceanwp_theme_panel' );

		if ( empty( $_POST['settings_for'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		if ( $_POST['settings_for'] == 'white_label' ) {
			if( class_exists('Ocean_White_Label') ) {
				$settings = Ocean_White_Label::get_white_label_settings();
				$this->save_white_label_settings( $settings, $params );
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
					)
				);
			}
		} else {
			$method = 'get_' . $_POST['settings_for'] . '_settings';
			if ( ! method_exists( 'Ocean_Extra_New_Theme_Panel', $method ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
					)
				);
			}

			$settings = self::$method();
			foreach ( $settings as $key => $setting ) {
				if ( isset( $params['owp_integrations'][ $key ] ) ) {
					update_option( 'owp_' . $key, sanitize_text_field( wp_unslash( $params['owp_integrations'][ $key ] ) ) );
				}
			}
		}

		if( $_POST['settings_for'] == 'adobe_fonts' && $params['owp_integrations'][ 'adobe_fonts_integration' ] == '1' ) {
			$check_project_id_result = OceanWP_Adobe_Font()->check_project_id();
			if( $check_project_id_result['status'] !== 'success' ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Project ID is wrong.', 'ocean-extra' ),
					)
				);	
			}
		}

		wp_send_json_success(
			array(
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
			)
		);
	}

	public function save_single_option() {
		$params = $_POST;

		OceanWP_Theme_Panel::check_ajax_access( $params['_nonce'], 'ocean_save_single_option' );

		if ( empty( $params['option_name'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		$option = trim( $params['option_name'] );
		$value  = null;
		if ( isset( $params['value'] ) ) {
			$value = $params['value'];
			if ( $value == 'true' ) {
				$value = true;
			} elseif ( $value == 'false' ) {
				$value = false;
			}
			if ( ! is_array( $value ) && ! is_bool( $value ) ) {
				$value = trim( $value );
			}
			$value = wp_unslash( $value );
		}
		update_option( $option, $value );

		wp_send_json_success(
			array(
				'option'  => $option,
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
				'value'   => $value,
			)
		);
	}

	public function customizer_reset() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['_nonce'], 'customizer_reset' );

		$theme               = wp_get_theme();
		$themename           = strtolower( $theme->name );
		$customizer_settings = get_option( "theme_mods_{$themename}" );
		if ( $customizer_settings ) {
			delete_option( "theme_mods_{$themename}" );
		}

		wp_send_json_success(
			array(
				'message' => esc_html__( 'Settings successfully reset.', 'ocean-extra' ),
			)
		);
	}

	public function customizer_export() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['_nonce'], 'customizer_export', 'echo' );

		$mods = get_theme_mods();
		$data = array(
			'mods'    => $mods ? $mods : array(),
			'options' => array(),
		);

		foreach ( $mods as $key => $value ) {

			// Don't save widget data.
			if ( 'widget_' === substr( strtolower( $key ), 0, 7 ) ) {
				continue;
			}

			// Don't save sidebar data.
			if ( 'sidebars_' === substr( strtolower( $key ), 0, 9 ) ) {
				continue;
			}

			$data['options'][ $key ] = $value;
		}

		if ( function_exists( 'wp_get_custom_css_post' ) ) {
			$data['wp_css'] = wp_get_custom_css();
		}

		echo serialize( $data );
		die;
	}

	/**
	 * Check if Ocean Child theme is installed.
	 *
	 * @return void
	 */
	public function child_theme_install() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['nonce'], 'oceanwp_theme_panel' );

		if ( file_exists( get_theme_root() . '/oceanwp-child-theme-master' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Child theme already installed', 'oceanwp' ) ) );
		}

		try {
			$ocean_child_zip_path = WP_CONTENT_DIR . '/oceanwp-child-theme.zip';

			if ( file_exists( $ocean_child_zip_path ) ) {
				unlink( $ocean_child_zip_path );
			}
			file_put_contents(
				$ocean_child_zip_path,
				file_get_contents( 'https://downloads.oceanwp.org/oceanwp/oceanwp-child-theme.zip' )
			);

			$zip = new ZipArchive();
			if ( $zip->open( $ocean_child_zip_path ) === true ) {
				$zip->extractTo( get_theme_root() );
				$zip->close();
				if ( file_exists( $ocean_child_zip_path ) ) {
					unlink( $ocean_child_zip_path );
				}
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} catch ( Exception $e ) {
			wp_send_json_error();
		}
	}

	public function customizer_import() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['_nonce'], 'customizer_import', true );

		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Something went wrong', 'ocean-extra' ) ) );
		}

		$template  = get_template();
		$overrides = array(
			'test_form' => false,
			'test_type' => false,
			'mimes'     => array( 'dat' => 'text/plain' ),
		);
		$file      = wp_handle_upload( $_FILES['file'], $overrides );

		if ( isset( $file['error'] ) ) {
			wp_die(
				$file['error'],
				'',
				array( 'back_link' => true )
			);
		}

		// Process import file
		$res = self::process_import_file( $file['file'] );

		if ( $res['status'] == 'updated' ) {
			wp_send_json_success(
				array(
					'message' => 'Success',
				)
			);
		} else {
			wp_send_json_error( array( 'message' => $res['msg'] ) );
		}
	}


	/**
	 * Process import file
	 */
	public static function process_import_file( $file ) {
		// File exists?
		if ( ! file_exists( $file ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Import file could not be found. Please try again.', 'ocean-extra' ) ) );
		}

		// Get file contents and decode
		$raw  = file_get_contents( $file );
		$data = @unserialize( $raw, [ 'allowed_classes' => false ]  );

		// Delete import file
		unlink( $file );

		// If wp_css is set then import it.
		if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
			wp_update_custom_css_post( $data['wp_css'] );
		}

		// Import data
		$res = self::import_data( $data['mods'] );
		return $res;
	}

	/**
	 * Sanitization callback
	 */
	public static function import_data( $file ) {
		$msg  = null;
		$type = null;

		// Import the file
		if ( ! empty( $file ) ) {

			if ( '0' == json_last_error() ) {

				// Loop through mods and add them
				foreach ( $file as $mod => $value ) {
					set_theme_mod( $mod, $value );
				}

				// Success message
				$msg  = esc_attr__( 'Settings imported successfully.', 'ocean-extra' );
				$type = 'updated';
			}

			// Display invalid json data error
			else {

				$msg  = esc_attr__( 'Invalid Import Data.', 'ocean-extra' );
				$type = 'error';
			}
		}

		// No json data entered
		else {
			$msg  = esc_attr__( 'No import data found.', 'ocean-extra' );
			$type = 'error';
		}

		// Return file
		return array(
			'msg'    => $msg,
			'status' => $type,
		);
	}

	/**
	 * Main Sanitization callback
	 */
	private static function validate_panels( $settings ) {
		// Get panels array
		$panels = self::get_panels();

		foreach ( $panels as $key => $val ) {

			$settings[ $key ] = ! empty( $settings[ $key ] ) ? true : false;
		}

		// Return the validated/sanitized settings
		return $settings;
	}


	/**
	 * Return customizer panels
	 */
	public static function get_panels() {
		$panels = array(
			'oe_general_panel'        => array(
				'label' => esc_html__( 'General Panel', 'ocean-extra' ),
			),
			'oe_typography_panel'     => array(
				'label' => esc_html__( 'Typography Panel', 'ocean-extra' ),
			),
			'oe_topbar_panel'         => array(
				'label' => esc_html__( 'Top Bar Panel', 'ocean-extra' ),
			),
			'oe_header_panel'         => array(
				'label' => esc_html__( 'Header Panel', 'ocean-extra' ),
			),
			'oe_blog_panel'           => array(
				'label' => esc_html__( 'Blog Panel', 'ocean-extra' ),
			),
			'oe_sidebar_panel'        => array(
				'label' => esc_html__( 'Sidebar Panel', 'ocean-extra' ),
			),
			'oe_footer_widgets_panel' => array(
				'label' => esc_html__( 'Footer Widgets Panel', 'ocean-extra' ),
			),
			'oe_footer_bottom_panel'  => array(
				'label' => esc_html__( 'Footer Bottom Panel', 'ocean-extra' ),
			),
			'oe_custom_code_panel'    => array(
				'label' => esc_html__( 'Custom CSS/JS Panel', 'ocean-extra' ),
			),
		);

		// Apply filters and return
		return apply_filters( 'oe_theme_panels', $panels );
	}

	/**
	 * Get settings.
	 */
	public static function get_setting( $option = '' ) {
		$defaults = self::get_default_settings();

		$settings = wp_parse_args( get_option( 'oe_panels_settings', $defaults ), $defaults );

		return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
	}

	/**
	 * Get default settings value.
	 *
	 * @since 1.2.2
	 */
	public static function get_default_settings() {
		// Get panels array
		$panels = self::get_panels();

		// Add array
		$default = array();

		foreach ( $panels as $key => $val ) {
			$default[ $key ] = 1;
		}

		// Return
		return apply_filters( 'oe_default_panels', $default );
	}

	public static function get_customizer_export_filename() {
		$site_url  = site_url( '', 'http' );
		$site_url  = trim( $site_url, '/\\' ); // remove trailing slash
		$filename  = str_replace( 'http://', '', $site_url ); // remove http://
		$filename  = str_replace( array( '/', '\\' ), '-', $filename ); // replace slashes with -
		$filename .= '-oceanwp-export'; // append
		$filename  = apply_filters( 'ocean_export_filename', $filename );
		$filename .= '.dat';
		return $filename;
	}

	function quick_settings_panel() {
		return OE_PATH . 'includes/themepanel/views/panes/quick-settings.php';
	}

	function customizer_search_part() {
		return OE_PATH . 'includes/themepanel/views/panes/customizer-search.php';
	}
	function customizer_reset_part() {
		return OE_PATH . 'includes/themepanel/views/panes/customizer-reset.php';
	}
	function customizer_import_export_part() {
		return OE_PATH . 'includes/themepanel/views/panes/customizer-import-export.php';
	}
	function customizer_controls_part() {
		return OE_PATH . 'includes/themepanel/views/panes/customizer-controls.php';
	}

	function extra_settings_adobe_fonts_part() {
		return OE_PATH . 'includes/themepanel/views/panes/extra-settings-adobe-fonts.php';
	}
	function extra_settings_metaboxes_part() {
		return OE_PATH . 'includes/themepanel/views/panes/extra-settings-metaboxes.php';
	}
	function extra_settings_widgets_part() {
		return OE_PATH . 'includes/themepanel/views/panes/extra-settings-widgets.php';
	}
	function extra_settings_my_library_part() {
		return OE_PATH . 'includes/themepanel/views/panes/extra-settings-my-library.php';
	}
	function install_demos_switcher_part() {
		return OE_PATH . 'includes/themepanel/views/panes/install-demos-switcher.php';
	}
	function install_demos_catalog_part() {
		return OE_PATH . 'includes/themepanel/views/panes/install-demos-catalog.php';
	}
	function integration_svg_part() {
		return OE_PATH . 'includes/themepanel/views/panes/integration-svg.php';
	}
	function integration_mailchimp_part() {
		return OE_PATH . 'includes/themepanel/views/panes/integration-mailchimp.php';
	}
	function system_info_details_part() {
		return OE_PATH . 'includes/themepanel/views/panes/system-info-details.php';
	}
	function integration_google_maps_part() {
		return OE_PATH . 'includes/themepanel/views/panes/integration-google-maps.php';
	}
	function integration_google_recaptcha_part() {
		return OE_PATH . 'includes/themepanel/views/panes/integration-google-recaptcha.php';
	}


	public static function control_metaboxes( $post_types ) {
		$metabox_posttypes_settings = get_option( 'oe_metabox_posttypes_settings', -1 );

		if ( $metabox_posttypes_settings !== -1 ) {

			foreach ( $post_types as $key => $post_type ) {
				if ( empty( $metabox_posttypes_settings[ $post_type ] ) ) {
					unset( $post_types[ $key ] );
				}
			}
		}

		return $post_types;
	}

	function control_svg_mime_type( $types ) {
		$oe_svg_support_active_status = get_option( 'oe_svg_support_active_status', 'no' );
		if ( $oe_svg_support_active_status == 'no' && ! empty( $types['svg'] ) ) {
			unset( $types['svg'] );
		}
		return $types;
	}

	public static function control_widgets( $widgets ) {
		$oe_widgets_settings = get_option( 'oe_widgets_settings', -1 );

		if ( $oe_widgets_settings !== -1 ) {

			foreach ( $widgets as $index => $widget_key ) {

				if ( empty( $oe_widgets_settings[ $widget_key ] ) ) {
					unset( $widgets[ $index ] );
				}
			}
		}

		return $widgets;
	}

	public static function get_mailchimp_settings() {
		$settings = array(
			'mailchimp_api_key' => get_option( 'owp_mailchimp_api_key' ),
			'mailchimp_list_id' => get_option( 'owp_mailchimp_list_id' ),
		);

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

	public static function get_adobe_fonts_settings() {
		$settings = array(
			'adobe_fonts_integration' => get_option( 'owp_adobe_fonts_integration' ),
			'adobe_fonts_integration_project_id' => get_option( 'owp_adobe_fonts_integration_project_id' ),
			'adobe_fonts_integration_enable_customizer' => get_option( 'owp_adobe_fonts_integration_enable_customizer' ),
			'adobe_fonts_integration_enable_elementor' => get_option( 'owp_adobe_fonts_integration_enable_elementor' ),
		);

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

	public static function get_google_maps_settings() {
		$settings = array(
			'google_map_api' => get_option( 'owp_google_map_api' ),
		);

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

	public static function get_google_recaptcha_settings() {
		$settings = array(
			'recaptcha_site_key'    => get_option( 'owp_recaptcha_site_key' ),
			'recaptcha_secret_key'  => get_option( 'owp_recaptcha_secret_key' ),
			'recaptcha_version'     => get_option( 'owp_recaptcha_version' ),
			'recaptcha3_site_key'   => get_option( 'owp_recaptcha3_site_key' ),
			'recaptcha3_secret_key' => get_option( 'owp_recaptcha3_secret_key' ),
		);

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

	public static function get_ocean_images_settings() {
		$settings = array();

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

	private function save_white_label_settings( $settings, $params ) {
		if ( ! isset( $params['oceanwp_branding'] ) ) {
			return;
		}

		// Loop
		foreach ( $settings as $key => $setting ) {

			if ( in_array( $key, array( 'description' ) ) ) {
				if ( isset( $params['oceanwp_branding']['description'] ) ) {
					update_option( 'oceanwp_theme_description', wp_filter_nohtml_kses( wp_unslash( $params['oceanwp_branding']['description'] ) ) );
				}
			} elseif ( in_array( $key, array( 'hide_oceanwp_news' ) ) ) {
				if ( isset( $params['oceanwp_branding']['hide_oceanwp_news'] ) ) {
					update_option( 'oceanwp_hide_oceanwp_news', true );
				} else {
					update_option( 'oceanwp_hide_oceanwp_news', false );
				}
			} elseif ( in_array( $key, array( 'hide_theme_panel_sidebar' ) ) ) {
				/*
				if ( isset( $params['oceanwp_branding']['hide_theme_panel_sidebar'] ) ) {
					update_option( 'oceanwp_hide_theme_panel_sidebar', true );
				} else {
					update_option( 'oceanwp_hide_theme_panel_sidebar', false );
				}*/
			} elseif ( in_array( $key, array( 'hide_themes_customizer', 'hide_box', 'hide_changelog', 'whitelabel_oceanwp_panel', 'hide_small_nav_menu', 'hide_help_section', 'hide_download_section', 'hide_love_corner_section' ) ) ) {
				if ( isset( $params['oceanwp_branding'][ $key ] ) ) {
					update_option( 'oceanwp_' . $key, true );
				} else {
					update_option( 'oceanwp_' . $key, false );
				}
			} else {
				if ( isset( $params['oceanwp_branding'][ $key ] ) ) {
					update_option( 'oceanwp_theme_' . $key, sanitize_text_field( wp_unslash( $params['oceanwp_branding'][ $key ] ) ) );
				}
			}
		}
	}


	function customizer_controll( $customizer ) {
		if ( $customizer->get_section( 'freemius_upsell' ) ) {
			require_once OE_PATH . 'includes/themepanel/includes/classes/class-customizer-control.php';

			// Get link
			$url = 'https://oceanwp.org/core-extensions-bundle/';

			// If affiliate ref
			$ref_url = '';
			$aff_ref = apply_filters( 'ocean_affiliate_ref', $ref_url );

			// Add & is has referal link
			if ( $aff_ref ) {
				$if_ref = '&';
			} else {
				$if_ref = '?';
			}

			// Add source
			$utm = $if_ref . 'utm_source=customizer&utm_campaign=bundle&utm_medium=wp-dash';

			$customizer->remove_section( 'freemius_upsell' );
			$customizer->remove_setting( 'freemius_upsell' );

			$customizer->add_section(
				new OceanWP_Freemius_Upsell_Section(
					$customizer,
					'oceanwp_freemius_section',
					array(
						'title'    => '&#9733; ' . __( 'View paid features', 'ocean-extra' ),
						'url'      => $url . $aff_ref . $utm,
						'priority' => 0,
					)
				)
			);
		}
	}

	function deactive_plugins_controll( $plugin, $network_deactivating ) {
		if ( $plugin == 'anywhere-elementor/anywhere-elementor.php' ) {
			$metabox_posttypes_settings = get_option( 'oe_metabox_posttypes_settings', -1 );
			if ( $metabox_posttypes_settings !== -1 ) {
				if ( ! empty( $metabox_posttypes_settings['ae_global_templates'] ) ) {
					unset( $metabox_posttypes_settings['ae_global_templates'] );
					update_option( 'oe_metabox_posttypes_settings', $metabox_posttypes_settings );
				}
			}
		}
	}
}

new Ocean_Extra_New_Theme_Panel();
