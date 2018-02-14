<?php
/**
 * Incapsulates behavior for list of database records
 *
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXI_Model_List extends PMXI_Model {
	
	/**
	 * Total number of records in database which correspond last getBy rule without paging
	 * @var int
	 */
	protected $total = 0;

	/**
	 * Joined tables
	 * @var array
	 */
	protected $joined = array();
	/**
	 * Columns to select from database
	 * @var string
	 */
	protected $what = '*';
	/**
	 * Sets table to use in conjuction with primary list table
	 * @param string $table Table to join
	 * @param string $on Condition to join
	 * @param string $type Join type (INNER, OUTER, etc)
	 * @return PMXI_Model_List
	 */
	public function join($table, $on, $type = 'INNER') {
		$this->joined[] = ( ! is_null($type) ? $type . ' ' : '') . 'JOIN ' . $table . ' ON ' . $on;
		return $this;
	}
	
	/**
	 * Set columns to be selected from database
	 * @param array $columns
	 * @return PMXI_Model_List
	 */
	public function setColumns($columns) {
		is_array($columns) or $columns = func_get_args();
		$this->what = implode(', ', $columns);
		return $this;
	}
	
	/**
	 * Read records from database by specified fields and values
	 * When 1st parameter is an array, it's expected to be an associative array of field => value pairs to read data by 
	 * When 2nd parameter is a scalar, it's expected to be a field name and second parameter - it's value
	 * 
	 * @param string|array[optional] $field
	 * @param mixed[optional] $value
	 * @param string[optional] $orderBy Ordering rule
	 * @param int[optional] $page Paging paramter used to limit number of records returned
	 * @param int[optional] $perPage Page size when paging parameter is used (20 by default) 
	 * @return PMXI_Model_List
	 */
	public function getBy($field = NULL, $value = NULL, $orderBy = NULL, $page = NULL, $perPage = NULL, $groupBy = NULL) {
		if (is_array($field) or is_null($field)) { // when associative array is submitted, do not expect second paramter to be $value, but act as if there is no $value parameter at all
			$groupBy = $perPage; $perPage = $page; $page = $orderBy; $orderBy = $value; $value = NULL;
		}
		! is_null($perPage) or $perPage = 20; // set default value for page length
		$page = intval($page);
		
		$sql = "FROM $this->table ";
		$sql .= implode(' ', $this->joined);
		if ( ! is_null($field)) {
			$sql .= " WHERE " . $this->buildWhere($field, $value);
		}
		if ( ! is_null($groupBy)) {
			$sql .= " GROUP BY $groupBy";
		}
		is_null($orderBy) and $orderBy = implode(', ', $this->primary); // default sort order is by primary key
		$sql .= " ORDER BY $orderBy";
		if ($page > 0) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS $this->what $sql LIMIT " . intval(($page - 1) * $perPage) . ", " . intval($perPage);
		} else {
			$sql = "SELECT $this->what $sql";
		}		
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		if (is_array($result)) {
			foreach ($result as $i => $row) {
				foreach ($row as $k => $v) {
					if (is_serialized($v)) {
						$result[$i][$k] = unserialize($v);
					}
				}
			}
			if ($page > 0) {
				$this->total = intval($this->wpdb->get_var('SELECT FOUND_ROWS()'));
			} else {
				$this->total = count($result);
			}
			$this->exchangeArray($result);
		} else {
			$this->total = 0;
			$this->clear();
		}
		return $this;
	}
	
	/**
	 * Count records in table
	 * @param string|array $field
	 * @param mixed[optional] $value
	 * @return int
	 */
	public function countBy($field = NULL, $value = NULL) {
		$sql = "SELECT COUNT(*) FROM $this->table ";
		$sql .= implode(' ', $this->joined);
		if ( ! is_null($field)) {
			$sql .= " WHERE " . $this->buildWhere($field, $value);
		}
		return intval($this->wpdb->get_var($sql));
	}
	
	/**
	 * Method returns number of rows in database which correspond last getBy query
	 * @return int
	 */
	public function total() {
		return $this->total;
	} 
	
	/**
	 * Converts elements to instances of specifield class. If includeFields are provided only fields listed are included
	 * @param string[optoinal] $elementClass
	 * @param array[optional] $includeFields
	 * @return PMXI_Model_List
	 */
	public function convertRecords($elementClass = NULL, $includeFields = NULL) {
		! is_null($elementClass) or $elementClass = preg_replace('%List$%', 'Record', get_class($this));
		if ( ! is_subclass_of($elementClass, PMXI_Plugin::PREFIX . 'Model_Record')) {
			throw new Exception("Provideded class name $elementClass must be a subclass of " . PMXI_Plugin::PREFIX . 'Model_Record');
		}
		$records = $this->exchangeArray(array());
		foreach ($records as $r) {
			$data = (array)$r;
			if ( ! is_null($includeFields)) {
				$data = array_intersect_key($data, array_flip($includeFields));
			}
			$this[] = new $elementClass($data);
		}
		return $this;
	}
	
}