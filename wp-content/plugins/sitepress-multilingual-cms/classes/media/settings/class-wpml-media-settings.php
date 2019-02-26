<?php

class WPML_Media_Settings {
	const ID = 'ml-content-setup-sec-media';

	private $wpdb;

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
		add_action( 'icl_tm_menu_mcsetup', array( $this, 'render' ) );
		add_filter( 'wpml_mcsetup_navigation_links', array( $this, 'mcsetup_navigation_links' ) );
	}

	public function enqueue_script() {
		wp_enqueue_script( 'wpml-media-settings', ICL_PLUGIN_URL . '/res/js/media/settings.js', array(), ICL_SITEPRESS_VERSION, true );
	}

	public function render() {
		include WPML_PLUGIN_PATH . '/classes/media/management.php';
	}

	public function mcsetup_navigation_links( array $mcsetup_sections ) {
		$mcsetup_sections[ self::ID ] = esc_html__( 'Media Translation', 'sitepress' );

		return $mcsetup_sections;
	}
}
