<?php

class WCML_Setup_UI {

	/** @var  woocommerce_wpml */
	private $woocommerce_wpml;

	/**
	 * WCML_Setup_UI constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks(){
		if ( current_user_can( 'manage_options' ) && $this->is_wcml_setup_page() ) {
            add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		}
    }

    public function add_wizard_notice_hook(){
	    add_filter( 'admin_notices', array( $this, 'wizard_notice' ) );
    }

	/**
	 * @return bool
	 */
	private function is_wcml_setup_page() {
		return isset( $_GET['page'] ) && $_GET['page'] === 'wcml-setup';
	}

	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wcml-setup', '' );
	}

	/**
	 * @param array $steps
	 * @param string $step
	 */
	public function setup_header( $steps, $step ) {
		set_current_screen( 'wcml-setup' );
		$header = new WCML_Setup_Header_UI( $steps, $step );
		echo $header->get_view();
	}

	/**
	 * @param array $steps
	 * @param string $current_step
	 */
	public function setup_steps( array $steps, $current_step ) {
		$step_keys = array_keys( $steps );
		array_shift( $steps );
		?>
        <ol class="wcml-setup-steps">
			<?php foreach ( $steps as $step_key => $step ) : ?>
                <li class="<?php
				if ( $step_key === $current_step ) {
					echo 'active';
				} elseif ( array_search( $current_step, $step_keys ) > array_search( $step_key, $step_keys ) ) {
					echo 'done';
				}
				?>"><?php echo esc_html( $step['name'] ); ?></li>
			<?php endforeach; ?>
        </ol>
		<?php
	}

    /**
    * @param mixed $view
    */
    public function setup_content(  $view ){

        echo '<div class="wcml-setup-content">';
        echo $view->get_view();
        echo '</div>';

    }

	/**
	 * @param bool $has_handler
	 */
	public function setup_footer( $has_handler = false ) {
		$footer = new WCML_Setup_Footer_UI( $has_handler );
		echo $footer->get_view();
	}

	public function wizard_notice() {
		wp_enqueue_style( 'wcml-setup-wizard-notice', WCML_PLUGIN_URL . '/res/css/wcml-setup-wizard-notice.css' );
		$notice = new WCML_Setup_Notice_UI();
		echo $notice->get_view();
	}

}