<?php

class WPML_Elementor_Translate_IDs implements IWPML_Action {

	/** @var WPML_Debug_BackTrace */
	private $debug_backtrace;

	public function __construct( WPML_Debug_BackTrace $debug_backtrace ) {
		$this->debug_backtrace = $debug_backtrace;
	}

	public function add_hooks() {
		add_filter( 'elementor/theme/get_location_templates/template_id', array( $this, 'translate_theme_location_template_id' ) );
		add_filter( 'elementor/theme/get_location_templates/condition_sub_id', array( $this, 'translate_location_condition_sub_id' ), 10, 2 );
		add_filter( 'elementor/documents/get/post_id', array(
			$this,
			'translate_template_id'
		) );
		add_filter( 'elementor/frontend/builder_content_data', array( $this, 'translate_global_widget_ids' ), 10, 2 );
	}

	public function translate_theme_location_template_id( $template_id ) {
		return $this->translate_id( $template_id );
	}

	/**
	 * @param int|string $sub_id
	 * @param array      $parsed_condition
	 *
	 * @return int|string
	 */
	public function translate_location_condition_sub_id( $sub_id, $parsed_condition ) {
		/**
		 * `$sub_name` gives a context for the `$sub_id`, it can be either:
		 * - `child_of`
		 * - `in_{taxonomy}`
		 * - `{post_type}`
		 * - `{taxonomy}`
		 */
		$sub_name = isset( $parsed_condition['sub_name'] ) ? $parsed_condition['sub_name'] : null;

		if ( (int) $sub_id > 0 && $sub_name ) {
			$element_type = $sub_name;

			if ( 'child_of' === $sub_name ) {
				$element_type = get_post_type( $sub_id );
			} elseif ( 0 === strpos( $sub_name, 'in_' ) ) {
				$element_type = preg_replace( '/^in_/', '', $sub_name );
			}

			$sub_id = $this->translate_id( $sub_id, $element_type );
		}

		return $sub_id;
	}

	public function translate_template_id( $template_id ) {
		if ( $this->is_WP_widget_call() || $this->is_shortcode_call() || $this->is_template_widget_call() ) {
			$template_id = $this->translate_id( $template_id );
		}

		return $template_id;
	}

	private function is_WP_widget_call() {
		return $this->debug_backtrace->is_class_function_in_call_stack(
			'ElementorPro\Modules\Library\WP_Widgets\Elementor_Library',
			'widget' );
	}

	private function is_shortcode_call() {
		return $this->debug_backtrace->is_class_function_in_call_stack(
			'ElementorPro\Modules\Library\Classes\Shortcode',
			'shortcode' );
	}

	private function is_template_widget_call() {
		return $this->debug_backtrace->is_class_function_in_call_stack(
			'ElementorPro\Modules\Library\Widgets\Template',
			'render' );
	}

	public function translate_global_widget_ids( $data_array, $post_id ) {
		foreach ( $data_array as &$data ) {
			if ( isset( $data['elType'] ) && 'widget' === $data['elType'] ) {
				if ( 'global' === $data['widgetType'] ) {
					$data['templateID'] = $this->translate_id( $data['templateID'] );
				} elseif ( 'template' === $data['widgetType'] ) {
					$data['settings']['template_id'] = $this->translate_id( $data['settings']['template_id'] );
				}
			}
			$data['elements'] = $this->translate_global_widget_ids( $data['elements'], $post_id );
		}

		return $data_array;
	}

	/**
	 * @param int    $element_id
	 * @param string $element_type
	 *
	 * @return int
	 */
	private function translate_id( $element_id, $element_type = null ) {
		if ( ! $element_type ) {
			$element_type = get_post_type( $element_id );
		}

		return apply_filters( 'wpml_object_id', $element_id, $element_type, true );
	}
}
