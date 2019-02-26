<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Status {
	const ICL_20_TS_ID       = 67;
	const ICL_LEGACY_TS_ID   = 4;
	const ICL_LEGACY_TS_SUID = '6ab1000a33e2cc9ecbcf6abc57254be8';
	const ICL_20_TS_SUID     = 'dd17d48516ca4bce0b83043583fabd2e';

	private $installer_settings = array();
	private $service;

	public function __construct( $service ) {
		$this->service = $service;
	}

	public function has_active_legacy_icl() {
		return $this->has_active_service() && $this->get_ICL_LEGACY_TS_ID() === $this->service->id;
	}

	public function has_active_icl_20() {
		return $this->has_active_service() && $this->get_ICL_20_TS_ID() === $this->service->id;
	}

	public function has_active_service() {
		return (bool) $this->service;
	}

	public function get_ICL_LEGACY_TS_ID() {
		if ( defined( 'WPML_TP_ICL_LEGACY_TS_ID' ) ) {
			return WPML_TP_ICL_LEGACY_TS_ID;
		}

		return self::ICL_LEGACY_TS_ID;
	}

	public function get_ICL_20_TS_ID() {
		if ( defined( 'WPML_TP_ICL_20_TS_ID' ) ) {
			return WPML_TP_ICL_20_TS_ID;
		}

		return self::ICL_20_TS_ID;
	}

	public function get_ICL_LEGACY_TS_SUID() {
		if ( defined( 'WPML_TP_ICL_LEGACY_TS_SUID' ) ) {
			return WPML_TP_ICL_LEGACY_TS_SUID;
		}

		return self::ICL_LEGACY_TS_SUID;
	}

	public function get_ICL_20_TS_SUID() {
		if ( defined( 'WPML_TP_ICL_20_TS_SUID' ) ) {
			return WPML_TP_ICL_20_TS_SUID;
		}

		return self::ICL_20_TS_SUID;
	}

	public function is_preferred_service_legacy_ICL() {
		return $this->has_preferred_service() && self::ICL_LEGACY_TS_SUID === $this->get_preferred_service();
	}

	public function set_preferred_service_to_ICL20() {
		if ( $this->get_preferred_service() ) {
			$this->installer_settings['repositories']['wpml']['ts_info']['preferred'] = $this->get_ICL_20_TS_SUID();
			$this->update_installer_settings();
		}
	}

	private function has_preferred_service() {
		return ! in_array( $this->get_preferred_service(), array( 'clear', false ), true );
	}

	private function get_preferred_service() {
		$installer_settings = $this->get_installer_settings();
		if ( isset( $installer_settings['repositories']['wpml']['ts_info']['preferred'] )
		     && 'clear' !== $installer_settings['repositories']['wpml']['ts_info']['preferred'] ) {
			return $installer_settings['repositories']['wpml']['ts_info']['preferred'];
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function get_installer_settings() {
		if ( ! $this->installer_settings ) {
			$raw_settings = get_option( 'wp_installer_settings', null );

			if ( $raw_settings ) {
				if ( is_array( $raw_settings ) || empty( $raw_settings ) ) { //backward compatibility 1.1
					$this->installer_settings = $raw_settings;
				} else {
					$has_gz_support = function_exists( 'gzuncompress' ) && function_exists( 'gzcompress' );
					$raw_settings   = base64_decode( $raw_settings );
					if ( $has_gz_support ) {
						$raw_settings = gzuncompress( $raw_settings );
					}
					/** @noinspection UnserializeExploitsInspection */
					$this->installer_settings = unserialize( $raw_settings );
				}
			}
		}

		return $this->installer_settings;
	}

	private function update_installer_settings() {
		$has_gz_support = function_exists( 'gzuncompress' ) && function_exists( 'gzcompress' );
		$raw_settings   = serialize( $this->installer_settings );
		if ( $has_gz_support ) {
			$raw_settings = gzcompress( $raw_settings );
		}
		$raw_settings = base64_encode( $raw_settings );
		update_option( 'wp_installer_settings', $raw_settings );
	}
}