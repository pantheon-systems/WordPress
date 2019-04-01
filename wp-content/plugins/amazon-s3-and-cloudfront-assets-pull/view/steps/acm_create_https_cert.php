<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
	For maximum security and performance*, we highly recommend serving your assets via HTTPS.
	To get started, we need to request a free SSL certificate for your CloudFront distribution using the AWS Certificate Manager.
</p>

<p>
	To request a certificate, go to <a href="https://console.aws.amazon.com/acm/home">AWS Certificate Manager</a> and click the
	<strong>Get Started</strong> button.
	If this is not your first certificate with Amazon's Certificate Manager, click <strong>Request a certificate</strong>.
</p>

<p>
	<strong>IMPORTANT:</strong> Make sure you switch to the “US East (N. Virginia)” region using the region selector at the top right, CloudFront only works with certificates that are created in that “global” region.
</p>

<p class="wp-ui-text-icon">
	<em>
		* Using HTTPS with CloudFront lets your visitors benefit from significantly faster load times by leveraging HTTP/2 where possible.
	</em>
	<a href="#" data-action-wizard-goto-step="create_distribution">Skip this step</a>
</p>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'acm-get-started.png' ) ) ?>" alt="Get Started">
</p>
