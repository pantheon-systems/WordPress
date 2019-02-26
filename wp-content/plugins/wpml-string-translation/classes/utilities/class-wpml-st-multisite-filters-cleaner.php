<?php

class WPML_ST_Multisite_Filters_Cleaner {

	private $filters;

	public function add_hooks() {
		add_action( 'switch_blog', array( $this, 'handle_filters' ) );
	}

	public function handle_filters() {
		foreach ( $this->get_hooks() as $hook ) {
			if ( ! is_plugin_active( basename( WPML_ST_PATH ) . '/plugin.php' ) ) {
				remove_filter( $hook['tag'], $hook['callback'], $hook['priority'] );
			} elseif ( ! has_filter( $hook['tag'], $hook['callback'] ) ) {
				add_filter( $hook['tag'], $hook['callback'], $hook['priority'] );
			}
		}
	}

	private function get_hooks() {
		if ( ! $this->filters ) {
			$this->filters = array(
				array(
					'tag'      => 'option_blogname',
					'callback' => 'wpml_st_blog_title_filter',
					'priority' => 10,
				),
				array(
					'tag'      => 'option_blogdescription',
					'callback' => 'wpml_st_blog_description_filter',
					'priority' => 10,
				)
			);
		}

		return $this->filters;
	}
}