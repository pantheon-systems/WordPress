<?php

class WPML_Media_Screen_Options implements IWPML_Action {

	/**
	 * @var array
	 */
	private $options = array();

	/**
	 * WPML_Media_Screen_Options constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options ) {
		$this->options = $options;
	}

	public function add_hooks() {
		add_action( 'load-wpml_page_wpml-media', array( $this, 'add_options' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	public function add_options() {
		foreach ( $this->options as $option ) {
			add_screen_option( $option['key'], $option['args'] );
		}
	}

	public function set_screen_option( $status, $option_name, $value ) {
		if ( $this->is_valid_option( $option_name ) ) {
			update_option( $option_name, $value );
		}
	}

	private function is_valid_option( $option_name ) {
		$valid = false;
		foreach ( $this->options as $option ) {
			if ( $option_name === $option['args']['option'] ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}

}