jQuery(document).ready(function(){
	
    jQuery('.ct').hide(function(){
		jQuery('.charity-names li:first-child a.charity').click();
	});			
    
    jQuery('a.charity').click(function(event){
         event.preventDefault();
        var target = jQuery(this).attr('data-char');
		jQuery(this).parent().siblings().children().removeClass('active');
        jQuery('.ct').hide();
		jQuery(this).addClass('active');
        jQuery("#"+target).show();
    });
	
	jQuery( "#start_date" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
	jQuery( "#end_date" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
	  
});