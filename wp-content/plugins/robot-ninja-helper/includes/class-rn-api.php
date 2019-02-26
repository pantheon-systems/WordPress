<?php
/**
 * Robot Ninja API Class
 *
 * @author 	Prospress
 * @since 	1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_API {

	/**
	 * Init Robot Ninja API Class
	 *
	 * @since 1.0
	 */
	public static function init() {
		// Register our custom routes
		add_action( 'rest_api_init', __CLASS__ . '::register_custom_routes' );

		// Register additional fields to be added to the GET system status endpoint
		add_action( 'rest_api_init', __CLASS__ . '::register_system_status_field', 15 );

		// Add the REST API link header to requests if it's been removed
		add_action( 'template_redirect', __CLASS__ . '::force_output_link_header', 15 );
	}

	/**
	 * Register Custom Routes
	 *
	 * @since 1.0
	 */
	public static function register_custom_routes() {
		// A simple GET route we can check quickly to determine if the plugin is activated as part of service discovery/onboarding
		register_rest_route( 'rn/helper', '/status', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => __CLASS__ . '::return_plugin_status',
		) );
	}

	/**
	 * Register the additional fields for the WC System Status endpoint.
	 *
	 * @since 1.0
	 */
	public static function register_system_status_field() {
		register_rest_field( 'system_status',
			'robot_ninja_data',
			array(
				'get_callback'    => __CLASS__ . '::add_robot_ninja_data',
				'update_callback' => null,
				'schema'          => array(
					'description' => __( 'Additional store data for Robot Ninja tests', 'robot-ninja-helper' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			)
		);
	}

	/**
	 * Add additional field to the system status endpoint response for robot ninja
	 *
	 * @since 1.0
	 * @param mixed $response
	 * @param string $field_name
	 * @param WP_Rest_Request
	 * @return Object
	 */
	public static function add_robot_ninja_data( $response, $field_name, $request ) {
		// add some products for robot ninja to test
		$info = new stdClass();
		$info->guest_checkout_enabled       = ( 'yes' == get_option( 'woocommerce_enable_guest_checkout' ) ) ? true : false;
		$info->shop_page_display            = ( '' === get_option( 'woocommerce_shop_page_display' ) ) ? 'products' : get_option( 'woocommerce_shop_page_display' );
		$info->signup_from_checkout_enabled = ( 'yes' === get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) ) ? true : false;
		$info->add_to_cart_redirect_enabled = ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) ? true : false;

		$info->products = $info->pages = array();

		$info->checkout_settings  = self::get_checkout_settings();
		$info->themes             = self::get_theme_settings();
		$info->gateways           = RN_Gateways_Settings::get_enabled_gateway_settings();
		$product_visibility_terms = wc_get_product_visibility_term_ids();

		// get private parent posts IDs
		$private_product_ids = get_posts( array(
			'fields'         => 'ids',
			'post_type'      => 'product',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'post_parent'    => 0,
		) );

		// get password protected parent post IDs
		$password_protected_product_ids = get_posts( array(
			'fields'         => 'ids',
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post_parent'    => 0,
			'has_password'   => true,
		) );

		// get the cheapest product to test with a price above 0.
		$cheapest_products = new WP_Query(
			array(
				'post_type'           => array( 'product', 'product_variation' ),
				'post_status'         => 'publish',
				'meta_key'            => '_price',
				'orderby'             => 'meta_value_num',
				'order'               => 'ASC',
				'posts_per_page'      => 1,
				'post_parent__not_in' => array_merge( $private_product_ids, $password_protected_product_ids ),
				'has_password'        => false,
				'fields'              => 'id=>parent',
				'no_found_rows'       => true,
				'meta_query'          => array(
					'relation'      => 'AND',
					array(
						'key'       => '_price',
						'value'     => 0,
						'compare'   => '>',
					),
					array(
						'key'       => '_stock_status',
						'value'     => 'instock',
						'compare'   => '=',
					),
				),
				'tax_query' => array(
					array( // don't return products set to hidden or excluded from catalog and are out of stock
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => array( $product_visibility_terms['exclude-from-catalog'], $product_visibility_terms['outofstock'] ),
						'operator' => 'NOT IN',
					),
				),
			)
		);

		foreach ( $cheapest_products->posts as $product ) {
			$product_id                    = ( ! empty( $product->post_parent ) ) ? $product->post_parent : $product->ID;
			$info->products[ $product_id ] = get_permalink( $product->ID );
		}

		$i = 0;
		foreach ( array( 'shop', 'cart', 'checkout', 'my_account' ) as $page_key ) {
			$page_info = $response['pages'][ $i ];

			// only attach the page URLs if the page exists and is visible
			if ( $page_info['page_set'] && $page_info['page_exists'] && $page_info['page_visible'] ) {
				$info->pages[ $page_key ] = get_permalink( $page_info['page_id'] );
			}
			$i++;
		}

		return $info;
	}

	/**
	 * Simple callback returning that the plugin is activated
	 *
	 * @since  1.0
	 * @param  WP_Rest_Request $request
	 * @return WP_REST_Response
	 */
	public static function return_plugin_status( $request ) {

		$data = array(
			'status'                      => 'activated',
			'php_auth_user'               => false,
			'php_auth_pw'                 => false,
			'http_authorization'          => false,
			'redirect_http_authorization' => false,
			'is_ssl'                      => ( function_exists( 'is_ssl' ) && is_ssl() ) ? true : false,
		);

		if ( ! empty( $_SERVER['PHP_AUTH_USER'] ) ) {
			$data['php_auth_user'] = true;
		}

		if ( ! empty( $_SERVER['PHP_AUTH_PW'] ) ) {
			$data['php_auth_pw'] = true;
		}

		if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			$data['http_authorization'] = true;
		}

		if ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
			$data['redirect_http_authorization'] = true;
		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Fetch checkout settings for a store to help with checkout testing in Robot Ninja
	 *
	 * @since 1.5
	 */
	public static function get_checkout_settings() {
		$settings = array();

		// terms and conditions
		$settings['showing_terms'] = apply_filters( 'woocommerce_checkout_show_terms', true );

		// wc 3.4+ has a better helper function to determine if the terms and conditions checkbox is enabled
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.4', '>=' ) ) {
			$settings['terms_checkbox_enabled'] = wc_terms_and_conditions_checkbox_enabled();
		} else {
			$terms_page_id                      = wc_get_page_id( 'terms' );
			$settings['terms_checkbox_enabled'] = ( $terms_page_id > 0 ) ? true : false;
		}

		// checkout field options - if pre wc3.4, default the values to default WC behaviour
		$settings['billing_company_field']   = get_option( 'woocommerce_checkout_company_field', 'optional' );
		$settings['billing_address_2_field'] = get_option( 'woocommerce_checkout_address_2_field', 'optional' );
		$settings['billing_phone_field']     = get_option( 'woocommerce_checkout_phone_field', 'required' );

		return $settings;
	}

	/**
	 * Fetch theme settings to help with better theme support in Robot Ninja
	 *
	 * @since 1.7
	 * @return array
	 */
	public static function get_theme_settings() {
		return apply_filters( 'rn_helper_theme_settings', array(), wp_get_theme() );
	}

	/**
	 * Make sure the API link header is available on REST responses if it's been inappropriately removed
	 *
	 * @since 1.5.1
	 */
	public static function force_output_link_header() {
		if ( ! has_action( 'template_redirect', 'rest_output_link_header' ) && function_exists( 'rest_output_link_header' ) ) {
			rest_output_link_header();
		}
	}
}
RN_API::init();
