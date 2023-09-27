<?php

$class = trim('ct-footer-socials' . ' ' . blocksy_visibility_classes(blocksy_default_akg(
	'footer_socials_visibility',
	$atts,
	[
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	]
)));

$socialsColor = blocksy_default_akg('footerSocialsColor', $atts, 'custom');
$socialsType = blocksy_default_akg('socialsType', $atts, 'simple');

$socials = blocksy_default_akg(
	'footer_socials',
	$atts,
	[
		[
			'id' => 'facebook',
			'enabled' => true,
		],

		[
			'id' => 'twitter',
			'enabled' => true,
		],

		[
			'id' => 'instagram',
			'enabled' => true,
		],
	]
);

$link_target = blocksy_default_akg('link_target', $atts, 'no');

if ($link_target === 'yes') {
	$link_target = '_blank';
} else {
	$link_target = false;
}

$link_rel = blocksy_default_akg('link_nofollow', $atts, 'no');

if ($link_rel === 'yes') {
	$link_rel = 'noopener noreferrer nofollow';
} else {
	$link_rel = 'noopener';
}

?>

<div
	class="<?php echo esc_attr($class) ?>"
	<?php echo blocksy_attr_to_html($attr) ?>>

	<?php echo blocksy_social_icons($socials, [
		'type' => $socialsType,
		'icons-color' => $socialsColor,
		'fill' => blocksy_default_akg(
			'socialsFillType',
			$atts,
			'solid'
		),
		'hide_labels' => !blocksy_some_device(blocksy_default_akg(
			'socialsLabelVisibility',
			$atts,
			[
				'desktop' => false,
				'tablet' => false,
				'mobile' => false,
			]
		)),
		'links_target' => $link_target,
		'links_rel' => $link_rel
	]) ?>
</div>

