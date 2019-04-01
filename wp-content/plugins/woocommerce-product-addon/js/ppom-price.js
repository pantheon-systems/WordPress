"use strict"

var PPOMWrapper = jQuery(".ppom-wrapper");
var ppomPriceListContainer = '';
var ppomPriceListContainerRow = '';
// Quantity update capture/update price change
var wc_product_qty  = jQuery('form.cart').find('input[name="quantity"]');
var ppom_product_base_price = ppom_input_vars.wc_product_price;

jQuery(function($){
    
    ppom_update_option_prices();
    
    // If quantity is changing with some -/+ elements
    $("form.cart .quantity").on('click', function(e){
       e.preventDefault();
       ppom_update_option_prices();
    });
    
    wc_product_qty.on('change keyup', function(e){
    
        ppom_update_option_prices();
        

        $.event.trigger({type: "ppom_wc_qty_updated",
                                qty: ppom_get_order_quantity(),
                                time: new Date()
                                });
    });
    
    
    $('.ppom-wrapper').on('change', 'select,input:checkbox,input:radio', function(e){
       
        ppom_update_option_prices();
   });
   
   PPOMWrapper.find('input[type="number"]').on('keyup change', function(e){
       
        ppom_update_option_prices();
   });

   
   // quantities input changes
    if($('.ppom-input-quantities').length > 0){
        $('input.ppom-quantity').on('click keyup', function(){
        	
        	ppom_update_option_prices();
        });
    }
    
    // Woocommmerce vartiation update events
    $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
        // Fired when the user selects all the required dropdowns / attributes
        // and a final variation is selected / shown
        
        // console.log(variation);
        
        ppom_product_base_price = variation.display_price;
        ppom_update_variation_quatity(variation.display_price);
        ppom_update_option_prices();
    } );
    
    
    $( ".single_variation_wrap" ).on( "hide_variation", function ( event, variation ) {
        // Fired when the user selects all the required dropdowns / attributes
        // and a final variation is selected / shown
        
        // console.log(variation);
        
        ppom_product_base_price = 0;
        ppom_update_option_prices();
    } );
    
    
    // Measue input creating price-checkbox on change
    $('.ppom-wrapper').on('click change keyup', '.ppom-measure-input', function() {
       
       var data_name = $(this).attr('id');
       var m_qty    = $(this).val();
       
        // console.log(use_units);
        $('input:radio[name="ppom[unit]['+data_name+']"]:checked').attr('data-qty', m_qty);
        ppom_update_option_prices();
    });
    
    
    // Delete option from price table
    // Since 16.4
    $(document).on('click', '.ppom-delete-option-table', function(e) {
       
       var field_name   = $(this).closest('tr').attr('data-data_name');
       var option_id    = $(this).closest('tr').attr('data-option_id');
       if( field_name ) {
           
           ppom_delete_option_from_price_table( field_name, option_id );
       }
    });
});


