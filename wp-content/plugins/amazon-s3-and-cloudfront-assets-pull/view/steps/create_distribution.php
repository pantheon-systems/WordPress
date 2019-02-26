<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
	Now we will create a CloudFront distribution which will "pull" and cache assets from your site on-demand.
</p>

<ol>
	<li>
		Navigate to the <a href="https://console.aws.amazon.com/cloudfront/home" target="_blank">CloudFront</a> service.
	</li>
	<li>
		Click the <strong>Create Distribution</strong> button.
	</li>
	<li>
		Under <strong>Web</strong>, click the <strong>Get Started</strong> button.
	</li>
</ol>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'cloudfront-create-distribution.png' ) ) ?>" alt="Create CloudFront web distribution">
</p>
