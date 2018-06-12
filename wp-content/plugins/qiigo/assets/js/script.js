jQuery(document).ready(function($) {
	$(document).on('change', '#bng_location_session', function(e){
		e.preventDefault();
		$.ajax({
			type : 'post',
			url : bng_session.ajaxurl,
			data : {bng_location_session:$(this).val(), action:'action_location_post'},

		}).done(function(response){
			console.log(response);
			location.reload();

		}).fail(function(error){

			alert('Opps! '+error);

		});
	});
	$(document).on('click', '#delete_session', function(e){
		e.preventDefault();
		$.ajax({
			type : 'post',
			url : bng_session.ajaxurl,
			data : {action:'delete_location_session'},

		}).done(function(response){
			console.log(response);
			location.reload();

		}).fail(function(error){

			alert('Opps! '+error);

		});
	});

})