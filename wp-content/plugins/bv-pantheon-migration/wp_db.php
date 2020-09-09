<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('PTNWPDb')) :

class PTNWPDb {
	public function dbprefix() {
		global $wpdb;
		$prefix = $wpdb->base_prefix ? $wpdb->base_prefix : $wpdb->prefix;
		return $prefix;
	}

	public function prepare($query, $args) {
		global $wpdb;
		return $wpdb->prepare($query, $args);
	}

	public function getSiteId() {
		global $wpdb;
		return $wpdb->siteid;
	}

	public function getResult($query, $obj = ARRAY_A) {
		global $wpdb;
		return $wpdb->get_results($query, $obj);
	}

	public function query($query) {
		global $wpdb;
		return $wpdb->query($query);
	}

	public function getVar($query, $col = 0, $row = 0) {
		global $wpdb;
		return $wpdb->get_var($query, $col, $row);
	}

	public function getCol($query, $col = 0) {
		global $wpdb;
		return $wpdb->get_col($query, $col);
	}

	public function tableName($table) {
		return $table[0];
	}

	public function showTables() {
		$tables = $this->getResult("SHOW TABLES", ARRAY_N);
		return array_map(array($this, 'tableName'), $tables);
	}

	public function showTableStatus() {
		return $this->getResult("SHOW TABLE STATUS");
	}

	public function tableKeys($table) {
		return $this->getResult("SHOW KEYS FROM $table;");
	}

	public function describeTable($table) {
		return $this->getResult("DESCRIBE $table;");
	}

	public function checkTable($table, $type) {
		return $this->getResult("CHECK TABLE $table $type;");
	}

	public function repairTable($table) {
		return $this->getResult("REPAIR TABLE $table;");
	}

	public function showTableCreate($table) {
		return $this->getVar("SHOW CREATE TABLE $table;", 1);
	}

	public function rowsCount($table) {
		$count = $this->getVar("SELECT COUNT(*) FROM $table;");
		return intval($count);
	}

	public function createTable($query, $name, $usedbdelta = false) {
		$table = $this->getBVTable($name);
		if (!$this->isTablePresent($table)) {
			if ($usedbdelta) {
				if (!function_exists('dbDelta'))
					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta($query);
			} else {
				$this->query($query);
			}
		}
		return $this->isTablePresent($table);
	}

	public function alterBVTable($query, $name) {
		$resp = false;
		$table = $this->getBVTable($name);
		if ($this->isTablePresent($table)) {
			$resp = $this->query($query);
		}
		return $resp;
	}

	public function getTableContent($table, $fields = '*', $filter = '', $limit = 0, $offset = 0) {
		$query = "SELECT $fields from $table $filter";
		if ($limit > 0)
			$query .= " LIMIT $limit";
		if ($offset > 0)
			$query .= " OFFSET $offset";
		$rows = $this->getResult($query);
		return $rows;
	}

	public function isTablePresent($table) {
		return ($this->getVar("SHOW TABLES LIKE '$table'") === $table);
	}

	public function getCharsetCollate() {
		global $wpdb;
		return $wpdb->get_charset_collate();
	}

	public function getWPTable($name) {
		return ($this->dbprefix() . $name);
	}

	public function getBVTable($name) {
		return ($this->getWPTable("bv_" . $name));
	}

	public function truncateBVTable($name) {
		$table = $this->getBVTable($name);
		if ($this->isTablePresent($table)) {
			return $this->query("TRUNCATE TABLE $table;");
		} else {
			return false;
		}
	}
	
	public function deleteBVTableContent($name, $filter = "") {
		$table = $this->getBVTable($name);
		if ($this->isTablePresent($table)) {
			return $this->query("DELETE FROM $table $filter;");
		} else {
			return false;
		}
	}

	public function dropBVTable($name) {
		$table = $this->getBVTable($name);
		if ($this->isTablePresent($table)) {
			$this->query("DROP TABLE IF EXISTS $table;");
		}
		return !$this->isTablePresent($table);
	}

	public function deleteRowsFromtable($name, $count = 1) {
		$table = $this->getBVTable($name);
		if ($this->isTablePresent($table)) {
			return $this->getResult("DELETE FROM $table LIMIT $count;");
		} else {
			return false;
		}
	}

	public function replaceIntoBVTable($name, $value) {
		global $wpdb;
		$table = $this->getBVTable($name);
		return $wpdb->replace($table, $value);
	}
	
	public function tinfo($name) {
		$result = array();
		$table = $this->getBVTable($name);

		$result['name'] = $table;

		if ($this->isTablePresent($table)) {
			$result['exists'] = true;
			$result['createquery'] = $this->showTableCreate($table);
		}

		return $result;
	}

	public function getMysqlVersion() {
		global $wpdb;
		return $wpdb->db_version();
	}
}
endif;