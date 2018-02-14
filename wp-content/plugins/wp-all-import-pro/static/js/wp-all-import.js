/**
 * plugin javascript
 */
(function($){$(function () {
		
	$('#dismiss').click(function(){

		$(this).parents('div.updated:first').slideUp();
		$.post('admin.php?page=pmxi-admin-settings&action=dismiss', {dismiss: true}, function (data) {}, 'html');
		
	});

	$('.wpallimport-dismissible').find('button.notice-dismiss').live('click', (function(e){

    	var request = {
			action: 'dismiss_notifications',		
			security: wp_all_import_security,	
			addon: $(this).parent('div:first').attr('rel')
	    };		

	    var ths = $(this);

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: request,
			success: function(response) {
								
			},			
			dataType: "json"
		});
    }));
	
});})(jQuery);