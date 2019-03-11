"use strict"
 
jQuery(function($){
    
//   console.log('loaded cart');

    var ppom_cart_validated = false;
    
    if($.blockUI !== undefined){ 
        $.blockUI.defaults.message = "";
    }
   
   $('form.cart').on('submit', function(e) {
      
       if( ppom_cart_validated ) return true;
       
       e.preventDefault();
       
       // Removing validation div 
       $(".ppom-ajax-validation").remove();
       $('form.cart').block();
       
       var data = $(this).serialize();
       $.post(ppom_input_vars.ajaxurl, data, function( notices ) {
           
           $('form.cart').unblock();
           if( notices.status == 'error' ) {
               
               var show_notice = $('<div/>')
                                .addClass('woocommerce-notices-wrapper ppom-ajax-validation')
                                .css('clear','both')
                                .css('margin-top', '5px')
                                .html(notices.message)
                                .appendTo('form.cart');
                                
           } else {
               
                ppom_cart_validated = true;
                $('button[name="add-to-cart"]').trigger('click');
           }
           
       });
       
   }); 
});