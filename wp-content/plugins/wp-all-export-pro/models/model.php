<?php 
/**
 * Base class for models
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
abstract class PMXE_Model extends ArrayObject {
	/**
	 * WPDB instance 
	 * @var wpdb
	 */
	protected $wpdb;
	/**
	 * Table name the model is linked to
	 * @var string
	 */
	protected $table;
	/**
	 * Array of columns representing primary key
	 * @var array
	 */
	protected $primary = array('id');
	/**
	 * Wether key field is auto_increment (sure make scence only if key s
	 * @var bool
	 */
	protected $auto_increment = FALSE;
	
	/**
	 * Cached data retrieved from database
	 * @var array
	 */
	private static $meta_cache = array();
	
	/**
	 * Initialize model
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct() {
		$this->wpdb = $GLOBALS['wpdb'];
	}
	
	/**
	 * Read records from database by specified fields and values
	 * When 1st parameter is an array, it expected to be an associative array of field => value pairs to read data by 
	 * If 2 parameters are set, first one is expected to be a field name and second - it's value
	 * 
	 * @param string|array $field
	 * @param mixed[optional] $value 
	 * @return PMXE_Model
	 */
	abstract public function getBy($field = NULL, $value = NULL);
	
	/**
	 * Magic function to automatically resolve calls like $obj->getBy%FIELD_NAME%
	 * @param string $method
	 * @param array $args
	 * @return PMXE_Model
	 */
	public function __call($method, $args) {
		if (preg_match('%^get_?by_?(.+)%i', $method, $mtch)) {
			array_unshift($args, $mtch[1]);
			return call_user_func_array(array($this, 'getBy'), $args);
		} else {
			throw new Exception("Requested method " . get_class($this) . "::$method doesn't exist.");
		}
	}
	
	/**
	 * Bind model to database table
	 * @param string $tableName
	 * @return PMXE_Model
	 */
	public function setTable($tableName) {
		if ( ! is_null($this->table)) {
			throw new Exception('Table name cannot be changed once being set.');
		}
		$this->table = $tableName;
		if ( ! isset(self::$meta_cache[$this->table])) {
			$tableMeta = $this->wpdb->get_results("SHOW COLUMNS FROM $this->table", ARRAY_A);
			$primary = array();
			$auto_increment = false;
			foreach ($tableMeta as $colMeta) {
				if ('PRI' == $colMeta['Key']) {
					$primary[] = $colMeta['Field'];
				}
				if ('auto_increment' == $colMeta['Extra']) {
					$auto_increment = true;
					break; // no point to iterate futher since auto_increment means corresponding primary key is simple
				}
			}
			self::$meta_cache[$this->table] = array('primary' => $primary, 'auto_increment' => $auto_increment);
		}
		$this->primary = self::$meta_cache[$this->table]['primary'];
		$this->auto_increment = self::$meta_cache[$this->table]['auto_increment'];
		
		return $this;
	} 
	
	/**
	 * Return database table name this object is bound to
	 * @return string
	 */
	public function getTable() {
		return $this->table;
	}
	/**
	 * Return column name with table name
	 * @param string $col
	 * @return string
	 */
	public function getFieldName($col) {
		return $this->table . '.' . $col;
	}
	
	/**
	 * Compose WHERE clause based on parameters provided
	 * @param string|array $field
	 * @param mixed[optional] $value
	 * @param string[optional] $operator AND or OR string, 'AND' by default
	 * @return string
	 */
	protected function buildWhere($field, $value = NULL, $operator = NULL) {
		if ( ! is_array($field)) {
			$field = array($field => $value);
		} else { // shift arguments
			$operator = $value;
		}
		! is_null($operator) or $operator = 'AND'; // apply default operator value
		
		$where = array();
		foreach ($field as $key => $val) {
			if (is_int($key)) {
				$where[] = '(' . call_user_func_array(array($this, 'buildWhere'), $val) . ')';
			} else {
				if ( ! preg_match('%^(.+?) *(=|<>|!=|<|>|<=|>=| (NOT +)?(IN|(LIKE|REGEXP|RLIKE)( BINARY)?))?$%i', trim($key), $mtch)) {
					throw new Exception('Wrong field name format.');
				}
				$key = $mtch[1];
				if (is_array($val) and (empty($mtch[2]) or 'IN' == strtoupper($mtch[4]))) {
					$op = empty($mtch[2]) ? 'IN' : strtoupper(trim($mtch[2]));
					if (count($val)) $where[] = $this->wpdb->prepare("$key $op (" . implode(', ', array_fill(0, count($val), "%s")) . ")", $val);
				} else {
					$op = empty($mtch[2]) ? '=' : strtoupper(trim($mtch[2]));
					$where[] = $this->wpdb->prepare("$key $op %s", $val);
				}
			}
		}
		return implode(" $operator ", $where);
	}
	
	
	/**
	 * Return associative array with record data
	 * @param bool[optional] $serialize Whether returned fields should be serialized
	 * @return array
	 */
	public function toArray($serialize = FALSE) {
		$result = (array)$this;
		if ($serialize) {
			foreach ($result as $k => $v) {
				if ( ! is_scalar($v)) {
					$result[$k] = serialize($v);
				}
			}
		}
		return $result;
	}
	
	/**
	 * Check whether object data is empty
	 * @return bool
	 */
	public function isEmpty() {
		return $this->count() == 0;
	}
	
	/**
	 * Empty object data
	 * @return PMXE_Model
	 */
	public function clear() {
		$this->exchangeArray(array());
		return $this;
	}
	
	/**
	 * Delete all content from model's table
	 * @return PMXE_Model
	 */
	public function truncateTable() {
		if (FALSE !== $this->wpdb->query("TRUNCATE $this->table")) {
			return $this;
		} else {
			throw new Exception($this->wpdb->last_error);
		}
	}
}