<?php

class WPML_Installer_Gateway {

	private static $the_instance;

	private function __construct() {}

	private function __clone() {}

	public static function get_instance() {
		if ( ! self::$the_instance ) {
			self::$the_instance = new WPML_Installer_Gateway();
		}
		return self::$the_instance;
	}

	public static function set_instance( $instance ) {
		self::$the_instance = $instance;
	}

	public function class_exists() {
		return class_exists( 'WP_Installer_API' );
	}

	public function get_site_key( $repository_id = 'wpml' ) {
		return WP_Installer_API::get_site_key( $repository_id );
	}

	public function get_ts_client_id( $repository_id = 'wpml' ) {
		return WP_Installer_API::get_ts_client_id( $repository_id );
	}

	public function get_registering_user_id( $repository_id = 'wpml' ) {
		return WP_Installer_API::get_registering_user_id( $repository_id );
	}

}