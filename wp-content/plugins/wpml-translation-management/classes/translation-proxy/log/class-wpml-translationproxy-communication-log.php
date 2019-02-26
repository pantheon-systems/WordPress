<?php

class WPML_TranslationProxy_Communication_Log {
	private $keys_to_block;
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->keys_to_block = array(
			'api_token',
			'username',
			'api_key',
			'sitekey',
			'accesskey',
			'file',
		);

		$this->sitepress = $sitepress;
	}

	public function log_call( $url, $params ) {
		$sanitized_params = $this->sanitize_data( $params );
		$sanitized_url    = $this->sanitize_url( $url );

		$this->add_to_log( 'call - ' . $sanitized_url . ' - ' . json_encode( $sanitized_params ) );
	}

	public function get_keys_to_block() {
		return $this->keys_to_block;
	}

	public function log_response( $response ) {
		$this->add_to_log( 'response - ' . $response );
	}

	public function log_error( $message ) {
		$this->add_to_log( 'error - ' . $message );
	}

	public function log_xml_rpc( $data ) {
		$this->add_to_log( 'xml-rpc - ' . json_encode( $data ) );
	}

	public function get_log() {
		return get_option( 'wpml_tp_com_log', '' );
	}

	public function clear_log() {
		$this->save_log( '' );
	}

	public function is_logging_enabled() {
		return $this->sitepress->get_setting( 'tp-com-logging', true );
	}

	/**
	 * @param string|array|stdClass $params
	 *
	 * @return array|stdClass
	 */
	public function sanitize_data( $params ) {
		$sanitized_params = $params;

		if ( is_object( $sanitized_params ) ) {
			$sanitized_params = get_object_vars( $sanitized_params );
		}

		if ( is_array( $sanitized_params ) ) {
			foreach ( $sanitized_params as $key => $value ) {
				$sanitized_params[ $key ] = $this->sanitize_data_item( $key, $sanitized_params[ $key ] );
			}
		}

		return $sanitized_params;
	}

	/**
	 * @param string                $key
	 * @param string|array|stdClass $item
	 *
	 * @return string|array|stdClass
	 */
	private function sanitize_data_item( $key, $item ) {
		if ( is_array( $item ) || is_object( $item ) ) {
			$item = $this->sanitize_data( $item );
		} elseif ( in_array( $key, $this->get_keys_to_block(), true ) ) {
			$item = 'UNDISCLOSED';
		}

		return $item;
	}

	/**
	 * @param $url
	 *
	 * @return mixed
	 */
	public function sanitize_url( $url ) {
		$original_url_parsed = wpml_parse_url( $url, PHP_URL_QUERY );
		parse_str( $original_url_parsed, $original_query_vars );

		$sanitized_query_vars = $this->sanitize_data( $original_query_vars );

		return add_query_arg( $sanitized_query_vars, $url );
	}

	public function set_logging_state( $state ) {
		$this->sitepress->set_setting( 'tp-com-logging', $state );
		$this->sitepress->save_settings();
	}

	public function add_com_log_link() {
		$url = esc_attr( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=com-log' );
		?>
			<p class="wpml-tm-basket-help-text">
		  <?php printf( __( 'For retrieving debug information for communication between your%s site and the translation system, use the <a href="%s">communication log</a> page.',
		                    'wpml-translation-management' ), '<br>', $url ); ?>
			</p>
		<?php
	}

	private function now() {
		return date( 'm/d/Y h:i:s a', time() );
	}

	private function add_to_log( $string ) {

		if ( $this->is_logging_enabled() ) {

			$max_log_length = 10000;

			$string = $this->now() . ' - ' . $string;

			$log = $this->get_log();
			$log .= $string;
			$log .= PHP_EOL;

			$log_length = strlen( $log );
			if ( $log_length > $max_log_length ) {
				$log = substr( $log, $log_length - $max_log_length );
			}

			$this->save_log( $log );
		}
	}

	private function save_log( $log ) {
		update_option( 'wpml_tp_com_log', $log, false );
	}
}
