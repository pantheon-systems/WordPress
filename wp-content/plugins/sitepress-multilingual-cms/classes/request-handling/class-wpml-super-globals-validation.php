<?php

class WPML_Super_Globals_Validation {

	/**
	 * @param string $key
	 * @param int    $filter
	 * @param mixed  $options
	 *
	 * @return mixed|null
	 */
	public function get( $key, $filter = FILTER_SANITIZE_STRING, $options = null ) {
		if ( ! isset( $_GET ) ) {
			return null;
		}

		return $this->get_value( $key, $_GET, $filter, $options );
	}

	/**
	 * @param string $key
	 * @param int    $filter
	 * @param mixed  $options
	 *
	 * @return mixed|null
	 */
	public function post( $key, $filter = FILTER_SANITIZE_STRING, $options = null ) {
		if ( ! isset( $_POST ) ) {
			return null;
		}

		return $this->get_value( $key, $_POST, $filter, $options );
	}

	/**
	 * @param string $key
	 * @param array  $var
	 * @param int    $filter
	 * @param mixed  $options
	 *
	 * @return mixed|null
	 */
	private function get_value( $key, array $var, $filter = FILTER_SANITIZE_STRING, $options = null ) {
		$value = null;

		if ( array_key_exists( $key, $var ) ) {
			if ( is_array( $var[ $key ] ) ) {
				$value = filter_var_array( $var[ $key ], $filter );
			} else {
				$value = filter_var( $var[ $key ], $filter, $options );
			}
		}

		return $value;
	}
}
