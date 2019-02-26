<?php

class WPML_ST_String_Tracking_AJAX implements IWPML_Action {

	/** @var WPML_ST_String_Positions $string_position */
	private $string_position;

	/** @var WPML_Super_Globals_Validation $globals_validation */
	private $globals_validation;

	/** @var string $action */
	private $action;

	/**
	 * WPML_ST_String_Tracking_AJAX constructor.
	 *
	 * @param WPML_ST_String_Positions      $string_position
	 * @param WPML_Super_Globals_Validation $globals_validation
	 * @param string                        $action
	 */
	public function __construct(
		WPML_ST_String_Positions $string_position,
		WPML_Super_Globals_Validation $globals_validation,
		$action
	) {
		$this->string_position    = $string_position;
		$this->globals_validation = $globals_validation;
		$this->action             = $action;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_' . $this->action, array( $this, 'render_string_position' ) );
	}

	public function render_string_position() {
		$string_id = $this->globals_validation->get( 'string_id', FILTER_SANITIZE_NUMBER_INT );
		$this->string_position->dialog_render( $string_id );
		wp_die();
	}
}
