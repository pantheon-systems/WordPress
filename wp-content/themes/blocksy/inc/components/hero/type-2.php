<?php

$attachment_id = false;
$prefix = blocksy_manager()->screen->get_prefix();


$page_title_bg_type = blocksy_akg_or_customizer(
	'page_title_bg_type',
	blocksy_get_page_title_source(),
	(
		(
			is_search()
			||
			is_author()
		) ? 'color' : 'featured_image'
	)
);

if ($page_title_bg_type !== 'color') {
	if (
		is_singular()
		||
		blocksy_is_page()
	) {
		if (blocksy_is_page()) {
			$attachment_id = get_post_thumbnail_id(blocksy_is_page());
		} else {
			$attachment_id = get_post_thumbnail_id($post_id);
		}
	} else {
		$term_id = get_queried_object_id();

		if ($term_id && !is_singular()) {
			$id = get_term_meta($term_id, 'thumbnail_id');

			if ($id && !empty($id)) {
				$attachment_id = $id[0];
			}

			if (! $id) {
				$attachment_id = null;
			}

			$term_atts = get_term_meta(
				$term_id,
				'blocksy_taxonomy_meta_options'
			);

			if (empty($term_atts)) {
				$term_atts = [[]];
			}

			$maybe_image = blocksy_akg('image', $term_atts[0], '');

			if (
				$maybe_image
				&&
				is_array($maybe_image)
				&&
				isset($maybe_image['attachment_id'])
			) {
				$attachment_id = $maybe_image['attachment_id'];
			}
		}
	}
}

if ($page_title_bg_type === 'custom_image') {
	$attachment_id = null;

	$custom_background_image = blocksy_akg_or_customizer(
		'custom_hero_background',
		blocksy_get_page_title_source(),
		[
			'attachment_id' => null
		]
	);

	if ($custom_background_image['attachment_id']) {
		$attachment_id = $custom_background_image['attachment_id'];
	}
}

if (
	$page_title_bg_type === 'custom_image'
	||
	$page_title_bg_type === 'featured_image'
) {
	$parallax_result = [];

	$parallax_value = blocksy_akg_or_customizer(
		'parallax',
		blocksy_get_page_title_source(),
		[
			'desktop' => false,
			'tablet' => false,
			'mobile' => false,
		]
	);

	if ($parallax_value['desktop']) {
		$parallax_result[] = 'desktop';
	}

	if ($parallax_value['tablet']) {
		$parallax_result[] = 'tablet';
	}

	if ($parallax_value['mobile']) {
		$parallax_result[] = 'mobile';
	}

	if (count($parallax_result) > 0) {
		$attr['data-parallax'] = implode(':', $parallax_result);
	}
}

$hero_structure = blocksy_akg_or_customizer(
	'hero_structure',
	blocksy_get_page_title_source(),
	'narrow'
);

$container_class = 'ct-container';

if ($hero_structure === 'narrow') {
	$container_class = 'ct-container-narrow';
}

$attr = apply_filters('blocksy:hero:wrapper-attr', $attr);

if ($prefix === 'courses_single' && function_exists('tutor')) {
	$elements = str_replace(
		'tutor-course-details-header tutor-mb-44',
		'tutor-course-details-header entry-header ' . $container_class,
		$elements
	);
}

?>

<div <?php echo blocksy_attr_to_html($attr) ?>>
	<?php if ($attachment_id) { ?>
		<figure>
			<?php
				echo blocksy_image(
					apply_filters('blocksy:hero:type-2:image-args', [
						'attachment_id' => $attachment_id,
						// 'size' => 'full',
						'size' => blocksy_akg_or_customizer(
							'page_title_image_size',
							blocksy_get_page_title_source(),
							'full'
						),
						'aspect_ratio' => false,
						'lazyload' => get_theme_mod(
							'has_lazy_load_page_title_image',
							'yes'
						) === 'yes'
					])
				);
			?>
		</figure>
	<?php } ?>

	<?php if ($prefix === 'courses_single' && function_exists('tutor')) { ?>
		<?php echo $elements ?>
	<?php } else { ?>
		<header class="entry-header <?php echo $container_class ?>">
			<?php echo $elements ?>
		</header>
	<?php } ?>
</div>


