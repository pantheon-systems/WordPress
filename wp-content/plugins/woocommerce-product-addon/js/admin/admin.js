jQuery(function($) {

	// produc-basic-settings toggle area
	$('.produc-basic-settings').on('click', function(event){
		
		$("#formbox-1").find('table#form-main-settings').slideToggle();
		if ( $(this).children('span.dashicons').hasClass('dashicons-plus') ) {

			$(this).children('span.dashicons').removeClass('dashicons-plus').addClass('dashicons-minus');
		}else if($(this).children('span.dashicons').hasClass('dashicons-minus')){

			$(this).children('span.dashicons').removeClass('dashicons-minus').addClass('dashicons-plus');
		};
	});
	$('#postcustoms > h3').on('click', function(event){
		
		if ( $(this).children('span.dashicons').hasClass('dashicons-plus') ) {
			$("ul#meta-input-holder li").find('table').slideDown();
			$(this).children('span.dashicons').removeClass('dashicons-plus').addClass('dashicons-minus');
		}else if($(this).children('span.dashicons').hasClass('dashicons-minus')){
			$("ul#meta-input-holder li").find('table').slideUp();
			$(this).children('span.dashicons').removeClass('dashicons-minus').addClass('dashicons-plus');
		};
	});
	/* === Hide and Show blocks ( import & export block and Add product meta block ) === */

	$('.import-export-btn').on('click', function(event){
		event.preventDefault();
		$( ".import-export-block" ).show( "fast" );
		$( ".product-meta-block" ).hide();
	});

	$('.cancle-import-export-btn').on('click', function(event){
		event.preventDefault();
		$( ".import-export-block" ).hide();
		$( ".product-meta-block" ).show("fast");
	});

	$('.product_checkbox').on('click', function(event){
		
		var checkboxProducts = $('.product_checkbox').map(function() {
		    return this.value;
		}).get();

		var checkedProducts = $('.product_checkbox:checked').map(function() {
		    return this.value;
		}).get();

		if (checkboxProducts.length == checkedProducts.length ) {
			$('#all-select-products-head-btn, #all-select-products-foot-btn').prop('checked', true);
		}else{
			$('#all-select-products-head-btn, #all-select-products-foot-btn').prop('checked', false);
		};

		$('#selected_products_count').html();
		$('#selected_products_count').html(checkedProducts.length);
	});
	
	// select all on checkbox click
	$('#all-select-products-head-btn, #all-select-products-foot-btn').on('click', function(event){
		
		$('.products-table input:checkbox').not(this).prop('checked', this.checked);
		var checkedProducts = $('.product_checkbox:checked').map(function() {
		    return this.value;
		}).get();
		$('#selected_products_count').html();
		$('#selected_products_count').html(checkedProducts.length);
	});

	/*
	** delete selected products
	*/
	$('#delete_selected_products_btn').on('click', function(event){
		
		event.preventDefault();
		
		var checkedProducts_ids;
		checkedProducts_ids = $('.product_checkbox:checked').map(function() {
		    return parseInt(this.value);
		}).get();
		// console.log(checkedProducts_ids);
		var a = confirm('Are you sure to delete this file?');
		if (a) {
			jQuery.post(ajaxurl, {
				action : 'ppom_delete_selected_meta',
				productmeta_ids : checkedProducts_ids
			}, function(resp) {
				// alert(data);
				alert(resp);
				window.location.reload(true);

			});
		}
	});

	
	/* ================== Bulk Quantities functions ============= */

	$('#form-meta-setting').on('click', '#irow', function(){
	    if($('#form-meta-setting .qty-val').val()){
	        $('#form-meta-setting #mtable tbody').append($("#form-meta-setting #mtable tbody tr:last").clone());
	        // $('#form-meta-setting #mtable tbody tr:last :checkbox').attr('checked',false);
	        $('#form-meta-setting #mtable tbody tr:last td:first').html($('#form-meta-setting .qty-val').val() + '<span class="dashicons dashicons-dismiss delete-row" style="cursor: pointer;color: red;"></span>');
	    }else{alert('Please provide Quantity Eg: 1-10');}
	});
	$('#form-meta-setting').on('click', '#icol', function(){
	    if($('#form-meta-setting .var-val').val()){
	        $('#form-meta-setting #mtable tr').append($("<td>"));
	        $('#form-meta-setting #mtable thead tr>td:last').html($('#form-meta-setting .var-val').val() + '<span class="dashicons dashicons-dismiss delete-col" style="cursor: pointer;color: red;"></span>' );
	        $('#form-meta-setting #mtable tbody tr').each(function(){$(this).children('td:last').append($('<input type="text" class="small-text">'))});
	    }else{alert('Please provide Variation name Eg: color');}
	});

	$('#form-meta-setting').on('click', '.save-bulk-data', function(event) {
		event.preventDefault();
		var bulk_wrap = $(this).closest('.bulk-quantity-wrap');
		bulk_wrap.find('table').find('input').each(function(index, el) {
			var td_wrap = $(this);
			td_wrap.closest('td').html($(this).val());
		});
		var bulkData = jQuery('#form-meta-setting #mtable').tableToJSON();
		bulk_wrap.find('.saving-bulk-qty').val(JSON.stringify(bulkData));
		
		$('.ui-tabs-nav').find('.button-primary').trigger('click');

	});

	$("table").on("click", ".delete-col", function ( event ) {
	    var ndx = $(this).parent().index() + 1;
	    $("td", event.delegateTarget).remove(":nth-child(" + ndx + ")");
	});

	$("table").on("click", ".delete-row", function ( event ) {
	    $(this).closest('tr').remove();
	});
	
	
	// ================== new meta form creator ===================

	var meta_removed;

	$('#form-meta-setting').on('click','.dashicons-trash', function(event){
		var list_item = $(this).closest('li');
		$("#remove-meta-confirm").dialog("open");
		meta_removed = $(list_item);
		event.preventDefault();
		console.log("working");
	});

	$('#form-meta-setting').on('click','.dashicons-image-rotate-right', function(event){
		var list_item = $(this).closest('li');
		list_item.clone(true).insertAfter(list_item);
	});
	//attaching hide and delete events for existing meta data
	$("ul#meta-input-holder li").each(function(i, item){
/*		$(item).find("h3").on('click', '.dashicons-image-flip-vertical', function(e) {
			$(item).find("table").slideToggle(300);
		});*/
		// for delete box
		$(item).find(".dashicons-trash").click(function(e) {
			console.log('delete button');
			$("#remove-meta-confirm").dialog("open");
			meta_removed = $(item);
		});
		// for copy box
		$(item).find(".dashicons-image-rotate-right").click(function(e) {
			$(item).clone(true).insertAfter(item);
			console.log('each');
		});	
	});
	
	$('.dashicons-arrow-up-alt2').click(function(e){
		$("ul#meta-input-holder li").find('table').slideUp();
	});
	$('.dashicons-arrow-down-alt2').click(function(e){
		$("ul#meta-input-holder li").find('table').slideDown();
	});
	
	$(document).on('click', '.dashicons-image-flip-vertical', function(e) {
		$(this).closest('li').find("table").slideToggle(300);
	});	
	
	
	$("#nmpersonalizedproduct-form-generator").tabs();
	$("#tab-container").tabs();
	
		$("ul.ppom-options-container").sortable({
		revert : true
		});
		

	$("ul#meta-input-holder").sortable({
		revert : true,
		start : function(event, ui) {
			
			$(ui.helper).addClass("ui-helper");
            
			$(ui.helper.context).find('h3,span').show();
		},
		stop : function(event, ui) {
			// only attach click event when dropped from right panel
			if (ui.originalPosition.left > 20) {
				$(ui.item).find(".dashicons-image-flip-vertical").click(function(e) {
					$(this).parent('.inputdata').find("table").slideToggle(300);
				});

				// // for delete box
				// $(ui.item).find(".dashicons-trash").click(function(e) {
				// 	$("#remove-meta-confirm").dialog("open");
				// 	meta_removed = $(ui.item);
				// 	console.log('second');
				// });
				
				// // for copy box
				// $(ui.item).find(".dashicons-image-rotate-right").click(function(e) {
				// 	$(ui.item).clone(true).insertAfter(ui.item);
				// });
			}
		}
	});

	// =========== remove dialog ===========
	$("#remove-meta-confirm").dialog({
		resizable : false,
		height : 160,
		autoOpen : false,
		modal : true,
		buttons : {
			"Remove" : function() {
				$(this).dialog("close");
				meta_removed.remove();
			},
			Cancel : function() {
				$(this).dialog("close");
			}
		}
	});

	$("#nm-input-types li").draggable(
			{
				connectToSortable : "ul#meta-input-holder",
				helper : "clone",
				revert : "invalid",
				start: function(event, ui){
					// ui.helper.width('100%');
					// ui.helper.height('auto');
					$(ui.helper).addClass("ui-helper");

					$("ul#meta-input-holder li").find('table').slideUp();
				},
				stop : function(event, ui) {
					console.log('stop end');
					// $(ui.helper).find('table');
					$(ui.helper).find('table').slideDown( 'slow' );
					// console.log($('.ui-draggable'));
					// ui.helper.width('100%');
					// ui.helper.height('auto');

					$('.ui-sortable .ui-draggable').removeClass(
							'input-type-item').find('div').addClass('inputdata');

					// now replacing the icons with arrow
					$('.inputdata').find('.ui-icon-arrow-4').removeClass(
							'ui-icon-arrow-4')
							.addClass('dashicons-image-flip-vertical dashicons-image-flip-vertical')
							.attr('title', 'Slide Up/Down');
					$('.inputdata').find('.ui-icon-placehorder').removeClass(
							'ui-icon-placehorder').addClass(
							'dashicons-trash')
							.attr('title', 'Remove');
					$('.inputdata').find('.ui-icon-placehorder-copy').removeClass(
							'ui-icon-placehorder-copy').addClass(
							'dashicons-image-rotate-right')
							.attr('title', 'Copy');;

				}
				// $(".form-meta-setting ul").accordion( "refresh" );
			});
	//$("ul, li").disableSelection();

	// ================== new meta form creator ===================

	// add validation message if required
	$('input:checkbox[name="meta-required"]').change(function() {

		if ($(this).is(':checked')) {
			$(this).parent().find('span').show();
		} else {
			$(this).parent().find('span').hide();
		}
	});

	// increaing/saming the width of section's element
	$(".the-section").find('input, select, textarea').css({
		'width' : '35%'
	});

	$("#form-meta-setting").on('click', 'img.add_rule', function(){
		
		var $div    = $(this).closest('div');
		var $clone = $div.clone();
		$clone.find('strong').val('Rule just added');
		
		var $td = $div.closest('td');
		$td.append($clone);
	});
	
	$("#form-meta-setting").on('click', 'img.remove_rule', function(){
		
		var $div    = $(this).closest('div');
		var $td = $div.closest('td');
		if($td.find('div').length > 1)
			$div.remove();
		else
			alert('Not allowed');
	});
	
	/* ============= new options / remove options =============== */
	$("#form-meta-setting").on('click', 'img.add_option', function(){
			
			var $li    = $(this).closest('li');
			var $clone = $li.clone();
			// $clone.find('strong').val('Rule just added');
			// console.log($li);
			
			var $ul = $li.closest('ul');
			$ul.append($clone);
	});
	
	$("#form-meta-setting").on('click', 'img.remove_option', function(){
	
		var $li    = $(this).closest('li');
		var $ul 	= $li.closest('ul');
		if($ul.find('li').length > 1)
			$li.remove();
		else
			alert('Not allowed');
	});
	
	// making table sortable
	// make table rows sortable
	$('#nm-file-meta-admin tbody').sortable(
			{
				start : function(event, ui) {
					// fix firefox position issue when dragging.
					if (navigator.userAgent.toLowerCase().match(/firefox/)
							&& ui.helper !== undefined) {
						ui.helper.css('position', 'absolute').css('margin-top',
								$(window).scrollTop());
						// wire up event that changes the margin whenever the
						// window scrolls.
						$(window).bind(
								'scroll.sortableplaylist',
								function() {
									ui.helper.css('position', 'absolute')
											.css('margin-top',
													$(window).scrollTop());
								});
					}
				},
				beforeStop : function(event, ui) {
					// undo the firefox fix.
					if (navigator.userAgent.toLowerCase().match(/firefox/)
							&& ui.offset !== undefined) {
						$(window).unbind('scroll.sortableplaylist');
						ui.helper.css('margin-top', 0);
					}
				},
				helper : function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				},
				scroll : true,
				stop : function(event, ui) {
					// SAVE YOUR SORT ORDER
				}
			}).disableSelection();
	
	
	// condtions handling
	populate_conditional_elements();
	
	// ========= Visibility Roles ============
	$("#form-meta-setting").on('change', '#visibility', function(){
	
		if( $(this).val() === 'roles' ) {
			$('.visibility_role').show();
		} else {
			$('.visibility_role').hide();
		}
	});
	
	// ========== Measure options ==========
	$("#form-meta-setting").on('change', 'input[name="use_units"]', function() {
	   
		console.log($(this).prop('checked'));
		if( $(this).prop('checked') ) {
			$(this).closest('div.inputdata').find('tr.options').show();
		} else {
			$(this).closest('div.inputdata').find('tr.options').hide();
		}
	});
	$('input[name="use_units"]').change();
	
	// Apply DataTable to PPOM Meta List
	$('#ppom-meta-table').DataTable();
	
	
	
/* ============== pre uploaded images 1- Media uploader launcher ================= */
	
	var $uploaded_image_container;

	$("#form-meta-setting").on('click', 'input:button[name="pre_upload_image_button"]', function(){
		
		$uploaded_image_container = $(this).closest('div');
		
		wp.media.editor.send.attachment = function(props, attachment)
		{
			// console.log(attachment);
			var existing_images;
			var fileurl = attachment.url;
			var fileid	= attachment.id;
			var img_icon = '<img width="75" src="'+fileurl+'">';
			var url_field = '<input placeholder="url" style="width:100px" type="text" name="pre-upload-url"><br>';
			if (attachment.type !== 'image') {
				var img_icon = '<img width="75" src="'+attachment.icon+'">';
				url_field = '';
			}
			
			if(fileurl){
	        	var image_box 	 = '<table style="display:block">'; // Do not change this style
	        	image_box 		+= '<tr>';
	        	image_box 		+= '<td>'+img_icon+'</td>';
	        	image_box 		+= '<input type="hidden" name="pre-upload-link" value="'+fileurl+'">';
	        	image_box 		+= '<input type="hidden" name="pre-upload-id" value="'+fileid+'">';
	        	image_box 		+= '<td><input placeholder="title" style="width:100px" type="text" name="pre-upload-title"><br>';
	        	image_box 		+= '<input placeholder="price" style="width:100px" type="text" name="pre-upload-price"><br>';
	        	image_box 		+= url_field;
	        	image_box 		+= '<input style="width:100px; color:red" name="pre-upload-delete" type="button" class="button" value="Delete"><br>';
	        	image_box 		+= '</td></tr>';
	        	image_box 		+= '</table><br>';
	        	
	        	$uploaded_image_container.append(image_box);
	        	//console.log(image_box);
        }
		}
		
		wp.media.editor.open(this);
		
		return false;
	});
	
	$("#form-meta-setting").on('click', 'input:button[name="imageselect_button"]', function(){
		
		$uploaded_image_container = $(this).closest('div');
		
		wp.media.editor.send.attachment = function(props, attachment)
		{
			// console.log(attachment);
			var existing_images;
			var fileurl = attachment.url;
			var fileid	= attachment.id;
			var img_icon = '<img width="75" src="'+fileurl+'">';
			var url_field = '<input placeholder="Description" style="width:100px" type="text" name="imageselect-description"><br>';

			if(fileurl){
	        	var image_box 	 = '<table style="display:block">'; // Do not change this style
	        	image_box 		+= '<tr>';
	        	image_box 		+= '<td>'+img_icon+'</td>';
	        	image_box 		+= '<input type="hidden" name="imageselect-link" value="'+fileurl+'">';
	        	image_box 		+= '<input type="hidden" name="imageselect-id" value="'+fileid+'">';
	        	image_box 		+= '<td><input placeholder="title" style="width:100px" type="text" name="imageselect-title"><br>';
	        	image_box 		+= '<input placeholder="price" style="width:100px" type="text" name="imageselect-price"><br>';
	        	image_box 		+= url_field;
	        	image_box 		+= '<input style="width:100px; color:red" name="imageselect-delete" type="button" class="button" value="Delete"><br>';
	        	image_box 		+= '</td></tr>';
	        	image_box 		+= '</table><br>';
	        	
	        	$uploaded_image_container.append(image_box);
	        	//console.log(image_box);
        }
		}
		
		wp.media.editor.open(this);
		
		return false;
	});
    
    $("#form-meta-setting").on('click', 'input:button[name="imageselect-delete"]', function(){
    
    	$(this).closest('li.data-options').remove();
    });
    
    $("#form-meta-setting").on('click', 'input:button[name="pre-upload-delete"]', function(){
    
    	$(this).closest('li.data-options').remove();
    });
    
    /* ================== auto generate data name field ============= */
    $("#form-meta-setting").on('keyup', 'input[name="title"]', function(){
    
    	var dataname =$(this).closest('table').find('input[name="data_name"]');
    	var field_id = $(this).val().replace(/[^A-Z0-9]/ig, "_");
    	field_id = field_id.toLowerCase();
   		$(dataname).val( field_id );
    });
    
    // ======== Auto Generate option IDs
    $("#form-meta-setting").on('keyup', '.option-title', function(){
    
    	var closes_id = $(this).closest('li').find('.option-id');
    	var option_id = $(this).val().replace(/[^A-Z0-9]/ig, "_");
    	option_id = option_id.toLowerCase();
   		$(closes_id).val( option_id );
    });
    
    // ============ Loading Products in DataTable
    $('#ppom-meta-table_wrapper').on('click','a.ppom-products-modal', function(ev){
        
        
        ev.preventDefault();
            var ppom_id = $(this).data('ppom_id');
            
            var get_url = ajaxurl+'?action=ppom_get_products&ppom_id='+ppom_id;
            
            //@Fayaz: Add blockui here
            $.get( get_url, function(html){
            $('#ppom-product-modal .modal-body').html(html);
            $("#ppom_id").val(ppom_id);
            $('#ppom-product-modal').modal('show', {backdrop: 'static'});
        });
    });
    
    // Saving PPOM IDs
    $("#ppom-product-form").on('submit', function(ev){
       
        //@Fayaz: Add blockui here
        ev.preventDefault();
        var data = $(this).serialize();
        $.post(ajaxurl, data, function(resp) {
        
            alert(resp.message);
            window.location.reload();
            
        }, 'json');
    });
    
    $('#ppom-product-modal').on('show.bs.modal', function (e) {
        
        $(".ppom-table").DataTable();
    });
    
    
});

