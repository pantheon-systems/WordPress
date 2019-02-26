<?php
/* @var array $errors All errors for the tool, grouped by blog_id then media_id */
/* @var string $tool Tool key the errors belong to */

if ( ! isset( $errors ) || ! is_array( $errors ) ) {
	return;
}

$all_errors = array();

foreach ( $errors as $blog_id => $blog_errors ) {
	foreach ( $blog_errors as $media_id => $messages ) {
		$all_errors[] = (object) compact( 'blog_id', 'media_id', 'messages' );
	}
}

?>

<ol class="as3cf-notice-toggle-list as3cf-<?php esc_attr_e( $tool ) ?>-notice-list" data-tool="<?php esc_attr_e( $tool ) ?>">
	<?php foreach ( $all_errors as $media_error ) :
		$this->switch_to_blog( $media_error->blog_id );
		?>

		<li class="media-error media-error-<?php esc_attr_e( $media_error->media_id ); ?>"
		    data-media-id="<?php esc_attr_e( $media_error->media_id ); ?>"
		    data-blog-id="<?php esc_attr_e( $media_error->blog_id ); ?>"

		>
			<div class="media-id">
				<strong class="media-error-title"><?php printf( __( 'Media Library Item #%1$s', 'amazon-s3-and-cloudfront' ), $media_error->media_id ) ?></strong>

				<?php if ( current_user_can( 'edit_post', $media_error->media_id ) ) : ?>
					<a class="link-inline" href="<?php echo esc_url( get_edit_post_link( $media_error->media_id ) ) ?>" target="_blank"><?php _e( 'Edit', 'amazon-s3-and-cloudfront' ) ?></a>
				<?php endif ?>

				<a href="#" class="link-inline" data-action="dismiss-item-errors"><?php _e( 'Dismiss All', 'amazon-s3-and-cloudfront' ) ?></a>
			</div>

			<ul class="media-error-messages">
				<?php foreach ( (array) $media_error->messages as $idx => $message ) : ?>
					<li class="media-error-message media-error-message-<?php echo $idx ?>" data-idx="<?php echo $idx ?>">
						<span class="media-error-message-text"><?php echo $message ?></span>
						<span class="media-error-dismiss">
							<a href="#" class="dismiss-link" data-action="dismiss-error"><?php _e( 'Dismiss', 'amazon-s3-and-cloudfront' ) ?></a>
						</span>
					</li>
				<?php endforeach ?>
			</ul>
		</li>

		<?php
		$this->restore_current_blog();

	endforeach;
	?>
</ol>