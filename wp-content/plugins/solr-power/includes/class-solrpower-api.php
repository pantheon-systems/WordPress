<?php
/**
 * Interface to the Solr instance.
 *
 * @package SolrPower_API
 */

/**
 * Interface to the Solr instance.
 */
class SolrPower_Api {

	/**
	 * Singleton instance
	 *
	 * @var SolrPower_Api|Bool
	 */
	private static $instance = false;

	/**
	 * Logging for debugging.
	 *
	 * @var array
	 */
	public $log = array();

	/**
	 * Solr instance
	 *
	 * @var Solarium\Client|null
	 */
	public $solr = null;

	/**
	 * Last response code/exception code.
	 *
	 * @var string
	 */
	public $last_code;

	/**
	 * Ping results from Solr.
	 *
	 * @var string
	 */
	public $last_error;

	/**
	 * Ping results from Solr.
	 *
	 * @var bool
	 */
	protected $ping_status;

	/**
	 * Grab instance of object.
	 *
	 * @return SolrPower_Api
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the API object.
	 */
	function __construct() {
		add_action( 'admin_notices', array( $this, 'check_for_schema' ) );
	}

	/**
	 * Allow 'ping' to be unset until it needs to be accessed.
	 *
	 * @param string $key Key to be accessed.
	 */
	public function __get( $key ) {
		if ( 'ping' === $key ) {
			if ( isset( self::$instance->ping_status ) ) {
				return self::$instance->ping_status;
			}
			self::ping_server();
			return self::$instance->ping_status;
		}
	}

	/**
	 * Allow setting of 'ping' publicly, because it was once public and made protected.
	 *
	 * @param string $key   Key to be set.
	 * @param mixed  $value Value to be set on the key.
	 */
	public function __set( $key, $value ) {
		if ( 'ping' === $key ) {
			self::$instance->ping_status = $value;
		}
	}

	/**
	 * Submit the schema to Solr.
	 */
	function submit_schema() {
		/*
		 * Solarium does not currently support submitting schemas to the server.
		 * So we'll do it ourselves.
		 * Let's check for a custom Schema.xml. It MUST be located in
		 * wp-content/uploads/solr-for-wordpress-on-pantheon/schema.xml.
		*/
		if ( ! empty( $_ENV['FILEMOUNT'] ) && is_file( realpath( ABSPATH ) . '/' . $_ENV['FILEMOUNT'] . '/solr-for-wordpress-on-pantheon/schema.xml' ) ) {
			$schema = realpath( ABSPATH ) . '/' . $_ENV['FILEMOUNT'] . '/solr-for-wordpress-on-pantheon/schema.xml';
		} else {
			$schema = SOLR_POWER_PATH . '/schema.xml';
		}

		$path        = $this->compute_path();
		$url         = $this->get_default_scheme() . '://' . getenv( 'PANTHEON_INDEX_HOST' ) . ':' . getenv( 'PANTHEON_INDEX_PORT' ) . $path;
		$client_cert = self::get_cert_path();

		/*
		 * A couple of quick checks to make sure everything seems sane
		 */
		$error_message = SolrPower::get_instance()->sanity_check();
		if ( $error_message ) {
			return $error_message;
		}

		if ( ! file_exists( $schema ) ) {
			return $schema . ' does not exist.';
		}

		if ( ! file_exists( $client_cert ) ) {
			return $client_cert . ' does not exist.';
		}

		$file = fopen( $schema, 'r' );
		// set URL and other appropriate options.
		$opts = array(
			CURLOPT_URL            => $url,
			CURLOPT_PORT           => getenv( 'PANTHEON_INDEX_PORT' ),
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSLCERT        => $client_cert,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER     => array( 'Content-type:text/xml; charset=utf-8' ),
			CURLOPT_PUT            => true,
			CURLOPT_BINARYTRANSFER => 1,
			CURLOPT_INFILE         => $file,
			CURLOPT_INFILESIZE     => filesize( $schema ),
		);

		$ch = curl_init();
		curl_setopt_array( $ch, $opts );

		$response  = curl_exec( $ch );
		$curl_opts = curl_getinfo( $ch );
		fclose( $file );
		if ( 200 === (int) $curl_opts['http_code'] ) {
			$return_value = 'Schema Upload Success: ' . $curl_opts['http_code'];
		} else {
			$return_value = 'Schema Upload Error: ' . $curl_opts['http_code'];
			if ( preg_match( '#<h1>(HTTP Status [\d]+ - )?(.+)</h1>#', $response, $matches ) ) {
				$return_value .= ' - ' . $matches[2];
			}
		}

		return $return_value;
	}

