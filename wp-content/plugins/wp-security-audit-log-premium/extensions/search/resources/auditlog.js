window.WsalAs = ( function() {
	var o = this;
	var attachEvents = [];

	o.AjaxUrl = window['ajaxurl'];
	o.AjaxAction = 'WsalAsWidgetAjax';

	// listen to auditlog refresh events
	o._WsalAuditLogRefreshed = window['WsalAuditLogRefreshed'];
	window['WsalAuditLogRefreshed'] = function(){
		o.Attach();
		o._WsalAuditLogRefreshed();

		// IP Tooltip
		jQuery( '.search-ip' ).darkTooltip( {
			animation : 'fadeIn',
	        gravity : 	'west',
	        size : 		'large',
	        confirm : 	true,
	        yes : 		'Search',
	        no : '',
	        onYes: function( elem ) {
				o.SearchByIP( elem.attr( 'data-ip' ) );
			}
		} );

		// Username Tooltip
		jQuery( '.search-user' ).darkTooltip( {
			animation : 'fadeIn',
	        gravity : 	'west',
	        size : 		'large',
	        confirm : 	true,
	        yes : 		'Search',
	        no : '',
	        onYes: function( elem ) {
				o.SearchByUser( elem.attr( 'data-user' ) );
			}
		} );

		// Search Help Tooltip.
		jQuery( '#wsal-search-help' ).darkTooltip( {
			animation : 'fadeIn',
	        gravity : 	'north',
	        size : 		'small',
	        confirm : 	false,
		} );
	};

	// Search by IP callback.
	o.SearchByIP = function ( ip ) {
		if ( ip.length == 0 ) return;
		window.WsalAs.real.removeAttr( 'value' );
		window.WsalAs.ClearFilters();
		jQuery( '#current-page-selector' ).attr( 'value', '1' );
		var ip_filter = 'ip:' + ip;
		o.AddFilter( ip_filter );
		jQuery( '#audit-log-viewer' ).submit();
	}

	// Search by username callback.
	o.SearchByUser = function ( username ) {
		if ( username.length == 0 ) return;
		window.WsalAs.real.removeAttr( 'value' );
		window.WsalAs.ClearFilters();
		jQuery( '#current-page-selector' ).attr( 'value', '1' );
		var username_filter = 'username:' + username;
		o.AddFilter( username_filter );
		jQuery( '#audit-log-viewer' ).submit();
	}

	// add callbacks to attach event
	o.Attach = function(cb){
		if(typeof cb === 'undefined'){
			// call callbacks
			for(i = 0; i < attachEvents.length; i++)attachEvents[i]();
		}else{
			// add callbacks
			attachEvents.push(cb);
		}
	};

	// extend default search box
	o.Attach(function(){
		if (jQuery('#wsal-as-fake-search').length) return; // already attached

		// select some important elements
		o.real = jQuery('#wsal-as-search-search-input');
		o.flds = jQuery('#wsal-as-filter-fields');
		o.searchBox = jQuery( '.search-box' );

		// nice search box effects
		o.real.css('width', '160px')
			.focus(function(){
				o.real.animate({ width: '360px' }, 'fast');
			})
			.blur(function(){
				o.real.animate({ width: '160px' }, 'fast');
			});

		// Search box help.
		o.search_help = jQuery( '<span />' );
		o.search_help.attr( 'id', 'wsal-search-help' );
		o.search_help.text( '?' );
		o.search_help.attr( 'data-tooltip', '- Use the free-text search to search for text in the alert\'s message.<br>- To search for a particular Alert ID, user, IP address, Post ID or Type or use date ranges, use the filters.' );

		// attach filter counter and dropdown
		o.fake = jQuery('<span id="wsal-as-fake-search" class="wsal-as-fake-search"/>');
		o.cntr = jQuery('<a id="wsal-filters-btn" href="javascript:;">0 filters</a>');
		o.ppup = jQuery('<div class="wsal-as-filter-popup" style="display: none;"/>');
		o.list = jQuery('<div class="wsal-as-filter-list no-filters"/>');
		o.real.before(o.ppup.append('', o.flds.show()), o.fake).appendTo(o.fake).after(o.cntr);
		( o.list ).insertAfter( o.real );
		( o.search_help ).insertBefore( o.list );

		// Clear Search Button
		o.clearBtn = jQuery( '<a></a>' );
		o.clearBtn.attr( 'id', 'clear-search' );
		o.clearBtn.addClass( 'button' );
		o.clearBtn.text( 'Clear Search' );
		o.clearBtn.attr( 'disabled', 'disabled' );

		// Save Search Button
		o.saveBtn = jQuery( '<a></a>' );
		o.saveBtn.attr( 'id', 'save-search-btn' );
		o.saveBtn.addClass( 'button' );
		o.saveBtn.text( 'Save Search & Filters' );

		// Load Search Button
		o.loadBtn = jQuery( '<a></a>' );
		o.loadBtn.attr( 'id', 'load-search-btn' );
		o.loadBtn.addClass( 'button' );
		o.loadBtn.text( 'Load Search & Filters' );

		( o.loadBtn ).insertAfter( o.searchBox );
		( o.saveBtn ).insertAfter( o.searchBox );
		( o.clearBtn ).insertAfter( o.searchBox );

		// Save Search Popup
		o.save_popup = jQuery( '<div class="wsal-save-popup" style="display:none" />' );
		o.save_name = jQuery( '<input name="wsal-save-search-name" id="wsal-save-search-name" placeholder="Search Save Name" />' );
		o.save_btn = jQuery( '<button type="submit" class="button button-primary">Save</button>' );
		o.save_error = jQuery( '<label id="wsal-save-search-error">Invalid Name</label>');
		o.save_tooltip = jQuery( '<p class="wsal-save-tooltip" />' );
		o.save_tooltip.text( 'Name can only be 12 characters long and only letters, numbers and underscore are allowed.' );
		( o.save_popup ).insertAfter( o.ppup );
		o.save_popup.append( o.save_name );
		o.save_popup.append( o.save_btn );
		o.save_popup.append( o.save_error );
		o.save_popup.append( o.save_tooltip );

		// Load Search Popup
		o.load_popup = jQuery( '<div class="wsal-load-popup" style="display:none" />' );
		o.load_list  = jQuery( '<div class="wsal-load-result-list" />' );
		o.load_popup.append( '<a class="close" href="javascript;" title="Remove">&times;</a>' );
		o.load_popup.append( load_list );
		( o.load_popup ).insertAfter( o.save_popup );

		o.cntr.click( function() {
			o.save_popup.hide();
			o.load_popup.fadeOut('fast');
			o.ppup.toggle();
		} );

		o.saveBtn.click( function() {
			o.ppup.hide();
			o.load_popup.fadeOut('fast');
			o.save_popup.toggle();
		} );

		jQuery( '.wsal-load-popup .close' ).click( function(e) {
			e.preventDefault();
			o.load_popup.fadeOut('fast');
		} );

		// attach suggestion dropdown
		// TODO suggestions should cause user query to be removed and the selected filter to appear in filters box
	});

	// add new filter
	o.AddFilter = function(text){
		var filter = text.split(':');
		if (filter[0] == 'from' || filter[0] == 'to') {
			// Validation date format
			if (!checkDate(filter[1])) {
	            return;
	        }
		}
		if(!jQuery('input[name="Filters[]"][value="' + text + '"]').length){
			o.list.append(
				jQuery('<span/>').append(
					jQuery('<input type="text" name="Filters[]"/>').val(text),
					jQuery('<a href="javascript:;" title="Remove">&times;</a></span>')
						.click(function(){
							jQuery(this).parents('span:first').fadeOut('fast', function(){
								jQuery(this).remove();
								o.CountFilters();
							});
						})
				)
			);
			o.clearBtn.removeAttr( 'disabled' );
		}
		o.CountFilters();
	};

	// remove existing filters
	o.ClearFilters = function(){
		o.list.html('');
		o.CountFilters();
	};

	// update filter count
	o.CountFilters = function(){
		var count = o.list.find('>span').length;
		o.cntr.text(count + ' filters');
		o.list[count === 0 ? 'addClass' : 'removeClass']('no-filters');
	};

	// Add new load result.
	o.AddSaveSearch = function( search ) {
		if ( ! search ) {
			var result_item = jQuery( '<div></div>' );
			result_item.addClass( 'saved-result-item' );

			var result_name = jQuery( '<span></span>' );
			result_name.addClass( 'save-result-name' );
			result_name.text( 'Nothing found!' );

			result_item.append( result_name );
			o.load_list.append( result_item );
			return;
		}
		var result_item = jQuery( '<div></div>' );
		result_item.addClass( 'saved-result-item' );

		var result_name = jQuery( '<span></span>' );
		result_name.addClass( 'save-result-name' );
		result_name.text( search['name'] );

		var result_load = jQuery( '<a></a>' );
		result_load.addClass( 'button button-primary load-search-result' );
		result_load.text( 'Load' );
		result_load.click( function( e ) {
			e.preventDefault();
			o.real.val( search.search_input );
			o.list.empty();
			if ( search.filters && search.filters.length > 0 ) {
				for ( var i = 0; i < search.filters.length; i++ ) {
					o.AddFilter( search.filters[i] );
				}
			}
			o.load_popup.fadeOut( 'fast' );
		} );

		var result_load_run = jQuery( '<a></a>' );
		result_load_run.addClass( 'button button-primary load-run-search-result' );
		result_load_run.text( 'Load & Run' );
		result_load_run.click( function( e ) {
			e.preventDefault();
			o.real.empty();
			o.real.val( search.search_input );
			o.list.empty();
			if ( search.filters && search.filters.length > 0 ) {
				for ( var i = 0; i < search.filters.length; i++ ) {
					o.AddFilter( search.filters[i] );
				}
			}
			jQuery( '#audit-log-viewer' ).submit();
		} );

		var delete_search = jQuery( '<a></a>' );
		delete_search.addClass( 'button button-primary delete-search-result' );
		delete_search.text( 'Delete' );

		// Delete ajax request.
		delete_search.click( function( e ) {
			e.preventDefault();
	    	delete_search.text( 'Deleting...' );

	    	// Get values of request.
	    	var load_nonce 	= jQuery( '#load_saved_search_field' ).val();
	    	var admin_url 	= jQuery( '#wsal-admin-url' ).val();
			var delete_search_request = jQuery.ajax( {
	            url : admin_url,
	            type : "POST",
	            data : {
	            	nonce : load_nonce,
	            	name : search.name,
	                action : "wsal_delete_save_search",
	            },
	            dataType : "json"
	        } );

	        delete_search_request.done( function( response ) {
	            if ( response.success ) {
	                delete_search.text( 'Deleted' );
	                result_item.fadeOut( 'slow' );
	                // document.location.reload();
	            } else {
	                console.log( response.message );
	            }
	        });

	        delete_search_request.fail( function( jqXHR, textStatus ) {
	            console.log( "Request Failed: " + textStatus );
	        });
		} );

		result_item.append( result_name );
		result_item.append( result_load );
		result_item.append( result_load_run );
		result_item.append( delete_search );

		o.load_list.append( result_item );

	}

	return o;
} )();

