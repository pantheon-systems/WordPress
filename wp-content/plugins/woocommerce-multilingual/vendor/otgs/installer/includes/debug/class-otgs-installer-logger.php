<?php

class OTGS_Installer_Logger {

	private $installer;
	private $storage;
	private $logger_factory;

	public function __construct( WP_Installer $installer, OTGS_Installer_Logger_Storage $storage ) {
		$this->installer = $installer;
		$this->storage   = $storage;
	}

	public function get_api_log() {
		return $this->installer->get_api_debug();
	}

	public function add_api_log( $log ) {
		$this->installer->api_debug_log( $log );
	}

	public function save_log( OTGS_Installer_Log $log ) {
		$this->storage->add( $log );
	}

	public function add_log( $log ) {
		$this->installer->log( $log );
	}
}