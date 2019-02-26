<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
	After submitting your request in the previous step, you will receive an email from Amazon Certificates, which contains a link to verify your ownership of the domain name.
</p>

<ol>
	<li>
		Click the link in the email from Amazon Certificates.
	</li>
	<li>
		On the certificate approval screen (shown below), click the <strong>I Approve</strong> link.
	</li>
</ol>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'acm-email-validation.png' ) ) ?>" alt="Validate email address">
</p>
