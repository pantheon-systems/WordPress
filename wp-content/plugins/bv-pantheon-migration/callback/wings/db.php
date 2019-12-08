<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVDBCallback')) :
require_once dirname( __FILE__ ) . '/../streams.php';

class BVDBCallback extends BVCallbackBase {
	public $db;
	public $stream;
	public $account;

	public function __construct($callback_handler) {
		$this->db = $callback_handler->db;
		$this->account = $callback_handler->account;
	}

	public function getLastID($pkeys, $end_row) {
		$last_ids = array();
		foreach($pkeys as $pk) {
			$last_ids[$pk] = $end_row[$pk];
		}
		return $last_ids;
	}

	public function getTableData($table, $tname, $rcount, $offset, $limit, $bsize, $filter, $pkeys, $include_rows = false) {
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
			$data["offset"] = $offset;
			$data["size"] = $srows;
			$data["md5"] = md5(serialize($rows));
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
				$table = urldecode($params['table']);
				$resp = array("table_keys" => $db->tableKeys($table));
				break;
			case "describetable":
				$table = urldecode($params['table']);
				$resp = array("table_description" => $db->describeTable($table));
				break;
			case "checktable":
				$table = urldecode($params['table']);
				$type = urldecode($params['type']);
				$resp = array("status" => $db->checkTable($table, $type));
				break;
			case "repairtable":
				$table = urldecode($params['table']);
				$resp = array("status" => $db->repairTable($table));
				break;
			case "gettcrt":
				$table = urldecode($params['table']);
				$resp = array("create" => $db->showTableCreate($table));
				break;
			case "getrowscount":
				$table = urldecode($params['table']);
				$resp = array("count" => $db->rowsCount($table));
				break;
			case "gettablecontent":
				$result = array();
				$table = urldecode($params['table']);
				$fields = urldecode($params['fields']);
				$filter = (array_key_exists('filter', $params)) ? urldecode($params['filter']) : "";
				$limit = intval(urldecode($params['limit']));
				$offset = intval(urldecode($params['offset']));
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
			case "tableinfo":
				$table = urldecode($params['table']);
				$offset = intval(urldecode($params['offset']));
				$limit = intval(urldecode($params['limit']));
				$bsize = intval(urldecode($params['bsize']));
				$filter = (array_key_exists('filter', $params)) ? urldecode($params['filter']) : "";
				$rcount = intval(urldecode($params['rcount']));
				$tname = urldecode($params['tname']);
				$pkeys = (array_key_exists('pkeys', $params)) ? $params['pkeys'] : array();
				$resp = $this->getTableData($table, $tname, $rcount, $offset, $limit, $bsize, $filter, $pkeys, false);
				break;
			case "uploadrows":
				$table = urldecode($params['table']);
				$offset = intval(urldecode($params['offset']));
				$limit = intval(urldecode($params['limit']));
				$bsize = intval(urldecode($params['bsize']));
				$filter = (array_key_exists('filter', $params)) ? urldecode($params['filter']) : "";
				$rcount = intval(urldecode($params['rcount']));
				$tname = urldecode($params['tname']);
				$pkeys = (array_key_exists('pkeys', $params)) ? $params['pkeys'] : array();
				$resp = $this->getTableData($table, $tname, $rcount, $offset, $limit, $bsize, $filter, $pkeys, true);
				break;
			case "tblexists":
				$resp = array("tblexists" => $db->isTablePresent($params['tablename']));
				break;
			case "crttbl":
				$usedbdelta = array_key_exists('usedbdelta', $params);
				$resp = array("crttbl" => $db->createTable($params['query'], $params['tablename'], $usedbdelta));
				break;
			case "drptbl":
				$resp = array("drptbl" => $db->dropBVTable($params['name']));
				break;
			case "trttbl":
				$resp = array("trttbl" => $db->truncateBVTable($params['name']));
				break;
			case "altrtbl":
				$resp = array("altrtbl" => $db->alterBVTable($params['query'], $params['query']));
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