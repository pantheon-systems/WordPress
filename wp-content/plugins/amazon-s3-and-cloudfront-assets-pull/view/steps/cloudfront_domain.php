<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
	CloudFront will begin to deploy your new distribution (indicated by the spinning arrows in the Status column), but this can take some time depending on the
	<strong>Price Class</strong> chosen in the previous step.
</p>

<ol>
	<li>
		Save your distribution's unique CloudFront <strong>Domain Name</strong> for use in the next step. <br>
		<em>E.g. <code>dXXXXXXXXXXXXX.cloudfront.net</code></em>
	</li>
</ol>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'cloudfront-domain.png' ) ) ?>" alt="CloudFront domain name">
</p>
