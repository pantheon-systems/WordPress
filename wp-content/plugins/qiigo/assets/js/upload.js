function bng_validate_file(val)
{
	return true;
}


jQuery(document).ready(function($) {
	$(document).on('click', '#bng_fields_settings_page input[type="submit"]', function(e){
		e.preventDefault();
		var data = $('#bng_source_file').val();
		var success = bng_validate_file(val);
		if(success === true){
			$.ajax({
				beforeSend	: function(){
					$.LoadingOverlay("show",{
						image : "../wp-content/plugins/bng-migration-tool/assets/img/loading.gif"
					});
				},
				url 		: 'wp-admin.php',
				type 		: 'post',
				data 		: 'action=bng_process_file',
				dataType 	: 'json'
			}).done(function(response){
				var my_class  = (response.success) ? 'bng_success' : 'bng_error';
				$.LoadingOverlay("hide");
				$('#bng_response_proccess').remove();
				$('#bng_fields_settings_page').prepend('<div id="bng_response_proccess" class="'+my_class+'">'+response.message+'</div>');

			}).fail(function(err){
				var n = noty({
				    text: err.responseText,
				    animation: {
				        open: {height: 'toggle'},
				        close: {height: 'toggle'},
				        easing: 'swing',
				        speed: 500 
				    }
				});
			});
			
		}else{
			var n = noty({
				text: success,
				animation: {
				    open: {height: 'toggle'},
				    close: {height: 'toggle'},
				    easing: 'swing',
				    speed: 500 
				}
			});
		}

	});	


	

});