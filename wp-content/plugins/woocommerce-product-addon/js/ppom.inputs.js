/**
 * PPOM input scripts
 * 
 **/
 
 "use strict"
 
 var ppom_bulkquantity_meta = '';
 var ppom_pricematrix_discount_type = '';
 
 jQuery(function($){
     
    // $('[data-toggle="tooltip"]').tooltip({container:'body', trigger:'hover'});
    
    // Measure
    $('.ppom-measure').on('change', '.ppom-measure-unit', function(e){
        
        e.preventDefault();
        // console.log($(this).text());
        
        $(this).closest('.ppom-measure').find('.ppom-measure-input').trigger('change');
    });
    
    // Disable ajax add to cart
    $(".add_to_cart_button").removeClass("ajax_add_to_cart")
     
    // Range slider updated
    $(document).on('ppom_range_slider_updated', function(e){ 
        
        wc_product_qty.val(e.qty);
        ppom_update_option_prices();
    });
    
    // move modals to body bottom
    if( $('.ppom-modals').length > 0 ) {
         $('.ppom-modals').appendTo('body');
    }
    
    $.each(ppom_input_vars.ppom_inputs, function(index, input){
         
        // console.log(input.type);
        var InputSelector = $("#"+input.data_name);
        
        // Applying JS on inputs
        switch( input.type ) {
            
            // masking
            case 'text':
                if(input.type === 'text' && input.input_mask !== undefined && input.input_mask !== '') {
                    InputSelector.inputmask( input.input_mask  );
                }
                break;
                
            case 'date':
                if( input.jquery_dp === 'on'){
                
                    InputSelector.datepicker("destroy");
                    InputSelector.datepicker({
                        changeMonth: true,
        				changeYear: true,
        				dateFormat: input.date_formats,
        				yearRange: input.year_range,
                    });
                    
                    if( input.past_dates === 'on' ) {
                        var date_today = new Date();
                        InputSelector.datepicker('option', 'minDate', date_today);
                    }
                    if( input.no_weekends === 'on' ) {
                        InputSelector.datepicker('option', 'beforeShowDay', $.datepicker.noWeekends);
                    }
                }
                break;
                
            case 'image':
                // Image Tooltip
                if( input.show_popup === 'on') {
                    $('.ppom-zoom').imageTooltip({
    							  xOffset: 5,
    							  yOffset: 5
    						    });
                }
						    
				// Data Tooltip
				// $(".pre_upload_image").tooltip({container: 'body'});
                break;
            // date_range
            case 'daterange':
                InputSelector.daterangepicker({
                    autoApply: (input.auto_apply == 'on') ? true : false,
                    locale: {
                      format: (input.date_formats !== '') ? input.date_formats : "YYYY-MM-DD"
                    },
                    showDropdowns: (input.drop_down == 'on') ? true : false,
                    showWeekNumbers: (input.show_weeks == 'on') ? true : false,
                    timePicker: (input.time_picker == 'on') ? true : false,
                    timePickerIncrement: (input.tp_increment !== '') ? parseInt(input.tp_increment) : '',
                    timePicker24Hour: (input.tp_24hours == 'on') ? true : false,
                    timePickerSeconds: (input.tp_seconds == 'on') ? true : false,
                    drops: (input.open_style !== '') ? input.open_style : 'down',
                    startDate: input.start_date,
                    endDate: input.end_date,
                    minDate: input.min_date,
                    maxDate: input.max_date,
                });
                break;
                
            // color: iris
            case 'color':
                
                InputSelector.css( 'background-color', input.default_color);
                var iris_options = {
                    'palettes': ppom_get_palette_setting(input),
                    'hide'  : input.show_onload == 'on' ? false : true,
                    'color' : input.default_color,
                    'mode' : input.palettes_mode != '' ? input.palettes_mode : 'hsv',
                    'width': input.palettes_width != '' ? input.palettes_width : 200,
                    change: function(event, ui) {
                        
                        InputSelector.css( 'background-color', ui.color.toString());
                        InputSelector.css( 'color', '#fff');   
                    }
                }
    
                InputSelector.iris(iris_options);
                break;
                
            // Palettes
            case 'palettes':
                
                // $(".ppom-single-palette").tooltip({container: 'body'});
                break;
            // Bulk quantity
            case 'bulkquantity':
                
                setTimeout(function() { $('.quantity.buttons_added').hide(); }, 50);
                $('form.cart').find('.quantity').hide();
				
				// setting formatter
				/*if ($('form.cart').closest('div').find('.price').length > 0){
					wc_price_DOM = $('form.cart').closest('div').find('.price');
				}*/

                ppom_bulkquantity_meta = input.options;				
				// Starting value
				ppom_bulkquantity_price_manager(1);
                break;
                
            case 'pricematrix':
                
                ppom_pricematrix_discount_type = input.discount_type;
               
                if( input.show_slider === 'on' ) {
                    var slider = new Slider('.ppom-range-slide', {
                       formatter: function(value){
                           jQuery.event.trigger({
                            	type: "ppom_range_slider_updated",
                            	qty: value,
                            	time: new Date()
                            });
                           return ppom_input_vars.text_quantity+": "+value;
                       }
                    });
                }
                break;
        }
        
        
     });
       
 });
 

 
 function ppom_get_palette_setting(input){
     
     var palettes_setting = false;
     // first check if palettes is on
     if(input.show_palettes === 'on'){
         palettes_setting = true;
     }
     if(palettes_setting && input.palettes_colors !== ''){
         palettes_setting = input.palettes_colors.split(',');
     }
     
     return palettes_setting;
 }
 
function ppom_get_field_type_by_id( field_id ) {
 
 var field_type = '';
 jQuery.each(ppom_input_vars.ppom_inputs, function(i, field){
    
     if( field.data_name === field_id ) {
         field_type = field.type;
         return;
     }
 });
 
 return field_type;
}

// Get all field meta by id
function ppom_get_field_meta_by_id( field_id ) {
 
 var field_meta = '';
 jQuery.each(ppom_input_vars.ppom_inputs, function(i, field){
    
     if( field.data_name === field_id ) {
         field_meta = field;
         return;
     }
 });
 
 return field_meta;
}

function ppom_get_field_meta_by_type( type ) {
 
 var field_meta = Array();
 jQuery.each(ppom_input_vars.ppom_inputs, function(i, field){
    
     if( field.type === type ) {
         field_meta.push(field);
         return;
     }
 });
 
 return field_meta;
}

function ppom_bulkquantity_price_manager( quantity ){
			         
    // 	console.log(ppom_bulkquantity_meta);
	jQuery('.ppom-bulkquantity-qty').val(quantity);

	var ppom_base_price = 0;
	jQuery.each(JSON.parse(ppom_bulkquantity_meta), function(idx, obj) {
	    
		var qty_range       = obj['Quantity Range'].split('-');
		var qty_range_from  = qty_range[0];
		var qty_range_to    = qty_range[1];
		
		if (quantity >= parseInt(qty_range_from) && quantity <= parseInt(qty_range_to)) {

			// Setting Initial Price to 0 and taking base price
			var price = 0;
			ppom_base_price = (obj['Base Price'] == undefined || obj['Base Price'] == '') ? 0 : obj['Base Price'];
			jQuery('.ppom-bulkquantity-options option:selected').attr('data-baseprice', ppom_base_price);

			// Taking selected variation price
			var variation = jQuery('.ppom-bulkquantity-options').val();
			var var_price = obj[variation];
			jQuery('.ppom-bulkquantity-options option:selected').attr('data-price', var_price);
		}
		
	});
	
	ppom_update_option_prices();
}