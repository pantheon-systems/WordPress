<?php

/**
 * @author OnTheGo Systems
 *
 * AMS: https://git.onthegosystems.com/ate/ams/wikis/home
 * ATE: https://git.onthegosystems.com/ate/ams/wikis/home (https://bitbucket.org/emartini_crossover/ate/wiki/browse/API/V1/jobs)
 */
class WPML_TM_ATE_AMS_Endpoints {
	const AMS_BASE_URL               = 'https://ams.wpml.org';
	const ATE_BASE_URL               = 'https://ate.wpml.org';
	const ATE_JOB_STATUS_CREATED     = 0;
	const ATE_JOB_STATUS_TRANSLATING = 1;
	const ATE_JOB_STATUS_TRANSLATED  = 6;
	const ATE_JOB_STATUS_DELIVERING  = 7;
	const ATE_JOB_STATUS_DELIVERED   = 8;
	/**
	 * AMS
	 */
	const ENDPOINTS_AUTO_LOGIN          = '/panel/autologin';
	const ENDPOINTS_CLIENTS             = '/api/wpml/clients';
	const ENDPOINTS_CONFIRM             = '/api/wpml/jobs/confirm';
	const ENDPOINTS_EDITOR              = '/api/wpml/jobs/{job_id}/open?translator={translator_email}&return_url={return_url}';
	const ENDPOINTS_SUBSCRIPTION        = '/api/wpml/websites/translators/{translator_email}/enable';
	const ENDPOINTS_SUBSCRIPTION_STATUS = '/api/wpml/websites/{WEBSITE_UUID}/translators/{translator_email}';
	/**
	 * ATE
	 */
	const ENDPOINTS_JOB         = '/api/wpml/job';
	const ENDPOINTS_JOBS        = '/api/wpml/jobs';
	const ENDPOINTS_MANAGERS    = '/api/wpml/websites/translation_managers';
	const ENDPOINTS_SITE        = '/api/wpml/websites';
	const ENDPOINTS_STATUS      = '/api/wpml/access_keys/{SHARED_KEY}/status';
	const ENDPOINTS_TRANSLATORS = '/api/wpml/websites/translators';
	const SERVICE_AMS           = 'ams';
	const SERVICE_ATE           = 'ate';

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ams_auto_login() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_AUTO_LOGIN );
	}

	/**
	 * @param string     $service
	 * @param string     $endpoint
	 * @param array|null $query_string
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_endpoint_url( $service, $endpoint, array $query_string = null ) {
		$url = $this->get_base_url( $service ) . $endpoint;

		if ( $query_string ) {
			$url_parts = wp_parse_url( $url );
			$query     = array();
			if ( array_key_exists( 'query', $url_parts ) ) {
				parse_str( $url_parts['query'], $query );
			}

			foreach ( $query_string as $key => $value ) {
				if ( $value ) {
					if ( is_scalar( $value ) ) {
						$query[ $key ] = $value;
					} else {
						$query[ $key ] = implode( ',', $value );
					}
				}
			}
			$url_parts['query'] = http_build_query( $query );

			$url = http_build_url( $url_parts );
		}

		return $url;
	}

	/**
	 * @param $service
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	private function get_base_url( $service ) {
		switch ( $service ) {
			case self::SERVICE_AMS:
				return $this->get_AMS_base_url();
			case self::SERVICE_ATE:
				return $this->get_ATE_base_url();
			default:
				throw new InvalidArgumentException( $service . ' is not a valid argument' );
		}
	}

	private function get_AMS_base_url() {
		return $this->get_service_base_url( self::SERVICE_AMS );
	}

	private function get_ATE_base_url() {
		return $this->get_service_base_url( self::SERVICE_ATE );
	}

	private function get_service_base_url( $service ) {
		$constant_name = strtoupper( $service ) . '_BASE_URL';

		$url = constant( __CLASS__ . '::' . $constant_name );

		if ( defined( $constant_name ) ) {
			$url = constant( $constant_name );
		}
		if ( getenv( $constant_name ) ) {
			$url = getenv( $constant_name );
		}

		return $url;
	}

	public function get_AMS_host() {
		return $this->get_service_host( self::SERVICE_AMS );
	}

	public function get_ATE_host() {
		return $this->get_service_host( self::SERVICE_ATE );
	}

	private function get_service_host( $service ) {
		$base_url = $this->get_service_base_url( $service );

		$url_parts = wp_parse_url( $base_url );

		return $url_parts['host'];
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ams_register_client() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_SITE );
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ams_status() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_STATUS );
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ams_synchronize_managers() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_MANAGERS );
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ams_synchronize_translators() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_TRANSLATORS );
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_enable_subscription() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_SUBSCRIPTION );
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_subscription_status() {
		return $this->get_endpoint_url( self::SERVICE_AMS, self::ENDPOINTS_SUBSCRIPTION_STATUS );
	}


	/**
	 * @param int|string|array $job_params
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ate_confirm_job( $job_params = null ) {
		$job_id_part = $this->parse_job_params( $job_params );

		return $this->get_endpoint_url( self::SERVICE_ATE, self::ENDPOINTS_CONFIRM . $job_id_part );
	}

	/**
	 * @param null|int|string|array $job_params
	 *
	 * @return string
	 */
	private function parse_job_params( $job_params ) {
		$job_id_part = '';

		if ( $job_params ) {
			if ( is_array( $job_params ) ) {
				$job_ids = implode( ',', $job_params );
			} else {
				$job_ids = $job_params;
			}
			$job_id_part = '/' . $job_ids;
		}

		return $job_id_part;
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ate_editor() {
		return $this->get_endpoint_url( self::SERVICE_ATE, self::ENDPOINTS_EDITOR );
	}

	/**
	 * @param null|int|string|array $job_params
	 * @param null|array            $statuses
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function get_ate_jobs( $job_params = null, array $statuses = null ) {
		$job_id_part = $this->parse_job_params( $job_params );

		return $this->get_endpoint_url( self::SERVICE_ATE,
		                                self::ENDPOINTS_JOBS . $job_id_part,
		                                array( 'status' => $statuses ) );
	}
}