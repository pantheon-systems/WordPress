<?php


function blocksy_ext_cookies_consent_output() {
	/*
	if (! BlocksyExtensionCookiesConsent::should_display_notification()) {
		if (! $forced) {
			return;
		}
	}
	 */

	$content = get_theme_mod(
		'cookie_consent_content',
		__('We use cookies to ensure that we give you the best experience on our website.', 'blocksy-companion')
	);

	$accept_button_text = get_theme_mod('cookie_consent_button_text', __('Accept', 'blocksy-companion'));
	$decline_button_text = get_theme_mod('cookie_consent_decline_button_text', __('Decline', 'blocksy-companion'));

	$period = get_theme_mod('cookie_consent_period', 'forever');
	$type = get_theme_mod('cookie_consent_type', 'type-1');

	$class = 'container';

	if ( $type === 'type-2' ) {
		$class = 'ct-container';
	}

	ob_start();

	?>


	<div class="cookie-notification ct-fade-in-start" data-period="<?php echo esc_attr($period) ?>" data-type="<?php echo esc_attr($type) ?>">

		<div class="<?php echo esc_attr($class) ?>">
			<?php if (!empty($content)) { ?>
				<div class="ct-cookies-content"><?php echo wp_kses_post($content) ?></div>
			<?php } ?>

			<div class="ct-button-group">
				<button type="submit" class="ct-button ct-cookies-accept-button"><?php echo esc_html($accept_button_text) ?></button>

				<button type="submit" class="ct-button ct-cookies-decline-button"><?php echo esc_html($decline_button_text) ?></button>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

function blocksy_ext_cookies_checkbox($prefix = '') {
	ob_start();

	if (! empty($prefix)) {
		$prefix = '_' . $prefix;
	}

	$message = get_theme_mod(
		'forms_cookie_consent_content',
		sprintf(
			__('I accept the %sPrivacy Policy%s', 'blocksy-companion'),
			'<a href="/privacy-policy">',
			'</a>'
		)
	);

	?>

	<p class="gdpr-confirm-policy">
		<input id="gdprconfirm<?php echo $prefix ?>" class="ct-checkbox" name="gdprconfirm" type="checkbox" required><label for="gdprconfirm<?php echo $prefix ?>"><?php echo $message ?></label>
	</p>

	<?php

	return ob_get_clean();
}
