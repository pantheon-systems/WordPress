<?php

add_action('admin_notices', function () {
	blocksy_output_companion_notice();
});

function blocksy_output_companion_notice() {
	if (! apply_filters(
		'blocksy:admin:display-companion-plugin-notice',
		true
	)) {
		return;
	}

	if (! current_user_can('activate_plugins') ) return;
	if (get_option('dismissed-blocksy_plugin_notice', false)) return;

	$manager = new Blocksy_Plugin_Manager();
	$status = $manager->get_companion_status()['status'];

	if ($status === 'active') return;

	$url = admin_url('themes.php?page=ct-dashboard');
	$plugin_url = admin_url('admin.php?page=ct-dashboard');
	$plugin_link = 'https://creativethemes.com/blocksy/companion/';

	echo '<div class="notice notice-blocksy-plugin">';
	echo '<div class="notice-blocksy-plugin-root" data-url="' . esc_attr($url) . '" data-plugin-url="' . esc_attr($plugin_url) . '" data-plugin-status="' . esc_attr($status) . '" data-link="' . esc_attr($plugin_link) . '">';

	?>

	<div class="ct-blocksy-plugin-inner">
		<span class="ct-notification-icon">
			<svg width="50" height="50" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg"><path d="M25 0c13.807 0 25 11.193 25 25S38.807 50 25 50 0 38.807 0 25 11.193 0 25 0zm4.735 25.637a.237.237 0 00-.312 0L19.28 34.83c-.069.063-.02.171.078.171h9.492c.116 0 .229-.042.312-.117l4.45-4.035a1.122 1.122 0 000-1.697zm0-10a.237.237 0 00-.312 0L18.13 25.873a.382.382 0 00-.129.282v7.613c0 .09.119.134.188.071l14.636-13.333c.517-.468.518-1.589 0-2.057zM27.674 15H18.22c-.122 0-.221.09-.221.2v8.568c0 .09.119.134.188.071l9.564-8.668c.07-.063.02-.171-.078-.171z" fill="#23282D" fill-rule="evenodd"/></svg>
		</span>

		<div class="ct-notification-content">
			<h2><?php esc_html_e( 'Thanks for installing Blocksy, you rock!', 'blocksy' ); ?></h2>
			<p>
				<?php esc_html_e( 'We strongly recommend you to activate the', 'blocksy' ); ?>
				<b><?php esc_html_e( 'Blocksy Companion', 'blocksy' ); ?></b> plugin.
				<br>
				<?php esc_html_e( 'This way you will have access to custom extensions, demo templates and many other awesome features', 'blocksy' ); ?>.
			</p>
		</div>
	</div>
	<?php

	echo '</div>';
	echo '</div>';
}

