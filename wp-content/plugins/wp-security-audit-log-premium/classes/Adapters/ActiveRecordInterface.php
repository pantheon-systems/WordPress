<?php
/**
 * Active Record Interface.
 *
 * Interface used by the ActiveRecord.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface used by the ActiveRecord.
 *
 * @package Wsal
 */
interface WSAL_Adapters_ActiveRecordInterface {

	/**
	 * Is installed?
	 */
	public function IsInstalled();

	/**
	 * Install.
	 */
	public function Install();

	/**
	 * Uninstall.
	 */
	public function Uninstall();

	/**
	 * Load.
	 *
	 * @param string $cond - Query Condition.
	 * @param array  $args - Query arguments.
	 */
	public function Load( $cond = '%d', $args = array( 1 ) );

	/**
	 * Save.
	 *
	 * @param object $activeRecord - Active Record object.
	 */
	public function Save( $activeRecord );

	/**
	 * Delete.
	 *
	 * @param object $activeRecord - Active Record object.
	 */
	public function Delete( $activeRecord );

	/**
	 * Load with Multiple Conditions.
	 *
	 * @param string $cond - Query Condition.
	 * @param array  $args - Query arguments.
	 */
	public function LoadMulti( $cond, $args = array() );

	/**
	 * Load and call foreach.
	 *
	 * @param string $callback - Callback.
	 * @param string $cond - Query Condition.
	 * @param array  $args - Query arguments.
	 */
	public function LoadAndCallForEach( $callback, $cond = '%d', $args = array( 1 ) );

	/**
	 * Count.
	 *
	 * @param string $cond - Query Condition.
	 * @param array  $args - Query arguments.
	 */
	public function Count( $cond = '%d', $args = array( 1 ) );

	/**
	 * Multiple Query.
	 *
	 * @param array $query - Query Condition.
	 * @param array $args - Query arguments.
	 */
	public function LoadMultiQuery( $query, $args = array() );
}
