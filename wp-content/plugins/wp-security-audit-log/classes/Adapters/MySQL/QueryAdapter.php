<?php
/**
 * Adapter: Query.
 *
 * MySQL database Query class.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MySQL database Query class.
 *
 * The SQL query is created in this class, here the SQL is filled with
 * the arguments.
 *
 * @package Wsal
 */
class WSAL_Adapters_MySQL_Query implements WSAL_Adapters_QueryInterface {

	/**
	 * DB Connection
	 *
	 * @var array
	 */
	protected $connection;

	/**
	 * Method: Constructor.
	 *
	 * @param array $conn - Connection array.
	 */
	public function __construct( $conn ) {
		$this->connection = $conn;
	}

	/**
	 * Get the SQL filled with the args.
	 *
	 * @param object $query - Query object.
	 * @param array  $args - Args of the query.
	 * @return string Generated sql.
	 */
	protected function GetSql( $query, &$args = array() ) {
		$conditions = $query->getConditions();
		$search_condition = $this->SearchCondition( $query );
		$s_where_clause = '';
		foreach ( $conditions as $field_name => $field_value ) {
			if ( empty( $s_where_clause ) ) {
				$s_where_clause .= ' WHERE ';
			} else {
				$s_where_clause .= ' AND ';
			}

			if ( is_array( $field_value ) ) {
				$sub_where_clause = '(';
				foreach ( $field_value as $or_field_name => $or_field_value ) {
					if ( is_array( $or_field_value ) ) {
						foreach ( $or_field_value as $value ) {
							if ( '(' != $sub_where_clause ) {
								$sub_where_clause .= ' OR ';
							}
							$sub_where_clause .= $or_field_name;
							$args[] = $value;
						}
					} else {
						if ( '(' != $sub_where_clause ) {
							$sub_where_clause .= ' OR ';
						}
						$sub_where_clause .= $or_field_name;
						$args[] = $or_field_value;
					}
				}
				$sub_where_clause .= ')';
				$s_where_clause .= $sub_where_clause;
			} else {
				$s_where_clause .= $field_name;
				$args[] = $field_value;
			}
		}

		$from_data_sets = $query->getFrom();
		$columns = $query->getColumns();
		$order_bys = $query->getOrderBy();

		$s_limit_clause = '';
		if ( $query->getLimit() ) {
			$s_limit_clause .= ' LIMIT ';
			if ( $query->getOffset() ) {
				$s_limit_clause .= $query->getOffset() . ', ';
			}
			$s_limit_clause .= $query->getLimit();
		}
		$join_clause = '';
		if ( $query->hasMetaJoin() ) {
			$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
			$occurrence = new WSAL_Adapters_MySQL_Occurrence( $this->connection );
			$join_clause = ' LEFT JOIN ' . $meta->GetTable() . ' AS meta ON meta.occurrence_id = ' . $occurrence->GetTable() . '.id ';
		}
		$fields = (empty( $columns )) ? $from_data_sets[0] . '.*' : implode( ',', $columns );
		if ( ! empty( $search_condition ) ) {
			$args[] = $search_condition['args'];
		}

		$sql = 'SELECT ' . $fields
			. ' FROM ' . implode( ',', $from_data_sets )
			. $join_clause
			. $s_where_clause
			. ( ! empty( $search_condition ) ? (empty( $s_where_clause ) ? ' WHERE ' . $search_condition['sql'] : ' AND ' . $search_condition['sql']) : '')
			// @todo GROUP BY goes here
			. ( ! empty( $order_bys ) ? (' ORDER BY ' . implode( ', ', array_keys( $order_bys ) ) . ' ' . implode( ', ', array_values( $order_bys ) )) : '')
			. $s_limit_clause;
		return $sql;
	}

	/**
	 * Get an instance of the ActiveRecord Adapter.
	 *
	 * @return WSAL_Adapters_MySQL_ActiveRecord
	 */
	protected function getActiveRecordAdapter() {
		return new WSAL_Adapters_MySQL_ActiveRecord( $this->connection );
	}

	/**
	 * Execute query and return data as $ar_cls objects.
	 *
	 * @param object $query - Query object.
	 * @return WSAL_Models_ActiveRecord[]
	 */
	public function Execute( $query ) {
		$args = array();
		$sql = $this->GetSql( $query, $args );

		$occurence_adapter = $query->getConnector()->getAdapter( 'Occurrence' );

		if ( in_array( $occurence_adapter->GetTable(), $query->getFrom() ) ) {
			return $occurence_adapter->LoadMulti( $sql, $args );
		} else {
			return $this->getActiveRecordAdapter()->LoadMulti( $sql, $args );
		}
	}