// saving form meta
function save_form_meta() {

	jQuery("#nm-saving-form").show();
	
	
	//usetting the photo_editing option is api key is not set
	if(jQuery('input[name="aviary_api_key"]').val() === "")
		jQuery('input[name="photo_editing"]').attr('checked',false);
	
	var product_meta_values = new Array();		//{};		//Array();
	jQuery("#meta-input-holder li").each(
			function(i, item) {

				var inner_array = {};
				inner_array['type']	= jQuery(item).attr('data-inputtype');
				
				jQuery(this).find('td.table-column-input').each(
						function(i, col) {

							var meta_input_type = jQuery(col).attr('data-type');
							var meta_input_name = jQuery(col).attr('data-name');
							var cb_value = '';

							if(meta_input_type == 'checkbox'){
								if(meta_input_name === 'editing_tools' || meta_input_name === 'cal_addon_disable_days'){
									cb_value = (jQuery(this).find('input:checkbox[name="' + meta_input_name + '[]"]:checked').serialize() === undefined ? '' : jQuery(this).find('input:checkbox[name="' + meta_input_name + '[]"]:checked').serialize());
									inner_array[meta_input_name] = cb_value;
								}else{
									cb_value = (jQuery(this).find('input:checkbox[name="' + meta_input_name + '"]:checked').val() === undefined ? '' : jQuery(this).find('input:checkbox[name="' + meta_input_name + '"]:checked').val());
									inner_array[meta_input_name] = cb_value;
								}
							}else if(meta_input_type == 'textarea'){
								inner_array[meta_input_name] = jQuery(this).find('textarea[name="' + meta_input_name + '"]').val();
							}else if(meta_input_type == 'select'){
								inner_array[meta_input_name] = jQuery(this).find('select[name="' + meta_input_name + '"]').val();
							}else if (meta_input_type == 'html-conditions') {
								
								var all_conditions = {};
								var the_conditions = new Array();	//{};
								
								all_conditions['visibility'] = jQuery(
										this)
										.find(
												'select[name="condition_visibility"]')
										.val();
								all_conditions['bound'] = jQuery(
										this)
										.find(
												'select[name="condition_bound"]')
										.val();
								jQuery(this).find('div.webcontact-rules').each(function(i, div_box){
								
									var the_rule = {};
									
									the_rule['elements'] = jQuery(
											this)
											.find(
													'select[name="condition_elements"]')
											.val();
									the_rule['operators'] = jQuery(
											this)
											.find(
													'select[name="condition_operators"]')
											.val();
									the_rule['element_values'] = jQuery(
											this)
											.find(
													'select[name="condition_element_values"]')
											.val();
									
									the_conditions.push(the_rule);
								});
								
								all_conditions['rules'] = the_conditions;
								inner_array[meta_input_name] = all_conditions;
							}else if (meta_input_type == 'pre-images') {
								
								var all_preuploads = new Array();
								jQuery(this).find('div.pre-upload-box table').each(function(i, preupload_box){
									var pre_upload_obj = {	
											link: jQuery(preupload_box).find('input[name="pre-upload-link"]').val(),
											id: jQuery(preupload_box).find('input[name="pre-upload-id"]').val(),
											title: jQuery(preupload_box).find('input[name="pre-upload-title"]').val(),
											price: jQuery(preupload_box).find('input[name="pre-upload-price"]').val(),
											url: jQuery(preupload_box).find('input[name="pre-upload-url"]').val(),
									};
									
									all_preuploads.push(pre_upload_obj);
								});
								
								inner_array['images'] = all_preuploads;
							}else if (meta_input_type == 'imageselect') {
								
								var all_preuploads = new Array();
								jQuery(this).find('div.imageselect-box table').each(function(i, imageselectbox){
									var pre_upload_obj = {	
											link: jQuery(imageselectbox).find('input[name="imageselect-link"]').val(),
											id: jQuery(imageselectbox).find('input[name="imageselect-id"]').val(),
											title: jQuery(imageselectbox).find('input[name="imageselect-title"]').val(),
											price: jQuery(imageselectbox).find('input[name="imageselect-price"]').val(),
											description: jQuery(imageselectbox).find('input[name="imageselect-description"]').val(),
									};
									
									all_preuploads.push(pre_upload_obj);
								});
								
								inner_array['images'] = all_preuploads;
							}else if (meta_input_type == 'pre-audios') {
								
								var all_preuploads = new Array();
								jQuery(this).find('div.pre-upload-box table').each(function(i, preupload_box){
									var pre_upload_obj = {	
											link: jQuery(preupload_box).find('input[name="pre-upload-link"]').val(),
											id: jQuery(preupload_box).find('input[name="pre-upload-id"]').val(),
											title: jQuery(preupload_box).find('input[name="pre-upload-title"]').val(),
											price: jQuery(preupload_box).find('input[name="pre-upload-price"]').val(),
											// url: jQuery(preupload_box).find('input[name="pre-upload-url"]').val(),
									};
									
									all_preuploads.push(pre_upload_obj);
								});
								
								inner_array['audio'] = all_preuploads;
							}else if (meta_input_type == 'paired') {
								
								var all_options = new Array();
								jQuery(this).find('.data-options').each(function(i, option_box){
									var option_set = {	option: jQuery(option_box).find('input[name="options[option]"]').val(),
														price: jQuery(option_box).find('input[name="options[price]"]').val(),
														weight: jQuery(option_box).find('input[name="options[weight]"]').val(),
														id: jQuery(option_box).find('input[name="options[id]"]').val(),};
									
									all_options.push(option_set);
								});
								
								inner_array['options'] = all_options;
							}else if (meta_input_type == 'paired-quantity') {
								
								var all_options = new Array();
								jQuery(this).find('.data-options').each(function(i, option_box){
									var option_set = {	option: jQuery(option_box).find('input[name="options[option]"]').val(),
														price: jQuery(option_box).find('input[name="options[price]"]').val(),
														min: jQuery(option_box).find('input[name="options[min]"]').val(),
														max: jQuery(option_box).find('input[name="options[max]"]').val(),
										
													};
									
									all_options.push(option_set);
								});
								
								inner_array['options'] = all_options;
							}else if (meta_input_type == 'paired-measure') {
								
								var all_options = new Array();
								jQuery(this).find('.data-options').each(function(i, option_box){
									var option_set = {	option: jQuery(option_box).find('input[name="options[option]"]').val(),
														price: jQuery(option_box).find('input[name="options[price]"]').val(),
														id: jQuery(option_box).find('input[name="options[id]"]').val(),
										
									};
									
									all_options.push(option_set);
								});
								
								inner_array['options'] = all_options;
							}else if (meta_input_type == 'paired-cropper') {
								
								var all_options = new Array();
								jQuery(this).find('.data-options').each(function(i, option_box){
									var option_set = {	option: jQuery(option_box).find('input[name="options[option]"]').val(),
														width: jQuery(option_box).find('input[name="options[width]"]').val(),
														height: jQuery(option_box).find('input[name="options[height]"]').val(),
														price: jQuery(option_box).find('input[name="options[price]"]').val(),
										
													};
									console.log(option_set);
									all_options.push(option_set);
								});
								
								inner_array['options'] = all_options;
							} else {
								inner_array[meta_input_name] = jQuery.trim(jQuery(this).find('input[name="'+ meta_input_name+ '"]').val())
								// inner_array.push(temp);
							}
							
						});

				product_meta_values.push( inner_array );

			});
	

	// console.log(product_meta_values); return false;
	// ok data is collected, so send it to server now Huh?

	var productmeta_id = jQuery('input[name="productmeta_id"]').val();

	if (productmeta_id != 0) {
		do_action = 'ppom_update_form_meta';
	} else {
		do_action = 'ppom_save_form_meta';
	}
	
	var server_data = {
		action 				: do_action,
		productmeta_id 		: jQuery.trim(jQuery('input[name="productmeta_id"]').val()),
		productmeta_name 	: jQuery.trim(jQuery('input[name="productmeta_name"]').val()),
		productmeta_validation 	: jQuery.trim(jQuery('input:checkbox[name="enable_ajax_validation"]:checked').val()),
		dynamic_price_hide 	: jQuery.trim(jQuery('select[name="dynamic_price_hide"] option:selected').val()),
		send_file_attachment 	: jQuery.trim(jQuery('input:checkbox[name="send_file_attachment"]:checked').val()),
		show_cart_thumb 	: jQuery.trim(jQuery('input:checkbox[name="show_cart_thumb"]:checked').val()),
		aviary_api_key 		: jQuery.trim(jQuery('input[name="aviary_api_key"]').val()),
		productmeta_style	: jQuery.trim(jQuery('textarea[name="productmeta_style"]').val()),
		productmeta_categories	: jQuery.trim(jQuery('textarea[name="productmeta_categories"]').val()),
		
		
		product_meta 		: product_meta_values
	};
		jQuery.post(ajaxurl, server_data, function(resp) {
			
			//console.log(resp); return false;
			jQuery("#nm-saving-form").html(resp.message);
			if(resp.status == 'success'){
				
				if(resp.productmeta_id != ''){
					window.location = nm_personalizedproduct_vars.plugin_admin_page + '&productmeta_id=' + resp.productmeta_id+'&do_meta=edit';
				}else{
					window.location.reload(true);	
				}
			}
			
		}, 'json');
}