function ppom_update_option_prices() {
    
    if( ppom_input_vars.show_option_price == 'hide' ) {
        
        jQuery("#ppom-price-container").hide();
    }
    
    var ppom_option_total = 0;
    var ppom_total_discount = 0;
    var productBasePrice = 0;
    
    var ppom_all_option_prices = ppom_update_get_prices();
    // Flags
    var ppom_has_variable   = false;
    var ppom_has_onetime    = false;
    var ppom_has_matrix     = false;
    var ppom_show_base_price= true;
    var show_option_price_indivisually = ppom_input_vars.show_option_price == 'all_option' ? true : false;
    var show_per_unit_price = false;
    
    // console.log(ppom_all_option_prices);
    
    
    // Set hidden input
    jQuery("#ppom_option_price").val( JSON.stringify(ppom_all_option_prices) );
    
    var ppomPriceContainer = jQuery("#ppom-price-container");
    // Reset container
    ppomPriceContainer.html('');
    
    /*ppomPriceListContainer = jQuery('<ul/>')
                                .addClass('ppom-option-price-list')
                                .appendTo(ppomPriceContainer);*/
    ppomPriceListContainer = jQuery('<table/>')
                                .addClass('table table-striped')
                                .appendTo(ppomPriceContainer)
                                .css('width', '100%');
    
    
    
    /** ====== Matrix Price =========== ***/
    jQuery(ppom_all_option_prices).each(function(i, option) {
        
        // Updating flash in first loop
        if( option.apply == 'variable') ppom_has_variable = true;
        if( option.apply == 'onetime') ppom_has_onetime   = true;
        
        // Sum only variable prices
        if( option.apply !== 'matrix') return;
        
        ppom_has_matrix         = true;
        ppom_show_base_price    = false;
        if( ppom_input_vars.show_price_per_unit )
            show_per_unit_price     = true;

        var option_label_with_qty = option.label+' x ' + ppom_get_order_quantity();
        
        var matrix_price    = parseFloat(option.price) *  ppom_get_order_quantity();
        ppom_add_price_item_in_table( option_label_with_qty, matrix_price, 'ppom-matrix-price');
        
        // Totals the options
        // ppom_option_total += parseFloat(matrix_price);
        // Set productBasePrice as matrix found
        productBasePrice = matrix_price;
        
    });
    /** ====== Matrix Price=========== ***/
    
    /** ====== Variation quantities =========== ***/
    jQuery(ppom_all_option_prices).each(function(i, option) {
        
        // Sum only variable prices
        if( option.apply !== 'quantities') return;
        
        // If Matrix price found then do not calculate each option prices
        if( ppom_has_matrix ) return;
        
        // console.log(option);
        
        if( option.include !== 'on'){
            ppom_show_base_price = false;
        } else {
            wc_product_qty.val(1);
        }
        
        var variation_price = option.price !== '' ? option.price : ppom_product_base_price;
        var option_price_with_qty   = parseFloat(option.quantity) * parseFloat(variation_price);
        // Totals the options
        ppom_option_total += option_price_with_qty;
        
        if( ! show_option_price_indivisually ) return ;
        
        var price_tag = ppom_get_wc_price(variation_price);
        var option_label_with_qty = option.label+' '+jQuery(price_tag).html()+' x '+option.quantity;
        
        ppom_add_price_item_in_table( option_label_with_qty, option_price_with_qty, 'ppom-quantities-price');
        
        
    });
    /** ====== Variation quantities =========== ***/
    
    /** ====== Bulkquantity Addon =========== ***/
    jQuery(ppom_all_option_prices).each(function(i, option) {
        
        // Sum only variable prices
        if( option.apply !== 'bulkquantity') return;
        
        if( option.include !== 'on'){
            ppom_show_base_price = false;
        }
        
        var option_label_with_qty = option.label+' '+ppom_get_formatted_price(option.price)+' x '+option.quantity;
        
        var option_price_with_qty   = parseFloat(option.quantity) * parseFloat(option.price);
        ppom_add_price_item_in_table( option_label_with_qty, option_price_with_qty, 'ppom-bulkquantity-price');
        ppom_option_total += option_price_with_qty;
        
        // Base price
        var base_label  = ppom_input_vars.product_base_label;
        ppom_add_price_item_in_table( base_label, option.base, 'ppom-bulkquantity-baseprice');
        ppom_option_total += parseFloat(option.base);
        
    });
    /** ====== Bulkquantity Addon =========== ***/
                    
    /** ====== Options price variable =========== ***/
    jQuery(ppom_all_option_prices).each(function(i, option) {
        
        if( option.apply !== 'variable') return;
        
        var option_price_with_qty   = ppom_get_order_quantity() * parseFloat(option.price);
        // Totals the options
        ppom_option_total += option_price_with_qty;
        
        // Check if to shos options or not
        if( ! show_option_price_indivisually ) return ;
        var price_tag = ppom_get_wc_price(option.price);
        
        var option_label_with_qty = option.label+' '+jQuery(price_tag).html()+' x '+ppom_get_order_quantity();
        
        ppom_add_price_item_in_table( option_label_with_qty, option_price_with_qty, 'ppom-variable-price', '', option);
        
    });
    /** ====== Options price variable =========== ***/
    
    /** ====== Options price onetime/fixed =========== ***/
    
    // Heading Fixed Fee
    if( ppom_has_onetime ) {
        ppom_add_price_item_in_table( ppom_input_vars.fixed_fee_heading, '', 'ppom-fixed-fee-heading');
    }
    
    jQuery(ppom_all_option_prices).each(function(i, option) {
        // Sum only variable prices
        if( option.apply !== 'onetime') return;
        var option_label_with_qty = option.label
        
        ppom_add_price_item_in_table( option_label_with_qty, option.price, 'ppom-fixed-price', '', option);
        
        // Totals the options
        ppom_option_total += parseFloat(option.price);
        
    });
    /** ====== Options price onetime/fixed=========== ***/
    
    
    /** ====== Options total price =========== ***/
    if( ppom_has_variable ) {
        ppom_add_price_item_in_table( ppom_input_vars.option_total_label, ppom_option_total, 'ppom-option-total-price');
    }
    /** ====== Options total price =========== ***/
    
    
    /** ====== Fixed Price Addon =========== ***/
    jQuery(ppom_all_option_prices).each(function(i, option) {
        // Sum only variable prices
        if( option.apply !== 'fixedprice') return;
        ppom_product_base_price = option.unitprice;
        ppom_set_order_quantity(option.quantity);
    });
    
    
    /** ====== Hide Product total price is zero ===== **/
    
        if( ppom_product_base_price == 0 ){
            ppom_show_base_price = false;
        }
    /** ====== Hide Product total price is zero ===== **/
    
    
    /** ====== Product base price =========== ***/
    
    if( ppom_show_base_price ) {
        
        var price_tag = ppom_get_wc_price(ppom_product_base_price);
        var product_base_label  = ppom_input_vars.product_base_label+' '+jQuery(price_tag).html()+' x '+ppom_get_order_quantity();
        productBasePrice        = ppom_get_order_quantity() * parseFloat(ppom_product_base_price);
        ppom_add_price_item_in_table( product_base_label, productBasePrice, 'ppom-product-base-price');
    }
    /** ====== Product base price =========== ***/
    
    
    /** ====== Apply Discounts =========== ***/
    jQuery(ppom_all_option_prices).each(function(i, option) {
        
        if( option.apply !== 'matrix_discount') return;
        
        var is_percent = (option.percent !== '') ? true : false;
        
        show_per_unit_price     = true;
        
        if( is_percent ) {
            var option_label_with_qty = ppom_input_vars.total_discount_label+' ('+option.percent+')';
        } else {
            var price_tag = ppom_get_wc_price(option.price);
            var option_label_with_qty = ppom_input_vars.total_discount_label+' '+price_tag.html();
        }
        
        
        if( is_percent ) {
            if( ppom_pricematrix_discount_type === "base" ) {
                // if discount apply on base
                var total_item_price = productBasePrice;
            } else {
                var total_item_price = parseFloat(productBasePrice) + parseFloat(ppom_option_total);
            }
            
            var discount_percent        = ppom_get_formatted_price(parseFloat(option.percent));
            // ppom_total_discount        += ppom_get_formatted_price(parseFloat(discount_percent/100) * parseFloat(total_item_price));
            ppom_total_discount        = parseFloat((parseFloat(option.percent)/100) * (total_item_price));
            
        } else {
            ppom_total_discount        += option.price;
        }
        
        ppom_add_price_item_in_table( option_label_with_qty, ppom_total_discount, 'ppom-discount-price');
        
        // Totals the options
        // ppom_option_total += parseFloat(option.price);
        
    });
    /** ====== Apply Discounts =========== ***/
    
    
    
    /*console.log('total_discount '+ppom_total_discount);
    console.log('baseprice '+parseFloat(productBasePrice));
    console.log('option_total '+parseFloat(ppom_option_total));*/
    
    /*productBasePrice    = productBasePrice || 0;
    ppom_option_total   = ppom_option_total || 0;
    ppom_total_discount = ppom_total_discount || 0;*/
    
    /** ====== Total without fixed/onetime fee =========== ***/
    // console.log(ppom_option_total);
    var ppom_total_price = ppom_calculate_totals(ppom_total_discount, productBasePrice, ppom_option_total);
    var total_price_label = ppom_input_vars.total_without_fixed_label;
    
    /** ====== Measures ===================**/
    var ppom_measure_found = 1;
    jQuery(ppom_all_option_prices).each(function(i, option) {
        // Sum only variable prices
        if( option.apply !== 'measure') return;
        ppom_show_base_price = false;
        
        
        if( option.use_units === 'no' ) {
        
            var option_qty = option.qty == '' ? 0 : parseInt(option.qty);
            ppom_measure_found = option_qty * ppom_measure_found;
        } else {
            
            ppom_total_price += parseFloat(option.price) * parseFloat( option.qty ) * ppom_get_order_quantity();
        }
        // console.log(ppom_measure_found);
        
        // total_price_label += ' '+ ppom_get_wc_price(ppom_total_price).html()+'x'+option_measure;
        
        /*var option_price_with_qty = ppom_get_order_quantity() * parseFloat(option.price);
        var option_label_with_qty = option.label+' x ' + ppom_get_order_quantity();
        
        var formatted_option_price  = ppom_get_wc_price( option_price_with_qty );
        ppom_add_price_item_in_table( option_label_with_qty, formatted_option_price);*/
        
        // var produc_total = ppom_calculate_totals(ppom_total_discount, productBasePrice, ppom_option_total);
    });
    
    // If measured input has quantities
    if( ppom_measure_found > 0 ) {
        ppom_total_price = ppom_total_price * parseFloat(ppom_measure_found);
    }
    
    var per_unit_price          = ppom_get_formatted_price(parseFloat(ppom_total_price / ppom_get_order_quantity()));
    var per_unit_label          = '';
    
    if ( show_per_unit_price && ppom_get_order_quantity() > 0 ) {
        per_unit_label          = ' ('+ppom_get_wc_price(per_unit_price).html()+' / '+ppom_input_vars.per_unit_label+')';
    }
    
    
    /** ====== Measures ===================**/
    
    ppom_add_price_item_in_table( total_price_label, ppom_total_price, 'ppom-total-without-fixed', per_unit_label);
    /** ====== Total without fixed/onetime fee =========== ***/
    
}