	/**
	 * Build the path that the Solr server uses
	 *
	 * @return string
	 */
	function compute_path() {
		if ( ! defined( 'SOLR_PATH' ) && getenv( 'SOLR_PATH' ) ) {
			define( 'SOLR_PATH', getenv( 'SOLR_PATH' ) );
		}
		if ( defined( 'SOLR_PATH' ) ) {
			return SOLR_PATH;
		}

		return '/sites/self/environments/' . getenv( 'PANTHEON_ENVIRONMENT' ) . '/index';
	}

	/**
	 * Check if the server is available by pinging it
	 *
	 * @return boolean
	 */
	function ping_server() {
		$solr = get_solr();

		if ( ! $solr ) {
			return false;
		}

		try {
			$ping            = $solr->ping( $solr->createPing() );
			$this->last_code = 200;
			$this->ping      = true;
			return true;
		} catch ( Solarium\Exception\HttpException $e ) {

			$this->last_code  = $e->getCode();
			$this->last_error = $e;

			return false;
		}
	}

	/**
	 * Connect to the solr service
	 *
	 * @return Solarium\Client Solr service object
	 */
	function get_solr() {

		$plugin_s4wp_settings = solr_options();
		$solarium_config      = array(
			'endpoint' => array(
				'localhost' => array(
					'host'   => getenv( 'PANTHEON_INDEX_HOST' ),
					'port'   => getenv( 'PANTHEON_INDEX_PORT' ),
					'scheme' => $this->get_default_scheme(),
					'path'   => $this->compute_path(),
					'ssl'    => array(
						'local_cert' => self::get_cert_path(),
					),
				),
			),
		);

		/**
		 * Filter connection options
		 *
		 * Filters connection options for the solr server.
		 *
		 * @param array $solarium_config {
		 *      Array of connection information.
		 *
		 * @type array $endpoint Array of endpoint information.
		 * }
		 */
		$solarium_config = apply_filters( 's4wp_connection_options', $solarium_config );

		// double check everything has been set.
		if ( ! ( $solarium_config['endpoint']['localhost']['host'] &&
				$solarium_config['endpoint']['localhost']['port'] &&
				$solarium_config['endpoint']['localhost']['path'] )
		) {
			$host = isset( $solarium_config['endpoint']['localhost']['host'] ) ? $solarium_config['endpoint']['localhost']['host'] : '';
			$port = isset( $solarium_config['endpoint']['localhost']['port'] ) ? $solarium_config['endpoint']['localhost']['port'] : '';
			$path = isset( $solarium_config['endpoint']['localhost']['path'] ) ? $solarium_config['endpoint']['localhost']['path'] : '';
			syslog( LOG_ERR, "host, port or path are empty, host:$host, port:$port, path:$path" );

			return null;
		}

		$solr = new Solarium\Client( $solarium_config );

		/**
		 * Filter solarium client
		 *
		 * Replace the solarium client with a custom client.
		 *
		 * @param Solarium\Client $solr The default Solarium client created by this plugin.
		 */
		$solr       = apply_filters( 's4wp_solr', $solr ); // better name?
		$this->solr = $solr;

		// Use the PantheonCurl adapter to get https.
		$this->solr->setAdapter( '\PantheonCurl' );

		return $solr;
	}

	/**
	 * Trigger Solr indexing process.
	 */
	function optimize() {
		try {
			$solr = get_solr();
			if ( null !== $solr ) {
				$update = $solr->createUpdate();
				$update->addOptimize();
				$solr->update( $update );
			}
		} catch ( Exception $e ) {
			syslog( LOG_ERR, $e->getMessage() );
		}
	}

