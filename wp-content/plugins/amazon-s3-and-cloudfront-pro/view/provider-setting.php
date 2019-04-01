<?php
$tr_class = ( isset( $tr_class ) ) ? $tr_class : '';
?>

<tr class="as3cf-provider <?php echo $tr_class; ?>">
	<td><h4><?php _e( 'Provider:', 'amazon-s3-and-cloudfront' ); ?></h4></td>
	<td>
		<span id="<?php echo $prefix; ?>-active-provider" class="as3cf-active-provider">
			<?php echo $this->get_provider()->get_provider_service_name(); // xss ok ?>
		</span>
		<a href="<?php echo $this->get_plugin_page_url( array( 'action' => 'change-provider' ) ); ?>" id="<?php echo $prefix; ?>-change-provider" class="as3cf-change-provider"><?php _e( 'Change', 'amazon-s3-and-cloudfront' ); ?></a>
	</td>
</tr>
