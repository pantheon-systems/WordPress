<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>Now we are going to configure the DNS for your chosen subdomain so assets are served through CloudFront using your own domain.</p>

<ol>
	<li>
		Log into your DNS provider, and navigate to the DNS records for the <code data-as3cf-copy><span data-as3cf-setting="basedomain_ref"></span></code> domain.
		<br>
		<em>Steps will vary by provider.</em>
	</li>
	<li>
		Create a <strong>CNAME</strong> record for the <code data-as3cf-setting="subdomain_ref" data-as3cf-copy></code> subdomain to your CloudFront distribution's domain name.
	</li>
</ol>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'cname-config.png' ) ) ?>" alt="CNAME DNS record configuration">
</p>
