<?php

class WCML_Setup_Footer_UI extends WPML_Templates_Factory {

	/** @var bool */
	private $has_handler;

	/**
	 * WCML_Setup_Footer_UI constructor.
	 *
	 * @param bool $has_handler
	 */
	public function __construct( $has_handler ) {
		parent::__construct();
		$this->has_handler = $has_handler;
	}

	/**
	 * @return array
	 */
	public function get_model() {

		$model = array(
			'has_handler' => $this->has_handler,
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
		return '/setup/footer.twig';
	}


}