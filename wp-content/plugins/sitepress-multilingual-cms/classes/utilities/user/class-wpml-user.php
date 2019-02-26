<?php

class WPML_User extends WP_User {

	/**
	 * @see \get_user_meta
	 *
	 * @param string $key
	 * @param bool $single
	 *
	 * @return mixed
	 */
	public function get_meta( $key = '', $single = false ) {
		return get_user_meta( $this->ID, $key, $single );
	}

	/**
	 * @see \update_meta
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $prev_value
	 */
	public function update_meta( $key, $value, $prev_value = '' ) {
		update_user_meta( $this->ID, $key, $value, $prev_value );
	}

	/**
	 * @see \get_user_option
	 *
	 * @param string $option
	 * @return mixed
	 */
	public function get_option( $option ) {
		return get_user_option( $option, $this->ID );
	}

	/**
	 * @see \update_user_option
	 *
	 * @param string $option_name
	 * @param mixed  $new_value
	 * @param bool   $global
	 * @return int|bool
	 */
	function update_option( $option_name, $new_value, $global = false ) {
		return update_user_option( $this->ID, $option_name, $new_value, $global );
	}
}