<?php

class OTGS_Installer_Source {

	private $installer;
	private $file_system;

	public function __construct( WP_Installer $installer, WP_Filesystem_Base $file_system ) {
		$this->installer = $installer;
		$this->file_system = $file_system;
	}

	/**
	 * @return array|null
	 */
	public function get() {
		return file_exists( $this->installer->plugin_path() ) ? json_decode( $this->file_system->get_contents( $this->installer->plugin_path() ) ) : null;
	}
}