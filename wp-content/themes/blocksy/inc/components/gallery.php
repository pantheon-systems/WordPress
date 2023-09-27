<?php

if (! function_exists('blocksy_flexy')) {
function blocksy_flexy($args = []) {
	$args = wp_parse_args($args, [
		'prefix' => '',

		'items' => '',
		'images' => null,
		'images_ratio' => '3/4',

		'pills_images' => null,

		'pills_count' => 0,

		'first_item_class' => '',
		'items_container_class' => '',
		'class' => '',

		'size' => 'medium',
		'lazyload' => true,
		'href' => null,

		'has_pills' => true,

		'enable' => true,
		'has_arrows' => true,

		'arrows_class' => '',

		'container_attr' => [],
		'slide_inner_content' => '',

		'autoplay' => false,

		'slide_image_args' => null,

		'active_index' => 1
	]);

	$prefix = $args['prefix'];

	if (! empty($args['prefix'])) {
		$prefix .= '_';
	}

	$has_scale_rotate = false;

	if ($args['images']) {
		$args['pills_count'] = count($args['images']);
		$args['items'] = '';

		foreach ($args['images'] as $index => $single_image) {
			$attachment_id = $single_image;

			if (
				is_array($single_image)
				&&
				isset($single_image['attachment_id'])
			) {
				$attachment_id = $single_image['attachment_id'];
			}

			if ($has_scale_rotate) {
				$args['items'] .= '<div>';
			}

			$single_item_href = $args['href'];
			$width = null;
			$height = null;

			if (! $single_item_href) {
				$single_item_href = wp_get_attachment_image_src(
					$attachment_id,
					'full'
				);

				if ($single_item_href) {
					$width = $single_item_href[1];
					$height = $single_item_href[2];

					$single_item_href = $single_item_href[0];
				}
			}

			$class = '';

			if ($index === 0 && $args['first_item_class']) {
				$class = $args['first_item_class'];
			}

			$slide_args = [
				'display_video' => true,
				'no_image_type' => 'woo',
				'attachment_id' => $attachment_id,
				'ratio' => $args['images_ratio'],
				'tag_name' => 'a',
				'size' => $args['size'],
				'html_atts' => array_merge([
					'href' => $single_item_href
				], $width ? [
					'data-width' => $width,
					'data-height' => $height
				] : []),
				'inner_content' => $args['slide_inner_content'],
				'lazyload' => $args['lazyload'],
			];

			if ($args['slide_image_args']) {
				$slide_args = call_user_func(
					$args['slide_image_args'],
					$index,
					$slide_args
				);
			}

			$slide_wrapper_attr = [];

			if (! empty($class)) {
				$slide_wrapper_attr['class'] = $class;
			}

			if (
				( $args['images_ratio'] === 'original' || is_customize_preview() )
				&&
				$index === (intval($args['active_index']) - 1)
			) {
				$slide_wrapper_attr['data-item'] = 'initial';
			}

			$args['items'] .= blocksy_html_tag(
				'div',
				$slide_wrapper_attr,
				blocksy_image($slide_args)
			);

			if ($has_scale_rotate) {
				$args['items'] .= '</div>';
			}
		}
	}

	if ($args['enable']) {
		$initial_value = 'no';

		if ($has_scale_rotate) {
			$initial_value = 'no:scalerotate';
		}

		$args['container_attr']['data-flexy'] = $initial_value;

		if ($args['active_index'] > 1) {
			$args['container_attr']['style'] = '--current-item: ' . (intval(
				$args['active_index']
			) - 1);
		}
	} else {
		$args['container_attr'] = [];
	}

	// Slider view
	// boxed | full
	$slider_view = 'boxed';

	$container_attr = '';

	if ($args['autoplay']) {
		$args['container_attr']['data-autoplay'] = $args['autoplay'];
	}

	foreach ($args['container_attr'] as $key => $value) {
		$container_attr .= ' ' . $key . '="' . $value . '"';
	}

	$container_attr = trim($container_attr);

	$dynamic_height_output = '';

	if ($args['images_ratio'] === 'original' || is_customize_preview()) {
		$dynamic_height_output = 'data-height="dynamic"';
	}

	$class = trim('flexy-container ' . $args['class']);

	?>

	<div
		class="<?php echo $class ?>"
		<?php echo $container_attr ?>>

		<div class="flexy">
			<div class="flexy-view" data-flexy-view="<?php echo $slider_view ?>">
				<div
					class="flexy-items <?php echo $args['items_container_class'] ?>"
					<?php echo $dynamic_height_output ?>>
					<?php echo $args['items']; ?>
				</div>
			</div>

			<?php if ($args['has_arrows']) { ?>
				<span class="<?php echo trim('flexy-arrow-prev' . ' ' . $args['arrows_class']) ?>">
					<svg width="16" height="10" viewBox="0 0 16 10">
						<path d="M15.3 4.3h-13l2.8-3c.3-.3.3-.7 0-1-.3-.3-.6-.3-.9 0l-4 4.2-.2.2v.6c0 .1.1.2.2.2l4 4.2c.3.4.6.4.9 0 .3-.3.3-.7 0-1l-2.8-3h13c.2 0 .4-.1.5-.2s.2-.3.2-.5-.1-.4-.2-.5c-.1-.1-.3-.2-.5-.2z"/>
					</svg>
				</span>

				<span class="<?php echo trim('flexy-arrow-next' . ' ' . $args['arrows_class']) ?>">
					<svg width="16" height="10" viewBox="0 0 16 10">
						<path d="M.2 4.5c-.1.1-.2.3-.2.5s.1.4.2.5c.1.1.3.2.5.2h13l-2.8 3c-.3.3-.3.7 0 1 .3.3.6.3.9 0l4-4.2.2-.2V5v-.3c0-.1-.1-.2-.2-.2l-4-4.2c-.3-.4-.6-.4-.9 0-.3.3-.3.7 0 1l2.8 3H.7c-.2 0-.4.1-.5.2z"/>
					</svg>
				</span>
			<?php } ?>
		</div>

		<?php
			if ($args['has_pills']) {
				blocksy_flexy_pills($args);
			}
		?>
	</div>
	<?php
}
}

