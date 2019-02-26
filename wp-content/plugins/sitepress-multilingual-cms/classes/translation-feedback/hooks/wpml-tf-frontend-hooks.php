<?php

/**
 * Class WPML_TF_Frontend_Hooks
 * @author OnTheGoSystems
 */
class WPML_TF_Frontend_Hooks implements IWPML_Action {

	/** @var WPML_TF_Frontend_Feedback_View $feedback_view */
	private $feedback_view;

	/** @var WPML_TF_Frontend_Scripts $scripts */
	private $scripts;

	/** @var WPML_TF_Frontend_Styles $styles */
	private $styles;

	/**
	 * WPML_TF_Frontend_Hooks constructor.
	 *
	 * @param WPML_TF_Frontend_Feedback_View $feedback_view
	 * @param WPML_TF_Frontend_Scripts       $scripts
	 * @param WPML_TF_Frontend_Styles        $styles
	 */
	public function __construct(
		WPML_TF_Frontend_Feedback_View $feedback_view,
		WPML_TF_Frontend_Scripts $scripts,
		WPML_TF_Frontend_Styles $styles
	) {
		$this->feedback_view = $feedback_view;
		$this->scripts       = $scripts;
		$this->styles        = $styles;
	}

	/**
	 * method init
	 */
	public function add_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_action' ) );
		add_action( 'wp_footer', array( $this, 'render_feedback_form' ) );
		add_action( 'wpml_tf_feedback_open_link', array( $this, 'render_custom_form_open_link' ) );
	}

	/**
	 * method enqueue_scripts_action
	 */
	public function enqueue_scripts_action() {
		$this->scripts->enqueue();
		$this->styles->enqueue();
	}

	/**
	 * method render_feedback_form
	 */
	public function render_feedback_form() {
		echo $this->feedback_view->render_open_button();
		echo $this->feedback_view->render_form();
	}

	/** @param string|array $args */
	public function render_custom_form_open_link( $args ) {
		echo $this->feedback_view->render_custom_open_link( $args );
	}
}
