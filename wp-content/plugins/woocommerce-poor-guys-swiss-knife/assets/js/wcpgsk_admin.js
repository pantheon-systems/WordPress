jQuery(function($) {

	// Uploading files
	var file_frame;
	jQuery('.upload_image_button').on('click', function( event ){

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data( 'uploader_title' ),
			button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			jQuery('#varproductuploadimageid').val(attachment.id);
			jQuery('#varproductuploadimagesrc').attr('src', attachment.url);
		});

		// Finally, open the modal
		file_frame.open();
	});

	jQuery('.upload_file_button').on('click', function( event ){

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data( 'uploader_title' ),
			button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here			
			if (jQuery('#varproductfiles').val())
				jQuery('#varproductfiles').val(jQuery('#varproductfiles').val() + '\n' + attachment.url);
			else jQuery('#varproductfiles').val(attachment.url);
		});

		// Finally, open the modal
		file_frame.open();
	});
	
	var methods = {
		_unserializeFormSetValue : function( el, _value, override ) {

			if(jQuery(el).length > 1) {
				// Assume multiple elements of the same name are radio buttons
				jQuery.each(el, function(i) {
					var match = (jQuery.isArray(_value)
						? (jQuery.inArray(this.value, _value) != -1)
						: (this.value == _value)
					);

					this.checked = match;
				});
			} else {
				// Assume, if only a single element, it is not a radio button
				if(jQuery(el).attr("type") == "checkbox") {
					jQuery(el).attr("checked", true);
				}
				//fix the select problem
				else if(jQuery(el).prop('tagName').toUpperCase() === "SELECT") {
					jQuery(el).children('option[value="' + _value + '"]').attr('selected', 'selected');
				} else {
					if(override) {
						jQuery(el).val(_value);
					} else {
						if (!jQuery(el).val()) {
							jQuery(el).val(_value);
						}
					}
				}
			}
		},

		_pushValue : function( obj, key, val ) {
			if (null == obj[key])
				obj[key] = val;
			else if (obj[key].push)
				obj[key].push(val);
			else
				obj[key] = [obj[key], val];
		}
	};

	// takes a GET-serialized string, e.g. first=5&second=3&a=b and sets input tags (e.g. input name="first") to their values (e.g. 5)
	jQuery.fn.unserializeForm = function( _values, _options ) {
		// Set up defaults
		var settings = jQuery.extend( {
		  'callback' : undefined,
		  'override-values' : false
		}, _options);

		return this.each(function() {
		// this small bit of unserializing borrowed from James Campbell's "JQuery Unserialize v1.0"
			_values = _values.split("&");
			_callback = settings["callback"];
			_override_values = settings["override-values"];

			if(_callback && typeof(_callback) !== "function") {
				_callback = undefined; // whatever they gave us wasn't a function, act as though it wasn't given
			}

			var serialized_values = new Array();
			jQuery.each(_values, function() {
				var properties = this.split("=");

				if((typeof properties[0] != 'undefined') && (typeof properties[1] != 'undefined')) {
					methods._pushValue(serialized_values, properties[0].replace(/\+/g, " "), decodeURI(properties[1].replace(/\+/g, " ")));
				}
			});

			// _values is now a proper array with values[hash_index] = associated_value
			_values = serialized_values;

			// Start with all checkboxes and radios unchecked, since an unchecked box will not show up in the serialized form
			jQuery(this).find(":checked").attr("checked", false);

			// Iterate through each saved element and set the corresponding element
			for(var key in _values) {
				var el = jQuery(this).add("input,select,textarea").find("[name=\"" + unescape(key) + "\"]");

				if(typeof(_values[key]) != "string") {
				// select tags using 'multiple' will be arrays here (reports "object")
				// We cannot do the simple unescape() because it will flatten the array.
				// Instead, unescape each item individually
					var _value = new Array();
					jQuery.each(_values[key], function(i, v) {
						_value.push(unescape(v));
					})
				} else {
					var _value = unescape(_values[key]);
				}

				if(_callback == undefined) {
				// No callback specified - assume DOM elements exist
					methods._unserializeFormSetValue(el, _value, _override_values);
				} else {
					// Callback specified - don't assume DOM elements already exist
					var result = _callback.call(this, unescape(key), _value, el);

					// If they return true, it means they handled it. If not, we will handle it.
					// Returning false then allows for DOM building without setting values.
					if(result == false) {
						var el = jQuery(this).add("input,select,textarea").find("[name=\"" + unescape(key) + "\"]");
						// Try and find the element again as it may have just been created by the callback
						methods._unserializeFormSetValue(el, _value, _override_values);
					}
				}
			}
		})
	}
});

