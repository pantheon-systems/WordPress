<?php 
/**
 * Base class for models
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXE_Model_Record extends PMXE_Model {
	/**
	 * Initialize model
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct();
		if (! is_array($data)) {
			throw new Exception("Array expected as paramenter for " . get_class($this) . "::" . __METHOD__);
		}
		$data and $this->set($data);
	}
	
	/**
	 * @see PMXE_Model::getBy()
	 * @return PMXE_Model_Record
	 */
	public function getBy($field = NULL, $value = NULL) {
		if (is_null($field)) {
			throw new Exception("Field parameter is expected at " . get_class($this) . "::" . __METHOD__);
		}
		$sql = "SELECT * FROM $this->table WHERE " . $this->buildWhere($field, $value);
		$result = $this->wpdb->get_row($sql, ARRAY_A);
		if (is_array($result)) {
			foreach ($result as $k => $v) {
				if (is_serialized($v)) {
					$result[$k] = unserialize($v);
				}
			}
			$this->exchangeArray($result);
		} else {
			$this->clear();
		}
		return $this;
	}
	
	/**
	 * Ger records related to current one
	 * @param string $model Class name of model of related records
	 * @param array[optoinal] $keyAssoc
	 * @return PMXE_Model_List
	 */
	public function getRelated($model, $keyAssoc = NULL) {
		$related = new $model();
		if ( ! empty($this->id)) {
			if (is_null($keyAssoc)) {
				$defaultPrefix = strtolower(preg_replace('%^' . strtoupper(PMXE_Plugin::PREFIX) . '|_Record$%', '', get_class($this)));
				$keyAssoc = array();
				foreach ($this->primary as $key) {
					$keyAssoc = array($defaultPrefix . '_' . $key => $key);
				}
			}
			foreach ($keyAssoc as $foreign => $local) {
				$keyAssoc[$foreign] = $this->$local;
			}
			$related->getBy($keyAssoc);
		}
		return $related instanceof PMXE_Model_List ? $related->convertRecords() : $related;
	}
	
	/**
	 * Saves currently set object data as database record
	 * @return PMXE_Model_Record
	 */
	public function insert() {
		if ($this->wpdb->insert($this->table, $this->toArray(TRUE))) {
			if (isset($this->auto_increment)) {
				$this[$this->primary[0]] = $this->wpdb->insert_id;
			}
			return $this;
		} else {
			throw new Exception($this->wpdb->last_error);
		}
	}
	/**
	 * Update record in database
	 * @return PMXE_Model_Record
	 */
	public function update() {		
		$record = $this->toArray(TRUE);	
		$this->wpdb->update($this->table, $record, array_intersect_key($record, array_flip($this->primary)));							
		if ($this->wpdb->last_error) {
			throw new Exception($this->wpdb->last_error);
		}		
		return $this;
	}
	
	/**
	 * Delete record form database
	 * @return PMXE_Model_Record
	 */
	public function delete() {
		if ($this->wpdb->query("DELETE FROM $this->table WHERE " . $this->buildWhere(array_intersect_key($this->toArray(TRUE), array_flip($this->primary))))) {
			return $this;
		} elseif ($this->wpdb->last_error) {
			throw new Exception($this->wpdb->last_error);
		}
	}
	/**
	 * Insert or Update the record
	 * WARNING: function doesn't check actual record presents in database, it simply tries to insert if no primary key specified and update otherwise
	 * @return PMXE_Model_Record
	 */
	public function save() {
		if (array_intersect_key($this->toArray(TRUE), array_flip($this->primary))) {
			$this->update();
		} else {
			$this->insert();
		}
		return $this;
	}
	
	/**
	 * Set record data
	 * When 1st parameter is an array, it expected to be an associative array of field => value pairs 
	 * If 2 parameters are set, first one is expected to be a field name and second - it's value
	 * 
	 * @param string|array $field
	 * @param mixed[optional] $value
	 * @return PMXE_Model_Record
	 */
	public function set($field, $value = NULL) {
		if (is_array($field) and ( ! is_null($value) or 0 == count($field))) {
			throw new Exception(__CLASS__ . "::set method expects either not empty associative array as the only paramter or field name and it's value as two seperate parameters.");
		}
		if (is_array($field)) {
			$this->exchangeArray(array_merge($this->toArray(), $field));
		} else {
			$this[$field] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Magic method to resolved object-like request to record values in format $obj->%FIELD_NAME%
	 * @param string $field
	 * @return mixed
	 */
	public function __get($field) {
		if ( ! $this->offsetExists($field)) {
			throw new Exception("Undefined field $field.");
		}
		return $this[$field];
	}
	/**
	 * Magic method to assign values to record fields in format $obj->%FIELD_NAME = value
	 * @param string $field
	 * @param mixed $value
	 */
	public function __set($field, $value) {
		$this[$field] = $value;
	}
	/**
	 * Magic method to check wether some record fields are set
	 * @param string $field
	 * @return bool
	 */
	public function __isset($field) {
		return $this->offsetExists($field);
	}
	/**
	 * Magic method to unset record fields
	 * @param string $field
	 */
	public function __unset($field) {
		$this->offsetUnset($field);
	}
	
}