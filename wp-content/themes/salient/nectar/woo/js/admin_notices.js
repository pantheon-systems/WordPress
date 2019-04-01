jQuery( document ).ready( function() {
	
	jQuery( document ).on( 'click', '.nectar-dismiss-notice .notice-dismiss', function() {
		var data = {
				action: 'nectar_dismiss_older_woo_templates_notice',
		};
		
		jQuery.post( notice_params.ajaxurl, data, function() {
		});
    
	})
  
});