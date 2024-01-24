<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVMiscCallback')) :
	
class BVMiscCallback extends BVCallbackBase {
	public $settings;
	public $bvinfo;
	public $siteinfo;
	public $account;
	public $bvapi;
	public $db;

	const MISC_WING_VERSION = 1.2;

	public function __construct($callback_handler) {
		$this->settings = $callback_handler->settings;
		$this->siteinfo = $callback_handler->siteinfo;
		$this->account = $callback_handler->account;
		$this->db = $callback_handler->db;
		$this->bvinfo = new PTNInfo($callback_handler->settings);
		$this->bvapi = new PTNWPAPI($callback_handler->settings);
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

	public function getWingInfo() {
		return array('wing_info' => self::$wing_infos);
	}

	public function post_types_data($post_params) {
		$result = array();
		$get_post_types_args = $post_params['get_post_types_args'];
		$post_types = get_post_types($get_post_types_args);
		$post_types = array_merge($post_types, $post_params['include_post_types']);
		$post_types = array_diff( $post_types, $post_params['exclude_post_types']);
		$result['post_types'] = $post_types;
		$post_types = esc_sql($post_types);
		$post_types = "'" . implode("','", $post_types) . "'";
		$post_table = $post_params['table'];
		$post_select_columns = implode(", ", $post_params['select_column']);
		$post_query = "SELECT MAX(ID) as $post_select_columns FROM ( SELECT
			$post_select_columns FROM $post_table WHERE post_type IN ( $post_types )
			AND post_status='publish' ORDER BY post_date DESC ) AS posts GROUP BY post_type";
		$posts = $this->db->getResult($post_query);
		foreach ( $posts as $key => $post ) {
			$posts[$key]['url'] = get_permalink($post['ID']);
		}
		$result['posts'] = $posts;
		return $result;
	}

	public function taxonomy_data($taxonomy_params) {
		$result = array();
		$get_taxonomies_args = $taxonomy_params['get_taxonomies_args'];
		$taxonomies = get_taxonomies($get_taxonomies_args);
		$taxonomies = array_diff($taxonomies, $taxonomy_params['exclude_taxonomies']);
		$result['taxonomies'] = $taxonomies;
		$taxonomies = esc_sql( $taxonomies );
		$taxonomies = "'" . implode( "','", $taxonomies ) . "'";
		$taxonomy_table = $taxonomy_params['table'];
		$taxonomy_select_columns = implode(", ", $taxonomy_params['select_column']);
		$taxonomy_query = "SELECT MAX( term_id ) AS $taxonomy_select_columns FROM (
			SELECT $taxonomy_select_columns FROM $taxonomy_table WHERE taxonomy IN (
				$taxonomies ) AND count > 0) AS taxonomies GROUP BY taxonomy";

		$taxonomies = $this->db->getResult($taxonomy_query);
		foreach($taxonomies as $key => $taxonomy) {
			$taxonomies[$key]['url'] = get_term_link((int)$taxonomy['term_id'], $taxonomy['taxonomy']);
		}
		$result['taxonomy_data'] = $taxonomies;
		return $result;
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
			$resp = array_merge($resp, $this->getWingInfo());
			break;
		case "pngbv":
			$info = array();
			$this->siteinfo->basic($info);
			$this->bvapi->pingbv('/bvapi/pingbv', $info);
			$resp = array("status" => true);
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
		case "wpnonce":
			$resp = array("wpnonce" => wp_create_nonce($params["wpnonce_action"]));
			break;
		case "dlttrsnt":
			$resp = array("dlttrsnt" => $settings->deleteTransient($params['key']));
			break;
		case "optns":
			$resp = array();

			if (array_key_exists("get_options", $params))
				$resp["get_options"] = $settings->getOptions($params["get_options"]);

			if (array_key_exists("update_options", $params))
				$resp["update_options"] = $settings->updateOptions($params["update_options"]);

			if (array_key_exists("delete_options", $params))
				$resp["delete_options"] = $settings->deleteOptions($params["delete_options"]);

			break;
		case "setbvss":
			$resp = array("status" => $settings->updateOption('bv_site_settings', $params['bv_site_settings']));
			break;
		case "stsrvcs":
			$resp = array();
			$deleted_configs = array();
			$updated_configs = array();
			if (array_key_exists("configs_to_delete", $params)) {
				foreach($params["configs_to_delete"] as $config_name) {
					$deleted_configs[$config_name] = $settings->deleteOption($config_name);
				}
			}
			if (array_key_exists("configs_to_update", $params)) {
				foreach($params["configs_to_update"] as $config_name => $config_value) {
					$settings->updateOption($config_name, $config_value);
					$updated_configs[$config_name] = $settings->getOption($config_name);
				}
			}
			$resp["updated_configs"] = $updated_configs;
			$resp["deleted_configs"] = $deleted_configs;
			break;
		case "critical_css_data":
			$resp = array();
			if (array_key_exists('fetch_post_data', $params) && $params['fetch_post_data'] == true) {
				$post_params = $params['post_params'];
				$post_result = $this->post_types_data($post_params);
				$resp['post_cp_results'] = $post_result['posts'];
				$resp['post_types'] = $post_result['post_types'];
			}
			if (array_key_exists('fetch_taxonomy_data', $params) && $params['fetch_taxonomy_data'] == true) {
				$taxonomy_params = $params['taxonomy_params'];
				$taxonomy_result = $this->taxonomy_data($taxonomy_params);
				$resp['taxonomy_cp_results'] = $taxonomy_result['taxonomy_data'];
				$resp['taxonomies'] = $taxonomy_result['taxonomies'];
			}
			break;

		case "get_post_ids":
			if (array_key_exists('urls', $params)) {
				$resp = array();
				foreach ( $params['urls'] as $url ) {
					$resp[$url] = url_to_postid($url);
				}
			}
			break;

		case "permalink":
			if (array_key_exists('post_ids', $params)) {
				$resp = array();
				foreach ( $params['post_ids'] as $id ) {
					$resp[$id]['url'] = get_permalink($id);
				}
			}
			break;
		default:
			$resp = false;
		}
		return $resp;
	}
}
endif;