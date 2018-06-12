<?php
/**
 * Query Interface.
 *
 * Interface used by the Query.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface used by the Query.
 *
 * @package Wsal
 */
interface WSAL_Adapters_QueryInterface {

	/**
	 * Execute query and return data as $ar_cls objects.
	 *
	 * @param object $query - Query object.
	 */
	public function Execute( $query );

	/**
	 * Count query.
	 *
	 * @param object $query - Query object.
	 */
	public function Count( $query );

	/**
	 * Query for deleting records.
	 *
	 * @param object $query - Query object.
	 */
	public function Delete( $query );
}
