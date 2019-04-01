<div class="as3cf-deactivate-plugin-container" style="display: none;">
	<h3><?php _e( 'WP Offload Media Uninstall', 'amazon-s3-and-cloudfront' ); ?></h3>
	<p><?php _e( 'You have the "Remove Files From Server" option ON, which means some media files are likely missing from your local server and deactivating WP Offload Media could result in broken images and file links on your site.', 'amazon-s3-and-cloudfront' ); ?></p>
	<p><?php _e( 'We recommend you copy all the media files back to your server from the bucket before deactivating. Would you like to do this now?', 'amazon-s3-and-cloudfront' ); ?></p>
	<p class="actions select">
		<button type="submit" class="button button-primary right" data-download-tool="1"><?php _e( 'Yes', 'amazon-s3-and-cloudfront' ); ?></button>
		<button type="submit" class="button right" data-download-tool="0"><?php _e( 'No', 'amazon-s3-and-cloudfront' ); ?></button>
		<span class="spinner right"></span>
	</p>
</div>