	/**
	 * Query the required server
	 * passes all parameters to the appropriate function based on the server name
	 * This allows for extensible server/core based query functions.
	 *
	 * @param string  $qry    Solr query.
	 * @param integer $offset Offset for the query.
	 * @param integer $count  Total number of results to return.
	 * @param array   $fq     Any facet queries to apply.
	 * @param string  $sortby Sort results by an attribute.
	 * @param string  $order  Order results ascending or descending.
	 * @param array   $fields Fields to include.
	 * @param array   $extra  Extra arguments to handle in the Solr query.
	 *
	 * @return Solarium\QueryType\Select\Result\Result
	 */
	function query( $qry, $offset, $count, $fq, $sortby, $order, $fields = null, $extra = array() ) {
		// NOTICE: does this needs to be cached to stop the db being hit to grab the options everytime search is being done.
		$plugin_s4wp_settings = solr_options();

		$solr = get_solr();

		return $this->master_query( $solr, $qry, $offset, $count, $fq, $sortby, $order, $plugin_s4wp_settings, $fields, null, $extra );
	}

	/**
	 * Perform a Solr query.
	 *
	 * @param Solarium\Client $solr   Solarium instance.
	 * @param string          $qry    Solr query.
	 * @param integer         $offset Offset for the query.
	 * @param integer         $count  Total number of results to return.
	 * @param array           $fq     Any facet queries to apply.
	 * @param string          $sortby Sort results by an attribute.
	 * @param string          $order  Order results ascending or descending.
	 * @param array           $plugin_s4wp_settings Plugin settings.
	 * @param array           $fields Fields to include.
	 * @param integer         $blogid Limit results to those of a specific blog.
	 * @param array           $extra  Extra arguments to handle in the Solr query.
	 *
	 * @return Solarium\QueryType\Select\Result\Result
	 */
	function master_query( $solr, $qry, $offset, $count, $fq, $sortby, $order, &$plugin_s4wp_settings, $fields = null, $blogid = null, $extra = array() ) {
		$this->add_log(
			array(
				'Search Query' => $qry,
				'Offset'       => $offset,
				'Count'        => $count,
				'Filter Query' => $fq,
				'Sort By'      => $sortby,
				'Order'        => $order,
			)
		);

		$response         = null;
		$facet_fields     = array();
		$number_of_tags   = $plugin_s4wp_settings['s4wp_max_display_tags'];
		$default_operator = ( isset( $plugin_s4wp_settings['s4wp_default_operator'] ) ) ? $plugin_s4wp_settings['s4wp_default_operator'] : 'OR';

		if ( $plugin_s4wp_settings['s4wp_facet_on_categories'] ) {
			$facet_fields[] = 'categories';
		}

		$facet_on_tags = $plugin_s4wp_settings['s4wp_facet_on_tags'];
		if ( $facet_on_tags ) {
			$facet_fields[] = 'tags';
		}

		if ( $plugin_s4wp_settings['s4wp_facet_on_author'] ) {
			$facet_fields[] = 'post_author';
		}

		if ( $plugin_s4wp_settings['s4wp_facet_on_type'] ) {
			$facet_fields[] = 'post_type';
		}

		$facet_on_custom_taxonomy = $plugin_s4wp_settings['s4wp_facet_on_taxonomy'];
		if ( $facet_on_custom_taxonomy ) {
			$taxonomies = (array) get_taxonomies(
				array(
					'_builtin' => false,
				),
				'names'
			);
			foreach ( $taxonomies as $parent ) {
				$facet_fields[] = $parent . '_taxonomy_str';
			}
		}

		$facet_on_custom_fields = $plugin_s4wp_settings['s4wp_facet_on_custom_fields'];
		/**
		 * Filter indexed custom fields
		 *
		 * Filter the list of custom field slugs available to index.
		 *
		 * @param array $facet_on_custom_fields Array of custom field slugs for indexing.
		 */
		$facet_on_custom_fields = apply_filters( 'solr_facet_custom_fields', $facet_on_custom_fields );
		if ( is_array( $facet_on_custom_fields ) and count( $facet_on_custom_fields ) ) {
			$facet_on_custom_fields = array_unique( $facet_on_custom_fields );
			foreach ( $facet_on_custom_fields as $field_name ) {
				$facet_fields[] = $field_name . '_str';
			}
		}
		$count = ( $count < 1 ) ? apply_filters( 'solr_max_search_results', 50000 ) : $count;
		if ( $solr ) {
			$select         = array(
				'query'      => $qry,
				'fields'     => '*,score',
				'start'      => $offset,
				'rows'       => $count,
				'omitheader' => false,
			);
			$select['sort'] = array();
			if ( ! empty( $extra['sort_before'] ) ) {
				$select['sort'] = array_merge( $select['sort'], $extra['sort_before'] );
			}
			if ( '' !== $sortby ) {
				$select['sort'][ $sortby ] = $order;
			} else {
				$select['sort']['post_date'] = 'desc';
			}
			$select    = apply_filters( 'solr_select_query', $select );
			$query     = $solr->createSelect( $select );
			$dismax    = $query->getDisMax();
			$facet_set = $query->getFacetSet();

			if ( is_multisite() ) {
				$facet_fields[] = 'blogid';
			}

			foreach ( $facet_fields as $facet_field ) {
				$facet_set->createFacetField( $facet_field )->setField( $facet_field );
			}

			/**
			 * Filter boost query
			 *
			 * Filter the items boosted in the Solr query.
			 *
			 * @param string $solr_boost_query String of items, with their boost applied.
			 */
			$solr_boost_query = apply_filters( 'solr_boost_query', 'post_title^2 post_content^1.2' );
			if ( false !== $solr_boost_query ) {
				$dismax->setBoostFunctions( $solr_boost_query );
			}

			/**
			 * Filter the Solarium dismax query object.
			 *
			 * @param object $dismax Solarium DisMax query object.
			 * @param object $query Solarium query object.
			 */
			$dismax = apply_filters( 'solr_dismax_query', $dismax, $query );

			$facet_set->setMinCount( 1 );
			if ( $facet_on_tags ) {
				$facet_set->setLimit( $number_of_tags );
			}

			if ( isset( $fq ) ) {
				if ( is_array( $fq ) ) {
					$fq = implode( ' ' . $default_operator . ' ', $fq );
				}
				if ( '' !== $fq ) {
					$query->createFilterQuery( 'searchfq' )->setQuery( $fq );
				}
			}
			if ( is_multisite() && false !== $blogid ) {
				if ( is_null( $blogid ) ) {
					$blogid = get_current_blog_id();
				}
				$query->createFilterQuery( 'blogid' )->setQuery( 'blogid:' . $blogid );
			}
			$query->getHighlighting()->setFields( 'post_content' );
			$query->getHighlighting()->setSimplePrefix( '<b>' );
			$query->getHighlighting()->setSimplePostfix( '</b>' );
			$query->getHighlighting()->setHighlightMultiTerm( true );

			$query->setQueryDefaultOperator( $default_operator );

			// Wildcards.
			if ( strstr( $query->getQuery(), '*:*' ) ) {
				$dismax->setQueryAlternative( $query->getQuery() );
				$query->setQuery( '' );
			}
			/**
			 * Filter the Solarium query object.
			 *
			 * @param object $query Solarium query object.
			 * @param object $dismax Solarium DisMax query object.
			 */
			$query = apply_filters( 'solr_query', $query, $dismax );

			try {
				$response = $solr->select( $query );
				if ( ! $response->getResponse()->getStatusCode() === 200 ) {
					$response = null;
				}
			} catch ( Exception $e ) {
				syslog( LOG_ERR, 'failed to query solr. ' . $e->getMessage() );
				$response = null;
			}
		} // End if().

		return $response;
	}

