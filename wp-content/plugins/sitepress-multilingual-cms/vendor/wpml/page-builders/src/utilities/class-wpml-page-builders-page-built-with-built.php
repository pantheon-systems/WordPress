<?php

class WPML_Page_Builders_Page_Built {

	private $config;

	public function __construct( WPML_Config_Built_With_Page_Builders $config ) {
		$this->config = $config;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return bool
	 */
	public function is_page_builder_page( WP_Post $post ) {
		$result      = false;
		$config_data = $this->config->get();

		if ( is_array( $config_data ) ) {
			foreach ( $config_data as $pattern ) {
				$result = (bool) preg_match_all( $pattern, $post->post_content );

				if ( $result ) {
					break;
				}
			}
		}

		return apply_filters( 'wpml_pb_is_page_builder_page', $result, $post );
	}
}