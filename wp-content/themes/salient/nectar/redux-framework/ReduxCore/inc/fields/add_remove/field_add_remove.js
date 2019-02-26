jQuery(document).ready(function($){
   
   /*nectar addition - added check to see if input exists on page to not throw js error in customizer */
   var count = ($('.add-remove-controls input[type=hidden]').length > 0) ? $('.add-remove-controls input[type=hidden]').val() : 1;
   /*nectar addition end*/ 
   if(count.length == 0) count = 1;
 
   
   $('.add-remove-controls .add').click(function(){
   		
   		if(count < 10){
   			$('.add-remove-controls .remove').stop(true,true).fadeIn();
   			count++;
   		}
   		if(count == 10) {
   			$('.add-remove-controls .add').hide();
   		}

   		$('#map-point-'+count).parents('tr').fadeIn();
   		if( $('#map-point-'+count).attr('checked') == 'checked') $('#map-point-'+count).parents('tr').nextAll('tr').stop(true,true).fadeIn();
   		
   		return false;
   });
   
   
   $('.add-remove-controls .remove').click(function(){
		
   		$('#map-point-'+count).parents('tr').fadeOut();
   		if( $('#map-point-'+count).attr('checked') == 'checked') $('#map-point-'+count).parents('tr').nextAll('tr').stop(true,true).fadeOut();
   		
   		if(count > 1){
   			$('.add-remove-controls .add').stop(true,true).fadeIn();
   			count--;
   		}
   		if(count == 1) {
   			$('.add-remove-controls .remove').hide();
   		}

   	
   		return false;
   });
   
   
   //update the value for saving
   $('.add-remove-controls .remove, .add-remove-controls .add').click(function(){
   	   $('.add-remove-controls input[type=hidden]').attr('value',count);	
   });
   
   //init
   $('#map-point-'+count).parents('tr').nextAll('tr').not('tr:has(".add-remove-controls"), tr:has("#map-greyscale"), tr:has("#map-color")').hide();
   if(count == 1){ $('.add-remove-controls .remove').hide(); }
   
});
