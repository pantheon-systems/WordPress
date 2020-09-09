<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('PTNWPSiteInfo')) :

class PTNWPSiteInfo {
	public function wpurl() {
		if (function_exists('network_site_url'))
			return network_site_url();
		else
			return get_bloginfo('wpurl');
	}

	public function siteurl() {
		if (function_exists('site_url')) {
			return site_url();
		} else {
			return get_bloginfo('wpurl');
		}
	}

	public function homeurl() {
		if (function_exists('home_url')) {
			return home_url();
		} else {
			return get_bloginfo('url');
		}
	}

	public function isMultisite() {
		if (function_exists('is_multisite'))
			return is_multisite();
		return false;
	}

	public function isMainSite() {
		if (!function_exists('is_main_site' ) || !$this->isMultisite())
			return true;
		return is_main_site();
	}

	public function info() {
		$info = array();
		$this->basic($info);
		$info['dbsig'] = $this->dbsig(false);
		$info["serversig"] = $this->serversig(false);
		return $info;
	}

	public function basic(&$info) {
		$info['wpurl'] = $this->wpurl();
		$info['siteurl'] = $this->siteurl();
		$info['homeurl'] = $this->homeurl();
		if (array_key_exists('SERVER_ADDR', $_SERVER)) {
			$info['serverip'] = $_SERVER['SERVER_ADDR'];
		}
		$info['abspath'] = ABSPATH;
	}

	public function serversig($full = false) {
		$sig_param = ABSPATH;
		if (array_key_exists('SERVER_ADDR', $_SERVER)) {
			$sig_param = $_SERVER['SERVER_ADDR'].ABSPATH;
		}
		$sig = sha1($sig_param);
		if ($full)
			return $sig;
		else
			return substr($sig, 0, 6);
	}

	public function dbsig($full = false) {
		if (defined('DB_USER') && defined('DB_NAME') &&
			defined('DB_PASSWORD') && defined('DB_HOST')) {
			$sig = sha1(DB_USER.DB_NAME.DB_PASSWORD.DB_HOST);
		} else {
			$sig = "bvnone".PTNAccount::randString(34);
		}
		if ($full)
			return $sig;
		else
			return substr($sig, 0, 6);
	}

	public static function isCWServer() {
		return isset($_SERVER['cw_allowed_ip']);
	}
}
endif;