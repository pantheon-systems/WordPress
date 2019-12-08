<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVBrandCallback')) :

class BVBrandCallback extends BVCallbackBase {
	public $settings;

	public function __construct($callback_handler) {
		$this->settings = $callback_handler->settings;
	}

	public function process($request) {
		$bvinfo = new PTNInfo($this->settings);
		$option_name = $bvinfo->brand_option;
		$params = $request->params;
		switch($request->method) {
		case 'setbrand':
			$brandinfo = array();
			if (array_key_exists('hide', $params)) {
				$brandinfo['hide'] = $params['hide'];
			} else {
				$brandinfo['name'] = $params['name'];
				$brandinfo['title'] = $params['title'];
				$brandinfo['description'] = $params['description'];
				$brandinfo['pluginuri'] = $params['pluginuri'];
				$brandinfo['author'] = $params['author'];
				$brandinfo['authorname'] = $params['authorname'];
				$brandinfo['authoruri'] = $params['authoruri'];
				$brandinfo['menuname'] = $params['menuname'];
				$brandinfo['logo'] = $params['logo'];
				$brandinfo['webpage'] = $params['webpage'];
				$brandinfo['appurl'] = $params['appurl'];
				if (array_key_exists('hide_plugin_details', $params)) {
					$brandinfo['hide_plugin_details'] = $params['hide_plugin_details'];
				}
				if (array_key_exists('hide_from_menu', $params)) {
					$brandinfo['hide_from_menu'] = $params['hide_from_menu'];
				}
			}
			$this->settings->updateOption($option_name, $brandinfo);
			$resp = array("setbrand" => $this->settings->getOption($option_name));
			break;
		case 'rmbrand':
			$this->settings->deleteOption($option_name);
			$resp = array("rmbrand" => !$this->settings->getOption($option_name));
			break;
		default:
			$resp = false;
		}
		return $resp;
	}
}
endif;