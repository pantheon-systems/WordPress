<?php

/**
 * Class WPML_Page_Builders_App
 */
class WPML_Page_Builders_App {

	/**
	 * @var WPML_Page_Builders_Defined
	 */
	private $page_builder_plugins;

	/**
	 * WPML_Page_Builders_App constructor.
	 *
	 * @param WPML_Page_Builders_Defined $page_builder_plugins
	 */
	public function __construct( WPML_Page_Builders_Defined $page_builder_plugins ) {
		$this->page_builder_plugins = $page_builder_plugins;
	}

	public function add_hooks() {
		add_action( 'wpml_load_page_builders_integration', array( $this, 'load_integration' ) );
		add_filter( 'wpml_integrations_components', array( $this, 'add_components' ), 10, 1 );
	}

	public function load_integration() {
		if ( ! class_exists( 'WPML_ST_Package_Factory' ) ) {
			return;
		}

		$factories = array();

		foreach ( $this->page_builder_plugins->get_settings() as $page_builder_id => $page_builder ) {
			if ( $this->page_builder_plugins->has( $page_builder_id ) ) {
				$current_factory = $page_builder['factory'];
				$factories[] = new $current_factory();
			}
		}

		if ( $factories ) {
			foreach ( $factories as $factory ) {
				$integration = $factory->create();
				$integration->add_hooks();
			}
		}
	}

	public function add_components( $components ) {
		return $this->page_builder_plugins->add_components( $components );
	}
}