/**
 * file upload js
 * @since 8.4
 **/

var isCartBlock = false;
var upload_instance = Array();
var file_count = Array();
var $filelist_DIV = Array();
var ppom_file_progress = '';
var featherEditor = '';
var uploaderInstances = {};
var Cropped_Data_Captured = false;

jQuery(function($){
	
	// If cropper input found in fields
	if( ppom_get_field_meta_by_type('cropper').length > 0) {
	    
	    var wc_cart_form = $( 'form.cart' );
        $(wc_cart_form).on('submit',function(e) {
            
            // e.preventDefault();
            var cropper_fields = ppom_get_field_meta_by_type('cropper');
            $.each(cropper_fields, function(i, cropper){
                var cropper_name = cropper.data_name;
                ppom_generate_cropper_data_for_cart(cropper.data_name);
            
            });
        });
	}

	$(document).on('ppom_image_ready', function(e){
	    
	    var image_url   = e.image_url;
	    var image_id	= e.image.id;
	    var data_name   = e.data_name;
	    var input_type	= e.input_type;
	    
	    if(input_type === 'cropper'){
	    	ppom_show_cropped_preview( data_name, image_url, image_id );
	    }
	    
	    // moving modal to body end
        $('.ppom-modals').appendTo('body');
	});
	
	// On file removed
	$(document).on('ppom_uploaded_file_removed', function(e) {
	    
	    var field_name  = e.field_name;
	   // var fileid      = e.fileid;
	   
	    ppom_reset_cropping_preview(field_name);
		ppom_update_option_prices();
	});
    
    
    // Croppie update size
    $('.ppom-croppie-preview').on('change', '.ppom-cropping-size', function(e) {
        
        var data_name = $(this).data('field_name');
        var cropp_preview_container = jQuery(".ppom-croppie-wrapper-"+data_name);
        var v_width = $('option:selected', this).data('width');
        var v_height = $('option:selected', this).data('height');
    
        cropp_preview_container.find('.croppie-container').each(function(i, croppie_dom){
            
            var image_id = jQuery(croppie_dom).attr('data-image_id');
            $(croppie_dom).croppie('destroy');
            var viewport = {'width':v_width,'height':v_height};
            ppom_set_croppie_options(data_name, viewport, image_id);
        });
       
    });
    
    // Deleting File
    $(".ppom-wrapper").on('click','.u_i_c_tools_del', function(e){
    		e.preventDefault();
    
    		var del_message = ppom_file_vars.delete_file_msg;
    		var a = confirm(del_message);
    		if(a){
    			// it is removing from uploader instance
    			var fileid = $(this).closest('.u_i_c_box').attr("data-fileid");
    			var file_data_name = $(this).closest('div.ppom-field-wrapper').attr("data-data_name");
                file_count[file_data_name] = 0;
    			
    			upload_instance[file_data_name].removeFile(fileid);
    
    			var filename  = $('input:checkbox[name="ppom[fields]['+file_data_name+']['+fileid+'][org]"]').val();
    			
    			// it is removing physically if uploaded
    			$("#u_i_c_"+fileid).find('img').attr('src', ppom_file_vars.plugin_url+'/images/loading.gif');
    			
    			// console.log('filename ppom[fields][<?php echo ]$args['id']?>['+fileid+']');
    			var data = {action: 'ppom_delete_file', file_name: filename};
    			
    			$.post(ppom_file_vars.ajaxurl, data, function(resp){
    				alert(resp);
    				$("#u_i_c_"+fileid).hide(500).remove();
    
    				// it is removing for input Holder
    				$('input:checkbox[name="ppom[fields]['+file_data_name+']['+fileid+'][org]"]').remove();
    				
    				// Removing file container
    				$(this).closest('.u_i_c_box').remove();
    				
    				// Trigger
    				$.event.trigger({type: "ppom_uploaded_file_removed",
                                field_name:file_data_name,
                                fileid: fileid,
                                time: new Date()
                                });
    				
    				file_count[file_data_name] -= 1;		
    			});
    		}
    	});
	
    $.each(ppom_file_vars.file_inputs, function(index, file_input){
        
        var file_data_name = file_input.data_name;
        file_count[file_data_name] = 0;
    
    	$filelist_DIV[file_data_name] = $('#filelist-'+file_data_name);
    	
    	ppom_setup_file_upload_input( file_input );
    	
    });         // $.each(ppom_file_vars

	
});	//	jQuery(function($){});

