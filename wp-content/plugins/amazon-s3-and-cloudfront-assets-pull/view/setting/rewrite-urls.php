<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
/* @var string $setting_key */
/* @var mixed $setting_value */
/* @var array $args */
?>
<tr class="<?php esc_attr_e( $args['tr_class'] ) ?>">
	<td>
		<?php $this->render_view( 'checkbox', $args ) ?>
	</td>
	<td>
		<?php echo $args['setting_msg'] ?>
		<h4><?php _e( 'Rewrite Asset URLs', 'amazon-s3-and-cloudfront-assets-pull' ) ?></h4>

		<p class="rewrite-urls-desc">
			<?php _e( 'Change the URLs of any enqueued asset files to the following domain.', 'amazon-s3-and-cloudfront-assets-pull' ) ?>
			<?php echo $this->assets_more_info_link( $setting_key, 'assets+pull+rewrite+urls' ) ?>
		</p>

		<?php $this->render_setting( 'domain', array( 'with_domain_check' => true ) ) ?>
	</td>
</tr>