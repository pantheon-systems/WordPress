<?php

class WPML_End_User_Info_Loader implements IWPML_Action {
	/** @var WPML_End_User_Dependency_Container */
	private $container;

	/**
	 * @param WPML_End_User_Dependency_Container $container
	 */
	public function __construct( WPML_End_User_Dependency_Container $container ) {
		$this->container = $container;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_end_user_get_info', array( $this, 'get_info' ), 10, 0 );
	}

	public function get_info() {
		$data = $this->container->get_info_repository()->get_data();
		$data = $this->container->get_info_model()->get( $data );

		wp_send_json_success( $data );
	}
}
