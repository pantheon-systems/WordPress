<?php

class Blocksy_Translations_Manager {
	public function get_all_translation_keys() {
		$builder_keys = Blocksy_Manager::instance()->builder->translation_keys();

		foreach (['blog', 'categories', 'search', 'author'] as $prefix) {
			$archive_order = get_theme_mod($prefix . '_archive_order', null);

			if (! $archive_order) {
				continue;
			}

			foreach ($archive_order as $single_archive_component) {
				if ($single_archive_component['id'] !== 'read_more') {
					continue;
				}

				if (blocksy_akg('read_more_text', $single_archive_component)) {
					$builder_keys[] = [
						'key' => $prefix . '_archive_read_more_text',
						'value' => blocksy_akg(
							'read_more_text',
							$single_archive_component
						)
					];
				}
			}
		}

		foreach (['blog', 'single_blog_post', 'single_page'] as $prefix) {
			$hero_elements = get_theme_mod($prefix . '_hero_elements', null);

			if (! $hero_elements) {
				continue;
			}

			foreach ($hero_elements as $single_hero_component) {
				if (
					$single_hero_component['id'] === 'custom_meta'
					&&
					is_array($single_hero_component['meta_elements'])
				) {
					foreach ($single_hero_component['meta_elements'] as $single_meta_element) {
						if (empty($single_meta_element['label'])) {
							continue;
						}

						$builder_keys[] = [
							'key' => $prefix . '_hero_meta_' . $single_meta_element['id'] . '_label',
							'value' => $single_meta_element['label']
						];
					}
				}

				if (
					$single_hero_component['id'] === 'custom_title'
					&&
					blocksy_akg('title', $single_hero_component)
				) {
					$builder_keys[] = [
						'key' => $prefix . '_hero_custom_title',
						'value' => blocksy_akg('title', $single_hero_component)
					];
				}

				if (
					$single_hero_component['id'] === 'custom_description'
					&&
					blocksy_akg('description', $single_hero_component)
				) {
					$builder_keys[] = [
						'key' => $prefix . '_hero_custom_description',
						'value' => blocksy_akg('description', $single_hero_component)
					];
				}
			}
		}

		return apply_filters(
			'blocksy:translations-manager:all-translation-keys',
			$builder_keys
		);
	}

	public function register_translation_keys() {
		if (!function_exists('pll_register_string')) {
			return;
		}

		$builder_keys = $this->get_all_translation_keys();

		foreach ($builder_keys as $single_key) {
			pll_register_string(
				$single_key['key'],
				$single_key['value'],
				'Blocksy',
				isset($single_key['multiline']) ? $single_key['multiline'] : false
			);
		}
	}

	public function register_wpml_translation_keys() {
		if (! function_exists('icl_object_id')) {
			return;
		}

		$builder_keys = $this->get_all_translation_keys();

		foreach ($builder_keys as $single_key) {
			do_action(
				'wpml_register_single_string',
				'Blocksy',
				$single_key['key'],
				$single_key['value']
			);
		}
	}
}

if (! function_exists('blocksy_get_all_i18n_languages')) {
	function blocksy_get_all_i18n_languages() {
		$result = [];

		if (function_exists('pll_languages_list')) {
			$locales = pll_languages_list(['fields' => '']);

			foreach ($locales as $locale) {
				$result[] = [
					'id' => $locale->locale,
					'name' => $locale->name
				];
			}
		}

		if (
			! function_exists('pll_languages_list')
			&&
			function_exists('icl_get_languages')
		) {
			$locales = icl_get_languages();

			foreach ($locales as $locale_key => $locale) {
				$result[] = [
					'id' => $locale['default_locale'],
					'name' => $locale['native_name']
				];
			}
		}

		if (class_exists('TRP_Translate_Press')) {
			$settings = new TRP_Settings();
			$settings_array = $settings->get_settings();

			$trp = TRP_Translate_Press::get_trp_instance();

			$trp_languages = $trp->get_component('languages');

			if (current_user_can(apply_filters(
				'trp_translating_capability',
				'manage_options'
			))) {
				$languages_to_display = $settings_array['translation-languages'];
			} else {
				$languages_to_display = $settings_array['publish-languages'];
			}

			$languages_info = $trp_languages->get_language_names(
				$languages_to_display
			);

			foreach ($languages_to_display as $code) {
				$result[] = [
					'id' => $code,
					'name' => $languages_info[$code]
				];
			}
		}

		if (function_exists('weglot_get_current_language')) {
			$languages_available = array_values((array)weglot_get_languages_available())[0];
			$original_language = weglot_get_original_language();
			$destination_languages = array_map(function ($object) {
				return $object['language_to'];
			}, weglot_get_destination_languages());
			$languages_to_display = array_merge(array($original_language), $destination_languages);

			foreach ($languages_to_display as $code) {
				$result[] = [
					'id' => $languages_available[$code]->getExternalCode(),
					'name' => $languages_available[$code]->getLocalName()
				];
			}
		}

		return $result;
	}
}

if (! function_exists('blocksy_get_current_language')) {
	function blocksy_get_current_language($format = 'locale') {
		if ($format === 'slug') {
			if (function_exists('pll_current_language')) {
				return pll_current_language();
			}

			return '__NOT_KNOWN__';
		}

		if (function_exists('pll_current_language')) {
			return pll_current_language('locale');
		}

		if (
			function_exists('icl_get_languages')
			&&
			defined('ICL_LANGUAGE_CODE')
			&&
			isset(icl_get_languages()[ICL_LANGUAGE_CODE])
		) {
			return icl_get_languages()[ICL_LANGUAGE_CODE]['default_locale'];
		}

		global $TRP_LANGUAGE;

		if (
			class_exists('TRP_Translate_Press')
			&&
			isset($TRP_LANGUAGE)
		) {
			return $TRP_LANGUAGE;
		}

		if (function_exists('weglot_get_current_language')) {
			return weglot_get_current_language();
		}

		return '__NOT_KNOWN__';
	}
}

if (! function_exists('blocksy_translate_dynamic')) {
	function blocksy_translate_dynamic($text, $key = null) {
		if (function_exists('pll__')) {
			return pll__($text); // PHPCS:ignore WordPress.WP.I18n
		}

		if ($key) {
			return apply_filters(
				'wpml_translate_single_string',
				$text,
				'Blocksy',
				$key
			);
		}

		return $text;
	}
}
