<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('PTNWPAPI')) :
	class PTNWPAPI {
		public $settings;

		public function __construct($settings) {
			$this->settings = $settings;
		}

		public function pingbv($method, $body, $public = false) {
			if ($public) {
				$this->do_request($method, $body, $public);
			} else {
				$api_public_key = $this->settings->getOption('bvApiPublic');
				if (!empty($api_public_key) && (strlen($api_public_key) >= 32)) {
					$this->do_request($method, $body, $api_public_key);
				}
			}
		}

		public function do_request($method, $body, $pubkey) {
			$account = PTNAccount::find($this->settings, $pubkey);
			if (isset($account)) {
				$url = $account->authenticatedUrl($method);
				$this->http_request($url, $body);
			}
		}

		public function http_request($url, $body, $headers = array()) {
			$_body = array(
				'method' => 'POST',
				'timeout' => 15,
				'body' => $body
			);
			if (!empty($headers)) {
				$_body['headers'] = $headers;
			}
			return wp_remote_post($url, $_body);
		}
	}
endif;