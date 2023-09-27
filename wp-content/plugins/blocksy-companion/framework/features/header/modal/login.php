<?php

// wp_login_form([]);

$redirect_to_url = apply_filters(
	'blocksy:account:modal:login:redirect_to',
	$current_url
);

$forgot_password_inline = apply_filters(
	'blocksy:account:modal:login:forgot-password-inline',
	true
);

$forgot_pass_class = 'ct-forgot-password';

if (! $forgot_password_inline) {
	$forgot_pass_class .= '-static';
}

?>

<form name="loginform" id="loginform" class="login" action="#" method="post">
	<?php do_action('woocommerce_login_form_start'); ?>
	<?php do_action('blocksy:account:modal:login:start'); ?>

	<p>
		<label for="user_login"><?php echo __('Username or Email Address', 'blocksy-companion') ?></label>
		<input type="text" name="log" id="user_login" class="input" value="" size="20">
	</p>

	<p>
		<label for="user_pass"><?php echo __('Password', 'blocksy-companion') ?></label>
		<span class="account-password-input">
			<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
			<span class="show-password-input"></span>
		</span>
	</p>

	<p class="login-remember col-2">
		<span>
			<input name="rememberme" type="checkbox" id="rememberme" class="ct-checkbox" value="forever">
			<label for="rememberme"><?php echo __('Remember Me', 'blocksy-companion') ?></label>
		</span>

		<a href="<?php echo wp_lostpassword_url() ?>" class="<?php echo $forgot_pass_class ?>">
			<?php echo __('Forgot Password?', 'blocksy-companion') ?>
		</a>
	</p>

	<?php
		if (function_exists('blc_fs') && blc_fs()->can_use_premium_code()) {
			if (
				class_exists('NextendSocialLogin', false)
				&&
				! class_exists('NextendSocialLoginPRO', false)
			) {
				\NextendSocialLogin::addLoginFormButtons();
			}
		}

		remove_action("login_form", "wp_login_attempt_focus_start");

		do_action('login_form')
	?>

	<p class="login-submit">
		<button name="wp-submit" class="ct-button">
			<?php echo __('Log In', 'blocksy-companion') ?>

			<svg width="23" height="23" viewBox="0 0 40 40">
				<path opacity=".2" fill="currentColor" d="M20.201 5.169c-8.254 0-14.946 6.692-14.946 14.946 0 8.255 6.692 14.946 14.946 14.946s14.946-6.691 14.946-14.946c-.001-8.254-6.692-14.946-14.946-14.946zm0 26.58c-6.425 0-11.634-5.208-11.634-11.634 0-6.425 5.209-11.634 11.634-11.634 6.425 0 11.633 5.209 11.633 11.634 0 6.426-5.208 11.634-11.633 11.634z"/>

				<path fill="currentColor" d="m26.013 10.047 1.654-2.866a14.855 14.855 0 0 0-7.466-2.012v3.312c2.119 0 4.1.576 5.812 1.566z">
					<animateTransform attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="1s" repeatCount="indefinite"/>
				</path>
			</svg>
		</button>

		<input type="hidden" name="redirect_to" value="<?php echo $redirect_to_url ?>">
	</p>

	<?php do_action('blocksy:account:modal:login:end'); ?>
	<?php do_action('woocommerce_login_form_end'); ?>
</form>

