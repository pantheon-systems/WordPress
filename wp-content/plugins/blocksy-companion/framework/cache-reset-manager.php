<?php

namespace Blocksy;

class CacheResetManager {
	public function __construct() {
		add_action(
			'blocksy:cache-manager:purge-all',
			function () {
				$this->run_cache_purge();
			}
		);

		add_action(
			'upgrader_process_complete',
			[$this, 'handle_update'],
			10, 2
		);
	}

	public function handle_update($upgrader, $options) {
		if ($options['action'] !== 'update') {
			return;
		}

		if (
			$options['type'] === 'theme'
			&&
			isset($options['themes'])
			&&
			$options['themes']
		) {
			if (in_array('blocksy', $options['themes'])) {
				$this->run_cache_purge();
				do_action('blocksy:dynamic-css:refresh-caches');
			}
		}

		if ($options['type'] === 'plugin') {
			$plugins = [];

			if (isset($options['plugins']) && is_array($options['plugins'])) {
				$plugins = $options['plugins'];
			}

			if (in_array(BLOCKSY_PLUGIN_BASE, $plugins)) {
				$this->run_cache_purge();
				do_action('blocksy:dynamic-css:refresh-caches');
			}
		}
	}

	public function run_cache_purge() {
		# Purge all W3 Total Cache
		if (function_exists('w3tc_pgcache_flush')) {
			w3tc_pgcache_flush();
		}

		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
		}

		# Purge WP Super Cache
		if (function_exists('wp_cache_clear_cache')) {
			wp_cache_clear_cache();
		}

		if (isset($GLOBALS['wp_fastest_cache'])) {
			if (method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
				$GLOBALS['wp_fastest_cache']->deleteCache();
				$GLOBALS['wp_fastest_cache']->deleteCache(true);
			}
		}

		if (function_exists('cachify_flush_cache')) {
			cachify_flush_cache();
		}

		if (class_exists("comet_cache")) {
			\comet_cache::clear();
		}

		if (class_exists("zencache")) {
			\zencache::clear();
		}

		if (class_exists('LiteSpeed_Cache_Tags')) {
			\LiteSpeed_Cache_Tags::add_purge_tag('*');
		}

		if (function_exists('sg_cachepress_purge_cache')) {
			sg_cachepress_purge_cache();
		}

		if (function_exists('sg_cachepress_purge_everything')) {
			sg_cachepress_purge_everything();
		}

		if (class_exists('LiteSpeed_Cache_Purge')) {
			\LiteSpeed_Cache_Purge::purge_all('Clear Cache For Me');
		}

		if (class_exists('WP_Optimize') && defined('WPO_PLUGIN_MAIN_PATH')) {
			ob_start();
			if (! class_exists('WP_Optimize_Cache_Commands')) include_once(WPO_PLUGIN_MAIN_PATH . 'cache/class-cache-commands.php');
			if (! class_exists('WP_Optimize_Minify_Commands')) include_once(WPO_PLUGIN_MAIN_PATH . 'minify/class-wp-optimize-minify-commands.php');
			if (! class_exists('WP_Optimize_Minify_Cache_Functions')) include_once(WPO_PLUGIN_MAIN_PATH . 'minify/class-wp-optimize-minify-cache-functions.php');

			if (class_exists('WP_Optimize_Cache_Commands')) {
				$wpoptimize_cache_commands = new \WP_Optimize_Cache_Commands();
				$wpoptimize_cache_commands->purge_page_cache();
			}

			if (class_exists('WP_Optimize_Minify_Commands')) {
				$wpoptimize_minify_commands = new \WP_Optimize_Minify_Commands();
				$wpoptimize_minify_commands->purge_minify_cache();
			}

			ob_get_clean();
		}

		if (
			class_exists('WPaaS\Plugin')
			&&
			function_exists('fastvelocity_godaddy_request')
		) {
			fastvelocity_godaddy_request('BAN');
		}

		# Purge WP Engine
		if (class_exists("WpeCommon")) {
			if (method_exists('WpeCommon', 'purge_memcached')) {
				\WpeCommon::purge_memcached();
			}

			if (method_exists('WpeCommon', 'clear_maxcdn_cache')) {
				\WpeCommon::clear_maxcdn_cache();
			}

			if (method_exists('WpeCommon', 'purge_varnish_cache')) {
				\WpeCommon::purge_varnish_cache();
			}
		}

		if (function_exists('rocket_clean_domain')) {
			rocket_clean_domain();
		}

		if (function_exists('rocket_clean_minify')) {
			rocket_clean_minify();
		}

		if (function_exists('wp_cache_clean_cache')) {
			global $file_prefix;
			wp_cache_clean_cache( $file_prefix, true );
		}

		if (class_exists('autoptimizeCache')) {
			\autoptimizeCache::clearall();
		}

		if (function_exists('fvm_purge_all')) {
			fvm_purge_all();
		}

		if (function_exists('fastvelocity_purge_others')) {
			fastvelocity_purge_others();
		}

		# wordpress default cache
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}

		# https://wordpress.org/plugins/hummingbird-performance/
		do_action('wphb_clear_page_cache');

		if (class_exists('Swift_Performance_Cache')) {
			\Swift_Performance_Cache::clear_all_cache();
		}

		if (class_exists('ShortPixelAI')) {
			\ShortPixelAI::clear_css_cache();
		}
	}
}

