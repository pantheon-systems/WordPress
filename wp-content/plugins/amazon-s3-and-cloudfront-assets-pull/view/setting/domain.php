<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
/* @var string $setting_key */
/* @var string $setting_value */
/* @var array $args */
$check_domain_ui = isset( $with_domain_check ) && $with_domain_check;
$last_checked    = get_site_transient( 'as3cf_assets_pull_last_checked' );
$has_checked     = is_array( $last_checked ) && ! empty( $last_checked['timestamp'] );
$check_success   = $has_checked && ! empty( $last_checked['success'] );
$check_domain    = isset( $last_checked['domain'] ) ? $last_checked['domain'] : '';
$check_message   = isset( $last_checked['message'] ) ? $last_checked['message'] : '';
$check_more_info = isset( $last_checked['more_info'] ) ? $last_checked['more_info'] : '';
$last_checked_at = $has_checked ? $this->last_checked_datetime( $last_checked['timestamp'] ) : '';
?>
<div class="<?php echo $args['tr_class'] ?>">
	<?php echo $args['setting_msg'] ?>

	<input type="text"
	       data-as3cf-setting="<?php echo esc_attr( $setting_key ) ?>"
	       data-as3cf-defined="<?php echo (int) $args['is_defined'] ?>"
	       data-as3cf-validate="domain"
	       name="<?php echo esc_attr( $setting_key ) ?>"
	       value="<?php echo esc_attr( $this->get_setting( $setting_key ) ) ?>"
	       <?php echo $args['disabled_attr'] ?>
	/>
	<?php if ( $check_domain_ui ) : ?>
	<div class="as3cf-verify-domain <?php echo $has_checked ? 'has-checked' : '' ?>" check-result="<?php echo (int) $check_success ?>">
		<div class="last-checked">
			<div class="check-result success">
				<span class="check-result-icon icon-success">
					<?php echo file_get_contents( $this->get_plugin_dir_path() . '/assets/img/icon-box-checked.svg' ) ?>
				</span>
				<span class="check-result-status">
					<?php echo sprintf( __( 'Assets are serving from %s', 'amazon-s3-and-cloudfront-assets-pull' ),
						'<span data-as3cf-bind="checked_domain">' . $check_domain . '</span>'
					) ?>
				</span>
			</div>
			<div class="check-result fail">
				<div class="notice error inline">
					<p>
						<span data-as3cf-bind="check_message"><?php echo $check_message ?></span>
						<span class="more-info">
							<a href="<?php esc_attr_e( $check_more_info ) ?>" data-as3cf-bind-href="more_info" target="_blank">More&nbsp;info&nbsp;&raquo;</a>
						</span>
					</p>
				</div>

				<span class="check-result-icon icon-fail">
					<?php echo file_get_contents( $this->get_plugin_dir_path() . '/assets/img/icon-box-x.svg' ) ?>
				</span>
				<span class="check-result-status">
					<?php echo sprintf( __( 'Assets are not serving from %s', 'amazon-s3-and-cloudfront-assets-pull' ),
						'<span data-as3cf-bind="checked_domain">' . $check_domain . '</span>'
					) ?>
				</span>

			</div>
			<div class="last-checked-at wp-ui-text-icon">
				<p>
					<em>Last checked <span data-as3cf-bind="last_checked_at"><?php echo $last_checked_at ?></span></em>&nbsp;
					<span class="check-domain">
						<a href="#" class="check-link" data-action-as3cf-check-domain>Check again</a>
					</span>
				</p>
			</div>
		</div>

		<div class="checking-domain wp-ui-text-icon">
			<p>
				<span class="spinner is-active"></span>
				Checking if assets are serving from <span data-as3cf-setting="domain"><?php echo $setting_value ?></span>...
			</p>
		</div>

		<a href="#" class="check-link check-first" data-action-as3cf-check-domain>Test domain configuration</a>
	</div>
	<?php endif ?>
	<span class="as3cf-validation-error" style="display: none;">
		<?php _e( 'Invalid character. Letters, numbers, periods and hyphens are allowed.', 'amazon-s3-and-cloudfront' ) ?>
	</span>
</div>
