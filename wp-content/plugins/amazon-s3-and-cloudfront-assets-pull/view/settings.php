<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
/* @var string $plugin_slug */
/* @var string $settings_nonce_key */
?>
<div id="tab-assets_pull"  class="aws-content as3cf-tab">
	<?php do_action( 'as3cf_pre_tab_render', 'assets_pull' ) ?>

	<div class="as3cf-main-settings">
		<form class="as3cf-settings-form" method="post">
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="plugin" value="<?php esc_attr_e( $plugin_slug ) ?>" />
			<?php wp_nonce_field( $settings_nonce_key ) ?>

			<table class="form-table">
				<?php $this->render_setting( 'rewrite-urls' ) ?>
				<?php $this->render_setting( 'force-https' ) ?>
			</table>

			<div class="as3cf-settings-form-actions">
				<button type="submit" class="button button-primary" <?php echo $this->maybe_disable_save_button() ?>><?php _e( 'Save Changes', 'amazon-s3-and-cloudfront-assets-pull' ) ?></button>
				<a href="#" data-action-wizard-cloudfront="launch">Launch CloudFront Set Up Guide</a>
			</div>
		</form>
	</div>
</div>
