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
		public $settings;
		public $sig;
		public $sighshalgo;
		public $time;
		public $version;
		public $is_sha1;
		public $bvb64stream;
		public $bvb64cksize;
		public $checksum;
		public $error = array();
		public $pubkey_name;
		public $bvprmsmac;
		public $bvboundry;

		public function __construct($account, $in_params, $settings) {
			$this->params = array();
			$this->account = $account;
			$this->settings = $settings;
			$this->wing = $in_params['wing'];
			$this->method = $in_params['bvMethod'];
			$this->is_afterload = array_key_exists('afterload', $in_params);
			$this->is_admin_ajax = array_key_exists('adajx', $in_params);
			$this->is_debug = array_key_exists('bvdbg', $in_params);
			$this->sig = $in_params['sig'];
			$this->sighshalgo = !empty($in_params['sighshalgo']) ? $in_params['sighshalgo'] : null;
			$this->time = intval($in_params['bvTime']);
			$this->version = $in_params['bvVersion'];
			$this->is_sha1 = array_key_exists('sha1', $in_params);
			$this->bvb64stream = isset($in_params['bvb64stream']);
			$this->bvb64cksize = array_key_exists('bvb64cksize', $in_params) ? intval($in_params['bvb64cksize']) : false;
			$this->checksum = array_key_exists('checksum', $in_params) ? $in_params['checksum'] : false;
			$this->pubkey_name = !empty($in_params['pubkeyname']) ?
					PTNAccount::sanitizeKey($in_params['pubkeyname']) : 'm_public';
			$this->bvprmsmac = !empty($in_params['bvprmsmac']) ? PTNAccount::sanitizeKey($in_params['bvprmsmac']) : "";
			$this->bvboundry = !empty($in_params['bvboundry']) ? $in_params['bvboundry'] : "";
		}

		public function isAPICall() {
			return array_key_exists('apicall', $this->params);
		}

		public function curlRequest($url, $body) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			return curl_exec($ch);
		}

		public function fileGetContentRequest($url, $body) {
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($body)
				)
			);

			$context  = stream_context_create($options);
			return file_get_contents($url, false, $context);
		}

		public function http_request($url, $body) {
			if (in_array('curl', get_loaded_extensions())) {
				return $this->curlRequest($url, $body);
			} else {
				return $this->fileGetContentRequest($url, $body);
			} 
		}

		public function get_params_via_api($params_key, $apiurl) {
			$res = $this->http_request($apiurl, array('bvkey' => $params_key));

			if ($res === FALSE) {
				return false;
			}

			return $res;
		}

		public function info() {
			$info = array(
				"requestedsig" => $this->sig,
				"requestedtime" => $this->time,
				"requestedversion" => $this->version,
				"error" => $this->error
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
			return $info;
		}

		public function processParams($in_params) {
			$params = array();

			if (array_key_exists('obend', $in_params) && function_exists('ob_end_clean'))
				@ob_end_clean();

			if (array_key_exists('op_reset', $in_params) && function_exists('output_reset_rewrite_vars'))
				@output_reset_rewrite_vars();

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

			if (isset($in_params['bvpdataviaapi']) && isset($in_params['bvapiurl'])) {
				$pdata = $this->get_params_via_api($in_params['bvpdataviaapi'], $in_params['bvapiurl']);
				if ($pdata !== false) {
					$in_params["bvprms"] = $pdata;
				}
			}

			if (array_key_exists('bvprms', $in_params) && isset($in_params['bvprms'])) {
				if (!empty($in_params['bvprmshshalgo']) && $in_params['bvprmshshalgo'] === 'sha256') {
					$calculated_mac = hash_hmac('SHA256', $in_params['bvprms'], $this->account->secret);
				} else {
					$calculated_mac = hash_hmac('SHA1', $in_params['bvprms'], $this->account->secret);
				}

				if ($this->compare_mac($this->bvprmsmac, $calculated_mac) === true) {

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
						$val = intval($in_params['memset']);
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

		public function authenticate() {
			if (!$this->account) {
				$this->error["message"] = "ACCOUNT_NOT_FOUND";
				return false;
			}

			$bv_last_recv_time = $this->settings->getOption('bvLastRecvTime');
			if ($this->time < intval($bv_last_recv_time) - 300) {
				return false;
			}

			$data = $this->method.$this->account->secret.$this->time.$this->version.$this->bvprmsmac;
			if (!$this->verify($data, base64_decode($this->sig), $this->sighshalgo)) {
				return false;
			}
			$this->settings->updateOption('bvLastRecvTime', $this->time);

			return 1;
		}

		public function verify($data, $sig, $sighshalgo) {
			if (!function_exists('openssl_verify') || !function_exists('openssl_pkey_get_public')) {
				$this->error["message"] = "OPENSSL_FUNCS_NOT_FOUND";
				return false;
			}

			$key_file = dirname( __FILE__ ) . '/../public_keys/' . $this->pubkey_name . '.pub';
			if (!file_exists($key_file)) {
				$this->error["message"] = "PUBLIC_KEY_NOT_FOUND";
				return false;
			}
			$public_key_str = file_get_contents($key_file);
			$public_key = openssl_pkey_get_public($public_key_str);
			if (!$public_key) {
				$this->error["message"] = "UNABLE_TO_LOAD_PUBLIC_KEY";
				return false;
			}

			if ($sighshalgo === 'sha256') {
				$verify = openssl_verify($data, $sig, $public_key, OPENSSL_ALGO_SHA256);
			} else {
				$verify = openssl_verify($data, $sig, $public_key);
			}
			if ($verify === 1) {
				return true;
			} elseif ($verify === 0) {
				$this->error["message"] = "INCORRECT_SIGNATURE";
				$this->error["pubkey_sig"] = substr(hash('md5', $public_key_str), 0, 8);
			} else {
				$this->error["message"] = "OPENSSL_VERIFY_FAILED";
			}
			return false;
		}

		public function corruptedParamsResp() {
			$bvinfo = new PTNInfo($this->settings);

			return array(
				"account_info" => $this->account->info(),
				"request_info" => $this->info(),
				"bvinfo" => $bvinfo->info(),
				"statusmsg" => "BVPRMS_CORRUPTED"
			);
		}

		public function authFailedResp() {
			$api_public_key = PTNAccount::getApiPublicKey($this->settings);
			$default_secret = PTNRecover::getDefaultSecret($this->settings);
			$bvinfo = new PTNInfo($this->settings);
			$resp = array(
				"request_info" => $this->info(),
				"bvinfo" => $bvinfo->info(),
				"statusmsg" => "FAILED_AUTH",
				"api_pubkey" => substr($api_public_key, 0, 8),
				"def_sigmatch" => substr(hash('sha1', $this->method.$default_secret.$this->time.$this->version), 0, 8)
			);

			if ($this->account) {
				$resp["account_info"] = $this->account->info();
				$resp["sigmatch"] = substr(hash('sha1', $this->method.$this->account->secret.$this->time.$this->version), 0, 6);
			} else {
				$resp["account_info"] = array("error" => "ACCOUNT_NOT_FOUND");
			}

			return $resp;
		}
	}
endif;