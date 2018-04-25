<?php 
class PMXI_Input {
	protected $filters = array('stripslashes');
	
	public function read($inputArray, $paramName, $default = NULL) {
		if (is_array($paramName) and ! is_null($default)) {
			throw new Exception('Either array of parameter names with default values as the only argument or param name and default value as seperate arguments are expected.');
		}
		if (is_array($paramName)) {
			foreach ($paramName as $param => $def) {
				if (isset($inputArray[$param])) {
					$paramName[$param] = $this->applyFilters($inputArray[$param]);
				}
			}
			return $paramName;
		} else {
			return isset($inputArray[$paramName]) ? $this->applyFilters($inputArray[$paramName]) : $default;
		}
	}
	
	public function get($paramName, $default = NULL) {
		$this->addFilter('strip_tags');
		$this->addFilter('htmlspecialchars');		
		$this->addFilter('esc_sql');		
		$result = $this->read($_GET, $paramName, $default);
		$this->removeFilter('strip_tags');		
		$this->removeFilter('htmlspecialchars');		
		$this->removeFilter('esc_sql');
		return $result;
	}
	
	public function post($paramName, $default = NULL) {
		return $this->read($_POST, $paramName, $default);
	}
	
	public function cookie($paramName, $default = NULL) {
		return $this->read($_COOKIE, $paramName, $default);
	}
	
	public function request($paramName, $default = NULL) {
		return $this->read($_GET + $_POST + $_COOKIE, $paramName, $default);
	}
	
	public function getpost($paramName, $default = NULL) {
		return $this->read($_GET + $_POST, $paramName, $default);
	}
	
	public function server($paramName, $default = NULL) {
		return $this->read($_SERVER, $paramName, $default);
	}
	
	public function addFilter($callback) {
		if ( ! is_callable($callback)) {
			throw new Exception(get_class($this) . '::' . __METHOD__ . ' parameter must be a proper callback function reference.');
		}
		if ( ! in_array($callback, $this->filters)) {
			$this->filters[] = $callback;
		}
		return $this;
	}
	
	public function removeFilter($callback) {
		$this->filters = array_diff($this->filters, array($callback));
		return $this;
	}
	
	protected function applyFilters($val) {
		if (is_array($val)) {
			foreach ($val as $k => $v) {
				$val[$k] = $this->applyFilters($v);
			}
		} else {
			foreach ($this->filters as $filter) {
				$val = call_user_func($filter, $val);
			}
		}		
		return $val;
	}
}