// generate thumbbox 
function add_thumb_box(file, $filelist_DIV){

	var inner_html	= '<div class="u_i_c_thumb"><div class="progress_bar"><span class="progress_bar_runner"></span><span class="progress_bar_number">(' + plupload.formatSize(file.size) + ')<span></div></div>';
	inner_html		+= '<div class="u_i_c_name"><strong>' + file.name + '</strong></div>';
	  
	jQuery( '<div />', {
		'id'	: 'u_i_c_'+file.id,
		'class'	: 'u_i_c_box',
		'data-fileid': file.id,
		'html'	: inner_html,
		
	}).appendTo($filelist_DIV);

	// clearfix
	// 1- removing last clearfix first
	$filelist_DIV.find('.u_i_c_box_clearfix').remove();
	
	jQuery( '<div />', {    
		'class'	: 'u_i_c_box_clearfix',				
	}).appendTo($filelist_DIV);
	
}


// save croped/edited photo
function save_edited_photo(img_id, photo_url){
			
	//console.log(img_id);
	
	//setting new image width to 75
	jQuery('#'+img_id).attr('width', 75);
	
	//disabling add to cart button for a while
	jQuery('form.cart').block({
                message: null,
                overlayCSS: {
                background: "#fff",
                opacity: .6
		                    }
         });
	var post_data = {action: 'ppom_save_edited_photo', image_url: photo_url,
						filename: jQuery('#'+img_id).attr('data-filename')
	};
	
	jQuery.post(ppom_file_vars.ajaxurl, post_data, function(resp) {
	    
	    //console.log( resp );
	    jQuery('form.cart').unblock();
	    
	});
}

// Cropping image with Croppie
function ppom_show_cropped_preview( file_name, image_url, image_id ) {
    
    
    var cropp_preview_container = jQuery(".ppom-croppie-wrapper-"+file_name);
    // Enable size option
    cropp_preview_container.find('.ppom-cropping-size').show();
    
    var croppie_container = jQuery('<div/>')
                            .addClass('ppom-croppie-preview-'+image_id)
                            .attr('data-image_id', image_id)
                            .appendTo(cropp_preview_container);
                            
    // $filelist_DIV[file_name]['croppie']     = cropp_preview_container.find('.ppom-croppie-preview');
    $filelist_DIV[file_name]['croppie'][image_id]     = croppie_container;
	$filelist_DIV[file_name]['image_id']    = image_id;    
	$filelist_DIV[file_name]['image_url']   = image_url;
	
	var viewport = undefined;
	ppom_set_croppie_options( file_name, viewport, image_id );
}

function ppom_set_croppie_options( file_name, viewport, image_id ) {
    
    var croppie_options = ppom_file_vars.croppie_options;
	jQuery.each(croppie_options, function(field_name, option){
	    
	    if( file_name === field_name ) {
	        
	       option.url = $filelist_DIV[file_name]['image_url'];
	       if( viewport !== undefined ) {
	           option.viewport = viewport;
	       }
        // console.log(option);
	    $filelist_DIV[file_name]['croppie'][image_id].croppie(option);
	    }
	});
}

// Reset cropping when image removed
function ppom_reset_cropping_preview(file_name) {
    
    var cropp_preview_container = jQuery(".ppom-croppie-wrapper-"+file_name);
    // Reseting preview DOM
    cropp_preview_container.find('.ppom-croppie-preview').html('');
}

