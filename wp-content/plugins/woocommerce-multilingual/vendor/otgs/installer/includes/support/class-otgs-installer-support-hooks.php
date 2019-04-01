<?php
	
class OTGS_Installer_Support_Hooks {

	private $template_factory;

	public function __construct( OTGS_Installer_Support_Template_Factory $template_factory ) {
		$this->template_factory = $template_factory;
	}

	public function add_hooks() {
		add_action( 'admin_menu', array( $this, 'add_support_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'otgs_render_installer_support_link', array( $this, 'render_link' ) );
	}

	public function add_support_page() {
		add_submenu_page(
			'commercial',
			__( 'Installer Support', 'installer' ),
			'Installer Support',
			'manage_options',
			'otgs-installer-support',
			array( $this, 'render_support_page' )
		);
	}

	public function render_support_page() {
		$this->template_factory->create()->show();
	}

	public function enqueue_scripts( $hook ) {
		if ( 'admin_page_otgs-installer-support' === $hook ) {
			wp_enqueue_style( 'otgs-installer-support-style', WP_Installer()->plugin_url() . '/dist/css/otgs-installer-support/styles.css', array(), WP_Installer()->version() );
			wp_enqueue_script( 'otgs-installer-support-script', WP_Installer()->plugin_url() . '/dist/js/otgs-installer-support/app.js', array(), WP_Installer()->version() );
		}
	}

	public function render_link( $args = array() ) {
		$this->template_factory->create()->render_support_link( $args );
	}
}
