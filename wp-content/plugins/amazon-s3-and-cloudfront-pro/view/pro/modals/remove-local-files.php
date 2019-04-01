<div class="as3cf-remove-local-files-prompt">
	<h3><?php _e( 'Remove All Files From Server', 'amazon-s3-and-cloudfront' ); ?></h3>
	<p><?php _e( 'You\'ve enabled the "Remove Files From Server" option. Do you want to remove all existing files from the server that have already been offloaded?', 'amazon-s3-and-cloudfront' ); ?></p>
	<p class="actions select">
		<button type="submit" name="remove-local-files" value="1" class="button button-primary right"><?php _e( 'Yes', 'amazon-s3-and-cloudfront' ); ?></button>
		<button type="submit" name="remove-local-files" value="0" class="button right"><?php _e( 'No', 'amazon-s3-and-cloudfront' ); ?></button>
		<span class="spinner right"></span>
	</p>
</div>