<?php
/**
 * About me widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */


// Widget title
$title = blocksy_default_akg('title', $atts, __('About me', 'blocksy-companion'));
$about_source = blocksy_default_akg('about_source', $atts, 'from_wp');
$about_type = blocksy_default_akg('about_type', $atts, 'simple');
$alignment = blocksy_default_akg('about_alignment', $atts, 'center');
$avatar_size = blocksy_default_akg('about_avatar_size', $atts, 'small');
$avatar_shape = blocksy_default_akg('avatar_shape', $atts, 'rounded');

$sizes = [
	'small' => 90,
	'medium' => 140,
	'large' => 200
];

$user_id = blocksy_akg('wp_user', $atts, null);

$image_output = blc_call_fn(['fn' => 'blocksy_image'], [
	'attachment_id' => blocksy_default_akg('about_avatar/attachment_id', $atts, null),
	'ratio' => '1/1',
	'tag_name' => 'figure',
	'size' => $avatar_size === 'small' ? 'thumb' : 'medium',
	// 'lazyload' => false,
	'html_atts' => [
		'data-size' => $avatar_size,
		'data-shape' => $avatar_shape,
	]
]);

$about_name = blocksy_default_akg('about_name', $atts, __('John Doe', 'blocksy-companion'));
$about_text = do_shortcode(blocksy_default_akg('about_text', $atts, ''));

if ($about_source === 'from_wp') {
	if (! $user_id) {
		require_once dirname( __FILE__ ) . '/helpers.php';
		$user_id = array_keys(blc_get_user_choices())[0];
	}

	$image_output = blc_call_fn(
		['fn' => 'blocksy_simple_image'],
		get_avatar_url($user_id, [
			/*
			'size' => intval(
				blocksy_default_akg('about_avatar_size', $atts, 75)
			)
			 */
			'size' => $sizes[$avatar_size] * 2
		]),
		[
			'tag_name' => 'figure',
			'ratio' => '1/1',
			'html_atts' => [
				'data-size' => $avatar_size,
				'data-shape' => $avatar_shape,
				// 'style' => 'max-width: ' . blocksy_default_akg(
				// 	'about_avatar_size', $atts, 75
				// ) . 'px'
			]
		]
	);

	$about_name = get_the_author_meta('display_name', $user_id);
	$about_text = get_the_author_meta('description', $user_id);
}

$size = blocksy_default_akg('about_social_icons_size', $atts, 'small');
$type = blocksy_default_akg('about_social_type', $atts, 'rounded');
$fill = blocksy_default_akg('about_social_icons_fill', $atts, 'outline');

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_widget;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
if (! empty(trim($title))) {
	echo $before_title . wp_kses_post($title) . $after_title;
}
?>


<div
	data-type="<?php echo esc_attr($about_type) ?>"
	data-alignment="<?php echo esc_attr($alignment) ?>">

	<?php echo $image_output; ?>

	<p class="ct-about-me-name">
		<?php echo $about_name; ?>

		<?php if ($about_source === 'from_wp') { ?>
			<a href="<?php echo get_author_posts_url($user_id) ?>" class="ct-about-me-link">
				<?php echo __('View Profile', 'blocksy-companion') ?>
			</a>
		<?php } ?>
	</p>

	<div class="ct-about-me-text"><?php echo $about_text; ?></div>

	<?php
		echo blc_call_fn(
			['fn' => 'blocksy_social_icons'],
			blocksy_default_akg(
				'about_socials',
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
				'type' => $type,
				'fill' => $fill
			]
		);
	?>
</div>

<?php echo wp_kses_post($after_widget); ?>
