/**
 * plugin admin area javascript
 */
(function($, EventService ){$(function () {

	var vm = {
		'preiviewText' :'',
		'isGoogleMerchantsExport' : false,
		'isWoocommerceOrderExport' : function(){
			return $('#woo_commerce_order').length;
		},
		'isCSVExport': function(){
			return $('input[name=export_to]').val() === 'csv';
		},
		'isProductVariationsExport' : function() {
			return this.hasVariations;
		},
		'hasVariations' : false,
		'modeEnabled' : true
	};

	function processElementName($element, $elementName){
		if ( $element.find('input[name^=cc_type]').val().indexOf('image_') !== -1 )
		{
			$elementName = 'Image ' + $elementName;
		}
		if ( $element.find('input[name^=cc_type]').val().indexOf('attachment_') !== -1 )
		{
			$elementName = 'Attachment ' + $elementName;
		}
		return $elementName;
	}

	function selectSpreadsheet()
	{
		vm.isGoogleMerchantsExport = false;
		resetDraggable();
		angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('googleMerchantsDeselected');
		$('.wpallexport-custom-xml-template').slideUp();
		$('.wpallexport-simple-xml-template').slideDown();
		$('.wpallexport-csv-options').show();
		$('.wpallexport-xml-options').hide();

		$('.wpallexport-csv-advanced-options').css('display', 'block');
		$('.wpallexport-xml-advanced-options').css('display', 'none');

		$('input[name=export_to]').val('csv');

		if ($('#export_to_sheet').val() !== 'csv') {
			if (isWooCommerceOrder || vm.isProductVariationsExport()) {
				$('.csv_delimiter').hide();
				$('.export_to_csv').show();
			} else {
				$('.export_to_csv').hide();
			}
		} else {
			/** isProductVariationsExport */
			if (isWooCommerceOrder) {
				$('.export_to_csv').show();
			} else {
				$('.export_to_csv').show();
				$('.csv_delimiter').show();
			}
		}
	}

	function selectFeed()
	{
		$('.wpallexport-csv-options').hide();
		$('.wpallexport-xml-options').show();
		$('input[name=export_to]').val('xml');
		$('.xml_template_type').change();

		$('.wpallexport-csv-advanced-options').css('display', 'none');
		$('.wpallexport-xml-advanced-options').css('display', 'block');
	}

	var currentLine = -1;

	var dragHelper = function(e, ui) {

		if(!vm.isGoogleMerchantsExport) {
			return $(this).clone().css("pointer-events","none").appendTo("body").show();
		}
		if(!$(this).find('.custom_column').length) {
			return $(this).clone().css("pointer-events","none").appendTo("body").show();
		}
		var elementName = $(this).find('.custom_column').find('input[name^=cc_name]').val();
		elementName = helpers.sanitizeElementName(elementName);
		elementName = processElementName($(this), elementName);

		return $('<div>{' + elementName + '}</div>').css("pointer-events","none").appendTo("body").show();

	};

	var onDrag = function(e, ui)
	{
		var exportType = $('select.xml_template_type').val();

		if ( exportType == 'custom' && isDraggingOverTextEditor(e))
		{
			xml_editor.focus();

			if ( ui.helper.find('.custom_column').length )
			{
				var $elementName = ui.helper.find('.custom_column').find('input[name^=cc_name]').val();

				var $elementValue = $elementName;
				$elementName = helpers.sanitizeElementName($elementName);

				if ( ! ui.helper.find('.custom_column').hasClass('wp-all-export-custom-xml-drag-over') ) ui.helper.find('.custom_column').addClass('wp-all-export-custom-xml-drag-over');
				ui.helper.find('.custom_column').find('.wpallexport-xml-element').html("&lt;" + $elementName.replace(/ /g,'') + "&gt;<span>{" + $elementValue + "}</span>&lt;/" + $elementName.replace(/ /g,'') + "&gt;");
			}
			if ( ui.helper.find('.default_column').length )
			{
				var $elementName = ui.helper.find('.default_column').find('.wpallexport-element-label').html();
				if ( ! ui.helper.find('.default_column').hasClass('wp-all-export-custom-xml-drag-over') ) ui.helper.find('.default_column').addClass('wp-all-export-custom-xml-drag-over');
			}

			var line = xml_editor.lineAtHeight(ui.position.top, 'page');
			var ch   = xml_editor.coordsChar(ui.position, 'page');

			if( line == currentLine ) {
				return;
			}

			if (currentLine != -1) {
				removeLine(currentLine);
			}

			currentLine = line;

			addLine("\n", line);

			xml_editor_doc.setCursor({line:line, ch:ch.ch});
		}

	};

	function isDraggingOverTextEditor(event) {
		var e = event.originalEvent.originalEvent.target;
		return $.contains(xml_editor.display.scroller, e)
	}

	function addLine(str, line, ch) {
		if(typeof ch === 'undefined') {
			ch = 0;
		}
		xml_editor.replaceRange(str, {line: line, ch:0}, {line:line, ch:0});
	}

	function removeLine(line) {
		xml_editor.removeLine(line);
	}

	var initDraggable = function() {
		$( "#available_data li:not(.available_sub_section, .wpallexport_disabled)").draggable({
			appendTo: "body",
			containment: "document",
			helper: dragHelper,
			drag: onDrag,
			start: function() {
				$('.google-merchants-droppable').css('cursor', 'copy');
				$('#columns').css('cursor', 'copy');
				$('.CodeMirror-lines').css('cursor', 'copy');
			},
			stop: function() {
				$('#columns').css('cursor', 'initial');
				$('.CodeMirror-lines').css('cursor', 'text');
				$('.google-merchants-droppable').css('cursor', 'initial');
			}
		});
	};

	var resetDraggable = function() {

		var $draggableSelector = $("#available_data li:not(.available_sub_section, .wpallexport_disabled)");

		if($draggableSelector.data('ui-draggable')){
			$draggableSelector.draggable('destroy');
		}

		initDraggable();
	};

	initDraggable();

	$('.export_variations').change(function(){
		setTimeout(liveFiltering, 200);
		if ($(this).val() == 3){
			$('.warning-only-export-parent-products').show();
		}
		if ($(this).val() == 2){
			$('.warning-only-export-product-variations').show();
		}
	});

	var helpers = {
		'sanitizeElementName' : function($elementName) {
			if($elementName.indexOf('(per tax)') !== false ){
				$elementName = $elementName.replace('(per tax)','PerTax');
				$elementName = $elementName.replace('(per coupon)','PerCoupon');
				$elementName = $elementName.replace('(per surcharge)','PerSurcharge');
			}

			return $elementName;
		}
	};

	if ( ! $('body.wpallexport-plugin').length) return; // do not execute any code if we are not on plugin page

	// fix layout position
	setTimeout(function () {
		$('table.wpallexport-layout').length && $('table.wpallexport-layout td.left h2:first-child').css('margin-top',  $('.wrap').offset().top - $('table.wpallexport-layout').offset().top);
	}, 10);

	// help icons
	$('a.wpallexport-help').tipsy({
		gravity: function() {
			var ver = 'n';
			if ($(document).scrollTop() < $(this).offset().top - $('.tipsy').height() - 2) {
				ver = 's';
			}
			var hor = '';
			if ($(this).offset().left + $('.tipsy').width() < $(window).width() + $(document).scrollLeft()) {
				hor = 'w';
			} else if ($(this).offset().left - $('.tipsy').width() > $(document).scrollLeft()) {
				hor = 'e';
			}
	        return ver + hor;
	    },
		live: true,
		html: true,
		opacity: 1
	}).live('click', function () {
		return false;
	}).each(function () { // fix tipsy title for IE
		$(this).attr('original-title', $(this).attr('title'));
		$(this).removeAttr('title');
	});

	if ($('#wp_all_export_code').length){
		var editor = CodeMirror.fromTextArea(document.getElementById("wp_all_export_code"), {
	        lineNumbers: true,
	        matchBrackets: true,
	        mode: "application/x-httpd-php",
	        indentUnit: 4,
	        indentWithTabs: true,
	        lineWrapping: true
	    });
	    editor.setCursor(1);
	    $('.CodeMirror').resizable({
		  resize: function() {
		    editor.setSize("100%", $(this).height());
		  }
		});
	}

	if ($('#wp_all_export_custom_xml_template').length)
	{
		var xml_editor = CodeMirror.fromTextArea(document.getElementById("wp_all_export_custom_xml_template"), {
	        lineNumbers: true,
	        matchBrackets: true,
	        mode: "xml",
	        indentUnit: 4,
	        indentWithTabs: true,
	        lineWrapping: true,
	        autoRefresh: true
	        // dragDrop: true,
	        // handleMouseEvents: true
	    });

	    xml_editor.setCursor(1);
	    $('.CodeMirror').resizable({
		  resize: function() {
		    xml_editor.setSize("100%", $(this).height());
		  }
		});

		var xml_editor_doc = xml_editor.getDoc();

	}

	if ($('#wp_all_export_main_code').length){
		var main_editor = CodeMirror.fromTextArea(document.getElementById("wp_all_export_main_code"), {
	        lineNumbers: true,
	        matchBrackets: true,
	        mode: "application/x-httpd-php",
	        indentUnit: 4,
	        indentWithTabs: true,
	        lineWrapping: true
	    });
	    main_editor.setCursor(1);
	    $('.CodeMirror').resizable({
		  resize: function() {
		    main_editor.setSize("100%", $(this).height());
		  }
		});
	}

	// swither show/hide logic
	$('input.switcher').live('change', function (e) {

		if ($(this).is(':radio:checked')) {
			$(this).parents('form').find('input.switcher:radio[name="' + $(this).attr('name') + '"]').not(this).change();
		}
		var $switcherID = $(this).attr('id');

		var $targets = $('.switcher-target-' + $switcherID);

		var is_show = $(this).is(':checked'); if ($(this).is('.switcher-reversed')) is_show = ! is_show;
		if (is_show) {
			$targets.fadeIn('fast', function(){
				if ($switcherID == 'coperate_php'){
					editor.setCursor(1);
				}
			});
		} else {
			$targets.hide().find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
		}
	}).change();

	// swither show/hide logic
	$('input.switcher-horizontal').live('change', function (e) {

		if ($(this).is(':checked')) {
			$(this).parents('form').find('input.switcher-horizontal[name="' + $(this).attr('name') + '"]').not(this).change();
		}
		var $targets = $('.switcher-target-' + $(this).attr('id'));

		var is_show = $(this).is(':checked'); if ($(this).is('.switcher-reversed')) is_show = ! is_show;

		if (is_show) {
			$targets.animate({width:'205px'}, 350);
		} else {
			$targets.animate({width:'0px'}, 1000).find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
		}
	}).change();

	// autoselect input content on click
	$('input.selectable').live('click', function () {
		$(this).select();
	});

	$('.pmxe_choosen').each(function(){
		$(this).find(".choosen_input").select2({tags: $(this).find('.choosen_values').html().split(',')});
	});

	// choose file form: option selection dynamic
	// options form: highlight options of selected post type
	$('form.choose-post-type input[name="type"]').click(function() {
		var $container = $(this).parents('.file-type-container');
		$('.file-type-container').not($container).removeClass('selected').find('.file-type-options').hide();
		$container.addClass('selected').find('.file-type-options').show();
	}).filter(':checked').click();

	$('.wpallexport-collapsed').each(function(){

		if ( ! $(this).hasClass('closed')) $(this).find('.wpallexport-collapsed-content:first').slideDown();

	});

	$('.wpallexport-collapsed').find('.wpallexport-collapsed-header:not(.disable-jquery)').live('click', function(){
			var $parent = $(this).parents('.wpallexport-collapsed:first');
		if ($parent.hasClass('closed')){
			$parent.find('hr').show();
			$parent.removeClass('closed');
			$parent.find('.wpallexport-collapsed-content:first').slideDown(400, function(){
				if ($('#wp_all_export_main_code').length) {
					main_editor.setCursor(1);
				}
				if ($('#wp_all_export_custom_xml_template').length){
					xml_editor.setCursor(1);
				}
			});
		}
		else{
			$parent.addClass('closed');
			$parent.find('.wpallexport-collapsed-content:first').slideUp();
			$parent.find('hr').hide();
		}
	});

	// [ Helper functions ]

	var get_valid_ajaxurl = function()
	{
		var $URL = ajaxurl;
	    if (typeof export_id != "undefined")
	    {
	    	if ($URL.indexOf("?") == -1)
	    	{
	    		$URL += '?id=' + export_id;
	    	}
	    	else
	    	{
	    		$URL += '&id=' + export_id;
	    	}
	    }
	    return $URL;
	}

	// generate warning on a fly when required fields deleting from the export template
	var trigger_warnings = function()
	{

		var missing_fields = ['id'];

		if ( $('#is_product_export').length ) missing_fields = missing_fields.concat(['_sku', 'product_type', 'parent']);
		if ( $('#is_wp_query').length ) missing_fields.push('post_type');

		$('#columns').find('li:not(.placeholder)').each(function(i, e){
			$(this).find('div.custom_column:first').attr('rel', i + 1);
			if ($(this).find('input[name^=cc_type]').val() == 'id'){
				var index = missing_fields.indexOf('id');
				if (index > -1) {
				    missing_fields.splice(index, 1);
				}
			}
			if ($(this).find('input[name^=cc_label]').val() == '_sku'){
				var index = missing_fields.indexOf('_sku');
				if (index > -1) {
				    missing_fields.splice(index, 1);
				}

			}
			if ($(this).find('input[name^=cc_label]').val() == 'product_type'){
				var index = missing_fields.indexOf('product_type');
				if (index > -1) {
				    missing_fields.splice(index, 1);
				}
			}
			if ($(this).find('input[name^=cc_label]').val() == 'parent'){
				var index = missing_fields.indexOf('parent');
				if (index > -1) {
					missing_fields.splice(index, 1);
				}
			}
			if ($(this).find('input[name^=cc_label]').val() == 'post_type'){
				var index = missing_fields.indexOf('post_type');
				if (index > -1) {
				    missing_fields.splice(index, 1);
				}
			}
		});

		if ( missing_fields.length )
		{
			var fields = '';
			switch (missing_fields.length)
			{
				case 1:
					fields = missing_fields.shift();
					break;
				case 2:
					fields = missing_fields.join(" and ");
					break;
				default:
					var latest_field = missing_fields.pop();
					fields = missing_fields.join(", ") + ", and " + latest_field;
					break;
			}

			var warning_template = $('#warning_template').length ? $('#warning_template').val().replace("%s", fields) : '';

            var is_dismiss_warnings = parseInt($('#dismiss_warnings').val());

            if ( ! is_dismiss_warnings ) {
                $('.wp-all-export-warning').find('p').html(warning_template);
                $('.wp-all-export-warning').show();
            }
		}
		else
		{
			$('.wp-all-export-warning').hide();
		}
	}

	// Get a valid filtering rules for selected field type
	var init_filtering_fields = function(){

		var wp_all_export_rules_config = {
	      '#wp_all_export_xml_element' : {width:"98%"},
	      '#wp_all_export_rule' : {width:"98%"},
	    }

	    for (var selector in wp_all_export_rules_config) {

	    	$(selector).chosen(wp_all_export_rules_config[selector]);

	    	if (selector == '#wp_all_export_xml_element'){

		    	$(selector).on('change', function(evt, params) {

		    		$('#wp_all_export_available_rules').html('<div class="wp_all_export_preloader" style="display:block;"></div>');

		    		var date_fields = ['post_date', 'post_modified', 'comment_date', 'user_registered', 'cf__completed_date', 'product_date'];

	    			if ( date_fields.indexOf(params.selected) > -1 )
		    		{
		    			$('#date_field_notice').show();
		    		}
		    		else
		    		{
		    			$('#date_field_notice').hide();
		    		}

		    		var request = {
						action: 'wpae_available_rules',
						data: {'selected' : params.selected},
						security: wp_all_export_security
				    };
				    $.ajax({
						type: 'POST',
						url: ajaxurl,
						data: request,
						success: function(response) {
							$('#wp_all_export_available_rules').html(response.html);
							$('#wp_all_export_rule').chosen({width:"98%"});
							$('#wp_all_export_rule').on('change', function(evt, params) {
								if (params.selected == 'is_empty' || params.selected == 'is_not_empty')
									$('#wp_all_export_value').hide();
								else
									$('#wp_all_export_value').show();
							});
						},
						dataType: "json"
					});
		    	});
		    }
	    }

	    $('.wp_all_export_filtering_rules').pmxe_nestedSortable({
	        handle: 'div',
	        items: 'li.dragging',
	        toleranceElement: '> div',
	        update: function () {
	        	$('.wp_all_export_filtering_rules').find('.condition').removeClass('last_condition').show();
	        	$('.wp_all_export_filtering_rules').find('.condition:last').addClass('last_condition');
	        	liveFiltering();
		    }
	    });

	}

	var is_first_load = true;

	var filtering = function(postType){

		var is_preload = $('.wpallexport-preload-post-data').val();
		var filter_rules_hierarhy = parseInt(is_preload) ? $('input[name=filter_rules_hierarhy]').val() : '';

		$('.wpallexport-preload-post-data').val(0);

		var request = {
			action: 'wpae_filtering',
			data: {'cpt' : postType, 'export_type' : 'specific', 'filter_rules_hierarhy' : filter_rules_hierarhy, 'product_matching_mode' : 'strict', 'taxonomy_to_export' : $('input[name=taxonomy_to_export]').val()},
			security: wp_all_export_security
	    };

	    if (is_first_load == false || postType != '') $('.wp_all_export_preloader').show();

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: request,
			success: function(response) {

				$('.wp_all_export_preloader').hide();

				var export_type = $('input[name=export_type]').val();

				if (export_type == 'advanced')
				{
					$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();
					$('.wpallexport-choose-file').find('.wp_all_export_continue_step_two').html(response.btns);
					$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').show();
				}
				else
				{
					if (postType != '')
					{

						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').html(response.html);
						$('.wpallexport-choose-file').find('.wp_all_export_continue_step_two').html(response.btns);

						init_filtering_fields();
						liveFiltering(is_first_load);
					}
					else
					{
						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();
						$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();
					}
				}

				is_first_load = false;

			},
			error: function( jqXHR, textStatus ) {

				$('.wp_all_export_preloader').hide();

			},
			dataType: "json"
		});

	};

	var liveFiltering = function(first_load, after_filtering){

		// serialize filters
		$('.hierarhy-output').each(function(){
			var sortable = $('.wp_all_export_filtering_rules.ui-sortable');
			if (sortable.length){
				$(this).val(window.JSON.stringify(sortable.pmxe_nestedSortable('toArray', {startDepthCount: 0})));
			}
		});

		var postType = $('input[name=cpt]').length ? $('input[name=cpt]').val() : $('input[name=selected_post_type]').val();

		var $export_only_new_stuff = $('input[name=export_only_new_stuff]').val();
		if ($('#export_only_new_stuff').length){
			$export_only_new_stuff = $('#export_only_new_stuff').is(':checked') ? 1 : 0;
		}

		var $export_only_modified_stuff = $('input[name=export_only_modified_stuff]').val();
		if ($('#export_only_modified_stuff').length){
			$export_only_modified_stuff = $('#export_only_modified_stuff').is(':checked') ? 1 : 0;
		}

		// prepare data for ajax request to get post count after filtering
		var request = {
			action: 'wpae_filtering_count',
			data: {
				'cpt' : postType,
				'filter_rules_hierarhy' : $('input[name=filter_rules_hierarhy]').val(),
				'product_matching_mode' : $('select[name=product_matching_mode]').length ? $('select[name=product_matching_mode]').val() : '',
				'is_confirm_screen' : $('.wpallexport-step-4').length,
				'is_template_screen' : $('.wpallexport-step-3').length,
				'export_only_new_stuff' : $export_only_new_stuff,
				'export_only_modified_stuff' : $export_only_modified_stuff,
				'export_type' : $('input[name=export_type]').val(),
				'taxonomy_to_export' : $('input[name=taxonomy_to_export]').val(),
				'wpml_lang' : $('input[name=wpml_lang]').val(),
				'export_variations' : $('#export_variations').val()
			},
			security: wp_all_export_security
	    };

	    $('.wp_all_export_preloader').show();
	    $('.wp_all_export_filter_preloader').show();

		$.ajax({
			type: 'POST',
			url: get_valid_ajaxurl(),
			data: request,
			success: function(response) {

				vm.hasVariations = response.hasVariations;
				if(vm.hasVariations) {

					if($('#export_to_sheet').val() == 'xls' || $('#export_to_sheet').val() == 'xlsx') {
						$('.csv_delimiter').hide();
						$('.export_to_csv').slideDown();
					}

					$('.product_variations').show();

				}

				$('.wp_all_export_filter_preloader').hide();

				$('#filtering_result').html(response.html);

				$('.wpallexport-choose-file').find('.wpallexport-filtering-wrapper').slideDown(400, function(){
					if (typeof first_load != 'undefined')
					{
						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideDown();
						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').addClass('closed');
						if (response.found_records) $('.wpallexport-choose-file').find('.wpallexport-submit-buttons').show();
					}
				});

				$('.wp_all_export_preloader').hide();

				if (typeof after_filtering != 'undefined')
				{
					after_filtering(response);
				}

		    	if ( $('.wpallexport-step-4').length && typeof wp_all_export_L10n != 'undefined'){

	    			if (response.found_records)
	    			{
	    				$('.wp_all_export_confirm_and_run').show();
	    				$('.confirm_and_run_bottom').val(wp_all_export_L10n.confirm_and_run);
	    				$('#filtering_result').removeClass('nothing_to_export');
	    			}
	    			else
	    			{
	    				$('.wp_all_export_confirm_and_run').hide();
	    				$('.confirm_and_run_bottom').val(wp_all_export_L10n.save_configuration);
	    				$('#filtering_result').addClass('nothing_to_export');
	    			}
		    	}

		    	if ( $('.wpallexport-step-3').length ){

	    			$('.founded_records').html(response.html);

	    			if (response.found_records)
		    		{
		    			$('.founded_records').removeClass('nothing_to_export');
		    		}
		    		else
		    		{
		    			$('.founded_records').addClass('nothing_to_export');
		    		}
		    	}

		    	if ( $('.wpallexport-step-1').length)
		    	{
		    		if (response.found_records)
		    		{
		    			$('.founded_records').removeClass('nothing_to_export');
		    			$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').show();
		    		}
		    		else
		    		{
		    			$('.founded_records').addClass('nothing_to_export');
		    			$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();
		    		}
		    	}
			},
			error: function( jqXHR, textStatus ) {

				$('.wp_all_export_filter_preloader').hide();
				$('.wp_all_export_preloader').hide();

			},
			dataType: "json"
		}).fail(function(xhr, textStatus, error) {
			$('div.error.inline').remove();
			$('.wpallexport-header').next('.clear').after("<div class='error inline'><p>" + textStatus + " " + error + "</p></div>");
		});

	}
	// [ \Helper functions ]


	// [ Step 1 ( chose & filter export data ) ]
	$('.wpallexport-step-1').each(function(){

		var $wrap = $('.wrap');

		var formHeight = $wrap.height();

		$('.wpallexport-import-from').click(function(){

			var showImportType = false;

			var postType = $('input[name=cpt]').val();

			switch ($(this).attr('rel')){
				case 'specific_type':
					
					$('.wpallexport-user-export-notice').hide();
					$('.wpallexport-shop_customer-export-notice').hide();
		    		$('.wpallexport-comments-export-notice').hide();

					if (postType != '')
					{
						if (postType == 'users'){							
							$('.wpallexport-user-export-notice').show();
							showImportType = false; 						
							$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideDown();
						}						
						else if (postType == 'comments')
						{
							$('.wpallexport-comments-export-notice').show();
							showImportType = false; 						
							$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideDown();
						}
						else if (postType == 'shop_customer')
						{
							$('.wpallexport-customer-export-notice').show();
							showImportType = false; 						
							$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideDown();
						}
						else if (postType == 'taxonomies'){
							showImportType = false; 						
							$('.taxonomy_to_export_wrapper').slideDown();
							$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideDown();
						}
						else
						{							
							showImportType = true; 						
							$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideDown();
						}						
						
						$('.wpallexport-filtering-wrapper').show();				
					}											
					break;
				case 'advanced_type':		

					$('.wpallexport-user-export-notice').hide();
		    		$('.wpallexport-comments-export-notice').hide();
		    		$('.wpallexport-shop_customer-export-notice').hide();

					if ($('input[name=wp_query_selector]').val() == 'wp_user_query')
					{
						$('.wpallexport-user-export-notice').show();
						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();					
						showImportType = false; 
					}
					else if ($('input[name=wp_query_selector]').val() == 'wp_comment_query')
					{
						$('.wpallexport-comments-export-notice').show();
						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();					
						showImportType = false; 
					}
					else
					{						
						$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();					
						showImportType = true; 
					}										
					$('.wpallexport-filtering-wrapper').hide();
					filtering();
					break;
			}

			$('.wpallexport-import-from').removeClass('selected').addClass('bind');
			$(this).addClass('selected').removeClass('bind');
			$('.wpallexport-choose-file').find('.wpallexport-upload-type-container').hide();
			$('.wpallexport-choose-file').find('.wpallexport-upload-type-container[rel=' + $(this).attr('rel') + ']').show();
			$('.wpallexport-choose-file').find('input[name=export_type]').val( $(this).attr('rel').replace('_type', '') );

			if ( ! showImportType)
			{
				$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();
			}
			else{
				$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').show();
			}

		});

		$('.wpallexport-import-from.selected').click();

		$('#file_selector').ddslick({
			width: 600,
			onSelected: function(selectedData){

				$('.wpallexport-user-export-notice').hide();
		    	$('.wpallexport-comments-export-notice').hide();
		    	$('.wpallexport-shop_customer-export-notice').hide();
		    	$('.wpallexport-taxonomies-export-notice').hide();

		    	if (selectedData.selectedData.value != ""){
		    		
		    		$('#file_selector').find('.dd-selected').css({'color':'#555'});

		    		var i = 0;
					var postType = selectedData.selectedData.value;
					$('#file_selector').find('.dd-option-value').each(function(){
						if (postType == $(this).val()) return false;
						i++;
					});

					$('.wpallexport-choose-file').find('input[name=cpt]').val(postType);

					if (postType == 'taxonomies'){
						$('.taxonomy_to_export_wrapper').slideDown();
						if ($('input[name=taxonomy_to_export]').val() != ''){
							filtering(postType);
						}
						else{
							$('.wpallexport-choose-file').find('.wpallexport-filtering-wrapper').slideUp();
							$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();							
						}
						$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();
					}
					else{
						$('.taxonomy_to_export_wrapper').slideUp();

						if (postType == 'users')
						{
							$('.wpallexport-user-export-notice').show();
							$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();	
						}
						else if (postType == 'comments')
						{
							$('.wpallexport-comments-export-notice').show();
							$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();	
						}
						else if (postType == 'shop_customer')
						{
							$('.wpallexport-shop_customer-export-notice').show();
							$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();	
						}					
						else
						{						
							$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').show();	
						}					

						filtering(postType);
					}																						
		    	}
		    	else
		    	{
					$('.taxonomy_to_export_wrapper').slideUp();
		    		$('.wpallexport-choose-file').find('input[name=cpt]').val('');
		    		$('#file_selector').find('.dd-selected').css({'color':'#cfceca'});
		    		$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();
		    		$('.wpallexport-choose-file').find('.wpallexport-filtering-wrapper').slideUp();

					switch ($('.wpallexport-import-from.selected').attr('rel')){
						case 'specific_type':
							filtering($('input[name=cpt]').val());
							break;
						case 'advanced_type':

							break;
					}
		    	}
		    }
		});

		$('a.auto-generate-template').live('click', function(){
			$('input[name^=auto_generate]').val('1');

			$('.hierarhy-output').each(function(){
				var sortable = $('.wp_all_export_filtering_rules.ui-sortable');
				if (sortable.length){
					$(this).val(window.JSON.stringify(sortable.pmxe_nestedSortable('toArray', {startDepthCount: 0})));
				}
			});

			$(this).parents('form:first').submit();
		});

		$('form.wpallexport-choose-file').find('input[type=submit]').click(function(e){
			e.preventDefault();

			$('.hierarhy-output').each(function(){
				var sortable = $('.wp_all_export_filtering_rules.ui-sortable');
				if (sortable.length){
					$(this).val(window.JSON.stringify(sortable.pmxe_nestedSortable('toArray', {startDepthCount: 0})));
				}
			});

			$(this).parents('form:first').submit();
		});

		$('#wp_query_selector').ddslick({
			width: 600,	
			onSelected: function(selectedData){											
				
				$('.wpallexport-user-export-notice').hide();
		    	$('.wpallexport-comments-export-notice').hide();
		    	$('.wpallexport-shop_customer-export-notice').hide();
		    	$('.wpallexport-taxonomies-export-notice').hide();
		    	
		    	$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();

		    	if (selectedData.selectedData.value != ""){

		    		$('#wp_query_selector').find('.dd-selected').css({'color':'#555'});
		    		var queryType = selectedData.selectedData.value;
		    		if (queryType == 'wp_query'){
		    			$('textarea[name=wp_query]').attr("placeholder", "'post_type' => 'post', 'post_status' => array( 'pending', 'draft', 'future' )");
		    			$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').show();
		    		}
		    		if(queryType == 'wp_user_query')
		    		{
		    			$('.wpallexport-user-export-notice').show();		    			
		    			$('textarea[name=wp_query]').attr("placeholder", "'role' => 'Administrator'");
		    			$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();	
		    		}		    		
		    		else if(queryType == 'wp_comment_query')
		    		{		    			
		    			$('.wpallexport-comments-export-notice').show();
		    			$('textarea[name=wp_query]').attr("placeholder", "'meta_key' => 'featured', 'meta_value' => '1'");		    			
		    			$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();	
		    		}
					$('input[name=wp_query_selector]').val(queryType);
		    	}
		    	else{

		    		$('#wp_query_selector').find('.dd-selected').css({'color':'#cfceca'});
		    		$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();

		    	}
		    }
		});
		// Taxonomies Export
		$('#taxonomy_to_export').ddslick({
			width: 600,
			onSelected: function(selectedData){

				if (selectedData.selectedData.value != ""){

					$('#taxonomy_to_export').find('.dd-selected').css({'color':'#555'});
					$('input[name=taxonomy_to_export]').val(selectedData.selectedData.value);
					filtering($('input[name=cpt]').val());					
					$('.wpallexport-taxonomies-export-notice').show();
					$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();	
				}
				else{
					$('#taxonomy_to_export').find('.dd-selected').css({'color':'#cfceca'});
					$('.wpallexport-choose-file').find('.wpallexport-filtering-wrapper').slideUp();
					$('.wpallexport-choose-file').find('.wpallexport-upload-resource-step-two').slideUp();
					$('.wpallexport-choose-file').find('.wpallexport-submit-buttons').hide();
				}
			}
		});
	});
	// [ \Step 1 ( chose & filter export data ) ]


	// [ Step 2 ( export template ) ]
	$('.wpallexport-export-template').each(function(){

		trigger_warnings();

		var $sortable = $( "#columns" );

		var outsideContainer = 0;

		// this one control if the draggable is outside the droppable area
		$('#columns_to_export').droppable({
		    accept      : '.ui-sortable-helper'
		});

		$( "#columns_to_export" ).on( "dropout", function( event, ui ) {
			outsideContainer = 1;
			ui.draggable.find('.custom_column').css('background', 'white');
		} );

		$( "#columns_to_export" ).on( "dropover", function( event, ui ) {
			outsideContainer = 0;
			ui.draggable.find('.custom_column').css('background', 'white');
		} );

		// this one control if the draggable is dropped
		$('body, form.wpallexport-template').droppable({
		    accept      : '.ui-sortable-helper',
		    drop        : function(event, ui){
		        if(outsideContainer == 1){
		            ui.draggable.remove();
		            trigger_warnings();

		            if ( $('#columns').find('li:not(.placeholder)').length === 1)
		            {
						$('#columns').find( ".placeholder" ).show();
					}
		        }else{
		            ui.draggable.find('.custom_column').css('background', 'none');
		        }
		    }
		});

		$( "#columns_to_export ol" ).droppable({
			activeClass: "pmxe-state-default",
			hoverClass: "pmxe-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drop: function( event, ui ) {
				$( this ).find( ".placeholder" ).hide();

				if (ui.draggable.find('input[name^=rules]').length){
					$('li.' + ui.draggable.find('input[name^=rules]').val()).each(function(){
						var $value = $(this).find('input[name^=cc_value]').val();
						var $is_media_field = false;
						if ( $(this).find('input[name^=cc_type]').val().indexOf('image_') !== -1 || $(this).find('input[name^=cc_type]').val().indexOf('attachment_') !== -1 )
						{
							$value = $(this).find('input[name^=cc_type]').val();
							$is_media_field = true;
						}
						var $add_field = true;
						$('#columns').find('li').each(function(){
							if ( $is_media_field )
							{
								if ($(this).find('input[name^=cc_type]').val() == $value){
									$add_field = false;
								}
							}
							else
							{
								if ($(this).find('input[name^=cc_value]').val() == $value){
									$add_field = false;
								}
							}
						});
						if ($add_field)
						{
							$( "<li></li>" ).html( $(this).html() ).appendTo( $( "#columns_to_export ol" ) );
							var $just_added = $('#columns').find('li:last').find('div:first');
							$just_added.attr('rel', $('#columns').find('li:not(.placeholder)').length);
							if ( $just_added.find('input[name^=cc_type]').val().indexOf('image_') !== -1 )
							{
								$just_added.find('.wpallexport-xml-element').html('Image ' + $just_added.find('input[name^=cc_name]').val());
								$just_added.find('input[name^=cc_name]').val('Image ' + $just_added.find('input[name^=cc_name]').val());
							}
							if ( $just_added.find('input[name^=cc_type]').val().indexOf('attachment_') !== -1 )
							{
								$just_added.find('.wpallexport-xml-element').html('Attachment ' + $just_added.find('input[name^=cc_name]').val());
								$just_added.find('input[name^=cc_name]').val('Attachment ' + $just_added.find('input[name^=cc_name]').val());
							}
						}
					});
				}
				else{
					$( "<li></li>" ).html( ui.draggable.html() ).appendTo( this );
					var $just_added = $('#columns').find('li:last').find('div:first');
					$just_added.attr('rel', $('#columns').find('li:not(.placeholder)').length);
					if ( $just_added.find('input[name^=cc_type]').val().indexOf('image_') !== -1 )
					{
						$just_added.find('.wpallexport-xml-element').html('Image ' + $just_added.find('input[name^=cc_name]').val());
						$just_added.find('input[name^=cc_name]').val('Image ' + $just_added.find('input[name^=cc_name]').val());
					}
					if ( $just_added.find('input[name^=cc_type]').val().indexOf('attachment_') !== -1 )
					{
						$just_added.find('.wpallexport-xml-element').html('Attachment ' + $just_added.find('input[name^=cc_name]').val());
						$just_added.find('input[name^=cc_name]').val('Attachment ' + $just_added.find('input[name^=cc_name]').val());
					}
				}

				trigger_warnings();

			}
		}).sortable({
			items: "li:not(.placeholder)",
			sort: function() {
				// gets added unintentionally by droppable interacting with sortable
				// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
				$( this ).removeClass( "ui-state-default" );
			}
		});

		$( ".CodeMirror-code" ).droppable({
			activeClass: "pmxe-template-state-default",
			hoverClass: "pmxe-template-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drag: function( event, ui ){
			},
			drop: function( event, ui ) {

				function getCodeToPlace($elementName) {
					var $elementValue = $elementName;
					$elementName = helpers.sanitizeElementName($elementName);
					return "<" + $elementName.replace(/ /g,'') + ">{" + $elementValue+ "}</" + $elementName.replace(/ /g,'') + ">\n"
				}


				function replaceLineWithElements(content){
					removeLine(currentLine);

					addLine( content, currentLine, currentLine);
					currentLine = -1;

					var totalLines = xml_editor.lineCount();
					xml_editor.autoIndentRange({line:0, ch:0}, {line:totalLines,ch:100});
				}

				if (ui.draggable.find('input[name^=rules]').length){
					var content = "";
					$('li.' + ui.draggable.find('input[name^=rules]').val()).each(function(){
						var $elementName = $(this).find('input[name^=cc_name]').val();
						$elementName = processElementName($(this),$elementName);
						content = content + getCodeToPlace($elementName);
					});

					replaceLineWithElements(content);
				}
				else{
					var $elementName = ui.draggable.find('.custom_column').find('input[name^=cc_name]').val();
					var $element = ui.draggable.find('.custom_column');
					$elementName = processElementName($element, $elementName);

					replaceLineWithElements(getCodeToPlace($elementName));
				}
			}
		});

		var $this = $(this);
		var $addAnother = $this.find('input.add_column');
		var $addAnotherForm = $('fieldset.wp-all-export-edit-column');
		var $template = $(this).find('.custom_column.template');

		if (typeof wpPointerL10n != "undefined") wpPointerL10n.dismiss = 'Close';

		// Add Another btn click
		$addAnother.click(function(){

			$addAnotherForm.find('form')[0].reset();
			$addAnotherForm.find('input[type=checkbox]').removeAttr('checked');

			$addAnotherForm.removeAttr('rel');
			$addAnotherForm.removeClass('dc').addClass('cc');
			$addAnotherForm.find('.cc_field').hide();

			$addAnotherForm.find('.wpallexport-edit-row-title').hide();
			$addAnotherForm.find('.wpallexport-add-row-title').show();
			$addAnotherForm.find('div[class^=switcher-target]').hide();
			$addAnotherForm.find('#coperate_php').removeAttr('checked');
			$addAnotherForm.find('input.column_name').parents('div.input:first').show();

			$('.custom_column').removeClass('active');

			$addAnotherForm.find('select[name=column_value_type]').find('option').each(function(){
				if ($(this).val() == 'id')
					$(this).attr({'selected':'selected'}).click();
				else
					$(this).removeAttr('selected');
			});

			$('.wp-all-export-chosen-select').trigger('chosen:updated');
			$('.wp_all_export_saving_status').html('');

			$('.wpallexport-overlay').show();
			$addAnotherForm.find('input.switcher').change();
			$addAnotherForm.show();

		});

		// Delete custom column action
		$addAnotherForm.find('.delete_action').click(function(){

			$('.custom_column').removeClass('active');

			$('.custom_column[rel='+ $addAnotherForm.attr('rel') +']').parents('li:first').fadeOut().remove();

			if ( ! $('#columns').find('li:visible').length ){
				$('#columns').find( ".placeholder" ).show();
			}

			trigger_warnings();

			$addAnotherForm.fadeOut();
			$('.wpallexport-overlay').hide();
		});

		// Add/Edit custom column action
		$addAnotherForm.find('.save_action').click(function(){

			var $save = true;

			// element name in exported file
			var $elementName = $addAnotherForm.find('input.column_name');

			// element name validation
			if ($elementName.val() == '')
			{
				$save = false;
				$elementName.addClass('error');
				return false;
			}

			// get PHP function name
			var $phpFunction = $addAnotherForm.find('.php_code:visible');

			// validation passed, prepare field data
			var $elementIndex = $addAnotherForm.attr('rel');
			// element type
			var $elementType = $addAnotherForm.find('select[name=column_value_type]');
			// element label, options and other stuff
			var $elementDetails = $elementType.find('option:selected');
			// element labeel
			var $elementLabel = $elementDetails.attr('label');

			var $clone = ( $elementIndex ) ? $('#columns').find('.custom_column[rel='+ $elementIndex +']') : $template.clone(true);

			// if new field adding
			if ( ! parseInt( $elementIndex ) )
			{
				// new column added, increase element Index
				$clone.attr('rel', $('#columns').find('.custom_column').length + 1);
			}

			// add element label
			$clone.find('label.wpallexport-xml-element').html( $elementName.val() );
			// wrap field value into PHP function
			$clone.find('input[name^=cc_php]').val( $addAnotherForm.find('#coperate_php').is(':checked') ? '1' : '0' );
			// save PHP function name
			$clone.find('input[name^=cc_code]').val( $phpFunction.val() );
			// save SQL code
			$clone.find('input[name^=cc_sql]').val( $addAnotherForm.find('textarea.column_value').val() );
 			// save element name
			$clone.find('input[name^=cc_name]').val( $elementName.val() );
			// save element type
			$clone.find('input[name^=cc_type]').val( $elementType.val() );
			// save element value
			$clone.find('input[name^=cc_value]').val( $elementDetails.attr('label') );
			// save element label
			$clone.find('input[name^=cc_label]').val( $elementDetails.attr('label') );
			// save element options
			$clone.find('input[name^=cc_options]').val( $elementDetails.attr('options') );

			// if new field adding append element to the export template
			if ( ! parseInt( $elementIndex ) )
			{
				$( "#columns" ).find( ".placeholder" ).hide();
				$sortable.append('<li></li>');
				$sortable.find('li:last').append($clone.removeClass('template').fadeIn());
			}

			var $fieldType = $elementType.val();

			if ($elementLabel == '_sale_price_dates_from' || $elementLabel == '_sale_price_dates_to') $fieldType = 'date';

			// set up additional element settings by element type
			switch ( $fieldType )
			{
				// save post date field format
				case 'date':
				case 'comment_date':
				case 'user_registered':
				case 'post_modified':
					var $dateType = $addAnotherForm.find('select.date_field_export_data').val();
					if ($dateType == 'unix')
						$clone.find('input[name^=cc_settings]').val('unix');
					else
						$clone.find('input[name^=cc_settings]').val($('.pmxe_date_format').val());
					break;
				// set up additional settings for repeater field
				case 'acf':
					// determine is repeater field selected in dropdown
					if ( $clone.find('input[name^=cc_options]').val().indexOf('s:4:"type";s:8:"repeater"') !== -1 )
					{
						var obj = {};
						obj['repeater_field_item_per_line'] = $addAnotherForm.find('#repeater_field_item_per_line').is(':checked');
						obj['repeater_field_fill_empty_columns'] = $addAnotherForm.find('#repeater_field_fill_empty_columns').is(':checked');
						$clone.find('input[name^=cc_settings]').val(window.JSON.stringify(obj));
					}
					break;
				case 'woo':
					switch ( $clone.find('input[name^=cc_value]').val() )
					{
						case '_upsell_ids':
						case '_crosssell_ids':
						case 'item_data___upsell_ids':
						case 'item_data___crosssell_ids':
							$clone.find('input[name^=cc_settings]').val($addAnotherForm.find('select.linked_field_export_data').val());
							break;
					}
					break;
				case 'woo_order':
					$woo_type = $clone.find('input[name^=cc_value]');
					switch ($woo_type.val()) {
						case 'post_date':
						case 'post_modified':
						case '_completed_date':
							var $dateType = $addAnotherForm.find('select.date_field_export_data').val();
							if ($dateType == 'unix')
								$clone.find('input[name^=cc_settings]').val('unix');
							else
								$clone.find('input[name^=cc_settings]').val($('.pmxe_date_format').val());
							break;
					}
					break;
				default:
					// save option for media images field types
					if ( $clone.find('input[name^=cc_type]').val().indexOf('image_') !== -1 )
					{
						var obj = {};
						obj['is_export_featured'] = $addAnotherForm.find('#is_image_export_featured').is(':checked');
						obj['is_export_attached'] = $addAnotherForm.find('#is_image_export_attached_images').is(':checked');
						obj['image_separator'] = $addAnotherForm.find('input[name=image_field_separator]').val();
						$clone.find('input[name^=cc_options]').val(window.JSON.stringify(obj));
					}

					break;
			}

			trigger_warnings();

			$addAnotherForm.hide();

			$('.wpallexport-overlay').hide();

			$('.custom_column').removeClass('active');

		});

		// Clicking on column for edit
		$('#columns').find('.custom_column').live('click', function(){

			$addAnotherForm.find('form')[0].reset();
			$addAnotherForm.find('input[type=checkbox]').removeAttr('checked');

			$addAnotherForm.removeClass('dc').addClass('cc');
			$addAnotherForm.attr('rel', $(this).attr('rel'));

			$addAnotherForm.find('.wpallexport-add-row-title').hide();
			$addAnotherForm.find('.wpallexport-edit-row-title').show();

			$addAnotherForm.find('input.column_name').parents('div.input:first').show();

			$addAnotherForm.find('.cc_field').hide();
			$('.custom_column').removeClass('active');
			$(this).addClass('active');

			var $elementType  = $(this).find('input[name^=cc_type]');
			var $elementLabel = $(this).find('input[name^=cc_label]');


			$('.wp_all_export_saving_status').html('');

			$addAnotherForm.find('select[name=column_value_type]').find('option').each(function(){
				if ($(this).attr('label') == $elementLabel.val() && $(this).val() == $elementType.val())
					$(this).attr({'selected':'selected'}).click();
				else
					$(this).removeAttr('selected');
			});

			$('.wp-all-export-chosen-select').trigger('chosen:updated');

			// set php snipped
			var $php_code = $(this).find('input[name^=cc_code]');
			var $is_php = parseInt($(this).find('input[name^=cc_php]').val());

			if ($is_php){
				$addAnotherForm.find('#coperate_php').attr({'checked':'checked'});
				$addAnotherForm.find('#coperate_php').parents('div.input:first').find('div[class^=switcher-target]').show();
			}
			else{
				$addAnotherForm.find('#coperate_php').removeAttr('checked');
				$addAnotherForm.find('#coperate_php').parents('div.input:first').find('div[class^=switcher-target]').hide();
			}

			$addAnotherForm.find('#coperate_php').parents('div.input:first').find('.php_code').val($php_code.val());

			var $options = $(this).find('input[name^=cc_options]').val();
			var $settings = $(this).find('input[name^=cc_settings]').val();

			var $fieldType = $elementType.val();

			if ($elementLabel.val() == '_sale_price_dates_from' || $elementLabel.val() == '_sale_price_dates_to') $fieldType = 'date';

			switch ( $fieldType ){
				case 'sql':
					$addAnotherForm.find('textarea.column_value').val($(this).find('input[name^=cc_sql]').val());
					$addAnotherForm.find('.sql_field_type').show();
					break;
				case 'acf':
					if ($options.indexOf('s:4:"type";s:8:"repeater"') !== -1)
					{
						$addAnotherForm.find('.repeater_field_type').show();
						if ($settings != "")
						{
							var $field_options = window.JSON.parse($settings);
							if ($field_options.repeater_field_item_per_line) $addAnotherForm.find('#repeater_field_item_per_line').attr('checked','checked');
							if ($field_options.repeater_field_fill_empty_columns) $addAnotherForm.find('#repeater_field_fill_empty_columns').attr('checked','checked');
						}
					}
					break;
				case 'woo':
					$woo_type = $(this).find('input[name^=cc_value]');
					switch ($woo_type.val())
					{
						case '_upsell_ids':
						case '_crosssell_ids':
						case 'item_data___upsell_ids':
						case 'item_data___crosssell_ids':

							$addAnotherForm.find('select.linked_field_export_data').find('option').each(function(){
								if ($(this).val() == $settings)
									$(this).attr({'selected':'selected'}).click();
								else
									$(this).removeAttr('selected');
							});
							$addAnotherForm.find('.linked_field_type').show();
							break;
					}
					break;
				case 'woo_order':
					$woo_type = $(this).find('input[name^=cc_value]');
					switch ($woo_type.val())
					{
						case 'post_date':
						case 'post_modified':
						case '_completed_date':

							$addAnotherForm.find('select.date_field_export_data').find('option').each(function(){
								if ($(this).val() == $settings || $settings != 'unix' && $(this).val() == 'php')
									$(this).attr({'selected':'selected'}).click();
								else
									$(this).removeAttr('selected');
							});

							if ($settings != 'php' && $settings != 'unix'){
								if ($settings != '0') $('.pmxe_date_format').val($settings); else $('.pmxe_date_format').val('');
								$('.pmxe_date_format_wrapper').show();
							}
							else{
								$('.pmxe_date_format').val('');
							}
							$addAnotherForm.find('.date_field_type').show();
							break;
					}
					break;
				case 'date':
				case 'comment_date':
				case 'user_registered':
				case 'post_modified':
					$addAnotherForm.find('select.date_field_export_data').find('option').each(function(){
						if ($(this).val() == $settings || $settings != 'unix' && $(this).val() == 'php')
							$(this).attr({'selected':'selected'}).click();
						else
							$(this).removeAttr('selected');
					});

					if ($settings != 'php' && $settings != 'unix'){
						if ($settings != '0') $('.pmxe_date_format').val($settings); else $('.pmxe_date_format').val('');
						$('.pmxe_date_format_wrapper').show();
					}
					else{
						$('.pmxe_date_format').val('');
					}
					$addAnotherForm.find('.date_field_type').show();
					break;
				default:

					if ( $elementType.val().indexOf('image_') !== -1 )
					{
						$addAnotherForm.find('.image_field_type').show();

						if ($options != "")
						{
							var $field_options = window.JSON.parse($options);

							if ($field_options.is_export_featured) $addAnotherForm.find('#is_image_export_featured').attr('checked','checked');
							if ($field_options.is_export_attached) $addAnotherForm.find('#is_image_export_attached_images').attr('checked','checked');

							$addAnotherForm.find('input[name=image_field_separator]').val($field_options.image_separator);
						}
					}

					break;
			}

			$addAnotherForm.find('input.switcher').change();

			var $column_name = $(this).find('input[name^=cc_name]').val();

			$addAnotherForm.find('input.column_name').val($column_name);
			$addAnotherForm.show();
			$('.wpallexport-overlay').show();

		});

		// Preview export file
		var doPreview = function( ths, tagno ){

			$('.wpallexport-overlay').show();

			ths.pointer({
	            content: '<div class="wpallexport-preview-preload wpallexport-pointer-preview"></div>',
	            position: {
	                edge: 'right',
	                align: 'center'
	            },
	            pointerWidth: 715,
	            close: function() {
	                $.post( ajaxurl, {
	                    pointer: 'pksn1',
	                    action: 'dismiss-wp-pointer'
	                });
	                $('.wpallexport-overlay').hide();
	            }
	        }).pointer('open');

	        var $pointer = $('.wpallexport-pointer-preview').parents('.wp-pointer').first();

	        var $leftOffset = ($(window).width() - 715)/2;

	        $pointer.css({'position':'fixed', 'top' : '15%', 'left' : $leftOffset + 'px'});

			var request = {
				action: 'wpae_preview',
				data: $('form.wpallexport-step-3').serialize(),
				custom_xml: xml_editor.getValue(),
				tagno: tagno,
				security: wp_all_export_security
		    };
			var url = get_valid_ajaxurl();
			var show_cdata = $('#show_cdata_in_preview').val();

			if (url.indexOf("?") == -1)  {
				url += '?show_cdata=' + show_cdata;
			} else {
				url += '&show_cdata=' + show_cdata;
			}

			$.ajax({
				type: 'POST',
				url: url,
				data: request,
				success: function(response) {

					ths.pointer({'content' : response.html});

					$pointer.css({'position':'fixed', 'top' : '15%', 'left' : $leftOffset + 'px'});

					var $preview = $('.wpallexport-preview');

					$preview.parent('.wp-pointer-content').removeClass('wp-pointer-content').addClass('wpallexport-pointer-content');

					$preview.find('.navigation a').unbind('click').die('click').live('click', function () {

						tagno += '#prev' == $(this).attr('href') ? -1 : 1;

						doPreview(ths, tagno);

					});

				},
				error: function( jqXHR, textStatus ) {
					// Handle an eval error
					if(jqXHR.responseText.indexOf('[[ERROR]]') !== -1) {
						vm.preiviewText = $('.wpallexport-preview-title').text();

						var json = jqXHR.responseText.split('[[ERROR]]')[1];
						json = $.parseJSON(json);
						ths.pointer({'content' : '<div id="post-preview" class="wpallexport-preview">' +
						'<p class="wpallexport-preview-title">' + json.title + '</p>\
						<div class="wpallexport-preview-content">'+json.error+'</div></div></div>'});

						$pointer.css({'position':'fixed', 'top' : '15%', 'left' : $leftOffset + 'px'});

					} else {
						ths.pointer({'content' : '<div id="post-preview" class="wpallexport-preview">' +
						'<p class="wpallexport-preview-title">An error occured</p>\
						<div class="wpallexport-preview-content">An unknown error occured</div></div></div>'});
						$pointer.css({'position':'fixed', 'top' : '15%', 'left' : $leftOffset + 'px'});
					}

				},
				dataType: "json"
			});

		};

		$(this).find('.preview_a_row').click( function(){
			doPreview($(this), 1);
		});

		// preview custom XML template
		$(this).find('.preview_a_custom_xml_row').click(function(){
			doPreview($(this), 1);
		});

        // help custom XML template
        $(this).find('.help_custom_xml').click(function(){
            $('.wp-all-export-custom-xml-help').css('left', ($( document ).width()/2) - 255 ).show();
            $('#wp-all-export-custom-xml-help-inner').css('max-height', $( window ).height()-150).show();
            $('.wpallexport-overlay').show();
        });

        $('.wp_all_export_custom_xml_help').find('h3').click(function(){
            var $action = $(this).find('span').html();
            $('.wp_all_export_custom_xml_help').find('h3').each(function(){
                $(this).find('span').html("+");
            });
            if ( $action == "+" ) {
                $('.wp_all_export_help_tab').slideUp();
                $('.wp_all_export_help_tab[rel=' + $(this).attr('id') + ']').slideDown();
                $(this).find('span').html("-");
            }
            else{
                $('.wp_all_export_help_tab[rel=' + $(this).attr('id') + ']').slideUp();
                $(this).find('span').html("+");
            }
        });

		$('.wpae-available-fields-group').click(function(){
			var $mode = $(this).find('.wpae-expander').text();
			$(this).next('div').slideToggle();
			if ($mode == '+') $(this).find('.wpae-expander').text('-'); else $(this).find('.wpae-expander').text('+');
		});

		$('.pmxe_remove_column').live('click', function(){
			$(this).parents('li:first').remove();
		});

		$('.close_action').click(function(){
			$(this).parents('fieldset:first').hide();
			$('.wpallexport-overlay').hide();
			$('#columns').find('div.active').removeClass('active');
		});

		$('.date_field_export_data').change(function(){
			if ($(this).val() == "unix")
				$('.pmxe_date_format_wrapper').hide();
			else
				$('.pmxe_date_format_wrapper').show();
		});

		$('.xml-expander').live('click', function () {
			var method;
			if ('-' == $(this).text()) {
				$(this).text('+');
				method = 'addClass';
			} else {
				$(this).text('-');
				method = 'removeClass';
			}
			// for nested representation based on div
			$(this).parent().find('> .xml-content')[method]('collapsed');
			// for nested representation based on tr
			var $tr = $(this).parent().parent().filter('tr.xml-element').next()[method]('collapsed');
		});

		$('.wp-all-export-edit-column').css('left', ($( document ).width()/2) - 355 );

	    var wp_all_export_config = {
	      '.wp-all-export-chosen-select' : {width:"50%"}
	    };

	    for (var selector in wp_all_export_config) {
	    	$(selector).chosen(wp_all_export_config[selector]);
	    	$(selector).on('change', function(evt, params) {
				$('.cc_field').hide();
				var selected_value = $(selector).find('option:selected').attr('label');
				var ftype = $(selector).val();

				switch (ftype){
					case 'post_modified':
					case 'date':
						$('.date_field_type').show();
						break;
					case 'sql':
						$('.sql_field_type').show();
						break;
					case 'woo':
							switch (selected_value){
								case 'item_data___upsell_ids':
								case 'item_data___crosssell_ids':
								case '_upsell_ids':
								case '_crosssell_ids':
									$addAnotherForm.find('.linked_field_type').show();
									break;
							}
						break;
					default:
						if ( $(selector).val().indexOf('image_') !== -1)
						{
							$('.image_field_type').show();
						}
						break;
				}
			});
	    }

	    $('.wp-all-export-advanced-field-options').click(function(){
	    	if ($(this).find('span').html() == '+'){
	    		$(this).find('span').html('-');
	    		$('.wp-all-export-advanced-field-options-content').fadeIn('fast', function(){
	    			if ($('#coperate_php').is(':checked')) editor.setCursor(1);
	    		});
	    	}
	    	else{
	    		$(this).find('span').html('+');
	    		$('.wp-all-export-advanced-field-options-content').hide();
	    	}
	    });

	    // Auto generate available data
	    $('.wp_all_export_auto_generate_data').click(function(){

	    	$('ol#columns').find('li:not(.placeholder)').fadeOut().remove();
	    	$('ol#columns').find('li.placeholder').fadeOut();

	    	if ($('#available_data').find('li.wp_all_export_auto_generate').length)
	    	{
	    		$('#available_data').find('li.wp_all_export_auto_generate, li.pmxe_cats').each(function(i, e){
		    		var $clone = $(this).clone();
		    		$clone.attr('rel', i);
		    		$( "<li></li>" ).html( $clone.html() ).appendTo( $( "#columns_to_export ol" ) );
		    	});
	    	}
	    	else
	    	{
	    		$('#available_data').find('div.custom_column').each(function(i, e){
	    			var $parent = $(this).parent('li');
		    		var $clone = $parent.clone();
		    		$clone.attr('rel', i);

		    		if ( $clone.find('input[name^=cc_type]').val().indexOf('image_') !== -1 )
					{
						$clone.find('.wpallexport-xml-element').html('Image ' + $clone.find('input[name^=cc_name]').val());
						$clone.find('input[name^=cc_name]').val('Image ' + $clone.find('input[name^=cc_name]').val());
					}

					if ( $clone.find('input[name^=cc_type]').val().indexOf('attachment_') !== -1 )
					{
						$clone.find('.wpallexport-xml-element').html('Attachment ' + $clone.find('input[name^=cc_name]').val());
						$clone.find('input[name^=cc_name]').val('Attachment ' + $clone.find('input[name^=cc_name]').val());
					}

		    		$( "<li></li>" ).html( $clone.html() ).appendTo( $( "#columns_to_export ol" ) );
		    	});
	    	}

	    	trigger_warnings();

	    });

		$('.wp_all_export_clear_all_data').live('click', function(){
			$('ol#columns').find('li:not(.placeholder)').remove();
			$('ol#columns').find('li.placeholder').fadeIn();
		});

	    if ($('input[name^=selected_post_type]').length){

    		var postType = $('input[name^=selected_post_type]').val();

    		init_filtering_fields();

			liveFiltering();

		    $('form.wpallexport-template').find('input[type=submit]').click(function(e){
				e.preventDefault();

				$('#validationError').fadeOut();
				$('#validationError p').find('*').remove();

				var submitButton = $(this);

				if(!vm.isGoogleMerchantsExport) {
					// Validate the form by sending it to preview before submitting it
					var request = {
						action: 'wpae_preview',
						data: $('form.wpallexport-step-3').serialize(),
						custom_xml: xml_editor.getValue(),
						security: wp_all_export_security
					};


					$.ajax({
						type: 'POST',
						url: get_valid_ajaxurl(),
						data: request,
						success: function(response) {

							// Look for errors
							var tempDom = $('<div>').append($.parseHTML(response.html));
							var errorMessage = $('.error', tempDom);

							// If we have error messages
							if(errorMessage.length) {
								// Display the error messages
								errorMessage.each(function(){
									$('#validationError').find('p').append($(this));
								});

								$('#validationError').fadeIn();
								$('html, body').animate({scrollTop: $("#validationError").offset().top - 50});
							} else {
								// Else submit the form
								$('.hierarhy-output').each(function(){
									var sortable = $('.wp_all_export_filtering_rules.ui-sortable');
									if (sortable.length){
										$(this).val(window.JSON.stringify(sortable.pmxe_nestedSortable('toArray', {startDepthCount: 0})));
									}
								});
								submitButton.parents('form:first').submit();
							}
						},
						error: function( jqXHR, textStatus ) {
							$('#validationError p').html('');

							// Handle an eval error
							if(jqXHR.responseText.indexOf('[[ERROR]]') != -1) {
								var json = jqXHR.responseText.split('[[ERROR]]')[1];
								json = $.parseJSON(json);

								$('#validationError').find('p').append(json.error);
								$('#validationError').fadeIn();
								$('html, body').animate({scrollTop: $("#validationError").offset().top - 50});

							} else {
								// We don't know the error
								$('#validationError').find('p').html('An unknown error occured');
								$('#validationError').fadeIn();
								$('html, body').animate({scrollTop: $("#validationError").offset().top - 50});
							}
						},
						dataType: "json"
					});
				} else {
					submitButton.parents('form:first').submit();
				}
			});		
    	}   	     	   

    	if ( $('input[name=export_to]').val() == 'csv' && $('#export_to_sheet').val() == 'xls' && $('#export_to_sheet').val() == 'xlsx'){
    		$('.export_to_xls_upgrade_notice').show();
    		$('.wpallexport-submit-template').attr('disabled', 'disabled');
    		$('.wpallexport-submit-buttons').hide();
    	} 
    	else{
    		$('.export_to_xls_upgrade_notice').hide();
    		$('.wpallexport-submit-template').removeAttr('disabled');
    		$('.wpallexport-submit-buttons').show();
    	}

    	$('.wpallexport-import-to-format').click(function(){

			var isWooCommerceOrder = vm.isWoocommerceOrderExport();

			$('.wpallexport-import-to-format').removeClass('selected');
    		$(this).addClass('selected');

    		if ($(this).hasClass('wpallexport-csv-type'))
    		{
				vm.isGoogleMerchantsExport = false;
				//resetDraggable();
				angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('googleMerchantsDeselected');
    			$('.wpallexport-custom-xml-template').slideUp();
    			$('.wpallexport-simple-xml-template').slideDown();
    			$('.wpallexport-csv-options').show();
    			$('.wpallexport-xml-options').hide();

				$('.wpallexport-csv-advanced-options').css('display', 'block');
				$('.wpallexport-xml-advanced-options').css('display', 'none');

    			$('input[name=export_to]').val('csv');

    			if ($('#export_to_sheet').val() !== 'csv') {
					if (isWooCommerceOrder || vm.isProductVariationsExport()) {
						$('.csv_delimiter').hide();
						$('.export_to_csv').show();
					} else {
						$('.export_to_csv').hide();
					}
					$('.wpallexport-submit-template').attr('disabled', 'disabled');
					$('.wpallexport-submit-buttons').hide();
				} else {
					/** isProductVariationsExport */
					if (isWooCommerceOrder) {
						$('.export_to_csv').show();
					} else {
						$('.export_to_csv').show();
						$('.csv_delimiter').show();
					}
					$('.wpallexport-submit-template').removeAttr('disabled');
					$('.wpallexport-submit-buttons').show();
				}
								
				$('.custom_xml_upgrade_notice').hide();
    		}
    		else
    		{
    			$('.wpallexport-csv-options').hide();
    			$('.wpallexport-xml-options').show();
    			$('input[name=export_to]').val('xml');
    			$('.xml_template_type').change();

				$('.wpallexport-csv-advanced-options').css('display', 'none');
				$('.wpallexport-xml-advanced-options').css('display', 'block');
				var $xml_export_format = $('.xml_template_type').val();
				if ( $xml_export_format == 'custom' || $xml_export_format == 'XmlGoogleMerchants'){
					$('.wpallexport-submit-template').attr('disabled', 'disabled');

					if ( $xml_export_format == 'custom') {
						$('.custom_xml_upgrade_notice.wpallexport-custom-xml-template').show();
					} else if ($xml_export_format == 'XmlGoogleMerchants') {
						$('.custom_xml_upgrade_notice.wpallexport-google-merchants-template').show();
					}

					$('.wpallexport-submit-buttons').hide();
				}
				else{
					$('.wpallexport-submit-buttons').show();
				}
    		}
    	});

    	// template form: auto submit when `load template` list value is picked
		$(this).find('select[name="load_template"]').live('change', function () {

			var template = $(this).find('option:selected').val();
			var exportMode = $('.xml_template_type').find('option:selected').val();

			$(this).parents('form').trigger('submit', ['templateSelected']);
			return;
			if( exportMode == 'XmlGoogleMerchants') {
				angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('selectedTemplate', template);
			} else {
				$(this).parents('form').submit();
			}
		});

		var height = $(window).height();
		$('#available_data').find('.wpallexport-xml').css({'max-height': height - 125});

        // dismiss export template warnings
        $('.wp-all-export-warning').find('.notice-dismiss').click(function(){

            var $parent = $(this).parent('.wp-all-export-warning');

            $('#dismiss_warnings').val('1');

            if ( typeof export_id == 'undefined') {
                $parent.slideUp();
                return true;
            }

            var request = {
                action: 'dismiss_export_warnings',
                data: {
                    export_id: export_id,
                    warning: $parent.find('p:first').html()
                },
                security: wp_all_export_security
            };

            $parent.slideUp();

            $.ajax({
                type: 'POST',
                url: get_valid_ajaxurl(),
                data: request,
                success: function(response) {},
                dataType: "json"
            });
        });

	});
	// [ \Step 2 ( export template ) ]


	// [ Step 3 ( export options ) ]
    if ( $('.wpallexport-export-options').length ){

    	if ($('input[name^=selected_post_type]').length){

    		var postType = $('input[name^=selected_post_type]').val();

    		init_filtering_fields();

    		// if ($('form.edit').length){

    			liveFiltering();

    		// }

		    $('form.choose-export-options').find('input[type=submit]').click(function(e){
				e.preventDefault();

				$('.hierarhy-output').each(function(){
					var sortable = $('.wp_all_export_filtering_rules.ui-sortable');
					if (sortable.length){
						$(this).val(window.JSON.stringify(sortable.pmxe_nestedSortable('toArray', {startDepthCount: 0})));
					}
				});

				$(this).parents('form:first').submit();
			});
    	}

    	$('.wp_all_export_confirm_and_run').click(function(e){
			e.preventDefault();
			$('form.choose-export-options').submit();
		});

    }
	$('#export_only_new_stuff').click(function(){
		$(this).attr('disabled','disabled');
		$('label[for=export_only_new_stuff]').addClass('loading');
		liveFiltering(null, function(){
			$('label[for=export_only_new_stuff]').removeClass('loading');
			$('#export_only_new_stuff').removeAttr('disabled');
		});
	});
	$('#export_only_modified_stuff').click(function(){
		$(this).attr('disabled','disabled');
		$('label[for=export_only_modified_stuff]').addClass('loading');
		liveFiltering(null, function(){
			$('label[for=export_only_modified_stuff]').removeClass('loading');
			$('#export_only_modified_stuff').removeAttr('disabled');
		});
	});
    // [ \Step 3 ( export options ) ]


    // [ Step 4 ( export completed ) ]
    $('.download_data').click(function(){
    	window.location.href = $(this).attr('rel');
    });
    // [ \Step 4 ( export completed ) ]


    // [ Additional functionality ]

    // Add new filtering rule
    $('#wp_all_export_add_rule').live('click', function(){

    	var $el   = $('#wp_all_export_xml_element');
    	var $rule = $('#wp_all_export_rule');
    	var $val  = $('#wp_all_export_value');

    	if ($el.val() == "" || $rule.val() == "") return;

    	var relunumber = $('.wp_all_export_filtering_rules').find('li').length + 1;

    	var html = '<li id="item_'+ relunumber +'" class="dragging"><div class="drag-element">';
    		html += '<input type="hidden" value="'+ $el.val() +'" class="wp_all_export_xml_element" name="wp_all_export_xml_element['+relunumber+']"/>';
    		html += '<input type="hidden" value="'+ $el.find('option:selected').html() +'" class="wp_all_export_xml_element_title" name="wp_all_export_xml_element_title['+relunumber+']"/>';
    		html += '<input type="hidden" value="'+ $rule.val() +'" class="wp_all_export_rule" name="wp_all_export_rule['+relunumber+']"/>';
    		html += '<input type="hidden" value="'+ $val.val() +'" class="wp_all_export_value" name="wp_all_export_value['+relunumber+']"/>';
    		html += '<span class="rule_element">' + $el.find('option:selected').html() + '</span> <span class="rule_as_is">' + $rule.find('option:selected').html() + '</span> <span class="rule_condition_value">"' + $val.val() +'"</span>';
    		html += '<span class="condition"> <label for="rule_and_'+relunumber+'">AND</label><input id="rule_and_'+relunumber+'" type="radio" value="and" name="rule['+relunumber+']" checked="checked" class="rule_condition"/><label for="rule_or_'+relunumber+'">OR</label><input id="rule_or_'+relunumber+'" type="radio" value="or" name="rule['+relunumber+']" class="rule_condition"/> </span>';
    		html += '</div><a href="javascript:void(0);" class="icon-item remove-ico"></a></li>';

    	$('#wpallexport-filters, #wp_all_export_apply_filters').show();
    	$('#no_options_notice').hide();

    	$('.wp_all_export_filtering_rules').append(html);

    	$('.wp_all_export_filtering_rules').find('.condition:hidden').each(function(){
    		$(this).show();
    		$(this).find('.rule_condition:first').attr('checked', 'checked');
    	});
    	$('.wp_all_export_filtering_rules').find('.condition').removeClass('last_condition');
        $('.wp_all_export_filtering_rules').find('.condition:last').addClass('last_condition');

        $('.wp_all_export_product_matching_mode').show();

    	$el.prop('selectedIndex',0).trigger('chosen:updated');
    	$rule.prop('selectedIndex',0).trigger('chosen:updated');

    	$val.val('');
    	$('#wp_all_export_value').show();

		$('#date_field_notice').hide();

    	liveFiltering();

    });

	// Re-count posts when clicking "OR" | "AND" clauses
	$('input[name^=rule]').live('click', function(){
		liveFiltering();
	});
	$('input.wpml_lang').live('click', function(){
		var inputName = $(this).attr('name');
		var value = $('input[name='+inputName +']:checked').val();
		var $thisInput = $('.wpml_lang[value='+value +']');
		$thisInput.prop('checked', 'checked');

		$('#wpml_lang').val(value);
		liveFiltering();
	});
	// Re-count posts when changing product matching mode in filtering section
	$('select[name^=product_matching_mode]').live('change', function(){
		liveFiltering();
	});
	// Re-count posts when deleting a filtering rule
	$('.wp_all_export_filtering_rules').find('.remove-ico').live('click', function(){
		$(this).parents('li:first').remove();
		if ( ! $('.wp_all_export_filtering_rules').find('li').length)
		{
			$('#wp_all_export_apply_filters').hide();
    		$('#no_options_notice').show();
    		$('.wp_all_export_product_matching_mode').hide();
		}
		else
		{
			$('.wp_all_export_filtering_rules').find('li:last').find('.condition').addClass('last_condition');
		}

		liveFiltering();
	});
	// hide "value" input when "Is Empty" or "Is Not Empty" rule is selected
	$('#wp_all_export_rule').change(function(){
		if ($(this).val() == 'is_empty' || $(this).val() == 'is_not_empty')
			$('#wp_all_export_value').hide();
		else
			$('#wp_all_export_value').show();
	});
    
    // auot-generate zapier API key
    $('input[name=pmxe_generate_zapier_api_key]').click(function(e){

    	e.preventDefault();

    	var request = {
			action: 'generate_zapier_api_key',
			security: wp_all_export_security
	    };

		$.ajax({
			type: 'POST',
			url: get_valid_ajaxurl(),
			data: request,
			success: function(response) {
				$('input[name=zapier_api_key]').val(response.api_key);
			},
			error: function( jqXHR, textStatus ) {

			},
			dataType: "json"
		});
    });

    var $tmp_xml_template = '';
    var $xml_template_first_load = true;

    $('.xml_template_type').change(function(e){

		switch ($(this).find('option:selected').val()){
    		case 'simple':
    			$('.simple_xml_template_options').slideDown();
    			$('.wpallexport-simple-xml-template').slideDown();
    			$('.wpallexport-custom-xml-template').slideUp();
				$('.wpallexport-function-editor').slideUp();

    			$('.pmxe_product_data').find(".wpallexport-xml-element:contains('Attributes')").parents('li:first').show();
    			$('.wpallexport-submit-template').removeAttr('disabled');
    			$('.custom_xml_upgrade_notice').hide();
    			$('.wpallexport-submit-buttons').show();
    			if(angular.element(document.getElementById('googleMerchants')).injector()){
					resetDraggable();
					angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('googleMerchantsDeselected');
                }
                vm.isGoogleMerchantsExport = false;

				if(!angular.isUndefined(e.originalEvent)) {
					if ( ! $('.wpallexport-file-options').hasClass('closed')) $('.wpallexport-file-options').find('.wpallexport-collapsed-header').click();
				}

                break;
			case 'custom':
				if(angular.element(document.getElementById('googleMerchants')).injector()){
					resetDraggable();
					angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('googleMerchantsDeselected');
                }
                vm.isGoogleMerchantsExport = false;
				$('.custom_xml_upgrade_notice').hide();
                $('.wpallexport-submit-buttons').hide();
    			$('.simple_xml_template_options').slideUp();
    			$('.wpallexport-simple-xml-template').slideUp();
				$('.wpallexport-function-editor').slideDown();

				// If the event was not triggered by the user
				if(!angular.isUndefined(e.originalEvent)) {
					if ( ! $('.wpallexport-file-options').hasClass('closed')) $('.wpallexport-file-options').find('.wpallexport-collapsed-header').click();
				}

    			$('.wpallexport-custom-xml-template').slideDown(400, function(){
    				xml_editor.setCursor(1);
    			});
    			$('.pmxe_product_data').find(".wpallexport-xml-element:contains('Attributes')").parents('li:first').hide();

                if ( $(this).find('option:selected').val() == 'XmlGoogleMerchants' ){
                    if ( ! $xml_template_first_load ) {
                        $tmp_xml_template = xml_editor.getValue();
                        // Get all necessary data according to the spec
                        var request = {
                            action: 'get_xml_spec',
                            security: wp_all_export_security,
                            spec_class: $(this).find('option:selected').val()
                        };
                        xml_editor.setValue("Loading...");
                        $.ajax({
                            type: 'POST',
                            url: get_valid_ajaxurl(),
                            data: request,
                            success: function (response) {
                                if (response.result) {
                                    xml_editor.setValue(response.fields);
                                }
                            },
                            error: function (jqXHR, textStatus) {

                            },
                            dataType: "json"
                        });
                    }
                }
                else{
                    if ( $tmp_xml_template != '' ){
                        xml_editor.setValue($tmp_xml_template);
                        $tmp_xml_template = '';
                    }
                }
                $('.wpallexport-submit-template').attr('disabled', 'disabled');
                $('.wpallexport-custom-xml-template').show();
                break;
            case 'XmlGoogleMerchants':
            	$('.wpallexport-submit-buttons').hide();
				$('.simple_xml_template_options').slideUp();
				$('.wpallexport-simple-xml-template').slideUp();
				$('.wpallexport-custom-xml-template').slideUp();
				if(!vm.isCSVExport()) {
					$('.wpallexport-google-merchants-template').slideUp();
				}
				$('.wpallexport-google-merchants-template').slideUp();

				if ( ! $('.wpallexport-file-options').hasClass('closed')) {
					$('.wpallexport-file-options').find('.wpallexport-collapsed-header').click();
				}

				$('.pmxe_product_data').find(".wpallexport-xml-element:contains('Attributes')").parents('li:first').show();

				if(angular.element(document.getElementById('googleMerchants')).injector()){
					resetDraggable();
					angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('googleMerchantsSelected', vm.isProductVariationsExport());
				}
				vm.isGoogleMerchantsExport = true;
                $('.wpallexport-submit-template').attr('disabled', 'disabled');
                $('.wpallexport-google-merchants-template').show();
    			break;
    		default:
    			resetDraggable();
				angular.element(document.getElementById('googleMerchants')).injector().get('$rootScope').$broadcast('googleMerchantsDeselected');
    			vm.isGoogleMerchantsExport = false;
                $('.simple_xml_template_options').slideUp();
    			$('.wpallexport-simple-xml-template').slideDown();
    			$('.wpallexport-custom-xml-template').slideUp();
			$('.wpallexport-function-editor').slideDown();

    			$('.pmxe_product_data').find(".wpallexport-xml-element:contains('Attributes')").parents('li:first').show();
    			$('.wpallexport-submit-template').removeAttr('disabled');
    			$('.custom_xml_upgrade_notice').hide();
    			break;
    	}
        $xml_template_first_load = false;
    }).change();

    $('.wpallexport-overlay').click(function(){
		$('.wp-pointer').hide();
		$('#columns').find('div.active').removeClass('active');
		$('fieldset.wp-all-export-edit-column').hide();
        $('fieldset.wp-all-export-custom-xml-help').hide();
		$(this).hide();
	});

    if ($('.wpallexport-template').length)
    {
    	setTimeout(function(){
			$('.wpallexport-template').slideDown();
		}, 1000);
    }
	// [ \Additional functionality ]

	// Logic for radio boxes (CDATA settings)
	$('input[name=simple_custom_xml_cdata_logic]').change(function(){
		var value = $('input[name=simple_custom_xml_cdata_logic]:checked').val();
		$('#custom_custom_xml_cdata_logic_'+value).prop('checked', true);
		$('#custom_xml_cdata_logic').val(value);
	});


	$('input[name=custom_custom_xml_cdata_logic]').change(function(event) {
		event.stopImmediatePropagation();
		var value = $('input[name=custom_custom_xml_cdata_logic]:checked').val();
		$('#simple_custom_xml_cdata_logic_'+value).prop('checked', true);
		$('input[name=simple_custom_xml_cdata_logic]').trigger('change');

	});

	// Logic for show CDATA tags in preview
	$('.show_cdata_in_preview').change(function(){
		if($(this).is(':checked')) {
			$('#show_cdata_in_preview').val(1);
			$('.show_cdata_in_preview').prop('checked', true);
		} else {
			$('#show_cdata_in_preview').val(0);
			$('.show_cdata_in_preview').prop('checked', false);
		}
	});

	// Logic to show CSV advanced options
	$('#export_to_sheet').change(function(e){

		if ( $('input[name=export_to]').val() === 'xml' ) return;

		var isWooCommerceOrder = vm.isWoocommerceOrderExport();
		var isVariationsExport = vm.isProductVariationsExport();

		var value = $(this).val();
		if(value === 'xls' || value === 'xlsx') {
			if(isWooCommerceOrder || isVariationsExport) {
				$('.csv_delimiter').hide();
			} else {
				$('.export_to_csv').slideUp();
			}
			$('.export_to_xls_upgrade_notice').show();
			$('.wpallexport-submit-buttons').hide();
			$('.wpallexport-submit-template').attr('disabled', 'disabled');
		} else {
			if(isWooCommerceOrder || isVariationsExport) {
				$('.csv_delimiter').show();
			} else {
				$('.export_to_csv').slideDown();
			}
			$('.export_to_xls_upgrade_notice').hide();
			$('.wpallexport-submit-buttons').show();
			$('.wpallexport-submit-template').removeAttr('disabled');
		}
	});

	$('#templateForm').submit(function(event){
		
		var exportType = $('select.xml_template_type').val();

		if(vm.isGoogleMerchantsExport || exportType == 'custom') {
			event.stopImmediatePropagation();
			return false;
		}
	});

	$('select[name=column_value_type]').change(function(){
		var disabledFields = ['fees', 'notes', 'refunds', 'taxes', 'item_data', 'items'];
		var selectedField  = $(this).find('option:selected').attr('options');
		var isShowWarning  = false;
		for (var i = 0; i < disabledFields.length; i++) {
			if (disabledFields[i] == selectedField){
				isShowWarning = true;
				break;
			}
		};
		if (isShowWarning){ 
			$('.disabled_fields_upgrade_notice').show(); 
			$('.save_action').addClass('wp_all_export_disabled_button').attr('disabled', 'disabled');
		}
		else {
			$('.disabled_fields_upgrade_notice').hide();
			$('.save_action').removeClass('wp_all_export_disabled_button').removeAttr('disabled');
		}

	});

	var $el = $('#available_data');
	if($el) {
		if($el.offset()) {


			var originalOffset = $el.offset().top - 50;
			var elementWidth = $el.width();

			$el.css('width', elementWidth);

			$(window).scroll(function (e) {

				var isPositionFixed = ($el.css('position') == 'fixed');
				if ($(this).scrollTop() > originalOffset && !isPositionFixed) {
					$('#available_data').css({'position': 'fixed', 'top': '50px'});
				}
				if ($(this).scrollTop() < originalOffset && isPositionFixed) {
					$('#available_data').css({'position': 'static', 'top': '50px'});
				}
			});
		}
	}


});})(jQuery, window.EventService);
