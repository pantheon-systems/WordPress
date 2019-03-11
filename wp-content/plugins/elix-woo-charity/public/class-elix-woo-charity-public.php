<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://elixinol.com
 * @since      1.0.0
 *
 * @package    Elix_woo_charity
 * @subpackage Elix_woo_charity/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Elix_woo_charity
 * @subpackage Elix_woo_charity/public
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_woo_charity_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/elix-woo-charity-public.css', array(), $this->version, 'all' );
	}

	public function elix_woo_load_charity($checkout) {
		echo '<div id="charity_selection"><h3>' . __('Charity') . '</h3>';

		woocommerce_form_field( 'billing_donations', array(
			'type'          => 'select',
			'input_class'         => array('billing_donations select2 select2-container'),
			'label'         => __('Please select which organization you would like us to donate 5% of your purchase to on your behalf'),
			'placeholder'   => __('Choose your organization'),
			'options'       => array(
								'blank'		=> __( 'Select a charity', 'wps' ),
								'Realm of Caring'	=> __( 'Realm of Caring', 'wps' ),
								'Autism One'	=> __( 'Autism One', 'wps' ),
								'Vote Hemp' 	=> __( 'Vote Hemp', 'wps' ),
								'Concussion Legacy Foundation ' 	=> __( 'Concussion Legacy Foundation ', 'wps' ),
								'American Brain Tumor Association' 	=> __( 'American Brain Tumor Association', 'wps' ),
								'The Cancer Cure Foundation' 	=> __( 'The Cancer Cure Foundation', 'wps' ),
								'Wounded Warrior Project' 	=> __( 'Wounded Warrior Project', 'wps' )
							)
			), $checkout->get_value( 'billing_donations' ));

		echo '</div>';

	}

	public function elix_woo_save_charity( $order_id ) {
		if ( ! empty( $_POST['billing_donations'] ) ) {
			update_post_meta( $order_id, '_billing_donations', sanitize_text_field( $_POST['billing_donations'] ) );
		}
	}

	public function elix_woo_display_charity($order){
		echo '<p><strong>'.__('Charity').':</strong> ' . get_post_meta( $order->get_id(), '_billing_donations', true ) . '</p>';
	}

}
