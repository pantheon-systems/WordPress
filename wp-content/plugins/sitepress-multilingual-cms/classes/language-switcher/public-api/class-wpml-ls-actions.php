<?php

class WPML_LS_Actions extends WPML_LS_Public_API {

	public function init_hooks() {
		if ( $this->sitepress->get_setting( 'setup_complete' ) ) {
			add_action( 'wpml_language_switcher', array( $this, 'callback' ), 10, 2 );

			/**
			 * Backward compatibility
			 * @deprecated see 'wpml_language_switcher'
			 */
			add_action( 'icl_language_selector', array( $this, 'callback' ) );
			add_action( 'wpml_add_language_selector', array( $this, 'callback' ) );
			add_action( 'wpml_footer_language_selector', array( $this, 'callback' ) );
		}
	}

	/**
	 * @param array       $args
	 * @param string|null $twig_template
	 */
	public function callback( $args, $twig_template = null ) {
		if ( '' === $args ) {
			$args = array();
		}

		$args = $this->parse_legacy_actions( $args );
		$args = $this->convert_shortcode_args_aliases( $args );
		echo $this->render( $args, $twig_template );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	private function parse_legacy_actions( $args ) {
		$current_filter = current_filter();

		if ( in_array( $current_filter, array( 'icl_language_selector', 'wpml_add_language_selector' ) ) ) {
			$args['type'] = 'custom';
		} elseif ( 'wpml_footer_language_selector' === $current_filter ) {
			$args['type'] = 'footer';
		}

		return $args;
	}

}