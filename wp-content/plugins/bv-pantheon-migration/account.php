<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('PTNAccount')) :
	class PTNAccount {
		public $settings;
		public $public;
		public $secret;
		public $sig_match;
		public static $api_public_key = 'bvApiPublic';
		public static $accounts_list = 'bvAccountsList';
	
		public function __construct($settings, $public, $secret) {
			$this->settings = $settings;
			$this->public = $public;
			$this->secret = $secret;
		}

		public static function find($settings, $public) {
			$accounts = self::allAccounts($settings);
			if (array_key_exists($public, $accounts) && isset($accounts[$public]['secret'])) {
				$secret = $accounts[$public]['secret'];
			}
			if (empty($secret) || (strlen($secret) < 32)) {
				return null;
			}
			return new self($settings, $public, $secret);
		}

		public static function update($settings, $allAccounts) {
			$settings->updateOption(self::$accounts_list, $allAccounts);
		}

		public static function randString($length) {
			$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

			$str = "";
			$size = strlen($chars);
			for( $i = 0; $i < $length; $i++ ) {
				$str .= $chars[rand(0, $size - 1)];
			}
			return $str;
		}

		public static function sanitizeKey($key) {
			return preg_replace('/[^a-zA-Z0-9_\-]/', '', $key);
		}

		public static function apiPublicAccount($settings) {
			$pubkey = $settings->getOption(self::$api_public_key);
			return self::find($settings, $pubkey);
		}

		public static function updateApiPublicKey($settings, $pubkey) {
			$settings->updateOption(self::$api_public_key, $pubkey);
		}

		public static function getApiPublicKey($settings) {
			return $settings->getOption(self::$api_public_key);
		}

		public static function getPlugName($settings) {
			$bvinfo = new PTNInfo($settings);
			return $bvinfo->plugname;
		}

		public static function allAccounts($settings) {
			$accounts = $settings->getOption(self::$accounts_list);
			if (!is_array($accounts)) {
				$accounts = array();
			}
			return $accounts;
		}

		public static function accountsByPlugname($settings) {
			$accounts = self::allAccounts($settings);
			$accountsByPlugname = array();
			$plugname = self::getPlugName($settings);
			foreach ($accounts as $pubkey => $value) {
				if (array_key_exists($plugname, $value) && $value[$plugname] == 1) {
					$accountsByPlugname[$pubkey] = $value;
				}
			}
			return $accountsByPlugname;
		}

		public static function isConfigured($settings) {
			$accounts = self::accountsByPlugname($settings);
			return (sizeof($accounts) >= 1);
		}

		public static function setup($settings) {
			$bvinfo = new PTNInfo($settings);
			$settings->updateOption($bvinfo->plug_redirect, 'yes');
			$settings->updateOption('bvActivateTime', time());
		}

		public function authenticatedUrl($method) {
			$bvinfo = new PTNInfo($this->settings);
			$qstr = http_build_query($this->newAuthParams($bvinfo->version));
			return $bvinfo->appUrl().$method."?".$qstr;
		}

		public function newAuthParams($version) {
			$bvinfo = new PTNInfo($this->settings);
			$args = array();
			$time = time();
			$sig = sha1($this->public.$this->secret.$time.$version);
			$args['sig'] = $sig;
			$args['bvTime'] = $time;
			$args['bvPublic'] = $this->public;
			$args['bvVersion'] = $version;
			$args['sha1'] = '1';
			$args['plugname'] = $bvinfo->plugname;
			return $args;
		}

		public static function addAccount($settings, $public, $secret) {
			$accounts = self::allAccounts($settings);
			if (!isset($public, $accounts)) {
				$accounts[$public] = array();
			}
			$accounts[$public]['secret'] = $secret;
			self::update($settings, $accounts);
		}

		public function info() {
			return array(
				"public" => substr($this->public, 0, 6),
				"sigmatch" => substr($this->sig_match, 0, 6)
			);
		}

		public static function getSigMatch($request, $secret) {
			$method = $request->method;
			$time = $request->time;
			$version = $request->version;
			if ($request->is_sha1) {
				$sig_match = sha1($method.$secret.$time.$version);
			} else {
				$sig_match = md5($method.$secret.$time.$version);
			}
			return $sig_match;
		}

		public function authenticate($request) {
			$time = $request->time;
			if ($time < intval($this->settings->getOption('bvLastRecvTime')) - 300) {
				return false;
			}
			$this->sig_match = self::getSigMatch($request, $this->secret);
			if ($this->sig_match !== $request->sig) {
				return $sig_match;
			}
			$this->settings->updateOption('bvLastRecvTime', $time);
			return 1;
		}
	
		public function updateInfo($info) {
			$accounts = self::allAccounts($this->settings);
			$plugname = self::getPlugName($this->settings);
			$pubkey = $info['pubkey'];
			if (!array_key_exists($pubkey, $accounts)) {
				$accounts[$pubkey] = array();
			}
			$accounts[$pubkey]['lastbackuptime'] = time();
			$accounts[$pubkey][$plugname] = true;
			$accounts[$pubkey]['url'] = $info['url'];
			$accounts[$pubkey]['email'] = $info['email'];
			self::update($this->settings, $accounts);
		}

		public static function remove($settings, $pubkey) {
			$accounts = self::allAccounts($settings);
			if (array_key_exists($pubkey, $accounts)) {
				unset($accounts[$pubkey]);
				self::update($settings, $accounts);
				return true;
			}
			return false;
		}

		public static function exists($settings, $pubkey) {
			$accounts = self::allAccounts($settings);
			return array_key_exists($pubkey, $accounts);
		}
	}
endif;