<?php

if (! isset($device)) {
	$device = 'desktop';
}

$default_logo = blocksy_expand_responsive_value(
	blocksy_default_akg('custom_logo', $atts, get_theme_mod('custom_logo', ''))
);

$transparent_logo = blocksy_expand_responsive_value(
	blocksy_default_akg('transparent_logo', $atts, '')
);

$sticky_logo = blocksy_expand_responsive_value(
	blocksy_default_akg('sticky_logo', $atts, '')
);

$custom_logo_id = '';
$additional_logo_id = '';

$logo_position = blocksy_expand_responsive_value(
	blocksy_default_akg('logo_position', $atts, '')
);

if (
	isset($has_transparent_header)
	&&
	$has_transparent_header
	&&
	is_array($has_transparent_header)
	&&
	in_array($device, $has_transparent_header)
	&&
	! empty($transparent_logo[$device])
) {
	$custom_logo_id = $transparent_logo[$device];
} else {
	if (! empty($default_logo[$device])) {
		$custom_logo_id = $default_logo[$device];
	}
}

if (
	isset($has_sticky_header)
	&&
	is_array($has_sticky_header)
	&&
	is_array($has_sticky_header['devices'])
	&&
	in_array($device, $has_sticky_header['devices'])
	&&
	! empty($sticky_logo[$device])
) {
	if (! $custom_logo_id) {
		$custom_logo_id = $sticky_logo[$device];
	} else {
		$additional_logo_id = $sticky_logo[$device];
	}
}

if ($custom_logo_id) {
	$custom_logo_attr = [
		'class' => 'default-logo',
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

	if (! empty($additional_logo_id)) {
		$custom_logo_attr['class'] = 'sticky-logo';

		$image_logo_html = wp_get_attachment_image(
			$additional_logo_id,
			'full',
			false,
			$custom_logo_attr
		) . $image_logo_html;
	}

	/**
	 * If the alt attribute is not empty, there's no need to explicitly pass
	 * it because wp_get_attachment_image() already adds the alt attribute.
	 */
	$logo_html = sprintf(
		'<a href="%1$s" class="site-logo-container" rel="home" itemprop="url">%2$s</a>',
		esc_url(
			apply_filters('blocksy:' . $panel_type . ':logo:url', home_url('/'))
		),
		$image_logo_html
	);
}

$tagline_class = 'site-description ' . blocksy_visibility_classes(
	blocksy_default_akg('blogdescription_visibility', $atts, [
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	])
);

$site_title_class = 'site-title ' . blocksy_visibility_classes(
	blocksy_default_akg('blogname_visibility', $atts, [
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	])
);

$tag = 'span';

// if (is_home() || is_front_page()) {
// 	if ($device !== 'mobile') {
// 		$tag = 'h1';
// 	}
// }

$tag = apply_filters('blocksy:' . $panel_type . ':logo:tag', $tag);
$wrapper_tag = apply_filters('blocksy:' . $panel_type . ':logo:wrapper-tag', 'div');

$has_site_title = blocksy_akg('has_site_title', $atts, 'yes') === 'yes';
$has_tagline = blocksy_akg('has_tagline', $atts, 'no') === 'yes';

$logo_position = '';

if (
	$custom_logo_id
	&&
	(
		$has_site_title
		||
		$has_tagline
	)
) {
	$logo_position_v = blocksy_expand_responsive_value(
		blocksy_default_akg('logo_position', $atts, 'top')
	);

	$logo_position = 'data-logo="' . $logo_position_v[$device] . '"';
}

$class = trim('site-branding' . ' ' . blocksy_visibility_classes(
	blocksy_akg('visibility', $atts, [
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	])
));

?>

<<?php echo $wrapper_tag ?>
	class="<?php echo $class ?>"
	<?php echo blocksy_attr_to_html($attr) ?>
	<?php echo $logo_position ?>
	<?php echo blocksy_schema_org_definitions('logo') ?>>

	<?php if ($custom_logo_id) { ?>
		<?php echo wp_kses_post($logo_html); ?>
	<?php } ?>

	<?php if ($has_site_title || $has_tagline) { ?>
		<div class="site-title-container">
			<?php if ($has_site_title) { ?>
				<<?php echo $tag ?> class="<?php echo $site_title_class ?>" <?php echo blocksy_schema_org_definitions('name') ?>>
					<a href="<?php echo esc_url(apply_filters('blocksy:' . $panel_type . ':logo:url', home_url('/'))); ?>" rel="home" <?php echo blocksy_schema_org_definitions('url')?>>
						<?php
							echo blocksy_translate_dynamic(blocksy_default_akg(
								'blogname',
								$atts,
								get_bloginfo('name')
							), $panel_type . ':' . $section_id . ':logo:blogname');
						?>
					</a>
				</<?php echo $tag ?>>
			<?php } ?>

			<?php if ($has_tagline) { ?>
				<p class="<?php echo $tagline_class ?>" <?php echo blocksy_schema_org_definitions('description') ?>>
					<?php
						echo blocksy_translate_dynamic(blocksy_default_akg(
							'blogdescription',
							$atts,
							get_bloginfo('description')
						), $panel_type . ':' . $section_id . ':logo:blogdescription');
					?>
				</p>
			<?php } ?>
		</div>
	  <?php } ?>
</<?php echo $wrapper_tag ?>>

