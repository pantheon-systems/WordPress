<?php

class OTGS_Installer_WP_Components_Setting_Resources {

	/**
	 * @var WP_Installer
	 */
	private $installer;

	const HANDLES_OTGS_INSTALLER_UI = 'otgs-installer-ui';

	public function __construct( WP_Installer $installer ) {
		$this->installer = $installer;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_resources' ) );
	}

	public function enqueue_resources() {
		$resources_url = $this->installer->res_url();

		wp_register_style(
			self::HANDLES_OTGS_INSTALLER_UI,
			$resources_url . '/dist/css/component-settings-reports/styles.css',
			array(),
			WP_INSTALLER_VERSION
		);

		wp_enqueue_script(
			'otgs-installer-components-save-setting',
			$resources_url . '/res/js/save-components-setting.js',
			array(),
			WP_INSTALLER_VERSION
		);
	}
}