if (! function_exists('blocksy_flexy_pills')) {
	function blocksy_flexy_pills($args = []) {
		$args = wp_parse_args($args, [
			'pills_count' => 0,
			'pills_images' => null,
			'pills_have_slider' => false,
			'pills_container_attr' => [],
			'pills_have_arrows' => false,
			'active_index' => 1,
			'pills_arrows_class' => '',
			'pills_class' => ''
		]);

		if ($args['pills_count'] === 0) return;

		$type = $args['pills_images'] ? 'thumbs' : 'circle';

		$container_attr = blocksy_attr_to_html($args['pills_container_attr']);

		if (! empty($container_attr)) {
			$container_attr = ' ' . $container_attr;
		}

		$class = 'flexy-pills';

		if (! empty($args['pills_class'])) {
			$class .= ' ' . $args['pills_class'];
		}

		echo '<div class="' . $class . '" data-type="' . $type . '">';
		echo '<ol' . $container_attr . '>';

		foreach (range(1, ceil($args['pills_count'])) as $index) {
			if ($args['pills_images']) {
				$class = '';

				if (intval($index) === $args['active_index']) {
					$class = ' class="active"';
				}

				$image_output = '<li' . $class . '>' . blocksy_image([
					'attachment_id' => $args['pills_images'][$index - 1],
					'ratio' => 'original',
					'tag_name' => 'span',
					'size' => "woocommerce_gallery_thumbnail",
					'html_atts' => [
						'aria-label' => sprintf(__('Slide %s', 'blocksy'), $index)
					],
					'display_video' => 'pill',
					'lazyload' => $args['lazyload']
				]) . '</li>';

				echo $image_output;
			} else {
				echo blocksy_html_tag(
					'li',
					array_merge([
						'aria-label' => sprintf(__('Slide %s', 'blocksy'), $index)
					], intval($index) === $args['active_index'] ? [
						'class' => 'active'
					] : []),
					''
				);
			}
		}

		echo '</ol>';

		if ($args['pills_have_arrows']) {
			echo '<span class="' . trim('flexy-arrow-prev' . ' ' . $args['pills_arrows_class']) . '">
				<svg width="16" height="10" viewBox="0 0 16 10">
					<path d="M15.3 4.3h-13l2.8-3c.3-.3.3-.7 0-1-.3-.3-.6-.3-.9 0l-4 4.2-.2.2v.6c0 .1.1.2.2.2l4 4.2c.3.4.6.4.9 0 .3-.3.3-.7 0-1l-2.8-3h13c.2 0 .4-.1.5-.2s.2-.3.2-.5-.1-.4-.2-.5c-.1-.1-.3-.2-.5-.2z"/>
				</svg>
			</span>';
			echo '<span class="' . trim('flexy-arrow-next' . ' ' . $args['pills_arrows_class']) . '">
				<svg width="16" height="10" viewBox="0 0 16 10">
					<path d="M.2 4.5c-.1.1-.2.3-.2.5s.1.4.2.5c.1.1.3.2.5.2h13l-2.8 3c-.3.3-.3.7 0 1 .3.3.6.3.9 0l4-4.2.2-.2V5v-.3c0-.1-.1-.2-.2-.2l-4-4.2c-.3-.4-.6-.4-.9 0-.3.3-.3.7 0 1l2.8 3H.7c-.2 0-.4.1-.5.2z"/>
				</svg>
			</span>';
		}

		echo '</div>';

	}
}
