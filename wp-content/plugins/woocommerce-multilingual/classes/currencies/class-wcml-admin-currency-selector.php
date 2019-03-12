<?php
/**
 * Class WCML_Admin_Currency_Selector
 */
class WCML_Admin_Currency_Selector {

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var  WCML_Admin_Cookie */
	private $currency_cookie;

	const NONCE_KEY = 'wcml-admin-currency-selector';

	/**
	 * WCML_Admin_Currency_Selector constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
     * @param WCML_Admin_Cookie $currency_cookie
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, WCML_Admin_Cookie $currency_cookie ) {
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->currency_cookie = $currency_cookie;
	}

	public function add_hooks(){
	    global $pagenow;

		if ( is_admin() ) {

			if( $this->user_can_manage_woocommerce() ){
				add_action( 'init', array( $this, 'set_dashboard_currency' ) );
				add_action( 'wp_ajax_wcml_dashboard_set_currency', array( $this, 'set_dashboard_currency_ajax' ) );
				add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_dashboard_currency_symbol' ) );
            }

			if ( 'index.php' === $pagenow && version_compare( WOOCOMMERCE_VERSION, '2.4', '<' ) ) {
				add_action( 'admin_footer', array( $this, 'show_dashboard_currency_selector' ) );
			} else {
				add_action( 'woocommerce_after_dashboard_status_widget', array(
					$this,
					'show_dashboard_currency_selector'
				) );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
		}
    }

	/**
	 * @return bool
	 */
	private function user_can_manage_woocommerce() {
		return current_user_can( 'view_woocommerce_reports' ) ||
		       current_user_can( 'manage_woocommerce' ) ||
		       current_user_can( 'publish_shop_orders' );

	}

	public function load_js(){
	    wp_enqueue_script(
	            'wcml-admin-currency-selector',
		        $this->woocommerce_wpml->plugin_url() .
                    '/res/js/admin-currency-selector' . $this->woocommerce_wpml->js_min_suffix() . '.js',
                array('jquery'),
                $this->woocommerce_wpml->version()
        );
		wp_localize_script( 'wcml-admin-currency-selector', 'wcml_admin_currency_selector',
			array(
				'nonce' => wp_create_nonce( self::NONCE_KEY )
			)
		);

    }

	/**
	 * Add currency drop-down on dashboard page ( WooCommerce status block )
	 */
	public function show_dashboard_currency_selector() {

		$current_dashboard_currency = $this->get_cookie_dashboard_currency();

		$wc_currencies = get_woocommerce_currencies();
		$order_currencies = $this->woocommerce_wpml->multi_currency->orders->get_orders_currencies();
		?>
        <select id="dropdown_dashboard_currency" style="display: none; margin : 10px; ">
			<?php if ( empty( $order_currencies ) ): ?>
                <option value=""><?php _e( 'Currency - no orders found', 'woocommerce-multilingual' ) ?></option>
			<?php else: ?>
				<?php foreach ( $order_currencies as $currency => $count ): ?>

                    <option value="<?php echo $currency ?>" <?php echo $current_dashboard_currency == $currency ? 'selected="selected"' : ''; ?>>
						<?php echo $wc_currencies[ $currency ]; ?>
                    </option>

				<?php endforeach; ?>
			<?php endif; ?>
        </select>
		<?php
	}

	public function set_dashboard_currency_ajax() {
		$nonce = sanitize_text_field( $_POST['wcml_nonce'] );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, self::NONCE_KEY ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'woocommerce-multilingual' ), 403 );
		} else {
			$this->set_dashboard_currency( sanitize_text_field( $_POST['currency'] ) );
			wp_send_json_success();
		}
	}

	/**
	 * Set dashboard currency cookie
	 * @param string $currency_code
	 */
	public function set_dashboard_currency( $currency_code = '' ) {
		if ( ! $currency_code && ! headers_sent() ) {
			$order_currencies = $this->woocommerce_wpml->multi_currency->orders->get_orders_currencies();
			$currency_code    = get_woocommerce_currency();
			if ( ! isset( $order_currencies[ $currency_code ] ) ) {
				$currency_code = key( $order_currencies );
			}
		}

		$this->currency_cookie->set_value( $currency_code, time() + DAY_IN_SECONDS );
	}

	/**
	 * Get dashboard currency cookie
	 *
	 * @return string
	 */
	public function get_cookie_dashboard_currency() {

	    $currency = $this->currency_cookie->get_value();
	    if( null === $currency ){
		    $currency = get_woocommerce_currency();
        }

		return $currency;
	}

	/**
	 * Filter currency symbol on dashboard page
	 * @param string $currency Currency code
	 *
	 * @return string
	 */
	public function filter_dashboard_currency_symbol( $currency ) {
		global $pagenow;

		remove_filter( 'woocommerce_currency_symbol', array( $this, 'filter_dashboard_currency_symbol' ) );
		if ( 'index.php' === $pagenow && isset( $_COOKIE ['_wcml_dashboard_currency'] ) ) {
			$currency = get_woocommerce_currency_symbol( $_COOKIE ['_wcml_dashboard_currency'] );
		}
		add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_dashboard_currency_symbol' ) );

		return $currency;
	}

}