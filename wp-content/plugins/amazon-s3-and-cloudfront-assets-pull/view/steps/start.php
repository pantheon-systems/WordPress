<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
/* @var string $icon_url */
?>
<div>
	<div style="float: left; margin: 0 1em 1em 0">
		<img class="assets-logo" src="<?php echo esc_attr( $icon_url ) ?>">
	</div>
	<h2>Thanks for installing the Assets addon! 🎉</h2>
	<p>The Assets addon will guide you through the set up of Amazon CloudFront. Once complete, it will start rewriting URLs to your enqueued styles and scripts so that they are served from CloudFront rather than your server.</p>
	<p>If you prefer to use a CDN other than CloudFront, you can skip the set up, go directly to the settings page, and enter the domain name you've configured with your other CDN.</p>
	<div class="clearfix"></div>
</div>

<h2>CloudFront Set Up</h2>
<p>
	The following steps will walk you through the process of setting up the Assets addon with Amazon CloudFront.
	Each step is clearly outlined with screenshots to guide you through these 3 basic processes:
</p>
<ul>
	<li>Choosing and configuring your domain name to use for serving your assets</li>
	<li>Requesting a free SSL certificate through the Amazon Certificate Manager</li>
	<li>Creating and configuring a new CloudFront distribution to serve your assets</li>
</ul>
<p>
	The whole process usually takes about 15-30 minutes, depending on your pace, and external factors (such as DNS propagation).
</p>

<div class="notice notice-info inline as3cf-click-to-copy-show" style="display: none">
    <p>
        Text that looks like <code data-as3cf-copy>this</code> can be clicked to copy it to your clipboard for easy pasting. <code data-as3cf-copy>Try me!</code>
    </p>
</div>
<p>
	When you're ready to begin, click <strong>Get Started</strong> below.
</p>
