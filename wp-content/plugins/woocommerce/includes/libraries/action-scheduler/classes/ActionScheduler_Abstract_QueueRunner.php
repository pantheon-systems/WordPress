<?php

/**
 * Abstract class with common Queue Cleaner functionality.
 */
abstract class ActionScheduler_Abstract_QueueRunner {

	/** @var ActionScheduler_QueueCleaner */
	protected $cleaner;

	/** @var ActionScheduler_FatalErrorMonitor */
	protected $monitor;

	/** @var ActionScheduler_Store */
	protected $store;

	/**
	 * The created time.
	 *
	 * Represents when the queue runner was constructed and used when calculating how long a PHP request has been running.
	 * For this reason it should be as close as possible to the PHP request start time.
	 *
	 * @var int
	 */
	private $created_time;

	/**
	 * ActionScheduler_Abstract_QueueRunner constructor.
	 *
	 * @param ActionScheduler_Store             $store
	 * @param ActionScheduler_FatalErrorMonitor $monitor
	 * @param ActionScheduler_QueueCleaner      $cleaner
	 */
	public function __construct( ActionScheduler_Store $store = null, ActionScheduler_FatalErrorMonitor $monitor = null, ActionScheduler_QueueCleaner $cleaner = null ) {

		$this->created_time = microtime( true );

		$this->store   = $store ? $store : ActionScheduler_Store::instance();
		$this->monitor = $monitor ? $monitor : new ActionScheduler_FatalErrorMonitor( $this->store );
		$this->cleaner = $cleaner ? $cleaner : new ActionScheduler_QueueCleaner( $this->store );
	}

	/**
	 * Process an individual action.
	 *
	 * @param int $action_id The action ID to process.
	 */
	public function process_action( $action_id ) {
		try {
			do_action( 'action_scheduler_before_execute', $action_id );

			if ( ActionScheduler_Store::STATUS_PENDING !== $this->store->get_status( $action_id ) ) {
				do_action( 'action_scheduler_execution_ignored', $action_id );
				return;
			}

			$action = $this->store->fetch_action( $action_id );
			$this->store->log_execution( $action_id );
			$action->execute();
			do_action( 'action_scheduler_after_execute', $action_id );
			$this->store->mark_complete( $action_id );
		} catch ( Exception $e ) {
			$this->store->mark_failure( $action_id );
			do_action( 'action_scheduler_failed_execution', $action_id, $e );
		}
		$this->schedule_next_instance( $action );
	}

	/**
	 * Schedule the next instance of the action if necessary.
	 *
	 * @param ActionScheduler_Action $action
	 */
	protected function schedule_next_instance( ActionScheduler_Action $action ) {
		$schedule = $action->get_schedule();
		$next     = $schedule->next( as_get_datetime_object() );

		if ( ! is_null( $next ) && $schedule->is_recurring() ) {
			$this->store->save_action( $action, $next );
		}
	}

	/**
	 * Run the queue cleaner.
	 *
	 * @author Jeremy Pry
	 */
	protected function run_cleanup() {
		$this->cleaner->clean();
	}

	/**
	 * Get the number of concurrent batches a runner allows.
	 *
	 * @return int
	 */
	public function get_allowed_concurrent_batches() {
		return apply_filters( 'action_scheduler_queue_runner_concurrent_batches', 5 );
	}

	/**
	 * Get the maximum number of seconds a batch can run for.
	 *
	 * @return int The number of seconds.
	 */
	protected function get_maximum_execution_time() {

		// There are known hosts with a strict 60 second execution time.
		if ( defined( 'WPENGINE_ACCOUNT' ) || defined( 'PANTHEON_ENVIRONMENT' ) ) {
			$maximum_execution_time = 60;
		} elseif ( false !== strpos( getenv( 'HOSTNAME' ), '.siteground.' ) ) {
			$maximum_execution_time = 120;
		} else {
			$maximum_execution_time = ini_get( 'max_execution_time' );
		}

		return absint( apply_filters( 'action_scheduler_maximum_execution_time', $maximum_execution_time ) );
	}

	/**
	 * Get the number of seconds a batch has run for.
	 *
	 * @return int The number of seconds.
	 */
	protected function get_execution_time() {
		$execution_time = microtime( true ) - $this->created_time;

		// Get the CPU time if the hosting environment uses it rather than wall-clock time to calculate a process's execution time.
		if ( function_exists( 'getrusage' ) && apply_filters( 'action_scheduler_use_cpu_execution_time', defined( 'PANTHEON_ENVIRONMENT' ) ) ) {
			$resource_usages = getrusage();

			if ( isset( $resource_usages['ru_stime.tv_usec'], $resource_usages['ru_stime.tv_usec'] ) ) {
				$execution_time = $resource_usages['ru_stime.tv_sec'] + ( $resource_usages['ru_stime.tv_usec'] / 1000000 );
			}
		}

		return $execution_time;
	}

	/**
	 * Check if the host's max execution time is (likely) to be exceeded if processing more actions.
	 *
	 * @param int $processed_actions The number of actions processed so far - used to determine the likelihood of exceeding the time limit if processing another action
	 * @return bool
	 */
	protected function time_likely_to_be_exceeded( $processed_actions ) {

		$execution_time        = $this->get_execution_time();
		$max_execution_time    = $this->get_maximum_execution_time();
		$time_per_action       = $execution_time / $processed_actions;
		$estimated_time        = $execution_time + ( $time_per_action * 2 );
		$likely_to_be_exceeded = $estimated_time > $this->get_maximum_execution_time();

		return apply_filters( 'action_scheduler_maximum_execution_time_likely_to_be_exceeded', $likely_to_be_exceeded, $this, $processed_actions, $execution_time, $max_execution_time );
	}

	/**
	 * Get memory limit
	 *
	 * Based on WP_Background_Process::get_memory_limit()
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			$memory_limit = '128M'; // Sensible default, and minimum required by WooCommerce
		}

		if ( ! $memory_limit || -1 === $memory_limit || '-1' === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32G';
		}

		return ActionScheduler_Compatibility::convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90% of the maximum WordPress memory.
	 *
	 * Based on WP_Background_Process::memory_exceeded()
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {

		$memory_limit    = $this->get_memory_limit() * 0.90;
		$current_memory  = memory_get_usage( true );
		$memory_exceeded = $current_memory >= $memory_limit;

		return apply_filters( 'action_scheduler_memory_exceeded', $memory_exceeded, $this );
	}

	/**
	 * See if the batch limits have been exceeded, which is when memory usage is almost at
	 * the maximum limit, or the time to process more actions will exceed the max time limit.
	 *
	 * Based on WC_Background_Process::batch_limits_exceeded()
	 *
	 * @param int $processed_actions The number of actions processed so far - used to determine the likelihood of exceeding the time limit if processing another action
	 * @return bool
	 */
	protected function batch_limits_exceeded( $processed_actions ) {
		return $this->memory_exceeded() || $this->time_likely_to_be_exceeded( $processed_actions );
	}

	/**
	 * Process actions in the queue.
	 *
	 * @author Jeremy Pry
	 * @return int The number of actions processed.
	 */
	abstract public function run();
}
