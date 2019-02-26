<?php

abstract class WPML_TM_MCS_Section_UI {

	private $id;
	private $title;

	public function __construct( $id, $title ) {
		$this->id = $id;
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function get_id() {
		return $this->id;
	}

	public function add_hooks() {
		add_filter( 'wpml_mcsetup_navigation_links', array( $this, 'mcsetup_navigation_links' ) );
	}

	public function mcsetup_navigation_links( array $mcsetup_sections ) {
		$mcsetup_sections[ $this->id ] = esc_html( $this->title );

		return $mcsetup_sections;
	}

	public function render() {
		$output = '';

		$output .= '<div class="wpml-section" id="' . esc_attr( $this->id ) . '">';
		$output .= '<div class="wpml-section-header">';
		$output .= '<h3>' . esc_html( $this->title ) . '</h3>';
		$output .= '</div>';
		$output .= '<div class="wpml-section-content">';
		$output .= $this->render_content();
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * @return string
	 */
	abstract protected function render_content();
}

