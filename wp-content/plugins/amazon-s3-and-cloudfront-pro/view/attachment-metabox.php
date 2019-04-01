<?php
/** @var Amazon_S3_And_CloudFront|Amazon_S3_And_CloudFront_Pro $this */
/** @var array|bool $provider_object */
/** @var WP_Post $post */
/** @var array $available_actions */
/** @var bool $local_file_exists */
/** @var string $sendback */
$provider_name       = empty( $provider_object['provider'] ) ? '' : $this->get_provider_service_name( $provider_object['provider'] );
$is_current_provider = ! empty( $provider_object['provider'] ) && $this->get_provider()->get_provider_key_name() === $provider_object['provider'] ? true : false;
$provider_class      = $is_current_provider ? '' : ' error';

$is_removable    = $is_current_provider && in_array( 'remove', $available_actions );
$is_copyable     = $local_file_exists && in_array( 'copy', $available_actions ) && ( $is_current_provider || empty( $provider_object ) );
$is_downloadable = ! $local_file_exists && in_array( 'download', $available_actions ) && $is_current_provider;
?>
<div class="s3-details">
	<?php if ( ! $provider_object ) : ?>
		<div class="misc-pub-section">
			<em class="not-copied"><?php _e( 'This item has not been offloaded yet.', 'amazon-s3-and-cloudfront' ); ?></em>
		</div>
	<?php else : ?>
		<div class="misc-pub-section">
			<div class="s3-key"><?php echo $this->get_media_action_strings( 'provider' ); ?>:</div>
			<input type="text" id="as3cf-provider" class="widefat<?php echo $provider_class; ?>" readonly="readonly" value="<?php echo $provider_name; ?>">
		</div>
		<div class="misc-pub-section">
			<div class="s3-key"><?php echo $this->get_media_action_strings( 'bucket' ); ?>:</div>
			<input type="text" id="as3cf-bucket" class="widefat" readonly="readonly" value="<?php echo $provider_object['bucket']; ?>">
		</div>
		<div class="misc-pub-section">
			<div class="s3-key"><?php echo $this->get_media_action_strings( 'key' ); ?>:</div>
			<input type="text" id="as3cf-key" class="widefat" readonly="readonly" value="<?php echo $provider_object['key']; ?>">
		</div>
		<?php if ( isset( $provider_object['region'] ) && $provider_object['region'] ) : ?>
			<div class="misc-pub-section">
				<div class="s3-key"><?php echo $this->get_media_action_strings( 'region' ); ?>:</div>
				<div id="as3cf-region" class="s3-value"><?php echo $provider_object['region']; ?></div>
			</div>
		<?php endif; ?>
		<div class="misc-pub-section">
			<div class="s3-key"><?php echo $this->get_media_action_strings( 'acl' ); ?>:</div>
			<div id="as3cf-acl" class="s3-value">
				<?php echo $this->get_acl_value_string( $provider_object['acl'], $post->ID ); ?>
			</div>
		</div>
		<?php if ( $is_downloadable ) : ?>
			<div class="misc-pub-section">
				<div class="not-copied"><?php _e( 'File does not exist on server', 'amazon-s3-and-cloudfront' ); ?></div>
				<a id="as3cf-download-action" href="<?php echo $this->get_media_action_url( 'download', $post->ID, $sendback ); ?>">
					<?php echo $this->get_media_action_strings( 'download' ); ?>
				</a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="clear"></div>
</div>

<?php if ( $is_removable || $is_copyable ) : ?>
	<div class="s3-actions">
		<?php if ( $is_removable ) : ?>
			<div class="remove-action">
				<a id="as3cf-remove-action" href="<?php echo $this->get_media_action_url( 'remove', $post->ID, $sendback ); ?>">
					<?php echo $this->get_media_action_strings( 'remove' ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ( $is_copyable ) : ?>
			<div class="copy-action">
				<a id="as3cf-copy-action" href="<?php echo $this->get_media_action_url( 'copy', $post->ID, $sendback ); ?>" class="button button-secondary">
					<?php echo $this->get_media_action_strings( 'copy' ); ?>
				</a>
			</div>
		<?php endif; ?>
		<div class="clear"></div>
	</div>
<?php endif; ?>
