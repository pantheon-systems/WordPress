<?php

class OTGS_Installer_Loader {

	private $installer_factory;

	public function __construct( OTGS_Installer_Factory $installer_factory ) {
		$this->installer_factory = $installer_factory;
	}

	public function init() {
		$this->installer_factory->load_wp_components_hooks();

		add_action( 'otgs_installer_initialized', array( $this, 'load_actions_after_installer_init' ) );
	}

	public function load_actions_after_installer_init() {
		$this->installer_factory
			->load_resources()
			->load_settings_hooks()
			->load_local_components_ajax_settings()
			->load_filename_hooks()
			->load_icons()
			->load_debug_info_hooks()
			->load_upgrade_response()
			->load_site_key_ajax_handler()
			->load_installer_support_hooks()
			->load_translation_service_info_hooks()
			->load_plugins_update_cache_cleaner()
			->load_buy_url_hooks();
	}
}