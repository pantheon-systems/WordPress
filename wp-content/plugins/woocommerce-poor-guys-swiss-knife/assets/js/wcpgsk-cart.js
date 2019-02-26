jQuery(document).ready(function() {
	jQuery('#wcpgsk_confirm_empty_cart').on('click', function(e) {		
		jQuery(this).toggle();
		jQuery('#wcpgsk_empty_cart').toggle();
	});

});