function updateOptions(options) {

	var opt = jQuery.parseJSON(options);

	/*
	 * getting action from object
	 */

	/*
	 * extractElementData defined in nm-globals.js
	 */
	var data = extractElementData(opt);

	if (data.bug) {
		// jQuery("#reply_err").html('Red are required');
		alert('bug here');
	} else {

		/*
		 * [1]
		 */
		data.action = 'nm_personalizedproduct_save_settings';

		jQuery.post(ajaxurl, data, function(resp) {

			// jQuery("#reply_err").html(resp);
			alert(resp);
			// window.location.reload(true);

		});
	}
}

function are_sure(productmeta_id) {

	var a = confirm('Are you sure to delete this file?');
	if (a) {
		jQuery("#del-file-" + productmeta_id).attr("src", nm_personalizedproduct_vars.doing);

		jQuery.post(ajaxurl, {
			action : 'ppom_delete_meta',
			productmeta_id : productmeta_id
		}, function(resp) {
			// alert(data);
			alert(resp);
			window.location.reload(true);

		});

	}
}

//conditiona logic for select, radio and checkbox
function populate_conditional_elements() {

	// resetting
	jQuery('select[name="condition_elements"]').html('');

	jQuery("ul#meta-input-holder li").each(
			function(i, item) {

				var input_type = jQuery(item).attr('data-inputtype');
				var conditional_elements = jQuery(item).find(
						'input[name="title"]').val();
				var conditional_elements_value = jQuery(item).find(
						'input[name="data_name"]').val();
				/*console.log(input_type);
				console.log(conditional_elements_value);*/

				if (conditional_elements !== '' 
					&& (input_type === 'select' 
							|| input_type === 'radio' 
							|| input_type === 'checkbox' 
							|| input_type === 'image'
							|| input_type === 'imageselect')){
					
					jQuery('select[name="condition_elements"]')
					.append(
							'<option value="'
									+ conditional_elements_value + '">'
									+ conditional_elements
									+ '</option>');
					
				}
					
			});
	
	// setting the existing conditional elements
	jQuery("ul#meta-input-holder li").each(
			function(i, item) {
				
				jQuery(item).find('select[name="condition_elements"]').each(function(i, condition_element){
				
					var existing_value1 = jQuery(condition_element).attr("data-existingvalue");
					jQuery(condition_element).val(existing_value1);
					
					// populating element_values, also setting existing option
					load_conditional_values(jQuery(condition_element));
				});
				
					
			});


}

