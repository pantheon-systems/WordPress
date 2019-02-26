<?php

abstract class WPML_Wizard extends WPML_Twig_Template_Loader {

	const TEMPLATE_PATH = '/templates/wizard';
	const NONCE = 'wpml_wizard_fetch_content';

	private $model = array();

	public function __construct() {
		parent::__construct( array( WPML_PLUGIN_PATH . self::TEMPLATE_PATH ) );
	}

	abstract protected function initialize_steps();
	abstract protected function enqueue_scripts();

	public function render() {
		$this->initialize_steps();
		$this->initialize_strings();
		$this->set_nonce();

		$this->enqueue_main_script();
		$this->enqueue_scripts();

		return $this->get_template()->show( $this->model, 'wizard.twig' );
	}

	protected function add_step( $slug, $title ) {
		$this->model['steps'][] = array( 'slug' => $slug, 'title' => $title );
	}

	protected function set_current_step( $current_step_slug ) {
		$this->model['current_step_slug' ] = $current_step_slug;
	}

	protected function initialize_strings() {
		$this->model['strings'] = array(
			'back' => __( '<<< Back', 'sitepress' ),
			'next' => __( 'Next >>>', 'sitepress' ),
			'finished' => __( 'Finished', 'sitepress' ),
		);
	}

	private function set_nonce() {
		$this->model['nonce'] = wp_create_nonce( self::NONCE );
	}

	private function enqueue_main_script() {
		wp_register_script( 'wpml-wizard', ICL_PLUGIN_URL . '/res/js/wizard.js', array( 'jquery' ), ICL_SITEPRESS_VERSION, true );
		wp_enqueue_script( 'wpml-wizard' );

		wp_register_style( 'wpml-wizard', ICL_PLUGIN_URL . '/res/css/wpml-wizard.css' );
		wp_enqueue_style( 'wpml-wizard' );
	}
}