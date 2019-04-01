<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>The Behavior settings control what CloudFront will do with the response it gets from the Origin (your site).</p>

<ol>
	<li>
		Scroll to the <strong>Default Cache Behavior Settings</strong> section.
	</li>
	<li>
		For <strong>Query String Forwarding and Caching</strong>, select <strong>Forward all, cache based on all</strong>. <br>
		<em>This is necessary to ensure the proper version of an asset is served when WordPress, plugins and themes are updated.</em>
	</li>
	<li>
		For <strong>Compress Objects Automatically</strong>, choose <strong>Yes</strong>. <br>
		<em>CloudFront will serve compressed versions of your assets to browsers that support it and uncompressed versions to browsers that don't. This can significantly reduce the size of the data needed to be downloaded by the browser making for faster load times.</em>
	</li>
</ol>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'default-cache-behavior.png' ) ) ?>" alt="CloudFront default cache behavior settings">
</p>
