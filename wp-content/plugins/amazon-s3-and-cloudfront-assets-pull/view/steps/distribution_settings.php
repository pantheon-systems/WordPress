<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<ol>
	<li>
		Scroll to the <strong>Distribution Settings</strong> section.
	</li>
	<li>
		Adjust the <strong>Price Class</strong> based on your requirements and budget. <br>
		<em>
			Choosing <strong>Use All Edge Locations</strong> will give you the best performance, but will
			<a href="https://aws.amazon.com/cloudfront/pricing/" target="_blank">cost</a> slightly more.
		</em>
	</li>
	<li>
		For <strong>Alternate Domain Names</strong>, enter <code data-as3cf-setting="domain" data-as3cf-copy><?php echo $this->get_setting( 'domain' ) ?></code>
		into the field.
	</li>
	<li>
		For <strong>SSL Certificate</strong>, select <strong>Custom SSL Certificate</strong>.
	</li>
	<li>
		Select your certificate from the dropdown menu (indicated by <code data-as3cf-copy>*.<span data-as3cf-setting="basedomain_ref"></span></code>). <br>
		<em>If you do not see a certificate to select for your domain, revisit the steps for
			<a href="#" data-action-wizard-goto-step="acm_request_cert">requesting a certificate</a> and
			<a href="#" data-action-wizard-goto-step="acm_email_validation">approving the certificate</a> before continuing.</em>
	</li>
	<li>
		Complete the configuration by scrolling to the end of the page and clicking the <strong>Create Distribution</strong> button in the bottom right corner.
	</li>
</ol>


<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'distribution-settings.png' ) ) ?>" alt="CloudFront distribution settings">
</p>
