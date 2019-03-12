<?php

class WCML_Multi_Currency_Configuration
{

	/**
	 * @var WCML_Multi_Currency
	 */
	private static $multi_currency;
	/**
	 * @var woocommerce_wpml
	 */
	private static $woocommerce_wpml;

	public static function set_up(WCML_Multi_Currency $multi_currency, woocommerce_wpml $woocommerce_wpml)
	{

		self::$multi_currency = $multi_currency;
		self::$woocommerce_wpml = $woocommerce_wpml;

		if (isset($_POST['action']) && $_POST['action'] == 'save-mc-options') {
			self::save_configuration();
		}

		self::set_prices_config();

		if (is_ajax()) {

			add_action('wp_ajax_legacy_update_custom_rates', array(__CLASS__, 'legacy_update_custom_rates'));
			add_action('wp_ajax_legacy_remove_custom_rates', array(__CLASS__, 'legacy_remove_custom_rates'));

			add_action('wp_ajax_wcml_new_currency', array(__CLASS__, 'edit_currency'));
			add_action('wp_ajax_wcml_save_currency', array(__CLASS__, 'save_currency'));
			add_action('wp_ajax_wcml_delete_currency', array(__CLASS__, 'delete_currency'));

			add_action('wp_ajax_wcml_update_currency_lang', array(__CLASS__, 'update_currency_lang'));
			add_action('wp_ajax_wcml_update_default_currency', array(__CLASS__, 'update_default_currency_ajax'));

		}

	}

	public static function save_configuration()
	{

		if (check_admin_referer('wcml_mc_options', 'wcml_nonce')) {

			$wcml_settings =& self::$woocommerce_wpml->settings;

			$wcml_settings['enable_multi_currency'] = isset($_POST['multi_currency']) ? intval($_POST['multi_currency']) : 0;
			$wcml_settings['display_custom_prices'] = isset($_POST['display_custom_prices']) ? intval($_POST['display_custom_prices']) : 0;

			//update default currency settings
			if ($wcml_settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT) {

				$options = array(
					'woocommerce_currency_pos' => 'position',
					'woocommerce_price_thousand_sep' => 'thousand_sep',
					'woocommerce_price_decimal_sep' => 'decimal_sep',
					'woocommerce_price_num_decimals' => 'num_decimals'
				);

				$woocommerce_currency = get_option('woocommerce_currency', true);

				foreach ($options as $wc_key => $key) {
					$wcml_settings['currency_options'][$woocommerce_currency][$key] = get_option($wc_key, true);
				}
			}

			$wcml_settings['currency_switcher_product_visibility'] = isset($_POST['currency_switcher_product_visibility']) ? intval($_POST['currency_switcher_product_visibility']) : 0;
			$wcml_settings['currency_switcher_additional_css'] = isset($_POST['currency_switcher_additional_css']) ? sanitize_text_field($_POST['currency_switcher_additional_css']) : '';

			self::$woocommerce_wpml->update_settings($wcml_settings);

			do_action('wcml_saved_mc_options', $_POST);

			$message = array(
				'id' => 'wcml-settings-saved',
				'text' => __('Your settings have been saved.', 'woocommerce-multilingual'),
				'group' => 'wcml-multi-currency',
				'admin_notice' => true,
				'limit_to_page' => true,
				'classes' => array('updated', 'notice', 'notice-success'),
				'show_once' => true
			);
			ICL_AdminNotifier::add_message($message);

			$wpml_admin_notices = wpml_get_admin_notices();
			$wpml_admin_notices->remove_notice('wcml-save-multi-currency-options', 'wcml-fixerio-api-key-required');
		}

	}

	public static function edit_currency()
	{

		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (wp_verify_nonce($nonce, 'wcml_edit_currency')) {

			self::$multi_currency->init_currencies();
			$args['currencies'] = self::$multi_currency->currencies;
			$args['wc_currencies'] = get_woocommerce_currencies();

			if (empty($_POST['currency'])) {

				$args['title'] = empty($_POST['currency']) ? __('Add new currency', 'woocommerce-multilingual') : __('Update currency', 'woocommerce-multilingual');

			} else {

				$args['currency_code'] = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$args['currency_name'] = $args['wc_currencies'][$args['currency_code']];
				$args['currency_symbol'] = get_woocommerce_currency_symbol($args['currency_code']);

				$args['title'] = sprintf(__('Update settings for %s', 'woocommerce-multilingual'),
					'<strong>' . $args['currency_name'] . ' (' . $args['currency_symbol'] . ')</strong>');

			}

			$custom_currency_options = new WCML_Custom_Currency_Options($args, self::$woocommerce_wpml);
			$return['html'] = $custom_currency_options->get_view();

			echo json_encode($return);

		}

		exit;
	}