jQuery( document ).ready( function( $ ) {

	window.WsalAs.Attach();
    wsal_CreateDatePicker($, $('#wsal_as_widget_from'), null);
    wsal_CreateDatePicker($, $('#wsal_as_widget_to'), null);

    var wsal_search = window.WsalAs.real;
    if ( wsal_search.val() != ' ' ) {
    	window.WsalAs.clearBtn.removeAttr( 'disabled' );
    }

    // Clear Search Button JS.
    $( window.WsalAs.clearBtn ).click( function( event ) {
    	event.preventDefault();
    	if ( 'disabled' == $( window.WsalAs.clearBtn ).attr( 'disabled' ) ) return;
    	window.WsalAs.real.removeAttr( 'value' );
    	window.WsalAs.ClearFilters();
    	location.reload();
    } );

    // Manually add ip.
    $( '#wsal-add-ip-filter' ).click( function( event ) {
    	event.preventDefault();
    	var ip = $( 'input#wsal_as_widget_ip[data-prefix="ip"]' );
    	var ip_value = ip.val();
    	if ( ip_value.length == 0 ) return;
    	var ip_filter_value = 'ip:' + ip_value;
    	window.WsalAs.AddFilter( ip_filter_value );
    	ip.removeAttr( 'value' );
	} );

	// Trigger search on ENTER.
	$( '#wsal-as-search-search-input' ).keypress( function( event ) {
		if ( 13 === event.which ) {
			$( '#audit-log-viewer' ).submit();
		}
	} );

    /**
     * Load Search Results Ajax Request.
     *
     * @since 1.1.7
     */
    var load_btn = $( window.WsalAs.loadBtn );
    var saved_searches; // To store saved search results.
    load_btn.click( function( event ) {

    	event.preventDefault();
    	load_btn.text( 'Loading...' );
    	window.WsalAs.save_popup.hide();
		window.WsalAs.ppup.hide();

    	// Get values of request.
    	var load_nonce 	= $( '#load_saved_search_field' ).val();
    	var admin_url 	= $( '#wsal-admin-url' ).val();

    	// Get results list container.
    	var load_popup = $( window.WsalAs.load_popup );
    	var load_list = $( window.WsalAs.load_list );
    	load_list.empty();

    	var load_saved_search_request = $.ajax( {
            url : admin_url,
            type : "POST",
            data : {
            	nonce : load_nonce,
                action : "wsal_get_save_search",
            },
            dataType : "json"
        } );

        load_saved_search_request.done( function( response ) {
            if ( response.success ) {
                load_btn.text( 'Load Search & Filters' );
                load_popup.fadeIn( 'fast' );
                if ( response.search_results ) {
                	var search_count = response.search_results.length;
                	saved_searches = response.search_results;
                } else {
                	window.WsalAs.AddSaveSearch();
                }

                for ( var i = 0; i < search_count; i++ ) {
                	window.WsalAs.AddSaveSearch( response.search_results[i] );
                }
            } else {
            	load_btn.text( 'Load Search & Filters' );
            	load_popup.fadeIn( 'fast' );
            	window.WsalAs.AddSaveSearch();
                console.log( response.message );
            }
        });

        load_saved_search_request.fail( function( jqXHR, textStatus ) {
            console.log( "Request Failed: " + textStatus );
        });
    } );

    // Search save name pattern detection.
    $( '#wsal-save-search-name' ).on( "change keyup paste", function() {
    	var search_name = $( this ).val();
    	window.WsalAs.save_error.hide();
    	window.WsalAs.save_btn.removeAttr( 'disabled' );
    	var name_length = search_name.length;
    	if ( 12 <= name_length ) {
    		window.WsalAs.save_error.show();
    		window.WsalAs.save_btn.attr( 'disabled', 'disabled' );
    	}

    	var name_pattern = /^[a-z\d\_]+$/i;
    	if ( name_length && ! name_pattern.test( search_name ) ) {
    		window.WsalAs.save_error.show();
    		window.WsalAs.save_btn.attr( 'disabled', 'disabled' );
    	}
    } );

    // IP address validation.
    var ip_error = jQuery( '<span />' );
    ip_error.addClass( 'wsal-input-error' );
    ip_error.text( '* Invalid IP' );
    var ip_label = jQuery( 'label[for="wsal_as_widget_ip"]' );
    ip_label.append( ip_error );

    $( '#wsal_as_widget_ip' ).on( 'change keyup paste', function() {
    	var ip_value = $( this ).val();
    	var ip_add_btn = $( '#wsal-add-ip-filter' );
    	ip_error.hide();
    	ip_add_btn.removeAttr( 'disabled' );

    	var ip_pattern = /^(?!.*\.$)((1?\d?\d|25[0-5]|2[0-4]\d)(\.|$)){4}$/;
    	if ( ip_value.length && ! ip_pattern.test( ip_value ) ) {
    		ip_error.show();
    		ip_add_btn.attr( 'disabled', 'disabled' );
    	}
    } );

} );