	/**
	 * Count query
	 *
	 * @param object $query - Query object.
	 * @return integer counting records.
	 */
	public function Count( $query ) {
		// Back up columns, use COUNT as default column and generate sql.
		$cols = $query->getColumns();
		$query->clearColumns();
		$query->addColumn( 'COUNT(*)' );

		$args = array();
		$sql = $this->GetSql( $query, $args );

		// Restore columns.
		$query->setColumns( $cols );
		// Execute query and return result.
		return $this->getActiveRecordAdapter()->CountQuery( $sql, $args );
	}

	/**
	 * Count DELETE query
	 *
	 * @param object $query - Query object.
	 * @return integer counting records.
	 */
	public function CountDelete( $query ) {
		$result = $this->GetSqlDelete( $query, true );
		// Execute query and return result.
		return $this->getActiveRecordAdapter()->CountQuery( $result['sql'], $result['args'] );
	}

	/**
	 * Query for deleting records
	 *
	 * @param object $query query object.
	 */
	public function Delete( $query ) {
		$result = $this->GetSqlDelete( $query );
		$this->DeleteMetas( $query, $result['args'] );
		return $this->getActiveRecordAdapter()->DeleteQuery( $result['sql'], $result['args'] );
	}

	/**
	 * Load occurrence IDs then delete Metadata by occurrence_id
	 *
	 * @param object $query - Query object.
	 * @param array  $args - Args of the query.
	 */
	public function DeleteMetas( $query, $args ) {
		// Back up columns, use COUNT as default column and generate sql.
		$cols = $query->getColumns();
		$query->clearColumns();
		$query->addColumn( 'id' );
		$sql = $this->GetSql( $query );
		// Restore columns.
		$query->setColumns( $cols );

		$_wpdb = $this->connection;
		$occ_ids = array();
		$sql = ( ! empty( $args ) ? $_wpdb->prepare( $sql, $args ) : $sql);
		foreach ( $_wpdb->get_results( $sql, ARRAY_A ) as $data ) {
			$occ_ids[] = $data['id'];
		}
		$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
		$meta->DeleteByOccurenceIds( $occ_ids );
	}

	/**
	 * Get the DELETE query SQL filled with the args.
	 *
	 * @param object $query - Query object.
	 * @param bool   $get_count - Get count.
	 * @return string - Generated sql.
	 */
	public function GetSqlDelete( $query, $get_count = false ) {
		$result = array();
		$args = array();
		// Back up columns, remove them for DELETE and generate sql.
		$cols = $query->getColumns();
		$query->clearColumns();

		$conditions = $query->getConditions();

		$s_where_clause = '';
		foreach ( $conditions as $field_name => $field_value ) {
			if ( empty( $s_where_clause ) ) {
				$s_where_clause .= ' WHERE ';
			} else {
				$s_where_clause .= ' AND ';
			}
			$s_where_clause .= $field_name;
			$args[] = $field_value;
		}

		$from_data_sets = $query->getFrom();
		$order_bys = $query->getOrderBy();

		$s_limit_clause = '';
		if ( $query->getLimit() ) {
			$s_limit_clause .= ' LIMIT ';
			if ( $query->getOffset() ) {
				$s_limit_clause .= $query->getOffset() . ', ';
			}
			$s_limit_clause .= $query->getLimit();
		}
		$result['sql'] = ($get_count ? 'SELECT COUNT(*) FROM ' : 'DELETE FROM ')
			. implode( ',', $from_data_sets )
			. $s_where_clause
			. ( ! empty( $order_bys ) ? (' ORDER BY ' . implode( ', ', array_keys( $order_bys ) ) . ' ' . implode( ', ', array_values( $order_bys ) )) : '')
			. $s_limit_clause;
		$result['args'] = $args;
		// Restore columns.
		$query->setColumns( $cols );

		return $result;
	}

	/**
	 * Search by alert code OR by Metadata value.
	 *
	 * @param object $query - Query object.
	 */
	public function SearchCondition( $query ) {
		$condition = $query->getSearchCondition();
		if ( empty( $condition ) ) {
			return null;
		}
		$search_conditions = array();
		$meta = new WSAL_Adapters_MySQL_Meta( $this->connection );
		$occurrence = new WSAL_Adapters_MySQL_Occurrence( $this->connection );
		if ( is_numeric( $condition ) && strlen( $condition ) == 4 ) {
			$search_conditions['sql'] = $occurrence->GetTable() . '.alert_id LIKE %s';
		} else {
			$search_conditions['sql'] = $occurrence->GetTable() . '.id IN (
				SELECT DISTINCT occurrence_id
					FROM ' . $meta->GetTable() . '
					WHERE TRIM(BOTH "\"" FROM value) LIKE %s
				)';
		}
		$search_conditions['args'] = '%' . $condition . '%';
		return $search_conditions;
	}
}
