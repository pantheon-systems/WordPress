<?php

class WPML_TM_Word_Count_Background_Process_Requested_Types extends WPML_TM_Word_Count_Background_Process {

	/** @var WPML_TM_Word_Count_Queue_Items_Requested_Types $queue */
	protected $queue;

	/** @var WPML_TM_Word_Count_Records $records */
	private $records;

	/**
	 * @param WPML_TM_Word_Count_Queue_Items_Requested_Types $queue_items
	 * @param IWPML_TM_Word_Count_Set[]       $setters
	 */
	public function __construct(
		WPML_TM_Word_Count_Queue_Items_Requested_Types $queue_items,
		array $setters,
		WPML_TM_Word_Count_Records $records
	) {
		/** We need to set the action before constructing the parent class `WP_Async_Request` */
		$this->action = WPML_TM_Word_Count_Background_Process_Factory::ACTION_REQUESTED_TYPES;
		parent::__construct( $queue_items, $setters );
		$this->records = $records;

		add_filter( 'wpml_tm_word_count_background_process_requested_types_memory_exceeded', array(
			$this,
			'memory_exceeded_filter',
		) );
	}

	public function init( $requested_types ) {
		$this->queue->reset( $requested_types );
		$this->records->reset_all( $requested_types );
		$this->dispatch();
	}

	public function dispatch() {
		update_option(
			WPML_TM_Word_Count_Hooks_Factory::OPTION_KEY_REQUESTED_TYPES_STATUS,
			WPML_TM_Word_Count_Hooks_Factory::PROCESS_IN_PROGRESS
		);

		parent::dispatch();
	}

	public function complete() {
		update_option(
			WPML_TM_Word_Count_Hooks_Factory::OPTION_KEY_REQUESTED_TYPES_STATUS,
			WPML_TM_Word_Count_Hooks_Factory::PROCESS_COMPLETED
		);

		parent::complete();
	}

	/**
	 * Filter result of memory_exceeded() function in WP_Background_Process class.
	 * Used by it get_memory_limit() function of WP_Background_Process class contains a number of bugs,
	 * producing wrong result when 'memory_limit' setting in php.ini is in human readable format like '1G'.
	 *
	 * @return bool
	 */
	public function memory_exceeded_filter() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );

		return $current_memory >= $memory_limit;
	}

	/**
	 * Get memory limit in bytes.
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return $this->convert_shorthand_to_bytes( $memory_limit );
	}

	/**
	 * Converts a shorthand byte value to an integer byte value.
	 *
	 * @param string $value A (PHP ini) byte value, either shorthand or ordinary.
	 * @return int An integer byte value.
	 */
	protected function convert_shorthand_to_bytes( $value ) {
		$value = strtolower( trim( $value ) );
		$bytes = (int) $value;

		if ( false !== strpos( $value, 'g' ) ) {
			$bytes *= 1024 * 1024 * 1024;
		} elseif ( false !== strpos( $value, 'm' ) ) {
			$bytes *= 1024 * 1024;
		} elseif ( false !== strpos( $value, 'k' ) ) {
			$bytes *= 1024;
		}

		// Deal with large (float) values which run into the maximum integer size.
		return min( $bytes, PHP_INT_MAX );
	}
}
