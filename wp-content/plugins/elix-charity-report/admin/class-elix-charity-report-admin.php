<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://elixinol.com
 * @since      1.0.0
 *
 * @package    Elix_Charity_Report
 * @subpackage Elix_Charity_Report/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Elix_Charity_Report
 * @subpackage Elix_Charity_Report/admin
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_Charity_Report_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Elix_Charity_Report_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Elix_Charity_Report_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/elix-charity-report-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Elix_Charity_Report_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Elix_Charity_Report_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/elix-charity-report-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_submenu_woocommerce() {
		// add_submenu_page( 'woocommerce',
		// 	__( 'Charities', 'elix-charity-report' ),
		// 	__( 'Charities', 'elix-charity-report' ),
		// 	'manage_woocommerce',
		// 	'admin.php?page=wc-charities',
		// 	'elix_charity_report_output'
		// );
		add_menu_page( 
			__( 'Charities', 'elix-charity-report' ),
			__( 'Charities', 'elix-charity-report' ),
			'view_woocommerce_reports',
			'admin.php?page=wc-charities',
			'elix_charity_report_output', 
			null, '55.6' 
		);
	}

}
