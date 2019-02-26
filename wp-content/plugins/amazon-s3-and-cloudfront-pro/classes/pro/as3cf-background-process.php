<?php

abstract class AS3CF_Background_Process extends AS3CF_Async_Request {

	/**
	 * @var
	 */
	protected $action = 'background-process';

	/**
	 * Start time of current process
	 *
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * @var int
	 */
	const STATUS_CANCELLED = 1;

	/**
	 * @var int;
	 */
	const STATUS_PAUSED = 2;

	/**
	 * Initiate new background process
	 *
	 * @param object $as3cf Instance of calling class
	 */
	public function __construct( $as3cf ) {
		parent::__construct( $as3cf );

		add_action( $this->identifier . '_cron', array( $this, 'handle_cron_healthcheck' ) );
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
	}

	/**
	 * Dispatch
	 *
	 * @return array|WP_Error
	 */
	public function dispatch() {
		$this->schedule_cron_healthcheck();

		// Perform remote post
		parent::dispatch();
	}

	/**
	 * Push to queue
	 *
	 * @param mixed $data
	 *
	 * @return $this
	 */
	public function push_to_queue( $data ) {
		$this->data[] = $data;

		return $this;
	}

	/**
	 * Save queue
	 *
	 * @return $this
	 */
	public function save() {
		$key = $this->generate_key( 'batch' );

		if ( ! empty( $this->data ) ) {
			update_site_option( $key, $this->data );
		}

		// Clean out data so that new data isn't prepended with closed session's data.
		$this->data = array();

		return $this;
	}

	/**
	 * Update queue
	 *
	 * @param string $key
	 * @param array  $data
	 *
	 * @return $this
	 */
	public function update( $key, $data ) {
		if ( ! empty( $data ) ) {
			update_site_option( $key, $data );
		}

		return $this;
	}

	/**
	 * Delete job.
	 *
	 * @param string $key
	 *
	 * @return $this
	 */
	public function delete( $key ) {
		delete_site_option( $key );

		return $this;
	}

	/**
	 * Delete entire job queue.
	 */
	public function delete_all() {
		$batches = $this->get_batches();

		foreach ( $batches as $batch ) {
			$this->delete( $batch->key );
		}

		delete_site_option( $this->get_status_key() );
	}

	/**
	 * Cancel job on next batch.
	 */
	public function cancel() {
		if ( $this->is_process_running() ) {
			update_site_option( $this->identifier . '_status', self::STATUS_CANCELLED );
		} else {
			$this->delete_all();
		}
	}

	/**
	 * Has the process been cancelled?
	 *
	 * @return bool
	 */
	public function is_cancelled() {
		$status = get_site_option( $this->get_status_key(), 0 );

		if ( absint( $status ) === self::STATUS_CANCELLED ) {
			return true;
		}

		return false;
	}

	/**
	 * Pause job on next batch.
	 */
	public function pause() {
		update_site_option( $this->get_status_key(), self::STATUS_PAUSED );
	}

	/**
	 * Is the job paused?
	 *
	 * @return bool
	 */
	public function is_paused() {
		$status = get_site_option( $this->get_status_key(), 0 );

		if ( absint( $status ) === self::STATUS_PAUSED ) {
			return true;
		}

		return false;
	}

	/**
	 * Resume job.
	 */
	public function resume() {
		delete_site_option( $this->get_status_key() );

		$this->schedule_cron_healthcheck();
		$this->dispatch();
	}

	/**
	 * Generate key
	 *
	 * Generates a unique key based on microtime. Queue items are
	 * given a unique key so that they can be merged upon save.
	 *
	 * @param string $key
	 * @param int    $length
	 *
	 * @return string
	 */
	protected function generate_key( $key = '', $length = 64 ) {
		$unique  = md5( microtime() . rand() );
		$prepend = $this->identifier . '_' . $key . '_';

		return substr( $prepend . $unique, 0, $length );
	}

	/**
	 * Get the status key.
	 *
	 * @return string
	 */
	protected function get_status_key() {
		return $this->identifier . '_status';
	}

	/**
	 * Maybe process queue
	 *
	 * Checks whether data exists within the queue and that
	 * the process is not already running.
	 */
	public function maybe_handle() {
		// Don't lock up other requests while processing
		session_write_close();

		if ( $this->is_process_running() ) {
			// Background process already running
			wp_die();
		}

		if ( $this->is_cancelled() ) {
			$this->delete_all();

			wp_die();
		}

		if ( $this->is_paused() ) {
			$this->clear_cron_healthcheck();

			wp_die();
		}

		if ( $this->is_queue_empty() ) {
			// No data to process
			wp_die();
		}

		check_ajax_referer( $this->identifier, 'nonce' );

		$this->handle();

		wp_die();
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	protected function is_queue_empty() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $this->identifier . '_batch_%';

		$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$table}
			WHERE {$column} LIKE %s
		", $key ) );

