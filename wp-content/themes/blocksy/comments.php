<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Blocksy
 */

if ( post_password_required() ) {
	return;
}

$entity_singular = 'comment';
$entity_plural = 'comments';

$commenter = wp_get_current_commenter();

$prefix = blocksy_manager()->screen->get_prefix();

$comments_position = get_theme_mod($prefix . '_comments_position', 'below');

do_action('blocksy:comments:before');

?>


<div class="ct-comments" id="comments">
	<?php do_action('blocksy:comments:top'); ?>

	<?php if ( have_comments() ) : ?>
		<h3 class="ct-comments-title">
			<?php comments_number( esc_html__( 'No comments yet', 'blocksy' ), __( 'One comment', 'blocksy' ), __( '% Comments', 'blocksy' ) ); ?>
		</h3>
	<?php endif; // have_comments() ?>

	<?php if ( $comments_position === 'above' ) comment_form(); ?>

	<?php if ( have_comments() ) : ?>
		<ol class="ct-comment-list">
			<?php
				wp_list_comments(
					[
						'short_ping'  => true,
						'avatar_size' => 100,
						'callback' => 'blocksy_custom_comment_template',
						'end-callback' => function () {
							echo '</li>';
						}
					]
				);
			?>
		</ol>

		<?php
		// Are there comments to navigate through?
		if (get_comment_pages_count() > 1 && get_option('page_comments')) :
			?>
			<nav class="ct-comment-navigation-container">
				<h4 class="screen-reader-text section-heading">
				<?php esc_html_e( 'Comment navigation', 'blocksy' ); ?>
				</h4>

				<div class="ct-comments-navigation">
					<span class="prev">
					<?php previous_comments_link( __( '&larr; Older Comments', 'blocksy' ) ); ?>
					</span>

					<span class="next">
					<?php next_comments_link( __( 'Newer Comments &rarr;', 'blocksy' ) ); ?>
					</span>
				</div>
			</nav>
		<?php endif; // Check for comment navigation ?>

		<?php if (! comments_open() && get_comments_number()) : ?>
			<p class="no-comments">
				<?php esc_html_e( 'Comments are closed.', 'blocksy' ); ?>
			</p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php if ( $comments_position === 'below' ) comment_form(); ?>

	<?php do_action('blocksy:comments:bottom'); ?>

</div>

<?php do_action('blocksy:comments:after'); ?>
