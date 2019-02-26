<?php

class WCML_Cart_Sync_Warnings{

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**
	 * @var Sitepress
	 */
	private $sitepress;
	/**
	 * @var array
	 */
	private $extensions_list = array(
		'WC_Subscriptions' => 'Woocommerce Subscriptions',
		'WC_Product_Addons'  => 'Woocommerce Product Addons',
		'WC_Bookings' => 'Woocommerce Bookings',
		'WC_Accommodation_Bookings_Plugin' => 'Woocommerce Accommodation Bookings',
		'WC_Product_Bundle' => 'Woocommerce Product Bundles',
		'WC_Composite_Products' => 'Woocommerce Composite Products',
		'WC_Dynamic_Pricing' => 'Woocommerce Dynamic Pricing',
	);

	public function __construct( $woocommerce_wpml, $sitepress ){
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress = $sitepress;
	}

	public function add_hooks(){

		if( $this->check_if_show_notices_needed() ){
			add_action( 'admin_notices', array( $this, 'show_cart_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );
		}

	}

	public function check_if_show_notices_needed(){

		$cart_sync_settings = $this->woocommerce_wpml->settings['cart_sync'];

		if(
			(
				$cart_sync_settings['lang_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_SYNC' ) ||
				$cart_sync_settings['currency_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_SYNC' )
			) &&
			!$this->woocommerce_wpml->settings[ 'dismiss_cart_warning' ] &&
			$this->get_list_of_active_extensions()
		) {
			return true;
		}

		return false;

	}

	public function register_styles(){
		wp_enqueue_style( 'wcml-cart-warning', $this->sitepress->get_wp_api()->constant( 'WCML_PLUGIN_URL' ) . '/res/css/wcml-cart-warning.css' );
	}

	public function show_cart_notice(){

		$list_of_extensions = $this->get_list_of_active_extensions();
		$request_url = esc_url( $_SERVER['REQUEST_URI'] );

		$admin_settings_url = esc_url( admin_url('admin.php?page=wpml-wcml&tab=settings#cart') );
		$documentation_link = esc_url( 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/clearing-cart-contents-when-language-or-currency-change/' );

		$reset_cart_strings[] = esc_html_x( 'Because of some elements in your site configuration, when the users switch the currency or the language on the front end, the cart content might not be synchronized correctly.', 'Reset cart option warning 1', 'woocommerce-multilingual' );
		$reset_cart_strings[] = esc_html_x( 'It is recommended that you %s with the option to reset the cart in a situation like this.', 'Reset cart option warning 2', 'woocommerce-multilingual' );

		$reset_cart_configure_link = '<strong><a href="' . $admin_settings_url . '">' . esc_html__( 'configure WooCommerce Multilingual', 'woocommerce-multilingual' ) . '</a></strong>';

		$reset_cart_message = $reset_cart_strings[0];
		$reset_cart_message .= '</p>';
		$reset_cart_message .= $list_of_extensions;
		$reset_cart_message .= '<p>';
		$reset_cart_message .= $reset_cart_strings[1];
		$reset_cart_message .= '<strong><a href="' . $documentation_link . '" target="_blank">' . esc_html__( 'More details', 'woocommerce-multilingual' ) . '</a></strong>';

		$message = '<div class="message error otgs-is-dismissible">';
		$message .= '<p>';
		$message .= sprintf( $reset_cart_message, $reset_cart_configure_link );
		$message .= '</p>';
		$message .= '<a class="notice-dismiss" href="' . $request_url . '&wcml_action=dismiss_cart_warning"><span class="screen-reader-text">' . esc_html__( 'Dismiss', 'woocommerce-multilingual' ) . '</a>';
		$message .= '</div>';

		echo $message;
	}

	public function get_list_of_active_extensions (){

		$html = '';

		foreach( $this->extensions_list as $extension_class => $display_name ){

			if( class_exists( $extension_class ) ){

				if( empty( $html ) ){
					$html .= '<ul>';
				}

				$html .= '<li>'.$display_name.'</li>';

			}
		}

		if( !empty( $html ) ){
			$html .= '</ul>';
		}

		return $html;

	}

}