function ppom_calculate_totals(ppom_total_discount, productBasePrice, ppom_option_total) {
    
    var totals = (parseFloat(productBasePrice) + parseFloat(ppom_option_total)) - parseFloat(ppom_total_discount);
    return totals;
}

// Adding TDs item in price container
function ppom_add_price_item_in_table( label, price, item_class, append_to_price, option) {
    
    var formatted_price = '';
    var row_id          = '';
    var row_data_name   = '';
    if( option !== undefined ) {
        row_id = option.option_id;
        row_data_name = option.data_name;
    }
    
    if(price == 0) return;
    
    if( price !== '' ) {
        if( item_class === 'ppom-discount-price' ) {
            formatted_price     = ppom_get_wc_price(price, true);
        } else {
            formatted_price     = ppom_get_wc_price(price);
        }
    }
    
    if( append_to_price !== undefined ) {
        formatted_price = formatted_price.html() + append_to_price;
    }
    
    ppomPriceListContainerRow = jQuery('<tr/>')
                                .addClass('ppom-option-price-list')
                                .attr('data-option_id', row_id)
                                .attr('data-data_name', row_data_name)
                                .appendTo(ppomPriceListContainer);
                                
    if( item_class ) {
        ppomPriceListContainerRow.addClass(item_class);
    }
  
    // Delete Option
    if( row_data_name ) {
        label = '<span class="fa fa-times ppom-delete-option-table" title="Remove"></span> '+label;
    }
    
    // Label Item
    var totalWithoutFixedLabel  = jQuery('<th/>')
                                .html( label )
                                .addClass('ppom-label-item')
                                .appendTo( ppomPriceListContainerRow );
                                
    if( price === '' ) {
        totalWithoutFixedLabel.attr('colspan', '2');
    } else {
                                
        // Price Item
        var totalWithoutFixedLabel  = jQuery('<th/>')
                                    .html( formatted_price )
                                    .addClass('ppom-price-item')
                                    .appendTo( ppomPriceListContainerRow );
    }
                                
    jQuery.event.trigger({type: "ppom_option_price_added",
            price: price,
            item: item_class,
            time: new Date()
            });
}

