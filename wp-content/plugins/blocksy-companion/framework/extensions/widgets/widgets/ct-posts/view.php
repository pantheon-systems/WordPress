<?php
/**
 * Posts widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */

$post_type = blocksy_default_akg('post_type_source', $atts, 'post');

$query_args = [
	'order' => 'DESC',
	'ignore_sticky_posts' => true,
	'post_type' => $post_type
];

if ($post_type !== 'page') {
	$source = blocksy_default_akg('post_source', $atts, 'categories');

	if ($source === 'categories') {
		$date_query = [];

		$days = blocksy_default_akg('days', $atts, 'all_time');

		if ($days && 'all_time' !== $days) {
			$time = time() - (intval($days) * 24 * 60 * 60);

			$date_query = [
				'after' => date('F jS, Y', $time),
				'before' => date('F jS, Y'),
				'inclusive' => true,
			];
		}

		$cat_option_id = 'category';
		$taxonomy = 'category';

		if ($post_type !== 'post') {
			$cat_option_id = $post_type . '_taxonomy';
			$taxonomies = get_object_taxonomies($post_type);

			if (count($taxonomies) > 0) {
				$taxonomy = $taxonomies[0];
			}
		}

		$cat_id = blocksy_default_akg($cat_option_id, $atts, 'all_categories');
		$cat_id = (empty($cat_id) || 'all_categories' === $cat_id) ? '' : $cat_id;

		$type = blocksy_default_akg('type', $atts, 'recent');

		if ($type !== 'default') {
			$orderby_map = [
				'random' => 'rand',
				'recent' => 'post_date',
				'commented' => 'comment_count'
			];

			if (isset($orderby_map[$type])) {
				$query_args['orderby'] = $orderby_map[$type];
			}
		}

		$query_args['date_query'] = $date_query;

		if (! empty($cat_id)) {
			$query_args['tax_query'] = [
				[
					'taxonomy' => $taxonomy,
					'field' => 'term_id',
					'terms' => [$cat_id]
				]
			];
		}

		$query_args['posts_per_page'] = intval(
			blocksy_default_akg('posts_number', $atts, 5)
		);
	}

	if ($source === 'custom') {
		$post_id = blocksy_default_akg('post_id', $atts, '');

		$query_args['orderby'] = 'post__in';
		$query_args['post__in'] = ['__INEXISTING__'];

		if (! empty(trim($post_id))) {
			$query_args['post__in'] = explode(',', str_replace(' ', '', trim(
				$post_id
			)));
		}
	}
}

if ($post_type === 'page') {
	$source = blocksy_default_akg('page_source', $atts, 'default');

	if ($source === 'default') {
		$query_args['posts_per_page'] = intval(
			blocksy_default_akg('page_number', $atts, 5)
		);
	}

	if ($source === 'custom') {
		$post_id = blocksy_default_akg('page_id', $atts, '');

		$query_args['orderby'] = 'post__in';
		$query_args['post__in'] = ['__INEXISTING__'];

		if (! empty(trim($post_id))) {
			$query_args['post__in'] = explode(',', str_replace(' ', '', trim(
				$post_id
			)));
		}
	}
}

$query = new WP_Query($query_args);

// Post thumbnail
$has_thumbnail = false;

$posts_type = blocksy_default_akg('posts_type', $atts, 'small-thumbs');
$type_output = 'data-type="' . esc_attr($posts_type) . '"';

if ($posts_type !== 'no-thumbs' && $posts_type !== 'numbered' ) {
	$has_thumbnail = true;
}

// Post meta
$has_meta = blocksy_default_akg( 'display_date', $atts, 'no' ) === 'yes';


// Comments
$has_comments = blocksy_default_akg( 'display_comments', $atts, 'no' ) === 'yes';

// Widget title
$title = blocksy_default_akg( 'title', $atts, __( 'Posts', 'blocksy-companion' ) );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_widget;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_title . wp_kses_post( $title ) . $after_title;

?>

<?php if ($query->have_posts()) { ?>
	<ul <?php echo $type_output ?>>
		<?php while ($query->have_posts()) { ?>
			<?php $query->the_post(); ?>

			<li>
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php
					if ( $has_thumbnail ) {
						$size = 'thumbnail';
						$ratio = blocksy_default_akg('post_widget_image_ratio', $atts, 'original');

						if ( $posts_type === 'large-thumbs' ) {
							$size = 'medium';
						}

						if ( $posts_type === 'rounded' ) {
							$ratio = '1/1';
						}

						if (
							$posts_type === 'large-small'
							&&
							$query->current_post === 0
						) {
							$size = 'medium';
							$ratio = '4/3';
						}

						if (get_post_thumbnail_id()) {
							echo wp_kses_post(
								blc_call_fn(
									['fn' => 'blocksy_image'],
									[
										'attachment_id' => get_post_thumbnail_id(),
										'ratio' => $ratio,
										'tag_name' => 'div',
										'size' => $size,
									]
								)
							);
						}
					}
					?>

					<div class="ct-entry-content">
						<div class="ct-post-title">
							<?php echo wp_kses_post(get_the_title()); ?>
						</div>

						<?php
							if (blocksy_default_akg('display_excerpt', $atts, 'no') === 'yes') {
								echo blocksy_entry_excerpt(
									blocksy_default_akg('excerpt_lenght', $atts, '10'),
									'ct-entry-excerpt',
									get_the_ID()
								);
							}
						?>

						<?php if ( $has_meta || $has_comments ) { ?>
							<div class="ct-entry-meta">
								<?php if ( $has_meta ) { ?>
									<span>
										<?php echo esc_attr(get_the_time(get_option('date_format', 'M j, Y'))); ?>

									</span>
								<?php } ?>

								<?php if ( $has_comments && get_comments_number() > 0 ) { ?>
									<span>
										<?php echo wp_kses_post( get_comments_number_text( '', __( '1 Comment', 'blocksy-companion' ), __( '% Comments', 'blocksy-companion' ) ) ); ?>
									</span>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</a>
			</li>
		<?php } ?>
	</ul>
<?php } ?>

<?php wp_reset_postdata(); ?>

<?php
	echo wp_kses_post( $after_widget );
?>
