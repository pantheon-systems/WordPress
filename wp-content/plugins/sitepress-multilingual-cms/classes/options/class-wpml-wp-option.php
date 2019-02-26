<?php

/**
 * @author OnTheGo Systems
 */
abstract class WPML_WP_Option {

	abstract public function get_key();
	abstract public function get_default();

	public function get() {
		return get_option( $this->get_key(), $this->get_default() );
	}

	public function set( $value, $autoload = true ) {
		update_option( $this->get_key(), $value, $autoload );
	}
}