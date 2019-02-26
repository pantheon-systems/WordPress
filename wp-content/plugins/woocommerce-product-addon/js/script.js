/*
 * NOTE: all actions are prefixed by plugin shortnam_action_name
 */
var selected_slide = 0;
var total_sections = 0;
var uploaderInstances = {};


jQuery(function($){

	//tweaking file uploader button css
	$("#uploadifive-nm_contact_file").css({'margin':'#fff'});
	
	//setting all input widht to 95% within P tags
	$(".nm-productmeta-box").find('input:text, input[type="email"], textarea, select').css({'width': '100%', 'padding': 0});
	
	/*
	 * handling date input
	 */
	/*$("input[data-type='date']").each(function(i, item){
		
		//console.log(item);
		$(item).datepicker({ 	changeMonth: true,
			changeYear: true,
			dateFormat: $(item).attr('data-format')
			});
	});*/
	
	
	
	
	$('input[name="quantity"]').on('blur', function(){
		
		//console.log($(this).val());
		set_price_for_matrix();
	});
	
	/**
	 * quantity input 
	 * 
	 * @since 3.6
	 */
	 if($('#input-quantities').length){
		$('#input-quantities input').change(function(){
			
			// ppom_update_quantity();
		});
	}
	
		
});


function set_price_for_matrix(){
		
		var price_matrix = jQuery("#_pricematrix").val();
		var selected_qty = jQuery('input[name="quantity"]').val();
		
		//console.log(price_matrix);
		if(price_matrix != '' && price_matrix != undefined){
			
			var pricematrix = jQuery.parseJSON(price_matrix);
			//console.log(pricematrix);
			jQuery.each(pricematrix, function(i, matrix){
				
				
				var mtx = matrix.option.split('-');
				var price = matrix.price;
				
				var range1 = parseInt(mtx[0]);	
				var range2 = parseInt(mtx[1]);
				
				if(selected_qty >= range1 && selected_qty <= range2){
					
					//console.log('price set '+price);
					
				}
			});
			
		}
}