// Adding Li item in price container
function ppom_add_price_item_in_list( label, price, item_class) {
    
    var item_class          = item_class || '';
    
    var ppomListItem        = jQuery('<li/>')
                                .addClass( item_class )
                                .appendTo(ppomPriceListContainer);
    // Label Item
    var totalWithoutFixedLabel  = jQuery('<span/>')
                                .html( label )
                                .addClass('ppom-label-item')
                                .appendTo( ppomListItem );
                                
    // Price Item
    var totalWithoutFixedLabel  = jQuery('<span/>')
                                .html( price )
                                .addClass('ppom-price-item')
                                .appendTo( ppomListItem );
}

function ppom_get_wc_price( price, is_discount ) {
    
    var do_discount     = is_discount || false;
    
    var wcPriceWithCurrency     = jQuery("#ppom-price-cloner").clone();
    var ppom_formatted_price    = ppom_get_formatted_price(price);
    var is_negative             = parseFloat(price) < 0;
    
    wcPriceWithCurrency.find('.ppom-price').html( ppom_formatted_price );
    
    // Adding (-) symbol
    if( do_discount || is_negative) {
        wcPriceWithCurrency.prepend('-');
    }
    return wcPriceWithCurrency;
}

function ppom_update_get_prices() {
    
    var options_price_added = [];
    PPOMWrapper.find('select,input:checkbox,input:radio').each(function(i, input){
        
        // if fixedprice (addon) then return
        if( jQuery("option:selected", this).attr('data-unitprice') !== undefined ) return;
        
        var selected_option_price = jQuery("option:selected", this).attr('data-price');
        var selected_option_label = jQuery("option:selected", this).attr('data-label');
        var selected_option_title = jQuery("option:selected", this).attr('data-title');
        var selected_option_apply = jQuery("option:selected", this).attr('data-onetime') !== 'on' ? 'variable' : 'onetime';
        var selected_option_taxable = jQuery("option:selected", this).attr('data-taxable');
        var selected_option_without_tax = jQuery("option:selected", this).attr('data-without_tax');
        var selected_option_optionid = jQuery("option:selected", this).attr('data-optionid');
        var selected_option_data_name = jQuery("option:selected", this).attr('data-data_name');
        
        var checked_option_price = jQuery(this).attr('data-price');
        var checked_option_label = jQuery(this).attr('data-label');
        var checked_option_title = jQuery(this).attr('data-title');
        var checked_option_apply = jQuery(this).attr('data-onetime') !== 'on' ? 'variable' : 'onetime';
        var checked_option_taxable = jQuery(this).attr('data-taxable');
        var checked_option_without_tax = jQuery(this).attr('data-without_tax');
        var checked_option_optionid = jQuery(this).attr('data-optionid');
        var checked_option_data_name = jQuery(this).attr('data-data_name');
        
        // apply now being added from data-attribute for new prices
        if( jQuery(this).attr('data-apply') !== undefined ) {
            checked_option_apply = jQuery(this).attr('data-apply');
            selected_option_apply = jQuery(this).attr('data-apply');
        }
        
            
        var does_option_has_price = true;
        
        if( (checked_option_price == undefined || checked_option_price == '') && 
            (selected_option_price == undefined || selected_option_price == '') ) {
            return;
        }
            
        var option_price = {};
        if( jQuery(this).prop("checked") ){
            
            if( checked_option_title !== undefined ) {
                option_price.label = checked_option_title+' '+checked_option_label;
            } else {
                option_price.label = checked_option_label;
            }
            option_price.price = checked_option_price;
            option_price.apply = checked_option_apply;
            
            option_price.product_title  = ppom_input_vars.product_title;
            option_price.taxable        = checked_option_taxable;
            option_price.without_tax    = checked_option_without_tax;
            option_price.option_id      = checked_option_optionid;
            option_price.data_name      = checked_option_data_name;
            
            // More data attributes
            if( checked_option_apply === 'measure' ) {
                option_price.qty = jQuery(this).attr('data-qty');
                option_price.use_units = jQuery(this).attr('data-use_units');
            }
            
            options_price_added.push( option_price );
            
    	} else if(selected_option_price !== undefined && is_option_calculatable(this) ) {
    	    
    	    if( selected_option_title !== undefined ) {
                option_price.label = selected_option_title+' '+selected_option_label;
            } else {
                option_price.label = selected_option_label;
            }
    	    option_price.price = selected_option_price;
            option_price.apply = selected_option_apply;
            
            option_price.product_title  = ppom_input_vars.product_title;
            option_price.taxable        = selected_option_taxable;
            option_price.without_tax    = selected_option_without_tax;
            option_price.option_id      = selected_option_optionid;
            option_price.data_name      = selected_option_data_name;
            
            options_price_added.push( option_price );
    	} else {
    	    
    	    
    	    /*if( jQuery(this).data('type') == 'measure' ) {
    	        
    	        var product_qty = ppom_get_order_quantity();
    	        var measure_price = checked_option_price * jQuery(this).val();
    	        console.log(checked_option_price);
    	        console.log(measure_price);
    	        checked_option_title = checked_option_title+' '+
    	                                ppom_get_formatted_price(checked_option_price)+'x'
    	                                +jQuery(this).val();
    	        
        	    option_price.label = checked_option_title;
        	    option_price.price = measure_price;
        	    option_price.measure = jQuery(this).val();
                option_price.apply = 'variable';
                
                option_price.product_title  = ppom_input_vars.product_title;
                option_price.taxable        = true;
                option_price.without_tax    = '';
                
                options_price_added.push( option_price );
    	    }*/
    	}
    	
    });
    
    
    // Price matrix
    var ppom_pricematrix = jQuery(".ppom_pricematrix.active").val();
    var ppom_pricematrix_discount = jQuery(".ppom_pricematrix.active").attr('data-discount');
    var ppom_pricematrix_id = jQuery(".ppom_pricematrix.active").data('dataname');
    
    var ppom_matrix_array = Array();
    var apply_as_discount = ppom_pricematrix_discount == 'on' ? true : false;
    
    if( ppom_pricematrix !== undefined) {
        jQuery.each( JSON.parse(ppom_pricematrix), function(range, meta){
            var option_price = {};
            
            var range_break = range.split("-");
            var range_from  = parseInt(range_break[0]);
            var range_to    = parseInt(range_break[1]);
            var product_qty = ppom_get_order_quantity();
            
            // console.log(meta);
            
            if( product_qty >= range_from && product_qty <= range_to ) {
                
                option_price.label = meta.label;
                option_price.price = meta.price;
                option_price.percent = meta.percent;
                option_price.range = range;
                option_price.apply = (apply_as_discount) ? 'matrix_discount' : 'matrix';
                option_price.data_name = ppom_pricematrix_id;
                options_price_added.push( option_price );
            }
            
        });
    }
    
    // Variation quantities
    var ppom_quantities_qty = 0;
    jQuery('.ppom-input-quantities').each(function(){
        
		// Checking if quantities is hidden
		if( jQuery(this).hasClass('ppom-locked') ) {
		    // Resetting quantity to one
		    wc_product_qty.val(1);
		    return;
		}
        
        jQuery(this).find('.ppom-quantity').each(function(){
		
    		
    		var option_price = {};
    		
    		option_price.price      = jQuery(this).attr('data-price');
    // 		console.log(ppom_product_base_price);
            option_price.label      = jQuery(this).attr('data-label');
            option_price.quantity   = (jQuery(this).val() === '' ) ? 0 :  jQuery(this).val();
            option_price.include    = jQuery(this).attr('data-includeprice');
            option_price.apply      = 'quantities';
            option_price.usebase_price      = jQuery(this).attr('data-usebase_price');
            ppom_quantities_qty     += parseInt(option_price.quantity);
            
            options_price_added.push( option_price );
            wc_product_qty.val(ppom_quantities_qty);
        });
    });
    
    // Bulkquantity
    if( jQuery('#ppom-input-bulkquantity').length > 0 ){
        
		var option_price = {};
		
		var ppom_bq_container = jQuery('#ppom-input-bulkquantity');
		
		option_price.price      = ppom_bq_container.find('.ppom-bulkquantity-options option:selected').attr('data-price');
		option_price.base       = ppom_bq_container.find('.ppom-bulkquantity-options option:selected').attr('data-baseprice');
        option_price.label      = ppom_bq_container.find('.ppom-bulkquantity-options option:selected').attr('data-label');
        option_price.quantity   = ppom_bq_container.find('.ppom-bulkquantity-qty').val();
        // option_price.include    = jQuery(this).attr('data-includeprice');
        option_price.apply      = 'bulkquantity';
        options_price_added.push( option_price );
        
        /*var option_price = {};
        // Base price
        option_price.price      = ppom_bq_container.find('.ppom-bulkquantity-baseprice').attr('data-price');
        option_price.label      = ppom_bq_container.find('.ppom-bulkquantity-baseprice').attr('data-label');
        // option_price.include    = jQuery(this).attr('data-includeprice');
        option_price.apply      = 'onetime';
        
        options_price_added.push( option_price );*/
    }
    
    // Fixedprice addon
     if( jQuery('.ppom-input-fixedprice').length > 0 ){
        
		var option_price = {};
		
		var ppom_fp_container = jQuery('.ppom-input-fixedprice.ppom-unlocked');
		
		option_price.price      = ppom_fp_container.find('select option:selected').attr('data-price') || 0;
		option_price.unitprice  = ppom_fp_container.find('select option:selected').attr('data-unitprice') || 0;
        option_price.label      = ppom_fp_container.find('select option:selected').attr('data-label') || '';
        option_price.quantity   = ppom_fp_container.find('select option:selected').attr('data-qty') || 0;
        // option_price.include    = jQuery(this).attr('data-includeprice');
        option_price.apply      = 'fixedprice';
        options_price_added.push( option_price );
		
     }
    
    // console.log(options_price_added);
    return options_price_added;
}

