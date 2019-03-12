<?php

class WCML_Setup_Notice_UI extends WPML_Templates_Factory {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * @return array
	 */
	public function get_model() {

		$model = array(
			'text'      => array(
				'prepare'    => __( 'Prepare your WooCommerce store to run multilingual!', 'woocommerce-multilingual' ),
				'help'       => __( 'We need to help you with a few steps to turn your WooCommerce store multilingual. These steps include:', 'woocommerce-multilingual' ),
				'store'      => __( "Translating the 'store' pages", 'woocommerce-multilingual' ),
				'attributes' => __( "Choosing which attributes to make translatable", 'woocommerce-multilingual' ),
				'currencies' => __( "Choosing if you need multiple currencies", 'woocommerce-multilingual' ),
				'start'      => __( 'Start the Setup Wizard', 'woocommerce-multilingual' ),
				'skip'       => __( 'Skip', 'woocommerce-multilingual' )
			),
			'setup_url' => esc_url( admin_url( 'admin.php?page=wcml-setup' ) ),
			'skip_url'  => esc_url( wp_nonce_url( add_query_arg( 'wcml-setup-skip', 1 ), 'wcml_setup_skip_nonce', '_wcml_setup_nonce' ) ),
		);

		return $model;

	}

	protected function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/',
		);
	}

	/**
	 * @return string
	 */
	public function get_template() {
		return '/setup/notice.twig';
	}


}