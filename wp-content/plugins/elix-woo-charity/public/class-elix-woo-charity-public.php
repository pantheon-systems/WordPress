<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Elix_Woo_Charity
 * @subpackage Elix_Woo_Charity/public
 * @author     Zvi Epner <zvi.epner@elixinol.com>
 */
class Elix_Woo_Charity_Public {

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
	 * Render the charity field.
	 * 
	 * @since    1.0.0
	 * @param    object    $checkout    The checkout form
	 */
	public function elix_woo_load_charity( $checkout ) {

		$select_options = get_option( 'elix-woo-charity-options' );
		if ( empty($select_options ) ) return;

		$placeholder = get_option( 'elix-woo-charity-placeholder' );
		$label = get_option( 'elix-woo-charity-label' );

		$options = array( 'blank' => __( $placeholder, 'elix-woo-charity' ) );
		foreach ( $select_options as $i => $option ) {
			$options[$option] = __( $option, 'elix-woo-charity' );
		}

		$field_name = 'billing_donations';
		$field_args = array(
			'type'        => 'select',
			'input_class' => array( 'billing_donations' ),
			'label'       => __( $label, 'elix-woo-charity' ),
			'placeholder' => __( $placeholder, 'elix-woo-charity' ),
			'options'     => $options,
			'return'      => TRUE,
		);
		$field = woocommerce_form_field( $field_name, $field_args );

		include plugin_dir_path( __FILE__ ) . 'partials/elix-woo-charity-public-display.php';

	}

	/**
	 * Process the charity field on form submission.
	 * 
	 * @since    1.0.0
	 * @param    integer    $order_id    The order ID
	 */
	public function elix_woo_save_charity( $order_id ) {
		if ( ! empty( $_POST['billing_donations'] ) ) {
			update_post_meta( $order_id, '_billing_donations', sanitize_text_field( $_POST['billing_donations'] ) );
		}
	}

	/**
	 * Add the charity field to the order display.
	 * 
	 * @since    1.0.0
	 * @param    object    The order
	 */
	public function elix_woo_display_charity( $order ) {
		echo '<p><strong>' . __( 'Charity', 'elix-woo-charity' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_billing_donations', true ) . '</p>';
	}

	public function elix_woo_charity_cron() {

		global $wpdb;
		$last_id = (get_option('elix_woo_charity_lastid') !== false) ? get_option('elix_woo_charity_lastid') : 0;

		$query = "SELECT p.ID, p.post_date,
        (SELECT meta_value FROM wp_postmeta WHERE post_id = p.ID AND meta_key = '_billing_first_name') as first_name,
        (SELECT meta_value FROM wp_postmeta WHERE post_id = p.ID AND meta_key = '_billing_last_name') as last_name,
        (SELECT meta_value FROM wp_postmeta WHERE post_id = p.ID AND meta_key = '_order_total') as total,
        pm.meta_value
		FROM wp_postmeta pm INNER JOIN wp_posts p ON p.ID = pm.post_id WHERE pm.meta_value <> '' AND pm.meta_value <> 'blank' AND pm.meta_key = '_billing_donations' AND p.ID > $last_id LIMIT 0,10000 ";
		
		// Fetch order in Processing state.
		$results = $wpdb->get_results($query, OBJECT );
	
		foreach ($results as $result) {
			$wpdb->replace( 
				$wpdb->prefix . 'charity_report',
				[ 
					'post_id' => $result->ID,
					'post_date' => $result->post_date,
					'first_name' => $result->first_name,
					'last_name' => $result->last_name,
					'total' => $result->total,
					'charity' => $result->meta_value
				],
				[
					'%d', '%s', '%s', '%s', '%s', '%s'
				]
			);
			update_option( 'elix_woo_charity_lastid', $result->ID );
		}
	}

}