	public static function add_currency($currency_code)
	{
		global $sitepress;

		$settings = self::$woocommerce_wpml->get_settings();

		$active_languages = $sitepress->get_active_languages();
		$return['languages'] = '';
		foreach ($active_languages as $language) {
			if (!isset($settings['currency_options'][$currency_code]['languages'][$language['code']])) {
				$settings['currency_options'][$currency_code]['languages'][$language['code']] = 1;
			}
		}
		$settings['currency_options'][$currency_code]['rate'] = (double)filter_input(INPUT_POST, 'currency_value', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$settings['currency_options'][$currency_code]['updated'] = date('Y-m-d H:i:s');

		$wc_currency = get_option('woocommerce_currency');
		if (!isset($settings['currencies_order']))
			$settings['currencies_order'][] = $wc_currency;

		$settings['currencies_order'][] = $currency_code;

		self::$woocommerce_wpml->update_settings($settings);
		self::$multi_currency->init_currencies();

	}

	public static function save_currency()
	{
		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (!$nonce || !wp_verify_nonce($nonce, 'save_currency')) {
			die('Invalid nonce');
		}

		$options = $_POST['currency_options'];

		$options['thousand_sep'] = wc_format_option_price_separators(null, null, $options['thousand_sep']);
		$options['decimal_sep'] = wc_format_option_price_separators(null, null, $options['decimal_sep']);

		$currency_code = $options['code'];

		if (!isset(self::$multi_currency->currencies[$currency_code])) {
			self::add_currency($currency_code);
		}

		$changed = false;
		$rate_changed = false;
		foreach (self::$multi_currency->currencies[$currency_code] as $key => $value) {

			if (isset($options[$key]) && $options[$key] != $value) {
				if ($key == 'rate') {
					$previous_rate = self::$multi_currency->currencies[$currency_code][$key];
					$rate_changed = true;
				}
				self::$multi_currency->currencies[$currency_code][$key] = $options[$key];
				$changed = true;
			}

		}

		if ($changed) {
			if ($rate_changed) {
				self::$multi_currency->currencies[$currency_code]['previous_rate'] = $previous_rate;
				self::$multi_currency->currencies[$currency_code]['updated'] = date('Y-m-d H:i:s');
			}
			self::$woocommerce_wpml->settings['currency_options'] = self::$multi_currency->currencies;
			self::$woocommerce_wpml->update_settings();
		}

		$wc_currency = get_option('woocommerce_currency');
		$wc_currencies = get_woocommerce_currencies();

		switch (self::$multi_currency->currencies[$currency_code]['position']) {
			case 'left':
				$price = sprintf('%s99.99', get_woocommerce_currency_symbol($currency_code));
				break;
			case 'right':
				$price = sprintf('99.99%s', get_woocommerce_currency_symbol($currency_code));
				break;
			case 'left_space':
				$price = sprintf('%s 99.99', get_woocommerce_currency_symbol($currency_code));
				break;
			case 'right_space':
				$price = sprintf('99.99 %s', get_woocommerce_currency_symbol($currency_code));
				break;
		}

		$return['currency_name_formatted'] = sprintf('<span class="truncate">%s</span> <small>(%s)</small>', $wc_currencies[$currency_code], $price);

		$return['currency_meta_info'] = sprintf('1 %s = %s %s', $wc_currency, self::$multi_currency->currencies[$currency_code]['rate'], $currency_code);

		$args = array();
		$args['default_currency'] = get_woocommerce_currency();
		$args['currencies'] = self::$multi_currency->currencies;
		$args['wc_currencies'] = $wc_currencies;
		$args['currency_code'] = $currency_code;
		$args['currency_name'] = $wc_currencies[$currency_code];
		$args['currency_symbol'] = get_woocommerce_currency_symbol($currency_code);
		$args['currency'] = self::$multi_currency->currencies[$currency_code];
		$args['title'] = sprintf(__('Update settings for %s', 'woocommerce-multilingual'), $args['currency_name'] . ' (' . $args['currency_symbol'] . ')');

		$custom_currency_options = new WCML_Custom_Currency_Options($args, self::$woocommerce_wpml);
		$return['currency_options'] = $custom_currency_options->get_view();
		$return['currency_name'] = $wc_currencies[$currency_code];
		$return['currency_symbol'] = get_woocommerce_currency_symbol($currency_code);

		echo json_encode($return);
		exit;
	}

	public static function delete_currency()
	{
		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (!$nonce || !wp_verify_nonce($nonce, 'wcml_delete_currency')) {
			die('Invalid nonce');
		}

		self::$multi_currency->delete_currency_by_code($_POST['code']);

		exit;
	}

	public static function update_currency_lang()
	{
		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (!$nonce || !wp_verify_nonce($nonce, 'wcml_update_currency_lang')) {
			die('Invalid nonce');
		}

		$settings = self::$woocommerce_wpml->get_settings();
		$settings['currency_options'][$_POST['code']]['languages'][$_POST['lang']] = $_POST['value'];

		self::$woocommerce_wpml->update_settings($settings);
		exit;
	}

	public static function update_default_currency_ajax()
	{


		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (!$nonce || !wp_verify_nonce($nonce, 'wcml_update_default_currency')) {
			die('Invalid nonce');
		}

		self::update_default_currency();

		exit;
	}

	public static function update_default_currency()
	{
		global $woocommerce;

		if (!empty($woocommerce->session) &&
			$_POST['lang'] == $woocommerce->session->get('client_currency_language')) {
			$woocommerce->session->set('client_currency', $_POST['code']);
		}

		self::$woocommerce_wpml->settings['default_currencies'][$_POST['lang']] = $_POST['code'];
		self::$woocommerce_wpml->update_settings();

	}

	public static function currency_options_update_default_currency($settings, $current_currency, $new_currency)
	{

		//When the default WooCommerce currency is updated, if it existed as a secondary currency, remove it
		if (isset($settings['currency_options'][$current_currency])) {
			$currency_settings = $settings['currency_options'][$current_currency];
			$settings['currency_options'][$new_currency] = $currency_settings;
			$settings = self::$woocommerce_wpml->multi_currency->delete_currency_by_code($current_currency, $settings, false);
		}

		$message_id = 'wcml-woocommerce-default-currency-changed';
		$message_args = array(
			'id' => $message_id,
			'text' => sprintf(__('The default currency was changed. In order to show accurate prices in all currencies, you need to update the exchange rates under the %sMulti-currency%s configuration.',
				'woocommerce-multilingual'), '<a href="' . admin_url('admin.php?page=wpml-wcml&tab=multi-currency') . '">', '</a>'),
			'type' => 'warning',
			'group' => 'wcml-multi-currency',
			'admin_notice' => true,
			'hide' => true
		);

		ICL_AdminNotifier::remove_message($message_id); // clear any previous instances
		ICL_AdminNotifier::add_message($message_args);

		return $settings;
	}

	public static function legacy_update_custom_rates()
	{

		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (!$nonce || !wp_verify_nonce($nonce, 'legacy_update_custom_rates')) {
			die('Invalid nonce');
		}
		foreach ($_POST['posts'] as $post_id => $rates) {
			update_post_meta($post_id, '_custom_conversion_rate', $rates);
		}

		echo json_encode(array());
		exit;
	}

	public static function legacy_remove_custom_rates()
	{

		$nonce = filter_input(INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (!$nonce || !wp_verify_nonce($nonce, 'legacy_remove_custom_rates')) {
			echo json_encode(array('error' => __('Invalid nonce', 'woocommerce-multilingual')));
			die();
		}

		delete_post_meta($_POST['post_id'], '_custom_conversion_rate');
		echo json_encode(array());

		exit;
	}

	public static function set_prices_config()
	{
		global $iclTranslationManagement, $sitepress_settings, $sitepress;

		$wpml_settings = $sitepress->get_settings();

		if (!isset ($wpml_settings['translation-management']) ||
			!isset($iclTranslationManagement) ||
			!($iclTranslationManagement instanceof TranslationManagement)) {
			return;
		}

		$keys = array(
			'_regular_price',
			'_sale_price',
			'_price',
			'_min_variation_regular_price',
			'_min_variation_sale_price',
			'_min_variation_price',
			'_max_variation_regular_price',
			'_max_variation_sale_price',
			'_max_variation_price',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'_wcml_schedule'
		);
		$save = false;

		foreach ($keys as $key) {
			$iclTranslationManagement->settings['custom_fields_readonly_config'][] = $key;
			if (!isset($sitepress_settings['translation-management']['custom_fields_translation'][$key]) ||
				$wpml_settings['translation-management']['custom_fields_translation'][$key] != WPML_COPY_CUSTOM_FIELD) {
				$wpml_settings['translation-management']['custom_fields_translation'][$key] = WPML_COPY_CUSTOM_FIELD;
				$save = true;
			}

			if (!empty(self::$multi_currency)) {
				foreach (self::$multi_currency->get_currency_codes() as $code) {
					$new_key = $key . '_' . $code;
					$iclTranslationManagement->settings['custom_fields_readonly_config'][] = $new_key;

					if (!isset($sitepress_settings['translation-management']['custom_fields_translation'][$new_key]) ||
						$wpml_settings['translation-management']['custom_fields_translation'][$new_key] != WPML_IGNORE_CUSTOM_FIELD) {
						$wpml_settings['translation-management']['custom_fields_translation'][$new_key] = WPML_IGNORE_CUSTOM_FIELD;
						$save = true;
					}
				}
			}
		}

		if ($save) {
			$sitepress->save_settings($wpml_settings);
		}
	}

}