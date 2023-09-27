<?php
/**
 * Comments helpers.
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

add_filter('comment_form_defaults', function ($defaults) {
	ob_start();
	do_action('blocksy:comments:title:before');
	$title_before = ob_get_clean();

	ob_start();
	do_action('blocksy:comments:title:after');
	$title_after = ob_get_clean();

	$prefix = blocksy_manager()->screen->get_prefix();
	$default_has_comments_website = 'yes';

	if ($prefix === 'product') {
		$default_has_comments_website = 'no';
	}

	$has_website_field = get_theme_mod(
		$prefix . '_has_comments_website',
		$default_has_comments_website
	);

	$website_field_class = '';

	if ($has_website_field === 'yes') {
		$website_field_class = 'has-website-field';
	}

	$label_position = get_theme_mod($prefix . '_comments_label_position', 'inside');
	$label_position_class = 'has-labels-' . $label_position;

	$defaults['format'] = 'xhtml';
	$defaults['class_form'] = 'comment-form ' . $website_field_class . ' ' . $label_position_class;
	$defaults['title_reply'] = __('Leave a Reply', 'blocksy');
	$defaults['cancel_reply_link'] = __('Cancel Reply', 'blocksy');

	// Title reply
	$defaults['title_reply_before'] = $title_before . '<h2 id="reply-title" class="comment-reply-title">';
	$defaults['title_reply_after'] = '</h2>' . $title_after;

	// Cancel reply
	$defaults['cancel_reply_before'] = '<span class="ct-cancel-reply">';
	$defaults['cancel_reply_after'] = '</span>';

	// Textarea
	$defaults['comment_field'] =
		'<p class="comment-form-field-textarea">
			<label for="comment">' . __( 'Add Comment', 'blocksy' ) . '</label>
			<textarea id="comment" name="comment" cols="45" rows="8" required="required">' . '</textarea>
		</p>';

	// submit button
	$defaults['submit_button'] = '<button type="submit" name="%1$s" id="%2$s" class="%3$s" value="%4$s">%4$s</button>';

	if (
		has_action('set_comment_cookies', 'wp_set_comment_cookies')
		&&
		get_option('show_comments_cookies_opt_in')
	) {
		$consent = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';

		$defaults['comment_field'] .= '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . '>' .
			'<label for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.', 'blocksy') . '</label></p>';
	}

	if (function_exists('blocksy_ext_cookies_checkbox')) {
		$defaults['comment_field'] .= blocksy_ext_cookies_checkbox('comment');
	}

	return $defaults;
}, 5);

/**
 * Reorder respond form fields
 */
add_filter(
	'comment_form_fields',
	function ($fields) {
		if (strpos($fields['comment'], 'rating') !== false) {
			return $fields;
		}

		$comment_field = $fields['comment'];
		unset($fields['comment']);
		$fields['comment'] = $comment_field;

		if (isset($fields['obr_hlc'])) {
			$comment_field = $fields['obr_hlc'];
			unset($fields['obr_hlc']);
			$fields['obr_hlc'] = $comment_field;
		}

		$commenter = wp_get_current_commenter();
		$req = get_option('require_name_email');
		$aria_req = ($req ? " required='required'" : '');

		$prefix = blocksy_manager()->screen->get_prefix();
		$has_website_field = get_theme_mod(
			$prefix . '_has_comments_website',
			'yes'
		);

		$fields['author'] =
			'<p class="comment-form-field-input-author">
			<label for="author">' . __( 'Name', 'blocksy' ) . ' <b class="required">&nbsp;*</b></label>
			<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . '>
			</p>';

		// Email input
		$fields['email'] =
			'<p class="comment-form-field-input-email">
				<label for="email">' . __( 'Email', 'blocksy' ) . ' <b class="required">&nbsp;*</b></label>
				<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . '>
			</p>';

		$website_field_output = '';

		if ($has_website_field === 'yes') {
			$website_field_output =
				'<p class="comment-form-field-input-url">
				<label for="url">' . __( 'Website', 'blocksy' ) . '</label>
				<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30">
				</p>';
		}

		$fields['url'] = $website_field_output;
		$fields['cookies'] = '';

		return $fields;
	}
);


/**
 * Comment view.
 *
 * @param object $comment comment instance.
 * @param array  $args comment display args.
 * @param int $depth current depth of the comments.
 */
if (! function_exists('blocksy_custom_comment_template')) {
function blocksy_custom_comment_template($comment, $args, $depth) {
	$is_by_author = get_the_author_meta( 'email' ) === $comment->comment_author_email;

	$has_avatar = (
		0 !== $args['avatar_size']
		&&
		get_comment_type($comment) === 'comment'
		&&
		get_option('show_avatars', 1)
	);

	$class = '';

	if ($has_avatar) {
		$class = 'ct-has-avatar';
	}

	if ($is_by_author) {
		$class .= ' ct-author-comment';
	}

	?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class($class); ?>>
		<article class="ct-comment-inner" id="ct-comment-inner-<?php comment_ID(); ?>">

			<footer class="ct-comment-meta">
				<?php
					if ($has_avatar) {
						$avatar_alt = $comment->comment_author;

						if ($comment->user_id !== 0) {
							$maybe_alt = blocksy_get_avatar_alt_for($comment->user_id);

							if (! empty($maybe_alt)) {
								$avatar_alt = $maybe_alt;
							}
						}

						echo blocksy_simple_image(
							get_avatar_url(
								$comment,
								['size' => $args['avatar_size']]
							),
							[
								'tag_name' => 'figure',
								'img_atts' => [
									'width' => intval($args['avatar_size']),
									'height' => intval($args['avatar_size']),
									'alt' => $avatar_alt
								],
							]
						);
					}
				?>

				<h4 class="ct-comment-author">
					<?php echo get_comment_author_link(); ?>
				</h4>

				<div class="ct-comment-meta-data">
					<a href="<?php echo esc_attr( get_comment_link( $comment->comment_ID ) ); ?>">
						<?php
							printf(
								/* translators: 1: date, 2: time */
								wp_kses_post( __( '%1$s / %2$s', 'blocksy' ) ),
								wp_kses_post( get_comment_date() ),
								wp_kses_post( get_comment_time() )
							);
						?>
					</a>

					<?php edit_comment_link( __( 'Edit', 'blocksy' ), '  ', '' ); ?>

					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'ct-comment-inner',
								'reply_text' => __('Reply', 'blocksy'),
								'depth' => $depth,
								'max_depth' => $args['max_depth'],
							)
						)
					)
					?>
				</div>
			</footer>


			<div class="ct-comment-content entry-content">
				<?php comment_text(); ?>

				<?php if ( '0' === $comment->comment_approved ) : ?>
					<em class="ct-awaiting-moderation">
						<?php esc_html_e( 'Your comment is awaiting moderation.', 'blocksy' ); ?>
					</em>
				<?php endif; ?>
			</div>

		</article>
	<?php
}
}
