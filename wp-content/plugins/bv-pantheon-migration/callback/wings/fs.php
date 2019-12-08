<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVFSCallback')) :
require_once dirname( __FILE__ ) . '/../streams.php';

class BVFSCallback extends BVCallbackBase {
	public $stream;
	public $account;

	public function __construct($callback_handler) {
		$this->account = $callback_handler->account;
	}

	function fileStat($relfile) {
		$absfile = ABSPATH.$relfile;
		$fdata = array();
		$fdata["filename"] = $relfile;
		$stats = @stat($absfile);
		if ($stats) {
			foreach (preg_grep('#size|uid|gid|mode|mtime#i', array_keys($stats)) as $key ) {
				$fdata[$key] = $stats[$key];
			}
			if (is_link($absfile)) {
				$fdata["link"] = @readlink($absfile);
			}
		} else {
			$fdata["failed"] = true;
		}
		return $fdata;
	}

	function scanFilesUsingGlob($initdir = "./", $offset = 0, $limit = 0, $bsize = 512, $recurse = true, $regex = '{.??,}*') {
		$i = 0;
		$dirs = array();
		$dirs[] = $initdir;
		$bfc = 0;
		$bfa = array();
		$current = 0;
		$abspath = realpath(ABSPATH).'/';
		$abslen = strlen($abspath);
		# XNOTE: $recurse cannot be used directly here
		while ($i < count($dirs)) {
			$dir = $dirs[$i];

			foreach (glob($abspath.$dir.$regex, GLOB_NOSORT | GLOB_BRACE) as $absfile) {
				$relfile = substr($absfile, $abslen);
				if (is_dir($absfile) && !is_link($absfile)) {
					$dirs[] = $relfile."/";
				}
				$current++;
				if ($offset >= $current)
					continue;
				if (($limit != 0) && (($current - $offset) > $limit)) {
					$i = count($dirs);
					break;
				}
				$bfa[] = $this->fileStat($relfile);
				$bfc++;
				if ($bfc == $bsize) {
					$str = serialize($bfa);
					$this->stream->writeStream($str);
					$bfc = 0;
					$bfa = array();
				}
			}
			$regex = '{.??,}*';
			$i++;
			if ($recurse == false)
				break;
		}
		if ($bfc != 0) {
			$str = serialize($bfa);
			$this->stream->writeStream($str);
		}
		return array("status" => "done");
	}

	function scanFiles($initdir = "./", $offset = 0, $limit = 0, $bsize = 512, $recurse = true) {
		$i = 0;
		$dirs = array();
		$dirs[] = $initdir;
		$bfc = 0;
		$bfa = array();
		$current = 0;
		while ($i < count($dirs)) {
			$dir = $dirs[$i];
			$d = @opendir(ABSPATH.$dir);
			if ($d) {
				while (($file = readdir($d)) !== false) {
					if ($file == '.' || $file == '..') { continue; }
					$relfile = $dir.$file;
					$absfile = ABSPATH.$relfile;
					if (is_dir($absfile) && !is_link($absfile)) {
						$dirs[] = $relfile."/";
					}
					$current++;
					if ($offset >= $current)
						continue;
					if (($limit != 0) && (($current - $offset) > $limit)) {
						$i = count($dirs);
						break;
					}
					$bfa[] = $this->fileStat($relfile);
					$bfc++;
					if ($bfc == $bsize) {
						$str = serialize($bfa);
						$this->stream->writeStream($str);
						$bfc = 0;
						$bfa = array();
					}
				}
				closedir($d);
			}
			$i++;
			if ($recurse == false)
				break;
		}
		if ($bfc != 0) {
			$str = serialize($bfa);
			$this->stream->writeStream($str);
		}
		return array("status" => "done");
	}

	function calculateMd5($absfile, $fdata, $offset, $limit, $bsize) {
		if ($offset == 0 && $limit == 0) {
			$md5 = md5_file($absfile);
		} else {
			if ($limit == 0)
				$limit = $fdata["size"];
			if ($offset + $limit < $fdata["size"])
				$limit = $fdata["size"] - $offset;
			$handle = fopen($absfile, "rb");
			$ctx = hash_init('md5');
			fseek($handle, $offset, SEEK_SET);
			$dlen = 1;
			while (($limit > 0) && ($dlen > 0)) {
				if ($bsize > $limit)
					$bsize = $limit;
				$d = fread($handle, $bsize);
				$dlen = strlen($d);
				hash_update($ctx, $d);
				$limit -= $dlen;
			}
			fclose($handle);
			$md5 = hash_final($ctx);
		}
		return $md5;
	}

