<?php

$custom_logo_id = blocksy_default_akg(
	'custom_logo',
	$atts,
	get_theme_mod('custom_logo', '')
);

if ($custom_logo_id) {
	$custom_logo_attr = [
		// 'class' => '',
		'itemprop' => 'logo',
		'loading' => false
	];

	/**
	 * If the logo alt attribute is empty, get the site title and explicitly
	 * pass it to the attributes used by wp_get_attachment_image().
	 */
	$image_alt = get_post_meta(
		$custom_logo_id,
		'_wp_attachment_image_alt',
		true
	);

	if (empty($image_alt)) {
		$custom_logo_attr['alt'] = get_bloginfo('name', 'display');
	}

	$image_logo_html = wp_get_attachment_image(
		$custom_logo_id,
		'full',
		false,
		$custom_logo_attr
	);
}

$href = esc_url(
	apply_filters('blocksy:header:offcanvas-logo:url', home_url('/'))
);

$old_data_id = $attr['data-id'];
unset($attr['data-id']);

$attr['href'] = $href;
$attr['class'] = 'site-logo-container';
$attr['data-id'] = $old_data_id;
$attr['rel'] = 'home';
$attr['itemprop'] = 'url';

?>

<a <?php echo blocksy_attr_to_html($attr) ?>>
	<?php if ($custom_logo_id) { ?>
		<?php echo wp_kses_post($image_logo_html); ?>
	<?php } ?>
</a>

