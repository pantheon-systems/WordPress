<?php
class WPML_TranslationProxy_Com_Log {
	private static $wrapped_class;

	/**
	 * @return WPML_TranslationProxy_Communication_Log
	 */
	private static function get_wrapped_class_instance() {
		if ( null === self::$wrapped_class ) {
			global $sitepress;
			self::$wrapped_class = new WPML_TranslationProxy_Communication_Log( $sitepress );
		}

		return self::$wrapped_class;
	}

	public static function log_call( $url, $params ) {
	  self::get_wrapped_class_instance()->log_call( $url, $params );
	}

	public static function get_keys_to_block() {
	  return self::get_wrapped_class_instance()->get_keys_to_block();
	}

	public static function log_response( $response ) {
	  self::get_wrapped_class_instance()->log_response( $response );
	}

	public static function log_error( $message ) {
	  self::get_wrapped_class_instance()->log_error( $message );
	}

	public static function log_xml_rpc( $data ) {
	  self::get_wrapped_class_instance()->log_xml_rpc( $data );
	}

	public static function get_log( ) {
	  return self::get_wrapped_class_instance()->get_log();
	}

	public static function clear_log( ) {
	  self::get_wrapped_class_instance()->clear_log();
	}

	public static function is_logging_enabled( ) {
	  return self::get_wrapped_class_instance()->is_logging_enabled();
	}

	/**
	 * @param string|array|stdClass $params
	 *
	 * @return array|stdClass
	 */
	public static function sanitize_data( $params ) {
	  return self::get_wrapped_class_instance()->sanitize_data( $params );
	}

	/**
	 * @param $url
	 *
	 * @return mixed
	 */
	public static function sanitize_url( $url ) {
	  return self::get_wrapped_class_instance()->sanitize_url( $url );
	}

	public static function set_logging_state( $state ) {
	  self::get_wrapped_class_instance()->set_logging_state( $state );
	}

	public static function add_com_log_link() {
	  self::get_wrapped_class_instance()->add_com_log_link();
	}
}