jQuery(document).ready(function() {
	
	jQuery('.select_removes').each(function() {		
		var dest = jQuery(this).attr('for');
		jQuery(this).click(function() {
			var c = this.checked;
			jQuery('.' + dest).prop('checked',c);
		});
	});
	
	jQuery('.select_required').each(function() {		
		var dest = jQuery(this).attr('for');
		jQuery(this).click(function() {
			var c = this.checked;
			jQuery('.' + dest).prop('checked',c);
		});
	});
	

	
	function trimme() {
		switch (arguments.length) {
			case 2:
				throw new Error("Invalid argument." + 
								" jQuery.trim must be called with either a string" +
								" or haystack, replacer, replacement");
				break;
			case 3:
				var re = new RegExp("^\\" +arguments[1] + "|\\" + arguments[1] + "$"); 
				return arguments[0].replace(re, arguments[2]);
			default:
				return jQuery.trim(arguments);// old_trim.call(jQuery, arguments);
		}
	}

	
	// sorter
	function attach_sorter(sortobj){
		var fixer = function(e, ui) {
			ui.children().each(function() {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		};

		sortobj.children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				setRowNumbers(sortobj);
			},
			handle: 'td.wcpgsk_order_col',
			helper: fixer,
			placeholder: 'ui-state-highlight'
		});
	}


	function setRowNumbers(fortable) {
		fortable.children('tbody').children('tr.wcpgsk_order_row').each(function(i) {
			jQuery(this).children('td.wcpgsk_order_col').children('input.wcpgsk_order_input').val(i+1);
			jQuery(this).children('td.wcpgsk_order_col').children('span.wcpgsk_order_span').html(i+1);
		});
	}

	function activateCustomFields(fortable) {
		fortable.children('tbody').children('tr.wcpgsk_order_row').each(function(i) {
			jQuery(this).children('td.wcpgsk_functions_col').children('button.wcpgsk_configure_field').each(function(i){
				var new_id = jQuery(this).attr('for');
				var new_type = jQuery(this).attr('type');
				var for_table = jQuery(this).attr('table');
				var name = jQuery(this).attr('name');
				jQuery(this)
					.button()
					.click(function() {
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'new_id', new_id );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'new_type', new_type );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'for_table', for_table );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'context', name );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'open' );
						return false;
					});	
				return false;
			});
			
		});
	}

	jQuery('.wcpgsk_fieldtable').each(function() {		
		var sortobj = jQuery('#' + jQuery(this).attr('id'));
		var row_count = sortobj.children('tbody').children('tr.wcpgsk_order_row').length;

		// Attach sorter
		attach_sorter(sortobj);
		activateCustomFields(sortobj);
	});
	// Add field
	jQuery('.add_custom_field').on('click', function(){
		var table = jQuery('#' + jQuery(this).attr('for')),			
			for_table = jQuery(this).attr('for'),
			new_placeholder = jQuery(this).attr('placeholder'),
			row_count = table.children('tbody').children('tr.wcpgsk_order_row').length,
			new_row = table.children('tbody').children('tr.wcpgsk_add_field_row').clone(false),
			/*fix hyphen problem*/
			new_id = jQuery('#' + jQuery(this).attr('for') + '_fieldid').val().toLowerCase().replace(/[^a-z0-9_\s]/gi, '').replace(/[\s]/g, '_'),			
			new_type = jQuery('#' + jQuery(this).attr('for') + '_type').val();// Create and add the new field row
			
		if (new_id.length > 0) {
			new_id = new_placeholder + '_' + new_id;
			var foundid = false;
			table.children('tbody').children('tr.wcpgsk_order_row').each(function() {
				jQuery(this).find('[name$="[label_' + new_id + ']"]').each(function(){
					foundid = true;
				});
			});
			if (!foundid) {
				new_row.attr( 'class', 'wcpgsk_order_row' );
				// Update names
				var count = parseInt(row_count) + 1;
				new_row.find('[convert]').each(function(){
					 
					var convert = jQuery(this).attr('convert').replace('_nn2id]','_' + new_id + ']').replace('[nn2id]','[' + new_id + ']');
					jQuery(this).attr('name', convert);
					jQuery(this).removeAttr('convert');
					
					
				});
				//fix new field problem
				var newfield = new_row.find('[name$="[label_' + new_id + ']"]').val();
				var nfcnt = 1;
				table.children('tbody').children('tr.wcpgsk_order_row').each(function() {
					jQuery(this).find('[name*="[label_"]').each(function(){
						if ( jQuery(this).val().indexOf(newfield) == 0 ) {
							nfcnt++;
						}
					});
				});
				if ( nfcnt > 1 ) {
					new_row.find('[name$="[placeholder_' + new_id + ']"]').val(newfield + nfcnt);
					new_row.find('[name$="[label_' + new_id + ']"]').val(newfield + nfcnt);					
				}
				//fix end
				new_row.find('[name$="[ident_' + new_id + ']"]').html(new_id);
				new_row.find('[name$="[button_' + new_id + ']"]').html(new_type);
				new_row.find('[name$="[type_' + new_id + ']"]').val(new_type);
				new_row.find('[name$="[custom_' + new_id + ']"]').val(new_id);
				//new_row.find('[name$="[category_' + new_id + ']"]').val(new_id);
				new_row.find('[name$="[button_' + new_id + ']"]')
					.button()
					.click(function() {
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'new_id', new_id );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'new_type', new_type );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'for_table', for_table );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'option', 'context', jQuery(this).attr('name') );							
						jQuery( '#wcpgsk_dialog_form_container' ).dialog( 'open' );
						return false;
					});	
				
				// Add row
				table.children('tbody').append(new_row); 
				setRowNumbers(table);

			}
			else {
				jQuery('#wcpgsk_error_dialog').dialog( 'option', 'errormsg', 'Your new id exists!<br />Please facilitate a unique identifier for your new field.' );							
				jQuery('#wcpgsk_error_dialog').dialog( 'open' );
			}
			
		}
		else {
			jQuery('#wcpgsk_error_dialog').dialog( 'option', 'errormsg', 'Your new id is not valid after processing!<br />Only letters from a to z, numbers, spaces and underscores are allowed.' );							
			jQuery('#wcpgsk_error_dialog').dialog( 'open' );
		}
		return false;	
	});

	
	jQuery( "#wcpgsk_error_dialog" ).dialog({
		autoOpen: false,
		height: 140,
		width: 320,
		modal: true,
		draggable: false,
		focus:  function() {
			jQuery(this).find('#wcpgsk_error_message').html(jQuery(this).dialog('option', 'errormsg'));
		}
	});
	
	jQuery( "#wcpgsk_dialog_form_container" ).dialog({
		autoOpen: false,
		closeOnEscape: true,
		height: 540,
		width: 640,
		modal: true,
		draggable: false,
		open: function() {
			
		},
		focus:  function() {
			jQuery(this).html('');//(new_html);
			var new_type = jQuery(this).dialog( 'option', 'new_type' );
			
			var new_html = jQuery( '#wcpgsk_dialog_form_' + new_type ).html();
			
			jQuery(this).append(new_html);
			jQuery(this).children('form').attr('id', jQuery(this).children('form').attr('for'));
			jQuery(this).children('form').find(':input').each(function() {
				jQuery(this).attr('name', jQuery(this).attr('for'));
			});
			jQuery(this).children('form').find('select').each(function() {
				jQuery(this).attr('id', jQuery(this).attr('for'));
			});

			
			var mee = jQuery(this).dialog( 'option', 'context' );
			var btn = jQuery('[name="' + mee + '"]');
			var new_id = jQuery(this).dialog( 'option', 'new_id' );

			tr = btn.closest('tr');
			var str = tr.find('[name$="[settings_' + new_id + ']"]').val();						
			var s = jQuery('#wcpgsk_dlg_form_' + new_type).unserializeForm(str);
			return false;
		},
		beforeClose: function(event, ui) {
		},
		buttons: {
		"Save Settings": function() {
			var bValid = true;
			var mee = jQuery(this).dialog( 'option', 'context' );
			var btn = jQuery('[name="' + mee + '"]');
			var new_id = jQuery(this).dialog( 'option', 'new_id' );
			var dlgtype = jQuery(this).dialog( 'option', 'new_type' );
			var str = jQuery('#wcpgsk_dlg_form_' + dlgtype).serialize();
			tr = btn.closest('tr');
			tr.find('[name$="[settings_' + new_id + ']"]').val(str);						
			jQuery( this ).dialog( 'close' );
		},
		Cancel: function() {
			jQuery( this ).dialog( 'close' );
		}
		},
		close: function() {
		}
	});
	
	
	// Remove button
	jQuery('.wcpgsk_remove_field').on('click', function(){
		var remTable = jQuery('#' + jQuery(this).attr('for')),
			tr = jQuery(this).closest('tr');
		
		tr.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
			tr.remove();
			setRowNumbers(remTable);		
		});
		return false;
	});	
	
});

function get_locale_fields_form() {
	var localeCode = jQuery('#wcpgsk_configcountry').val();
	
	if (localeCode) {
		jQuery("#locale_field_form").html("");
		var data = {
				action: 'get_locale_field_form',
				localeCode: localeCode
		};
		jQuery.post(ajaxurl, data, function(response)
		{
			jQuery("#locale_field_form").html("");
			jQuery("#locale_field_form").append(response);
		});
	}
	else {
		jQuery("#locale_field_form").html("");
	}
};

function save_checkoutjs() {
		jQuery("#result_save_checkoutjs").html("");
		checkoutjs = jQuery('#wcpgsk_checkoutjs').val();
		if (checkoutjs != null) {
			 jQuery.ajax({
					  url: ajaxurl,
					  type: "POST",
					  data:{
						   'action':'wcpgsk_save_checkoutjs',
						   'checkoutjs':checkoutjs
						   },
					  dataType: 'html',
					  success:function(data){
					  		jQuery("#result_save_checkoutjs").append(data);
							},
					  error: function(jxq, eStatus, errorThrown){
						   alert('error' + eStatus);
						   console.log(errorThrown);
					  }
				 });		
		}
		else jQuery("#result_save_checkoutjs").html("");
		return false;
};

