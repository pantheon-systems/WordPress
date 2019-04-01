<?php

class WPML_Deactivate_Old_Media {

	private $php_functions;

	public function __construct( WPML_PHP_Functions $php_functions ) {
		$this->php_functions = $php_functions;
	}

	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'deactivate_media' ) );
	}

	public function deactivate_media() {
		if ( $this->php_functions->defined( 'WPML_MEDIA_VERSION' ) && $this->php_functions->constant( 'WPML_MEDIA_VERSION' ) < '2.3' ) {
			deactivate_plugins( $this->php_functions->constant( 'WPML_MEDIA_PATH' ) . '/plugin.php' );
		}
	}
}