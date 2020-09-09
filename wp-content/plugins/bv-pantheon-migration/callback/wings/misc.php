<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVMiscCallback')) :
	
class BVMiscCallback extends BVCallbackBase {
	public $settings;
	public $bvinfo;
	public $siteinfo;
	public $account;

	public function __construct($callback_handler) {
		$this->settings = $callback_handler->settings;
		$this->siteinfo = $callback_handler->siteinfo;
		$this->account = $callback_handler->account;
		$this->bvinfo = new PTNInfo($callback_handler->settings);
	}

	public function refreshPluginUpdates() {
		global $wp_current_filter;
		$wp_current_filter[] = 'load-update-core.php';
	
		wp_update_plugins();

		array_pop($wp_current_filter);

		wp_update_plugins();

		return array("wpupdateplugins" => true);
	}

	public function refreshThemeUpdates() {
		global $wp_current_filter;
		$wp_current_filter[] = 'load-update-core.php';

		wp_update_themes();

		array_pop($wp_current_filter);

		wp_update_themes();

		return array("wpupdatethemes" => true);
	}

	public function process($request) {
		$bvinfo = $this->bvinfo;
		$settings = $this->settings;
		$params = $request->params;
		switch ($request->method) {
		case "dummyping":
			$resp = array();
			$resp = array_merge($resp, $this->siteinfo->info());
			$resp = array_merge($resp, $this->account->info());
			$resp = array_merge($resp, $this->bvinfo->info());
			break;
		case "enablebadge":
			$option = $bvinfo->badgeinfo;
			$badgeinfo = array();
			$badgeinfo['badgeurl'] = $params['badgeurl'];
			$badgeinfo['badgeimg'] = $params['badgeimg'];
			$badgeinfo['badgealt'] = $params['badgealt'];
			$settings->updateOption($option, $badgeinfo);
			$resp = array("status" => $settings->getOption($option));
			break;
		case "disablebadge":
			$option = $bvinfo->badgeinfo;
			$settings->deleteOption($option);
			$resp = array("status" => !$settings->getOption($option));
			break;
		case "getoption":
			$resp = array('getoption' => $settings->getOption($params['opkey']));
			break;
		case "setdynplug":
			$settings->updateOption('bvdynplug', $params['dynplug']);
			$resp = array("setdynplug" => $settings->getOption('bvdynplug'));
			break;
		case "unsetdynplug":
			$settings->deleteOption('bvdynplug');
			$resp = array("unsetdynplug" => $settings->getOption('bvdynplug'));
			break;
		case "wpupplgs":
			$resp = $this->refreshPluginUpdates();
			break;
		case "wpupthms":
			$resp = $this->refreshThemeUpdates(); 
			break;
		case "wpupcre":
			$resp = array("wpupdatecore" => wp_version_check());
			break;
		case "phpinfo":
			phpinfo();
			die();
			break;
		case "dlttrsnt":
			$resp = array("dlttrsnt" => $settings->deleteTransient($params['key']));
			break;
		case "setmanulsignup":
			$resp = array("setmanulsignup" => $settings->updateOption("bvmanualsignup", true));
			break;
		default:
			$resp = false;
		}
		return $resp;
	}
}
endif;