	/**
	 * Add items to debug log.
	 *
	 * @param array $item Array of items.
	 */
	function add_log( $item ) {
		$this->log = array_merge( $this->log, $item );
	}

	/**
	 * Loops through each public post type and returns array of index count.
	 *
	 * @return array
	 */
	function index_stats() {
		$cache_key = 'solr_index_stats';
		$stats     = wp_cache_get( $cache_key, 'solr' );
		if ( false === $stats ) {

			$post_types = SolrPower::get_post_types();

			$stats = array();
			foreach ( $post_types as $type ) {
				$stats[ $type ] = $this->fetch_stat( $type );
			}

			wp_cache_set( $cache_key, $stats, 'solr', 300 );
		}

		return $stats;
	}

	/**
	 * Queries Solr with specified post_type and returns number found.
	 *
	 * @param string $type Post type registered with WordPress.
	 * @return int
	 */
	private function fetch_stat( $type ) {
		// Can't do wildcard with dismax...
		add_filter( 'solr_query', array( $this, 'dismax_query' ), 10, 2 );
		$qry    = 'post_type:' . $type;
		$offset = 0;
		$count  = 1;
		$fq     = array();
		$sortby = 'score';
		$order  = 'desc';
		$search = $this->query( $qry, $offset, $count, $fq, $sortby, $order );
		if ( is_null( $search ) ) {
			return 0;
		}
		try {
			$search = $search->getData();
		} catch ( \Exception $e ) {
			return 0;
		}

		$search = $search['response'];

		return $search['numFound'];
	}

