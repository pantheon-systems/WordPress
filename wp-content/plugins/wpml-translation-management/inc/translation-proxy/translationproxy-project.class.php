<?php
/**
 * @package wpml-core
 * @subpackage wpml-core
 */

require_once dirname( __FILE__ ) . '/translationproxy-api.class.php';
require_once dirname( __FILE__ ) . '/translationproxy-service.class.php';
require_once dirname( __FILE__ ) . '/translationproxy-batch.class.php';

/**
 * Class TranslationProxy_Project
 */
class TranslationProxy_Project {

	public $id;
	/**
	 * @var string
	 *
	 * `access_key` used when sending **any request** to TP
	 */
	public $access_key;
	/**
	 * @var int
	 *
	 * `ts_id` (aka `website_id`) is used **exclusively** when sending request directly to ICL
	 */
	public $ts_id;
	/**
	 * @var string
	 *
	 * `ts_access_key` is used **exclusively** when sending request directly to ICL
	 */
	public $ts_access_key;

	/**
	 * @var object
	 */
	public $service;

	/** @var WPML_TP_Client $tp_client */
	public $tp_client;

	public $errors = array();

	/**
	 * @param TranslationProxy_Service $service
	 * @param string                   $delivery
	 * @param WPML_TP_Client           $tp_client
	 */
	public function __construct( $service, $delivery = 'xmlrpc', WPML_TP_Client $tp_client ) {
		$this->service   = $service;
		$this->tp_client = $tp_client;

		$icl_translation_projects = TranslationProxy::get_translation_projects();
		$project_index            = self::generate_service_index( $service );

		if ( $project_index && $icl_translation_projects && isset( $icl_translation_projects [ $project_index ] ) ) {
			$project = $icl_translation_projects[ $project_index ];
			$this->id            = $project['id'];
			$this->access_key    = $project['access_key'];
			$this->ts_id         = $project['ts_id'];
			$this->ts_access_key = $project['ts_access_key'];

			$this->service->delivery_method = $delivery;
		}
	}

	/**
	 * @return TranslationProxy_Service
	 */
	public function service() {

		return $this->service;
	}

	/**
	 * Returns the index by which a translation service can be found in the array returned by
	 * \TranslationProxy::get_translation_projects
	 *
	 * @param $service object
	 *
	 * @return bool|string
	 */
	public static function generate_service_index( $service ) {
		$index = false;
		if ( $service ) {
			$service->custom_fields_data = isset( $service->custom_fields_data ) ? $service->custom_fields_data : array();
			if ( isset( $service->id ) ) {
				$index = md5( $service->id . serialize( $service->custom_fields_data ) );
			}
		}

		return $index;
	}

	/**
	 * Create and configure project (Translation Service)
	 *
	 * @param string $url
	 * @param string $name
	 * @param string $description
	 * @param string $delivery
	 *
	 * @return bool
	 * @throws WPMLTranslationProxyApiException
	 * @throws Exception
	 */
	public function create(
		$url,
		$name,
		$description,
		$delivery = 'xmlrpc'
	) {
		global $sitepress;

		$networking          = wpml_tm_load_tp_networking();
		$project_creation    = new WPML_TP_Project_Creation( $this, $sitepress, $networking, array(
			'name'               => $name,
			'description'        => $description,
			'url'                => $url,
			'delivery_method'    => $delivery,
			'sitekey'            => WP_Installer_API::get_site_key( 'wpml' ),
			'client_external_id' => WP_Installer_API::get_ts_client_id(),
		) );
		$response_project    = $project_creation->run();
		$this->id            = $response_project->id;
		$this->access_key    = $response_project->accesskey;
		$this->ts_id         = $response_project->ts_id;
		$this->ts_access_key = $response_project->ts_accesskey;

		if ( isset( $response_project->polling_method ) && $response_project->polling_method !== $delivery ) {
			$this->service->delivery_method = $response_project->polling_method;
		}

		return true;
	}

