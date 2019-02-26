<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
/* @var string $setting_key */
/* @var array $args */
?>
<tr class="configure-url as3cf-border-bottom url-preview <?php echo esc_attr( $args['tr_class'] ) ?>">
	<td>
		<?php $this->render_view( 'checkbox', $args ) ?>
	</td>
	<td>
		<?php echo $args['setting_msg'] ?>
		<h4><?php _e( 'Force HTTPS', 'amazon-s3-and-cloudfront' ) ?></h4>
		<p>
			<?php _e( 'By default we use HTTPS when the request is HTTPS and regular HTTP when the request is HTTP, but you may want to force the use of HTTPS always, regardless of the request.', 'amazon-s3-and-cloudfront' ) ?>
			<?php echo $this->assets_more_info_link( $setting_key, 'assets+pull+force+https' ) ?>
		</p>
	</td>
</tr>