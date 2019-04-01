<?php

/**
 * This code is inspired by WPML Widgets (https://wordpress.org/plugins/wpml-widgets/),
 * created by Jeroen Sormani
 *
 * @author OnTheGo Systems
 */
class WPML_Widgets_Support_Frontend implements IWPML_Action {
	private $current_language;

	/**
	 * WPML_Widgets constructor.
	 *
	 * @param string $current_language
	 */
	public function __construct( $current_language ) {
		$this->current_language = $current_language;
	}

	public function add_hooks() {
		add_filter( 'widget_display_callback', array( $this, 'display' ), - PHP_INT_MAX, 1 );
	}

	/**
	 * Get display status of the widget.
	 *
	 * @param array|bool $instance
	 *
	 * @return array|bool
	 */
	public function display( $instance ) {
		if ( ! $instance || $this->it_must_display( $instance ) ) {
			return $instance;
		}

		return false;
	}

	/**
	 * Returns display status of the widget as boolean.
	 *
	 * @param array $instance
	 *
	 * @return bool
	 */
	private function it_must_display( $instance ) {
		return ! array_key_exists( 'wpml_language', $instance )
		       || $instance['wpml_language'] === $this->current_language
		       || 'all' === $instance['wpml_language'];
	}
}