// Attach FILE API with DOM
function ppom_setup_file_upload_input( file_input ) {
    
    var file_data_name = file_input.data_name;
    
    if( upload_instance[file_data_name] !== undefined ) {
        upload_instance[file_data_name].destroy();
    }
    
    var ppom_file_data = {'action'          : 'ppom_upload_file', 
                            'settings'      : file_input,
                            'ppom_nonce'    : ppom_file_vars.ppom_file_upload_nonce
                        }
    upload_instance[file_data_name] = new plupload.Uploader({
    		runtimes 			: ppom_file_vars.plupload_runtime,
    		browse_button 		: 'selectfiles-'+file_data_name, // you can pass in id...
    		container			: 'ppom-file-container-'+file_data_name, // ... or DOM Element itself
    		drop_element		: 'ppom-file-container-'+file_data_name,
    		url 				: ppom_file_vars.ajaxurl,
    		multipart_params 	: ppom_file_data,
    		max_file_size 		: file_input.file_size,
    		max_file_count 		: parseInt(file_input.files_allowed),
    	    chunk_size: '1mb',
    		
    	  	filters : {
    			mime_types: [
    				{title : "Filetypes", extensions : file_input.file_types}
    			]
    		},
    		
    		init: {
    			PostInit: function() {
    			    
    				// $filelist_DIV[file_data_name].html('');
    
    				/*$('#uploadfiles-'+file_data_name).bind('click', function() {
    					upload_instance[file_data_name].start();
    					return false;
    				});*/
    			},
    
    			FilesAdded: function(up, files) {
    
                    // Adding progress bar
                	var file_pb = jQuery('<div/>')
                	               .addClass('progress')
                	               .appendTo($filelist_DIV[file_data_name]);
                	var file_pb_runner = jQuery('<div/>')
                	                    .addClass('progress-bar')
                	                    .attr('role', 'progressbar')
                	                    .attr('aria-valuenow', 0)
                	                    .attr('aria-valuemin', 0)
                	                    .attr('aria-valuemax', 100)
                	                    .css('height', '15px')
                	                    .css('width', 0)
                	                    .appendTo(file_pb);
                	                    
    				var files_added = files.length;
    				var max_count_error = false;
    
    				if((file_count[file_data_name] + files_added) > upload_instance[file_data_name].settings.max_file_count){
    					alert(upload_instance[file_data_name].settings.max_file_count + ppom_file_vars.mesage_max_files_limit);
    				}else{
    					
    					plupload.each(files, function (file) {
    						file_count[file_data_name]++;
    			    		// Code to add pending file details, if you want
    			            add_thumb_box(file, $filelist_DIV[file_data_name], up);
    			            setTimeout('upload_instance[\''+file_data_name+'\'].start()', 100);
    			        });
    				}
    			    
    				
    			},
    			
    			FileUploaded: function(up, file, info){
    				
    				// console.log($.parseJSON(info.response));
    
    				var obj_resp = jQuery.parseJSON(info.response);
    				
    				if(obj_resp.file_name === 'ThumbNotFound'){
    					
    					upload_instance[file_data_name].removeFile(file.id);
    					jQuery("#u_i_c_"+file.id).hide(500).remove();
    					file_count[file_data_name]--;	
    					
    					alert('There is some error please try again');
    					return;
    					
    				}else if(obj_resp.status == 'error'){
    					
    					upload_instance[file_data_name].removeFile(file.id);
    					
    					jQuery("#u_i_c_"+file.id).hide(500).remove();
    
    					file_count[file_data_name]--;	
    					alert(obj_resp.message);
    					return;
    				};
    				
    				var file_thumb 	= ''; 
    
                    /*if( file_input.file_cost != "" ) {
                        jQuery('input[name="woo_file_cost"]').val( file_input.file_cost );
                    }*/
    
    				$filelist_DIV[file_data_name].find('#u_i_c_' + file.id).html(obj_resp.html)
    				.trigger({
                            	type: "ppom_image_ready",
                            	image: file,
                            	data_name: file_data_name,
                            	input_type: file_input.type,
                            	image_url: obj_resp.file_url,
                            	image_resp: obj_resp,
                            	time: new Date()
                            });
    
    				
    				// checking if uploaded file is thumb
    				ext = obj_resp.file_name.substring(obj_resp.file_name.lastIndexOf('.') + 1);					
    				ext = ext.toLowerCase();
    				
    				if(ext == 'png' || ext == 'gif' || ext == 'jpg' || ext == 'jpeg'){
    
    					
    					var file_full 	= ppom_file_vars.file_upload_path + obj_resp.file_name;
    					// thumb thickbox only shown if it is image
    					$filelist_DIV[file_data_name]
    					.find('#u_i_c_' + file.id)
    					.find('.u_i_c_thumb')
    					.append('<div style="display:none" id="u_i_c_big' + file.id + '"><img src="'+file_full+ '" /></div>');
    
    					// Aviary editing tools
    					if( file_input.photo_editing === 'on' && ppom_file_vars.aviary_api_key !== ''){
    						var editing_tools = file_input.editing_tools;
    						$filelist_DIV[file_data_name]
    						 .find('#u_i_c_' + file.id)
    						 .find('.u_i_c_tools_edit')
    						 .append('<a onclick="return   (\'thumb_'+file.id+'\', \''+file_full+'\', \''+obj_resp.file_name+'\', \''+editing_tools+'\')" href="javascript:;" title="Edit"><img width="15" src="'+ppom_file_vars.plugin_url+'/images/edit.png" /></a>');
    					}
    
    					is_image = true;
    				}else{
    					file_thumb = ppom_file_vars.plugin_url+'/images/file.png';
    					$filelist_DIV[file_data_name].find('#u_i_c_' + file.id)
    					                       .find('.u_i_c_thumb')
    					                       .html('<img src="'+file_thumb+ '" id="thumb_'+file.id+'" />')
    					is_image = false;
    				}
    				
    				// adding checkbox input to Hold uploaded file name as array
    				var file_container  = $filelist_DIV[file_data_name].find('.u_i_c_box');
    				var fileCheck = jQuery('<input checked="checked" name="ppom[fields]['+file_data_name+']['+file.id+'][org]" type="checkbox"/>')
    				                .attr('data-price', file_input.file_cost)
    				                .attr('data-label', obj_resp.file_name)
    				                .attr('data-title', file_input.title)
    				                .attr('data-onetime', file_input.onetime)
    				                .val(obj_resp.file_name)
    				                .css('display','none')
    				                .addClass('ppom-file-cb-'+file_data_name)
    				                .addClass('ppom-file-cb')
    				                .appendTo(file_container);
    				ppom_update_option_prices();
    				
    				jQuery('form.cart').unblock();
    				isCartBlock = false;
    				
    				// Removing progressbar
    				$filelist_DIV[file_data_name].find('.progress').remove();
    				
    				// Trigger
    				jQuery.event.trigger({type: "ppom_file_uploaded",
                                file_meta:file_input,
                                file_resp: obj_resp,
                                time: new Date()
                                });
    			},
    
    			UploadProgress: function(up, file) {
    				
    				$filelist_DIV[file_data_name].find('.progress-bar').css('width', file.percent + '%');
    				
    				//disabling add to cart button for a while
    				if( ! isCartBlock ){
    				jQuery('form.cart').block({
    		                    message: null,
    		                    overlayCSS: {
    		                    background: "#fff",
    		                    opacity: .6,
    		                    onBlock: function() { 
    				                isCartBlock = true;
    				            } 
    					                    }
    			         });
    				}
    			},
    
    			Error: function(up, err) {
    				//document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
    				alert("\nError #" + err.code + ": " + err.message);
    			}
    		}
    		
    
    	});
    	
    	upload_instance[file_data_name].init();
    	uploaderInstances[file_data_name] = upload_instance[file_data_name];
}

// Generate Cropped image data for cart
function ppom_generate_cropper_data_for_cart(field_name) {
    
    var cropp_preview_container = jQuery(".ppom-croppie-wrapper-"+field_name);
    
    cropp_preview_container.find('.croppie-container').each(function(i, croppie_dom){
        
        var image_id = jQuery(croppie_dom).attr('data-image_id');
        jQuery(croppie_dom).croppie('result', {
        	type: 'rawcanvas',
        	// size: { width: 300, height: 300 },
        	format: 'png'
        }).then(function (canvas) {
    		var image_url = canvas.toDataURL();
    		var fileCheck = jQuery('<input checked="checked" name="ppom[fields]['+field_name+']['+image_id+'][cropped]" type="checkbox"/>')
    				                .val(image_url)
    				                .css('display','none')
    				                .appendTo($filelist_DIV[field_name]);
    		
    	});
    });
}