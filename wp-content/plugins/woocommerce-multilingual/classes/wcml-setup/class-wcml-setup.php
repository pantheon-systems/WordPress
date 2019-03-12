<?php

/**
 * Class WCML_Setup
 */
class WCML_Setup {

	/** @var WCML_Setup_UI */
	private $ui;
	/** @var WCML_Setup_Handlers */
	private $handlers;
	/** @var  array */
	private $steps;
	/** @var  string */
	private $step;
	/** @var  woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var  SitePress */
	private $sitepress;
	/** @var  string */
	private $next_step = false;

	/**
	 * WCML_Setup constructor.
	 *
	 * @param WCML_Setup_UI $ui
	 * @param WCML_Setup_Handlers $handlers
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 */
	public function __construct( WCML_Setup_UI $ui, WCML_Setup_Handlers $handlers, woocommerce_wpml $woocommerce_wpml, SitePress $sitepress ) {

		$this->ui               = $ui;
		$this->handlers         = $handlers;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;

		$include_translation_options_step = $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'ICL_SITEPRESS_VERSION' ), '3.9.0', '>=' );

		$this->steps = array(
			'introduction'   => array(
				'name'    => __( 'Introduction', 'woocommerce-multilingual' ),
				'view'    => new WCML_Setup_Introduction_UI(
					$this->woocommerce_wpml,
					$this->step_url( 'store-pages' )
				),
				'handler' => ''
			),
			'store-pages'    => array(
				'name'    => __( 'Store Pages', 'woocommerce-multilingual' ),
				'view'    => new WCML_Setup_Store_Pages_UI(
					$this->woocommerce_wpml,
					$this->sitepress,
					$this->step_url( 'attributes' )
				),
				'handler' => array( $this->handlers, 'install_store_pages' ),
			),
			'attributes'     => array(
				'name'    => __( 'Global Attributes', 'woocommerce-multilingual' ),
				'view'    => new WCML_Setup_Attributes_UI(
					$this->woocommerce_wpml,
					$this->step_url( 'multi-currency' )
				),
				'handler' => array( $this->handlers, 'save_attributes' )
			),
			'multi-currency' => array(
				'name'    => __( 'Multiple Currencies', 'woocommerce-multilingual' ),
				'view'    => new WCML_Setup_Multi_Currency_UI(
					$this->woocommerce_wpml,
					$this->step_url( $include_translation_options_step ? 'translation-options' :'ready' )
				),
				'handler' => array( $this->handlers, 'save_multi_currency' )
			)
		);

		if ( $include_translation_options_step ) {
			$this->steps['translation-options'] = array(
				'name'    =>  __( 'Translation Options', 'woocommerce-multilingual' ),
				'view'    => new WCML_Setup_Translation_Options_UI(
					$this->woocommerce_wpml,
					$this->step_url( 'ready' )
				),
				'handler' => array( $this->handlers, 'save_translation_options' )
			);
		}

		$this->steps['ready'] = array(
			'name'    => __( 'Ready!', 'woocommerce-multilingual' ),
			'view'    => new WCML_Setup_Ready_UI( $this->woocommerce_wpml ),
			'handler' => ''
		);


	}

	public function add_hooks() {
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_init', array( $this, 'wizard' ) );
			add_action( 'admin_init', array( $this, 'handle_steps' ), 0 );
			add_filter( 'wp_redirect', array( $this, 'redirect_filters' ) );
		}

		if ( ! $this->has_completed() ) {
			$this->ui->add_wizard_notice_hook();
			add_action( 'admin_init', array( $this, 'skip_setup' ), 1 );
		}
	}

	public function exit_wrapper(){
		exit;
	}

	public function setup_redirect() {
		if ( get_transient( '_wcml_activation_redirect' ) ) {
			delete_transient( '_wcml_activation_redirect' );

			if ( ! $this->do_not_redirect_to_setup() && ! $this->has_completed() ) {
				wp_safe_redirect( admin_url( 'index.php?page=wcml-setup' ) );
				add_filter( 'wp_die_handler', array( $this, 'exit_wrapper' ) );
				wp_die();
			}
		}
	}

	private function do_not_redirect_to_setup() {

		$woocommerce_notices       = get_option( 'woocommerce_admin_notices', array() );
		$woocommerce_setup_not_run = in_array( 'install', $woocommerce_notices, true );

		return $this->is_wcml_setup_page() ||
		       is_network_admin() ||
		       isset( $_GET['activate-multi'] ) ||
		       ! current_user_can( 'manage_options' ) ||
		       $woocommerce_setup_not_run;

	}

	/**
	 * @return bool
	 */
	private function is_wcml_setup_page() {
		return isset( $_GET['page'] ) && 'wcml-setup' === $_GET['page'];
	}

	/**
	 * @return bool
	 */
	private function is_wcml_admin_page() {
		return isset( $_GET['page'] ) && 'wcml' === $_GET['page'];
	}

	public function wizard() {

		$this->splash_wizard_on_wcml_pages();

		if ( ! $this->is_wcml_setup_page() ){
			return;
		}

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		wp_enqueue_style( 'otgs-ico', ICL_PLUGIN_URL . '/res/css/otgs-ico.css', null, ICL_SITEPRESS_VERSION );
		wp_enqueue_style( 'wcml-setup', WCML_PLUGIN_URL . '/res/css/wcml-setup.css', array(
			'dashicons',
			'install'
		), WCML_VERSION );

		wp_enqueue_script( 'wcml-setup', WCML_PLUGIN_URL . '/res/js/wcml-setup.js', array( 'jquery' ), WCML_VERSION );


		$this->ui->setup_header( $this->steps, $this->step );

		$steps_keys = array_keys( $this->steps );
		$step_index      = array_search( $this->step, $steps_keys );
		$this->next_step = isset( $steps_keys[ $step_index + 1 ] ) ? $steps_keys[ $step_index + 1 ] : '';

		$this->ui->setup_steps( $this->steps, $this->step );
		$this->ui->setup_content( $this->steps[ $this->step ]['view'] );
		$this->ui->setup_footer( ! empty( $this->steps[ $this->step ]['handler'] ) );

		if ( $this->step == 'ready' ) {
			$this->complete_setup();
		}

		wp_die();
	}

	private function splash_wizard_on_wcml_pages() {

		if ( isset( $_GET['src'] ) && $_GET['src'] == 'setup_later' ) {
			$this->woocommerce_wpml->settings['set_up_wizard_splash'] = 1;
			$this->woocommerce_wpml->update_settings();
		}

		if ( $this->is_wcml_admin_page() && ! $this->has_completed() && empty( $this->woocommerce_wpml->settings['set_up_wizard_splash'] ) ) {
			wp_redirect( 'admin.php?page=wcml-setup' );
			add_filter( 'wp_die_handler', array( $this, 'exit_wrapper' ) );
			wp_die();
		}
	}

	public function skip_setup() {

		if ( isset( $_GET['wcml-setup-skip'] ) && isset( $_GET['_wcml_setup_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wcml_setup_nonce'], 'wcml_setup_skip_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-multilingual' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( "Cheatin' huh?", 'woocommerce' ) );
			}

			$this->complete_setup();
			remove_filter( 'admin_notices', array( $this, 'wizard_notice' ) );

			delete_transient( '_wcml_activation_redirect' );
		}

	}

	public function complete_setup() {
		$this->woocommerce_wpml->settings['set_up_wizard_run']    = 1;
		$this->woocommerce_wpml->settings['set_up_wizard_splash'] = 1;
		$this->woocommerce_wpml->update_settings();
	}

	private function has_completed() {
		return ! empty( $this->woocommerce_wpml->settings['set_up_wizard_run'] );
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 */
	public function redirect_filters( $url ) {
		if ( isset( $_POST['next_step_url'] ) && $_POST['next_step_url'] ) {
			$url = sanitize_text_field( $_POST['next_step_url'] );
		}
		return $url;
	}

	/**
	 * @param string $step
	 *
	 * @return string
	 */
	private function step_url( $step ){
		$url = admin_url( 'admin.php?page=wcml-setup&step=' . $step );
		return $url;
	}

	/**
	 * @return string|void
	 */
	public function next_step_url() {
		$url = $this->step_url( $this->next_step );
		return $url;
	}

	/**
	 * @param string $step
	 *
	 * @return mixed
	 */
	private function get_handler( $step ) {
		$handler = ! empty( $this->steps[ $step ]['handler'] ) ? $this->steps[ $step ]['handler'] : '';
		return $handler;
	}

	public function handle_steps() {
		if ( isset( $_POST['handle_step'] ) && $_POST['nonce'] == wp_create_nonce( $_POST['handle_step'] ) ) {
			$step_name = sanitize_text_field( $_POST['handle_step'] );
			if ( $handler = $this->get_handler( $step_name ) ) {
				if( is_callable( $handler, true ) ){
					call_user_func( $handler, $_POST );
				}
			}
		}
	}

}