<?php
/**
 * WooCommerce advanced settings
 *
 * @package  WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings for API.
 */
if ( class_exists( 'WC_Settings_Advanced', false ) ) {
	return new WC_Settings_Advanced();
}

/**
 * WC_Settings_Advanced.
 */
class WC_Settings_Advanced extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'advanced';
		$this->label = __( 'Advanced', 'woocommerce' );

		parent::__construct();
		$this->notices();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''           => __( 'Page setup', 'woocommerce' ),
			'keys'       => __( 'REST API', 'woocommerce' ),
			'webhooks'   => __( 'Webhooks', 'woocommerce' ),
			'legacy_api' => __( 'Legacy API', 'woocommerce' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section slug.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		$settings = array();

		if ( '' === $current_section ) {
			$settings = apply_filters(
				'woocommerce_settings_pages', array(

					array(
						'title' => __( 'Page setup', 'woocommerce' ),
						'desc'  => __( 'These pages need to be set so that WooCommerce knows where to send users to checkout.', 'woocommerce' ),
						'type'  => 'title',
						'id'    => 'advanced_page_options',
					),

					array(
						'title'    => __( 'Cart page', 'woocommerce' ),
						/* Translators: %s Page contents. */
						'desc'     => sprintf( __( 'Page contents: [%s]', 'woocommerce' ), apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) ),
						'id'       => 'woocommerce_cart_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'wc-enhanced-select-nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Checkout page', 'woocommerce' ),
						/* Translators: %s Page contents. */
						'desc'     => sprintf( __( 'Page contents: [%s]', 'woocommerce' ), apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) ),
						'id'       => 'woocommerce_checkout_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'wc-enhanced-select-nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'My account page', 'woocommerce' ),
						/* Translators: %s Page contents. */
						'desc'     => sprintf( __( 'Page contents: [%s]', 'woocommerce' ), apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) ),
						'id'       => 'woocommerce_myaccount_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'wc-enhanced-select-nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Terms and conditions', 'woocommerce' ),
						'desc'     => __( 'If you define a "Terms" page the customer will be asked if they accept them when checking out.', 'woocommerce' ),
						'id'       => 'woocommerce_terms_page_id',
						'default'  => '',
						'class'    => 'wc-enhanced-select-nostd',
						'css'      => 'min-width:300px;',
						'type'     => 'single_select_page',
						'args'     => array( 'exclude' => wc_get_page_id( 'checkout' ) ),
						'desc_tip' => true,
						'autoload' => false,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'advanced_page_options',
					),

					array(
						'title' => '',
						'type'  => 'title',
						'id'    => 'checkout_process_options',
					),

					'force_ssl_checkout'   => array(
						'title'           => __( 'Secure checkout', 'woocommerce' ),
						'desc'            => __( 'Force secure checkout', 'woocommerce' ),
						'id'              => 'woocommerce_force_ssl_checkout',
						'default'         => 'no',
						'type'            => 'checkbox',
						'checkboxgroup'   => 'start',
						'show_if_checked' => 'option',
						/* Translators: %s Docs URL. */
						'desc_tip'        => sprintf( __( 'Force SSL (HTTPS) on the checkout pages (<a href="%s" target="_blank">an SSL Certificate is required</a>).', 'woocommerce' ), 'https://docs.woocommerce.com/document/ssl-and-https/#section-3' ),
					),

					'unforce_ssl_checkout' => array(
						'desc'            => __( 'Force HTTP when leaving the checkout', 'woocommerce' ),
						'id'              => 'woocommerce_unforce_ssl_checkout',
						'default'         => 'no',
						'type'            => 'checkbox',
						'checkboxgroup'   => 'end',
						'show_if_checked' => 'yes',
					),

					array(
						'type' => 'sectionend',
						'id'   => 'checkout_process_options',
					),

					array(
						'title' => __( 'Checkout endpoints', 'woocommerce' ),
						'type'  => 'title',
						'desc'  => __( 'Endpoints are appended to your page URLs to handle specific actions during the checkout process. They should be unique.', 'woocommerce' ),
						'id'    => 'account_endpoint_options',
					),

					array(
						'title'    => __( 'Pay', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "Checkout &rarr; Pay" page.', 'woocommerce' ),
						'id'       => 'woocommerce_checkout_pay_endpoint',
						'type'     => 'text',
						'default'  => 'order-pay',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Order received', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "Checkout &rarr; Order received" page.', 'woocommerce' ),
						'id'       => 'woocommerce_checkout_order_received_endpoint',
						'type'     => 'text',
						'default'  => 'order-received',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Add payment method', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "Checkout &rarr; Add payment method" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_add_payment_method_endpoint',
						'type'     => 'text',
						'default'  => 'add-payment-method',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Delete payment method', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the delete payment method page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_delete_payment_method_endpoint',
						'type'     => 'text',
						'default'  => 'delete-payment-method',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Set default payment method', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the setting a default payment method page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_set_default_payment_method_endpoint',
						'type'     => 'text',
						'default'  => 'set-default-payment-method',
						'desc_tip' => true,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'checkout_endpoint_options',
					),

					array(
						'title' => __( 'Account endpoints', 'woocommerce' ),
						'type'  => 'title',
						'desc'  => __( 'Endpoints are appended to your page URLs to handle specific actions on the accounts pages. They should be unique and can be left blank to disable the endpoint.', 'woocommerce' ),
						'id'    => 'account_endpoint_options',
					),

					array(
						'title'    => __( 'Orders', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; Orders" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_orders_endpoint',
						'type'     => 'text',
						'default'  => 'orders',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'View order', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; View order" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_view_order_endpoint',
						'type'     => 'text',
						'default'  => 'view-order',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Downloads', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; Downloads" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_downloads_endpoint',
						'type'     => 'text',
						'default'  => 'downloads',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Edit account', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; Edit account" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_edit_account_endpoint',
						'type'     => 'text',
						'default'  => 'edit-account',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Addresses', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; Addresses" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_edit_address_endpoint',
						'type'     => 'text',
						'default'  => 'edit-address',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Payment methods', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; Payment methods" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_payment_methods_endpoint',
						'type'     => 'text',
						'default'  => 'payment-methods',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Lost password', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the "My account &rarr; Lost password" page.', 'woocommerce' ),
						'id'       => 'woocommerce_myaccount_lost_password_endpoint',
						'type'     => 'text',
						'default'  => 'lost-password',
						'desc_tip' => true,
					),

					array(
						'title'    => __( 'Logout', 'woocommerce' ),
						'desc'     => __( 'Endpoint for the triggering logout. You can add this to your menus via a custom link: yoursite.com/?customer-logout=true', 'woocommerce' ),
						'id'       => 'woocommerce_logout_endpoint',
						'type'     => 'text',
						'default'  => 'customer-logout',
						'desc_tip' => true,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'account_endpoint_options',
					),
				)
			);

			if ( wc_site_is_https() ) {
				unset( $settings['unforce_ssl_checkout'], $settings['force_ssl_checkout'] );
			}
		} elseif ( 'legacy_api' === $current_section ) {
			$settings = apply_filters(
				'woocommerce_settings_rest_api', array(
					array(
						'title' => '',
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'legacy_api_options',
					),
					array(
						'title'   => __( 'Legacy API', 'woocommerce' ),
						'desc'    => __( 'Enable the legacy REST API', 'woocommerce' ),
						'id'      => 'woocommerce_api_enabled',
						'type'    => 'checkbox',
						'default' => 'no',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'legacy_api_options',
					),
				)
			);
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}

	/**
	 * Form method.
	 *
	 * @deprecated 3.4.4
	 * @param  string $method Method name.
	 * @return string
	 */
	public function form_method( $method ) {
		return 'post';
	}

	/**
	 * Notices.
	 */
	private function notices() {
		if ( isset( $_GET['section'] ) && 'webhooks' === $_GET['section'] ) { // WPCS: input var okay, CSRF ok.
			WC_Admin_Webhooks::notices();
		}
		if ( isset( $_GET['section'] ) && 'keys' === $_GET['section'] ) { // WPCS: input var okay, CSRF ok.
			WC_Admin_API_Keys::notices();
		}
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		if ( 'webhooks' === $current_section ) {
			WC_Admin_Webhooks::page_output();
		} elseif ( 'keys' === $current_section ) {
			WC_Admin_API_Keys::page_output();
		} else {
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		if ( apply_filters( 'woocommerce_rest_api_valid_to_save', ! in_array( $current_section, array( 'keys', 'webhooks' ), true ) ) ) {
			$settings = $this->get_settings( $current_section );

			// Prevent the T&Cs and checkout page from being set to the same page.
			if ( isset( $_POST['woocommerce_terms_page_id'], $_POST['woocommerce_checkout_page_id'] ) && $_POST['woocommerce_terms_page_id'] === $_POST['woocommerce_checkout_page_id'] ) { // WPCS: input var ok, CSRF ok.
				$_POST['woocommerce_terms_page_id'] = '';
			}

			WC_Admin_Settings::save_fields( $settings );

			if ( $current_section ) {
				do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}
}

/**
 * WC_Settings_Rest_API class.
 *
 * @deprecated 3.4 in favour of WC_Settings_Advanced.
 * @todo remove in 4.0.
 */
class WC_Settings_Rest_API extends WC_Settings_Advanced {} // @codingStandardsIgnoreLine.

return new WC_Settings_Advanced();
