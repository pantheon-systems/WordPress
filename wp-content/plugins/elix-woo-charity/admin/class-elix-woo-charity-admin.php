<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://elixinol.com
 * @since      1.0.0
 *
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/admin
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_Woo_Charity_Admin {

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
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/elix-woo-charity-admin.js', array( 'jquery' ), $this->version, false );
	}

	function ewc_add_menu_items() {
		$page_title = __('Charity Selection Reports', 'elix-woo-charity');
		if (current_user_can('view_woocommerce_reports')) {
			add_submenu_page( 'woocommerce', $page_title, $page_title, 'view_woocommerce_reports', 'ewc_list_table', 'ewc_render_list_page');
		}
	}

	function __return_true() {
		return true;
	}

}
