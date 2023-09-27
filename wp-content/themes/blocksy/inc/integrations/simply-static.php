<?php

add_action(
	'ss_after_extract_and_replace_urls_in_html',
	function ($dom, $url_extractor) {
		$blocksy_scripts = $dom->find('script[id="ct-scripts-js-extra"]');

		foreach ($blocksy_scripts as $single_script) {
			$content = $single_script->innertext;

			$all_components = explode('};', $content);

			$ct_localizations = str_replace(
				'var ct_localizations = ', '',
				array_shift($all_components)
			) . '}';

			$decoded = json_decode($ct_localizations, true);

			$decoded['ajax_url'] = $url_extractor->add_to_extracted_urls(
				$decoded['ajax_url']
			);

			$decoded['public_url'] = $url_extractor->add_to_extracted_urls(
				$decoded['public_url']
			);

			$decoded['rest_url'] = $url_extractor->add_to_extracted_urls(
				$decoded['rest_url']
			);

			$decoded['dynamic_styles']['lazy_load'] = $url_extractor->add_to_extracted_urls(
				$decoded['dynamic_styles']['lazy_load']
			);

			$decoded['dynamic_styles']['search_lazy'] = $url_extractor->add_to_extracted_urls(
				$decoded['dynamic_styles']['search_lazy']
			);

			foreach ($decoded['dynamic_js_chunks'] as $index => $single_chunk) {
				$decoded['dynamic_js_chunks'][$index]['url'] = $url_extractor
					->add_to_extracted_urls(
						$decoded['dynamic_js_chunks'][$index]['url']
					);
			}

			foreach ($decoded['dynamic_styles_selectors'] as $index => $single_chunk) {
				$decoded['dynamic_styles_selectors'][$index]['url'] = $url_extractor
					->add_to_extracted_urls(
						$decoded['dynamic_styles_selectors'][$index]['url']
					);
			}

			$decoded['dynamic_styles_selectors'][0]['url'] = $url_extractor
				->add_to_extracted_urls(
					$decoded['dynamic_styles_selectors'][0]['url']
				);

			$result = 'var ct_localizations = ' . json_encode($decoded) . ';' .  implode(
				'};',
				$all_components
			);

			$single_script->innertext = $result;
		}
	},
	10, 2
);

add_action(
	'ss_after_setup_task',
	function () {
		\Simply_Static\Setup_Task::add_additional_files_to_db(
			get_template_directory() . '/static/bundle'
		);
	}
);

