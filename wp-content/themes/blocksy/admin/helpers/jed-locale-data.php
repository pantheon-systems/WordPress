<?php

function blocksy_get_json_translation_files($domain) {
	$cached_mofiles = [];

	$locations = [
		WP_LANG_DIR . '/themes'
	];

	foreach ($locations as $location) {
		$mofiles = glob($location . '/*.json');

		if (! $mofiles) {
			continue;
		}

		$cached_mofiles = array_merge($cached_mofiles, $mofiles);
	}

	$locale = determine_locale();

	$result = [];

	foreach ($cached_mofiles as $single_file) {
		if (strpos($single_file, $locale) === false) {
			continue;
		}

		$result[] = $single_file;
	}

	return $result;
}

if (! function_exists('blocksy_get_jed_locale_data')) {
	function blocksy_get_jed_locale_data($domain) {
		static $locale = [];

		if (isset($locale[$domain])) {
			return $locale[$domain];
		}

		$translations = get_translations_for_domain($domain);

		$locale[$domain] = [
			'' => [
				'domain' => $domain,
				'lang' => is_admin() ? get_user_locale() : get_locale(),
			]
		];

		if (! empty($translations->headers['Plural-Forms'])) {
			$locale[$domain]['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ($translations->entries as $msgid => $entry) {
			$locale[$domain][$msgid] = $entry->translations;
		}

		foreach (blocksy_get_json_translation_files('blocksy') as $file_path) {
			$parsed_json = json_decode(
				call_user_func(
					'file' . '_get_contents',
					$file_path
				),
				true
			);

			if (
				! $parsed_json
				||
				! isset($parsed_json['locale_data']['messages'])
			) {
				continue;
			}

			foreach ($parsed_json['locale_data']['messages'] as $msgid => $entry) {
				if (empty($msgid)) {
					continue;
				}

				$locale[$domain][$msgid] = $entry;
			}
		}

		return $locale[$domain];
	}
}
