<?php

/**
 * Class WCML_Exchange_Rate_Service
 */
abstract class WCML_Exchange_Rate_Service {

	/** @var string  */
	private $id;
	/** @var string  */
	private $name;
	/** @var string  */
	private $url;
	/** @var string  */
	private $api_url;

	private $settings = array();

	protected $api_key = '';
	protected $requires_key = false;

	/**
	 * WCML_Exchange_Rate_Service constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $api_url
	 * @param string $url
	 */
	public function __construct( $id, $name, $api_url, $url = '' ) {

		$this->id      = $id;
		$this->name    = $name;
		$this->api_url = $api_url;
		$this->url     = $url;

		$this->settings = get_option( 'wcml_exchange_rate_service_' . $this->id, array() );

		if ( $this->is_key_required() ) {
			$this->api_key = $this->get_setting( 'api-key' );
		}

	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param string $from
	 * @param array $to
	 *
	 * @return mixed
	 */
	public abstract function get_rates( $from, $to );

	/**
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	private function save_settings() {
		update_option( 'wcml_exchange_rate_service_' . $this->id, $this->settings );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get_setting( $key ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function save_setting( $key, $value ) {
		$this->settings[ $key ] = $value;
		$this->save_settings();
	}

	/**
	 * @return bool
	 */
	public function is_key_required() {
		return $this->requires_key;
	}

	/**
	 * @param string $error_message
	 */
	public function save_last_error( $error_message ) {
		$this->save_setting( 'last_error',
			array(
				'text' => $error_message,
				'time' => date_i18n( 'F j, Y g:i a', current_time( 'timestamp' ) )
			)
		);
	}

	public function clear_last_error() {
		$this->save_setting( 'last_error', false );
	}

	/**
	 * @return mixed
	 */
	public function get_last_error() {
		return isset( $this->settings['last_error'] ) ? $this->settings['last_error'] : false;
	}

}