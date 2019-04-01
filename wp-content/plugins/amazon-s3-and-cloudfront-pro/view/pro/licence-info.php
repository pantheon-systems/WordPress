<section class="as3cf-licence-info support support-section">
	<h3 class="as3cf-section-heading"><?php _e( 'Email Support', 'amazon-s3-and-cloudfront' ); ?></h3>

	<?php /* Must use "support-content" class as required by markup in API response. */ ?>
	<div class="support-content">
		<?php if ( ! empty( $licence ) ) : ?>
			<p>
				<?php _e( 'Fetching support form for your license, please wait...', 'amazon-s3-and-cloudfront' ); ?>
				<span data-as3cf-licence-spinner class="spinner"></span>
			</p>
		<?php else : ?>
			<p>
				<?php _e( 'We couldn\'t find your license information.', 'amazon-s3-and-cloudfront' ); ?>
				<a href="#licence">
					<?php _e( 'Please enter a valid license key.', 'amazon-s3-and-cloudfront' ); ?>
				</a>
			</p>
			<p><?php _e( 'Once entered, you can view your support details.', 'amazon-s3-and-cloudfront' ); ?></p>
		<?php endif; ?>
	</div>
</section>