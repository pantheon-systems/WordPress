<?php

class WPML_PB_Fix_Maintenance_Query {

	const AFTER_TEMPLATE_APPLY = 12;

	public function add_hooks() {
		add_action( 'template_redirect', array( $this, 'fix_global_query' ), self::AFTER_TEMPLATE_APPLY );
	}

	public function fix_global_query() {
		if (
			class_exists( '\Elementor\Maintenance_Mode' ) &&
			isset( $GLOBALS['post']->ID ) &&
			(int) \Elementor\Maintenance_Mode::get( 'template_id' ) === $GLOBALS['post']->ID
		) {
			$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
		}
	}
}
