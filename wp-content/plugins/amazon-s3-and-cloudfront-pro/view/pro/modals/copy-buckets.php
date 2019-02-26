<div class="as3cf-copy-buckets-prompt">
	<?php
	if ( ! empty( $_GET['prev_action'] ) && 'change-bucket' === $_GET['prev_action'] ) {
		$back_args = array( 'action' => 'change-bucket' );
		echo '<a href="' . $this->get_plugin_page_url( $back_args ) . '">' . __( '&laquo;&nbsp;Back', 'amazon-s3-and-cloudfront' ) . '</a>';
	}
	?>
	<h3><?php _e( 'Copy Existing Files to New Bucket', 'amazon-s3-and-cloudfront' ); ?></h3>
	<p><?php _e( 'Would you like to copy media files from their current bucket to this new bucket?', 'amazon-s3-and-cloudfront' ); ?></p>
	<p class="actions select">
		<button type="submit" name="copy-buckets" value="1" class="button button-primary right"><?php _e( 'Yes', 'amazon-s3-and-cloudfront' ); ?></button>
		<button type="submit" name="copy-buckets" value="0" class="button right"><?php _e( 'No', 'amazon-s3-and-cloudfront' ); ?></button>
		<span class="spinner right"></span>
	</p>
</div>