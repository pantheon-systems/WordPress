<?php

class WCML_Admin_Cookie{

	/** @var string */
	private $name;

	/**
	 * WCML_Admin_Cookie constructor.
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * @param mixed $value
	 * @param int $expiration
	 */
	public function set_value( $value, $expiration = null ){
		if( null === $expiration ){
			$expiration = time() + DAY_IN_SECONDS;
		}
		$this->handle_cache_plugins();
		wc_setcookie( $this->name, $value, $expiration );
	}

	/**
	 * @return mixed
	 */
	public function get_value() {
		$value = null;
		if ( isset( $_COOKIE [ $this->name ] ) ){
			$value = $_COOKIE[ $this->name ];
		}
		return $value;
	}

	/**
	 * @param $name
	 */
	private function handle_cache_plugins() {
		// @todo uncomment or delete when #wpmlcore-5796 is resolved
		//do_action( 'wpsc_add_cookie', $this->name );
	}
}