	/**
	 * Convert WPML language code to service language
	 *
	 * @param $language string
	 *
	 * @return bool|string
	 */
	private function service_language( $language ) {
		return TranslationProxy_Service::get_language( $this->service, $language );
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Get information about the project (Translation Service)
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	public function custom_text( $location, $locale = "en" ) {
		$response = '';
		if ( ! $this->ts_id || ! $this->ts_access_key ) {
			return '';
		}

		//Sending Translation Service (ts_) id and access_key, as we are talking directly to the Translation Service
		//Todo: use project->id and project->access_key once this call is moved to TP
		$params = array(
			'project_id' => $this->ts_id,
			'accesskey'  => $this->ts_access_key,
			'location'   => $location,
			'lc'         => $locale,
		);

		if ( $this->service->custom_text_url ) {
			try {
				$response = TranslationProxy_Api::service_request( $this->service->custom_text_url,
					$params, 'GET', true, true, true );
			} catch ( Exception $e ) {
				throw new RuntimeException( 'error getting custom text from Translation Service: ' . serialize( $params ) . ' url: ' . $this->service->custom_text_url,
					0, $e );
			}
		}

		return $response;
	}

	function current_service_name() {

		return TranslationProxy::get_current_service_name();
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * IFrames to display project info (Translation Service)
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	public function select_translator_iframe_url( $source_language, $target_language ) {
		//Sending Translation Service (ts_) id and access_key, as we are talking directly to the Translation Service
		$params['project_id']      = $this->ts_id;
		$params['accesskey']       = $this->ts_access_key;
		$params['source_language'] = $this->service_language( $source_language );
		$params['target_language'] = $this->service_language( $target_language );
		$params['compact']         = 1;

		return $this->_create_iframe_url( $this->service->select_translator_iframe_url, $params );
	}

	public function translator_contact_iframe_url( $translator_id ) {
		//Sending Translation Service (ts_) id and access_key, as we are talking directly to the Translation Service
		$params['project_id']    = $this->ts_id;
		$params['accesskey']     = $this->ts_access_key;
		$params['translator_id'] = $translator_id;
		$params['compact']       = 1;
		if ( $this->service->translator_contact_iframe_url ) {
			return $this->_create_iframe_url( $this->service->translator_contact_iframe_url, $params );
		}

		return false;
	}

	private function _create_iframe_url( $url, $params ) {
			if ( $params ) {
				$url = TranslationProxy_Api::add_parameters_to_url( $url, $params );
				$url .= '?' . http_build_query( $params );
			}

			return $url;
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Jobs handling (Translation Proxy)
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * @throws WPML_TP_Batch_Exception
	 *
	 * @param bool $source_language
	 * @param bool $target_languages
	 *
	 * @internal param bool $name
	 * @return false|WPML_TP_Batch
	 */
	function get_batch_job(
		$source_language = false,
		$target_languages = false
	) {
		$cache_key = md5( wp_json_encode( array(
			$source_language,
			$target_languages
		) ) );
		$cache_group = 'get_batch_job';
		$cache_found = false;

		$batch_data = wp_cache_get( $cache_key, $cache_group, false, $cache_found );

		if ( $cache_found ) {
			return $batch_data;
		}

		$batch_data = TranslationProxy_Basket::get_batch_data();

		if ( ! $batch_data ) {
			if ( ! $source_language ) {
				$source_language = TranslationProxy_Basket::get_source_language();
			}
			if ( ! $target_languages ) {
				$target_languages = TranslationProxy_Basket::get_remote_target_languages();
			}

			if ( ! $source_language || ! $target_languages ) {
				return false;
			}

			$batch_data = $this->create_batch_job( $source_language, $target_languages );

			if ( $batch_data ) {
				TranslationProxy_Basket::set_batch_data( $batch_data );
			}
		}

		wp_cache_set( $cache_key, $batch_data, $cache_group );

		return $batch_data;
	}

	/**
	 * @throws WPML_TP_Batch_Exception
	 *
	 * @return false|int
	 */
	function get_batch_job_id() {
		$ret        = false;
		$batch_data = $this->get_batch_job();

		if ( $batch_data ) {
			$ret = $batch_data->get_id();
		}

		return $ret;
	}

	/**
	 * @throws WPML_TP_Batch_Exception
	 *
	 * @param bool $source_language
	 * @param      $target_languages
	 *
	 * @internal param bool $name
	 * @return false|WPML_TP_Batch
	 */
	public function create_batch_job( $source_language, $target_languages ) {
		$batch_name    = TranslationProxy_Basket::get_basket_name();
		$batch_options = TranslationProxy_Basket::get_options();
		$extra_fields  = TranslationProxy_Basket::get_basket_extra_fields();

		$batch_data = array(
			'source_language'  => $source_language,
			'target_languages' => $target_languages,
			'name'             => $batch_name,
		);

		if ( ! $batch_data['source_language'] ) {
			$batch_data['source_language'] = TranslationProxy_Basket::get_source_language();
		}

		if ( ! $batch_data['target_languages'] ) {
			$batch_data['target_languages'] = TranslationProxy_Basket::get_remote_target_languages();
		}

		if ( ! $batch_data['source_language'] || ! $batch_data['target_languages'] ) {
			return false;
		}

		if ( ! $batch_data['name'] ) {
			$batch_data['name'] = sprintf( __( '%s: WPML Translation Jobs',
				'wpml-translation-management' ), get_option( 'blogname' ) );
		}

		TranslationProxy_Basket::set_basket_name( $batch_data['name'] );

		if ( isset( $batch_options['deadline_date'] ) ) {
			$batch_data['deadline'] = strtotime( $batch_options['deadline_date'] );
		}

		return $this->tp_client->batches()->create( $batch_data, $extra_fields );
	}

	/**
	 *
	 * Add Files Batch Job
	 *
	 * @throws WPML_TP_Batch_Exception
	 *
	 * @param string $file
	 * @param string $title
	 * @param string $cms_id
	 * @param string $url
	 * @param string $source_language
	 * @param string $target_language
	 * @param int    $word_count
	 * @param int    $translator_id
	 * @param string $note
	 *
	 * @return bool
	 */
	public function send_to_translation_batch_mode(
		$file,
		$title,
		$cms_id,
		$url,
		$source_language,
		$target_language,
		$word_count,
		$translator_id = 0,
		$note = '',
		$uuid = null
	) {

		$batch_id = $this->get_batch_job_id();

		if ( ! $batch_id ) {
			return false;
		}

		$job_data = array(
			'file'            => $file,
			'word_count'      => $word_count,
			'title'           => $title,
			'cms_id'          => $cms_id,
			'udid'            => $uuid,
			'url'             => $url,
			'translator_id'   => $translator_id,
			'note'            => $note,
			'source_language' => $source_language,
			'target_language' => $target_language,
		);

		$tp_job = $this->tp_client->batches()->add_job( $batch_id, $job_data );

		if ( $tp_job ) {
			return $tp_job->get_id();
		}
	}

	/**
	 * @param bool|int $tp_batch_id
	 *
	 * @link http://git.icanlocalize.com/onthego/translation_proxy/wikis/commit_batch_job
	 *
	 * @return array|bool|mixed|null|stdClass|string
	 */
	function commit_batch_job( $tp_batch_id = false ) {
		$tp_batch_id = $tp_batch_id ? $tp_batch_id : $this->get_batch_job_id();

		if ( ! $tp_batch_id ) {
			return true;
		}

		$params = array(
			"api_version" => TranslationProxy_Api::API_VERSION,
			'project_id'  => $this->id,
			'accesskey'   => $this->access_key,
			'batch_id'    => $tp_batch_id,
		);

		$response    = TranslationProxy_Api::proxy_request( '/batches/{batch_id}/commit.json', $params, 'PUT', false );
		$basket_name = TranslationProxy_Basket::get_basket_name();
		if ( $basket_name ) {
			global $wpdb;

			$batch_id_sql      = "SELECT id FROM {$wpdb->prefix}icl_translation_batches WHERE batch_name=%s";
			$batch_id_prepared = $wpdb->prepare( $batch_id_sql,
				array( $basket_name ) );
			$batch_id          = $wpdb->get_var( $batch_id_prepared );

			$batch_data = array(
				'batch_name'  => $basket_name,
				'tp_id'       => $tp_batch_id,
				'last_update' => date( 'Y-m-d H:i:s' ),
			);
			if ( isset( $response ) && $response ) {
				$batch_data['ts_url'] = serialize( $response );
			}

			if ( ! $batch_id ) {
				$wpdb->insert( $wpdb->prefix . 'icl_translation_batches',
					$batch_data );
			} else {
				$wpdb->update( $wpdb->prefix . 'icl_translation_batches',
					$batch_data, array( 'id' => $batch_id ) );
			}
		}

		return isset( $response ) ? $response : false;
	}

	/**
	 *
	 * @return object[]
	 */
	public function jobs() {

		return $this->get_jobs( 'any' );
	}

	/**
	 * @return object[]
	 */
	public function finished_jobs() {

		return $this->get_jobs( 'translation_ready' );
	}

	public function set_delivery_method( $method ) {
		$params = array(
			'project_id' => $this->id,
			'accesskey'  => $this->access_key,
			'project'    => array( 'delivery_method' => $method ),
		);
		TranslationProxy_Api::proxy_request( '/projects.json', $params, 'put' );

		return true;
	}

	public function fetch_translation( $job_id ) {
		$params = array(
			'project_id' => $this->id,
			'accesskey'  => $this->access_key,
			'job_id'     => $job_id,
		);

		return TranslationProxy_Api::proxy_download( '/jobs/{job_id}/xliff.json',
			$params );
	}

	public function check_status( $batch_id ) {
		$tp_networking = wpml_tm_load_tp_networking();
		$params        = array(
			'batch_id'   => $batch_id,
			'project_id' => $this->id,
			'accesskey'  => $this->access_key,
		);

		$tp_networking->send_request( OTG_TRANSLATION_PROXY_URL . "/batches/{batch_id}/check.json",
			$params, 'GET', true );
	}

	public function update_job( $job_id, $url = null, $state = 'delivered' ) {
		$params = array(
			'job_id'     => $job_id,
			'project_id' => $this->id,
			'accesskey'  => $this->access_key,
			'job'        => array(
				'state' => $state,
			),
		);
		if ( $url ) {
			$params['job']['url'] = $url;
		}

		TranslationProxy_Api::proxy_request( '/jobs/{job_id}.json', $params,
			'PUT' );
	}

	/**
	 * @param string $state
	 *
	 * @return mixed
	 */
	private function get_jobs( $state = 'any' ) {
		$batch = TranslationProxy_Basket::get_batch_data();

		$params = array(
			'project_id' => $this->id,
			'accesskey'  => $this->access_key,
			'state'      => $state,
		);

		if ( $batch ) {
			$params['batch_id'] = $batch ? $batch->get_id() : false;

			return TranslationProxy_Api::proxy_request( '/batches/{batch_id}/jobs.json',
				$params );
		} else {
			//FIXME: remove this once TP will accept the TP Project ID: https://icanlocalize.basecamphq.com/projects/11113143-translation-proxy/todo_items/182251206/comments
			$params['project_id'] = $this->id;
		}

		return TranslationProxy_Api::proxy_request( '/jobs.json', $params );
	}
}
