<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Site_ID {
	const SITE_ID_KEY = 'WPML_SITE_ID';

	private $site_id;

	/**
	 * @return string
	 */
	public function get_site_id() {
		$this->read_value();
		if ( ! $this->site_id ) {
			$this->site_id = $this->generate_site_id();
		}

		return $this->site_id;
	}

	/**
	 * @return string
	 */
	private function generate_site_id() {
		$site_url  = get_site_url();
		$site_uuid = uuid_v5( $site_url, wp_generate_uuid4() );
		$time_uuid = uuid_v5( time(), wp_generate_uuid4() );

		$this->site_id = uuid_v5( $site_uuid, $time_uuid );
		$this->write_value( $this->site_id );

		return $this->site_id;
	}

	private function read_value() {
		if ( ! $this->site_id ) {
			$this->site_id = get_option( self::SITE_ID_KEY, null );
		}
	}

	private function write_value( $value ) {
		update_option( self::SITE_ID_KEY, $value );
	}
}