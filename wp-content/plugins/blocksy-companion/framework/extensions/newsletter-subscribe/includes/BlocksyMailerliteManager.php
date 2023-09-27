<?php

class BlocksyMailerliteManager extends BlocksyNewsletterManager {
	public function fetch_lists($api_key) {
		if (! $api_key) {
			return 'api_key_invalid';
		}

		$response = wp_remote_get(
			'https://api.mailerlite.com/api/v2/groups',
			[
				'headers' => [
					'X-MailerLite-ApiKey' => $api_key
				]
			]
		);

		if (! is_wp_error($response)) {
			if (200 !== wp_remote_retrieve_response_code($response)) {
				return 'api_key_invalid';
			}

			$body = json_decode(wp_remote_retrieve_body($response), true);

			if (! $body) {
				return 'api_key_invalid';
			}

			return array_map(function($list) {
				return [
					'name' => $list['name'],
					'id' => $list['id'],
				];
			}, $body);
		} else {
			return 'api_key_invalid';
		}
	}

	public function get_form_url_and_gdpr_for($maybe_custom_list = null) {
		return [
			'form_url' => '#',
			'has_gdpr_fields' => false,
			'provider' => 'mailerlite'
		];
	}

	public function subscribe_form($args = []) {
		$args = wp_parse_args($args, [
			'email' => '',
			'name' => '',
			'group' => ''
		]);

		$settings = $this->get_settings();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.mailerlite.com/api/v2/groups/" . $args['group'] . "/subscribers",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode([
				'email' => $args['email'],
				'name' => $args['name']
			]),
			CURLOPT_HTTPHEADER => array(
				"content-type: application/json",
				"x-mailerlite-apikey: " . $settings['api_key']
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return [
				'result' => 'no',
				'error' => $err
			];
		} else {
			$response = json_decode($response, true);

			if (isset($response['error'])) {
				return [
					'result' => 'no',
					'message' => $response['error']['message']
				];
			}

			return [
				'result' => 'yes',
				'message' => __('Thank you for subscribing to our newsletter!', 'blocksy-companion')
			];
		}

	}
}
