<?php
$title              = isset( $title ) ? $title : '';
$more_info          = isset( $more_info ) ? $more_info : '';
$progress_percent   = isset( $progress_percent ) ? (int) $progress_percent : 0;
$is_queued          = isset( $is_queued ) ? $is_queued : false;
$is_paused          = isset( $is_paused ) ? $is_paused : false;
$is_cancelled       = isset( $is_cancelled ) ? $is_cancelled : false;
$status_description = isset( $status_description ) ? $status_description : '';
$busy_description   = isset( $busy_description ) ? $busy_description : '';
$button             = isset( $button ) ? $button : '';
?>

<div class="block-title-wrap <?php echo ! empty( $more_info ) ? 'with-description' : ''; ?>">
	<h4 class="block-title"><?php echo $title; ?></h4>

	<?php if ( ! empty ( $more_info ) ) : ?>
		<a href="#" class="general-helper"></a>
		<div class="helper-message">
			<?php echo $more_info; ?>
		</div>
	<?php endif; ?>
</div>

<p class="block-description">
	<?php echo $status_description; ?>
</p>

<div class="progress-bar-wrapper <?php echo $is_paused ? 'paused' : ''; ?>" style="display: <?php echo $is_queued ? 'block' : 'none'; ?>;">
	<div class="progress-bar" style="width: <?php echo esc_attr( $progress_percent ); ?>%;"></div>
</div>

<?php if ( ! empty( $button ) ) : ?>
	<div class="button-wrapper <?php echo $is_queued ? 'processing' : ''; ?>">
		<a href="#" id="as3cf-<?php echo $slug; ?>-start" class="start button" data-busy-description="<?php echo $busy_description; ?>">
			<?php echo $button; ?>
		</a>
		<a href="#" id="as3cf-<?php echo $slug; ?>-pause" class="pause pause-resume button" data-busy-description="<?php _e( 'Pausing&hellip;', 'amazon-s3-and-cloudfront' ); ?>" style="display: <?php echo ($is_queued && ! $is_paused) ? 'inline-block' : 'none'; ?>;">
			<?php _e( 'Pause', 'amazon-s3-and-cloudfront' ); ?>
		</a>
		<a href="#" id="as3cf-<?php echo $slug; ?>-resume" class="resume pause-resume button" data-busy-description="<?php _e( 'Resuming&hellip;', 'amazon-s3-and-cloudfront' ); ?>" style="display: <?php echo ($is_queued && $is_paused) ? 'inline-block' : 'none'; ?>;">
			<?php _e( 'Resume', 'amazon-s3-and-cloudfront' ); ?>
		</a>
		<a href="#" id="as3cf-<?php echo $slug; ?>-cancel" class="cancel button" data-busy-description="<?php _e( 'Cancelling&hellip;', 'amazon-s3-and-cloudfront' ); ?>">
			<?php _e( 'Cancel', 'amazon-s3-and-cloudfront' ); ?>
		</a>
	</div>
<?php endif; ?>