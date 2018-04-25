<?php
/**
 * Adapter: Occurrence.
 *
 * MySQL database Occurrence class.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MySQL database Occurrence class.
 *
 * MySQL wsal_occurrences table used for to store the alerts.
 *
 * @package Wsal
 */
class WSAL_Adapters_MySQL_Occurrence extends WSAL_Adapters_MySQL_ActiveRecord implements WSAL_Adapters_OccurrenceInterface {

	/**
	 * Contains the table name.
	 *
	 * @var string
	 */
	protected $_table = 'wsal_occurrences';

	/**
	 * Contains primary key column name, override as required.
	 *
	 * @var string
	 */
	protected $_idkey = 'id';

	/**
	 * Meta data.
	 *
	 * @var WSAL_Meta
	 */
	protected $_meta;

	/**
	 * Occurrence id.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Site id.
	 *
	 * @var int
	 */
	public $site_id = 0;

	/**
	 * Alert id.
	 *
	 * @var int
	 */
	public $alert_id = 0;

	/**
	 * Created on.
	 *
	 * @var string
	 */
	public $created_on = 0.0;

	/**
	 * Is read?
	 *
	 * @var bool
	 */
	public $is_read = false;

	/**
	 * Is migrated?
	 *
	 * @var bool
	 */
	public $is_migrated = false;

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
				. '    KEY site_alert_created (site_id,alert_id,created_on)';
	}

	/**
	 * Returns the model class for adapter.
	 *
	 * @return WSAL_Models_Occurrence
	 */
	public function GetModel() {
		return new WSAL_Models_Occurrence();
	}

	/**
	 * Returns metadata related to this event.
	 *
	 * @param object $occurence - Occurrence model instance.
	 * @see WSAL_Adapters_MySQL_ActiveRecord::Load()
	 * @return WSAL_Meta
	 */
	public function GetMeta( $occurence ) {
		if ( ! isset( $this->_meta ) ) {
			$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
			$this->_meta = $meta->Load( 'occurrence_id = %d', array( $occurence->id ) );
		}
		return $this->_meta;
	}

	/**
	 * Returns allmeta data related to this event.
	 *
	 * @param object $occurence - Occurrence model instance.
	 * @see WSAL_Adapters_MySQL_ActiveRecord::LoadArray()
	 * @return WSAL_Meta[]
	 */
	public function GetMultiMeta( $occurence ) {
		if ( ! isset( $this->_meta ) ) {
			$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
			$this->_meta = $meta->LoadArray( 'occurrence_id = %d', array( $occurence->id ) );
		}
		return $this->_meta;
	}

	/**
	 * Loads a meta item given its name.
	 *
	 * @param object $occurence - Occurrence model instance.
	 * @param string $name - Meta name.
	 * @see WSAL_Adapters_MySQL_ActiveRecord::Load()
	 * @return WSAL_Meta The meta item, be sure to checked if it was loaded successfully.
	 */
	public function GetNamedMeta( $occurence, $name ) {
		$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
		$this->_meta = $meta->Load( 'occurrence_id = %d AND name = %s', array( $occurence->id, $name ) );
		return $this->_meta;
	}

	/**
	 * Returns the first meta value from a given set of names. Useful when you have a mix of items that could provide a particular detail.
	 *
	 * @param object $occurence - Occurrence model instance.
	 * @param array  $names - List of meta names.
	 * @return WSAL_Meta The first meta item that exists.
	 */
	public function GetFirstNamedMeta( $occurence, $names ) {
		$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
		$query = '(' . str_repeat( 'name = %s OR ', count( $names ) ) . '0)';
		$query = 'occurrence_id = %d AND ' . $query . ' ORDER BY name DESC LIMIT 1';
		array_unshift( $names, $occurence->id ); // Prepend args with occurrence id.

				$this->_meta = $meta->Load( $query, $names );
		return $meta->getModel()->LoadData( $this->_meta );

		// TO DO: Do we want to reintroduce is loaded check/logic?
		// return $meta->IsLoaded() ? $meta : null;
	}

	/**
	 * Returns newest unique occurrences.
	 *
	 * @param integer $limit Maximum limit.
	 * @return WSAL_Occurrence[]
	 */
	public static function GetNewestUnique( $limit = PHP_INT_MAX ) {
		$temp = new self();
		return self::LoadMultiQuery('
			SELECT *, COUNT(alert_id) as count
			FROM (
				SELECT *
				FROM ' . $temp->GetTable() . '
				ORDER BY created_on DESC
			) AS temp_table
			GROUP BY alert_id
			LIMIT %d
			', array( $limit )
		);
	}

	/**
	 * Gets occurences of the same type by IP and Username within specified time frame.
	 *
	 * @param array $args - User arguments.
	 * @return WSAL_Occurrence[]
	 */
	public function CheckKnownUsers( $args = array() ) {
		$tt2 = new WSAL_Adapters_MySQL_Meta( $this->connection );
		return self::LoadMultiQuery(
			'SELECT occurrence.* FROM `' . $this->GetTable() . '` occurrence
			INNER JOIN `' . $tt2->GetTable() . '` ipMeta on ipMeta.occurrence_id = occurrence.id
			and ipMeta.name = "ClientIP"
			and ipMeta.value = %s
			INNER JOIN `' . $tt2->GetTable() . '` usernameMeta on usernameMeta.occurrence_id = occurrence.id
			and usernameMeta.name = "Username"
			and usernameMeta.value = %s
			WHERE occurrence.alert_id = %d AND occurrence.site_id = %d
			AND (created_on BETWEEN %d AND %d)
			GROUP BY occurrence.id',
			$args
		);
	}

	/**
	 * Gets occurences of the same type by IP within specified time frame.
	 *
	 * @param array $args - User arguments.
	 * @return WSAL_Occurrence[]
	 */
	public function CheckUnKnownUsers( $args = array() ) {
		$tt2 = new WSAL_Adapters_MySQL_Meta( $this->connection );
		return self::LoadMultiQuery(
			'SELECT occurrence.* FROM `' . $this->GetTable() . '` occurrence
			INNER JOIN `' . $tt2->GetTable() . '` ipMeta on ipMeta.occurrence_id = occurrence.id
			and ipMeta.name = "ClientIP" and ipMeta.value = %s
			WHERE occurrence.alert_id = %d AND occurrence.site_id = %d
			AND (created_on BETWEEN %d AND %d)
			GROUP BY occurrence.id',
			$args
		);
	}

	/**
	 * Gets occurences of the alert 1003.
	 *
	 * @param array $args - User arguments.
	 * @return WSAL_Occurrence[]
	 */
	public function check_alert_1003( $args = array() ) {
		return self::LoadMultiQuery(
			'SELECT occurrence.* FROM `' . $this->GetTable() . '` occurrence
			WHERE (occurrence.alert_id = %d)
			AND (occurrence.site_id = %d)
			AND (occurrence.created_on BETWEEN %d AND %d)
			GROUP BY occurrence.id',
			$args
		);
	}

	/**
	 * Add conditions to the Query
	 *
	 * @param string $query - Query.
	 */
	protected function prepareOccurrenceQuery( $query ) {
		$search_query_parameters = array();
		$search_conditions = array();
		$conditions = $query->getConditions();

		// BUG: not all conditions are occurence related. maybe it's just a field site_id. need seperate arrays.
		if ( ! empty( $conditions ) ) {
			$tmp = new WSAL_Adapters_MySQL_Meta( $this->connection );
			$s_where_clause = '';
			foreach ( $conditions as $field => $value ) {
				if ( ! empty( $s_where_clause ) ) {
					$s_where_clause .= ' AND ';
				}
				$s_where_clause .= 'name = %s AND value = %s';
				$search_query_parameters[] = $field;
				$search_query_parameters[] = $value;
			}

			$search_conditions[] = 'id IN (
				SELECT DISTINCT occurrence_id
				FROM ' . $tmp->GetTable() . '
				WHERE ' . $s_where_clause . '
			)';
		}

		// Do something with search query parameters and search conditions - give them to the query adapter?
		return $search_conditions;
	}

	/**
	 * Gets occurrence by Post_id.
	 *
	 * @param int $post_id - Post ID.
	 * @return WSAL_Occurrence[]
	 */
	public function GetByPostID( $post_id ) {
		$tt2 = new WSAL_Adapters_MySQL_Meta( $this->connection );
		return self::LoadMultiQuery(
			'SELECT occurrence.* FROM `' . $this->GetTable() . '`AS occurrence
			INNER JOIN `' . $tt2->GetTable() . '`AS postMeta ON postMeta.occurrence_id = occurrence.id
			and postMeta.name = "PostID"
			and postMeta.value = %d
			GROUP BY occurrence.id
			ORDER BY created_on DESC',
			array( $post_id )
		);
	}

	/**
	 * Gets occurences of the same type by IP within specified time frame.
	 *
	 * @param array $args - Query Arguments.
	 * @return WSAL_Occurrence[]
	 */
	public function CheckAlert404( $args = array() ) {
		$tt2 = new WSAL_Adapters_MySQL_Meta( $this->connection );
		return self::LoadMultiQuery(
			'SELECT occurrence.* FROM `' . $this->GetTable() . '` occurrence
			INNER JOIN `' . $tt2->GetTable() . '` ipMeta on ipMeta.occurrence_id = occurrence.id
			and ipMeta.name = "ClientIP" and ipMeta.value = %s
			INNER JOIN `' . $tt2->GetTable() . '` usernameMeta on usernameMeta.occurrence_id = occurrence.id
			and usernameMeta.name = "Username" and usernameMeta.value = %s
			WHERE occurrence.alert_id = %d AND occurrence.site_id = %d
			AND (created_on BETWEEN %d AND %d)
			GROUP BY occurrence.id',
			$args
		);
	}
}
