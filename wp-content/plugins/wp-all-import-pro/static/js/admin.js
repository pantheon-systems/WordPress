/**
 * plugin admin area javascript
 */
(function($){$(function () {

	if ( ! $('body.wpallimport-plugin').length) return; // do not execute any code if we are not on plugin page	
	
	// fix wpallimport-layout position
	setTimeout(function () {
		$('table.wpallimport-layout').length && $('table.wpallimport-layout td.left h2:first-child').css('margin-top',  $('.wrap').offset().top - $('table.wpallimport-layout').offset().top);
	}, 10);	
	
	// help icons
	$('a.wpallimport-help').tipsy({
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

	// swither show/hide logic
	$('input.switcher').live('change', function (e) {	

		if ($(this).is(':radio:checked')) {
			$(this).parents('form').find('input.switcher:radio[name="' + $(this).attr('name') + '"]').not(this).change();
		}
		var $targets = $('.switcher-target-' + $(this).attr('id'));

		var is_show = $(this).is(':checked'); if ($(this).is('.switcher-reversed')) is_show = ! is_show;
		if (is_show) {
			$targets.slideDown();
		} else {
			$targets.slideUp().find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
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

	$('#billing_source_match_by').on('change', function(){

	});
	
	// autoselect input content on click
	$('input.selectable').live('click', function () {
		$(this).select();
	});
	
	// input tags with title
	$('input[title]').each(function () {
		var $this = $(this);
		$this.bind('focus', function () {
			if ('' == $(this).val() || $(this).val() == $(this).attr('title')) {
				$(this).removeClass('note').val('');
			}
		}).bind('blur', function () {
			if ('' == $(this).val() || $(this).val() == $(this).attr('title')) {
				$(this).addClass('note').val($(this).attr('title'));
			}
		}).blur();
		$this.parents('form').bind('submit', function () {
			if ($this.val() == $this.attr('title')) {
				$this.val('');
			}
		});
	});

	// datepicker
	$('input.datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		showOn: 'button',
		buttonText: '',
		constrainInput: false,
		showAnim: 'fadeIn',
		showOptions: 'fast'
	}).bind('change', function () {
		var selectedDate = $(this).val();
		var instance = $(this).data('datepicker');
		var date = null;
		if ('' != selectedDate) {
			try {
				date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
			} catch (e) {
				date = null;
			}
		}
		if ($(this).hasClass('range-from')) {
			$(this).parent().find('.datepicker.range-to').datepicker("option", "minDate", date);
		}
		if ($(this).hasClass('range-to')) {
			$(this).parent().find('.datepicker.range-from').datepicker("option", "maxDate", date);
		}
	}).change();
	$('.ui-datepicker').hide(); // fix: make sure datepicker doesn't break wordpress wpallimport-layout upon initialization 
	
	// no-enter-submit forms
	$('form.no-enter-submit').find('input,select,textarea').not('*[type="submit"]').keydown(function (e) {
		if (13 == e.keyCode) e.preventDefault();
	});

	$('a.collapser').each(function(){
		if ($(this).html() == "+"){
			$(this).parents('div:first').find('.collapser_content:first').hide();
		}
		else{
			$(this).parents('div:first').find('.collapser_content:first').fadeIn();
		}
		$(this).next('h3').css({'cursor':'pointer'});
	});

	$('a.collapser').click(function(){
		if ($(this).html() == "+") {
			$(this).html("-"); 
			$(this).parents('div:first').find('.collapser_content:first').fadeIn();
		} else { 
			$(this).html("+");
			$(this).parents('div:first').find('.collapser_content:first').hide();
		}		
	});

	$('a.collapser').each(function(){
		$(this).parents('.fieldset:first').find('h3:first').click(function(){			
			$(this).prev('a.collapser').click();
		});
	});

	$('.change_file').each(function(){

		var $wrap = $('.wrap');

		var formHeight = ($('.wpallimport-layout').height() < 730) ? 730 : $('.wpallimport-layout').height();

		$('#file_selector').ddslick({
			width: 600,	
			onSelected: function(selectedData){		

				if (selectedData.selectedData.value != ""){
		    		
		    		$('#file_selector').find('.dd-selected').css({'color':'#555'});
		    		
					var filename = selectedData.selectedData.value;
					
					$('.change_file').find('input[name=file]').val(filename);
					
		    	}
		    	else{
		    		$('#file_selector').find('.dd-selected').css({'color':'#cfceca'});
		    	}		    	
		    } 
		});
		
		var fixWrapHeight = false;

		$('#custom_type_selector').ddslick({
			width: 590,	
			onSlideDownOptions: function(o){		
				formHeight = ($('.wpallimport-layout').height() < 730) ? 730 : $('.wpallimport-layout').height();				
				$wrap.css({'height': formHeight + $('#custom_type_selector').find('.dd-options').height() + 'px'});				
			},
			onSlideUpOptions: function(o){						
				$wrap.css({'height': 'auto'});				
			},
			onSelected: function(selectedData){								
				if (fixWrapHeight)
					$wrap.css({'height': 'auto'});				
				else
					fixWrapHeight = true;

				$('.wpallimport-upgrade-notice').hide();

		        $('input[name=custom_type]').val(selectedData.selectedData.value);		        
		        $('#custom_type_selector').find('.dd-selected').css({'color':'#555'});

				var is_import_denied = $('.wpallimport-upgrade-notice[rel='+ selectedData.selectedData.value +']').length;

				if (is_import_denied){
					$('.wpallimport-upgrade-notice[rel='+ selectedData.selectedData.value +']').slideDown();
					$('.wpallimport-submit-buttons').hide();
				}
				else{
					$('.wpallimport-submit-buttons').slideDown();
				}


		    } 
		});

		$('.wpallimport-import-from').click(function(){
			$('.wpallimport-import-from').removeClass('selected').addClass('bind');			
			$(this).addClass('selected').removeClass('bind');			
			$('.change_file').find('.wpallimport-upload-type-container').hide();
			$('.change_file').find('.wpallimport-file-upload-result').attr('rel', $(this).attr('rel'));
			$('.change_file').find('.wpallimport-upload-type-container[rel=' + $(this).attr('rel') + ']').show();
			$('.change_file').find('#wpallimport-url-upload-status').html('');
			$('.change_file').find('input[name=new_type]').val( $(this).attr('rel').replace('_type', '') );			
			//$('.first-step-errors').hide();

			if ($(this).attr('rel') == 'upload_type'){
				$('input[type=file]').click();
			}			
		});
		$('.wpallimport-import-from.selected').click();

	});

	$('input[name=url]').change(function(){
		
	}).keyup(function (e) {
		if ($(this).val() != ''){
			$('.wpallimport-url-icon').addClass('focus');
			$(this).addClass('focus');				
		}
		else{
			$('.wpallimport-url-icon').removeClass('focus');
			$(this).removeClass('focus');				
		}
	}).focus(function(){
		if ($(this).val() == 'Enter a web address to download the file from...')
			$(this).val('');			
	}).blur(function(){
		if($(this).val() == '')
			$(this).val('Enter a web address to download the file from...');			
	});

	$('#taxonomy_to_import').ddslick({
		width: 300,
		onSelected: function(selectedData){
			if (selectedData.selectedData.value != ""){
				$('#taxonomy_to_import').find('.dd-selected').css({'color':'#555'});
				$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').show();
			}
			else{
				$('#taxonomy_to_import').find('.dd-selected').css({'color':'#cfceca'});
				$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();
			}
			$('input[name=taxonomy_type]').val(selectedData.selectedData.value);
		}
	});

	// enter-submit form on step 1
	$('.wpallimport-step-1').each(function(){
						
		var $wrap = $('.wrap');

		var formHeight = ($('.wpallimport-layout').height() < 730) ? 730 : $('.wpallimport-layout').height();

		$('.wpallimport-import-from').click(function(){
			
			var showImportType = false;
			
			switch ($(this).attr('rel')){
				case 'upload_type':
					if ($('input[name=filepath]').val() != '')
						showImportType = true; 
					break;	
				case 'url_type':
					if ($('input[name=url]').val() != '')
						showImportType = true; 
					break;
				case 'file_type':					
					if ($('input[name=file]').val() != '')
						showImportType = true; 
					break;
			}			
			
			$('.wpallimport-import-from').removeClass('selected').addClass('bind');
			$('.wpallimport-import-types').find('h2').slideUp();			
			$(this).addClass('selected').removeClass('bind');
			$('.wpallimport-choose-file').find('.wpallimport-upload-type-container').hide();
			$('.wpallimport-choose-file').find('.wpallimport-file-upload-result').attr('rel', $(this).attr('rel'));
			$('.wpallimport-choose-file').find('.wpallimport-upload-type-container[rel=' + $(this).attr('rel') + ']').show();
			$('.wpallimport-choose-file').find('#wpallimport-url-upload-status').html('');
			$('.wpallimport-choose-file').find('input[name=type]').val( $(this).attr('rel').replace('_type', '') );							

			if ($('.auto-generate-template').attr('rel') == $(this).attr('rel')){
				$('.auto-generate-template').css({'display':'inline-block'});
			}
			else
			{
				$('.auto-generate-template').hide();
			}			

			if ($(this).attr('rel') == 'upload_type'){
				$('input[type=file]').click();
			}			
			if ( ! showImportType){		
				$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideUp();
				$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();						
			}
			else{
				$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideDown();
				$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').show();						
			}

		});

		$('.wpallimport-import-from.selected').click();

		$('.wpallimport-download-from-url').click(function(){

			var $url = $('input[name=url]').val();
			var $template = $('input[name=template]').val();

			if ("" == $url) return;

			$('#wpallimport-url-upload-status').html('');
			$('.error.inline').remove();
			$('.first-step-errors').hide();

			var request = {
				action: 'upload_resource',		
				security: wp_all_import_security,	
				type: 'url',
				file: $url,
				template: $template
		    };		
		    $(this).attr({'disabled':'disabled'});   

		    var $indicator = $('.img_preloader').css({'visibility':'visible'});

		    $('.wpallimport-upload-type-container[rel=url_type]').find('.wpallimport-note').find('span').hide();

		    var ths = $(this);

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: request,
				success: function(response) {

					if (response.success){						
						
						if (response.post_type)
						{
							var index = $('#custom_type_selector li:has(input[value="'+ response.post_type +'"])').index();
							if (index != -1)
							{
								if (response.taxonomy_type){
									var tindex = $('#taxonomy_to_import li:has(input[value="'+ response.taxonomy_type +'"])').index();
									if (tindex != -1){
										$('#taxonomy_to_import').ddslick('select', {index: tindex });
									}
								}

								$('#custom_type_selector').ddslick('select', {index: index });
								$('.auto-generate-template').css({'display':'inline-block'}).attr('rel', 'url_type');
							}
							else
							{
								$('.auto-generate-template').hide();
							}
						}
						else
						{
							$('.auto-generate-template').hide();
						}

						if ( response.post_type && response.notice !== false ) {
							var $note = $('.wpallimport-upload-type-container[rel=url_type]').find('.wpallimport-note');
							$note.find('span').html("<div class='wpallimport-free-edition-notice'>" + response.notice + "</div>").show();							
							$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();
							$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideUp();
							$('input[name=filepath]').val('');
						}
						else {
							$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideDown(400, function(){
								$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').show();						
							});						
							$('.wpallimport-choose-file').find('input[name=downloaded]').val(window.JSON.stringify(response.upload_result));
						}

					}
					else 
					{
						if (response.is_valid)
						{
							$('.wpallimport-header').next('.clear').after(response.errors);	
						}
						else
						{
							$('.error-file-validation').find('h4').html(response.errors);
							$('.error-file-validation').show();
						}
					}
					$indicator.css({'visibility':'hidden'});
					ths.removeAttr('disabled');
				},
				error: function(response) {						
					$indicator.css({'visibility':'hidden'});
					ths.removeAttr('disabled');
					$('.wpallimport-header').next('.clear').after(response.responseText);
				},			
				dataType: "json"
			});
		});		
		
		var fixWrapHeight = false;

		$('#custom_type_selector').ddslick({
			width: 300,		
			onSlideDownOptions: function(o){	
				formHeight = ($('.wpallimport-layout').height() < 730) ? 730 : $('.wpallimport-layout').height();
				$wrap.css({'height': formHeight + $('#custom_type_selector').find('.dd-options').height() + 'px'});				
			},
			onSlideUpOptions: function(o){
				$wrap.css({'height' : 'auto'});				
			},		
			onSelected: function(selectedData){
				if (fixWrapHeight){
					$wrap.css({'height': 'auto'});
				}
				else{
					fixWrapHeight = true;
				}

				$('.wpallimport-upgrade-notice').hide();

		        $('input[name=custom_type]').val(selectedData.selectedData.value);

				var is_import_denied = $('.wpallimport-upgrade-notice[rel='+ selectedData.selectedData.value +']').length;

				if (is_import_denied){
					$('.wpallimport-upgrade-notice[rel='+ selectedData.selectedData.value +']').slideDown();
				}

				if ($('.wpallimport-upload-resource-step-two:visible').length && ! is_import_denied)
				{
					$('#custom_type_selector').find('.dd-selected').css({'color':'#555'});
					$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').show();
				}
				else
				{
					$('#custom_type_selector').find('.dd-selected').css({'color':'#555'});
					$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();
				}

				if (selectedData.selectedData.value == 'taxonomies'){
					$('.taxonomy_to_import_wrapper').slideDown();
					var selectedTaxonomy = $('input[name=taxonomy_type]').val();
					if (selectedTaxonomy == ''){
						$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();
					}
				}
				else{
					$('.taxonomy_to_import_wrapper').slideUp();
				}
		    } 
		});		

		$('#file_selector').ddslick({
			width: 600,	
			onSelected: function(selectedData){											

				$('.wpallimport-upload-type-container[rel=file_type]').find('.wpallimport-note').find('span').hide();

		    	if (selectedData.selectedData.value != ""){
		    		
		    		$('#file_selector').find('.dd-selected').css({'color':'#555'});
		    		
					var filename = selectedData.selectedData.value;
					$('#file_selector').find('.dd-option-value').each(function(){
						if (filename == $(this).val()) return false;						
					});

					$('.wpallimport-choose-file').find('input[name=file]').val(filename);	

					var request = {
						action: 'get_bundle_post_type',		
						security: wp_all_import_security,							
						file: filename						
				    };		
				    
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: request,
						success: function(response) {

							if (response.post_type)
							{
								var index = $('#custom_type_selector li:has(input[value="'+ response.post_type +'"])').index();
								if (index != -1)
								{
									if (response.taxonomy_type){
										var tindex = $('#taxonomy_to_import li:has(input[value="'+ response.taxonomy_type +'"])').index();
										if (tindex != -1){
											$('#taxonomy_to_import').ddslick('select', {index: tindex });
										}
									}
									$('#custom_type_selector').ddslick('select', {index: index });
									$('.auto-generate-template').css({'display':'inline-block'}).attr('rel', 'url_type');
								}
								else
								{
									$('.auto-generate-template').hide();
								}
							}

							if (response.post_type && response.notice !== false)
							{
								var $note = $('.wpallimport-upload-type-container[rel=file_type]').find('.wpallimport-note');
								$note.find('span').html("<div class='wpallimport-free-edition-notice'>" + response.notice + "</div>").show();								
								$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();
								$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideUp();								
							}
							else 
							{
								$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideDown(400, function(){
									$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').show();
								});
							}							
						},
						error: function(response) {													
							$('.wpallimport-header').next('.clear').after(response.responseText);
						},			
						dataType: "json"
					});					
														
		    	}
		    	else
		    	{
		    		if ($('.wpallimport-import-from.selected').attr('rel') == 'file_type')
		    		{
		    			$('.wpallimport-choose-file').find('input[name=file]').val('');	
			    		$('#file_selector').find('.dd-selected').css({'color':'#cfceca'});
			    		$('.wpallimport-choose-file').find('.wpallimport-upload-resource-step-two').slideUp();
						$('.wpallimport-choose-file').find('.wpallimport-submit-buttons').hide();	
		    		}		    		
		    	}
		    } 
		});

		$('.wpallimport-import-to').click(function(){			
			if ($(this).attr('rel') == 'new'){
				$('.wpallimport-new-records').show();
				$('.wpallimport-existing-records').hide();
			}
			else{
				$('.wpallimport-new-records').hide();
				$('.wpallimport-existing-records').show();
			}
			$('.wpallimport-import-to').removeClass('wpallimport-import-to-checked');
			$(this).addClass('wpallimport-import-to-checked');
			$('input[name=wizard_type]').val($(this).attr('rel'));
			$('.wpallimport-choose-import-direction').attr({'rel' : $(this).attr('rel')});
			$('.dd-container').fadeIn();
		});

		$('#custom_type_selector').hide();

		$('.wpallimport-import-to.wpallimport-import-to-checked').click();		

		$('a.auto-generate-template').click(function(){
			$('input[name^=auto_generate]').val('1');					
			$(this).parents('form:first').submit();
		});
		
	});
	//[/End Step 1]		
	
	// template form: auto submit when `load template` list value is picked
	$('form.wpallimport-template').find('select[name="load_template"]').live('change', function () {
		$(this).parents('form').submit();
	});		

	var serialize_ctx_mapping = function(){
		$('.custom_type[rel=tax_mapping]').each(function(){
	    	var values = new Array();
	    	$(this).find('.form-field').each(function(){
	    		if ($(this).find('.mapping_to').val() != "")
	    		{
	    			var skey = $(this).find('.mapping_from').val();
	    			if ('' != skey){	    				
	    				var obj = {};
	    				obj[skey] = $(this).find('.mapping_to').val();
	    				values.push(obj);
	    			}	    			
	    		}
	    	});	    		    	
	    	$(this).find('input[name^=tax_mapping]').val(window.JSON.stringify(values));
	    });
	};

	// [xml representation dynamic]
	$.fn.xml = function (opt) {
		if ( ! this.length) return this;
		
		var $self = this;
		var opt = opt || {};
		var action = {};
		if ('object' == typeof opt) {
			action = opt;
		} else {
			action[opt] = true;
		}
		action = $.extend({init: ! this.data('initialized')}, action);
		
		if (action.init) {
			this.data('initialized', true);
			// add expander
			this.find('.xml-expander').live('click', function () {
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
		}
		if (action.dragable) { // drag & drop
			var _w; var _dbl = 0;
			var $drag = $('__drag'); $drag.length || ($drag = $('<input type="text" id="__drag" readonly="readonly" />'));

			$drag.css({
				position: 'absolute',
				background: 'transparent',
				top: -50,
				left: 0,
				margin: 0,
				border: 'none',
				lineHeight: 1,
				opacity: 0,
				cursor: 'pointer',
				borderRadius: 0,
				zIndex:99
			}).appendTo(document.body).mousedown(function (e) {
				if (_dbl) return;
				var _x = e.pageX - $drag.offset().left;
				var _y = e.pageY - $drag.offset().top;
				if (_x < 4 || _y < 4 || $drag.width() - _x < 0 || $drag.height() - _y < 0) {
					return;
				}
				$drag.width($(document.body).width() - $drag.offset().left - 5).css('opacity', 1);
				$drag.select();
				_dbl = true; setTimeout(function () {_dbl = false;}, 400);
			}).mouseup(function () {
				$drag.css('opacity', 0).css('width', _w);
				$drag.blur();
			}).dblclick(function(){
				if (dblclickbuf.selected)
				{
					$('.xml-element[title*="/'+dblclickbuf.value.replace('{','').replace('}','')+'"]').removeClass('selected');

					if ($(this).val() == dblclickbuf.value)
					{
						dblclickbuf.value = '';
						dblclickbuf.selected = false;
					}
					else
					{
						dblclickbuf.selected = true;
						dblclickbuf.value = $(this).val();
						$('.xml-element[title*="/'+$(this).val().replace('{','').replace('}','')+'"]').addClass('selected');
					}
				}
				else
				{
					dblclickbuf.selected = true;
					dblclickbuf.value = $(this).val();
					$('.xml-element[title*="/'+$(this).val().replace('{','').replace('}','')+'"]').addClass('selected');
				}
			});
			
			$('#title, #content, .widefat, input[name^=custom_name], textarea[name^=custom_value], input[name^=featured_image], input[name^=unique_key]').bind('focus', insertxpath );
			
			$(document).mousemove(function () {
				if (parseInt($drag.css('opacity')) != 0) {
					setTimeout(function () {
						$drag.css('opacity', 0);
					}, 50);
					setTimeout(function () {
						$drag.css('width', _w);
					}, 500);
				}
			});

			this.find('.xml-tag.opening > .xml-tag-name, .xml-attr-name, .csv-tag.opening > .csv-tag-name, .ui-menu-item').each(function () {
				var $this = $(this);
				var xpath = '.';
				if ($this.is('.xml-attr-name'))
					xpath = '{' + ($this.parents('.xml-element:first').attr('title').replace(/^\/[^\/]+\/?/, '') || '.') + '/@' + $this.html().trim() + '}';
				else if($this.is('.ui-menu-item'))
					xpath = '{' + ($this.attr('title').replace(/^\/[^\/]+\/?/, '') || '.') + '}';
				else
					xpath = '{' + ($this.parent().parent().attr('title').replace(/^\/[^\/]+\/?/, '') || '.') + '}';

				$this.mouseover(function (e) {
					$drag.val(xpath).offset({left: $this.offset().left - 2, top: $this.offset().top - 2}).width(_w = $this.width()).height($this.height() + 4);
				});
			}).eq(0).mouseover();
		}
		return this;
	};

	// template form: preview button
	$('form.wpallimport-template').each(function () {
		var $form = $(this);
				
		var $detected_cf = new Array();		

		$form.find('.preview, .preview_images, .preview_taxonomies, .preview_prices').click(function () {
			var $preview_type = $(this).attr('rel');
			var $options_slug = $(this).parent('div').find('.wp_all_import_section_slug').val();

			if ($preview_type == 'preview_taxonomies') serialize_ctx_mapping();

			var $URL = 'admin.php?page=pmxi-admin-import&action=' + $preview_type + ((typeof import_id != "undefined") ? '&id=' + import_id : '');
			var $tagURL = 'admin.php?page=pmxi-admin-import&action=tag' + ((typeof import_id != "undefined") ? '&id=' + import_id : '');
			
			if ($options_slug != undefined) $URL += '&slug=' + $options_slug;

			$('.wpallimport-overlay').show();
			
			var $ths = $(this);	

			$(this).pointer({
	            content: '<div class="wpallimport-preview-preload wpallimport-pointer-' + $preview_type + '"></div>',
	            position: {
	                edge: 'right',
	                align: 'center'                
	            },
	            pointerWidth: ($preview_type == 'preview_images') ? 800 : 715,
	            close: function() {
	                $.post( ajaxurl, {
	                    pointer: 'pksn1',
	                    action: 'dismiss-wp-pointer'
	                });
	                $('.wpallimport-overlay').hide();
	            }
	        }).pointer('open');

	        var $pointer = $('.wpallimport-pointer-' + $preview_type).parents('.wp-pointer').first();	        	        

	        var $leftOffset = ($(window).width() - (($preview_type == 'preview_images') ? 800 : 715))/2;

	        $pointer.css({'position':'fixed', 'top' : '15%', 'left' : $leftOffset + 'px'});

			if (typeof tinyMCE != 'undefined') tinyMCE.triggerSave(false, false);

			$.post($URL, $form.serialize(), function (response) {
							
				$ths.pointer({'content' : response.html});

				$pointer.css({'position':'fixed', 'top' : '15%', 'left' : $leftOffset + 'px'});
				
				var $preview = $('.wpallimport-' + $preview_type);		

				$preview.parent('.wp-pointer-content').removeClass('wp-pointer-content').addClass('wpallimport-pointer-content');

				var $tag = $('.tag');
				var tagno = parseInt($tag.find('input[name="tagno"]').val());
				$preview.find('.navigation a').unbind('click').die('click').live('click', function () {
					tagno += '#prev' == $(this).attr('href') ? -1 : 1;
					$tag.addClass('loading').css('opacity', 0.7);
					$preview.addClass('loading').css('opacity', 0.7);
					$.post($tagURL, {tagno: tagno, import_action: import_action, security: wp_all_import_security}, function (data) {
						var $indicator = $('<span />').insertBefore($tag);
						$tag.replaceWith(data.html);
						fix_tag_position();
						$indicator.next().tag().prevObject.remove();
						if ($('#variations_xpath').length){						
							$('#variations_xpath').data('checkedValue', '').change();
						}						
					    $preview.find('input[name="tagno"]').die();
					    $preview.find('.navigation a').die('click');
					    $form.find('.' + $preview_type).click();
					    
					}, 'json');
					return false;
				});
				$preview.find('input[name="tagno"]').unbind('click').die('click').live('change', function () {
					tagno = (parseInt($(this).val()) > parseInt($preview.find('.pmxi_count').html())) ? $preview.find('.pmxi_count').html() : ( (parseInt($(this).val())) ? $(this).val() : 1 );									
					$tag.addClass('loading').css('opacity', 0.7);
					$.post($tagURL, {tagno: tagno, security: wp_all_import_security}, function (data) {
						var $indicator = $('<span />').insertBefore($tag);
						$tag.replaceWith(data.html);
						fix_tag_position();
						$indicator.next().tag().prevObject.remove();
						if ($('#variations_xpath').length){						
							$('#variations_xpath').data('checkedValue', '').change();
						}						
					    $preview.find('input[name="tagno"]').die();
					    $preview.find('.navigation a').die('click');
					    $form.find('.' + $preview_type).click();
					}, 'json');
					return false;
				});							

			}, 'json');
			return false;
		});		

		$form.find('.set_encoding').live('click', function(e){
			e.preventDefault();
			$form.find('a[rel="preview"].preview').click();
		});

		$form.find('input[name$=download_images]').each(function(){
			if ($(this).is(':checked') && $(this).val() == 'gallery' )
			{
				$(this).parents('.wpallimport-collapsed-content:first').find('.advanced_options_files').find('p:first').show();
				$(this).parents('.wpallimport-collapsed-content:first').find('.advanced_options_files').find('input').attr({'disabled':'disabled'});
			}
		});

		$form.find('input[name$=download_images]').click(function(){
			if ($(this).is(':checked') && $(this).val() == 'gallery' )
			{
				$(this).parents('.wpallimport-collapsed-content:first').find('.advanced_options_files').find('p:first').show();
				$(this).parents('.wpallimport-collapsed-content:first').find('.advanced_options_files').find('input').attr({'disabled':'disabled'});
			}
			else
			{
				$(this).parents('.wpallimport-collapsed-content:first').find('.advanced_options_files').find('p:first').hide();
				$(this).parents('.wpallimport-collapsed-content:first').find('.advanced_options_files').find('input').removeAttr('disabled');
			}
		});

		// Auto-detect custom fields
		$form.find('.auto_detect_cf').click(function(){
			
			var parent = $(this).parents('.wpallimport-collapsed-content:first');
			var request = {
				action:'auto_detect_cf',			
				fields: $('#existing_meta_keys').val().split(','),
				post_type: $('input[name=custom_type]').val(),
				security: wp_all_import_security
		    };		
		    $(this).attr({'disabled':'disabled'});   

		    var $indicator = $('<span class="img_preloader" style="top:0;"/>').insertBefore($(this)).show();

		    var ths = $(this);

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: request,
				success: function(response) {

					parent.find('input[name^=custom_name]:visible').each(function(){
						if ("" == $(this).val()) $(this).parents('tr').first().remove();
					});
					
					$detected_cf = response.result;

					var $added_fields_count = 0;
					if (response.result.length){
						for (var i = 0; i < response.result.length; i++){
							var allow_add = true;
							parent.find('input[name^=custom_name]:visible').each(function(){
								if (response.result[i].key == "" || response.result[i].key == $(this).val()) {
									allow_add = false;
									return false;
								}
							});
							// if this field doesn't present in custom fields section then put it there
							if ( allow_add ){								
								parent.find('a.add-new-custom').click();
								var fieldParent = parent.find('.form-field:visible').last();
								fieldParent.find('input[name^=custom_name]:visible').last().val(response.result[i].key);
								fieldParent.find('textarea[name^=custom_value]:visible').last().val(response.result[i].val);
								if (response.result[i].is_serialized) fieldParent.find('.set_serialize').last().parent().click();
								$added_fields_count++;
							}							
						}												
					}

					$indicator.remove();

					$('.cf_detected').html(response.msg);
					$('.cf_welcome').hide();
					$('.cf_detect_result').fadeIn();

					ths.removeAttr('disabled');
				},
				error: function(request) {				
					$indicator.remove();
					ths.removeAttr('disabled');
				},			
				dataType: "json"
			});
		});		

		// Clear all detected custom fields
		$form.find('.clear_detected_cf').click(function(){
			var parent = $(this).parents('.wpallimport-collapsed-content:first');
			if ($detected_cf.length){				
				for (var i = 0; i < $detected_cf.length; i++){
					parent.find('input[name^=custom_name]:visible').each(function(){
						if ($detected_cf[i].key == $(this).val()) $(this).parents('tr').first().remove();
					});
				}
			}					
			if ( ! parent.find('input[name^=custom_name]:visible').length){
				parent.find('a.add-new-custom').click();
			}			
			$('.cf_detected').html('');
			$('.cf_detect_result').hide();			
			$('.cf_welcome').fadeIn();			
			$detected_cf = new Array();
		});			

		// toggle custom field as serialized/default
		$form.find('.wpallimport-cf-menu li').live('click', function(){
			var $triggerEvent = $(this).find('a');
			if ($triggerEvent.hasClass('set_serialize')){
				var parent = $triggerEvent.parents('.form-field:first');
				var parent_custom_format = parent.find('input[name^=custom_format]:first');
				var parent_custom_value = parent.find('textarea[name^=custom_value]:first');
				if (parseInt(parent_custom_format.val())){
					parent_custom_format.val(0);
					parent.find('.specify_cf:first').hide();
					parent_custom_value.fadeIn();
					$triggerEvent.parent().removeClass('active');
				}
				else{
					parent_custom_format.val(1);
					parent_custom_value.hide();
					parent.find('.specify_cf:first').fadeIn();				
					$triggerEvent.parent().addClass('active');				
				}
			}			
		});

		// [Serialized custom fields]

			// Save serialized custom field format
			$('.save_sf').live('click', function(){
				var $source = $(this).parents('table:first');
				var $destination = $('div#' + $source.attr('rel'));
				$destination.find('table:first').html('');
				$source.find('input').each(function(i, e){
					$(this).attr("value", $(this).val());
				});			
				$destination.find('table:first').html($source.html());						
				$destination.parents('td:first').find('.pmxi_cf_pointer').pointer('destroy');	
				$('.wpallimport-overlay').hide();		
			});

			// Auto-detect serialized custom fields
			$('.auto_detect_sf').live('click', function(){
				var $source = $(this).parents('table:first');
				var $destination = $('div#' + $source.attr('rel'));
				var $parentDestination = $destination.parents('tr:first');
				var $cf_name = $parentDestination.find('input[name^=custom_name]:first').val();
				
				if ($cf_name != ''){
					var request = {
						action:'auto_detect_sf',
						security: wp_all_import_security,
						post_type: $('input[name=custom_type]').val(),
						name: $cf_name
				    };		
				    $(this).attr({'disabled':'disabled'});   

					var $indicator = $('<span class="img_preloader" style="position: absolute; top:0;"/>').insertBefore($(this)).show();
					var ths = $(this);

					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: request,
						success: function(response) {													
							
							if (response.result.length){

								$destination.find('tr.form-field').each(function(){
									if ( ! $(this).hasClass('template') ) $(this).remove();
								});

								for (var i = 0; i < response.result.length; i++){
									
									$destination.find('a.add-new-key').click();
									$destination.find('tr.form-field').not('.template').last().css({"opacity": 1}).find('input.serialized_key').attr("value", response.result[i].key);
									$destination.find('tr.form-field').not('.template').last().css({"opacity": 1}).find('input.serialized_value').attr("value", response.result[i].val);									
																
								}			

								$destination.parents('td:first').find('.pmxi_cf_pointer').pointer('destroy');										
								$destination.parents('td:first').find('.pmxi_cf_pointer').click();
							}
							else{
							
								var $notice = $('<p style="color:red; position: absolute; top: -10px; padding:0; margin:0;">No fields detected.</p>').insertBefore(ths).show();
								setTimeout(function() {
							
									$notice.slideUp().remove();

								}, 2500);
							}

							$indicator.remove();
							ths.removeAttr('disabled');
						},
						error: function(request) {				
							$indicator.remove();
							ths.removeAttr('disabled');
						},			
						dataType: "json"
					});
				}
			});

		// [/ Serialized custom fields]

		// Save mapping rules for custom field
		$('.save_mr').live('click', function(){
			var $source = $(this).parents('table:first');
			var $destination = $('div#' + $source.attr('rel'));
			var $is_active = false;
			$destination.find('table:first').html('');
			$source.find('input').each(function(i, e){
				$(this).attr("value", $(this).val());
				if ($(this).val() != "")
					$is_active = true;					
			});		
			var $box = $destination.parents('td.action:first');
			if ( $is_active ){				
				$box.find('.set_mapping').parent().addClass('active');
			}				
			else{
				$box.find('.set_mapping').parent().removeClass('active');
			}
			$destination.find('table:first').html($source.html());									
			$destination.parents('td:first').find('.pmxi_cf_mapping').pointer('destroy');	
			$('.wpallimport-overlay').hide();		
		});		

		// Taxonnomies
		$form.find('#show_hidden_ctx').click(function(){
			$(this).parents('table:first').find('tr.private_ctx').toggle();
		});		

		// Test & Preview images
		$('.test_images').live('click', function(){

			var ths = $(this);		

			$(this).attr({'disabled':'disabled'});

			$('.img_preloader').show();
			$('.img_success').html('').hide();
			$('.img_failed').remove();

			var imgs = new Array();

			$('.images_list').find('li').each(function(){
				imgs.push($(this).attr('rel'));
			});
				
			var request = {
				action: 'test_images',		
				security: wp_all_import_security,	
				download: ths.attr('rel'),
				imgs:imgs				
		    };		    

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: request,
				success: function(response) {
					$('.img_preloader').hide();
					if ( parseInt(response.success_images)) 
						$('.img_success').html(response.success_msg).show();					

					if (response.failed_msgs.length){
						for (var i = 0; i < response.failed_msgs.length; i++){
							$('.test_progress').append('<div class="img_failed">' + response.failed_msgs[i] + '</div>');
						}
						$('.img_failed').show();
					}				
					ths.removeAttr('disabled');
				},
				error: function(request) {				
					$('.img_failed').html(request.textStatus).show();
					ths.removeAttr('disabled');
				},			
				dataType: "json"
			});
			
		});	

		/* Merge Main XML file with sub file by provided fields */
		$form.find('.parse').live('click', function(){

			var submit = true;

			if ("" == $form.find('input[name=nested_url]').val()){
				$form.find('input[name=nested_url]').css({'background':'red'});
				submit = false;
			}
			
			if (submit){

				var ths = $(this);		
				var $fileURL = $form.find('input[name=nested_url]').val();

				$(this).attr({'disabled':'disabled'});

				var request = {
					action:'nested_merge',
					security: wp_all_import_security,	
					filePath: $fileURL,					
			    };		    
			    
			    var $indicator = $('<span class="img_preloader" style="top:10px;"/>').insertBefore($(this)).show();

			    $form.find('.nested_msgs').html('');

				$.ajax({
					type: 'POST',
					url: ajaxurl + ((typeof import_id != "undefined") ? '?id=' + import_id : ''),
					data: request,
					success: function(response) {
						$indicator.remove();
						
						if (response.success)
						{
							//$form.find('.nested_cancel').click();

							$form.find('.nested_files ul').append('<li rel="' + $form.find('.nested_files ul').find('li').length + '">' + $fileURL + ' <a href="javascript:void(0);" class="unmerge">remove</a></li>');
							$form.find('input[name=nested_files]').val(window.JSON.stringify(response.nested_files));

							var $tag = $('.tag');
							var $tagno = parseInt($tag.find('input[name="tagno"]').val());
							var $tagURL = 'admin.php?page=pmxi-admin-import&action=tag' + ((typeof import_id != "undefined") ? '&id=' + import_id : '');
							
							$tag.addClass('loading').css('opacity', 0.7);
							$.post($tagURL, {tagno: $tagno, import_action: import_action}, function (data) {
								var $indicator = $('<span />').insertBefore($tag);
								$tag.replaceWith(data.html);
								fix_tag_position();
								$indicator.next().tag().prevObject.remove();
								if ($('#variations_xpath').length){						
									$('#variations_xpath').data('checkedValue', '').change();
								}												   
							}, 'json');
							return false;							

						}
						else
						{
							$form.find('.nested_msgs').html(response.msg);
						}
						ths.removeAttr('disabled');
					},
					error: function(request) {		
						$indicator.remove();
						ths.removeAttr('disabled');
					},			
					dataType: "json"
				});
			}
		});

		/* Unmerge nested XMl/CSV files */
		$form.find('.unmerge').live('click', function(){

			var ths = $(this);		

			$(this).attr({'disabled':'disabled'});

			var $indicator = $('<span class="img_preloader" style="top:5px;"/>').insertBefore($(this)).show();
						
			var request = {
				action:'unmerge_file',
				source: ths.parents('li:first').attr('rel'),	
				security: wp_all_import_security			
		    };		    

		    $form.find('.nested_msgs').html('');

			$.ajax({
				type: 'POST',
				url: ajaxurl + ((typeof import_id != "undefined") ? '?id=' + import_id : ''),
				data: request,
				success: function(response) {
					$indicator.remove();
					if (response.success){		

						ths.parents('li:first').remove();
						$form.find('input[name=nested_files]').val(window.JSON.stringify(response.nested_files));

						var $tag = $('.tag');
						var $tagno = parseInt($tag.find('input[name="tagno"]').val());
						var $tagURL = 'admin.php?page=pmxi-admin-import&action=tag' + ((typeof import_id != "undefined") ? '&id=' + import_id : '');
						
						$tag.addClass('loading').css('opacity', 0.7);
						$.post($tagURL, {tagno: $tagno, import_action: import_action}, function (data) {
							var $indicator = $('<span />').insertBefore($tag);
							$tag.replaceWith(data.html);
							fix_tag_position();
							$indicator.next().tag().prevObject.remove();
							if ($('#variations_xpath').length){						
								$('#variations_xpath').data('checkedValue', '').change();
							}										   
						}, 'json');
						return false;						
					}
					else{
						$form.find('.msgs').html(response.errors);
						$form.find('.pmxi_counter').remove();
					}
					ths.removeAttr('disabled');
				},
				error: function(request) {		
					$indicator.remove();						
					ths.removeAttr('disabled');
				},			
				dataType: "json"
			});
		});

		$form.find('input[name=nested_url]').focus(function(){
			$(this).css({'background':'#fff'});
		});		

		var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
		var is_safari = navigator.userAgent.indexOf("Safari") > -1;
		var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;

		if ((is_safari && !is_chrome) || is_firefox){
			$form.find('textarea[name$=download_featured_image]').attr("placeholder", "http://example.com/images/image-1.jpg");
			$form.find('textarea[name$=featured_image]').attr("placeholder", "image-1.jpg");
			$form.find('textarea[name$=gallery_featured_image]').attr("placeholder", "image-1.jpg");
		}
		else{
			$form.find('textarea[name$=download_featured_image]').attr("placeholder", "http://example.com/images/image-1.jpg\nhttp://example.com/images/image-2.jpg\n...");
			$form.find('textarea[name$=featured_image]').attr("placeholder", "image-1.jpg\nimage-2.jpg\n...");
			$form.find('textarea[name$=gallery_featured_image]').attr("placeholder", "image-1.jpg\nimage-2.jpg\n...");
		}

		$form.find('input[name$=download_images]:checked').each(function(){			
			if ($(this).val() == 'gallery')
			{
				$(this).parents('table:first').find('.search_through_the_media_library').slideUp();
			}
			else
			{
				$(this).parents('table:first').find('.search_through_the_media_library').slideDown();
			}
		});

		$form.find('input[name$=download_images]').click(function(){
			if ($(this).is(':checked') && $(this).val() == 'gallery')
			{
				$(this).parents('table:first').find('.search_through_the_media_library').slideUp();
			}
			else
			{
				$(this).parents('table:first').find('.search_through_the_media_library').slideDown();
			}
		});

		$form.find('.wpallimport-dismiss-cf-welcome').click(function(){
			$('.cf_welcome, .cf_detect_result').slideUp();
		});

	});	

	// options form: highlight options of selected post type
	$('form.wpallimport-template input[name="type"]').click(function() {
		var $container = $(this).parents('.post-type-container');
		$('.post-type-container').not($container).removeClass('selected').find('.post-type-options').hide();
		$container.addClass('selected').find('.post-type-options').show();
	}).filter(':checked').click();
	// options form: add / remove custom params
	$('.form-table a.action[href="#add"]').live('click', function () {
		var $template = $(this).parents('table').first().find('tr.template');
		$template.clone(true).insertBefore($template).css('display', 'none').removeClass('template').fadeIn();
		return false;
	});	

	// options form: auto submit when `load options` checkbox is checked
	$('input[name="load_options"]').click(function () {		
		if ($(this).is(':checked')) $(this).parents('form').submit();
	});
	// options form: auto submit when `reset options` checkbox is checked
	$('form.wpallimport-template').find('input[name="reset_options"]').click(function () {		
		if ($(this).is(':checked')) $(this).parents('form').submit();
	});
	$('.form-table .action.remove a, .cf-form-table .action.remove a, .tax-form-table .action.remove a').live('click', function () {
		var $box = $(this).parents('tbody').first();
		$(this).parents('tr').first().remove();
		if ( ! $box.find('tr.form-field:visible').length ){
			$box.find('.add-new-entry').click();			
		}
		return false;
	});
	
	var dblclickbuf = {
		'selected':false,
		'value':''
	};

	function insertxpath(){
		if ($(this).hasClass('wpallimport-placeholder')){ 
			$(this).val('');
			$(this).removeClass('wpallimport-placeholder');
		}
		if (dblclickbuf.selected)
		{
			$(this).val($(this).val() + dblclickbuf.value);
			$('.xml-element[title*="/'+dblclickbuf.value.replace('{','').replace('}','')+'"]').removeClass('selected');
			dblclickbuf.value = '';
			dblclickbuf.selected = false;					
		}
	}	

	var go_to_template = false;

	// selection logic
	$('form.wpallimport-choose-elements').each(function () {
		var $form = $(this);
		$form.find('.wpallimport-xml').xml();
		var $input = $form.find('input[name="xpath"]');		
		var $next_element = $form.find('#next_element');
		var $prev_element = $form.find('#prev_element');		
		var $goto_element =  $form.find('#goto_element');
		var $get_default_xpath = $form.find('#get_default_xpath');	
		var $root_element = $form.find('#root_element');		
		var $submit = $form.find('input[type="submit"]');
		var $csv_delimiter = $form.find('input[name=delimiter]');
		var $apply_delimiter = $form.find('input[name=apply_delimiter]');

		var $xml = $('.wpallimport-xml');
		
		var xpathChanged = function () {
			if ($input.val() == $input.data('checkedValue')) return;			
			
			$form.addClass('loading');			
			$form.find('.xml-element.selected').removeClass('selected'); // clear current selection
			// request server to return elements which correspond to xpath entered
			$input.attr('readonly', true).unbind('change', xpathChanged).data('checkedValue', $input.val());
			$xml.css({'visibility':'hidden'});
			$('.wpallimport-set-csv-delimiter').hide();					

			$xml.parents('fieldset:first').addClass('preload');			
			go_to_template = false;
			$submit.hide();
			var evaluate = function(){				
				$.post('admin.php?page=pmxi-admin-import&action=evaluate', {xpath: $input.val(), show_element: $goto_element.val(), root_element:$root_element.val(), is_csv: $apply_delimiter.length, delimiter:$csv_delimiter.val(), security: wp_all_import_security}, function (response) {					
					if (response.result){
						$('.wpallimport-elements-preloader').hide();
						$('.ajax-console').html(response.html);
						$input.attr('readonly', false).change(function(){$goto_element.val(1); xpathChanged();});
						$form.removeClass('loading');											
						
						$xml.parents('fieldset:first').removeClass('preload');
						$('.wpallimport-set-csv-delimiter').show();
						go_to_template = true;		
						$('#pmxi_xml_element').find('option').each(function(){
							if ($(this).val() != "") $(this).remove();
						});
						$('#pmxi_xml_element').append(response.render_element);
						$('.wpallimport-root-element').html(response.root_element);
						$('.wpallimport-elements-count-info').html(response.count);
						$('.wp_all_import_warning').hide(); 
						if (response.count){ 
							$submit.show();							
							if ($('.xml-element.lvl-1').length < 1) $('.wp_all_import_warning').css({'display':'inline-block'});
						}
						else
							$submit.hide();
					}
				}, "json").fail(function() { 					
					
					$xml.parents('fieldset:first').removeClass('preload');
					$form.removeClass('loading');
					$('.ajax-console').html('<div class="error inline"><p>No matching elements found for XPath expression specified.</p></div>');

				});		
			}
			evaluate();
		};
		$next_element.live('click', function(){
			var matches_count = ($('.matches_count').length) ? parseInt($('.matches_count').html()) : 0;
			var show_element = Math.min((parseInt($goto_element.val()) + 1), matches_count);
			$goto_element.val(show_element).html( show_element ); $input.data('checkedValue', ''); xpathChanged();
		});
		$prev_element.live('click', function(){
			var show_element = Math.max((parseInt($goto_element.val()) - 1), 1);
			$goto_element.val(show_element).html( show_element ); $input.data('checkedValue', ''); xpathChanged();
		});
		$goto_element.change(function(){
			var matches_count = ($('.matches_count').length) ? parseInt($('.matches_count').html()) : 0;
			var show_element = Math.max(Math.min(parseInt($goto_element.val()), matches_count), 1);
			$goto_element.val(show_element); $input.data('checkedValue', ''); xpathChanged();			
		});

		var reset_filters = function(){
			$('#apply_filters').hide();
			$('.filtering_rules').empty();	
			$('#filtering_rules').find('p').show();	
		}

		$get_default_xpath.click(function(){
			$input.val($(this).attr('rel'));
			if ($input.val() == $input.data('checkedValue')) return;									
			reset_filters();
			$root_element.val($(this).attr('root')); $goto_element.val(1);  xpathChanged();
		});
		$('.wpallimport-change-root-element').click(function(){
			$input.val('/' + $(this).attr('rel'));
			if ($input.val() == $input.data('checkedValue')) return;	
			$('.wpallimport-change-root-element').removeClass('selected');
			$(this).addClass('selected');
			reset_filters();
			$('.root_element').html($(this).attr('rel'));
			$root_element.val($(this).attr('rel')); $goto_element.val(1); xpathChanged();
		});
		$input.change(function(){$goto_element.val(1); xpathChanged();}).change();
		$input.keyup(function (e) {
			if (13 == e.keyCode) $(this).change();
		});

		$apply_delimiter.click(function(){			
			if ( ! $input.attr('readonly') ){										
				$('input[name="xpath"]').data('checkedValue','');
				xpathChanged();
			}
		});

		/* Advanced Filtering */

		$('.filtering_rules').pmxi_nestedSortable({
	        handle: 'div',
	        items: 'li',
	        toleranceElement: '> div',
	        update: function () {	        
	        	$('.filtering_rules').find('.condition').show();
	        	$('.filtering_rules').find('.condition:last').hide();     								
		    }
	    });

	    $('#pmxi_add_rule').click(function(){    	

	    	var $el = $('#pmxi_xml_element');
	    	var $rule = $('#pmxi_rule');
	    	var $val = $('#pmxi_value');

	    	if ($el.val() == "" || $rule.val() == "") return;    	

	    	if ($rule.val() != 'is_empty' && $rule.val() != "is_not_empty" && $val.val() == "") return;

	    	var relunumber = $('.filtering_rules').find('li').length + "_" + $.now();

	    	var html = '<li><div class="drag-element">';
	    		html += '<input type="hidden" value="'+ $el.val() +'" class="pmxi_xml_element"/>';
	    		html += '<input type="hidden" value="'+ $rule.val() +'" class="pmxi_rule"/>';
	    		html += '<input type="hidden" value="'+ $val.val() +'" class="pmxi_value"/>';
	    		html += '<span class="rule_element">' + $el.val() + '</span> <span class="rule_as_is">' + $rule.find('option:selected').html() + '</span> <span class="rule_condition_value">"' + $val.val() +'"</span>';
	    		html += '<span class="condition"> <label for="rule_and_'+relunumber+'">AND</label><input id="rule_and_'+relunumber+'" type="radio" value="and" name="rule_'+relunumber+'" checked="checked" class="rule_condition"/><label for="rule_or_'+relunumber+'">OR</label><input id="rule_or_'+relunumber+'" type="radio" value="or" name="rule_'+relunumber+'" class="rule_condition"/> </span>';
	    		html += '</div><a href="javascript:void(0);" class="icon-item remove-ico"></a></li>';

	    	$('#wpallimport-filters, #apply_filters').show();
	    	$('#filtering_rules').find('p').hide();    	

	    	$('.filtering_rules').append(html);

	    	$('.filtering_rules').find('.condition').show();
	        $('.filtering_rules').find('.condition:last').hide();

	    	$el.prop('selectedIndex',0);	
	    	$rule.prop('selectedIndex',0);	
	    	$val.val('');	    	
	    	$('#pmxi_value').show();	    	

	    });

		$('.filtering_rules').find('.remove-ico').live('click', function(){
			$(this).parents('li:first').remove();
			if (!$('.filtering_rules').find('li').length){
				$('#apply_filters').hide();
	    		$('#filtering_rules').find('p').show();			
			}
		});

		$('#pmxi_rule').change(function(){
			if ($(this).val() == 'is_empty' || $(this).val() == 'is_not_empty')
				$('#pmxi_value').hide();
			else
				$('#pmxi_value').show();
		});

		var filter = '[';

		var xpath_builder = function(rules_box, lvl){						

			var rules = rules_box.children('li');	

			var root_element = $('#root_element').val();		

			if (lvl && rules.length > 1) filter += ' (';

			rules.each(function(){
				
				var node = $(this).children('.drag-element').find('.pmxi_xml_element').val();
				var condition = $(this).children('.drag-element').find('.pmxi_rule').val();
				var value = $(this).children('.drag-element').find('.pmxi_value').val();

				var clause = ($(this).children('.drag-element').find('.condition').is(':visible')) ? $(this).children('.drag-element').find('input.rule_condition:checked').val() : false;				

				var is_attr = false;

				if (node.indexOf('@') != -1){
					is_attr = true;
					node_name = node.split('@')[0];
					attr_name = node.split('@')[1];
				}

				if (is_attr)
					filter += (node_name == root_element) ? '' : node_name.replace(/->/g, '/');
				else
					filter += node.replace(/->/g, '/');

				if (is_attr) filter += '@' + attr_name;
 
				switch (condition){
					case 'equals':
						filter += ' = "%s"';
						break;
					case 'not_equals':
						filter += ' != "%s"';
						break;
					case 'greater':
						filter += ' > %s';
						break;
					case 'equals_or_greater':
						filter += ' >= %s';
						break;
					case 'less':
						filter += ' < %s';
						break;
					case 'equals_or_less':
						filter += ' <= %s';
						break;
					case 'contains':
						filter += '[contains(.,"%s")]';
						break;
					case 'not_contains':
						filter += '[not(contains(.,"%s"))]';
						break;
					case 'is_empty':
						filter += '[not(string())]';
						break;
					case 'is_not_empty':
						filter += '[string()]';
						break;
				}

				filter = filter.replace('%s', value);

				//if (is_attr) filter += ']';

				if (clause) filter += ' ' + clause + ' ';				

				if ($(this).children('ol').length){
					$(this).children('ol').each(function(){						
						if ($(this).children('li').length) xpath_builder($(this), 1);
					});				
				}
			});
	
			if (lvl && rules.length > 1) filter += ') ';	

		}	

		$('#apply_filters').click(function(){

			var xpath = $('input[name=xpath]').val();

			filter = '[';
			xpath_builder($('.filtering_rules'), 0);
			filter += ']';

			$input.val( $input.val().split('[')[0] + filter);

			$input.data('checkedValue', ''); xpathChanged();

		});
	});
	
	$('form.wpallimport-choose-elements').find('input[type="submit"]').click(function(e){
		e.preventDefault();
		if (go_to_template) $(this).parents('form:first').submit();
	});

	var init_context_menu = function(){
		if ( $(".tag").length ){
			
			$('.xml-element').each(function(){
				var $ths = $(this);
				if ($(this).children('.xml-element-xpaths').find('li').length){
					$(this).children('.xml-content').css({'cursor':'context-menu'}).attr({'title' : 'Right click to view alternate XPaths'});
					$(this).contextmenu({
					    delegate: ".xml-content",
					    menu: "#" + $(this).children('.xml-element-xpaths').find('ul').attr('id'),
					    select: function(event, ui) {
					        //alert("select " + ui.cmd + " on " + ui.target.text());
					    }
					});				
				}
			});			
		}
	}

	// tag preview
	$.fn.tag = function () {
		this.each(function () {

			init_context_menu();

			var $tag = $(this);
			$tag.xml('dragable');
			var tagno = parseInt($tag.find('input[name="tagno"]').val());
			var $tagURL = 'admin.php?page=pmxi-admin-import&action=tag' + ((typeof import_id != "undefined") ? '&id=' + import_id : '');

			$tag.find('.navigation a').live('click', function () {
				tagno += '#prev' == $(this).attr('href') ? -1 : 1;				
				$tag.addClass('loading').css('opacity', 0.7);
				$.post($tagURL, {tagno: tagno, import_action: import_action, security: wp_all_import_security}, function (data) {
					var $indicator = $('<span />').insertBefore($tag);
					$tag.replaceWith(data.html);
					fix_tag_position();
					$indicator.next().tag().prevObject.remove();
					if ($('#variations_xpath').length){						
						$('#variations_xpath').data('checkedValue', '').change();
					}			

				}, 'json');
				return false;
			});
			$tag.find('input[name="tagno"]').live('change', function () {
				tagno = (parseInt($(this).val()) > parseInt($tag.find('.pmxi_count').html())) ? $tag.find('.pmxi_count').html() : ( (parseInt($(this).val())) ? $(this).val() : 1 );				
				$(this).val(tagno);
				$tag.addClass('loading').css('opacity', 0.7);
				$.post($tagURL, {tagno: tagno, import_action: import_action, security: wp_all_import_security}, function (data) {
					var $indicator = $('<span />').insertBefore($tag);
					$tag.replaceWith(data.html);
					fix_tag_position();
					$indicator.next().tag().prevObject.remove();
					if ($('#variations_xpath').length){						
						$('#variations_xpath').data('checkedValue', '').change();
					}					
				}, 'json');
				return false;
			});
		});			
		return this;
	};
	$('.tag').tag();
	// [/xml representation dynamic]
	
	$('.wpallimport-custom-fields').each(function(){		
		$(this).find('.wp_all_import_autocomplete').each(function(){
			if ( ! $(this).parents('tr:first').hasClass('template')){				
				$(this).autocomplete({
					source: eval('__META_KEYS'),
					minLength: 0
				}).click(function () {
					$(this).autocomplete('search', '');
					$(this).attr('rel', '');
				});			
			}
		});
		
		$(this).find('textarea[name^=custom_value]').live('click', function(){
			var $ths = $(this);
			var $parent = $ths.parents('tr:first');
			var $custom_name = $parent.find('input[name^=custom_name]');
			var $key = $custom_name.val();
			
			if ($key != "" && $custom_name.attr('rel') != "done"){
				$ths.addClass('loading');
				$.post('admin.php?page=pmxi-admin-settings&action=meta_values', {key: $key, security: wp_all_import_security}, function (data) {
					if (data.meta_values.length){
						$ths.autocomplete({
							source: eval(data.meta_values),
							minLength: 0
						}).click(function () {
							$(this).autocomplete('search', '');
						}).click();						
					}
					$custom_name.attr('rel','done');
					$ths.removeClass('loading');
				}, 'json');
			}
		});		

		$('.wpallimport-cf-options').live('click', function(){
			$(this).next('.wpallimport-cf-menu').slideToggle();
		});
	});

	/* Categories hierarchy */

	$('ol.sortable').pmxi_nestedSortable({
        handle: 'div',
        items: 'li.dragging',        
        toleranceElement: '> div',        
        update: function () {	        
	       $(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).pmxi_nestedSortable('toArray', {startDepthCount: 0})));
	       if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
	    }
    });

    $('.drag-element').find('input').live('blur', function(){    	
    	$(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).parents('.ui-sortable:first').pmxi_nestedSortable('toArray', {startDepthCount: 0})));
    	if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
    });

    $('.drag-element').find('input').live('change', function(){    	
    	$(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).parents('.ui-sortable:first').pmxi_nestedSortable('toArray', {startDepthCount: 0})));
    	if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
    });

    $('.drag-element').find('input').live('hover', function(){},function(){    	
    	$(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).parents('.ui-sortable:first').pmxi_nestedSortable('toArray', {startDepthCount: 0})));
    	if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
    });

    $('.taxonomy_auto_nested').live('click', function(){
    	$(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).parents('td:first').find('.ui-sortable:first').pmxi_nestedSortable('toArray', {startDepthCount: 0})));
    	if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
    });

	$('.sortable').find('.remove-ico').live('click', function(){
	 	
	 	var parent_td = $(this).parents('td:first');
	 
		$(this).parents('li:first').remove(); 			
		parent_td.find('ol.sortable:first').find('li').each(function(i, e){
			$(this).attr({'id':'item_'+ (i+1)});
		});
		parent_td.find('.hierarhy-output').val(window.JSON.stringify(parent_td.find('.ui-sortable:first').pmxi_nestedSortable('toArray', {startDepthCount: 0})));	         	
	 	if (parent_td.find('input:first').val() == '') parent_td.find('.hierarhy-output').val('');	 			 	
	});

	$('.tax_hierarchical_logic').find('.remove-ico').live('click', function(){
		$(this).parents('li:first').remove();
	});

	$('.add-new-ico').live('click', function(){				
		var count = $(this).parents('tr:first').find('ol.sortable').find('li.dragging').length + 1;

		var $template = $(this).parents('td:first').find('ol').children('li.template');		
		
		$clone = $template.clone(true);
		$clone.addClass('dragging').attr({'id': $clone.attr('id') + '_' + count}).find('input[type=checkbox][name^=categories_mapping]').each(function(){			
			$(this).attr({'id': $(this).attr('id') + '_' + count});
			$(this).next('label').attr({'for':$(this).next('label').attr('for') + '_' + count});
			$(this).next('label').next('div').addClass($(this).next('label').next('div').attr('rel') + '_' + count);
		}); 		
		$clone.insertBefore($template).css('display', 'none').removeClass('template').fadeIn().find('input.switcher').change();		
		
		var sortable = $(this).parents('.ui-sortable:first');
		if (sortable.length){
			$(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify(sortable.pmxi_nestedSortable('toArray', {startDepthCount: 0})));
	    	if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
	    }
		$('.widefat').bind('focus', insertxpath );

	});	

	$('.add-new-cat').click(function(){
		var $template = $(this).parents('td:first').find('ul.tax_hierarchical_logic').children('li.template');		
		var $number = $(this).parents('td:first').find('ul.tax_hierarchical_logic').children('li').length - 1;
		var $cloneName = $template.find('input.assign_term').attr('name').replace('NUMBER', $number);		
		$clone = $template.clone(true);
		$clone.find('input[name^=tax_hierarchical_assing]').attr('name', $cloneName);
		$clone.insertBefore($template).css('display', 'none').removeClass('template').fadeIn().find('input.switcher').change();	
	});

	$('ol.sortable').each(function(){
		if ( ! $(this).children('li').not('.template').length ) $(this).next('.add-new-ico').click();
	});
	
	$('form.wpallimport-template').find('input[type=submit]').click(function(e){

		e.preventDefault();
		
		$('.hierarhy-output').each(function(){
			var sortable = $(this).parents('td:first').find('.ui-sortable:first');
			if (sortable.length){
				$(this).val(window.JSON.stringify(sortable.pmxi_nestedSortable('toArray', {startDepthCount: 0})));			
				if ($(this).parents('td:first').find('input:first').val() == '') $(this).val('');
			}
		});
		if ($(this).attr('name') == 'btn_save_only') $('.save_only').val('1');

		$('input[name^=in_variations], input[name^=is_visible], input[name^=is_taxonomy], input[name^=create_taxonomy_in_not_exists], input[name^=variable_create_taxonomy_in_not_exists], input[name^=variable_in_variations], input[name^=variable_is_visible], input[name^=variable_is_taxonomy]').each(function(){
	    	if ( ! $(this).is(':checked') && ! $(this).parents('.form-field:first').hasClass('template')){	    		
	    		$(this).val('0').attr('checked','checked');
	    	}
	    });

	    $('.custom_type[rel=serialized]').each(function(){
	    	var values = new Array();
	    	$(this).find('.form-field').each(function(){	    		
    			var skey = $(this).find('.serialized_key').val();
    			if ('' == skey){
    				values.push($(this).find('.serialized_value').val());
    			}
    			else
    			{
    				var obj = {};
    				obj[skey] = $(this).find('.serialized_value').val();
    				values.push(obj);
    			}	    			
	    	});
	    	$(this).find('input[name^=serialized_values]').val(window.JSON.stringify(values));
	    });

	    $('.custom_type[rel=mapping]').each(function(){
	    	var values = new Array();
	    	$(this).find('.form-field').each(function(){
	    		if ($(this).find('.mapping_to').val() != "")
	    		{
	    			var skey = $(this).find('.mapping_from').val();
	    			if ('' != skey){	    				
	    				var obj = {};
	    				obj[skey] = $(this).find('.mapping_to').val();
	    				values.push(obj);
	    			}	    			
	    			
	    		}
	    	});
	    	$(this).find('input[name^=custom_mapping_rules], .pmre_mapping_rules').val(window.JSON.stringify(values));
	    });

	    serialize_ctx_mapping();

		$(this).parents('form:first').submit();

	});	

	$('.wpallimport-step-4').each(function(){
		$(this).find('input[name^=custom_duplicate_name]').autocomplete({
			source: eval('__META_KEYS'),
			minLength: 0
		}).click(function () {
			$(this).autocomplete('search', '');
			$(this).attr('rel', '');
		});

	});

	$('.add-new-custom').click(function(){		
		var $template = $(this).parents('table').first().children('tbody').children('tr.template');
		$number = $(this).parents('table').first().children('tbody').children('tr').length - 2;
		$clone = $template.clone(true);
		
		$clone.find('div[rel^=serialized]').attr({'id':'serialized_' + $number}).find('table:first').attr({'rel':'serialized_' + $number});
		$clone.find('div[rel^=mapping]').attr({'id':'cf_mapping_' + $number}).find('table:first').attr({'rel':'cf_mapping_' + $number});
		$clone.find('a.specify_cf').attr({'rel':'serialized_' + $number})
		$clone.find('a.pmxi_cf_mapping').attr({'rel':'cf_mapping_' + $number})
		$clone.find('.wpallimport-cf-menu').attr({'id':'wpallimport-cf-menu-' + $number}).menu();
		$clone.find('input[name^=custom_name]').autocomplete({
			source: eval('__META_KEYS'),
			minLength: 0
		}).click(function () {
			$(this).autocomplete('search', '');
			$(this).attr('rel', '');
		});
		$clone.insertBefore($template).css('display', 'none').removeClass('template').fadeIn();

		return false;
	});

	$('.add-new-key').live('click', function(){
		var $template = $(this).parents('table').first().find('tr.template');
		$template.clone(true).insertBefore($template).css('display', 'none').removeClass('template').fadeIn();
	});

	/* END Categories hierarchy */		

	$('form.options').each(function(){
		var $form = $(this);
		var $uniqueKey = $form.find('input[name=unique_key]');
		var $tmpUniqueKey = $form.find('input[name=tmp_unique_key]');
		$form.find('.wpallimport-auto-detect-unique-key').click(function(){
			$uniqueKey.val($tmpUniqueKey.val());
		});
	});

	$('form.edit').each(function(){
		var $form = $(this);		
		$form.find('.wpallimport-change-unique-key').click(function(){
			var $ths = $(this);
			$( "#dialog-confirm" ).dialog({
				resizable: false,
				height: 290,
				width: 550,
				modal: true,
				draggable: false,
				buttons: {
					"Continue": function() {						
						$( this ).dialog( "close" );
						$ths.hide();
						$('input[name=unique_key]').removeAttr('disabled').focus();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		});
		var $uniqueKey = $form.find('input[name=unique_key]');
		var $tmpUniqueKey = $form.find('input[name=tmp_unique_key]');
		$form.find('.wpallimport-auto-detect-unique-key').click(function(){
			$uniqueKey.val($tmpUniqueKey.val());
		});
	});
	
	// chunk files upload
	if ($('#plupload-ui').length)
	{
		$('#plupload-ui').show();
		$('#html-upload-ui').hide();	

		wplupload = $('#select-files').wplupload({
			runtimes : 'gears,browserplus,html5,flash,silverlight,html4',
			url : 'admin.php?page=pmxi-admin-settings&action=upload&_wpnonce=' + wp_all_import_security,
			container: 'plupload-ui',
			browse_button : 'select-files',
			file_data_name : 'async-upload',
			multipart: true,
			max_file_size: '1000mb',
			chunk_size: '1mb',			
			drop_element: 'plupload-ui',
			multipart_params : {}				
		});
	}	

	/* END plupload scripts */

	$('#view_log').live('click', function(){
		$('#import_finished').css({'visibility':'hidden'});
		$('#logwrapper').slideToggle(100, function(){
			$('#import_finished').css({'visibility':'visible'});
		});
	});			     

	// Select Encoding
	$('#import_encoding').live('change', function(){
		if ($(this).val() == 'new'){
			$('#select_encoding').hide();
			$('#add_encoding').show();
		}
	});

	$('#cancel_new_encoding').live('click', function(){
		$('#add_encoding').hide();
		$('#select_encoding').show();		
		$('#new_encoding').val('');
		$('#import_encoding').prop('selectedIndex', 0);	
	});

	$('#add_new_encoding').live('click', function(){
		var new_encoding = $('#new_encoding').val();
		if ("" != new_encoding){
			$('#import_encoding').prepend('<option value="'+new_encoding+'">' + new_encoding + '</option>');
			$('#cancel_new_encoding').click();
			$('#import_encoding').prop('selectedIndex',0);	
		}
		else alert('Please enter encoding.');
	});

	$('input[name=keep_custom_fields]').click(function(){
		$(this).parents('.input:first').find('.keep_except').slideToggle();
	});		
	
    $('.pmxi_choosen').each(function(){    	
    	$(this).find(".choosen_input").select2({
    		tags: $(this).find('.choosen_values').html().split(','),
    		width: '80%',    		
    	});
    });    

    if (typeof wpPointerL10n != "undefined") wpPointerL10n.dismiss = 'Close';

	$('.show_hints').live('click', function(){	
		var $ths = $(this);	
		$('.wpallimport-overlay').show();

		$(this).pointer({
            content: $('#' + $ths.attr('rel')).html(),
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
                $('.wpallimport-overlay').hide();
            }
        }).pointer('open');
	});		

	// Serialized Custom Field Dialog
	$('.pmxi_cf_pointer').live('click', function(){	
		var $ths = $(this);	
		//$('.wpallimport-overlay').show();

		if ($ths.parents('.form-field:first').find('input[name^=custom_name]').val() == "") {
			$('#' + $ths.attr('rel')).find('.auto_detect_sf').hide();
		}
		else{
			$('#' + $ths.attr('rel')).find('.auto_detect_sf').show();
		}

		$(this).pointer({
            content: $('#' + $ths.attr('rel')).html(),
            position: {
                edge: 'top',
                align: 'center'                
            },
            pointerWidth: 450,
            close: function() {
                $.post( ajaxurl, {
                    pointer: 'pksn1',
                    action: 'dismiss-wp-pointer'
                });
                //$('.wpallimport-overlay').hide();
            }
        }).pointer('open');
	});	

	// Custom Fields Mapping Dialog
	$('.wpallimport-cf-menu li').live('click', function(){
		var $triggerEvent = $(this).find('a');
		if ($triggerEvent.hasClass('pmxi_cf_mapping')){

			//$('.wpallimport-overlay').show();
			var $ths = $triggerEvent;	
			$triggerEvent.pointer({
	            content: $('#' + $ths.attr('rel')).html(),
	            position: {
	                edge: 'right',
	                align: 'center'                
	            },
	            pointerWidth: 450,
	            close: function() {
	                $.post( ajaxurl, {
	                    pointer: 'pksn1',
	                    action: 'dismiss-wp-pointer'
	                });
	                //$('.wpallimport-overlay').hide();
	            }
	        }).pointer('open');					
		}
	});	

	$('.wpallimport-overlay').click(function(){
		$('.wp-pointer').hide();
		$(this).hide();        
	});	

	if ($('#wp_all_import_code').length){
		var editor = CodeMirror.fromTextArea(document.getElementById("wp_all_import_code"), {
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
		var currentImportFunctions = editor.getValue();
		editor.on('change',function(cMirror){
			if ( currentImportFunctions != cMirror.getValue()){
				window.onbeforeunload = function () {
					return 'WARNING:\nFunctions are not saved, leaving the page will reset changes in Function editor.';
				};
			}
			else{
				window.onbeforeunload = false;
			}
		});
	}

    $('.wp_all_import_save_functions').click(function(){
    	var request = {
			action: 'save_import_functions',	
			data: editor.getValue(),				
			security: wp_all_import_security				
	    };    
	    $('.wp_all_import_functions_preloader').show();
	    $('.wp_all_import_saving_status').html('');
		$.ajax({
			type: 'POST',
			url: ajaxurl + ((typeof export_id != "undefined") ? '?id=' + import_id : ''),
			data: request,
			success: function(response) {						
				$('.wp_all_import_functions_preloader').hide();
				
				if (response.result)
				{
					window.onbeforeunload = false;
					$('.wp_all_import_saving_status').css({'color':'green'});
					setTimeout(function() {
						$('.wp_all_import_saving_status').html('').fadeOut();
					}, 3000);
				}
				else
				{
					$('.wp_all_import_saving_status').css({'color':'red'});
				}

				$('.wp_all_import_saving_status').html(response.msg).show();
									
			},
			error: function( jqXHR, textStatus ) {						
				$('.wp_all_import_functions_preloader').hide();
			},
			dataType: "json"
		});
    }); 

    $('.wp_all_import_ajax_deletion').click(function(e){
    	e.preventDefault();
    	var $ths = $(this);
    	$(this).attr('disabled', 'disabled');
	    var iteration = 1;
		var request = {
			action: 'delete_import',
			data: $(this).parents('form:first').serialize(),
			security: wp_all_import_security,
			iteration: iteration
		};
		var deleteImport = function(){
			request.iteration = iteration;
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: request,
				success: function(response) {

					iteration++;

					$ths.parents('form:first').find('.wp_all_import_deletion_log').html('<p>' + response.msg + '</p>');

					if (response.result){
						$('.wp_all_import_functions_preloader').hide();
						window.location.href = response.redirect;
					}
					else
					{
						deleteImport();
					}
				},
				error: function( jqXHR, textStatus ) {
					$ths.removeAttr('disabled');
					$('.wp_all_import_functions_preloader').hide();
				},
				dataType: "json"
			});
		}
		$('.wp_all_import_functions_preloader').show();
		deleteImport();
    });

	$('.wpallimport-collapsed').each(function(){

		if ( ! $(this).hasClass('closed')) $(this).find('.wpallimport-collapsed-content:first').slideDown();

	});

	$('.wpallimport-collapsed').find('.wpallimport-collapsed-header').click(function(){
		var $parent = $(this).parents('.wpallimport-collapsed:first');
		if ($parent.hasClass('closed')){			
			$parent.removeClass('closed');
			$parent.find('.wpallimport-collapsed-content:first').slideDown(400, function(){
				if ($('#wp_all_import_code').length) editor.setCursor(1);
			});
		}
		else{
			$parent.addClass('closed');			
			$parent.find('.wpallimport-collapsed-content:first').slideUp();
		}
	});	

	$('#is_delete_posts').change(function(){
		if ($(this).is(':checked')){
			$('.wpallimport-delete-posts-warning').show();
		}
		else{
			$('.wpallimport-delete-posts-warning').hide();
		}
	});

	$('.wpallimport-dependent-options').each(function(){
		$(this).prev('div.input').find('input[type=text]:last, textarea:last').addClass('wpallimport-top-radius');
	});

	$('.wpallimport-delete-and-edit, .download_import_template, .download_import_bundle').click(function(e){
		e.preventDefault();
    	window.location.href = $(this).attr('rel');
    });        

    $('.wpallimport-wpae-notify-read-more').click(function(e){
    	e.preventDefault();
    	
    	var request = {
			action: 'dismiss_notifications',		
			security: wp_all_import_security,	
			addon: $(this).parent('div:first').attr('rel')
	    };		

	    var ths = $(this);

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: request,
			success: function(response) {
								
			},			
			dataType: "json"
		});
		
		$(this).parent('div:first').slideUp();

    	window.open($(this).attr('href'), '_blank');
    });

    // [ Delete Import]
    var wpai_are_sure_to_delete_import = function()
    {
    	if ( ! $('.delete-single-import').length ) return;

    	$('.delete-single-import').removeAttr('disabled');

    	if ( $('#is_delete_import').is(':checked') || $('#is_delete_posts').is(':checked'))
    	{
    		$('.wp-all-import-sure-to-delete').show();
    	}
    	if ( ! $('#is_delete_import').is(':checked') && ! $('#is_delete_posts').is(':checked'))
    	{
    		$('.wp-all-import-sure-to-delete').hide();    		
    		$('.delete-single-import').attr('disabled', 'disabled');
    	}
    	if ( $('#is_delete_import').is(':checked') && $('#is_delete_posts').is(':checked'))
    	{
    		$('.sure_delete_posts_and_import').show();
    	}
    	if ($('#is_delete_import').is(':checked'))
    	{
    		$('.sure_delete_import').show();
    	}
    	else
    	{
    		$('.sure_delete_import').hide();
    		$('.sure_delete_posts_and_import').hide();
    	}
    	if ($('#is_delete_posts').is(':checked'))
    	{
    		$('.sure_delete_posts').show();
    	}
    	else
    	{
    		$('.sure_delete_posts').hide();
    		$('.sure_delete_posts_and_import').hide();
    	}
    }

    wpai_are_sure_to_delete_import();

    $('#is_delete_import, #is_delete_posts').click(function(){
    	wpai_are_sure_to_delete_import();
    });    
    // [\ Delete Import]    

    if ($('.switcher-target-update_choosen_data').length)
    {    	
    	var $re_import_options = $('.switcher-target-update_choosen_data');
    	var $toggle_re_import_options = $('.wpallimport-trigger-options');
    	
    	if ($re_import_options.find('input[type=checkbox]').length == $re_import_options.find('input[type=checkbox]:checked').length)
    	{
    		var $newtitle = $toggle_re_import_options.attr('rel');    		
    		$toggle_re_import_options.attr('rel', $toggle_re_import_options.html());
    		$toggle_re_import_options.html($newtitle);
    		$toggle_re_import_options.removeClass('wpallimport-select-all');
    	}    	
    }

    $('.wpallimport-trigger-options').click(function(){
    	var $parent = $(this).parents('.switcher-target-update_choosen_data:first');
    	var $newtitle = $(this).attr('rel');
    	if ( $(this).hasClass('wpallimport-select-all') ) 
    	{
    		$parent.find('input[type=checkbox]').removeAttr('checked').click();
    		$(this).removeClass('wpallimport-select-all');    		    		
    	}
    	else
    	{    		
    		$parent.find('input[type=checkbox]:checked').click();
    		$(this).addClass('wpallimport-select-all');
    	}    	
    	$(this).attr('rel', $(this).html());
    	$(this).html($newtitle);
    });

	$('.post_excerpt_edit_mode').click(function(){
		var $current = $(this).attr('rel');
		if ($current == 'expand'){
			$(this).find('.collapse_mode_title').show();
			$(this).find('.expand_mode_title').hide();
			$('.post_excerpt_edit_mode_collapse').hide();
			$('.post_excerpt_edit_mode_expand').show();
			$(this).attr('rel', 'collapse');
			$('input.post_excerpt_input').attr('name', '');
			$('textarea.post_excerpt_input').attr('name', 'post_excerpt');
			$('textarea.post_excerpt_input').val($('input.post_excerpt_input').val());
		}
		else{
			$(this).find('.collapse_mode_title').hide();
			$(this).find('.expand_mode_title').show();
			$('.post_excerpt_edit_mode_collapse').show();
			$('.post_excerpt_edit_mode_expand').hide();
			$(this).attr('rel', 'expand');
			$('textarea.post_excerpt_input').attr('name', '');
			$('input.post_excerpt_input').attr('name', 'post_excerpt');
			$('input.post_excerpt_input').val($('textarea.post_excerpt_input').val());
		}
	});

	var fix_tag_position = function(){
		if ($('.wpallimport-layout').length && $('.tag').length){
	    	var offset = $('.wpallimport-layout').offset();
	        if ($(document).scrollTop() > offset.top){
	            $('.tag').css({'top':'50px'});        
	            $('.wpallimport-xml').css({'max-height': ($(window).height() - 147) + 'px' });
	        }
	        else{
	        	$('.tag').css({'top':'127px'});
	        	$('.wpallimport-xml').css({'max-height': ($(window).height() - 220) + 'px' });
	        }
	    }
	}

	fix_tag_position();	

	$(document).scroll(function() {    	    				
    	fix_tag_position();
	});   

});})(jQuery);
