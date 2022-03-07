<?php
/**
 * Plugin Name: Lost Password Prefill
 * Plugin URI: https://pantheon.io/
 * Description: Add prepopulated email address to password reset form if provided.
 * Version: 0.1
 * Author: Pantheon
 * Author URI: https://pantheon.io/
 *
 * @package pantheon
 */

function lost_pass_help() {
	$lost_pass_text = <<<LOST
		<script>
		let loginUrlParams = new URLSearchParams(window.location.search);
		if (loginUrlParams.has('user_login')) {
			window.document.querySelector('input#user_login').value = loginUrlParams.get('user_login');
		}
		</script>
	LOST;

	echo $lost_pass_text . PHP_EOL;
}

add_action('lostpassword_form', 'lost_pass_help');