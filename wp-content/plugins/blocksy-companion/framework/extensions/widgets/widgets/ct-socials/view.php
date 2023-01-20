<?php
/**
 * Socials Widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */

// Widget title.
$title = blocksy_default_akg('title', $atts, __('Social Icons', 'blocksy-companion'));
$size = blocksy_default_akg('social_icons_size', $atts, 'medium');
$color = blocksy_default_akg('social_icons_color', $atts, 'default');
$type = blocksy_default_akg('social_type', $atts, 'simple');
$fill = blocksy_default_akg('social_icons_fill', $atts, 'outline');

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


// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_widget;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
if (! empty(trim($title))) {
	echo $before_title . wp_kses_post($title) . $after_title;
}


/**
 * blocksy_social_icons() function is already properly escaped.
 * Escaping it again here would cause SVG icons to not be outputed
 */
echo blc_call_fn(
	['fn' => 'blocksy_social_icons'],
	blocksy_default_akg(
		'socials',
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
	),
	[
		'size' => $size,
		'icons-color' => $color,
		'fill' => $fill,
		'type' => $type,
		'links_target' => $link_target,
		'links_rel' => $link_rel,
	]
);

echo wp_kses_post($after_widget);