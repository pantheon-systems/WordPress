<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Functionality that is enabled when the WooCommerce Subscriptions plugin is active.
 *
 * Plugin URL: https://woocommerce.com/products/woocommerce-subscriptions/
 *
 * Class WC_Zapier_Subscriptions
 */
class WC_Zapier_Subscriptions {

	/**
	 * The minimum WooCommerce Subscriptions version that this plugin supports.
	 */
	const MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION = '2.3.0';

	/**
	 * Trigger keys that the subscriptions data should be added to.
	 *
	 * @var array
	 */
	private $trigger_keys = array(
		'wc.new_order', // New Order
		'wc.order_status_change' // New Order Status Change
	);

	/**
	 * Constructor
	 */
	public function __construct() {

		// Version check
		if ( version_compare( WC_Subscriptions::$version, self::MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			WC_Zapier()->log( "WooCommerce Subscriptions plugin version (" . WC_Subscriptions::$version . ") is less than " . self::MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION );
			return;
		}

		add_filter( 'wc_zapier_trigger_directories', array( $this, 'wc_zapier_trigger_directories' ) );
		add_filter( 'wc_zapier_autoload_file', array( $this, 'wc_zapier_autoload_file' ), 10, 2 );

		foreach ( $this->trigger_keys as $trigger_key ) {
			add_filter( "wc_zapier_data_{$trigger_key}", array( $this, 'order_data_override' ), 10, 2 );
		}

	}

	/**
	 * Load Subscriptions-related Triggers from the subscriptions sub directory.
	 *
	 * @param $directories
	 *
	 * @return array
	 */
	public function wc_zapier_trigger_directories( $directories ) {
		$directories[] = WC_Zapier::$plugin_path . 'includes/triggers/subscriptions';
		return $directories;
	}

	/**
	 * Autoloading for Subscriptions-specific triggers.
	 *
	 * @param string $file
	 * @param string $class_name
	 *
	 * @return string
	 */
	public function wc_zapier_autoload_file( $file, $class_name ) {
		if ( false !== strpos( $class_name, 'Zapier_Trigger_Subscription' ) ) {
			$file = str_replace( 'triggers/', 'triggers/subscriptions/', $file );
		}
		return $file;
	}

	/**
	 * Displays a message if the user isn't using a supported version of WooCommerce Subscriptions.
	 */
	public function admin_notice() {
		?>
		<div id="message" class="error">
			<p><?php echo esc_html( sprintf( __( 'The WooCommerce Zapier Integration plugin is only compatible with WooCommerce Subscriptions version %s or later. Please update WooCommerce Subscriptions.', 'wc_zapier' ), self::MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION ) ); ?></p>
		</div>
		<?php
	}


	/**
	 * When sending WooCommerce Order data to Zapier, also send any additional WC subscriptions fields.
	 *
	 * @param             array $order_data Order data that will be overridden.
	 * @param WC_Zapier_Trigger $trigger    Trigger that initiated the data send.
	 *
	 * @return array
	 */
	public function order_data_override( $order_data, WC_Zapier_Trigger $trigger ) {

		if ( $trigger->is_sample() ) {
			$order_data['is_subscription_renewal'] = false;
			$order_data['subscription_id']         = '';
		} else {
			// Sending live data
			$subscription_renewal = get_post_meta( $order_data['id'], '_subscription_renewal', true );
			if ( !empty($subscription_renewal) ) {
				$order_data['is_subscription_renewal'] = true;
				$order_data['subscription_id']         = $subscription_renewal;
			} else {
				$order_data['is_subscription_renewal'] = false;
				$order_data['subscription_id']         = '';
			}
		}

		return $order_data;
	}

}