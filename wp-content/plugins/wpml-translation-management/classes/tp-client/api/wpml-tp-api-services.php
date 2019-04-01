<?php

/**
 * Class WPML_TP_API_Services
 *
 * @author OnTheGoSystems
 */
class WPML_TP_API_Services extends WPML_TP_Abstract_API {

	const ENDPOINT_SERVICES      = '/services.json';
	const ENDPOINT_SERVICE       = '/services/{service_id}.json';
	const ENDPOINT_LANGUAGES_MAP = '/services/{service_id}/language_identifiers.json';
	const ENDPOINT_CUSTOM_FIELDS = '/services/{service_id}/custom_fields.json';

	const TRANSLATION_MANAGEMENT_SYSTEM = 'tms';
	const PARTNER                       = 'partner';
	const TRANSLATION_SERVICE           = 'ts';
	const CACHED_SERVICES_KEY_DATA      = 'wpml_translation_services';
	const CACHED_SERVICES_TRANSIENT_KEY = 'wpml_translation_services_list';
	const CACHED_SERVICES_KEY_TIMESTAMP = 'wpml_translation_services_timestamp';

	private $endpoint;

	/** @return string */
	protected function get_endpoint_uri() {
		return $this->endpoint;
	}

	/** @return bool */
	protected function is_authenticated() {
		return false;
	}

	/**
	 * @param bool $reload
	 *
	 * @return array
	 */
	public function get_all( $reload = false ) {
		$this->endpoint = self::ENDPOINT_SERVICES;

		$translation_services = $reload ? null : $this->get_cached_services();

		if ( ! $translation_services ) {
			$translation_services = get_transient( self::CACHED_SERVICES_TRANSIENT_KEY );

			if ( $translation_services ) {
				$translation_services = $this->convert_to_tp_services( $translation_services );
			}
		}

		if ( ! $translation_services || $this->has_cache_services_expired() ) {
			$fresh_translation_services = parent::get();

			if ( $fresh_translation_services ) {
				$translation_services = $this->convert_to_tp_services( $fresh_translation_services );
				$this->cache_services( $translation_services );
			}
		}

		return apply_filters( 'otgs_translation_get_services', $translation_services ? $translation_services : array() );
	}

	/**
	 * @return bool
	 */
	public function refresh_cache() {
		update_option( self::CACHED_SERVICES_KEY_TIMESTAMP, strtotime( '-2 day', $this->get_cached_services_timestamp() ) );
		return (bool) $this->get_all();
	}

	/**
	 * @return mixed
	 */
	private function get_cached_services() {
		return get_option( self::CACHED_SERVICES_KEY_DATA );
	}

	/**
	 * @return mixed
	 */
	private function get_cached_services_timestamp() {
		return get_option( self::CACHED_SERVICES_KEY_TIMESTAMP );
	}

	/**
	 * @param $services
	 */
	private function cache_services( $services ) {
		update_option( self::CACHED_SERVICES_KEY_DATA, $services );
		update_option( self::CACHED_SERVICES_KEY_TIMESTAMP, time() );
	}

	/**
	 * @return bool
	 */
	private function has_cache_services_expired() {
		return time() >= strtotime( '+1 day', $this->get_cached_services_timestamp() );
	}

	/**
	 * @param array $translation_services
	 *
	 * @return array
	 */
	private function convert_to_tp_services( $translation_services ) {
		$converted_services = array();

		foreach ( $translation_services as $translation_service ) {
			$ts = $translation_service;

			if ( $translation_service instanceof stdClass ) {
				$ts = new WPML_TP_Service( $translation_service );
			}

			$converted_services[] = $ts;
		}

		return $converted_services;
	}

	/**
	 * @param bool $partner
	 * @return array
	 */
	public function get_translation_services( $partner = true ) {
		return array_values( wp_list_filter( $this->get_all(), array( self::TRANSLATION_MANAGEMENT_SYSTEM => false, self::PARTNER => $partner ) ) );
	}

	/**
	 * @return array
	 */
	public function get_translation_management_systems() {
		return array_values( wp_list_filter( $this->get_all(), array( self::TRANSLATION_MANAGEMENT_SYSTEM => true ) ) );
	}

	/**
	 * @param bool $reload
	 *
	 * @return null|WPML_TP_Service
	 */
	public function get_active( $reload = false ) {
		return $this->get_one( $this->tp_client->get_project()->get_translation_service_id(), $reload );
	}

	/**
	 * @param int  $service_id
	 * @param bool $reload
	 *
	 * @return null|string
	 */
	public function get_name( $service_id, $reload = false ) {
		$translator_name = null;

		/** @var array $translation_services */
		$translation_service = $this->get_one( $service_id, $reload );

		if ( null !== $translation_service && isset( $translation_service->name ) ) {
			$translator_name = $translation_service->name;
		}

		return $translator_name;
	}

	public function get_service( $service_id, $reload = false ) {
		return $this->get_one( $service_id, $reload );
	}

	/**
	 * @param int  $translation_service_id
	 * @param bool $reload
	 *
	 * @return null|WPML_TP_Service
	 */
	private function get_one( $translation_service_id, $reload = false ) {
		$translation_service = null;
		if ( ! $translation_service_id ) {
			return $translation_service;
		}

		/** @var array $translation_services */
		$translation_services = $this->get_all( $reload );
		$translation_services = wp_list_filter(
			$translation_services,
			array(
				'id' => (int) $translation_service_id,
			)
		);

		if ( $translation_services ) {
			$translation_service = current( $translation_services );
		} else {
			$translation_service = $this->get_unlisted_service( $translation_service_id );
		}

		return $translation_service;
	}

	/**
	 * @param string $translation_service_id
	 *
	 * @return null|WPML_TP_Service
	 */
	private function get_unlisted_service( $translation_service_id ) {
		$this->endpoint = self::ENDPOINT_SERVICE;
		$service        = parent::get( array( 'service_id' => $translation_service_id ) );

		if ( $service instanceof stdClass ) {
			return new WPML_TP_Service( $service );
		}

		return null;
	}

	/**
	 * @param $service_id
	 *
	 * @return array
	 */
	public function get_languages_map( $service_id ) {
		$this->endpoint = self::ENDPOINT_LANGUAGES_MAP;

		$args = array(
			'service_id' => $service_id,
		);

		return parent::get( $args );
	}

	/**
	 * @param $service_id
	 *
	 * @return mixed
	 */
	public function get_custom_fields( $service_id ) {
		$this->endpoint = self::ENDPOINT_CUSTOM_FIELDS;

		$args = array(
			'service_id' => $service_id,
		);

		return parent::get( $args );
	}
}
