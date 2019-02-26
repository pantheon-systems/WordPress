<?php

/**
 * Class WPML_Compatibility_Plugin_Visual_Composer
 *
 * @author OnTheGoSystems
 */
class WPML_Compatibility_Plugin_Visual_Composer {

	/** @var WPML_Debug_BackTrace $debug_backtrace */
	private $debug_backtrace;

	/** @var array $filters_to_restore */
	private $filters_to_restore = array();

	/**
	 * WPML_Compatibility_Plugin_Visual_Composer constructor.
	 *
	 * @param WPML_Debug_BackTrace $debug_backtrace
	 */
	public function __construct( WPML_Debug_BackTrace $debug_backtrace ) {
		$this->debug_backtrace = $debug_backtrace;
	}

	public function add_hooks() {
		$this->prevent_registering_widget_strings_twice();
	}

	private function prevent_registering_widget_strings_twice() {
		add_filter( 'widget_title', array( $this, 'suspend_vc_widget_translation' ), - PHP_INT_MAX );
		add_filter( 'widget_text', array( $this, 'suspend_vc_widget_translation' ), - PHP_INT_MAX );
		add_filter( 'widget_title', array( $this, 'restore_widget_translation' ), PHP_INT_MAX );
		add_filter( 'widget_text', array( $this, 'restore_widget_translation' ), PHP_INT_MAX );
		add_filter( 'wpml_pb_shortcode_encode', array( $this, 'vc_safe_encode' ), 10, 2 );
		add_filter( 'wpml_pb_shortcode_decode', array( $this, 'vc_safe_decode' ), 10, 3 );
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function suspend_vc_widget_translation( $text ) {
		if ( $this->debug_backtrace->is_function_in_call_stack( 'vc_do_shortcode' )
			&& ! $this->debug_backtrace->is_function_in_call_stack( 'dynamic_sidebar' )
		) {
			$filter           = new stdClass();
			$filter->hook     = current_filter();
			$filter->name     = 'icl_sw_filters_' . $filter->hook;
			$filter->priority = has_filter( $filter->hook, $filter->name );

			if ( false !== $filter->priority ) {
				remove_filter( $filter->hook, $filter->name, $filter->priority );
				$this->filters_to_restore[] = $filter;
			}
		}

		return $text;
	}

	/**
	 * @param string $text
	 *
	 * @return mixed
	 */
	public function restore_widget_translation( $text ) {
		foreach ( $this->filters_to_restore as $filter ) {
			add_filter( $filter->hook, $filter->name, $filter->priority );
		}

		return $text;
	}

	function vc_safe_encode( $string, $encoding ) {
		if ( 'vc_safe' === $encoding ) {
				if ( is_array( $string ) ) {
						$string = implode( ',', $string );
				}
				$string = '#E-8_' . base64_encode( rawurlencode( $string ) );
		}

		return $string;
	}

	function vc_safe_decode( $string, $encoding, $encoded_string ) {
		if ( 'vc_safe' === $encoding ) {
				$values = rawurldecode( base64_decode( substr( $string, 5 ) ) );
				$values = explode( ',', $values );
				$string = array();
				foreach ( $values as $index => $value ) {
						$string[ $index ] = array( 'value' => $value, 'translate' => true );
				}
		}

		return $string;
	}

}
