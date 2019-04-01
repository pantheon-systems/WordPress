<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Utilities
 * @author    SkyVerge / Delicious Brains
 * @copyright Copyright (c) 2015-2016 Delicious Brains Inc.
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WP_Background_Job_Handler' ) ) :

/**
 * SkyVerge WordPress Background Job Handler class
 *
 * Based on the wonderful WP_Background_Process class by deliciousbrains:
 * https://github.com/A5hleyRich/wp-background-processing
 *
 * Subclasses SV_WP_Async_Request. Instead of the concept of `batches` used in
 * the Delicious Brains' version, however, this takes a more object-oriented approach
 * of background `jobs`, allowing greater control over manipulating job data and
 * processing.
 *
 * A batch implicitly expected an array of items to process, whereas a job does
 * not expect any particular data structure (although it does default to
 * looping over job data) and allows subclasses to provide their own
 * processing logic.
 *
 * # Sample usage:
 *
 * $background_job_handler = new SV_WP_Background_Job_Handler();
 * $job = $background_job_handler->create_job( $attrs );
 * $background_job_handler->dispatch();
 *
 * @since 4.4.0
 */
abstract class SV_WP_Background_Job_Handler extends SV_WP_Async_Request {


	/** @var string async request prefix */
	protected $prefix = 'sv_wp';

	/** @var string async request action */
	protected $action = 'background_job';

	/** @var string data key */
	protected $data_key = 'data';

	/** @var int start time of current process */
	protected $start_time = 0;

	/** @var string cron hook identifier */
	protected $cron_hook_identifier;

	/** @var string cron interval identifier */
	protected $cron_interval_identifier;

	/** @var string debug message, used by the system status tool */
	protected $debug_message;


	/**
	 * Initiate new background job handler
	 *
	 * @since 4.4.0
	 */
	public function __construct() {

		parent::__construct();

		$this->cron_hook_identifier     = $this->identifier . '_cron';
		$this->cron_interval_identifier = $this->identifier . '_cron_interval';

		$this->add_hooks();
	}


	/**
	 * Adds the necessary action and filter hooks.
	 *
	 * @since 4.8.0
	 */
	protected function add_hooks() {

		// cron healthcheck
		add_action( $this->cron_hook_identifier, array( $this, 'handle_cron_healthcheck' ) );
		add_filter( 'cron_schedules', array( $this, 'schedule_cron_healthcheck' ) );

		// debugging & testing
		add_action( "wp_ajax_nopriv_{$this->identifier}_test", array( $this, 'handle_connection_test_response' ) );
		add_filter( 'woocommerce_debug_tools', array( $this, 'add_debug_tool' ) );
		add_filter( 'gettext', array( $this, 'translate_success_message' ), 10, 3 );
	}


	/**
	 * Dispatch
	 *
	 * @since 4.4.0
	 * @return array|WP_Error
	 */
	public function dispatch() {

		// schedule the cron healthcheck
		$this->schedule_event();

		// perform remote post
		return parent::dispatch();
	}


	/**
	 * Maybe process job queue
	 *
	 * Checks whether data exists within the job queue and that
	 * the background process is not already running.
	 *
	 * @since 4.4.0
	 */
	public function maybe_handle() {

		if ( $this->is_process_running() ) {
			// background process already running
			wp_die();
		}

		if ( $this->is_queue_empty() ) {
			// no data to process
			wp_die();
		}

		// WC core does 2 things here that can interfere with our nonce check:
		// 1. WooCommerce starts a session due to our GET request to dispatch a job
		//  However, this happens *after* we've generated a nonce without a session (in CRON context)
		// 2. it then filters nonces for logged-out users indiscriminately without checking the nonce action; if
		//  there is a session created (and now the server does have one), it tries to filter every.single.nonce
		//  for logged-out users to use the customer session ID instead of 0 for user ID. We *want* to check
		//  against a UID of 0 (since that's how the nonce was created), so we temporarily pause the
		//  logged-out nonce hijacking before standing aside.
		remove_filter( 'nonce_user_logged_out', array( WC()->session, 'nonce_user_logged_out' ) );

		check_ajax_referer( $this->identifier, 'nonce' );

		// sorry, later nonce users! please play again
		add_filter( 'nonce_user_logged_out', array( WC()->session, 'nonce_user_logged_out' ) );

		$this->handle();

		wp_die();
	}