	function getFilesStats($files, $offset = 0, $limit = 0, $bsize = 102400, $md5 = false) {
		$result = array();
		foreach ($files as $file) {
			$fdata = $this->fileStat($file);
			$absfile = ABSPATH.$file;
			if (!is_readable($absfile)) {
				$result["missingfiles"][] = $file;
				continue;
			}
			if ($md5 === true) {
				$fdata["md5"] = $this->calculateMd5($absfile, $fdata, $offset, $limit, $bsize);
			}
			$result["stats"][] = $fdata;
		}
		return $result;
	}

	function uploadFiles($files, $offset = 0, $limit = 0, $bsize = 102400) {
		$result = array();
		foreach ($files as $file) {
			if (!is_readable(ABSPATH.$file)) {
				$result["missingfiles"][] = $file;
				continue;
			}
			$handle = fopen(ABSPATH.$file, "rb");
			if (($handle != null) && is_resource($handle)) {
				$fdata = $this->fileStat($file);
				$_limit = $limit;
				$_bsize = $bsize;
				if ($_limit == 0)
					$_limit = $fdata["size"];
				if ($offset + $_limit > $fdata["size"])
					$_limit = $fdata["size"] - $offset;
				$fdata["limit"] = $_limit;
				$sfdata = serialize($fdata);
				$this->stream->writeStream($sfdata);
				fseek($handle, $offset, SEEK_SET);
				$dlen = 1;
				while (($_limit > 0) && ($dlen > 0)) {
					if ($_bsize > $_limit)
						$_bsize = $_limit;
					$d = fread($handle, $_bsize);
					$dlen = strlen($d);
					$this->stream->writeStream($d);
					$_limit -= $dlen;
				}
				fclose($handle);
			} else {
				$result["unreadablefiles"][] = $file;
			}
		}
		$result["status"] = "done";
		return $result;
	}

	function process($request) {
		$params = $request->params;
		$stream_init_info = BVStream::startStream($this->account, $request);
		if (array_key_exists('stream', $stream_init_info)) {
			$this->stream = $stream_init_info['stream'];
			switch ($request->method) {
			case "scanfilesglob":
				$initdir = urldecode($params['initdir']);
				$offset = intval(urldecode($params['offset']));
				$limit = intval(urldecode($params['limit']));
				$bsize = intval(urldecode($params['bsize']));
				$regex = urldecode($params['regex']);
				$recurse = true;
				if (array_key_exists('recurse', $params) && $params["recurse"] == "false") {
					$recurse = false;
				}
				$resp = $this->scanFilesUsingGlob($initdir, $offset, $limit, $bsize, $recurse, $regex);
				break;
			case "scanfiles":
				$initdir = urldecode($params['initdir']);
				$offset = intval(urldecode($params['offset']));
				$limit = intval(urldecode($params['limit']));
				$bsize = intval(urldecode($params['bsize']));
				$recurse = true;
				if (array_key_exists('recurse', $params) && $params["recurse"] == "false") {
					$recurse = false;
				}
				$resp = $this->scanFiles($initdir, $offset, $limit, $bsize, $recurse);
				break;
			case "getfilesstats":
				$files = $params['files'];
				$offset = intval(urldecode($params['offset']));
				$limit = intval(urldecode($params['limit']));
				$bsize = intval(urldecode($params['bsize']));
				$md5 = false;
				if (array_key_exists('md5', $params)) {
					$md5 = true;
				}
				$resp = $this->getFilesStats($files, $offset, $limit, $bsize, $md5);
				break;
			case "sendmanyfiles":
				$files = $params['files'];
				$offset = intval(urldecode($params['offset']));
				$limit = intval(urldecode($params['limit']));
				$bsize = intval(urldecode($params['bsize']));
				$resp = $this->uploadFiles($files, $offset, $limit, $bsize);
				break;
			case "filelist":
				$initdir = $params['initdir'];
				$glob_option = GLOB_MARK;
				if(array_key_exists('onlydir', $params)) {
					$glob_option = GLOB_ONLYDIR;
				}
				$regex = "*";
				if(array_key_exists('regex', $params)){
					$regex = $params['regex'];
				}
				$directoryList = glob($initdir.$regex, $glob_option);
				$resp = $this->getFilesStats($directoryList);
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