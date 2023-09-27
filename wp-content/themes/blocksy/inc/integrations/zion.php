<?php

add_action('init', function () {
	add_filter(
		'rest_request_after_callbacks',
		function ($response, $handler, \WP_REST_Request $request) {
			$route = $request->get_route();

			if ($route === '/zionbuilder/v1/options') {
				$data = $response->get_data();

				$data['local_colors'][] = 'var(--paletteColor1)';
				$data['local_colors'][] = 'var(--paletteColor2)';
				$data['local_colors'][] = 'var(--paletteColor3)';
				$data['local_colors'][] = 'var(--paletteColor4)';
				$data['local_colors'][] = 'var(--paletteColor5)';
				$data['local_colors'][] = 'var(--paletteColor6)';
				$data['local_colors'][] = 'var(--paletteColor7)';
				$data['local_colors'][] = 'var(--paletteColor8)';

				$response->set_data($data);
			}

			return $response;
		},
		1000, 3
	);
});

