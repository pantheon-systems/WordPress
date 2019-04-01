<?php
/* @var array $localize_data */
?>
<script>
(function( $ ) {
	var data = <?php echo wp_json_encode( (array) $localize_data ) ?>;

	$( data.container_selector ).on( 'click', '.notice-dismiss,a', function() {
		$.post( ajaxurl, {
			action: data.action,
			_ajax_nonce: data.nonce
		} );
	} )
})( jQuery );
</script>