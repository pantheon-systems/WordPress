<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVCallbackResponse')) :

	class BVCallbackResponse extends BVCallbackBase {
		public $status;
		public $bvb64cksize;

		public function __construct($bvb64cksize) {
			$this->status = array("blogvault" => "response");
			$this->bvb64cksize = $bvb64cksize;
		}

		public function addStatus($key, $value) {
			$this->status[$key] = $value;
		}

		public function addArrayToStatus($key, $value) {
			if (!isset($this->status[$key])) {
				$this->status[$key] = array();
			}
			$this->status[$key][] = $value;
		}

		public function terminate($resp = array()) {
			$resp = array_merge($this->status, $resp);
			$resp["signature"] = "Blogvault API";
			$response = "bvbvbvbvbv".serialize($resp)."bvbvbvbvbv";
			$response = "bvb64bvb64".$this->base64Encode($response, $this->bvb64cksize)."bvb64bvb64";
			die($response);

			exit;
		}
	}
endif;