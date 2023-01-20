<?php

if (! function_exists('blocksy_has_post_nav')) {
	function blocksy_has_post_nav() {
		$post_options = blocksy_get_post_options();
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_post_nav = get_theme_mod(
			$prefix . '_has_post_nav',
			'no'
		) === 'yes';

		if (blocksy_is_page()) {
			$has_post_nav = false;
		}

		if (
			blocksy_default_akg(
				'disable_posts_navigation', $post_options, 'no'
			) === 'yes'
		) {
			$has_post_nav = false;
		}

		return $has_post_nav;
	}
}

if (! function_exists('blocksy_has_share_box')) {
	function blocksy_has_share_box() {
		$post_options = blocksy_get_post_options();
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_share_box = get_theme_mod(
			$prefix . '_has_share_box',
			'no'
		) === 'yes';

		if (
			blocksy_default_akg(
				'disable_share_box',
				$post_options,
				'no'
			) === 'yes'
		) {
			$has_share_box = false;
		}

		return $has_share_box;
	}
}

if (! function_exists('blocksy_has_author_box')) {
	function blocksy_has_author_box() {
		$post_options = blocksy_get_post_options();
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_author_box = get_theme_mod(
			$prefix . '_has_author_box',
			'no'
		) === 'yes';

		if (blocksy_is_page()) {
			$has_author_box = false;
		}

		if (
			blocksy_default_akg(
				'disable_author_box', $post_options, 'no'
			) === 'yes'
		) {
			$has_author_box = false;
		}

		$has_author_box = apply_filters(
			'blocksy:single:has-author-box',
			$has_author_box
		);

		return $has_author_box;
	}
}

