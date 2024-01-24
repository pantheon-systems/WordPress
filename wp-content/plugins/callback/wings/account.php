<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVAccountCallback')) :
class BVAccountCallback extends BVCallbackBase {
	public $account;
	public $settings;
	
	const ACCOUNT_WING_VERSION = 1.2;

	public function __construct($callback_handler) {
		$this->account = $callback_handler->account;
		$this->settings = $callback_handler->settings;
	}

	function updateInfo($args) {
		$result = array();

		if (array_key_exists('update_info', $args)) {
			$this->account->updateInfo($args['update_info']);
			$result['update_info'] = array(
				"status" => PTNAccount::exists($this->settings, $args['update_info']['pubkey'])
			);
		}
	
		if (array_key_exists('update_api_key', $args)) {
			PTNAccount::updateApiPublicKey($this->settings, $args['update_api_key']['pubkey']);
			$result['update_api_key'] = array(
				"status" => $this->settings->getOption(PTNAccount::$api_public_key)
			);
		}

		if (array_key_exists('update_options', $args))
			$result['update_options'] = $this->settings->updateOptions($args['update_options']);

		if (array_key_exists('delete_options', $args))
			$result['delete_options'] = $this->settings->deleteOptions($args['delete_options']);

		$result['status'] = true;

		return $result;
	}

	function process($request) {
		$params = $request->params;
		$account = $this->account;
		$settings = $this->settings;
		switch ($request->method) {
		case "addacc":
			PTNAccount::addAccount($this->settings, $params['public'], $params['secret']);
			$resp = array("status" => PTNAccount::exists($this->settings, $params['public']));
			break;
		case "rmacc":
			$resp = array("status" => PTNAccount::remove($this->settings, $params['public']));
			break;
		case "updt":
			$account->updateInfo($params);
			$resp = array("status" => PTNAccount::exists($this->settings, $params['pubkey']));
			break;
		case "updtapikey":
			PTNAccount::updateApiPublicKey($this->settings, $params['pubkey']);
			$resp = array("status" => $this->settings->getOption(PTNAccount::$api_public_key));
			break;
		case "rmbvscrt":
			$resp = array("status" => $settings->deleteOption('bvSecretKey'));
			break;
		case "rmbvkeys":
			$resp = array("status" => $settings->deleteOption('bvKeys'));
			break;
		case "rmdefpub":
			$resp = array("status" => $settings->deleteOption('bvDefaultPublic'));
			break;
		case "rmoldbvacc":
			$resp = array("status" => $settings->deleteOption('bvAccounts'));
			break;
		case "fetch":
			$accounts = PTNAccount::allAccounts($this->settings);
			if (!isset($params['full'])) {
				foreach ($accounts as &$account) {
					if (isset($account['secret'])) {
						unset($account['secret']);
					}
				}
			}
			$resp = array("status" => $accounts);
			break;
		case "updtinfo":
			$resp = $this->updateInfo($params);
			break;
		default:
			$resp = false;
		}
		return $resp;
	}
}
endif;