<?php

class WPML_ST_String_Tracking_AJAX_Factory implements IWPML_AJAX_Action_Loader {

	const ACTION_POSITION_IN_SOURCE = 'view_string_in_source';
	const ACTION_POSITION_IN_PAGE   = 'view_string_in_page';

	public function create() {

		if ( $this->is_string_position_view() ) {
			return new WPML_ST_String_Tracking_AJAX(
				$this->get_st_string_positions(),
				new WPML_Super_Globals_Validation(),
				filter_var( $_GET['action'], FILTER_SANITIZE_STRING )
			);
		}

		return null;
	}

	private function is_string_position_view() {
		return isset( $_GET['action'], $_GET['nonce'] )
			&& in_array(
					$_GET['action'],
					array(
						self::ACTION_POSITION_IN_SOURCE,
						self::ACTION_POSITION_IN_PAGE,
					),
					true
		       )
			&& wp_verify_nonce( $_GET['nonce'], $_GET['action'] );
	}

	/** @return WPML_ST_String_Positions_In_Page|WPML_ST_String_Positions_In_Source */
	private function get_st_string_positions() {
		global $sitepress, $wpdb;

		$string_positions_mapper = new WPML_ST_DB_Mappers_String_Positions( $wpdb );

		if ( self::ACTION_POSITION_IN_PAGE === $_GET['action'] ) {
			return new WPML_ST_String_Positions_In_Page(
				new WPML_ST_String_Factory( $wpdb ),
				$string_positions_mapper,
				$this->get_template_service()
			);
		} else {
			return new WPML_ST_String_Positions_In_Source(
				$sitepress,
				$string_positions_mapper,
				$this->get_template_service(),
				new WPML_WP_API()
			);
		}
	}

	private function get_template_service() {
		$loader = new Twig_Loader_Filesystem( array( WPML_ST_PATH . WPML_ST_String_Positions::TEMPLATE_PATH ) );

		$options = array();

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$options['debug'] = true;
		}

		$twig_env = new Twig_Environment( $loader, $options );

		return new WPML_Twig_Template( $twig_env );
	}
}
