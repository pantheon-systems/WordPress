<?php
/**
 * Adapter: Meta data.
 *
 * MySQL database Metadata class.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MySQL database Metadata class.
 *
 * MySQL wsal_metadata table used for to store the alert meta data:
 * username, user_roles, client_ip, user_agent, post_id, post_title, etc.
 *
 * @package Wsal
 */
class WSAL_Adapters_MySQL_Meta extends WSAL_Adapters_MySQL_ActiveRecord implements WSAL_Adapters_MetaInterface {

	/**
	 * Contains the table name.
	 *
	 * @var string
	 */
	protected $_table = 'wsal_metadata';

	/**
	 * Contains primary key column name, override as required.
	 *
	 * @var string
	 */
	protected $_idkey = 'id';

	/**
	 * Meta id.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Occurrence id.
	 *
	 * @var int
	 */
	public $occurrence_id = 0;

	/**
	 * Meta name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Meta name max length.
	 *
	 * @var int
	 */
	public static $name_maxlength = 100;

	/**
	 * Meta value.
	 *
	 * @var mix
	 */
	public $value = array(); // Force mixed type.

	/**
	 * Returns the model class for adapter.
	 *
	 * @return WSAL_Models_Meta
	 */
	public function GetModel() {
		return new WSAL_Models_Meta();
	}

	/**
	 * Method: Constructor.
	 *
	 * @param array $conn - Connection array.
	 */
	public function __construct( $conn ) {
		parent::__construct( $conn );
	}

	/**
	 * SQL table options (constraints, foreign keys, indexes etc).
	 *
	 * @return string
	 */
	protected function GetTableOptions() {
		return parent::GetTableOptions() . ',' . PHP_EOL
				. '    KEY occurrence_name (occurrence_id,name)';
	}

	/**
	 * Delete metadata by occurrence_id.
	 *
	 * @param array $occurence_ids - List of occurrence IDs.
	 */
	public function DeleteByOccurenceIds( $occurence_ids ) {
		if ( ! empty( $occurence_ids ) ) {
			$sql = 'DELETE FROM ' . $this->GetTable() . ' WHERE occurrence_id IN (' . implode( ',', $occurence_ids ) . ')';
			// Execute query.
			parent::DeleteQuery( $sql );
		}
	}

	/**
	 * Load metadata by name and occurrence_id.
	 *
	 * @param string $meta_name - Metadata name.
	 * @param string $occurence_id - Metadata occurrence_id.
	 * @return WSAL_Meta[]
	 */
	public function LoadByNameAndOccurenceId( $meta_name, $occurence_id ) {
		return $this->Load( 'occurrence_id = %d AND name = %s', array( $occurence_id, $meta_name ) );
	}

	/**
	 * Get distinct values of IPs.
	 *
	 * @param int $limit - (Optional) Limit.
	 * @return array - Distinct values of IPs.
	 */
	public function GetMatchingIPs( $limit = null ) {
		$_wpdb = $this->connection;
		$sql = "SELECT DISTINCT value FROM {$this->GetTable()} WHERE name = \"ClientIP\"";
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}
		$ips = $_wpdb->get_col( $sql );
		foreach ( $ips as $key => $ip ) {
			$ips[ $key ] = str_replace( '"', '', $ip );
		}
		return array_unique( $ips );
	}
}
