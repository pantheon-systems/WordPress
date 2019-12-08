<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVCallbackRequest')) :
	class BVCallbackRequest {
		public $params;
		public $method;
		public $wing;
		public $is_afterload;
		public $is_admin_ajax;
		public $is_debug;
		public $account;
		public $calculated_mac;
		public $sig;
		public $time;
		public $version;
		public $is_sha1;
		public $bvb64stream;
		public $bvb64cksize;
		public $checksum;

		public function __construct($account, $in_params) {
			$this->params = array();
			$this->account = $account;
			$this->wing = $in_params['wing'];
			$this->method = $in_params['bvMethod'];
			$this->is_afterload = array_key_exists('afterload', $in_params);
			$this->is_admin_ajax = array_key_exists('adajx', $in_params);
			$this->is_debug = array_key_exists('bvdbg', $in_params);
			$this->sig = $in_params['sig'];
			$this->time = intval($in_params['bvTime']);
			$this->version = $in_params['bvVersion'];
			$this->is_sha1 = array_key_exists('sha1', $in_params);
			$this->bvb64stream = isset($in_params['bvb64stream']);
			$this->bvb64cksize = array_key_exists('bvb64cksize', $in_params) ? intval($in_params['bvb64cksize']) : false;
			$this->checksum = array_key_exists('checksum', $in_params) ? $in_params['checksum'] : false;
		}

		public function isAPICall() {
			return array_key_exists('apicall', $this->params);
		}

		public function respInfo() {
			$info = array(
				"requestedsig" => $this->sig,
				"requestedtime" => $this->time,
				"requestedversion" => $this->version
			);
			if ($this->is_debug) {
				$info["inreq"] = $this->params;
			}
			if ($this->is_admin_ajax) {
				$info["adajx"] = true;
			}
			if ($this->is_afterload) {
				$info["afterload"] = true;
			}
			if ($this->calculated_mac) {
				$info["calculated_mac"] = $this->calculated_mac;
			}
			return $info;
		}

		public function processParams($in_params) {
			$params = array();

			if (array_key_exists('obend', $in_params) && function_exists('ob_end_clean'))
				@ob_end_clean();

			if (array_key_exists('op_reset', $in_params) && function_exists('output_reset_rewrite_vars'))
				@output_reset_rewrite_vars();

			if (array_key_exists('binhead', $in_params)) {
				header("Content-type: application/binary");
				header('Content-Transfer-Encoding: binary');
			}

			if (array_key_exists('concat', $in_params)) {
				foreach ($in_params['concat'] as $key) {
					$concated = '';
					$count = intval($in_params[$key]);
					for ($i = 1; $i <= $count; $i++) {
						$concated .= $in_params[$key."_bv_".$i];
					}
					$in_params[$key] = $concated;
				}
			}

			if (array_key_exists('bvprms', $in_params) && isset($in_params['bvprms']) &&
					array_key_exists('bvprmsmac', $in_params) && isset($in_params['bvprmsmac'])) {
				$digest_algo = 'SHA1';
				$sent_mac = $in_params['bvprmsmac'];

				if (array_key_exists('bvprmshshalgo', $in_params) && isset($in_params['bvprmshshalgo'])) {
					$digest_algo = $in_params['bvprmshshalgo'];
				}

				$calculated_mac = hash_hmac($digest_algo, $in_params['bvprms'], $this->account->secret);
				$this->calculated_mac = substr($calculated_mac, 0, 6);

				if ($this->compare_mac($sent_mac, $calculated_mac) === true) {

					if (array_key_exists('b64', $in_params)) {
						foreach ($in_params['b64'] as $key) {
							if (is_array($in_params[$key])) {
								$in_params[$key] = array_map('base64_decode', $in_params[$key]);
							} else {
								$in_params[$key] = base64_decode($in_params[$key]);
							}
						}
					}

					if (array_key_exists('unser', $in_params)) {
						foreach ($in_params['unser'] as $key) {
							$in_params[$key] = json_decode($in_params[$key], TRUE);
						}
					}

					if (array_key_exists('sersafe', $in_params)) {
						$key = $in_params['sersafe'];
						$in_params[$key] = BVCallbackRequest::serialization_safe_decode($in_params[$key]);
					}

					if (array_key_exists('bvprms', $in_params) && isset($in_params['bvprms'])) {
						$params = $in_params['bvprms'];
					}

					if (array_key_exists('clacts', $in_params)) {
						foreach ($in_params['clacts'] as $action) {
							remove_all_actions($action);
						}
					}

					if (array_key_exists('clallacts', $in_params)) {
						global $wp_filter;
						foreach ( $wp_filter as $filter => $val ){
							remove_all_actions($filter);
						}
					}

					if (array_key_exists('memset', $in_params)) {
						$val = intval(urldecode($in_params['memset']));
						@ini_set('memory_limit', $val.'M');
					}

					return $params;
				}
			}

			return false;
		}

		private function compare_mac($l_hash, $r_hash) {
			if (!is_string($l_hash) || !is_string($r_hash)) {
				return false;
			}

			if (strlen($l_hash) !== strlen($r_hash)) {
				return false;
			}

			if (function_exists('hash_equals')) {
				return hash_equals($l_hash, $r_hash);
			} else {
				return $l_hash === $r_hash;
			}
		}

		public static function serialization_safe_decode($data) {
			if (is_array($data)) {
				$data = array_map(array('BVCallbackRequest', 'serialization_safe_decode'), $data);
			} elseif (is_string($data)) {
				$data = base64_decode($data);
			}

			return $data;
		}
	}
endif;