		return ( $count > 0 ) ? false : true;
	}

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	public function is_process_running() {
		if ( get_site_transient( $this->identifier . '_process_lock' ) ) {
			// Process already running
			return true;
		}

		return false;
	}

	/**
	 * Lock process
	 *
	 * Lock the process so that multiple instances can't run simultaneously.
	 * Override if applicable, but the duration should be greater than that
	 * defined in the time_exceeded() method.
	 */
	protected function lock_process() {
		$this->start_time = time(); // Set start time of current process

		$lock_duration = ( property_exists( $this, 'queue_lock_time' ) ) ? $this->queue_lock_time : 60; // 1 minute
		$lock_duration = apply_filters( $this->identifier . '_queue_lock_time', $lock_duration );

		set_site_transient( $this->identifier . '_process_lock', microtime(), $lock_duration );
	}

	/**
	 * Unlock process
	 *
	 * Unlock the process so that other instances can spawn.
	 *
	 * @return $this
	 */
	protected function unlock_process() {
		delete_site_transient( $this->identifier . '_process_lock' );

		return $this;
	}

	/**
	 * Get batch
	 *
	 * @return stdClass Return the first batch from the queue
	 */
	protected function get_batch() {
		return array_reduce(
			$this->get_batches( 1 ),
			function ( $carry, $batch ) {
				return $batch;
			},
			array()
		);
	}

	/**
	 * Get batches
	 *
	 * @param int $limit Number of batches to return, defaults to all.
	 *
	 * @return array of stdClass
	 */
	public function get_batches( $limit = 0 ) {
		global $wpdb;

		if ( empty( $limit ) || ! is_int( $limit ) ) {
			$limit = 0;
		}

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		$key = $this->identifier . '_batch_%';

		$sql = "
			SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC
			";

		if ( ! empty( $limit ) ) {
			$sql .= " LIMIT {$limit}";
		}

		$items = $wpdb->get_results( $wpdb->prepare( $sql, $key ) );

		$batches = array();

		if ( ! empty( $items ) ) {
			$batches = array_map(
				function ( $item ) use ( $column, $value_column ) {
					$batch       = new stdClass();
					$batch->key  = $item->$column;
					$batch->data = maybe_unserialize( $item->$value_column );

					return $batch;
				},
				$items
			);
		}

		return $batches;
	}

	/**
	 * Handle
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 */
	protected function handle() {
		$this->lock_process();

		/**
		 * Number of seconds to sleep between batches. Defaults to 0 seconds, minimum 0.
		 */
		$throttle_seconds = apply_filters( 'as3cf_seconds_between_batches', 0 );

		do {
			$batch = $this->get_batch();

			foreach ( $batch->data as $key => $value ) {
				if ( $this->time_exceeded() || $this->memory_exceeded() ) {
					// Batch limits reached
					break;
				}

				$task = $this->task( $value );

				if ( false !== $task ) {
					$batch->data[ $key ] = $task;
				} else {
					unset( $batch->data[ $key ] );
				}

				// Let the server breathe a little.
				sleep( $throttle_seconds );
			}

			// Update or delete current batch
			if ( ! empty( $batch->data ) ) {
				$this->update( $batch->key, $batch->data );
			} else {
				$this->delete( $batch->key );
			}
		} while ( ! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next batch or complete process
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}

		wp_die();
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		return $this->as3cf->memory_exceeded( $this->identifier . '_memory_exceeded' );
	}

	/**
	 * Time exceeded
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + apply_filters( 'as3cf_default_time_limit', 20 ); // 20 seconds
		$return = false;

		if ( time() >= $finish ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_time_exceeded', $return );
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		delete_site_option( $this->get_status_key() );

		$this->clear_cron_healthcheck();
	}

	/**
	 * Add cron schedules.
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 */
	public function cron_schedules( $schedules ) {
		$interval = apply_filters( $this->identifier . '_cron_interval', 5 );

		if ( property_exists( $this, 'cron_interval' ) ) {
			$interval = apply_filters( $this->identifier . '_cron_interval', $this->cron_interval );
		}

		// Adds every 5 minutes to the existing schedules.
		$schedules[ $this->identifier . '_cron_interval' ] = array(
			'interval' => MINUTE_IN_SECONDS * $interval,
			'display'  => sprintf( __( 'Every %d Minutes', 'amazon-s3-and-cloudfront' ), $interval ),
		);

		return $schedules;
	}

	/**
	 * Schedule cron health check.
	 */
	protected function schedule_cron_healthcheck() {
		$this->as3cf->schedule_event( $this->identifier . '_cron', $this->identifier . '_cron_interval' );
	}

	/**
	 * Clear cron health check.
	 */
	protected function clear_cron_healthcheck() {
		$this->as3cf->clear_scheduled_event( $this->identifier . '_cron' );
	}

	/**
	 * Handle cron health check
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running
			exit;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process
			$this->as3cf->clear_scheduled_event( $this->identifier . '_cron' );
			exit;
		}

		$this->dispatch();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	abstract protected function task( $item );

}