	/**
	 * Check whether job queue is empty or not
	 *
	 * @since 4.4.0
	 * @return bool True if queue is empty, false otherwise
	 */
	protected function is_queue_empty() {
		global $wpdb;

		$key = $this->identifier . '_job_%';

		// only queued or processing jobs count
		$queued     = '%"status":"queued"%';
		$processing = '%"status":"processing"%';

		$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$wpdb->options}
			WHERE option_name LIKE %s
			AND ( option_value LIKE %s OR option_value LIKE %s )
		", $key, $queued, $processing ) );

		return ( $count > 0 ) ? false : true;
	}


	/**
	 * Check whether background process is running or not
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 *
	 * @since 4.4.0
	 * @return bool True if processing is running, false otherwise
	 */
	protected function is_process_running() {

		// add a random artificial delay to prevent a race condition if 2 or more processes are trying to 
		// process the job queue at the very same moment in time and neither of them have yet set the lock
		// before the others are calling this method
		usleep( rand( 100000, 300000 ) );

		return (bool) get_transient( "{$this->identifier}_process_lock" );
	}


	/**
	 * Lock process
	 *
	 * Lock the process so that multiple instances can't run simultaneously.
	 * Override if applicable, but the duration should be greater than that
	 * defined in the time_exceeded() method.
	 *
	 * @since 4.4.0
	 */
	protected function lock_process() {

		// set start time of current process
		$this->start_time = time();

		// set lock duration to 1 minute by default
		$lock_duration = ( property_exists( $this, 'queue_lock_time' ) ) ? $this->queue_lock_time : 60;

		/**
		 * Filter the queue lock time
		 *
		 * @since 4.4.0
		 * @param int $lock_duration Lock duration in seconds
		 */
		$lock_duration = apply_filters( "{$this->identifier}_queue_lock_time", $lock_duration );

		set_transient( "{$this->identifier}_process_lock", microtime(), $lock_duration );
	}


	/**
	 * Unlock process
	 *
	 * Unlock the process so that other instances can spawn.
	 *
	 * @since 4.4.0
	 * @return \SV_WP_Background_Job_Handler
	 */
	protected function unlock_process() {

		delete_transient( "{$this->identifier}_process_lock" );

		return $this;
	}


	/**
	 * Check if memory limit is exceeded
	 *
	 * Ensures the background job handler process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @since 4.4.0
	 *
	 * @return bool True if exceeded memory limit, false otherwise
	 */
	protected function memory_exceeded() {

		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;

		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		/**
		 * Filter whether memory limit has been exceeded or not
		 *
		 * @since 4.4.0
		 *
		 * @param bool $exceeded
		 */
		return apply_filters( "{$this->identifier}_memory_exceeded", $return );
	}


	/**
	 * Get memory limit
	 *
	 * @since 4.4.0
	 *
	 * @return int memory limit in bytes
	 */
	protected function get_memory_limit() {

		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// sensible default
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 == $memory_limit ) {
			// unlimited, set to 32GB
			$memory_limit = '32000M';
		}

		return intval( $memory_limit ) * 1024 * 1024;
	}


	/**
	 * Check whether request time limit has been exceeded or not
	 *
	 * Ensures the background job handler never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @since 4.4.0
	 *
	 * @return bool True, if time limit exceeded, false otherwise
	 */
	protected function time_exceeded() {

		/**
		 * Filter default time limit for background job execution, defaults to
		 * 20 seconds
		 *
		 * @since 4.4.0
		 *
		 * @param int $time Time in seconds
		 */
		$finish = $this->start_time + apply_filters( "{$this->identifier}_default_time_limit", 20 );
		$return = false;

		if ( time() >= $finish ) {
			$return = true;
		}

		/**
		 * Filter whether maximum execution time has exceeded or not
		 *
		 * @since 4.4.0
		 * @param bool $exceeded true if execution time exceeded, false otherwise
		 */
		return apply_filters( "{$this->identifier}_time_exceeded", $return );
	}


	/**
	 * Create a background job
	 *
	 * Delicious Brains' versions alternative would be using ->data()->save().
	 * Allows passing in any kind of job attributes, which will be available at item data processing time.
	 * This allows sharing common options between items without the need to repeat
	 * the same information for every single item in queue.
	 *
	 * Instead of returning self, returns the job instance, which gives greater
	 * control over the job.
	 *
	 * @since 4.4.0
	 *
	 * @param mixed $attrs Job attributes.
	 * @return object|null
	 */
	public function create_job( $attrs ) {
		global $wpdb;

		if ( empty( $attrs ) ) {
			return null;
		}

		// generate a unique ID for the job
		$job_id = md5( microtime() . mt_rand() );

		/**
		 * Filter new background job attributes
		 *
		 * @since 4.4.0
		 *
		 * @param array $attrs Job attributes
		 * @param string $id Job ID
		 */
		$attrs = apply_filters( "{$this->identifier}_new_job_attrs", $attrs, $job_id );

		// ensure a few must-have attributes
		$attrs = wp_parse_args( array(
			'id'         => $job_id,
			'created_at' => current_time( 'mysql' ),
			'created_by' => get_current_user_id(),
			'status'     => 'queued',
		), $attrs );

		$wpdb->insert( $wpdb->options, array(
			'option_name'  => "{$this->identifier}_job_{$job_id}",
			'option_value' => json_encode( $attrs ),
			'autoload'     => 'no'
		) );

		$job = new stdClass();

		foreach ( $attrs as $key => $value ) {
			$job->{$key} = $value;
		}

		/**
		 * Run when a job is created
		 *
		 * @since 4.4.0
		 *
		 * @param object $job The created job
		 */
		do_action( "{$this->identifier}_job_created", $job );

		return $job;
	}


	/**
	 * Get a job (by default the first in the queue)
	 *
	 * @since 4.4.0
	 *
	 * @param string $id Optional. Job ID. Will return first job in queue if not
	 *                   provided. Will not return completed or failed jobs from queue.
	 * @return object|null The found job object or null
	 */
	public function get_job( $id = null ) {
		global $wpdb;

		if ( ! $id ) {

			$key        = $this->identifier . '_job_%';
			$queued     = '%"status":"queued"%';
			$processing = '%"status":"processing"%';

			$results = $wpdb->get_var( $wpdb->prepare( "
				SELECT option_value
				FROM {$wpdb->options}
				WHERE option_name LIKE %s
				AND ( option_value LIKE %s OR option_value LIKE %s )
				ORDER BY option_id ASC
				LIMIT 1
			", $key, $queued, $processing ) );

		} else {

			$results = $wpdb->get_var( $wpdb->prepare( "
				SELECT option_value
				FROM {$wpdb->options}
				WHERE option_name = %s
			", "{$this->identifier}_job_{$id}" ) );

		}

		if ( ! empty( $results ) ) {

			$job = new stdClass();

			foreach ( json_decode( $results, true ) as $key => $value ) {
				$job->{$key} = $value;
			}

		} else {
			return null;
		}

		/**
		 * Filter job as returned from the database
		 *
		 * @since 4.4.0
		 *
		 * @param object $job
		 */
		return apply_filters( "{$this->identifier}_returned_job", $job );
	}


	/**
	 * Get jobs
	 *
	 * @since 4.4.2
	 *
	 * @param array $args {
	 *     Optional. An array of arguments
	 *
	 *     @type string|array $status Job status(es) to include
	 *     @type string $order ASC or DESC. Defaults to DESC
	 *     @type string $orderby Field to order by. Defaults to option_id
	 * }
	 * @return array|null Found jobs or null if none found
	 */
	public function get_jobs( $args = array() ) {
		global $wpdb;

		$args = wp_parse_args( $args, array(
			'order'   => 'DESC',
			'orderby' => 'option_id',
		) );

		$replacements = array( $this->identifier . '_job_%' );
		$status_query = '';

		// prepare status query
		if ( ! empty( $args['status'] ) ) {

			$statuses     = (array) $args['status'];
			$placeholders = array();

			foreach ( $statuses as $status ) {

				$placeholders[] = '%s';
				$replacements[] = '%"status":"' . sanitize_key( $status ) . '"%';
			}

			$status_query = 'AND ( option_value LIKE ' . implode( ' OR option_value LIKE ', $placeholders ) . ' )';
		}

		// prepare sorting vars
		$order   = sanitize_key( $args['order'] );
		$orderby = sanitize_key( $args['orderby'] );

		// put it all together now
		$query = $wpdb->prepare( "
			SELECT option_value
			FROM {$wpdb->options}
			WHERE option_name LIKE %s
			{$status_query}
			ORDER BY {$orderby} {$order}
		", $replacements );

		$results = $wpdb->get_col( $query );

		if ( empty( $results ) ) {
			return null;
		}

		$jobs = array();

		foreach ( $results as $result ) {

			$job = new stdClass();

			foreach ( json_decode( $result, true ) as $key => $value ) {
				$job->{$key} = $value;
			}

			/** This filter is documented above */
			$job = apply_filters( "{$this->identifier}_returned_job", $job );

			$jobs[] = $job;
		}

		return $jobs;
	}


	/**
	 * Handle
	 *
	 * Process jobs while remaining within server memory and time limit constraints.
	 *
	 * @since 4.4.0
	 */
	protected function handle() {

		$this->lock_process();

		do {

			// Get next job in the queue
			$job = $this->get_job();

			// handle PHP errors from here on out
			register_shutdown_function( array( $this, 'handle_shutdown' ), $job );

			// Start processing
			$this->process_job( $job );

		} while ( ! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next job or complete process
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}

		wp_die();
	}


	/**
	 * Process a job
	 *
	 * Default implementation is to loop over job data and passing each item to
	 * the item processor. Subclasses are, however, welcome to override this method
	 * to create totally different job processing implementations - see
	 * WC_CSV_Import_Suite_Background_Import in CSV Import for an example.
	 *
	 * If using the default implementation, the job must have a $data_key property set.
	 * Subclasses can override the data key, but the contents must be an array which
	 * the job processor can loop over. By default, the data key is `data`.
	 *
	 * If no data is set, the job will completed right away.
	 *
	 * @since 4.4.0
	 *
	 * @param object $job
	 * @param int $items_per_batch number of items to process in a single request. Defaults to unlimited.
	 * @throws \Exception when job data is incorrect
	 * @return object $job
	 */
	public function process_job( $job, $items_per_batch = null ) {

		if ( ! $this->start_time ) {
			$this->start_time = time();
		}

		// Indicate that the job has started processing
		if ( 'processing' !== $job->status ) {

			$job->status                = 'processing';
			$job->started_processing_at = current_time( 'mysql' );

			$job = $this->update_job( $job );
		}

		$data_key = $this->data_key;

		if ( ! isset( $job->{$data_key} ) ) {
			throw new Exception( sprintf( __( 'Job data key "%s" not set', 'woocommerce-plugin-framework' ), $data_key ) );
		}

		if ( ! is_array( $job->{$data_key} ) ) {
			throw new Exception( sprintf( __( 'Job data key "%s" is not an array', 'woocommerce-plugin-framework' ), $data_key ) );
		}

		$data = $job->{$data_key};

		$job->total = count( $data );

		// progress indicates how many items have been processed, it
		// does NOT indicate the processed item key in any way
		if ( ! isset( $job->progress ) ) {
			$job->progress = 0;
		}

		// skip already processed items
		if ( $job->progress && ! empty( $data ) ) {
			$data = array_slice( $data, $job->progress, null, true );
		}

		// loop over unprocessed items and process them
		if ( ! empty( $data ) ) {

			$processed       = 0;
			$items_per_batch = (int) $items_per_batch;

			foreach ( $data as $item ) {

				// process the item
				$this->process_item( $item, $job );

				$processed++;
				$job->progress++;

				// update job progress
				$job = $this->update_job( $job );

				// job limits reached
				if ( ( $items_per_batch && $processed >= $items_per_batch ) || $this->time_exceeded() || $this->memory_exceeded() ) {
					break;
				}
			}
		}

		// complete current job
		if ( $job->progress >= count( $job->{$data_key} ) ) {
			$job = $this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Update job attrs
	 *
	 * @since 4.4.0
	 *
	 * @param object|string $job Job instance or ID
	 * @return object|false on failure
	 */
	public function update_job( $job ) {

		if ( is_string( $job ) ) {
			$job = $this->get_job( $job );
		}

		if ( ! $job ) {
			return false;
		}

		$job->updated_at = current_time( 'mysql' );

		$this->update_job_option( $job );

		/**
		 * Run when a job is updated
		 *
		 * @since 4.4.0
		 * @param object $job The updated job
		 */
		do_action( "{$this->identifier}_job_updated", $job );

		return $job;
	}


	/**
	 * Handle job completion
	 *
	 * @since 4.4.0
	 *
	 * @param object|string $job Job instance or ID
	 * @return object|false on failure
	 */
	public function complete_job( $job ) {

		if ( is_string( $job ) ) {
			$job = $this->get_job( $job );
		}

		if ( ! $job ) {
			return false;
		}

		$job->status       = 'completed';
		$job->completed_at = current_time( 'mysql' );

		$this->update_job_option( $job );

		/**
		 * Run when a job is completed
		 *
		 * @since 4.4.0
		 * @param object $job The completed job
		 */
		do_action( "{$this->identifier}_job_complete", $job );

		return $job;
	}


	/**
	 * Handle job failure
	 *
	 * Default implementation does not call this method directly, but it's
	 * provided as a convenience method for subclasses that may call this to
	 * indicate that a particular job has failed for some reason.
	 *
	 * @since 4.4.0
	 *
	 * @param object|string $job Job instance or ID
	 * @param string $reason Optional. Reason for failure.
	 * @return object|false on failure
	 */
	public function fail_job( $job, $reason = '' ) {

		if ( is_string( $job ) ) {
			$job = $this->get_job( $job );
		}

		if ( ! $job ) {
			return false;
		}

		$job->status    = 'failed';
		$job->failed_at = current_time( 'mysql' );

		if ( $reason ) {
			$job->failure_reason = $reason;
		}

		$this->update_job_option( $job );

		/**
		 * Run when a job is failed
		 *
		 * @since 4.4.0
		 *
		 * @param object $job The failed job
		 */
		do_action( "{$this->identifier}_job_failed", $job );

		return $job;
	}


	/**
	 * Delete a job
	 *
	 * @since 4.4.2
	 *
	 * @param object|string $job Job instance or ID
	 * @return false on failure
	 */
	public function delete_job( $job ) {
		global $wpdb;

		if ( is_string( $job ) ) {
			$job = $this->get_job( $job );
		}

		if ( ! $job ) {
			return false;
		}

		$wpdb->delete( $wpdb->options, array( 'option_name' => "{$this->identifier}_job_{$job->id}" ) );

		/**
		* Run after a job is deleted
		*
		* @since 4.4.2
		*
		* @param object $job The job that was deleted from database
		*/
		do_action( "{$this->identifier}_job_deleted", $job );
	}


	/**
	 * Handle job queue completion
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 *
	 * @since 4.4.0
	 */
	protected function complete() {

		// unschedule the cron healthcheck
		$this->clear_scheduled_event();
	}

	/**
	 * Schedule cron healthcheck
	 *
	 * @since 4.4.0
	 * @param array $schedules
	 * @return mixed
	 */
	public function schedule_cron_healthcheck( $schedules ) {

		$interval = property_exists( $this, 'cron_interval' ) ? $this->cron_interval : 5;

		/**
		 * Filter cron health check interval
		 *
		 * @since 4.4.0
		 * @param int $interval Interval in minutes
		 */
		$interval = apply_filters( "{$this->identifier}_cron_interval", $interval );

		// adds every 5 minutes to the existing schedules.
		$schedules[ $this->identifier . '_cron_interval' ] = array(
			'interval' => MINUTE_IN_SECONDS * $interval,
			'display'  => sprintf( __( 'Every %d Minutes' ), $interval ),
		);

		return $schedules;
	}


	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 *
	 * @since 4.4.0
	 */
	public function handle_cron_healthcheck() {

		if ( $this->is_process_running() ) {
			// background process already running
			exit;
		}

		if ( $this->is_queue_empty() ) {
			// no data to process
			$this->clear_scheduled_event();
			exit;
		}

		$this->dispatch();
	}


	/**
	 * Schedule cron health check event
	 *
	 * @since 4.4.0
	 */
	protected function schedule_event() {

		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			
			// schedule the health check to fire after 30 seconds from now, as to not create a race condition
			// with job process lock on servers that fire & handle cron events instantly
			wp_schedule_event( time() + 30, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}


	/**
	 * Clear scheduled health check event
	 *
	 * @since 4.4.0
	 */
	protected function clear_scheduled_event() {

		$timestamp = wp_next_scheduled( $this->cron_hook_identifier );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $this->cron_hook_identifier );
		}
	}


	/**
	 * Process an item from job data
	 *
	 * Implement this method to perform any actions required on each
	 * item in job data.
	 *
	 * @since 4.4.2
	 *
	 * @param mixed $item Job data item to iterate over
	 * @param object $job Job instance
	 * @return mixed
	 */
	abstract protected function process_item( $item, $job );


	/**
	 * Handles PHP shutdown, say after a fatal error.
	 *
	 * @since 4.5.0
	 * @param object $job the job being processed
	 */
	public function handle_shutdown( $job ) {

		$error = error_get_last();

		// if shutting down because of a fatal error, fail the job
		if ( $error && E_ERROR === $error['type'] ) {

			$this->fail_job( $job, $error['message'] );

			$this->unlock_process();
		}
	}


	/**
	 * Update a job option in options database.
	 *
	 * @since 4.6.3
	 *
	 * @param object $job the job instance to update in database
	 * @return int|bool number of rows updated or false on failure, see wpdb::update()
	 */
	private function update_job_option( $job ) {
		global $wpdb;

		return $wpdb->update(
			$wpdb->options,
			array( 'option_value' => json_encode( $job ) ),
			array( 'option_name'  => "{$this->identifier}_job_{$job->id}" )
		);
	}


	/** Debug & Testing Methods ***********************************************/


	/**
	 * Tests the background handler's connection.
	 *
	 * @since 4.8.0
	 *
	 * @return bool
	 */
	public function test_connection() {

		$test_url = add_query_arg( 'action', "{$this->identifier}_test", admin_url( 'admin-ajax.php' ) );
		$result   = wp_safe_remote_get( $test_url );
		$body     = ! is_wp_error( $result ) ? wp_remote_retrieve_body( $result ) : null;

		// some servers may add a UTF8-BOM at the beginning of the response body, so we check if our test
		// string is included in the body, as an equal check would produce a false negative test result
		return $body && SV_WC_Helper::str_exists( $body, '[TEST_LOOPBACK]' );
	}


	/**
	 * Handles the connection test request.
	 *
	 * @since 4.8.0
	 */
	public function handle_connection_test_response() {

		echo '[TEST_LOOPBACK]';
		exit;
	}


	/**
	 * Adds the WooCommerce debug tool.
	 *
	 * @since 4.8.0
	 *
	 * @param array $tools WooCommerce core tools
	 * @return array
	 */
	public function add_debug_tool( $tools ) {

		// this key is not unique to the plugin to avoid duplicate tools
		$tools['sv_wc_background_job_test'] = array(
			'name'     => __( 'Background Processing Test', 'woocommerce-plugin-framework' ),
			'button'   => __( 'Run Test', 'woocommerce-plugin-framework' ),
			'desc'     => __( 'This tool will test whether your server is capable of processing background jobs.', 'woocommerce-plugin-framework' ),
			'callback' => array( $this, 'run_debug_tool' ),
		);

		return $tools;
	}


	/**
	 * Runs the test connection debug tool.
	 *
	 * @since 4.8.0
	 *
	 * @return string
	 */
	public function run_debug_tool() {

		if ( $this->test_connection() ) {
			$this->debug_message = __( 'Success! You should be able to process background jobs.', 'woocommerce-plugin-framework' );
			$result = true;
		} else {
			$this->debug_message = __( 'Could not connect. Please ask your hosting company to ensure your server has loopback connections enabled.', 'woocommerce-plugin-framework' );
			$result = false;
		}

		// WC 2.6 has no positive message output by default
		if ( SV_WC_Plugin_Compatibility::is_wc_version_lt_3_0() && $result ) {
			echo "<div class='updated inline'><p>{$this->debug_message}</p></div>";
		}

		return $result;
	}


	/**
	 * Translate the tool success message.
	 *
	 * This can be removed in favor of returning the message string in `run_debug_tool()`
	 *  when WC 3.1 is required, though that means the message will always be "success" styled.
	 *
	 * @since 4.8.0
	 *
	 * @param string $translated the text to output
	 * @param string $original the original text
	 * @param string $domain the textdomain
	 * @return string the updated text
	 */
	public function translate_success_message( $translated, $original, $domain ) {

		if ( 'woocommerce' === $domain && ( 'Tool ran.' === $original || 'There was an error calling %s' === $original ) ) {
			$translated = $this->debug_message;
		}

		return $translated;
	}


	/** Helper Methods ********************************************************/


	/**
	 * Gets the job handler identifier.
	 *
	 * @since 4.8.0
	 *
	 * @return string
	 */
	public function get_identifier() {

		return $this->identifier;
	}


}

endif; // Class exists check