if (! function_exists('blocksy_single_content')) {
function blocksy_single_content($content = null) {
	$post_options = blocksy_get_post_options();

	$prefix = blocksy_manager()->screen->get_prefix();

	$has_post_tags = get_theme_mod(
		$prefix . '_has_post_tags',
		'no'
	) === 'yes';

	if (
		blocksy_default_akg(
			'disable_post_tags', $post_options, 'no'
		) === 'yes'
	) {
		$has_post_tags = false;
	}

	$featured_image_location = 'none';

	$page_title_source = blocksy_get_page_title_source();
	$featured_image_source = blocksy_get_featured_image_source();

	if ($page_title_source) {
		$actual_type = blocksy_akg_or_customizer(
			'hero_section',
			blocksy_get_page_title_source(),
			'type-1'
		);

		if ($actual_type !== 'type-2') {
			$featured_image_location = get_theme_mod(
				$prefix . '_featured_image_location',
				'above'
			);
		} else {
			$featured_image_location = 'below';
		}
	} else {
		$featured_image_location = 'above';
	}

	$share_box_type = get_theme_mod($prefix . '_share_box_type', 'type-1');

	$share_box1_location = get_theme_mod($prefix . '_share_box1_location', [
		'top' => false,
		'bottom' => true,
	]);

	$share_box2_location = get_theme_mod($prefix . '_share_box2_location', 'right');
	$share_box2_colors = get_theme_mod($prefix . '_share_box2_colors', 'custom');

	$content_class = 'entry-content';

	ob_start();

	?>

	<article
		id="post-<?php the_ID(); ?>"
		<?php post_class(); ?>>

		<?php
			do_action('blocksy:single:top');

			if ($featured_image_location === 'above') {
				echo blocksy_get_featured_image_output();
			}

			if (
				! is_singular([ 'product' ])
				&&
				apply_filters('blocksy:single:has-default-hero', true)
			) {
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_output_hero_section() used here escapes the value properly.
				 */
				echo blocksy_output_hero_section([
					'type' => 'type-1'
				]);
			}

			if ($featured_image_location === 'below') {
				echo blocksy_get_featured_image_output();
			}
		?>

		<?php if (
			$share_box1_location['top']
			&&
			blocksy_has_share_box()
		) { ?>
			<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_get_social_share_box() used here escapes the value properly.
				 */
				echo blocksy_get_social_share_box([
					'html_atts' => [
						'data-location' => 'top'
					],
					'links_wrapper_attr' => $share_box_type === 'type-2' ? [
						'data-color' => $share_box2_colors
					] : [],
					'type' => $share_box_type
				]);
			?>
		<?php } ?>

		<?php do_action('blocksy:single:content:top'); ?>

		<div class="<?php echo $content_class ?>">
			<?php

			if (! is_attachment()) {
				if (
					function_exists('blc_get_content_block_that_matches')
					&&
					blc_get_content_block_that_matches([
						'template_type' => 'single',
						'template_subtype' => 'content'
					])
				) {
					$content = blc_render_content_block(
						blc_get_content_block_that_matches([
							'template_type' => 'single',
							'template_subtype' => 'content'
						])
					);
				}

				if ($content) {
					echo $content;
				} else {
					the_content(
						sprintf(
							wp_kses(
								/* translators: %s: Name of current post. Only visible to screen readers */
								__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'blocksy' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							get_the_title()
						)
					);
				}
			} else {
				?>
					<figure class="entry-attachment wp-block-image">
						<?php
							echo blocksy_image([
								'attachment_id' => get_the_ID(),
								'post_id' => get_the_ID(),
								'size' => 'full',
								'tag_name' => 'a',
								'ratio' => 'original',
								'html_atts' => [
									'href' => wp_get_attachment_url(get_the_ID())
								]
							]);
						?>

						<figcaption class="wp-caption-text"><?php the_excerpt(); ?></figcaption>
					</figure>
				<?php
			}

			?>
		</div>

		<?php
			if (get_post_type() === 'post') {
				edit_post_link(
					sprintf(
						/* translators: %s: Post title. */
						__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'blocksy' ),
						get_the_title()
					)
				);
			}

			wp_link_pages(
				[
					'before' => '<div class="page-links"><span class="post-pages-label">' . esc_html__( 'Pages', 'blocksy' ) . '</span>',
					'after'  => '</div>',
				]
			);

			do_action('blocksy:single:content:bottom');
		?>

		<?php if ($has_post_tags) { ?>
			<?php
				$tax_to_check = blocksy_maybe_get_matching_taxonomy(
					get_post_type(),
					false
				);

				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_post_meta() used here escapes the value properly.
				 */
				if (
					$tax_to_check
					&&
					blocksy_get_categories_list([
						'taxonomy' => $tax_to_check
					])
					&&
					! is_wp_error(blocksy_get_categories_list([
						'taxonomy' => $tax_to_check
					]))
				) {
					echo blocksy_html_tag(
						'div',
						['class' => 'entry-tags'],
						blocksy_get_categories_list([
							'taxonomy' => $tax_to_check,
							'before_each' => '# ',
							'has_term_class' => false
						])
					);
				}
			?>
		<?php } ?>

		<?php if (
			$share_box1_location['bottom']
			&&
			blocksy_has_share_box()
		) { ?>
			<?php
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_get_social_share_box() used here escapes the value properly.
				 */
				echo blocksy_get_social_share_box([
					'html_atts' => ['data-location' => 'bottom'],
					'links_wrapper_attr' => $share_box_type === 'type-2' ? [
						'data-color' => $share_box2_colors
					] : [],
					'type' => $share_box_type
				]);
			?>
		<?php } ?>

		<?php

		if (blocksy_has_author_box()) {
			blocksy_author_box();
		}

		if (blocksy_has_post_nav()) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_post_navigation() used here escapes the value properly.
			 */
			echo blocksy_post_navigation();
		}

		if (function_exists('blc_ext_newsletter_subscribe_form')) {
			if (get_post_type() === 'post') {
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blc_ext_newsletter_subscribe_form() used here escapes the value properly.
				 */
				echo blc_ext_newsletter_subscribe_form();
			}
		}

		blocksy_display_page_elements('contained');

		do_action('blocksy:single:bottom');

		?>

	</article>

	<?php

	return ob_get_clean();
}
}

