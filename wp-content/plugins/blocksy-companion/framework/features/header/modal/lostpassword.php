<form name="lostpasswordform" id="lostpasswordform" action="#" method="post">
	<?php do_action('blocksy:account:modal:lostpassword:start'); ?>

	<p>
		<label for="user_login_forgot"><?php echo __('Username or Email Address', 'blocksy-companion')?></label>
		<input type="text" name="user_login" id="user_login_forgot" class="input" value="" size="20" autocapitalize="off" required>
	</p>

	<?php do_action('lostpassword_form'); ?>

	<p>
		<button name="wp-submit" class="ct-button">
			<?php echo __('Get New Password', 'blocksy-companion') ?>

			<svg width="23" height="23" viewBox="0 0 40 40">
				<path opacity=".2" fill="currentColor" d="M20.201 5.169c-8.254 0-14.946 6.692-14.946 14.946 0 8.255 6.692 14.946 14.946 14.946s14.946-6.691 14.946-14.946c-.001-8.254-6.692-14.946-14.946-14.946zm0 26.58c-6.425 0-11.634-5.208-11.634-11.634 0-6.425 5.209-11.634 11.634-11.634 6.425 0 11.633 5.209 11.633 11.634 0 6.426-5.208 11.634-11.633 11.634z"/>

				<path fill="currentColor" d="m26.013 10.047 1.654-2.866a14.855 14.855 0 0 0-7.466-2.012v3.312c2.119 0 4.1.576 5.812 1.566z">
					<animateTransform attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="1s" repeatCount="indefinite"/>
				</path>
			</svg>
		</button>

		<!-- <input type="hidden" name="redirect_to" value="<?php echo blocksy_current_url() ?>"> -->
	</p>

	<?php do_action('blocksy:account:modal:lostpassword:end'); ?>
	<?php wp_nonce_field('blocksy-lostpassword', 'blocksy-lostpassword-nonce'); ?>
</form>

