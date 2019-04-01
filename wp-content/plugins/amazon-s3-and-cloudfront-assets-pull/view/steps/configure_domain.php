<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<h2>What domain name would you like to serve your assets from?</h2>

<?php $this->render_setting( 'domain' ) ?>

<p>We highly recommend using a subdomain of <code data-as3cf-copy><?php echo AS3CF_Utils::current_base_domain() ?></code> for better
	<a href="https://www.keycdn.com/blog/cdn-seo-indexing-images/" target="_blank">SEO</a>.</p>
