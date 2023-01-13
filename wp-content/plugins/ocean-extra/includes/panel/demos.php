<?php
/**
 * Demos
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
if ( ! class_exists( 'OceanWP_Demos' ) ) {

	class OceanWP_Demos {

		/**
		 * Start things up
		 */
		public function __construct() {

			// Return if not in admin
			if ( ! is_admin() || is_customize_preview() ) {
				return;
			}

			// Import demos page
			if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
				require_once( OE_PATH .'/includes/panel/classes/importers/class-helpers.php' );
				require_once( OE_PATH .'/includes/panel/classes/class-install-demos.php' );
			}

			// Disable Woo Wizard if the Pro Demos plugin is activated
			if ( class_exists( 'Ocean_Pro_Demos' ) ) {
				add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
				add_filter( 'woocommerce_show_admin_notice', '__return_false' );
				add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_false' );
	        }

			// Start things
			add_action( 'admin_init', array( $this, 'init' ) );

			// Demos scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

			// Allows xml uploads
			add_filter( 'upload_mimes', array( $this, 'allow_xml_uploads' ) );

			// Demos popup
			add_action( 'admin_footer', array( $this, 'popup' ) );

		}

		/**
		 * Register the AJAX methods
		 *
		 * @since 1.0.0
		 */
		public function init() {

			// Demos popup ajax
			add_action( 'wp_ajax_owp_ajax_get_demo_data', array( $this, 'ajax_demo_data' ) );
			add_action( 'wp_ajax_owp_ajax_required_plugins_activate', array( $this, 'ajax_required_plugins_activate' ) );

			// Get data to import
			add_action( 'wp_ajax_owp_ajax_get_import_data', array( $this, 'ajax_get_import_data' ) );

			// Import XML file
			add_action( 'wp_ajax_owp_ajax_import_xml', array( $this, 'ajax_import_xml' ) );

			// Import customizer settings
			add_action( 'wp_ajax_owp_ajax_import_theme_settings', array( $this, 'ajax_import_theme_settings' ) );

			// Import widgets
			add_action( 'wp_ajax_owp_ajax_import_widgets', array( $this, 'ajax_import_widgets' ) );

			// Import forms
			add_action( 'wp_ajax_owp_ajax_import_forms', array( $this, 'ajax_import_forms' ) );

			// After import
			add_action( 'wp_ajax_owp_after_import', array( $this, 'ajax_after_import' ) );

		}

		/**
		 * Load scripts
		 *
		 * @since 1.4.5
		 */
		public static function scripts( $hook_suffix ) {

			if ( 'theme-panel_page_oceanwp-panel-install-demos' == $hook_suffix || 'toplevel_page_oceanwp' == $hook_suffix ) {

				// CSS
				wp_enqueue_style( 'owp-demos-style', plugins_url( '/assets/css/demos.min.css', __FILE__ ) );

				// JS
				wp_enqueue_script( 'owp-demos-js', plugins_url( '/assets/js/demos.min.js', __FILE__ ), array( 'jquery', 'wp-util', 'updates' ), '1.1', true );

				wp_localize_script( 'owp-demos-js', 'owpDemos', array(
					'ajaxurl' 					=> admin_url( 'admin-ajax.php' ),
					'demo_data_nonce' 			=> wp_create_nonce( 'get-demo-data' ),
					'owp_import_data_nonce' 	=> wp_create_nonce( 'owp_import_data_nonce' ),
					'content_importing_error' 	=> esc_html__( 'There was a problem during the importing process resulting in the following error from your server:', 'ocean-extra' ),
					'button_activating' 		=> esc_html__( 'Activating', 'ocean-extra' ) . '&hellip;',
					'button_active' 			=> esc_html__( 'Active', 'ocean-extra' ),
				) );

			}

		}

		/**
		 * Allows xml uploads so we can import from server
		 *
		 * @since 1.0.0
		 */
		public function allow_xml_uploads( $mimes ) {
			$mimes = array_merge( $mimes, array(
				'xml' 	=> 'application/xml'
			) );
			return $mimes;
		}

		/**
		 * Get demos data to add them in the Demo Import and Pro Demos plugins
		 *
		 * @since 1.4.5
		 */
		public static function get_demos_data() {

			// Demos url
			$url = 'https://demos.oceanwp.org/';

			$data = array(

				'elementor' => array(

					'maria' => array(
						'categories'  		=> array( 'Blog', 'One Page' ),
						'xml_file'     		=> $url . 'maria/sample-data.xml',
						'theme_settings' 	=> $url . 'maria/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'maria/widgets.wie',
						'form_file'  		=> $url . 'maria/form.json',
						'home_title'  		=> '',
						'blog_title'  		=> 'Home',
						'posts_to_show'  	=> '7',
						'elementor_width'  	=> '1220',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
								array(
									'slug'  	=> 'ocean-stick-anything',
									'init'  	=> 'ocean-stick-anything/ocean-stick-anything.php',
									'name'  	=> 'Ocean Stick Anything',
								),
							),
						),
					),

					'photos' => array(
						'categories'  		=> array( 'Business', 'Corporate' ),
						'xml_file'     		=> $url . 'photos/sample-data.xml',
						'theme_settings' 	=> $url . 'photos/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'photos/widgets.wie',
						'form_file'  		=> $url . 'photos/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '7',
						'elementor_width'  	=> '1220',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
						),
					),

					'architect' => array(
						'categories'  		=> array( 'Business' ),
						'xml_file'     		=> $url . 'architect/sample-data.xml',
						'theme_settings' 	=> $url . 'architect/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'architect/widgets.wie',
						'form_file'  		=> $url . 'architect/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'elementor_width'  	=> '1220',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
							),
						),
					),
					
					'blogger' => array(
						'categories'  		=> array( 'Blog' ),
						'xml_file'     		=> $url . 'blogger/sample-data.xml',
						'theme_settings' 	=> $url . 'blogger/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'blogger/widgets.wie',
						'form_file'  		=> $url . 'blogger/form.json',
						'home_title'  		=> '',
						'blog_title'  		=> 'Home',
						'posts_to_show'  	=> '12',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
							),
						),
					),
					
					'coach' => array(
						'categories'  		=> array( 'Business', 'Sport', 'One Page' ),
						'xml_file'     		=> $url . 'coach/sample-data.xml',
						'theme_settings' 	=> $url . 'coach/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'coach/widgets.wie',
						'form_file'  		=> $url . 'coach/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
							),
						),
					),
					
					'gym' => array(
						'categories'  		=> array( 'Business', 'Sport' ),
						'xml_file'     		=> $url . 'gym/sample-data.xml',
						'theme_settings' 	=> $url . 'gym/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'gym/widgets.wie',
						'form_file'  		=> $url . 'gym/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'News',
						'posts_to_show'  	=> '3',
						'elementor_width'  	=> '1100',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
							),
						),
					),
					
					'lawyer' => array(
						'categories'  		=> array( 'Business' ),
						'xml_file'     		=> $url . 'lawyer/sample-data.xml',
						'theme_settings' 	=> $url . 'lawyer/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'lawyer/widgets.wie',
						'form_file'  		=> $url . 'lawyer/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'elementor_width'  	=> '1220',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-side-panel',
									'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
									'name' 		=> 'Ocean Side Panel',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
							),
						),
					),
					
					'megagym' => array(
						'categories'  		=> array( 'Business', 'Sport', 'One Page' ),
						'xml_file'     		=> $url . 'megagym/sample-data.xml',
						'theme_settings' 	=> $url . 'megagym/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'megagym/widgets.wie',
						'form_file'  		=> $url . 'megagym/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
							),
						),
					),
					
					'personal' => array(
						'categories'  		=> array( 'Blog' ),
						'xml_file'     		=> $url . 'personal/sample-data.xml',
						'theme_settings' 	=> $url . 'personal/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'personal/widgets.wie',
						'form_file'  		=> $url . 'personal/form.json',
						'home_title'  		=> '',
						'blog_title'  		=> 'Home',
						'posts_to_show'  	=> '3',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-posts-slider',
									'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
									'name'  	=> 'Ocean Posts Slider',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
							),
						),
					),
					
					'simple' => array(
						'categories'  		=> array( 'eCommerce' ),
						'xml_file'     		=> $url . 'simple/sample-data.xml',
						'theme_settings' 	=> $url . 'simple/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'simple/widgets.wie',
						'form_file'  		=> $url . 'simple/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'elementor_width'  	=> '1100',
						'is_shop'  			=> true,
						'woo_image_size'  	=> '454',
						'woo_thumb_size' 	=> '348',
						'woo_crop_width'  	=> '3',
						'woo_crop_height' 	=> '4',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-modal-window',
									'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
									'name'  	=> 'Ocean Modal Window',
								),
								array(
									'slug'  	=> 'ocean-product-sharing',
									'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
									'name'  	=> 'Ocean Product Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
								array(
									'slug'  	=> 'woocommerce',
									'init'  	=> 'woocommerce/woocommerce.php',
									'name'  	=> 'WooCommerce',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
								array(
									'slug' 		=> 'ocean-footer-callout',
									'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
									'name' 		=> 'Ocean Footer Callout',
								),
								array(
									'slug' 		=> 'ocean-sticky-footer',
									'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
									'name' 		=> 'Ocean Sticky Footer',
								),
							),
						),
					),
					
					'store' => array(
						'categories'  		=> array( 'eCommerce' ),
						'xml_file'     		=> $url . 'store/sample-data.xml',
						'theme_settings' 	=> $url . 'store/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'store/widgets.wie',
						'form_file'  		=> $url . 'store/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '6',
						'elementor_width'  	=> '1220',
						'is_shop'  			=> true,
						'woo_image_size'  	=> '504',
						'woo_thumb_size' 	=> '265',
						'woo_crop_width'  	=> '4',
						'woo_crop_height' 	=> '5',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-custom-sidebar',
									'init'  	=> 'ocean-custom-sidebar/ocean-custom-sidebar.php',
									'name'  	=> 'Ocean Custom Sidebar',
								),
								array(
									'slug'  	=> 'ocean-product-sharing',
									'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
									'name'  	=> 'Ocean Product Sharing',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'ocean-stick-anything',
									'init'  	=> 'ocean-stick-anything/ocean-stick-anything.php',
									'name'  	=> 'Ocean Stick Anything',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
								array(
									'slug'  	=> 'woocommerce',
									'init'  	=> 'woocommerce/woocommerce.php',
									'name'  	=> 'WooCommerce',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
								array(
									'slug' 		=> 'ocean-footer-callout',
									'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
									'name' 		=> 'Ocean Footer Callout',
								),
							),
						),
					),
					
					'stylish' => array(
						'categories'  		=> array( 'Business' ),
						'xml_file'     		=> $url . 'stylish/sample-data.xml',
						'theme_settings' 	=> $url . 'stylish/oceanwp-export.dat',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '12',
						'elementor_width'  	=> '1420',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
							),
						),
					),
					
					'travel' => array(
						'categories'  		=> array( 'Blog' ),
						'xml_file'     		=> $url . 'travel/sample-data.xml',
						'theme_settings' 	=> $url . 'travel/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'travel/widgets.wie',
						'form_file'  		=> $url . 'travel/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '4',
						'elementor_width'  	=> '1220',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-custom-sidebar',
									'init'  	=> 'ocean-custom-sidebar/ocean-custom-sidebar.php',
									'name'  	=> 'Ocean Custom Sidebar',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
							),
						),
					),
					
					'lingerie' => array(
						'categories'  		=> array( 'eCommerce' ),
						'xml_file'     		=> $url . 'lingerie/sample-data.xml',
						'theme_settings' 	=> $url . 'lingerie/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'lingerie/widgets.wie',
						'form_file'  		=> $url . 'lingerie/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'elementor_width'  	=> '1220',
						'is_shop'  			=> true,
						'woo_image_size'  	=> '433',
						'woo_thumb_size' 	=> '265',
						'woo_crop_width'  	=> '4',
						'woo_crop_height' 	=> '5',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'ocean-product-sharing',
									'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
									'name'  	=> 'Ocean Product Sharing',
								),
								array(
									'slug'  	=> 'ocean-social-sharing',
									'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
									'name'  	=> 'Ocean Social Sharing',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
								array(
									'slug'  	=> 'woocommerce',
									'init'  	=> 'woocommerce/woocommerce.php',
									'name'  	=> 'WooCommerce',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
								array(
									'slug' 		=> 'ocean-elementor-widgets',
									'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
									'name' 		=> 'Ocean Elementor Widgets',
								),
								array(
									'slug' 		=> 'ocean-footer-callout',
									'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
									'name' 		=> 'Ocean Footer Callout',
								),
								array(
									'slug' 		=> 'ocean-woo-popup',
									'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
									'name' 		=> 'Ocean Woo Popup',
								),
							),
						),
					),
					
					'yoga' => array(
						'categories'  		=> array( 'Business', 'Sport' ),
						'xml_file'     		=> $url . 'yoga/sample-data.xml',
						'theme_settings' 	=> $url . 'yoga/oceanwp-export.dat',
						'widgets_file'  	=> $url . 'yoga/widgets.wie',
						'form_file'  		=> $url . 'yoga/form.json',
						'home_title'  		=> 'Home',
						'blog_title'  		=> 'Blog',
						'posts_to_show'  	=> '3',
						'required_plugins'  => array(
							'free' => array(
								array(
									'slug'  	=> 'ocean-extra',
									'init'  	=> 'ocean-extra/ocean-extra.php',
									'name'  	=> 'Ocean Extra',
								),
								array(
									'slug'  	=> 'elementor',
									'init'  	=> 'elementor/elementor.php',
									'name'  	=> 'Elementor',
								),
								array(
									'slug'  	=> 'wpforms-lite',
									'init'  	=> 'wpforms-lite/wpforms.php',
									'name'  	=> 'WPForms',
								),
							),
							'premium' => array(
								array(
									'slug' 		=> 'ocean-sticky-header',
									'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
									'name' 		=> 'Ocean Sticky Header',
								),
							),
						),
					),

				),

			);

			// Return
			return apply_filters( 'owp_demos_data', $data );

		}

		/**
		 * Get the category list of all categories used in the predefined demo imports array.
		 *
		 * @since 1.4.5
		 */
		public static function get_demo_all_categories( $demo_imports ) {
			$categories = array();

			foreach ( $demo_imports as $item ) {
				if ( ! empty( $item['categories'] ) && is_array( $item['categories'] ) ) {
					foreach ( $item['categories'] as $category ) {
						$categories[ sanitize_key( $category ) ] = $category;
					}
				}
			}

			if ( empty( $categories ) ) {
				return false;
			}

			return $categories;
		}

		/**
		 * Return the concatenated string of demo import item categories.
		 * These should be separated by comma and sanitized properly.
		 *
		 * @since 1.4.5
		 */
		public static function get_demo_item_categories( $item ) {
			$sanitized_categories = array();

			if ( isset( $item['categories'] ) ) {
				foreach ( $item['categories'] as $category ) {
					$sanitized_categories[] = sanitize_key( $category );
				}
			}

			if ( ! empty( $sanitized_categories ) ) {
				return implode( ',', $sanitized_categories );
			}

			return false;
		}

	    /**
	     * Demos popup
	     *
		 * @since 1.4.5
	     */
	    public static function popup() {
	    	global $pagenow;

	        // Display on the demos pages
	        if ( ( 'admin.php' == $pagenow && 'oceanwp-panel-install-demos' && isset( $_GET['page'] ) == $_GET['page'] )
	            || ( 'admin.php' == $pagenow && 'oceanwp-panel-pro-demos' && isset( $_GET['page'] )  == $_GET['page'] )
				|| ( 'admin.php' == $pagenow && 'oceanwp' && isset( $_GET['page'] )  == $_GET['page'] ) ) { ?>
		        
		        <div id="owp-demo-popup-wrap">
					<div class="owp-demo-popup-container">
						<div class="owp-demo-popup-content-wrap">
							<div class="owp-demo-popup-content-inner">
								<a href="#" class="owp-demo-popup-close">×</a>
								<div id="owp-demo-popup-content"></div>
							</div>
						</div>
					</div>
					<div class="owp-demo-popup-overlay"></div>
				</div>

	    	<?php
	    	}
	    }

		/**
		 * Demos popup ajax.
		 *
		 * @since 1.4.5
		 */
		public static function ajax_demo_data() {

			if ( !current_user_can('manage_options')||! wp_verify_nonce( $_GET['demo_data_nonce'], 'get-demo-data' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Database reset url
			if ( is_plugin_active( 'wordpress-database-reset/wp-reset.php' ) ) {
				$plugin_link 	= admin_url( 'tools.php?page=database-reset' );
			} else {
				$plugin_link 	= admin_url( 'plugin-install.php?s=Wordpress+Database+Reset&tab=search' );
			}

			// Get all demos
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}

			// Get selected demo
			$demo = $_GET['demo_name'];
			$demo_has_type = $_GET['demo_type'];

			// Get required plugins
			$plugins = $demo_data[$demo][ 'required_plugins' ];

			// Get free plugins
			$free = $plugins[ 'free' ];

			// Get premium plugins
			$premium = $plugins[ 'premium' ]; ?>

			<div id="owp-demo-plugins">

				<h2 class="title"><?php echo sprintf( esc_html__( 'Import the %1$s demo', 'ocean-extra' ), esc_attr( $demo ) ); ?></h2>

				<div class="owp-popup-text">

					<p><?php echo
						sprintf(
							esc_html__( 'Importing a demo template allows you to kick-start your website fast, instead of creating content from scratch. It is recommended to upload a demo template on a fresh WordPress install to prevent conflict with your current content or content loss. You can use this plugin to reset your site if needed: %1$sWordpress Database Reset%2$s.', 'ocean-extra' ),
							'<a href="'. $plugin_link .'" target="_blank">',
							'</a>'
						); ?></p>

					<div class="owp-required-plugins-wrap">
						<h3><?php esc_html_e( 'Required Plugins', 'ocean-extra' ); ?></h3>
						<p><?php esc_html_e( 'For your site to look exactly like this demo, we recommend the plugins below to be installed and activated.', 'ocean-extra' ); ?></p>
						<div class="owp-required-plugins oe-plugin-installer">
							<?php
							self::required_plugins( $free, 'free' );
							self::required_plugins( $premium, 'premium' ); ?>
						</div>
					</div>

				</div>

				<a class="owp-button owp-plugins-next" href="#"><?php esc_html_e( 'Go to the next step', 'ocean-extra' ); ?></a>

			</div>

			<form method="post" id="owp-demo-import-form">

				<input id="owp_import_demo" type="hidden" name="owp_import_demo" value="<?php echo esc_attr( $demo ); ?>" data-demo-type="<?php echo esc_attr( $demo_has_type ); ?>" />

				<div class="owp-demo-import-form-types">

					<h2 class="title"><?php esc_html_e( 'Select what you want to import:', 'ocean-extra' ); ?></h2>
					
					<ul class="owp-popup-text">
						<li>
							<label for="owp_import_xml">
								<input id="owp_import_xml" type="checkbox" name="owp_import_xml" checked="checked" />
								<strong><?php esc_html_e( 'Import XML Data', 'ocean-extra' ); ?></strong> (<?php esc_html_e( 'pages, posts, images, menus, etc...', 'ocean-extra' ); ?>)
							</label>
						</li>

						<li>
							<label for="owp_theme_settings">
								<input id="owp_theme_settings" type="checkbox" name="owp_theme_settings" checked="checked" />
								<strong><?php esc_html_e( 'Import Customizer Settings', 'ocean-extra' ); ?></strong>
							</label>
						</li>

						<li>
							<label for="owp_import_widgets">
								<input id="owp_import_widgets" type="checkbox" name="owp_import_widgets" checked="checked" />
								<strong><?php esc_html_e( 'Import Widgets', 'ocean-extra' ); ?></strong>
							</label>
						</li>

						<li>
							<label for="owp_import_forms">
								<input id="owp_import_forms" type="checkbox" name="owp_import_forms" checked="checked" />
								<strong><?php esc_html_e( 'Import Contact Form', 'ocean-extra' ); ?></strong>
							</label>
						</li>
					</ul>

				</div>
				
				<?php wp_nonce_field( 'owp_import_demo_data_nonce', 'owp_import_demo_data_nonce' ); ?>
				<input type="submit" name="submit" class="owp-button owp-import" value="<?php esc_html_e( 'Install this demo', 'ocean-extra' ); ?>"  />

			</form>

			<div class="owp-loader">
				<h2 class="title"><?php esc_html_e( 'The import process could take some time, please be patient', 'ocean-extra' ); ?></h2>
				<div class="owp-import-status owp-popup-text"></div>
			</div>

			<div class="owp-last">
				<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path></svg>
				<h3><?php esc_html_e( 'Demo Imported!', 'ocean-extra' ); ?></h3>
				<a href="<?php echo esc_url( get_home_url() ); ?>"" target="_blank"><?php esc_html_e( 'See the result', 'ocean-extra' ); ?></a>
			</div>

			<?php
			die();
		}

		/**
		 * Required plugins.
		 *
		 * @since 1.4.5
		 */
		public static function required_plugins( $plugins, $return ) {

			foreach ( $plugins as $key => $plugin ) {

				$api = array(
					'slug' 	=> isset( $plugin['slug'] ) ? $plugin['slug'] : '',
					'init' 	=> isset( $plugin['init'] ) ? $plugin['init'] : '',
					'name' 	=> isset( $plugin['name'] ) ? $plugin['name'] : '',
				);

				if ( ! is_wp_error( $api ) ) { // confirm error free

					// Installed but Inactive.
					if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) && is_plugin_inactive( $plugin['init'] ) ) {

						$button_classes = 'button activate-now button-primary';
						$button_text 	= esc_html__( 'Activate', 'ocean-extra' );

					// Not Installed.
					} elseif ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) ) {

						$button_classes = 'button install-now';
						$button_text 	= esc_html__( 'Install Now', 'ocean-extra' );

					// Active.
					} else {
						$button_classes = 'button disabled';
						$button_text 	= esc_html__( 'Activated', 'ocean-extra' );
					} ?>

					<div class="owp-plugin owp-clr owp-plugin-<?php echo $api['slug']; ?>" data-slug="<?php echo $api['slug']; ?>" data-init="<?php echo $api['init']; ?>">
						<h2><?php echo $api['name']; ?></h2>

						<?php
						// If premium plugins and not installed
						if ( 'premium' == $return
							&& ! file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) ) { ?>
							<a class="button" href="https://oceanwp.org/extension/<?php echo $api['slug']; ?>/" target="_blank"><?php esc_html_e( 'Get This Addon', 'ocean-extra' ); ?></a>
						<?php
						} else { ?>
							<button class="<?php echo $button_classes; ?>" data-init="<?php echo $api['init']; ?>" data-slug="<?php echo $api['slug']; ?>" data-name="<?php echo $api['name']; ?>"><?php echo $button_text; ?></button>
						<?php
						} ?>
					</div>

				<?php
				}
			}

		}

		/**
		 * Required plugins activate
		 *
		 * @since 1.4.5
		 */
		public function ajax_required_plugins_activate() {

			if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! $_POST['init'] ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => __( 'No plugin specified', 'ocean-extra' ),
					)
				);
			}

			$plugin_init = ( isset( $_POST['init'] ) ) ? esc_attr( $_POST['init'] ) : '';
			$activate 	 = activate_plugin( $plugin_init, '', false, true );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => $activate->get_error_message(),
					)
				);
			}

			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( 'Plugin Successfully Activated', 'ocean-extra' ),
				)
			);

		}

		/**
		 * Returns an array containing all the importable content
		 *
		 * @since 1.4.5
		 */
		public function ajax_get_import_data() {
                    if (!current_user_can('manage_options')) {
                            die( 'This action was stopped for security purposes.' );
			}
			check_ajax_referer( 'owp_import_data_nonce', 'security' );

			echo json_encode( 
				array(
					array(
						'input_name' 	=> 'owp_import_xml',
						'action' 		=> 'owp_ajax_import_xml',
						'method' 		=> 'ajax_import_xml',
						'loader' 		=> esc_html__( 'Importing XML Data', 'ocean-extra' )
					),

					array(
						'input_name' 	=> 'owp_theme_settings',
						'action' 		=> 'owp_ajax_import_theme_settings',
						'method' 		=> 'ajax_import_theme_settings',
						'loader' 		=> esc_html__( 'Importing Customizer Settings', 'ocean-extra' )
					),

					array(
						'input_name' 	=> 'owp_import_widgets',
						'action' 		=> 'owp_ajax_import_widgets',
						'method' 		=> 'ajax_import_widgets',
						'loader' 		=> esc_html__( 'Importing Widgets', 'ocean-extra' )
					),

					array(
						'input_name' 	=> 'owp_import_forms',
						'action' 		=> 'owp_ajax_import_forms',
						'method' 		=> 'ajax_import_forms',
						'loader' 		=> esc_html__( 'Importing Form', 'ocean-extra' )
					)
				)
			);

			die();
		}

		/**
		 * Import XML file
		 *
		 * @since 1.4.5
		 */
		public static function ajax_import_xml() {
			if ( !current_user_can('manage_options')||! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Get the selected demo
			$demo_type 			= $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Content
			$xml_file 			= isset( $demo['xml_file'] ) ? $demo['xml_file'] : '';

			// Delete the default post and page
			$sample_page 		= get_page_by_path( 'sample-page', OBJECT, 'page' );
			$hello_world_post 	= get_page_by_path( 'hello-world', OBJECT, 'post' );

			if ( ! is_null( $sample_page ) ) {
				wp_delete_post( $sample_page->ID, true );
			}

			if ( ! is_null( $hello_world_post ) ) {
				wp_delete_post( $hello_world_post->ID, true );
			}

			// Import Posts, Pages, Images, Menus.
			$instance = new OceanWP_Demos();
			$result = $instance->process_xml( $xml_file );

			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}

		/**
		 * Import customizer settings
		 *
		 * @since 1.4.5
		 */
		public static function ajax_import_theme_settings() {
			if (!current_user_can('manage_options') || ! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Include settings importer
			include OE_PATH . 'includes/panel/classes/importers/class-settings-importer.php';

			// Get the selected demo
			$demo_type 			= $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Settings file
			$theme_settings 	= isset( $demo['theme_settings'] ) ? $demo['theme_settings'] : '';

			// Import settings.
			$settings_importer = new OWP_Settings_Importer();
			$result = $settings_importer->process_import_file( $theme_settings );
			
			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}

		/**
		 * Import widgets
		 *
		 * @since 1.4.5
		 */
		public static function ajax_import_widgets() {
			if (!current_user_can('manage_options') || ! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Include widget importer
			include OE_PATH . 'includes/panel/classes/importers/class-widget-importer.php';

			// Get the selected demo
			$demo_type 			= $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Widgets file
			$widgets_file 		= isset( $demo['widgets_file'] ) ? $demo['widgets_file'] : '';

			// Import settings.
			$widgets_importer = new OWP_Widget_Importer();
			$result = $widgets_importer->process_import_file( $widgets_file );
			
			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}

		/**
		 * Import forms
		 *
		 * @since 1.4.5
		 */
		public static function ajax_import_forms() {
			if ( !current_user_can('manage_options') ||! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Include form importer
			include OE_PATH . 'includes/panel/classes/importers/class-wpforms-importer.php';

			// Get the selected demo
			$demo_type 			= $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Widgets file
			$form_file 			= isset( $demo['form_file'] ) ? $demo['form_file'] : '';

			// Import settings.
			$forms_importer = new OWP_WPForms_Importer();
			$result = $forms_importer->process_import_file( $form_file );
			
			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}

		/**
		 * After import
		 *
		 * @since 1.4.5
		 */
		public static function ajax_after_import() {
			if ( !current_user_can('manage_options') ||! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// If XML file is imported
			if ( $_POST['owp_import_is_xml'] === 'true' ) {

				// Get the selected demo
				$demo_type    = $_POST['owp_import_demo'];
				$demo_builder = $_POST['owp_import_demo_type'];

				// Get demos data
				$demos = self::get_demos_data();
				$demo_data = $demos['elementor'];
				if ( ! empty( $demos['gutenberg'] ) ) {
					$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
				}
				$demo = $demo_data[ $demo_type ];

				// Elementor width setting
				$elementor_width 	= isset( $demo['elementor_width'] ) ? $demo['elementor_width'] : '';

				// Reading settings
				$homepage_title 	= isset( $demo['home_title'] ) ? $demo['home_title'] : 'Home';
				$blog_title 		= isset( $demo['blog_title'] ) ? $demo['blog_title'] : '';

				// Posts to show on the blog page
				$posts_to_show 		= isset( $demo['posts_to_show'] ) ? $demo['posts_to_show'] : '';

				// If shop demo
				$shop_demo 			= isset( $demo['is_shop'] ) ? $demo['is_shop'] : false;

				// Product image size
				$image_size 		= isset( $demo['woo_image_size'] ) ? $demo['woo_image_size'] : '';
				$thumbnail_size 	= isset( $demo['woo_thumb_size'] ) ? $demo['woo_thumb_size'] : '';
				$crop_width 		= isset( $demo['woo_crop_width'] ) ? $demo['woo_crop_width'] : '';
				$crop_height 		= isset( $demo['woo_crop_height'] ) ? $demo['woo_crop_height'] : '';

				// Assign WooCommerce pages if WooCommerce Exists
				if ( class_exists( 'WooCommerce' ) && true == $shop_demo ) {

					$woopages = array(
						'woocommerce_shop_page_id' 				=> 'Shop',
						'woocommerce_cart_page_id' 				=> 'Cart',
						'woocommerce_checkout_page_id' 			=> 'Checkout',
						'woocommerce_pay_page_id' 				=> 'Checkout &#8594; Pay',
						'woocommerce_thanks_page_id' 			=> 'Order Received',
						'woocommerce_myaccount_page_id' 		=> 'My Account',
						'woocommerce_edit_address_page_id' 		=> 'Edit My Address',
						'woocommerce_view_order_page_id' 		=> 'View Order',
						'woocommerce_change_password_page_id' 	=> 'Change Password',
						'woocommerce_logout_page_id' 			=> 'Logout',
						'woocommerce_lost_password_page_id' 	=> 'Lost Password'
					);

					foreach ( $woopages as $woo_page_name => $woo_page_title ) {

						$woopage = get_page_by_title( $woo_page_title );
						if ( isset( $woopage ) && $woopage->ID ) {
							update_option( $woo_page_name, $woopage->ID );
						}

					}

					// We no longer need to install pages
					delete_option( '_wc_needs_pages' );
					delete_transient( '_wc_activation_redirect' );

					// Get products image size
					update_option( 'woocommerce_single_image_width', $image_size );
					update_option( 'woocommerce_thumbnail_image_width', $thumbnail_size );
					update_option( 'woocommerce_thumbnail_cropping', 'custom' );
					update_option( 'woocommerce_thumbnail_cropping_custom_width', $crop_width );
					update_option( 'woocommerce_thumbnail_cropping_custom_height', $crop_height );

				}

				// Set imported menus to registered theme locations
				$locations 	= get_theme_mod( 'nav_menu_locations' );
				$menus 		= wp_get_nav_menus();

				if ( $menus ) {
					
					foreach ( $menus as $menu ) {

						if ( $menu->name == 'Main Menu' ) {
							$locations['main_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Top Menu' ) {
							$locations['topbar_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Footer Menu' ) {
							$locations['footer_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Sticky Footer' ) {
							$locations['sticky_footer_menu'] = $menu->term_id;
						}

					}

				}

				// Set menus to locations
				set_theme_mod( 'nav_menu_locations', $locations );

				// Disable Elementor default settings
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );
			    if ( ! empty( $elementor_width ) ) {
					update_option( 'elementor_container_width', $elementor_width );
				}

				// Assign front page and posts page (blog page).
			    $home_page = get_page_by_title( $homepage_title );
			    $blog_page = get_page_by_title( $blog_title );

			    update_option( 'show_on_front', 'page' );

			    if ( is_object( $home_page ) ) {
					update_option( 'page_on_front', $home_page->ID );
				}

				if ( is_object( $blog_page ) ) {
					update_option( 'page_for_posts', $blog_page->ID );
				}

				// Posts to show on the blog page
			    if ( ! empty( $posts_to_show ) ) {
					update_option( 'posts_per_page', $posts_to_show );
				}

				if ( 'elementor' !== $demo_builder ) {

					$page_ids = get_all_page_ids();

					foreach ( $page_ids as $id ) {
						delete_post_meta( $id, '_elementor_edit_mode', '' );
					}

				}
			}

			die();
		}

		/**
		 * Import XML data
		 *
		 * @since 1.0.0
		 */
		public function process_xml( $file ) {
			
			$response = OWP_Demos_Helpers::get_remote( $file );

			// No sample data found
			if ( $response === false ) {
				return new WP_Error( 'xml_import_error', __( 'Can not retrieve sample data xml file. The server may be down at the moment please try again later. If you still have issues contact the theme developer for assistance.', 'ocean-extra' ) );
			}

			// Write sample data content to temp xml file
			$temp_xml = OE_PATH .'includes/panel/classes/importers/temp.xml';
			file_put_contents( $temp_xml, $response );

			// Set temp xml to attachment url for use
			$attachment_url = $temp_xml;

			// If file exists lets import it
			if ( file_exists( $attachment_url ) ) {
				$this->import_xml( $attachment_url );
			} else {
				// Import file can't be imported - we should die here since this is core for most people.
				return new WP_Error( 'xml_import_error', __( 'The xml import file could not be accessed. Please try again or contact the theme developer.', 'ocean-extra' ) );
			}

		}
		
		/**
		 * Import XML file
		 *
		 * @since 1.0.0
		 */
		private function import_xml( $file ) {

			// Make sure importers constant is defined
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true );
			}

			// Import file location
			$import_file = ABSPATH . 'wp-admin/includes/import.php';

			// Include import file
			if ( ! file_exists( $import_file ) ) {
				return;
			}

			// Include import file
			require_once( $import_file );

			// Define error var
			$importer_error = false;

			if ( ! class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ) {
					require_once $class_wp_importer;
				} else {
					$importer_error = __( 'Can not retrieve class-wp-importer.php', 'ocean-extra' );
				}
			}

			if ( ! class_exists( 'WP_Import' ) ) {
				$class_wp_import = OE_PATH . 'includes/panel/classes/importers/class-wordpress-importer.php';

				if ( file_exists( $class_wp_import ) ) {
					require_once $class_wp_import;
				} else {
					$importer_error = __( 'Can not retrieve wordpress-importer.php', 'ocean-extra' );
				}
			}

			// Display error
			if ( $importer_error ) {
				return new WP_Error( 'xml_import_error', $importer_error );
			} else {

				// No error, lets import things...
				if ( ! is_file( $file ) ) {
					$importer_error = __( 'Sample data file appears corrupt or can not be accessed.', 'ocean-extra' );
					return new WP_Error( 'xml_import_error', $importer_error );
				} else {
					$importer = new WP_Import();
					$importer->fetch_attachments = true;
					$importer->import( $file );

					// Clear sample data content from temp xml file
					$temp_xml = OE_PATH .'includes/panel/classes/importers/temp.xml';
					file_put_contents( $temp_xml, '' );
				}
			}
		}

	}

}
new OceanWP_Demos();