function is_valid_email(email) {
	var pattern = new RegExp(
			/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	return pattern.test(email);
};

function get_option(key) {

	/*
	 * TODO: change plugin shortname
	 */
	var keyprefix = 'nm_personalizedproduct';

	key = keyprefix + key;

	var req_option = '';

	jQuery.each(nm_personalizedproduct_vars.settings, function(k, option) {

		// console.log(k);

		if (k == key)
			req_option = option;
	});

	// console.log(req_option);
	return req_option;
}

function update_rule_childs(element_name, element_values){
	
	jQuery(".nm-productmeta-box > p, .nm-productmeta-box div.fileupload-box").each(function(i, p_box){

		var parsed_conditions 	= jQuery.parseJSON (jQuery(p_box).attr('data-rules'));
		var box_id				= jQuery(p_box).attr('id');
		
		if(parsed_conditions !== null){
		
			var _visiblity		= parsed_conditions.visibility;
			var _bound			= parsed_conditions.bound;
			var _total_rules 	= Object.keys(parsed_conditions.rules).length;
			
			 var matched_rules = {};
			 var last_meched_element = '';
			jQuery.each(parsed_conditions.rules, function(i, rule){
				
				var _element 		= rule.elements;
				var _elementvalues	= rule.element_values;
				var _operator 		= rule.operators;
				
				//console.log('_element ='+_element+' element_name ='+element_name);
				var matched_rules = {};	
				
				if(element_values === 'child')
					_elementvalues = element_values;
				
				if(_element === element_name && _elementvalues === element_values){
					//console.log('Hiding _element ='+_element+' under box ='+jQuery(p_box).find('select').attr('name'));
					//console.log('hiddedn rule '+element_name+' value ' + element_values + 'under box = ' + jQuery(p_box).attr('id'));
					jQuery(p_box).hide(300, function(){
						update_rule_childs(jQuery(this).find('select, input:radio').attr('name'), 'child');
					});
					
				}
			});
		}
});
	
}
	
function remove_existing_rules(box_rules, element){
	
	if(box_rules){
        jQuery.each(box_rules, function(j, matched){
            if(matched !== undefined){
                jQuery.each(matched, function(k,v){
                	if(k === element){
                  		delete box_rules[j];
                  		update_rule_childs(k, v);
                	}
                });
            }
        });
    }
}

function ppom_addThousandSeperator(n){

    var rx=  /(\d+)(\d{3})/;
    return String(n).replace(/^\d+/, function(w){
        while(rx.test(w)){
            w= w.replace(rx, '$1'+nm_personalizedproduct_vars.wc_thousand_sep+'$2');
        }
        return w;
    });

}


function get_woocommerce_price_format(p) {
  var currency_pos = nm_personalizedproduct_vars.wc_currency_pos;
  var format = '';
  var sym = nm_personalizedproduct_vars.woo_currency;
  
  switch ( currency_pos ) {
    case 'left' :
      format = sym + p;
    break;
    case 'right' :
      format = p + sym;
    break;
    case 'left_space' :
      format = sym + ' ' + p;
    break;
    case 'right_space' :
      format = p + ' ' + sym;
    break;
  }

  return format;
}
	
	
	
function ppom_update_quantity() {
		
	var total_price = 0;
	var item_qty = 0;
	var html_total_price = 0;
	var total_option_price = 0;
	var product_price = false;
	var decimalSeparator = nm_personalizedproduct_vars.wc_decimal_sep;
	var noOfDecimal = nm_personalizedproduct_vars.wc_no_decimal;
	

	//resetting
	jQuery('#display-total-price > span.ppom-total-option-price').hide();
	jQuery('#display-total-price > span.ppom-grand-total-price').hide();
	
	jQuery('#input-quantities').find('.ppom-quantity').each(function(){
		
		var qty = jQuery(this).val();
		var option_price = jQuery(this).attr('data-price');
		var base_price = jQuery("#_product_price").val();
	
		
		item_qty = item_qty + parseInt( jQuery(this).val() );
		jQuery('form.cart .qty').val( item_qty );
		
		if( option_price !== '' ){
			
			total_option_price += parseFloat(option_price * qty);
		}
		
		var this_total = qty * base_price;
		total_price = parseFloat(total_price) + parseFloat(this_total);
		html_total_price = ppom_addThousandSeperator(total_price.toFixed(noOfDecimal));
		html_total_price = html_total_price.toString().replace('.', decimalSeparator);
	});
	
	// Product Total
	jQuery('#display-total-price > span.ppom-total-price').show().find('.ppom-price').html( html_total_price );
	
	var grand_total = parseFloat(total_option_price) + parseFloat(total_price);
	// Option Total
	if( total_option_price > 0 ){
		
		var html_total_option_price = ppom_addThousandSeperator(total_option_price.toFixed(noOfDecimal));
		html_total_option_price = html_total_option_price.toString().replace('.', decimalSeparator);
		jQuery('#display-total-price > span.ppom-total-option-price').show().find('.ppom-price').html( html_total_option_price );
		
		var html_grand_total = ppom_addThousandSeperator(grand_total.toFixed(noOfDecimal));
		html_grand_total = html_grand_total.toString().replace('.', decimalSeparator);
		jQuery('#display-total-price > span.ppom-grand-total-price').show().find('.ppom-price').html( html_grand_total );
	}
	
	// updating hidden var to have total option price
	jQuery("#_quantities_option_price").val(total_option_price);
	
}


function stripslashes (str) {
	  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +   improved by: Ates Goral (http://magnetiq.com)
	  // +      fixed by: Mick@el
	  // +   improved by: marrtins
	  // +   bugfixed by: Onno Marsman
	  // +   improved by: rezna
	  // +   input by: Rick Waldron
	  // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
	  // +   input by: Brant Messenger (http://www.brantmessenger.com/)
	  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	  // *     example 1: stripslashes('Kevin\'s code');
	  // *     returns 1: "Kevin's code"
	  // *     example 2: stripslashes('Kevin\\\'s code');
	  // *     returns 2: "Kevin\'s code"
	  return (str + '').replace(/\\(.?)/g, function (s, n1) {
	    switch (n1) {
	    case '\\':
	      return '\\';
	    case '0':
	      return '\u0000';
	    case '':
	      return '';
	    default:
	      return n1;
	    }
	  });
	}