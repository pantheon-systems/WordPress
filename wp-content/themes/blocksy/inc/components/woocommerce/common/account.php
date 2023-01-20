<?php

if (! function_exists('blocksy_woocommerce_has_account_customizations')) {
	function blocksy_woocommerce_has_account_customizations() {
		return ! defined('YITH_WCMAP');
	}
}

add_filter(
	'do_shortcode_tag',
	function ($output, $tag, $attr, $m) {
		if (! blocksy_woocommerce_has_account_customizations()) {
			return $output;
		}

		if ($tag === 'woocommerce_my_account') {
			$endpoint = WC()->query->get_current_endpoint();

			$account_class = 'ct-woo-account';

			if (
				! is_user_logged_in()
				||
				$endpoint === 'lost-password'
			) {
				$account_class = 'ct-woo-unauthorized';
			}

			return str_replace(
				'class="woocommerce"',
				'class="woocommerce ' . $account_class . '"',
				$output
			);
		}

		return $output;
	},
	9999,
	4
);

add_action('woocommerce_before_account_navigation', function () {
	if (! blocksy_woocommerce_has_account_customizations()) {
		return;
	}

	$username = '';

	if (get_theme_mod('has_account_page_name', 'no') === 'yes') {
		$username .= wp_get_current_user()->display_name;
	}

	if (get_theme_mod('has_account_page_quick_actions', 'no') === 'yes') {
		$account_details_url = wc_get_endpoint_url(
			'edit-account',
			'',
			get_permalink(get_option('woocommerce_myaccount_page_id'))
		);
		$username .= '<span><a href="' . $account_details_url . '">' . __('Account', 'blocksy') . '</a> <i>|</i> <a href="' . wc_logout_url() . '">' . __("Log out", 'blocksy') . '</a></span>';
	}

	if (! empty($username)) {
		$username = '<div class="ct-account-user-box">' . $username . '</div>';
	}

	if (get_theme_mod('has_account_page_avatar', 'no') === 'yes') {
		$avatar_size = intval(get_theme_mod(
			'account_page_avatar_size',
			'35'
		)) * 2;

		$username = blocksy_simple_image(
			get_avatar_url(
				get_current_user_id(),
				[
					'size' => $avatar_size
				]
			),
			[
				'tag_name' => 'span',

				'aspect_ratio' => false,
				'suffix' => 'static',
				'img_atts' => [
					'width' => $avatar_size / 2,
					'height' => $avatar_size / 2,
					'style' => 'height:' . (
						intval($avatar_size) / 2
					) . 'px',
					'alt' => blocksy_get_avatar_alt_for(get_the_author_meta('ID'))
				],
			]
		) . $username;
	}

	if (! empty($username)) {
		echo '<div class="ct-account-welcome">';
		echo $username;
		echo '</div>';
	}
});