// load conditional values
function load_conditional_values(element) {

	// resetting
	jQuery(element).parent().find('select[name="condition_element_values"]')
			.html('');

	jQuery("ul#meta-input-holder li").each(
			function(i, item) {

				var conditional_elements_value = jQuery(item).find(
						'input[name="data_name"]').val();
				if (conditional_elements_value === jQuery(element).val()) {

					
					var opt = '';
					jQuery(item).find('input:text[name="options[option]"], input:text[name="pre-upload-title"], input:text[name="imageselect-title"]')
					.each(function(i, item){
						
						console.log(jQuery(item).val());
						opt = jQuery(item).val();
						var existing_value2 = jQuery(element).parent().find('select[name="condition_element_values"]').attr("data-existingvalue");
						var selected = (opt === existing_value2) ? 'selected = "selected"' : '';

						//console.log(jQuery(element).val() + ' ' +existing_value2);
						jQuery(element).parent().find(
								'select[name="condition_element_values"]')
								.append(
										'<option '+selected+' value="' + opt + '">' + opt
												+ '</option>');
					});
					

				}

			});
}

function validate_api_wooproduct(form){
	
	jQuery(form).find("#nm-sending-api").html(
			'<img src="' + nm_personalizedproduct_vars.doing + '">');
	
	var data = jQuery(form).serialize();
	data = data + '&action=nm_personalizedproduct_validate_api';
	
	jQuery.post(ajaxurl, data, function(resp) {

		//console.log(resp);
		jQuery(form).find("#nm-sending-api").html(resp.message);
		if( resp.status == 'success' ){
			window.location.reload(true);			
		}
	}, 'json');
	
	
	return false;
}

