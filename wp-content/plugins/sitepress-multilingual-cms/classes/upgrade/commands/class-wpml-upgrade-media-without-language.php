<?php

class WPML_Upgrade_Media_Without_Language extends WPML_Upgrade_Run_All {

	/** @var wpdb */
	private $wpdb;

	/** @var string */
	private $default_language;

	public function __construct( array $args ) {
		$this->wpdb = $args[0];
		$this->default_language = $args[1];
	}

	/** @return bool */
	protected function run() {
		$initialize_post_type = new WPML_Initialize_Language_For_Post_Type( $this->wpdb );
		return $initialize_post_type->run( 'attachment', $this->default_language );
	}
}
