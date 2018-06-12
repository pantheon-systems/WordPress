jQuery(window).load(function(e) {
				jQuery('.lokhal_verify_email_popup').slideDown();
		       jQuery('.lokhal_verify_email_popup_overlay').show();
			});
 jQuery(document).ready(function() {
	 jQuery('.lokhal_cancel').click(function(e) { 
	    e.preventDefault();  
		var email = jQuery('#verify_lokhal_email').val();   
		var fname = jQuery('#verify_lokhal_fname').val();   
		var lname = jQuery('#verify_lokhal_lname').val(); 
		jQuery('.lokhal_verify_email_popup').slideUp();
		jQuery('.lokhal_verify_email_popup_overlay').hide();		
		send_ajax('cancel', email, fname, lname);
    });
	 jQuery('.verify_local_email').click(function(e) { 
	    e.preventDefault();  
		var email = jQuery('#verify_lokhal_email').val(); 
		var fname = jQuery('#verify_lokhal_fname').val();   
		var lname = jQuery('#verify_lokhal_lname').val();  
		jQuery('.lokhal_verify_email_popup').slideUp();
		jQuery('.lokhal_verify_email_popup_overlay').hide();		
		send_ajax('verify', email, fname, lname);
    });
						
});
function send_ajax(todo, email, fname, lname) {
	        jQuery.ajax({
						 type : "post",
						 url : ajaxurl,
						 data : {action: "mk_duplicatepage_verify_email", 'todo' : todo, 'vle_nonce': vle_nonce, 'lokhal_email': email, 'lokhal_fname': fname, 'lokhal_lname': lname},
						 success: function(response) {
							if(response == '1') {
			alert('A confirmation link has been sent to your email address. Please click on the link to verify your email address.');
							} else if(response == '2') {
								alert('Error - Email Not Sent.');
							}
						 }
						});	
}