	/**
	 * Admin Notice Hook
	 * Checks HTTP status response code to determine schema submission.
	 * Pings Solr, checks exception code, and then submits schema.
	 * Displays error if schema submission fails.
	 */
	function check_for_schema() {
		$last_check = get_transient( 'schema_check' );
		if ( false === $last_check ) {

			if ( getenv( 'PANTHEON_ENVIRONMENT' ) !== false ) {
				// Ping Solr.
				$this->ping_server();

				if ( 404 === $this->last_code ) { // Schema is missing on Pantheon.
					$submit_schema = $this->submit_schema();
					if ( strpos( $submit_schema, 'Error' ) ) {
						echo '<div class="notice notice-error"><p>';
						echo '<h2>Solr Power Error:</h2>';
						echo 'Error posting schema.xml to ApacheSolr backend, which will prevent content from being indexed. You can try navigating to the Solr Power admin section in the WordPress dashboard to try posting the schema directly. If this problem persists, open a support ticket from you Pantheon site dashboard.';
						echo '</p></div>';
					}
				}
				// Set a transient so we are not checking on every page load.
				set_transient( 'schema_check', '1', 300 );
			}
		}
	}

	/**
	 * Returns an array of server information.
	 *
	 * @since 1.0
	 *
	 * @return array Array of server connection information.
	 */
	public function get_server_info() {

		$ping = $this->ping_server();

		return array(
			'ping_status' => $ping,
			'ip_address'  => getenv( 'PANTHEON_INDEX_HOST' ),
			'port'        => getenv( 'PANTHEON_INDEX_PORT' ),
			'path'        => $this->compute_path(),
		);

	}

	/**
	 * Set a basic dismax query
	 *
	 * @param Solarium\QueryType\Select\Query\Query            $query  Solarium query object.
	 * @param Solarium\QueryType\Select\Query\Component\DisMax $dismax Solarium dismax object.
	 *
	 * @return Solarium\QueryType\Select\Query\Query
	 */
	function dismax_query( $query, $dismax ) {
		$dismax->setQueryAlternative( $query->getQuery() );
		$query->setQuery( '' );

		return $query;
	}

	/**
	 * Check for the SOLR_POWER_SCHEME constant.
	 * If it exists and is "http" or "https", use it as the default scheme value.
	 */
	private function get_default_scheme() {
		if ( ! defined( 'SOLR_POWER_SCHEME' ) && getenv( 'SOLR_POWER_SCHEME' ) ) {
			define( 'SOLR_POWER_SCHEME', getenv( 'SOLR_POWER_SCHEME' ) );
		}
		$default_scheme = ( defined( 'SOLR_POWER_SCHEME' ) && 1 === preg_match( '/^http[s]?$/', SOLR_POWER_SCHEME ) ) ? SOLR_POWER_SCHEME : 'https';
		/**
		 * Filter server schema
		 *
		 * Filters the schema used to connect to the solr server (http/https).
		 *
		 * @param string $default_scheme The connection scheme to the solr server (http/https).
		 */
		return apply_filters( 'solr_scheme', $default_scheme );
	}

	/**
	 * Get the cert path, based on whether WP is installed in a subdir or not.
	 *
	 * @return string
	 */
	private static function get_cert_path() {
		if ( getenv( 'PANTHEON_ENVIRONMENT' ) !== false
			&& file_exists( $_SERVER['HOME'] . '/certs/binding.pem' ) ) {
			return $_SERVER['HOME'] . '/certs/binding.pem';
		}
		return realpath( ABSPATH . '../certs/binding.pem' );
	}

}
