<?php

defined('ABSPATH') || die("Don't run this file directly!");

add_filter(
	'excerpt_length',
	function ($length) {
		return 100;
	}
);

if (! function_exists('blocksy_trim_excerpt')) {
	function blocksy_trim_excerpt($excerpt, $length) {
		$text = $excerpt;

		if ($length !== 'original') {
			$raw_chars_count = strlen(utf8_decode(trim($excerpt)));
			$multibyte_chars_count = strlen(trim($excerpt)) - $raw_chars_count;

			if ($raw_chars_count > 0) {
				$match_result = [];

				preg_match(
					'/^[\p{Latin}\p{Common}\p{Greek}\p{Cyrillic}\p{Georgian}\p{Old_Turkic}]+$/u',
					$excerpt,
					$match_result
				);

				$percentage_of_multibyte_chars = 100 * $multibyte_chars_count / $raw_chars_count;

				if (
					empty($match_result)
					&&
					$percentage_of_multibyte_chars > 30
					&&
					function_exists('mb_strimwidth')
				) {
					$text = mb_strimwidth($excerpt, 0, $length, '…');
				} else {
					$text = wp_trim_words($excerpt, $length, '…');
				}
			}
		}

		foreach (wp_extract_urls($text) as $url) {
			$text = str_replace($url, '', $text);
		}

		$text = apply_filters('blocksy:excerpt:output', $text);

		echo apply_filters('the_excerpt', $text);
	}
}

add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);