function extractElementData(elements) {

	var data = new Object;

	data.bug = false;
	jQuery.each(elements,
			function(i, item) {

			if(item.req == undefined || item.req == 0){
				item.req = false;
				
			}else{
				item.req = true;
				
			}
			
				switch (item.type) {
				
				case 'text':

					data[i] = jQuery("input[name^='" + i + "']").val();
					if(jQuery("input[name^='" + i + "']").val() == '' && item.req){
						jQuery("input[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
					break;

				case 'select':

					data[i] = jQuery("select[name^='" + i + "']").val();
					if(jQuery("select[name^='" + i + "']").val() == '' && item.req){
						jQuery("select[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
					break;

				case 'checkbox':
					
					var checkedVals = [];
					jQuery('input:checkbox[name^="' + i + '"]:checked').each(function() {
						checkedVals.push(jQuery(this).val());
					});
					
					data[i] = (checkedVals.length == 0) ? null : checkedVals;
					
					if (!jQuery("input:checkbox[name^='" + i + "']").is(':checked') && item.req){
						
						jQuery("input:checkbox[name^='" + i + "']").parent('label').css('color', 'red');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
						
					break;

				case 'radio':

					data[i] = jQuery(
							"input:radio[name^='" + i + "']:checked").val();
					if (!jQuery("input:radio[name^='" + i + "']").is(':checked') && item.req){
											
						jQuery("input:radio[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						alert(item.type+' is required');
					}
					break;
					
				case 'textarea':

					data[i] = jQuery("textarea[name^='" + i + "']").val();
					
					if(jQuery("textarea[name^='" + i + "']").val() == '' && item.req){
						jQuery("textarea[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
					break;
				}

			});

	return data;
}


/*
 * function checking the checkbox for current value
 * current value: json object
 * @Author: Najeeb
 * 13 Oct, 2012
 */

function setChecked(elementName, currentValue){
	
	var elementCB = jQuery('input:checkbox[name="' + elementName + '"]');
	
	var currentValues = jQuery.parseJSON(currentValue);
	
	
	//console.log(currentValues);
	
	jQuery.each(elementCB, function(i, item){
		
		//console.log(item.id);
		var current_cb_id = item.id;
		
		jQuery.each(currentValues, function(i, item){
			
			//console.log(item + jQuery("#"+current_cb_id).attr('value'));
			if(jQuery("#"+current_cb_id).attr('value') == item){
				
				jQuery("#"+current_cb_id).attr('checked', true);
			}else{
				if ( jQuery("#"+current_cb_id).attr('checked') == true)
					jQuery("#"+current_cb_id).attr('checked', false);
			}
			//jQuery('input:checkbox[value="' + item + '"]').attr("checked", "checked");
		});
	});
	
	
	
}

/*
 * function checking the RADIO for current value
 * current value: single
 * @Author: Najeeb
 * 3 July, 2012
 */

function setCheckedRadio(elementName, currentValue) {

	var elementRadio = jQuery('input:radio[name="' + elementName + '"]');

	//console.log(elementRadio);
	jQuery.each(elementRadio, function(i, item) {

		//console.log(item.id);
		var current_radio_id = item.id;
		
		if (jQuery("#" + current_radio_id).attr('value') == currentValue) {

			jQuery("#" + current_radio_id).attr('checked', true);
		} else {
			if (jQuery("#" + current_radio_id).attr('checked') == true)
				jQuery("#" + current_radio_id).attr('checked', false);
		}
						
	});

}