<?php
/**
 * Index a batch of WordPress posts
 *
 * @package Solr_Power
 */

/**
 * Index a batch of WordPress posts
 */
class SolrPower_Batch_Index {

	/**
	 * Total posts to be indexed.
	 *
	 * @var integer
	 */
	private $total_posts;

	/**
	 * Remaining posts to be indexed.
	 *
	 * @var integer
	 */
	private $remaining_posts;

	/**
	 * Query arguments used to fetch post ids from WP_Query.
	 *
	 * @var array
	 */
	private $query_args;

	/**
	 * Solr update instance
	 *
	 * @var object
	 */
	private $solr_update;

	/**
	 * Total batches of posts.
	 *
	 * @var integer
	 */
	private $total_batches;

	/**
	 * Cache key for resuming batch at specific offset
	 *
	 * @var string
	 */
	private $batch_cache_key;

	/**
	 * Number of posts successfully indexed.
	 *
	 * @var integer
	 */
	private $success_posts = 0;

	/**
	 * Number of posts failed to index.
	 *
	 * @var integer
	 */
	private $failed_posts = 0;

	/**
	 * Post ids to index.
	 *
	 * @var array
	 */
	private $post_ids = array();

	/**
	 * Instantiate the batch index object.
	 *
	 * @param array $query_args Override arguments passed to WP_Query.
	 */
	public function __construct( $query_args = array() ) {
		$defaults         = array(
			'post_status'    => SolrPower::get_post_statuses(),
			'post_type'      => SolrPower::get_post_types(),
			'posts_per_page' => 100,
		);
		$clean_query_args = array();
		foreach ( $defaults as $key => $value ) {
			$clean_query_args[ $key ] = isset( $query_args[ $key ] ) ? $query_args[ $key ] : $value;
		}
		// Always need to iterate post ids.
		$clean_query_args['fields']  = 'ids';
		$clean_query_args['orderby'] = 'ID';
		$clean_query_args['order']   = 'ASC';
		// Generate a cache key to store the current page.
		$this->batch_cache_key = 'solr_power_batch_' . md5( serialize( $clean_query_args ) );
		// Include 'paged' always starts at that page,
		// otherwise try to restore the page from cache.
		if ( isset( $query_args['batch'] ) ) {
			$clean_query_args['paged'] = $query_args['batch'];
		} else {
			$clean_query_args['paged'] = get_option( $this->batch_cache_key, 1 );
		}
		$this->query_args = $clean_query_args;
		// Cache the 'paged' value for resuming.
		delete_option( $this->batch_cache_key );
		add_option( $this->batch_cache_key, $this->query_args['paged'], null, false );
		$query          = new WP_Query( $clean_query_args );
		$this->post_ids = $query->posts;
		$found_posts    = $query->found_posts;
		if ( $this->query_args['paged'] > 1 ) {
			$found_posts = $found_posts - ( ( $this->query_args['paged'] - 1 ) * $this->query_args['posts_per_page'] );
		}
		$this->total_posts     = $found_posts;
		$this->remaining_posts = $found_posts;
		$this->total_batches   = $query->max_num_pages;
		// Initialize the Solr updater.
		$solr              = get_solr();
		$this->solr_update = $solr->createUpdate();
	}

	/**
	 * Get the total posts to be indexed.
	 *
	 * @return integer
	 */
	public function get_total_posts() {
		return $this->total_posts;
	}

	/**
	 * Get the remaining posts to be indexed.
	 *
	 * @return integer
	 */
	public function get_remaining_posts() {
		return $this->remaining_posts;
	}

	/**
	 * Get the number of posts successfully indexed.
	 *
	 * @return integer
	 */
	public function get_success_posts() {
		return $this->success_posts;
	}

	/**
	 * Get the number of posts that failed to be indexed.
	 *
	 * @return integer
	 */
	public function get_failed_posts() {
		return $this->failed_posts;
	}

	/**
	 * Whether or not there are remaining posts to index.
	 *
	 * @return bool
	 */
	public function have_posts() {
		return ! empty( $this->post_ids );
	}

	/**
	 * Get the current page for this batch index.
	 *
	 * @return integer
	 */
	public function get_current_batch() {
		return $this->query_args['paged'];
	}

	/**
	 * Get the total batches that need to be indexed.
	 *
	 * @return integer
	 */
	public function get_total_batches() {
		return $this->total_batches;
	}

	/**
	 * Fetch the next posts to index.
	 *
	 * @return bool
	 */
	public function fetch_next_posts() {
		self::clear_object_cache();
		$this->increment_page();
		$query          = new WP_Query( $this->query_args );
		$this->post_ids = $query->posts;
		if ( ! empty( $this->post_ids ) ) {
			return true;
		}
		// Out of posts, so remove our offset.
		delete_option( $this->batch_cache_key );
		return false;
	}

	/**
	 * Increment value for 'paged', and update its value in the cache
	 */
	public function increment_page() {
		$this->query_args['paged']++;
		delete_option( $this->batch_cache_key );
		add_option( $this->batch_cache_key, $this->query_args['paged'], null, false );
	}

	/**
	 * Index the next post in the stack.
	 *
	 * @return array Result of the index process.
	 */
	public function index_post() {
		$result = array(
			'post_id'    => 0,
			'post_title' => '',
			'status'     => '',
			'message'    => '',
		);
		if ( empty( $this->post_ids ) ) {
			return $result;
		}
		$post_id              = array_shift( $this->post_ids );
		$post                 = get_post( $post_id );
		$result['post_id']    = $post_id;
		$result['post_title'] = html_entity_decode( $post->post_title );
		$documents[]          = SolrPower_Sync::get_instance()->build_document( $this->solr_update->createDocument(), $post );
		$sync_result          = SolrPower_Sync::get_instance()->post( $documents, true, false );
		if ( false !== $sync_result ) {
			$this->success_posts++;
			$result['status'] = 'success';
		} else {
			$this->failed_posts++;
			$result['status'] = 'failed';
			$error_msg        = SolrPower_Sync::get_instance()->error_msg;
			// Error messages are html-escaped.
			if ( preg_match( '#&lt;h1&gt;(.+)&lt;/h1&gt;#is', $error_msg, $matches ) ) {
				// Pull out contents of <h1> tags, if present.
				$error_msg = $matches[1];
			} elseif ( preg_match( '#&lt;title&gt;(.+)&lt;/title&gt;#is', $error_msg, $matches ) ) {
				// Otherwise, pull out contents of <title>.
				$error_msg = $matches[1];
			}
			$result['message'] = $error_msg;
		}
		$this->remaining_posts--;
		return $result;
	}

	/**
	 * Clear objects in WordPress internal object cache.
	 *
	 * Because WordPress' in-memory cache can grow indefinitely, it needs to
	 * be cleared occasionally for long processes.
	 */
	private static function clear_object_cache() {
		global $wpdb, $wp_object_cache;

		$wpdb->queries = array();

		if ( ! is_object( $wp_object_cache ) ) {
			return;
		}

		$wp_object_cache->group_ops      = array();
		$wp_object_cache->stats          = array();
		$wp_object_cache->memcache_debug = array();
		$wp_object_cache->cache          = array();

		if ( is_callable( $wp_object_cache, '__remoteset' ) ) {
			$wp_object_cache->__remoteset();
		}
	}

}
