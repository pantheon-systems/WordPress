<?php
/**
 * Class to load config files
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXI_Config implements IteratorAggregate {
	/**
	 * Config variables stored
	 * @var array
	 */
	protected $config = array();
	/**
	 * List of loaded files in order to avoid loading same file several times
	 * @var array
	 */
	protected $loaded = array();
	
	/**
	 * Static method to create config instance from file on disc
	 * @param string $filePath
	 * @param string[optional] $section
	 * @return PMXI_Config
	 */
	public static function createFromFile($filePath, $section = NULL) {
		$config = new self();
		return $config->loadFromFile($filePath, $section);
	}
	
	/**
	 * Load config file
	 * @param string $filePath
	 * @param string[optional] $section
	 * @return PMXI_Config
	 */
	public function loadFromFile($filePath, $section = NULL) {
		if ( ! is_null($section)) {
			$this->config[$section] = self::createFromFile($filePath);
		} else {
			$filePath = realpath($filePath);
			if ($filePath and ! in_array($filePath, $this->loaded)) {
				require $filePath;				
				$config = (!isset($config)) ? array() : $config;
				$this->loaded[] = $filePath;
				$this->config = array_merge($this->config, $config);
			}
		}
		return $this;
	}
	/**
	 * Return value of setting with specified name
	 * @param string $field Setting name
	 * @param string[optional] $section Section name to look setting in
	 * @return mixed
	 */
	public function get($field, $section = NULL) {
		return ! is_null($section) ? $this->config[$section]->get($field) : $this->config[$field];
	}
	
	/**
	 * Magic method for checking whether some config option are set
	 * @param string $field
	 * @return bool
	 */
	public function __isset($field) {
		return isset($this->config[$field]);
	}
	/**
	 * Magic method to implement object-like access to config parameters
	 * @param string $field
	 * @return mixed
	 */
	public function __get($field) {
		return $this->config[$field];
	}
	
	/**
	 * Return all config options as array
	 * @return array
	 */
	public function toArray($section = NULL) {
		return ! is_null($section) ? $this->config[$section]->toArray() : $this->config;
	}
	
	public function getIterator() {
		return new ArrayIterator($this->config);
	}
	
}