<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVCallbackHandler')) :

	class BVCallbackHandler {
		public $db;
		public $settings;
		public $siteinfo;
		public $request;
		public $account;
		public $response;
		public $bvinfo;

		public function __construct($db, $settings, $siteinfo, $request, $account, $response) {
			$this->db = $db;
			$this->settings = $settings;
			$this->siteinfo = $siteinfo;
			$this->request = $request;
			$this->account = $account;
			$this->response = $response;
			$this->bvinfo = new PTNInfo($this->settings);
		}

		public function bvAdmExecuteWithoutUser() {
			$this->execute(array("bvadmwithoutuser" => true));
		}

		public function bvAdmExecuteWithUser() {
			$this->execute(array("bvadmwithuser" => true));
		}

		public function execute($resp = array()) {
			$this->routeRequest();
			$resp = array(
				"request_info" => $this->request->info(),
				"site_info" => $this->siteinfo->info(),
				"account_info" => $this->account->info(),
				"bvinfo" => $this->bvinfo->info(),
				"api_pubkey" => substr(PTNAccount::getApiPublicKey($this->settings), 0, 8)
			);
			$this->response->terminate($resp);
		}

		public function routeRequest() {
			switch ($this->request->wing) {
			case 'manage':
				require_once dirname( __FILE__ ) . '/wings/manage.php';
				$module = new BVManageCallback($this);
				break;
			case 'fs':
				require_once dirname( __FILE__ ) . '/wings/fs.php';
				$module = new BVFSCallback($this);
				break;
			case 'db':
				require_once dirname( __FILE__ ) . '/wings/db.php';
				$module = new BVDBCallback($this);
				break;
			case 'info':
				require_once dirname( __FILE__ ) . '/wings/info.php';
				$module = new BVInfoCallback($this);
				break;
			case 'dynsync':
				require_once dirname( __FILE__ ) . '/wings/dynsync.php';
				$module = new BVDynSyncCallback($this);
				break;
			case 'ipstr':
				require_once dirname( __FILE__ ) . '/wings/ipstore.php';
				$module = new BVIPStoreCallback($this);
				break;
			case 'wtch':
				require_once dirname( __FILE__ ) . '/wings/watch.php';
				$module = new BVWatchCallback($this);
				break;
			case 'brand':
				require_once dirname( __FILE__ ) . '/wings/brand.php';
				$module = new BVBrandCallback($this);
				break;
			case 'pt':
				require_once dirname( __FILE__ ) . '/wings/protect.php';
				$module = new BVProtectCallback($this);
				break;
			case 'act':
				require_once dirname( __FILE__ ) . '/wings/account.php';
				$module = new BVAccountCallback($this);
				break;
			case 'fswrt':
				require_once dirname( __FILE__ ) . '/wings/fs_write.php';
				$module = new BVFSWriteCallback();
				break;
			default:
				require_once dirname( __FILE__ ) . '/wings/misc.php';
				$module = new BVMiscCallback($this);
				break;
			}
			$resp = $module->process($this->request);
			if ($resp === false) {
				$resp = array(
					"statusmsg" => "Bad Command",
					"status" => false);
			}
			$resp = array(
				$this->request->wing => array(
					$this->request->method => $resp
				)
			);
			$this->response->addStatus("callbackresponse", $resp);
			return 1;
		}
	}
endif;