// Return formatted price with decimal and seperator
function ppom_get_formatted_price( price ) {
    
    var decimal_separator= ppom_input_vars.wc_decimal_sep;
	var no_of_decimal    = ppom_input_vars.wc_no_decimal;
	
	var formatted_price = Math.abs( parseFloat(price) );
	formatted_price = formatted_price.toFixed(no_of_decimal);
	formatted_price = formatted_price.toString().replace('.', decimal_separator);
	formatted_price = ppom_add_thousand_seperator(formatted_price);
		
	return formatted_price;
}

function ppom_add_thousand_seperator(n){

    var rx= /(\d+)(\d{3})/;
    return String(n).replace(/^\d+/, function(w){
        if (ppom_input_vars.wc_thousand_sep) {
            while(rx.test(w)){
                w= w.replace(rx, '$1'+ppom_input_vars.wc_thousand_sep+'$2');
            }
        }
        return w;
    });
}

// sometime options should not be calculated like in case of bulkquantity
function is_option_calculatable( selector ) {
    
    var option_calculatable = true;
    if( jQuery(selector).attr('data-type') === 'bulkquantity' ) {
        option_calculatable = false;
    }
    
    return option_calculatable;
}

// Return quantity
function ppom_get_order_quantity(){
    
    var quantity = ppom_input_vars.is_shortcode === 'yes' ? 1 : wc_product_qty.val();
    quantity = quantity || 1;
    return parseInt(quantity);
    
}

// Set quantity
function ppom_set_order_quantity(qty){
    
    wc_product_qty.val(qty);
}

// Deleting option from price table
function ppom_delete_option_from_price_table( field_name, option_id ) {
    
    var field_type = ppom_get_field_type_by_id( field_name );
    console.log(field_type);
    
    switch( field_type ) {
        
        case 'palettes':
        case 'image':
        case 'radio':
        case 'checkbox':
            
            jQuery("#"+option_id).prop('checked', false);
            ppom_update_option_prices();
        break;
        
        case 'select':
            jQuery("#"+field_name).val('')
            ppom_update_option_prices();
        break;
    }
}

// Update variation quantity price if baseprice is set = yes
function ppom_update_variation_quatity ( price ) {
    
    jQuery('input.ppom-quantity').each(function(i, q){
        
        if( jQuery(q).attr('data-usebase_price') == 'yes' ) {
            jQuery(q).attr('data-price', price);
        }
    });
}