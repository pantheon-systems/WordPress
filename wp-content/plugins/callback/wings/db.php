<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVDBCallback')) :
require_once dirname( __FILE__ ) . '/../streams.php';

class BVDBCallback extends BVCallbackBase {
	public $db;
	public $stream;
	public $account;
	public $siteinfo;

	public static $bvTables = array("fw_requests", "lp_requests", "ip_store");

	const DB_WING_VERSION = 1.3;

	public function __construct($callback_handler) {
		$this->db = $callback_handler->db;
		$this->account = $callback_handler->account;
		$this->siteinfo = $callback_handler->siteinfo;
	}

	public function getLastID($pkeys, $end_row) {
		$last_ids = array();
		foreach($pkeys as $pk) {
			$last_ids[$pk] = $end_row[$pk];
		}
		return $last_ids;
	}

	public function getTableData($table, $tname, $offset, $limit, $bsize, $filter, $pkeys, $include_rows = false) {
		$tinfo = array();
		
		$rows_count = $this->db->rowsCount($table);
		$result = array('count' => $rows_count);
		if ($limit == 0) {
			$limit = $rows_count;
		}
		$srows = 1;
		while (($limit > 0) && ($srows > 0)) {
			if ($bsize > $limit)
				$bsize = $limit;
			$rows = $this->db->getTableContent($table, '*', $filter, $bsize, $offset);
			$srows = sizeof($rows);
			$data = array();
			$data["table_name"] = $tname;
			$data["offset"] = $offset;
			$data["size"] = $srows;
			$serialized_rows = serialize($rows);
			$data['md5'] = md5($serialized_rows);
			$data['length'] = strlen($serialized_rows);
			array_push($tinfo, $data);
			if (!empty($pkeys) && $srows > 0) {
				$end_row = end($rows);
				$last_ids = $this->getLastID($pkeys, $end_row);
				$data['last_ids'] = $last_ids;
				$result['last_ids'] = $last_ids;
			}
			if ($include_rows) {
				$data["rows"] = $rows;
				$str = serialize($data);
				$this->stream->writeStream($str);
			}
			$offset += $srows;
			$limit -= $srows;
		}
		$result['size'] = $offset;
		$result['tinfo'] = $tinfo;
		return $result;
	}

	public function streamQueryResult($identifier, $query, $pkeys) {
		$data = array();
		$data["identifier"] = $identifier;
		$data["query"] = $query;

		$data["query_start_time"] = time();
		$rows = $this->db->getResult($query);
		$srows = sizeof($rows);
		$data["size"] = $srows;
		$data["query_end_time"] = time();
		if (!empty($pkeys) && $srows > 0) {
			$end_row = end($rows);
			$last_ids = $this->getLastID($pkeys, $end_row);
			$data['last_ids'] = $last_ids;
		}
		$result = array_merge($data);
		$data["rows"] = $rows;
		$serialized_rows = serialize($data);
		$this->stream->writeStream($serialized_rows);
		$result['length'] = strlen($serialized_rows);
		return $result;
	}

	function getRandomData($totalSize, $bsize) {
		if ($bsize == 0) {
			$bsize = $totalSize;
		}

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);

