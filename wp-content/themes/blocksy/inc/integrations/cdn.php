<?php

if (! function_exists('blocksy_cdn_url')) {
	function blocksy_cdn_url($url) {
		if (class_exists('BunnyCDN')) {
			$bunnyCdnOptions = BunnyCDN::getOptions();

			$url = str_replace(
				$bunnyCdnOptions["site_url"],
				(
					is_ssl() ? 'https://' : 'http://'
				) . $bunnyCdnOptions["cdn_domain_name"],
				$url
			);
		}

		if (function_exists('get_rocket_cdn_url')) {
			$url = get_rocket_cdn_url($url);
		}

		return apply_filters('blocksy:frontend:static-assets:cdn', $url);
	}
}
