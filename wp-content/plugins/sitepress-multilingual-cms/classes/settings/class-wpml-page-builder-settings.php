<?php

class WPML_Page_Builder_Settings {

	const OPTION_KEY = 'wpml_page_builders_options';

	private $settings;

	/** @return bool */
	public function is_raw_html_translatable() {
		return (bool) $this->get_setting( 'translate_raw_html', true );
	}

	/** @param bool $is_enabled */
	public function set_raw_html_translatable( $is_enabled ) {
		$this->set_setting( 'translate_raw_html', $is_enabled );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	private function set_setting( $key, $value ) {
		$this->settings[ $key ] = $value;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	private function get_setting( $key, $default = null ) {
		if ( null === $this->settings ) {
			$this->settings = get_option( self::OPTION_KEY, array() );
		}

		if ( array_key_exists( $key, $this->settings ) ) {
			return $this->settings[ $key ];
		}

		return $default;
	}

	public function save() {
		update_option( self::OPTION_KEY, $this->settings );
	}
}
