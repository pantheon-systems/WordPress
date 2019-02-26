<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
    If your theme uses web fonts you may experience issues with CORS.
    If you notice that font files aren't loading correctly, or receive the following error in the browser console then you need to modify your server configuration.
</p>

<p>
    <img src="<?php echo esc_url( $this->get_step_media_url( 'cors-errors.png' ) ) ?>" alt="CORS errors in the browser console">
</p>

<p>
    <strong>Please refer to <a href="<?php echo $this->dbrains_url( '/wp-offload-media/doc/configure-cors-to-resolve-web-font-issues/' ) ?>" target="_blank">this guide</a> on how to configure your web server.</strong>
</p>
