<?php
/* @var \Amazon_S3_And_CloudFront|\Amazon_S3_And_CloudFront_Pro $this */
/* @var bool $is_defined */
/* @var bool $is_set */
/* @var string $masked_licence */
$dynamic_classes = array(
	$is_defined ? 'as3cf-defined' : '',
	$is_set ? 'as3cf-saved-field' : '',
	( ! $is_defined && ! $is_set ) ? 'as3cf-licence-not-entered' : '',
);
?>
<section class="as3cf-licence">
	<form class="as3cf-licence-form" method="post">
		<h3><?php _e( 'Your License', 'amazon-s3-and-cloudfront' ) ?></h3>

		<div class="as3cf-field-wrap as3cf-licence-input-wrap <?php echo join( ' ', $dynamic_classes ) ?>">
			<input type="text" class="as3cf-licence-input code"
			       autocomplete="off"
			       value="<?php echo esc_attr( $masked_licence ) ?>"
			       <?php echo ( $is_defined || $is_set ) ? 'disabled' : '' ?>
			>
			<span class="as3cf-defined-in-config"><?php _e( 'defined in wp-config.php', 'amazon-s3-and-cloudfront' ) ?></span>
			<button class="button button-primary as3cf-activate-licence" data-as3cf-licence-action="activate"><?php _e( 'Activate License', 'amazon-s3-and-cloudfront' ) ?></button>
			<button class="button button-secondary as3cf-remove-licence" data-as3cf-licence-action="remove"><?php _e( 'Remove', 'amazon-s3-and-cloudfront' ) ?></button>
			<span data-as3cf-licence-spinner class="spinner" style="display: none;"></span>
		</div>

		<div data-as3cf-licence-feedback class="notice inline" style="display: none;">
			<!-- filled by JS -->
		</div>
	</form>
</section>