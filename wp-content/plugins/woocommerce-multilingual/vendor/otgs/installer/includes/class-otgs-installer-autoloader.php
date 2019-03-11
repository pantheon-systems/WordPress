<?php

class OTGS_Installer_Autoloader {

	public function initialize() {
		include_once dirname( __FILE__ ) . '/functions-core.php';
		include_once dirname( __FILE__ ) . '/functions-templates.php';

		spl_autoload_register( array( $this, 'autoload' ) );
	}

	public function autoload( $class_name ) {
		$classMap = require dirname( __FILE__ ) . '/otgs-installer-autoload-classmap.php';

		if ( array_key_exists( $class_name, $classMap ) ) {
			$file = $classMap[ $class_name ];
			include $file;
		}
	}
}