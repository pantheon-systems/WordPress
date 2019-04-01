<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
	The Origin settings control how the distribution will access your site for any assets which CloudFront does not have a cached version to serve.
</p>

<ol>
	<li>
		For <strong>Origin Domain Name</strong>, enter <code data-as3cf-copy><?php echo AS3CF_Utils::current_domain() ?></code>
	</li>
	<li>
		For <strong>Origin Protocol Policy</strong>, select <strong>Match Viewer</strong>.
	</li>
</ol>

<p>Everything else within the <strong>Origin Settings</strong> section can be left at the default value.</p>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'cloudfront-origin-settings.png' ) ) ?>" alt="Configuring CloudFront origin settings">
</p>