		while ($totalSize > 0) {
			if ($bsize > $totalSize) {
				$bsize = $totalSize;
			}

			$randomString = '';
			for ($i = 0; $i < $bsize; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}

			$this->stream->writeStream($randomString);
			$totalSize -= $bsize;
		}
		return array("status" => "true");
	}

	public function getCreateTableQueries($tables) {
		$resp = array();
		foreach($tables as $table) {
			$tname = $table;
			$resp[$tname] = array("create" => $this->db->showTableCreate($table)); 
		}
		return $resp;
	}

	public function checkTables($tables, $type) {
		$resp = array();
		foreach($tables as $table) {
			$tname = $table;
			$resp[$tname] = array("status" => $this->db->checkTable($table, $type));
		}
		return $resp;
	}

	public function describeTables($tables) {
		$resp = array();
		foreach($tables as $table) {
			$tname = $table;
			$resp[$tname] = array("description" => $this->db->describeTable($table));
			$resp[$tname]["primary_keys_index"] = $this->db->showTableIndex($table);
		}
		return $resp;
	}

	public function checkTablesExist($tables) {
		$resp = array();
		foreach($tables as $table) {
			$tname = $table;
			$resp[$tname] = array("tblexists" => $this->db->isTablePresent($table));
		}
		return $resp;
	}

	public function getTablesRowCount($tables) {
		$resp = array();
		foreach($tables as $table) {
			$tname = $table;
			$resp[$tname] = array("count" => $this->db->rowsCount($table));
		}
		return $resp;
	}

	public function getTablesKeys($tables) {
		$resp = array();
		foreach($tables as $table) {
			$tname = $table;
			$resp[$tname] = array("keys" => $this->db->tableKeys($table));
		}
		return $resp;
	}

	public function multiGetResult($queries) {
		$resp = array();
		foreach($queries as $query) {
			array_push($resp, $this->db->getResult($query));
		}
		return $resp;
	}

	public function process($request) {
		$db = $this->db;
		$params = $request->params;
		$stream_init_info = BVStream::startStream($this->account, $request);

		if (array_key_exists('stream', $stream_init_info)) {
			$this->stream = $stream_init_info['stream'];
			switch ($request->method) {
			case "gettbls":
				$resp = array("tables" => $db->showTables());
				break;
			case "tblstatus":
				$resp = array("statuses" => $db->showTableStatus());
				break;
			case "tablekeys":
				$table = $params['table'];
				$resp = array("table_keys" => $db->tableKeys($table));
				break;
			case "describetable":
				$table = $params['table'];
				$resp = array("table_description" => $db->describeTable($table));
				$resp["primary_keys_index"] = $db->showTableIndex($table);
				break;
			case "checktable":
				$table = $params['table'];
				$type = $params['type'];
				$resp = array("status" => $db->checkTable($table, $type));
				break;
			case "repairtable":
				$table = $params['table'];
				$resp = array("status" => $db->repairTable($table));
				break;
			case "gettcrt":
				$table = $params['table'];
				$resp = array("create" => $db->showTableCreate($table));
				break;
			case "tblskys":
				$tables = $params['tables'];
				$resp = $this->getTablesKeys($tables);
				break;
			case "getmlticrt":
				$tables = $params['tables'];
				$resp = $this->getCreateTableQueries($tables);
				break;
			case "desctbls":
				$tables = $params['tables'];
				$resp = $this->describeTables($tables);
				break;
			case "mltirwscount":
				$tables = $params['tables'];
				$resp = $this->getTablesRowCount($tables);
				break;
			case "chktabls":
				$tables = $params['tables'];
				$type = $params['type'];
				$resp = $this->checkTables($tables, $type);
				break;
			case "chktablsxist":
				$tables = $params['tables'];
				$resp = $this->checkTablesExist($tables);
				break;
			case "getrowscount":
				$table = $params['table'];
				$resp = array("count" => $db->rowsCount($table));
				break;
			case "gettablecontent":
				$result = array();
				$table = $params['table'];
				$fields = $params['fields'];
				$filter = (array_key_exists('filter', $params)) ? $params['filter'] : "";
				$limit = intval($params['limit']);
				$offset = intval($params['offset']);
				$pkeys = (array_key_exists('pkeys', $params)) ? $params['pkeys'] : array();
				$result['timestamp'] = time();
				$result['tablename'] = $table;
				$rows = $db->getTableContent($table, $fields, $filter, $limit, $offset);
				$srows = sizeof($rows);
				if (!empty($pkeys) && $srows > 0) {
					$end_row = end($rows);
					$result['last_ids'] = $this->getLastID($pkeys, $end_row);
				}
				$result["rows"] = $rows;
				$resp = $result;
				break;
			case "multitablecontent":
				$tableParams = $params['table_params'];
				$resp = array();
				foreach($tableParams as $tableParam) {
					$result = array();
					$identifier = $tableParam['identifier'];
					$table = $tableParam['table'];
					$tname = $tableParam['tname'];
					$fields = $tableParam['fields'];
					$filter = (array_key_exists('filter', $tableParam)) ? $tableParam['filter'] : "";
					$limit = $tableParam['limit'];
					$offset = $tableParam['offset'];
					$pkeys = (array_key_exists('pkeys', $tableParam)) ? $tableParam['pkeys'] : array();
					$result['timestamp'] = time();
					$result['table_name'] = $tname;
					$rows = $db->getTableContent($table, $fields, $filter, $limit, $offset);
					$srows = sizeof($rows);
					if (!empty($pkeys) && $srows > 0) {
						$end_row = end($rows);
						$result['last_ids'] = $this->getLastID($pkeys, $end_row);
					}
					$result["rows"] = $rows;
					$result["size"] = $srows;
					$resp[$identifier] = $result;
				}
				break;
			case "tableinfo":
				$table = $params['table'];
				$offset = intval($params['offset']);
				$limit = intval($params['limit']);
				$bsize = intval($params['bsize']);
				$filter = (array_key_exists('filter', $params)) ? $params['filter'] : "";
				$tname = $params['tname'];
				$pkeys = (array_key_exists('pkeys', $params)) ? $params['pkeys'] : array();
				$resp = $this->getTableData($table, $tname, $offset, $limit, $bsize, $filter, $pkeys, false);
				break;
			case "getmulttables":
				$result = array();
				$tableParams = $params['table_params'];
				$resp = array();
				foreach($tableParams as $tableParam) {
					$table = $tableParam['table'];
					$tname = $tableParam['tname'];
					$filter = (array_key_exists('filter', $tableParam)) ? $tableParam['filter'] : "";
					$limit = intval($tableParam['limit']);
					$offset = intval($tableParam['offset']);
					$bsize = intval($tableParam['bsize']);
					$pkeys = (array_key_exists('pkeys', $tableParam)) ? $tableParam['pkeys'] : array();
					$resp[$tname] = $this->getTableData($table, $tname, $offset, $limit, $bsize, $filter, $pkeys, true);
				}
				break;
			case "uploadrows":
				$table = $params['table'];
				$offset = intval($params['offset']);
				$limit = intval($params['limit']);
				$bsize = intval($params['bsize']);
				$filter = (array_key_exists('filter', $params)) ? $params['filter'] : "";
				$tname = $params['tname'];
				$pkeys = (array_key_exists('pkeys', $params)) ? $params['pkeys'] : array();
				$resp = $this->getTableData($table, $tname, $offset, $limit, $bsize, $filter, $pkeys, true);
				break;
			case "tblexists":
				$resp = array("tblexists" => $db->isTablePresent($params['table']));
				break;
			case "crttbl":
				$usedbdelta = array_key_exists('usedbdelta', $params);
				$resp = array("crttbl" => $db->createTable($params['query'], $params['table'], $usedbdelta));
				break;
			case "drptbl":
				$resp = array("drptbl" => $db->dropBVTable($params['table']));
				break;
			case "trttbl":
				$resp = array("trttbl" => $db->truncateBVTable($params['table']));
				break;
			case "altrtbl":
				$resp = array("altrtbl" => $db->alterBVTable($params['query'], $params['query']));
				break;
			case "mltigtrslt":
				$resp = array("mltigtrslt" => $this->multiGetResult($params['queries']));
				break;
			case "mltiqrsstrm":
				$queries = $params['queries'];
				$result = array();
				foreach ($queries as $qparams) {
					$identifier = $qparams['identifier'];
					$query = $qparams['query'];
					$pkeys = (array_key_exists('pkeys', $qparams)) ? $qparams['pkeys'] : array();
					array_push($result, $this->streamQueryResult($identifier, $query, $pkeys));
				}
				$resp = array('mltqrsstrm' => $result);
				break;
			case "getrndmdata":
				$resp = array("getrndmdata" => $this->getRandomData($params['size'], $params['batch_size']));
				break;
			case "tbls":
				$resp = array();

				if (array_key_exists('truncate', $params))
					$resp['truncate'] = $db->truncateTables($params['truncate']);

				if (array_key_exists('drop', $params))
					$resp['drop'] = $db->dropTables($params['drop']);

				if (array_key_exists('create', $params))
					$resp['create'] = $db->createTables($params['create']);

				if (array_key_exists('alter', $params))
					$resp['alter'] = $db->alterTables($params['alter']);

				break;
			default:
				$resp = false;
			}
			$end_stream_info = $this->stream->endStream();
			if (!empty($end_stream_info) && is_array($resp)) {
				$resp = array_merge($resp, $end_stream_info);
			}
		} else {
			$resp = $stream_init_info;
		}
		return $resp;
	}
}
endif;