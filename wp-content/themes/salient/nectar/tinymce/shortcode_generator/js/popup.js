jQuery(document).ready(function($){
   
   
	
 	    //Upload function
		function initUpload(clone){
				
			var itemToInit = null;

			itemToInit = typeof clone !== 'undefined' ? clone : $('.shortcode-dynamic-item');
				
	        itemToInit.find('.redux-opts-upload').on('click',function( event ) {
					
	                var activeFileUploadContext = jQuery(this).parent();
	                var relid = jQuery(this).attr('rel-id');
					
	                event.preventDefault();
	                
	                // if its not null, its broking custom_file_frame's onselect "activeFileUploadContext"
	                custom_file_frame = null;
	
	                // Create the media frame.
	                custom_file_frame = wp.media.frames.customHeader = wp.media({
	                    // Set the title of the modal.
	                    title: jQuery(this).data("choose"),
	
	                    // Tell the modal to show only images. Ignore if want ALL
	                    library: {
	                        type: 'image'
	                    },
	                    // Customize the submit button.
	                    button: {
	                        // Set the text of the button.
	                        text: jQuery(this).data("update")
	                    }
	                });

	                custom_file_frame.on( "select", function() {
	           
	                    // Grab the selected attachment.
	                    var attachment = custom_file_frame.state().get("selection").first();
	
	                    // Update value of the targetfield input with the attachment url.
	                    jQuery('.redux-opts-screenshot',activeFileUploadContext).attr('src', attachment.attributes.url);
	                    jQuery('#' + relid ).val(attachment.attributes.url).trigger('change');
	
	                    jQuery('.redux-opts-upload',activeFileUploadContext).hide();
	                    jQuery('.redux-opts-screenshot',activeFileUploadContext).show();
	                    jQuery('.redux-opts-upload-remove',activeFileUploadContext).show();
	            });
	
	            custom_file_frame.open();
	        });
	
	   	itemToInit.find('.redux-opts-upload-remove').on('click', function( event ) {
	        var activeFileUploadContext = jQuery(this).parent();
	        var relid = jQuery(this).attr('rel-id');
	
	        event.preventDefault();
	
	        jQuery('#' + relid).val('');
	        jQuery(this).prev().fadeIn('slow');
	        jQuery('.redux-opts-screenshot',activeFileUploadContext).fadeOut('slow');
	        jQuery(this).fadeOut('slow');
	    });
	}
  

            		
  function calcPercent() {
  	var $output = $("<span>");
  	$output.addClass('output');
  	
  	$("div.shortcode-options[data-name=bar_graph] .shortcode-dynamic-items > div:last-child .content.dd-percent").append($output);
    $("[data-slider]").bind("slider:ready slider:changed", function (event, data) {
      $(this).nextAll(".output:first").html(data.value + '%').attr('data-num',data.value);
    });
  }		
  
  calcPercent();


   
  $('#shortcode-content textarea').val('');
    
    function dynamic_items(){
   	
		var code = '';
		var tabID = '1', barID = '1', clientID = '1', testimonialID = '1', toggleID = '1';
		var tabContent, barPercent;
		var toggleContent, toggleTitle, toggleColor;
		var barTitle, barColor; 
		var clientImage, clientURL; 
		var testimonialName, testimonialQuote;
		
		//tabs
		if( $('.shortcode-options[data-name=tabbed_section]').is(':visible') ){
			$('.shortcode-options[data-name=tabbed_section] .shortcode-dynamic-item-input').each(function(){
			   if( $(this).val() != '' ) {
					tabContent = $(this).parent().parent().find('.shortcode-dynamic-item-text').val();
					code += ' [tab title="'+$(this).val()+'" id="t'+tabID+'"] '+tabContent+' [/tab] '; 
					tabID++;
				}
			});
		}
		
		//toggles
		else if( $('.shortcode-options[data-name=toggles]').is(':visible') ) {
			$('.shortcode-options[data-name=toggles] .shortcode-dynamic-item').each(function(){
				
				toggleTitle = $(this).find('.shortcode-dynamic-item-input:nth-child(1)').val();
				toggleContent = ($(this).find('textarea').val().length != 0) ? $(this).find('textarea').val() : '';
				toggleColor = $(this).find('select').val();
				
				code += ' [toggle title="'+toggleTitle+'" color="' + toggleColor + '" id="b'+toggleID+'"] '+toggleContent+' [/toggle]'; 
				toggleID++;
			});
		}
		
		//bar graph
		else if( $('.shortcode-options[data-name=bar_graph]').is(':visible') ) {
			$('.shortcode-options[data-name=bar_graph] .shortcode-dynamic-item').each(function(){
				
				barTitle = $(this).find('.shortcode-dynamic-item-input:nth-child(1)').val();
				barPercent = (typeof $(this).find('.output').attr('data-num') != 'undefined') ? $(this).find('.output').attr('data-num') : '1';
				barColor = $(this).find('select').val();
				
				code += ' [bar title="'+barTitle+'" percent="' + barPercent + '" color="' + barColor + '" id="b'+barID+'"]'; 
				barID++;
			});
		}
		
		//clients
		else if( $('#options-item.clients').is(':visible') ) {
			$('#options-item.clients .shortcode-dynamic-item').each(function(){
				
				clientImage = $(this).find('.redux-opts-screenshot:first').attr('src');
				clientURL = ($(this).find('.shortcode-dynamic-item-input').val().length != 0) ? $(this).find('.shortcode-dynamic-item-input').val() : 'none';
				
				code += ' [client image="'+clientImage+'" url="' + clientURL + '" id="c'+clientID+'"]'; 
				clientID++;
			});
		}
		
		//testimonials
		else if( $('#options-item.testimonials').is(':visible') ) {
			$('#options-item.testimonials .shortcode-dynamic-item').each(function(){
				testimonialName = $(this).find('.shortcode-dynamic-item-input:nth-child(1)').val();
				testimonialQuote = ($(this).find('textarea').val().length != 0) ? $(this).find('textarea').val() : '';
				
				code += ' [testimonial name="'+testimonialName+'" quote="' + testimonialQuote + '" id="t'+testimonialID+'"]'; 
				testimonialID++;
			});
		}
		
		$('#shortcode-storage-d').html(code);
    }
    
    function directToEditor() {
    	var name = $('#nectar-shortcodes').val();
    	var content = '';
    	
    	switch(name) {
    		case 'carousel':
    			content = '<br/>' + 
    			'[item] content here [/item]<br/>' + 
    			'[item] content here [/item]<br/>' + 
    			'[item] content here [/item]<br/>' + 
    			'[item] content here [/item]<br/>' + 
    			'[/carousel]';
    		break;
    		
    		case 'pricing_table':
    		   var columnNum = 0;
    		   var selectedColumnNum = $('#options-'+name+' input.attr:checked').val();
    		   var columnHeader;
    		   (typeof selectedColumnNum != 'undefined' && selectedColumnNum.length > 0) ? columnNum = selectedColumnNum : columnNum = 0;
    		   
    			for(var i=1;i<=columnNum;i++) {
    				if(i == 2) {
    					columnHeader = '[pricing_column title="Column '+i+'" highlight="true" highlight_reason="Most Popular" color="extra-color-1" price="'+100*i+'" currency_symbol="$" interval="Per Month" ]<br/>';
    				}
    				else{
    					columnHeader = '[pricing_column title="Column '+i+'" price="'+100*i+'" currency_symbol="$" interval="Per Month"]<br/>';
    				}
	    			content += '<br/><br/>' + 
		    			  columnHeader +
						    '<ul class="features">'+
							'<li> Your text here </li>'+
							'<li> Your text here </li>'+
							'<li> Your text here </li>'+
						   '</ul>'+
						   '[button size="medium" url="#" text="Sign up now!" color="extra-color-1"]<br/>'+
						'[/pricing_column]';
    			}
    			content += '<br/><br/>[/pricing_table]';
    		break;
    	}
    	
    	//insert shortcode
		window.wp.media.editor.insert( $('#shortcode-storage-o').text() + content);
		
		$.magnificPopup.close();
		
		//wipe out storage 
		$('#shortcode-storage-o, #shortcode-storage-d, #shortcode-storage-c').text('');
		resetFileds();
    		

		return false;	
    }
    
    function update_shortcode(ending){
		
		var name = $('#nectar-shortcodes').val();
		var dataType = $('#options-'+name).attr('data-type');
		var extra_attrs = '', extra_attrs2 = '', extra_attrs3 = '', extra_attrs3b = '', extra_attrs3c = '', extra_attrs4 = '';
		
		ending = ending || '';
		
		//take care of the dynamic events easier
		dynamic_items();
		
		//last check
		var code = '['+name;
		if( $('#options-'+name).attr('data-type')=='checkbox' ){
		    if($('#options-'+name+' input.last').attr('checked') == 'checked') ending = '_last';
		}
		code += ending;
		 
		//checkbox loop for extra attrs
		$('#options-'+name+' input[type=checkbox]').each(function(){
			 if($(this).attr('checked') == 'checked' && $(this).attr('class') != 'last') extra_attrs += ' ' + $(this).attr('class')+'="true"';	
		});
		
		code += extra_attrs;
		
		//textarea loop for extra attrs
		$('#options-'+name+' textarea:not("#shortcode_content")').each(function(){
			 extra_attrs2 += ' ' + $(this).attr('data-attrname')+'="'+ $(this).val() +'"';	
		});
		
		if(dataType != 'dynamic') code += extra_attrs2;
		
		//select loop for extra attrs
		$('#options-'+name+' select:not(".dynamic-select, [multiple=multiple], .skip-processing")').each(function(){
			 extra_attrs3 += ' ' + $(this).attr('id')+'="' + $(this).attr('value') + '"';	
		});
		
		code += extra_attrs3;
		
		//multiselect loop for extra attrs
		$('#options-'+name+' select[multiple=multiple]').each(function(){
			 var $categories = ($(this).val() != null && $(this).val().length > 0) ? $(this).val() : 'all';
			 extra_attrs3b += ' ' + $(this).attr('id')+'="' + $categories + '"';	
		});
		
		code += extra_attrs3b;
		
		//select loop for button conditional
		$('#options-'+name+' select.skip-processing').each(function(){
			if($(this).attr('value') == 'default-arrow') {
				extra_attrs3c += ' image="' + $(this).attr('value') + '"';	
			}
			 
		});
		
		code += extra_attrs3c;
		
		//image upload loop for extra attrs
		$('#options-'+name+' [data-name=image-upload] img.redux-opts-screenshot').each(function(){
			 extra_attrs4 += ' ' + $(this).attr('id')+'="' + $(this).attr('src') + '"';	
		});
		
		code += extra_attrs4;
		
		//input loop for extra attrs
		$('#options-'+name+' input.attr:not(".skip-processing")').each(function(){
			if( $(this).attr('type') == 'text' ){ code += ' '+ $(this).attr('data-attrname')+'="'+ $(this).val()+'"'; }
			else { if($(this).attr('checked') == 'checked') code += ' '+ $(this).attr('data-attrname')+'="'+ $(this).val()+'"'; }
		});
		
		
		//color loop for extra attrs
		$('#options-'+name+':not([data-name="button"]) input.popup-colorpicker:not(.simple)').each(function(){
			 code += ' background_color="'+ $(this).val()+'"'; 
		});
		$('#options-'+name+':not([data-name="button"]) input.popup-colorpicker.simple').each(function(){
			 code += ' color="'+ $(this).val()+'"'; 
		});
		$('#options-'+name+'[data-name="button"] input.popup-colorpicker').each(function(){
			if($(this).parents('.content').prev('.label').find('strong').text() == 'Hover BG Color:'){
				if($(this).val().length > 0) {
				 code += ' hover_color_override="'+ $(this).val()+'"'; 
				}
			} else {
				 code += ' color_override="'+ $(this).val()+'"'; 
			}
		});
	
		
		
		//take care of icon attrs
		if(name == 'icon' && $('.icon-option i.selected').is('[data-svg-val]')) {  //svg icons
			if(name == 'icon' && $('.icon-option i.selected').length > 0 || name == 'button' && $('.icon-option i.selected').length > 0) code += ' image="'+ $('.icon-option i.selected').attr('data-svg-val') +'"';  
		} else { //standard icons
			if(name == 'icon' && $('.icon-option i.selected').length > 0 || name == 'button' && $('.icon-option i.selected').length > 0) { 
				if($('.icon-option i.selected').attr('class').split(' ')[0] == 'fa')
					code += ' image="'+ $('.icon-option i.selected').attr('class').split(' ')[1] +'"'; 
				else 
					code += ' image="'+ $('.icon-option i.selected').attr('class').split(' ')[0] +'"'; 
			}
		}
		code += ']';

		$('#shortcode-storage-o').html(code);
		if( dataType!= 'dynamic') $('#shortcode-storage-d').text($('#shortcode-content textarea').val());
	    if( dataType != 'regular' && dataType != 'radios' && dataType != 'direct_to_editor') $('#shortcode-storage-c').html(' [/'+name+ending+']');
		if( dataType == 'direct_to_editor') directToEditor();
		
	 }
     
	//events
  $('#add-shortcode').click(function(){
    	
    	//column animation check (don't add the attrs when unnecessary)
    	var name = $('#nectar-shortcodes').val();
    	if(name == 'one_half' || name == 'one_third' || name == 'two_thirds' || name == 'one_fourth' || name == 'three_fourths' || name == 'one_sixth' || name == 'five_sixths' || name == 'one_whole') {
    		if( $('#options-'+name+' select').val() == 'None') {
    			$('#options-'+name+' select').addClass('skip-processing');
    			$('#options-'+name+' input[data-attrname="delay"]').addClass('skip-processing');
    			
    		} else {
    			$('#options-'+name+' select').removeClass('skip-processing');
    			$('#options-'+name+' input[data-attrname="delay"]').removeClass('skip-processing');
    		}
    	}
    	
    	var dataType = $('#options-'+name).attr('data-type');
    	
    	
    	update_shortcode();
		if( dataType != 'direct_to_editor') {
			
			var $shortcodeData = $('#shortcode-storage-o').text() + $('#shortcode-storage-d').text() + $('#shortcode-storage-c').text() ;
			
			window.wp.media.editor.insert( $('#shortcode-storage-o').text() + $('#shortcode-storage-d').text() + $('#shortcode-storage-c').text() );
			$.magnificPopup.close();
			
			//wipe out storage 
			$('#shortcode-storage-o, #shortcode-storage-d, #shortcode-storage-c').text('');
			
			resetFileds();
			
		}
		return false;
    });

    $('#nectar-shortcodes').change(function(){
		$('.shortcode-options').hide();
		$('#options-'+$(this).val()).show();

		var dataType = $('#options-'+$(this).val()).attr('data-type');
		
		if( dataType == 'checkbox' || dataType == 'simple' ){
    		$('#shortcode-content').show().find('textarea').val('');
		}
		
		else {
    		$('#shortcode-content textarea').val('').parent().parent().hide();
		}

    });

    $('#options-box input[type="radio"]').click(function(){

		if($(this).val() == 'custom'){
		    $('#custom-box-name').attr('data-attrname','style').addClass('attr');
		    $('#options-box input[type="radio"]').attr('data-attrname','temp').removeClass('attr');
		}
		else{
		    $('#options-box input[type="radio"]').attr('data-attrname','style').addClass('attr');
		    $('#custom-box-name').attr('data-attrname','temp').removeClass('attr');
		}
    });
 	
 	////Dynamic item events
    $('.add-list-item').click(function(){
    	
    	if(!$(this).parent().find('.remove-list-item').is(':visible')) $(this).parent().find('.remove-list-item').show();
    	
    	//clone item 
    	var $clone = $(this).parent().find('.shortcode-dynamic-item:first').clone();
    	$clone.find('input[type=text],textarea').attr('value','');
    	
    	//init ss if it's a bar graph
    	if( $clone.find('.percent').length > 0 ) {
    		/*$($clone).find('.slider, .output').remove();
    		$($clone).find('.percent').simpleSlider({
    			range: [1,100],
    			step: '1'
    		});*/
    	}
    	
    	//init new upload button and clear image if it's an upload
    	if( $clone.find('.redux-opts-upload').length > 0 ) {
    		$clone.find('.redux-opts-screenshot').attr('src','');
    		$clone.find('.redux-opts-upload-remove').hide();
    		$clone.find('.redux-opts-upload').css('display','inline-block');
    		setTimeout(function(){ initUpload($clone) },200);
    	}
    	
    	//append clone
		$(this).prevAll('.shortcode-dynamic-items').append($clone);
		
		
		if( $clone.find('.percent').length > 0 ) calcPercent();
	
		return false;
    });
	
    $('body').on('click', '.remove-list-item', function(){
    	if($(this).parent().find('.shortcode-dynamic-item').length > 1){
    		$(this).parent().find('#options-item .shortcode-dynamic-item:last').remove();
			dynamic_items();	
    	}
    	if($(this).parent().find('.shortcode-dynamic-item').length == 1) $(this).hide();
    	
    	
		return false;
    });
    
    //hide remove btn to start
    $('.remove-list-item').hide();
	
  $('body').on('keyup','.shortcode-dynamic-item-input, .shortcode-dynamic-item-text', function(){ dynamic_items(); });
	$("body").on("input propertychange", '.shortcode-dynamic-item textarea', function(){ dynamic_items(); });
	
	//icon selection
	$('.icon-option i').click(function(){
		$('.icon-option i').removeClass('selected');
		$(this).addClass('selected');
	});
	
	//icon set selection
	$('select[name="icon-set-select"]').change(function(){
		var $selected_set = $(this).val();
		$(this).parents('.shortcode-options').find('.icon-option').hide();
		$(this).parents('.shortcode-options').find('.icon-option').next('.clear').hide();
		$(this).parents('.shortcode-options').find('.icon-option.'+$selected_set).stop(true,true).fadeIn();
		$(this).parents('.shortcode-options').find('.icon-option.'+$selected_set).next('.clear').show();

		//svg icons
		if($selected_set == 'linea') {		
			$('label[for="shortcode-option-icon-size-regular"]').parent().slideUp();
			$('label[for="shortcode-option-icon-size-regular"]').parent().prev('.label').slideUp();
			$('label[for="shortcode-option-icon-size-regular"]').parent().next('.clear').slideUp();
			$('label[for="shortcode-option-icon-size-regular"]').parent().find('input[type="radio"]').prop('checked', false);
			$('#options-icon input[data-attrname="icon_size"]').val('');
			$('#options-icon input[data-attrname="icon_size"], #options-icon input.enable_animation').parent().slideDown();
			$('#options-icon input[data-attrname="icon_size"], #options-icon input.enable_animation').parent().next('.clear').slideDown();
			$('#options-icon input[data-attrname="icon_size"], #options-icon input.enable_animation').parent().prev('.label').slideDown();
			if($('#options-icon input.enable_animation').is(':checked')) {
				$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().slideDown();
				$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().next('.clear').slideDown();
				$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().prev('.label').slideDown();
			}
			$('#options-icon #color').trigger('change');
		} else {
			$('label[for="shortcode-option-icon-size-regular"]').parent().slideDown();
			$('label[for="shortcode-option-icon-size-regular"]').parent().prev('.label').slideDown();
			$('label[for="shortcode-option-icon-size-regular"]').parent().next('.clear').slideDown();
			$('#options-icon input[data-attrname="icon_size"], #options-icon input.enable_animation').parent().slideUp();
			$('#options-icon input[data-attrname="icon_size"], #options-icon input.enable_animation').parent().next('.clear').slideUp();
			$('#options-icon input[data-attrname="icon_size"], #options-icon input.enable_animation').parent().prev('.label').slideUp();
			
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().slideUp();
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().next('.clear').slideUp();
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().prev('.label').slideUp();
		
		}
	});
  //moved to nectar-shortcode-generator.js
	//$('select[name="icon-set-select"]').trigger('change');

	//animation delay relation
	$('#options-icon input.enable_animation').change(function(){
		if($('#options-icon input.enable_animation').is(':checked')) {
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().slideDown();
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().next('.clear').slideDown();
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().prev('.label').slideDown();
		} else {
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().slideUp();
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().next('.clear').slideUp();
			$('#options-icon input[data-attrname="animation_delay"], #options-icon select#animation_speed').parent().prev('.label').slideUp();
		}
	});
	
	//color change
	$('#options-icon #color').change(function(){
		if($(this).val() == 'Extra-Color-Gradient-1' || $(this).val() == 'Extra-Color-Gradient-2' || $('select[name="icon-set-select"]').val() != 'linea') {
			$('#options-icon #enable_animation').prop('checked', false);
			$('#options-icon label[for="enable_animation"]').parent().slideUp();
			$('#options-icon label[for="enable_animation"]').parent().next().slideUp();
			$('#options-icon label[for="enable_animation"]').parent().next().next().slideUp();
			$('#options-icon input.enable_animation').trigger('change');
			$('#options-icon select#animation_speed, #options-icon input[data-attrname="animation_delay"]').addClass('skip-processing');
		} else {
			$('#options-icon label[for="enable_animation"]').parent().slideDown();
			$('#options-icon label[for="enable_animation"]').parent().next().slideDown();
			$('#options-icon label[for="enable_animation"]').parent().next().next().slideDown();
			$('#options-icon select#animation_speed, #options-icon input[data-attrname="animation_delay"]').removeClass('skip-processing');
		}
	});
  //moved to nectar-shortcode-generator.js
	//$('#options-icon #color').trigger('change');

	//starting category population
	$('.starting_category').hide();
	$('.starting_category').next('.clear').hide();
	$('#options-nectar_portfolio #starting_category option:first').remove();
	$('#options-nectar_portfolio #starting_category').prepend('<option value="default">Default</option>')
	$('#options-nectar_portfolio #starting_category option:first').attr('selected','selected');
	
	$('#options-nectar_portfolio #enable_sortable').click(function(){
		var $this = $(this);
	    if ($this.is(':checked')) {
			$('.starting_category').stop().slideDown();
			$('.starting_category').next('.clear').stop().slideDown();
	    } else {
	    	$('.starting_category').stop().slideUp();
	    	$('.starting_category').next('.clear').stop().slideUp();
   		}
	});
	
	$("#options-nectar_portfolio #category").change(function(){
		var selectedCats = $(this).val();
		
		if(selectedCats == 'all') {
			$('#options-nectar_portfolio #starting_category option').removeAttr('disabled').removeAttr('selected').show();
		} else {
			$('#options-nectar_portfolio #starting_category option:not([value="default"])').attr('disabled','disabled').removeAttr('selected').hide();
			for(var i=0; i < selectedCats.length; i++){
				$('#options-nectar_portfolio #starting_category option[value="' + selectedCats[i] + '"]').removeAttr('disabled').show();
			}
			$('#options-nectar_portfolio #starting_category option:not([disabled])').first().attr('selected','selected');
		}
	});
	
	
	//button hover color
	$('#options-button #color').change(function(){
		var $selected_style = $(this).val();
		
		if($selected_style == 'see-through-2' || $selected_style == 'see-through-3'){
			$('#options-button input.popup-colorpicker').each(function(){
				
				if($(this).parents('.content').prev('.label').find('strong').text() == 'Hover BG Color:') {
					$(this).parents('.content').show();
					$(this).parents('.content').prev('.label').show();
					$(this).parents('.content').next('.clear').show();
				} 

				if($(this).parents('.content').prev('.label').find('strong').text() == 'Color Override:') {
					$(this).parents('.content').show();
					$(this).parents('.content').prev('.label').show();
					$(this).parents('.content').next('.clear').show();
				} 
				
				$('#hover_text_color_override').parents('.content').show();
				$('#hover_text_color_override').parents('.content').prev('.label').show();
				$('#hover_text_color_override').parents('.content').next('.clear').show();

				$('#options-button [name="icon-set-select"]').parents('.content').show();
				$('#options-button [name="icon-set-select"]').parents('.content').prev('.label').show();
				$('#options-button [name="icon-set-select"]').parents('.content').next('.clear').show();
				
			});
		} else if ($selected_style == 'extra-color-gradient-1' || $selected_style == 'extra-color-gradient-2' || $selected_style == 'see-through-extra-color-gradient-1' || $selected_style == 'see-through-extra-color-gradient-2') {

			$('#options-button input.popup-colorpicker').each(function(){
				
				if($(this).parents('.content').prev('.label').find('strong').text() == 'Hover BG Color:') {
					$(this).parents('.content').hide();
					$(this).parents('.content').prev('.label').hide();
					$(this).parents('.content').next('.clear').hide();
				} 

				if($(this).parents('.content').prev('.label').find('strong').text() == 'Color Override:') {
					$(this).parents('.content').hide();
					$(this).parents('.content').prev('.label').hide();
					$(this).parents('.content').next('.clear').hide();
				} 

			});

			$('#hover_text_color_override').parents('.content').hide();
			$('#hover_text_color_override').parents('.content').prev('.label').hide();
			$('#hover_text_color_override').parents('.content').next('.clear').hide();

			//$('#options-button [name="icon-set-select"]').parents('.content').hide();
			//$('#options-button [name="icon-set-select"]').parents('.content').prev('.label').hide();
			//$('#options-button [name="icon-set-select"]').parents('.content').next('.clear').hide();

		} else {
			$('#options-button input.popup-colorpicker').each(function(){
				
				if($(this).parents('.content').prev('.label').find('strong').text() == 'Hover BG Color:') {
					$(this).parents('.content').hide();
					$(this).parents('.content').prev('.label').hide();
					$(this).parents('.content').next('.clear').hide();
				} 

				if($(this).parents('.content').prev('.label').find('strong').text() == 'Color Override:') {
					$(this).parents('.content').show();
					$(this).parents('.content').prev('.label').show();
					$(this).parents('.content').next('.clear').show();
				} 
				
				$('#hover_text_color_override').parents('.content').hide();
				$('#hover_text_color_override').parents('.content').prev('.label').hide();
				$('#hover_text_color_override').parents('.content').next('.clear').hide();

				//$('#options-button [name="icon-set-select"]').parents('.content').show();
				//$('#options-button [name="icon-set-select"]').parents('.content').prev('.label').show();
				//$('#options-button [name="icon-set-select"]').parents('.content').next('.clear').show();
				
			});
		}
	});
	
  //moved to nectar-shortcode-generator.js
	//$('#options-button #color').trigger('change');
	
	
	function resetFileds(){
		//reset data
		$('#nectar-sc-generator').find('input:text, input:password, input:file, textarea').val('');
		$('#nectar-sc-generator').find('select:not(#nectar-shortcodes) option:first-child').attr("selected", "selected");
		$('#nectar-sc-generator').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		$('#nectar-sc-generator').find('.shortcode-options').each(function(){
			$(this).find('.shortcode-dynamic-item').addClass('marked-for-removal');
			$(this).find('.shortcode-dynamic-item:first').removeClass('marked-for-removal');
			$(this).find('.shortcode-dynamic-item.marked-for-removal').remove();
		});
		$('#nectar-sc-generator').find('.redux-opts-screenshot').attr('src','');
		$('#nectar-sc-generator').find('.redux-opts-upload-remove').hide();
		$('#nectar-sc-generator').find('.redux-opts-upload').show();
		$('#nectar-sc-generator').find('.wp-color-result').attr('style','');
		$('select[name="icon-set-select"]').trigger('change');
		
		////hideconstrain option 
		$('#constrain_max_cols').parents('.content').hide();
		$('#constrain_max_cols').parents('.content').prev('.label').hide();
		$('#constrain_max_cols').parents('.content').next('.clear').hide();
	
		//reset icons
		$('#nectar-sc-generator').find('.icon-option i').removeClass('selected');
		
		//starting category population
		$('.starting_category').hide();
		$('.starting_category').next('.clear').hide();
		
		//button hover color
		$('#options-button #color').trigger('change');
	}
    
});