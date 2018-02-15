/**
 * plugin javascript
 */
(function($){$(function () {
		
	$('#dismiss').click(function(){

		$(this).parents('div.updated:first').slideUp();
		$.post('admin.php?page=pmxe-admin-settings&action=dismiss', {dismiss: true}, function (data) {
			
		}, 'html');
	});		
	
});})(jQuery);