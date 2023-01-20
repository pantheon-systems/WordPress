<?php

class BlocksyNewsletterManager {
	static public function get_for_settings() {
		$m = new BlocksyNewsletterManager();
		$settings = $m->get_settings();

		return BlocksyNewsletterManager::get_for_provider(
			$settings['provider']
		);
	}

	static public function get_for_provider($provider) {
		if ($provider === 'mailchimp') {
			return new BlocksyMailchimpManager();
		}

		return new BlocksyMailerliteManager();
	}

	public function fetch_lists($api_key) {
		return [];
	}

	public function get_settings() {
		$option = get_option('blocksy_ext_mailchimp_credentials', []);

		if (empty($option)) {
			$option = [];
		}

		return array_merge([
			'provider' => 'mailchimp',
			'api_key' => null,
			'list_id' => null
		], $option);
	}

	public function set_settings($vals) {
		update_option('blocksy_ext_mailchimp_credentials', array_merge([
			'provider' => 'mailchimp',
			'api_key' => null,
			'list_id' => null
		], $vals));
	}

	public function can($capability = 'manage_options') {
		if (is_multisite()) {
			// Only network admin can change files that affects the entire network.
			$can = current_user_can_for_blog( get_current_blog_id(), $capability );
		} else {
			$can = current_user_can( $capability );
		}

		if ($can) {
			// Also you can use this method to get the capability.
			$can = $capability;
		}

		return $can;
	}
}

