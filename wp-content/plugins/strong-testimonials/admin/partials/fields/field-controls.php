<?php
/**
 * Controls
 */
$is_core = ( isset( $field['core'] ) && $field['core'] );
?>
<div class="controls">
	<?php if ( $adding || ! $is_core ) : ?>
		<span><a href="#" class="delete-field"><?php _e( 'Delete' ); ?></a></span>
	<?php endif; ?>
	<span class="close-field"><a href="#"><?php _ex( 'Close', 'verb', 'strong-testimonials' ); ?></a></span>
</div>
