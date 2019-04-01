function makeJsonVar( obj ) {
	return encodeURIComponent( makeJson( obj ) ) ;
}
function makeJson( obj ) {
	return JSON.stringify( obj.serializeJSON() )  ;
}

jQuery( document ).ready( function( $ ) {

        $('#d-schedule-4 .datetimes-date').datepicker({
            dateFormat: 'yy-mm-dd',
            constrainInput: false,
            minDate: 0,
	});

	$( '#d-schedule-3 .btn-add' ).click( function(e) {
		var times = $( 'input[name="settings[schedule][times]"]' ).val();
		var weekday = $( '#d-schedule-3 .wc_oe-select-weekday' ).val();
		var time = $( '#d-schedule-3 .wc_oe-select-time' ).val();

		if( times.indexOf( weekday + ' ' + time) != -1 ) {
			return;
		}

		var data = [];
		if( times != '' ) {
			data = times.split( ',' ).map( function( time ) {
				var arr = time.split( ' ' );
				return { weekday: arr[ 0 ], time: arr[ 1 ] };
			} );
		}

		data.push( { weekday: weekday, time: time } );

		var weekdays = {
			'Sun': 1,
			'Mon': 2,
			'Tue': 3,
			'Wed': 4,
			'Thu': 5,
			'Fri': 6,
			'Sat': 7,
		};

		data.sort( function( a, b ) {
			if( weekdays[ a.weekday ] == weekdays[ b.weekday ] ) {
				return new Date( '1970/01/01 ' + a.time ) - new Date( '1970/01/01 ' + b.time );
			} else {
				return weekdays[ a.weekday ] - weekdays[ b.weekday ];
			}
		} );

		var html = data.map( function( elem ) {
			var weekday = settings_form.day_names[elem.weekday] ;
			return '<div class="time"><span class="btn-delete">×</span>'
			       + weekday + ' ' + elem.time + '</div>';
		} ).join( '' );

		times = data.map( function( elem ) {
			return elem.weekday + ' ' + elem.time;
		} ).join();

		$( '#d-schedule-3 .input-times' ).html( html );
		$( '#d-schedule-3 .btn-delete' ).click( shedule3_time_delete );

		$( 'input[name="settings[schedule][times]"]' ).val( times );
	} );

        $( '#d-schedule-4 .btn-add' ).click( function(e) {

                var times = $( 'input[name="settings[schedule][date_times]"]' ).val();
		var date = $( '#d-schedule-4 .datetimes-date' ).val();
		var time = $( '#d-schedule-4 .wc_oe-select-time' ).val();

		if( times.indexOf( date + ' ' + time) !== -1 ) {
			return;
		}

		var data = [];
		if( times !== '' ) {
                    data = times.split( ',' ).map( function( time ) {
                            var arr = time.split( ' ' );
                            return { date: arr[ 0 ], time: arr[ 1 ] };
                    } );
		}

		data.push( { date: date, time: time } );

		data.sort( function( a, b ) {
                    return new Date( a.date + ' ' + a.time ) - new Date( b.date + ' ' + b.time );
		} );

		var html = data.map( function( elem ) {
			return '<div class="time"><span class="btn-delete">×</span>'
			       + elem.date + ' ' + elem.time + '</div>';
		} ).join( '' );

		times = data.map( function( elem ) {
			return elem.date + ' ' + elem.time;
		} ).join();

		$( '#d-schedule-4 .input-date-times' ).html( html );
		$( '#d-schedule-4 .btn-delete' ).click( shedule4_time_delete );

		$( 'input[name="settings[schedule][date_times]"]' ).val( times );
	} );

	$( '#d-schedule-3 .input-times' ).ready( function() {
		var times = $( 'input[name="settings[schedule][times]"]' ).val();
		if( !times || times == '' ) {
			return;
		}
		var data = times.split( ',' );
		var html = data.map( function( elem ) {
			var x = elem.split(' ');
			var weekday = settings_form.day_names[x[0]] + ' ' + x[1];
			return '<div class="time"><span class="btn-delete">×</span>' + weekday + '</div>';
		} ).join( '' );
		$( '#d-schedule-3 .input-times' ).html( html );
		$( '#d-schedule-3 .btn-delete' ).click( shedule3_time_delete );
	} );

	$( '#d-schedule-4 .input-date-times' ).ready( function() {

                var times = $( 'input[name="settings[schedule][date_times]"]' ).val();

                if( !times || times == '' ) {
			return;
		}

                var data = times.split( ',' );

                var html = data.map( function( elem ) {
                    return '<div class="time"><span class="btn-delete">×</span>' + elem + '</div>';
		} ).join( '' );

		$( '#d-schedule-4 .input-date-times' ).html( html );
		$( '#d-schedule-4 .btn-delete' ).click( shedule4_time_delete );
	} );

	function shedule3_time_delete( e ) {
		var index = $( this ).parent().index();
		var data = $( 'input[name="settings[schedule][times]"]' ).val().split( ',' );
		data.splice( index, 1 );
		$( 'input[name="settings[schedule][times]"]' ).val( data.join() );
		$( this ).parent().remove();
	}

	function shedule4_time_delete( e ) {
		var index = $( this ).parent().index();
		var data = $( 'input[name="settings[schedule][date_times]"]' ).val().split( ',' );
		data.splice( index, 1 );
		$( 'input[name="settings[schedule][date_times]"]' ).val( data.join() );
		$( this ).parent().remove();
	}


	$( '#schedule-1,#schedule-2,#schedule-3,#schedule-4,#schedule-5' ).change( function() {
		if ( $( '#schedule-1' ).is( ':checked' ) && $( '#schedule-1' ).val() == 'schedule-1' ) {
			$( '#d-schedule-2 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-2 select' ).attr( 'disabled', true )
			$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', false )
			$( '#d-schedule-1 select' ).attr( 'disabled', false )
			$( '#d-schedule-3 .block' ).addClass( 'disabled' );
            $( '#d-schedule-4 .block' ).addClass( 'disabled' );
            $( '#d-schedule-5 .block' ).addClass( 'disabled' );
		} else if( $( '#schedule-2' ).is( ':checked' ) && $( '#schedule-2' ).val() == 'schedule-2' ) {
			$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-1 select' ).attr( 'disabled', true )
			$( '#d-schedule-2 select' ).attr( 'disabled', false )
			$( '#d-schedule-2 input:not(input[type=radio]) ' ).attr( 'disabled', false )
			$( '#d-schedule-3 .block' ).addClass( 'disabled' );
            $( '#d-schedule-4 .block' ).addClass( 'disabled' );
            $( '#d-schedule-5 .block' ).addClass( 'disabled' );
		} else if( $( '#schedule-3' ).is( ':checked' ) && $( '#schedule-3' ).val() == 'schedule-3' ) {
			$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-1 select' ).attr( 'disabled', true )
			$( '#d-schedule-2 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-2 select' ).attr( 'disabled', true )
			$( '#d-schedule-3 .block' ).removeClass( 'disabled' );
			$( '#d-schedule-4 .block' ).addClass( 'disabled' );
			$( '#d-schedule-5 .block' ).addClass( 'disabled' );
		} else if( $( '#schedule-4' ).is( ':checked' ) && $( '#schedule-4' ).val() == 'schedule-4' ) {
			$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-1 select' ).attr( 'disabled', true )
			$( '#d-schedule-2 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-2 select' ).attr( 'disabled', true )
			$( '#d-schedule-3 .block' ).addClass( 'disabled' );
			$( '#d-schedule-4 .block' ).removeClass( 'disabled' );
			$( '#d-schedule-5 .block' ).addClass( 'disabled' );
		} else if( $( '#schedule-5' ).is( ':checked' ) && $( '#schedule-5' ).val() == 'schedule-5' ) {
			$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-1 select' ).attr( 'disabled', true )
			$( '#d-schedule-2 input:not(input[type=radio])' ).attr( 'disabled', true )
			$( '#d-schedule-2 select' ).attr( 'disabled', true )
			$( '#d-schedule-3 .block' ).addClass( 'disabled' );
			$( '#d-schedule-4 .block' ).addClass( 'disabled' );
			$( '#d-schedule-5 .block' ).removeClass( 'disabled' );
		}
	} );
	$( '#schedule-1' ).change()
	$( '.wc_oe-select-interval' ).change( function() {
		var interval = $( this ).val()
		if ( interval == 'custom' ) {
			$( '#custom_interval' ).show()
		} else {
			$( '#custom_interval' ).hide()
		}
	} );
	$( '.wc_oe-select-interval' ).change()

	$( '.output_destination' ).click( function() {
		var input = $( this ).find( 'input' );
		var target = input.val();
		$( '.set-destination:not(#' + target + ')' ).hide();
		$( '.my-icon-triangle' ).removeClass( 'ui-icon-triangle-1-n' );
		$( '.my-icon-triangle' ).addClass( 'ui-icon-triangle-1-s' );
		if ( !jQuery( '#' + target ).is( ':hidden' ) ) {
			jQuery( '#' + target ).hide();
		}
		else {
			if ( jQuery( '#' + target ).is( ':hidden' ) ) {
				jQuery( '#' + target ).show();
				$( '#test_reply_div' ).hide();
				$( input ).next().removeClass( 'ui-icon-triangle-1-s' );
				$( input ).next().addClass( 'ui-icon-triangle-1-n' );
			}
		}
	} );

	var is_unchecked_shown = true;
	$('#hide_unchecked').on('click', function(e) {
		e.preventDefault();
		is_unchecked_shown = !is_unchecked_shown;
		$("#order_fields li input:checkbox:not(:checked)").closest('.mapping_row').toggle(is_unchecked_shown);
		$('#hide_unchecked div').toggle();
	});

	function my_hide( item ) {
		if ( $( item ).is( ':hidden' ) ) {
			$( item ).show();
			return false;
		}
		else {
			$( item ).hide();
			return true;
		}
	}

	$( '.my-hide-parent' ).click( function() {
		my_hide( $( this ).parent() );
	} );

	$( '.my-hide-next' ).click( function() {
		var f = my_hide( $( this ).next() );
		if ( f ) {
			$( this ).find( 'span' ).removeClass( 'ui-icon-triangle-1-n' );
			$( this ).find( 'span' ).addClass( 'ui-icon-triangle-1-s' );
		}
		else {
			$( this ).find( 'span' ).removeClass( 'ui-icon-triangle-1-s' );
			$( this ).find( 'span' ).addClass( 'ui-icon-triangle-1-n' );
		}
		return false;
	} );


	$( '.wc_oe_test' ).click( function() {
		var test = $( this ).attr( 'data-test' );
		var data = 'json=' + makeJsonVar( $( '#export_job_settings' ) )
		data = data + "&action=order_exporter&method=test_destination&mode=" + mode + "&id=" + job_id + "&destination=" + test + '&woe_nonce=' + woe_nonce;
		$( '#test_reply_div' ).hide();
		$.post( ajaxurl, data, function( data ) {
			$( '#test_reply' ).val( data );
			$( '#test_reply_div' ).show();
		} )
	} )



	$( '.segment_choice' ).click( function () {

		$('.segment_choice').removeClass('active');
		$(this).addClass('active');

		$('.settings-segment').removeClass('active');
		$( '#' + $( this ).data( 'segment' ) + '_unselected_segment' ).addClass('active');

		window.location.href = $(this).attr('href');

                jQuery('.tab-actions-forms .segment-form').removeClass('active').find('input,select').val('');
	} );

	setTimeout( function () {
		if (summary_mode) {
			$('.segment_choice[href="products"]').click()
		} else if (window.location.hash.indexOf('segment') !== -1) {
			$('.segment_choice[href="'+ window.location.hash +'"]').click()
		} else {
			$('.segment_choice').first().click();
		}
	}, 1000 );


	var text_area = $( '#destination-email-body' );

	$( '#show-email-body' ).click( function () {
		text_area.toggle();
	} );

	setTimeout( function (  ) {
		if ( ! $( '#destination-email-body textarea' ).val() ) {
			text_area.hide();
		}
	}, 0);


	$( '#clear_selected_fields' ).click( function () {
		var confirm = window.confirm(localize_settings_form.remove_all_fields_confirm);
		if ( confirm ) {
			if ( $( '#order_fields .mapping_row-delete_field' ).length > 0 ) {
				$( '#order_fields .mapping_row-delete_field' ).click();
			}
		}
	} );

} )

function remove_custom_field( item ) {
	jQuery( item ).parent().parent().remove();
	return false;
}

function make_repeat_options( index ) {
	var repeat_select = jQuery( '<select name="duplicated_fields_settings[' + index + '][repeat]"></select>' );
	var repeat_options_html = {};

	jQuery.each(localize_settings_form.repeats, function(key, currentValue) {
		repeat_select.append( '<option value="' + key + '">' + currentValue + '</option>' );
		repeat_options_html[key] = [];
	});

	var duplicate_settings = window.duplicated_fields_settings[index] || {};
	var repeat_value = ( typeof( duplicate_settings.repeat ) !== 'undefined' ) ? duplicate_settings.repeat : "rows";
	repeat_select.val( repeat_value );

	// rows options
	if ( index === 'products' ) {
		var populate_check_on = duplicate_settings.populate_other_columns === '1' ? 'checked' : '';
		var populate_check_off = duplicate_settings.populate_other_columns === '1' ? '' : 'checked';
		var populate_check_html = '<div class="">' +
		                          '<label>' +  localize_settings_form.js_tpl_popup.fill_order_columns_label + '</label>' +
		                          '<label>' +
		                          '<input type=radio name="duplicated_fields_settings[' + index + '][populate_other_columns]" value=1 ' + populate_check_on + ' >' +
		                          localize_settings_form.js_tpl_popup.for_all_rows_label + '</label>' +
		                          '<label>' +
		                          '<input type=radio name="duplicated_fields_settings[' + index + '][populate_other_columns]" value=0 ' + populate_check_off + ' >' +
		                          localize_settings_form.js_tpl_popup.for_first_row_only_label + '</label>' +
		                          '</div>';
		repeat_options_html['rows'].push(populate_check_html);
	}

	// columns options
	var max_cols        = ( typeof(duplicate_settings.max_cols) !== 'undefined' ) ? duplicate_settings.max_cols : "10";

	var max_cols_html = '<div class="">' +
	                    '<label>' + localize_settings_form.js_tpl_popup.add + '</label>' +
	                    '<input type=text size=2 name="duplicated_fields_settings['+ index +'][max_cols]" value="'+ max_cols +'"> ' +
	                    '<label>' + localize_settings_form.repeats.columns + '</label>' +
	                    '</div>';

	var grouping_by_product_check = duplicate_settings.group_by === 'product' ? 'checked' : '';
	var group_by_item_check_html = '<div class="">' +
	                               '<input type="hidden" name="duplicated_fields_settings[' + index + '][group_by]" value="as_independent_columns" >' +
	                               '<input type="checkbox" name="duplicated_fields_settings[' + index + '][group_by]" value="product" ' + grouping_by_product_check +'>' +
	                               '<label>' + localize_settings_form.js_tpl_popup.grouping_by[index] +'</label>' +
	                               '</div>';
	repeat_options_html['columns'].push(max_cols_html);
	repeat_options_html['columns'].push(group_by_item_check_html);

	// inside one cell options
	var line_delimiter  = ( typeof(duplicate_settings.line_delimiter) !== 'undefined' ) ? duplicate_settings.line_delimiter : '\\n';
	var line_delimiter_html = '<div class="">' +
	                          '<label>' + localize_settings_form.js_tpl_popup.split_values_by +
	                          '<input class="input-delimiter" type=text size=1 name="duplicated_fields_settings['+ index +'][line_delimiter]" value="'+ line_delimiter +'">' +
	                          '</label>' +
	                          '</div>';
	repeat_options_html['inside_one_cell'].push(line_delimiter_html);

	var popup_options = jQuery('<div class=""></div>');
	popup_options.append(jQuery('<div class="segment-header">' + '<label>' + localize_settings_form.js_tpl_popup.add + ' ' + index + ' '  + localize_settings_form.js_tpl_popup.as + '</label>' + '</div>').append(repeat_select) );

	jQuery.each(repeat_options_html, function(key, currentValue) {
		popup_options.append(jQuery('<div class="display_as duplicate_' + key + '_options"></div>').append(currentValue));
	});

	popup_options.append("<hr>");

	repeat_select.off('change').on('change', function(){
		jQuery(this).parent().siblings('.display_as').removeClass('active');
		jQuery(this).parent().siblings('.duplicate_' + this.value + '_options').addClass('active');
	}).trigger('change');

	return popup_options;
}

function create_selected_fields( old_output_format, format , format_changed) {

	var $old_format_order_fields = jQuery( "#order_fields" ).clone();

        setTimeout(function () {
            create_unselected_fields( old_output_format, format , format_changed, $old_format_order_fields );
        }, 0);

	//jQuery( '#export_job_settings' ).prepend( jQuery( "#fields_control_products" ) );
	//jQuery( '#export_job_settings' ).prepend( jQuery( "#fields_control_coupons" ) );

        jQuery( "#fields .fields-control-block").addClass('hidden');
        jQuery( "#order_fields").addClass('non_flat_height');

	/*
	Clone elements for using in create_modal_fields ($old_format_order_fields) and
	before insert fields in 'order_fields' element ($old_format_modal_content) for
	able to migrate checkbox values from pop up to 'order_fields' element and vice versa
	*/

	var html = '';
        var fields_control_block_elements = [];

	if ( is_flat_format( format ) ) {
		fields_control_block_elements.push( make_repeat_options( 'products' ) );
		fields_control_block_elements.push( make_repeat_options( 'coupons' ) );
	}

	jQuery.each( window['selected_order_fields'], function( i, value ) {

            var index   = value.key;
            var colname = value.colname;

            colname     = escapeStr(colname);
            value.label = escapeStr(value.label);
            index       = escapeStr(index);
            value.value = escapeStr(value.value);

            if(format_changed) {
                if( is_flat_format( format ) )
                    colname = value.label;
                else if ( is_xml_format( format ) )
                    colname = to_xml_tags( index );
                else
                    colname = index;
            }

            if ( index == 'products' || index == 'coupons' ) {

                var row = '';

                jQuery( "#fields_control .segment_" + index ).remove();

                if( ! is_flat_format( format ) ) {
                    // TODO fix segment names for product and coupon fields
                    row = '<li class="mapping_row segment_' + value.segment + 's' + ' flat-'+ index +'-group" style="display: none">\
                            <div class="mapping_col_1" style="width: 10px">\
                                    <input type=hidden name="orders[][segment]"  value="' + value.segment + '">\
                                    <input type=hidden name="orders[][key]"  value="' + index + '">\
                                    <input type=hidden name="orders[][label]"  value="' + value.label + '">\
                                    <input type=hidden name="orders[][format]"  value="'+ value.format +'">\
                            </div>\
                            <div class="mapping_col_2">' + value.label + '</div>\
                            <div class="mapping_col_3">';
                    row += '<div class="segment_' + index + '">';
                    row += '<input class="mapping_fieldname" type=input name="orders[][colname]" value="' + colname + '">';
                    row += '</div>';
                    row += '</div>';
                    row += '<ul id="sortable_'+ index +'">'+ create_group_fields( format, index, format_changed, old_output_format, $old_format_order_fields) +'</ul>';
                    row += '</li>';
                } else {
                    row = '<div class="hide flat-'+ index +'-group">';
                    row += '<input type=hidden name="orders[][segment]"  value="' + value.segment + '">';
                    row += '<input type=hidden name="orders[][key]"  value="' + index + '">';
                    row += '<input class="mapping_fieldname" type=hidden name="orders[][colname]" value="' + colname + '">';
                    row += '<input type=hidden name="orders[][label]"  value="' + value.label + '">';
                    row += '<input type=hidden name="orders[][format]"  value="'+ value.format +'"></div>';

                }

            }
            else {

                if ( ! is_flat_format( format ) && ( value.segment === "products" || value.segment === "coupons" ) ) {
                    return true;
                }

                var value_part = ''
                var label_part = '';
                var delete_btn = '<div class="mapping_col_3 mapping_row-delete_field_block"><a href="" class="mapping_row-delete_field"><span class="dashicons dashicons-trash"></span></a></div>';
                var label_prefix = '';
				var index_api = index;

                if ( index.indexOf( 'static_field' ) >= 0 ) {
                    value_part = '<div class="mapping_col_3"><input class="mapping_fieldname" type=input name="orders[][value]" value="' + value.value + '"></div>';
                }

                // label prefix for products and coupons
                if ( is_flat_format(format) ) {
                    if ( value.segment === 'products' ) {
                        label_prefix = '[P] ';
						index_api = index_api.replace("plain_products_", "");
                    }
                    if ( value.segment === 'coupons' ) {
                        label_prefix = '[C] ';
						index_api = index_api.replace("plain_coupons_", "");
                    }
                }
                var row = '<li class="mapping_row segment_' + value.segment + '">\
                            <div class="mapping_col_1" style="width: 10px">\
                                    <input type=hidden name="orders[][segment]"  value="' + value.segment + '">\
                                    <input type=hidden name="orders[][key]"  value="' + index + '">\
                                    <input type=hidden name="orders[][label]"  value="' + value.label + '">\
                                    <input type=hidden name="orders[][format]"  value="' + value.format + '">\
                            </div>\
                            <div class="mapping_col_2" title="'+index_api+'">' + '<span class="field-prefix">' + label_prefix + '</span>' + value.label + label_part + '</div>\
                            <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="orders[][colname]" value="' + colname + '"></div> ' + value_part + delete_btn + '\
                        </li>\
                        ';
            }

            html += row;

	} );

	jQuery( "#order_fields" ).html( html );

        if ( ! jQuery( "#fields .fields-control-block").html() ) {
	        fields_control_block_elements.forEach(function(currentValue){
		        jQuery( "#fields .fields-control-block").append(currentValue);
	        });
        }

        if ( fields_control_block_elements.length > 0 ) {
            jQuery( "#fields .fields-control-block").removeClass('hidden');
	        jQuery( "#order_fields").removeClass('non_flat_height');
        }

        add_bind_for_custom_fields( 'products', output_format, jQuery( "#order_fields" ) );
        add_bind_for_custom_fields( 'coupons', output_format, jQuery( "#order_fields" ) );

        jQuery( "#sortable_products" ).sortable();
        jQuery( "#sortable_coupons" ).sortable();

        check_sortable_groups();

        moving_products_and_coupons_group_blocks_to_first_item(output_format);
}

function create_group_fields( format, index_p, format_changed ) {

    var html = '';

    jQuery.each( window['selected_order_' + index_p + '_fields'], function( i, value ) {

        var index   = value.key;
	var colname = value.colname;

        colname     = escapeStr(colname);
        value.label = escapeStr(value.label);
        index       = escapeStr(index);
        value.value = escapeStr(value.value);

        if(format_changed) {
            if( is_flat_format( format ) ) {
                colname = value.label;
            } else {
                colname = index.replace('plain_' + index_p + '_', '');
                if ( is_xml_format( format ) )
                        colname = to_xml_tags( colname );
            }
        }

        var value_part = '';
        var label_part = '';
        var delete_btn = '<div class="mapping_col_3 mapping_row-delete_field_block"><a href="#" class="mapping_row-delete_field"><span class="dashicons dashicons-trash"></span></a></div>';

        if ( index.indexOf( 'static_field' ) >= 0 ) {
                value_part = '<div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + index_p + '[][value]" value="' + value.value + '"></div>';
        }

        var row = '<li class="mapping_row segment_' + index_p + '">\
                    <div class="mapping_col_1" style="width: 10px">\
                        <input type=hidden name="'+ index_p +'[][label]"  value="' + value.label + '">\
                        <input type=hidden name="'+ index_p +'[][key]"  value="' + index + '">\
                        <input type=hidden name="'+ index_p +'[][segment]"  value="' + index_p + '">\
                        <input type=hidden name="'+ index_p +'[][format]"  value="' + value.format + '">\
                    </div>\
                    <div class="mapping_col_2" title="'+index+'">' + value.label + label_part + '</div>\
                    <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="'+ index_p +'[][colname]" value="' + colname + '"></div> ' + value_part + delete_btn + '\
            </li>\
            ';

        html += row;

    } );

    return html;
}

//for XML labels
function to_xml_tags( str ) {
	var arr = str.split( /_/ );
	for ( var i = 0, l = arr.length; i < l; i++ ) {
		arr[i] = arr[i].substr( 0, 1 ).toUpperCase() + ( arr[i].length > 1 ? arr[i].substr( 1 ).toLowerCase() : "" );
	}
	return arr.join( "_" );
}


function change_filename_ext() {
	if ( jQuery( '#export_filename' ).length ) {
		var filename = jQuery( '#export_filename input' ).val();
		var ext = output_format.toLowerCase();
		if( ext=='xls'  && !jQuery( '#format_xls_use_xls_format' ).prop('checked') ) //fix for XLSX
			ext = 'xlsx';

		var file = filename.replace( /^(.*)\..+$/, "$1." + ext );
		if( file.indexOf(".") == -1)  //no dots??
			file = file + "." + ext;
		jQuery( '#export_filename input' ).val( file );
		show_summary_report(output_format);
	}
}

function show_summary_report(ext) {
	if( is_flat_format(ext) ) {
		jQuery( '#summary_report_by_products' ).show();
	} else  {
		jQuery( '#summary_report_by_products' ).hide();
		jQuery( '#summary_setup_fields' ).hide();
		jQuery( '#summary_report_by_products_checkbox' ).prop('checked', false).trigger('change');
	}
}

function modal_buttons()
{
    jQuery('input[name=custom_meta_products_mode]').change();
    jQuery('#custom_meta_coupons_mode_all').attr('checked', 'checked');
    jQuery('#custom_meta_coupons_mode_all').change();
}

jQuery( document ).ready( function( $ ) {

	try {
		select2_inits();
	}
	catch ( err ) {
		console.log( err.message );
		jQuery( '#select2_warning' ).show();
	}

	jQuery( "#settings_title" ).focus();

	bind_events();
	jQuery( '#taxonomies' ).change();
	jQuery( '#attributes' ).change();
	if ( jQuery( '#itemmeta option' ).length>0 )
		jQuery( '#itemmeta' ).change();
	jQuery( '#custom_fields' ).change();
	jQuery( '#product_custom_fields' ).change();
	jQuery( '#user_custom_fields' ).change();
	jQuery( '#shipping_locations' ).change();
	jQuery( '#billing_locations' ).change();
	jQuery( '#item_names' ).change();
	jQuery( '#item_metadata' ).change();
//		jQuery( '#' + output_format + '_options' ).show();

	//jQuery('#fields').toggle(); //debug
	create_selected_fields( null, output_format, false );
	$( '#test_reply_div' ).hide();
//		jQuery( '#' + output_format + '_options' ).hide();

	jQuery( "#sort_products" ).sortable()/*.disableSelection()*/;
	jQuery( "#sort_coupons" ).sortable()/*.disableSelection()*/;
	jQuery( "#order_fields" ).sortable({
            scroll: true,
            scrollSensitivity: 100,
            scrollSpeed: 100,
            stop: function ( event, ui ) {
                moving_products_and_coupons_group_blocks_to_first_item(jQuery( '.output_format:checked' ).val());
            }
        });


	modal_buttons();

	jQuery( '.date' ).datepicker( {
		dateFormat: 'yy-mm-dd',
		constrainInput: false
	} );

	jQuery( '#adjust-fields-btn' ).click( function() {
		jQuery( '#fields' ).toggle();
		jQuery( '#fields_control' ).toggle();
		return false;
	} );

	jQuery( '.field_section' ).click( function() {
		var section = jQuery( this ).val();
		var checked = jQuery( this ).is( ':checked' );

		jQuery( '.segment_' + section ).each( function( index ) {
			if ( checked ) {
				jQuery( this ).show();
				//jQuery(this).find('input:checkbox:first').attr('checked', true);
			}
			else {
				jQuery( this ).hide();
				jQuery( this ).find( 'input:checkbox:first' ).attr( 'checked', false );
			}
		} );
	} );

	jQuery( '.output_format' ).click( function() {
		var new_format = jQuery( this ).val();
		jQuery( '#my-format .my-icon-triangle' ).removeClass( 'ui-icon-triangle-1-n' );
		jQuery( '#my-format .my-icon-triangle' ).addClass( 'ui-icon-triangle-1-s' );

		if ( new_format != output_format ) {
			jQuery( this ).next().removeClass( 'ui-icon-triangle-1-s' );
			jQuery( this ).next().addClass( 'ui-icon-triangle-1-n' );
			jQuery( '#' + output_format + '_options' ).hide();
			jQuery( '#' + new_format + '_options' ).show();
			var format_type_changed = ! (is_flat_format(new_format) && is_flat_format(output_format));
			old_output_format = output_format;
			output_format = new_format;
			synch_selected_fields( old_output_format, output_format );
			create_selected_fields( old_output_format, output_format, format_type_changed );
			jQuery( '.field_section' ).prop('checked', true);
			jQuery( '#output_preview, #output_preview_csv' ).hide();
//				jQuery( '#fields' ).hide();
//				jQuery( '#fields_control' ).hide();
			change_filename_ext();
		}
		else {
			if ( !jQuery( '#' + new_format + '_options' ).is( ':hidden' ) ) {
				jQuery( '#' + new_format + '_options' ).hide();
			}
			else {
				if ( jQuery( '#' + new_format + '_options' ).is( ':hidden' ) ) {
					jQuery( '#' + new_format + '_options' ).show();
					jQuery( this ).next().removeClass( 'ui-icon-triangle-1-s' );
					jQuery( this ).next().addClass( 'ui-icon-triangle-1-n' );
				}
			}
		}

                check_sortable_groups();
	} );

	$( '#date_format_block select' ).change( function() {
		var value = $( this ).val();
		if( value == 'custom' ) {
			$( '#custom_date_format_block' ).show();
		} else {
			$( '#custom_date_format_block' ).hide();
			$( 'input[name="settings[date_format]"]' ).val( value );
		}
	} );

	$( '#time_format_block select' ).change( function() {
		var value = $( this ).val();
		if( value == 'custom' ) {
			$( '#custom_time_format_block' ).show();
		} else {
			$( '#custom_time_format_block' ).hide();
			$( 'input[name="settings[time_format]"]' ).val( value );
		}
	} );

	$( 'input[type="checkbox"][name="settings[custom_php]"]' ).change( function() {
		$( 'div#custom_php_code_textarea' ).toggle( $( this ).is( ':checked' ) );
	} );

	$( '#order_fields input[type=checkbox]' ).change( function() {
		if ( $( '#order_fields input[type=checkbox]:not(:checked)' ).length ) {
			$( 'input[name=orders_all]' ).attr( 'checked', false );
		}
		else {
			$( 'input[name=orders_all]' ).attr( 'checked', true );
		}
	} );

	$( 'input[name=orders_all]' ).change( function() {
		if ( $( 'input[name=orders_all]' ).is( ':checked' ) ) {
			$( '#order_fields input[type=checkbox]' ).attr( 'checked', true );
		}
		else {
			$( '#order_fields input[type=checkbox]' ).attr( 'checked', false );
		}
	} );

	if ( $( '#order_fields input[type=checkbox]' ).length ) {
		$( '#order_fields input[type=checkbox]:first' ).change();
	}




	$( ".preview-btn" ).click( function() {
		preview(jQuery(this).attr('data-limit'));
		return false;
	} );

	$( '#progress_div .title-download' ).click( function() {
		$( '#progress_div .title-download' ).hide();
		$( '#progress_div .title-cancel' ).show();
		$( '#progressBar' ).show();
		jQuery( '#progress_div' ).hide();
		closeWaitingDialog();
	});

	function preview(size) {
		jQuery( '#output_preview, #output_preview_csv' ).hide();
		var data = 'json=' + makeJsonVar( $( '#export_job_settings' ) );
		var estimate_data = data + "&action=order_exporter&method=estimate&mode=" + mode + "&id=" + job_id + '&woe_nonce=' + woe_nonce;
		$.post( ajaxurl, estimate_data, function( response ) {
				if (!response || typeof response.total == 'undefined') {
					woe_show_error_message(response);
					return;
				}
				jQuery( '#output_preview_total' ).find( 'span' ).html( response.total );
				jQuery( '#preview_actions' ).removeClass( 'hide' );
			}, "json"
		).fail( function( xhr, textStatus, errorThrown ) {
			woe_show_error_message( xhr.responseText );
		});

		function showPreview( response ) {
			var id = 'output_preview';
			if ( is_flat_format( output_format ) )
				id = 'output_preview_csv';
			if ( is_object_format( output_format ) ) {
				jQuery( '#' + id ).text( response );
			}
			else {
				jQuery( '#' + id ).html( response );
			}
			jQuery( '#' + id ).show();
			window.scrollTo( 0, document.body.scrollHeight );
		}

		data = data + "&action=order_exporter&method=preview&limit="+size+"&mode=" + mode + "&id=" + job_id + '&woe_nonce=' + woe_nonce;
		$.post( ajaxurl, data, showPreview, "html" ).fail( function( xhr, textStatus, errorThrown ) {
			showPreview( xhr.responseText );
		});
	}
// EXPORT FUNCTIONS
	function get_data() {
		var data = new Array();
		data.push( { name: 'json', value: makeJson( $( '#export_job_settings' ))  } );
		data.push( { name: 'action', value: 'order_exporter' } );
		data.push( { name: 'mode', value: mode } );
		data.push( { name: 'id', value: job_id } );
		return data;
	}

	function progress( percent, $element ) {

		if ( percent == 0 ) {
			$element.find( 'div' ).html( percent + "%&nbsp;" ).animate( { width: 0 }, 0 );
			waitingDialog();
			jQuery( '#progress_div' ).show();
		}
		else {
			var progressBarWidth = percent * $element.width() / 100;
			$element.find( 'div' ).html( percent + "%&nbsp;" ).animate( { width: progressBarWidth }, 200 );

			if ( percent >= 100 ) {
				if(!is_iPad_or_iPhone()) {
					jQuery( '#progress_div' ).hide();
					closeWaitingDialog();
				}
			}
		}
	}

	function get_all( start, percent, method ) {
		if (window.cancelling) {
			return;
		}

		progress( parseInt( percent, 10 ), jQuery( '#progressBar' ) );

		if ( percent < 100 ) {
			data = get_data();
			data.push( { name: 'method', value: method } );
			data.push( { name: 'start', value: start } );
			data.push( { name: 'file_id', value: window.file_id } );
			data.push( { name: 'woe_nonce', value: woe_nonce } );

			jQuery.ajax( {
				type: "post",
				data: data,
				cache: false,
				url: ajaxurl,
				dataType: "json",
				error: function( xhr, status, error ) {
					woe_show_error_message( xhr.responseText );
					progress( 100, jQuery( '#progressBar' ) );
				},
				success: function( response ) {
					if ( !response) {
						woe_show_error_message(response);
					} else if ( typeof response.error !== 'undefined') {
						woe_show_error_message( response.error );
					} else {
						get_all( response.start, ( response.start / window.count ) * 100, method )
					}
				}
			} );
		}
		else {
			data = get_data();
			data.push( { name: 'method', value: 'export_finish' } );
			data.push( { name: 'file_id', value: window.file_id } );
			data.push( { name: 'woe_nonce', value: woe_nonce } );
			jQuery.ajax( {
				type: "post",
				data: data,
				cache: false,
				url: ajaxurl,
				dataType: "json",
				error: function( xhr, status, error ) {
					alert( xhr.responseText );
				},
				success: function( response ) {
					var download_format = output_format;
					if( output_format=='XLS' && !jQuery( '#format_xls_use_xls_format' ).prop('checked') )
						download_format =  'XLSX';

					if(is_iPad_or_iPhone()) {
						$( '#progress_div .title-download a' ).attr( 'href', ajaxurl + (ajaxurl.indexOf('?') === -1? '?':'&')+'action=order_exporter&method=export_download&format=' + download_format + '&file_id=' + window.file_id );
						$( '#progress_div .title-download' ).show();
						$( '#progress_div .title-cancel' ).hide();
						$( '#progressBar' ).hide();
					} else {
						$( '#export_new_window_frame' ).attr( "src", ajaxurl + (ajaxurl.indexOf('?') === -1? '?':'&')+'action=order_exporter&method=export_download&format=' + download_format + '&file_id=' + window.file_id );
					}

					reset_date_filter_for_cron();
				}
			} );
		}
	}

	function is_iPad_or_iPhone() {
		return navigator.platform.match(/i(Phone|Pad)/i)
	}

	function waitingDialog() {
		jQuery( "#background" ).addClass( "loading" );
		jQuery( '#wpbody-content' ).keydown(function(event) {
			if ( event.keyCode == 27 ) {
				if (!window.cancelling) {
					event.preventDefault();
					window.cancelling = true;

					jQuery.ajax( {
						type: "post",
						data: {
							action: 'order_exporter',
							method: 'cancel_export',
							file_id: window.file_id,
						},
						cache: false,
						url: ajaxurl,
						dataType: "json",
						error: function( xhr, status, error ) {
							alert( xhr.responseText );
							progress( 100, jQuery( '#progressBar' ) );
						},
						success: function( response ) {
							progress( 100, jQuery( '#progressBar' ) );
						}
					} );

					window.count = 0;
					window.file_id = '';
					jQuery( '#wpbody-content' ).off('keydown');
				}
				return false;
			}
		});
	}
	function closeWaitingDialog() {
		jQuery( "#background" ).removeClass( "loading" );
	}

	function openFilter(object_id, verify_checkboxes) {
		verify_checkboxes = verify_checkboxes || 0;
		var f = false;
		$( '#'+object_id+' ul' ).each( function( index ) {
			if ( $( this ).find( 'li:not(:first)' ).length ) {
				f = true;
			}
		} );

		// show checkboxes for order and coupon section  ?
		if ( f  ||  verify_checkboxes && $('#'+object_id+" input[type='checkbox']:checked").length ) {
			$( '#'+object_id ).prev().click();
		}
	}

	function validateExport() {
		if ( ( mode == settings_form.EXPORT_PROFILE ) && ( !$( "[name='settings[title]']" ).val() ) ) {
			alert( export_messages.empty_title );
			$( "[name='settings[title]']" ).focus();
			return false;
		}

		if ( ( $( "#from_date" ).val() ) && ( $( "#to_date" ).val() ) ) {
			var d1 = new Date( $( "#from_date" ).val() );
			var d2 = new Date( $( "#to_date" ).val() );
			if ( d1.getTime() > d2.getTime() ) {
				alert( export_messages.wrong_date_range );
				return false;
			}
		}
		if ( $( '#order_fields > li' ).length == 0 )
		{
			alert( export_messages.no_fields );
			return false;
		}

		return true;
	}
// EXPORT FUNCTIONS END
	$( "#export-wo-pb-btn" ).click( function() {
		$( '#export_wo_pb_form' ).attr( "action", ajaxurl );
		$( '#export_wo_pb_form' ).find( '[name=json]' ).val( makeJson( $( '#export_job_settings' ) ) );
		$( '#export_wo_pb_form' ).submit();
		return false;
	} );

	$( "#export-btn, #my-quick-export-btn" ).click( function() {
		window.cancelling = false;

		data = get_data();

		data.push( { name: 'method', value: 'export_start' } );

		data.push( { name: 'woe_nonce', value: woe_nonce } );

                if ( ( $( "#from_date" ).val() ) && ( $( "#to_date" ).val() ) ) {
			var d1 = new Date( $( "#from_date" ).val() );
			var d2 = new Date( $( "#to_date" ).val() );
			if ( d1.getTime() > d2.getTime() ) {
				alert( export_messages.wrong_date_range );
				return false;
			}
		}

		if ( $( '#order_fields > li' ).length == 0 )
		{
			alert( export_messages.no_fields );
			return false;
		}

                jQuery.ajax( {
			type: "post",
			data: data,
			cache: false,
			url: ajaxurl,
			dataType: "json",
			error: function( xhr, status, error ) {
				woe_show_error_message( xhr.responseText.replace(/<\/?[^>]+(>|$)/g, "") );
			},
			success: function( response ) {
				if (!response || typeof response['total'] == 'undefined') {
					woe_show_error_message(response);
					return;
				}
				window.count = response['total'];
				window.file_id = response['file_id'];
				console.log( window.count );

				if ( window.count > 0 )
					get_all( 0, 0, 'export_part' );
				else {
					alert( export_messages.no_results );
					reset_date_filter_for_cron();
				}
			}
		} );

		return false;
	} );
	$( "#save-btn" ).click( function() {
		if (!validateExport()) {
			return false;
		}
		setFormSubmitting();
		var data = 'json=' + makeJsonVar( $( '#export_job_settings' ) )
		data = data + "&action=order_exporter&method=save_settings&mode=" + mode + "&id=" + job_id + '&woe_nonce=' + woe_nonce;
		$.post( ajaxurl, data, function( response ) {
			document.location = settings_form.save_settings_url;
		}, "json" );
		return false;
	} );
	$( "#save-only-btn" ).click( function() {
		if (!validateExport()) {
			return false;
		}
		setFormSubmitting();
		var data = 'json=' + makeJsonVar( $( '#export_job_settings' ) )
		data = data + "&action=order_exporter&method=save_settings&mode=" + mode + "&id=" + job_id + '&woe_nonce=' + woe_nonce;
		$('#Settings_updated').hide();
		$.post( ajaxurl, data, function( response ) {
				$('#Settings_updated').show().delay(5000).fadeOut();
		}, "json" );
		return false;
	} );
	$( "#copy-to-profiles" ).click( function() {
		if (!validateExport()) {
			return false;
		}

		var data = 'json=' + makeJsonVar( $( '#export_job_settings' ) )
		data = data + "&action=order_exporter&method=save_settings&mode=" + settings_form.EXPORT_PROFILE + "&id=" + '&woe_nonce=' + woe_nonce;
		$.post( ajaxurl, data, function( response ) {
			document.location =settings_form.copy_to_profiles_url  + '&profile_id=' + response.id;
		}, "json" );
		return false;
	} );

	$( "#reset-profile" ).click( function () {
		if ( confirm( localize_settings_form.reset_profile_confirm ) ) {
			var data = "action=order_exporter&method=reset_profile&mode=" + mode + "&id=" + '&woe_nonce=' + woe_nonce;
			$.post( ajaxurl, data, function ( response ) {
				if (response.success) {
					document.location.reload();
				}
			}, "json" );
		}

		return false;
	} );

	openFilter('my-order', 1);

	openFilter('my-products');

	openFilter('my-shipping');

	openFilter('my-users');

	openFilter('my-coupons', 1);

	openFilter('my-billing');

	openFilter('my-items-meta');

	if ( mode == settings_form.EXPORT_SCHEDULE )
		setup_alert_date_filter();
	//for XLSX
	$('#format_xls_use_xls_format').click(function() {
		change_filename_ext();
	});

	show_summary_report( output_format );
	if( !summary_mode )
		jQuery('#summary_setup_fields').hide();

	//logic for setup link
	jQuery( "#summary_report_by_products_checkbox" ).change( function(e, action) {
		var summary_report_fields = [];
		summary_report_fields.push($('#products_unselected_segment input[value="plain_products_summary_report_total_qty"]').parents('li'));
		summary_report_fields.push($('#products_unselected_segment input[value="plain_products_summary_report_total_amount"]').parents('li'));

            jQuery('#manage_fields').toggleClass('summary-products-report', !!jQuery(this).prop('checked'));

            $('#unselected_fields .segment_choice').removeClass('active');
            $('#unselected_fields_list .settings-segment').removeClass('active');

            if (jQuery(this).prop('checked')) {
                var segment = 'products';

                // hide product fields starts with 'line' and 'qty'
	            $( '#products_unselected_segment input, #order_fields input' ).map( function () {
		            var matches = $( this ).attr( 'value' ).match( /plain_products_(line|qty).*/ );
		            if ( matches ) {
			            $( this ).closest( '.mapping_row' ).hide();
		            }
	            } );

	            if ( 'onstart' !== action ) {
		            // purge summary report fields before insert
		            $('#order_fields input[value="plain_products_summary_report_total_qty"]').closest('.mapping_row').remove();
		            $('#order_fields input[value="plain_products_summary_report_total_amount"]').closest('.mapping_row').remove();

		            // insert summary report fields
		            jQuery.each( summary_report_fields, function( i, value ) {
			            $(value).show();
			            var $field_to_copy = $(value).clone();
			            $field_to_copy
				            .attr('style', '')
				            .addClass('ui-draggabled')
				            .removeClass('segment_field')
				            .find('input').prop('disabled', false);

			            jQuery('#manage_fields #order_fields').append($field_to_copy);
		            } );
	            }

            } else {
                var segment = window.location.hash.replace('#segment=', '');

	            // show product fields starts with 'line' and 'qty'
	            $( '#products_unselected_segment input, #order_fields input' ).map( function () {
	            	var $value = $( this ).attr( 'value' );
	            	if ( typeof $value === 'undefined' ) {
	            		return;
		            }

		            if ( $value.match( /plain_products_(line|qty).*/ ) ) {
			            $( this ).closest( '.mapping_row' ).show();
		            }
	            } );

	            // purge summary report fields
	            $('#order_fields input[value="plain_products_summary_report_total_qty"]').closest('.mapping_row').remove();
	            $('#order_fields input[value="plain_products_summary_report_total_amount"]').closest('.mapping_row').remove();

	            jQuery.each( summary_report_fields, function( i, value ) {
		            $(value).hide();
	            } );
            }

            $('#unselected_fields .segment_choice[data-segment="'+ segment +'"]').addClass('active');
            $('#unselected_fields_list .settings-segment#'+ segment +'_unselected_segment').addClass('active');

	});

        setTimeout(function () {
           jQuery( "#summary_report_by_products_checkbox" ).trigger('change', 'onstart');
        }, 1)

	// this line must be last , we don't have any errors
	jQuery('#JS_error_onload').hide();

        jQuery('#order_fields').on('click', '.mapping_row-delete_field', function () {

            $(this).closest('.mapping_row').remove();

            check_sortable_groups();

            return false;
        });

        jQuery('.tab-controls .tab-actions-buttons .add-meta').on('click', function () {

            jQuery('.tab-actions-forms .segment-form').removeClass('active');

            if (jQuery('.tab-actions-forms .div_meta.segment-form.' +
                jQuery('#unselected_fields .segment_choice.active').attr('data-segment') + '-segment'
            ).length) {
                jQuery('.tab-actions-forms .div_meta.segment-form.' +
                    jQuery('#unselected_fields .segment_choice.active').attr('data-segment') + '-segment'
                ).addClass('active');
            } else {
                jQuery('.tab-actions-forms .div_meta.segment-form.all-segments').addClass('active');
            }

            return false;
        });

        jQuery('.tab-controls .tab-actions-buttons .add-custom').on('click', function () {

            jQuery('.tab-actions-forms .segment-form').removeClass('active');

            if (jQuery('.tab-actions-forms .div_custom.segment-form.' +
                jQuery('#unselected_fields .segment_choice.active').attr('data-segment') + '-segment'
            ).length) {
                jQuery('.tab-actions-forms .div_custom.segment-form.' +
                    jQuery('#unselected_fields .segment_choice.active').attr('data-segment') + '-segment'
                ).addClass('active');
            } else {
                jQuery('.tab-actions-forms .div_custom.segment-form.all-segments').addClass('active');
            }

            return false;
        });

        jQuery('.tab-controls .button-cancel').on('click', function () {

            jQuery(this).closest('.segment-form')
                    .removeClass('active')
                    .find('input,select').val('');

            return false;
        });

	init_image_uploaders();

} );

function is_flat_format(format) {
	return (settings_form.flat_formats.indexOf(format) > -1);
}
function is_object_format(format) {
	return (settings_form.object_formats.indexOf(format) > -1);
}
function is_xml_format(format) {
	return (settings_form.xml_formats.indexOf(format) > -1);
}
function reset_date_filter_for_cron() {
	if(mode == 'cron') {
		jQuery( "#from_date" ).val("");
		jQuery( "#to_date" ).val("");
		try_color_date_filter();
	}
}

function create_unselected_fields( old_output_format, format , format_changed, old_format_order_fields ) {

    var $unselected_fields_list = jQuery('#unselected_fields_list');

    var $unselected_segment_id = '%s_unselected_segment';

    var active_segment_id = $unselected_fields_list.find('.section.active').attr('id');

    $unselected_fields_list.html("");
    $unselected_fields_list.append( make_segments( $unselected_segment_id ) );

    if (active_segment_id) {
        jQuery('#unselected_fields_list #' + active_segment_id).addClass('active');
    }

    jQuery.each( window['all_fields'], function( segment, fields ) {

        fields.forEach(function (value) {

            var $unselected_field_segment = jQuery( '#' + sprintf( $unselected_segment_id, segment ) );
            var index = value.key;

            $unselected_field_segment.append(
                make_unselected_field( index, value, format, format_changed, segment )
            );

            activate_draggable_field(
                $unselected_field_segment.find('.segment_field'),
                segment,
                format
            );
        })

    });
}

function make_segments($segment_id) {

    var $segments_list = jQuery('<ul></ul>');

    jQuery.each( window['order_segments'], function( index, label ) {
        var $segment = jQuery('<div id="' + sprintf($segment_id, index)  + '" class="section settings-segment"></div>')
        $segments_list.append($segment);
    });

    return $segments_list;
}

function sprintf( format ) {
	for ( var i = 1; i < arguments.length; i ++ ) {
		format = format.replace( /%s/, arguments[i] );
	}
	return format;
}

function make_unselected_field($index, $field_data, $format, $format_changed, $segment ) {

    var label_part = '';
    var label_prefix = '';
    var value_part = '';

    var $mapping_col_1 = jQuery('<div class="mapping_col_1" style="width: 10px"></div>');

    var $mapping_col_2 = jQuery('<div class="mapping_col_2" title="'+escapeStr($index)+'"></div>');
    var $mapping_col_3 = jQuery('<div class="mapping_col_3"></div>');

    var colname = escapeStr($field_data.colname);

    var _index = $index;

    if ( is_flat_format($format) && ['products', 'coupons'].indexOf($segment) > -1 ) {
        _index = 'plain_' + $segment + '_' + $index;
    }

    if($format_changed) {
        if( is_flat_format( $format ) )
            colname = $field_data.label;
        else {

            colname = $index;

            if ( is_xml_format( $format ) )
                    colname = to_xml_tags( colname );
        }
    }

    if ( ! is_flat_format($format) && ['products', 'coupons'].indexOf($segment) > -1 ) {

        $mapping_col_1
                .append( make_input( 'hidden', null, $segment + '[][label]' , $field_data.label, false ) )
                .append( make_input( 'hidden', null, $segment + '[][format]' , $field_data.format, false ) )
                .append( make_input( 'hidden', null, $segment + '[][segment]' , $segment, false ) )
                .append( make_input( 'hidden', null, $segment + '[][key]' , $index, false ) );

        $mapping_col_3.append( make_input( 'input', 'mapping_fieldname', $segment + '[][colname]', colname ) );

        if ( $index.indexOf( 'static_field' ) >= 0 ) {
            value_part = '<div class="mapping_col_3 custom-field-value"><input class="mapping_fieldname" type=input name="' + $segment + '[][value]" value="' + $field_data.value + '"></div>';
        }

    } else {

        if ( $segment === 'products' ) {
                label_prefix = '[P] '
        }

        if ( $segment === 'coupons' ) {
                label_prefix = '[C] '
        }

        $mapping_col_1
                .append( make_input( 'hidden', null, 'orders[][segment]' , $segment, false ) )
                .append( make_input( 'hidden', null, 'orders[][key]' , _index, false ) )
                .append( make_input( 'hidden', null, 'orders[][label]' , $field_data.label, false ) )
                .append( make_input( 'hidden', null, 'orders[][format]' , $field_data.format, false ) );

        $mapping_col_3.append( make_input( 'input', 'mapping_fieldname', 'orders[][colname]', colname ) );

        if ( $index.indexOf( 'static_field' ) >= 0 ) {
            value_part = '<div class="mapping_col_3 custom-field-value"><input class="mapping_fieldname" type=input name="' + 'orders[][value]" value="' + $field_data.value + '"></div>';
        }

    }
    var delete_btn = '<div class="mapping_col_3 mapping_row-delete_field_block"><a href="#" class="mapping_row-delete_field"><span class="dashicons dashicons-trash"></span></a></div>';

    $mapping_col_2.append( '<span class="field-prefix">' + label_prefix + '</span>' + $field_data.label + label_part );

    if ( $index.charAt( 0 ) === '_'  || $index.substr( 0,3 ) === 'pa_' || !$field_data.default || $index.indexOf( 'static_field' ) > -1) {
            $mapping_col_2.append( '<a href="#" onclick="return remove_custom_field(this);" class="mapping_row-delete_custom_field" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a>' );
    }

    var $field = jQuery('<li class="mapping_row segment_field segment_'+ $segment +'"></li>');

    $field
        .append( $mapping_col_1 )
        .append( $mapping_col_2 )
        .append( $mapping_col_3 )
        .append( value_part )
        .append( delete_btn );

    $field.find('input').prop('disabled', 'disabled');

    return $field;
}

function make_input( $type, $classes, $name, $field_data, $is_checked ) {

    var $input = jQuery('<input>');

    $input.prop('type', $type);

    if ( $classes && jQuery.isArray($classes) ) {
        $input.addClass($classes.join(' '));
    }

    $input.prop('name', $name);
    $input.attr('value', $field_data);

    if ( $is_checked ) {
            $input.prop('checked', 'checked');
    }

    return $input;
}

function check_sortable_groups () {
    jQuery('#sortable_products').closest('.mapping_row').toggle(!!jQuery('#sortable_products li').length);
    jQuery('#sortable_coupons').closest('.mapping_row').toggle(!!jQuery('#sortable_coupons li').length);
}

function activate_draggable_field (el, segment, format) {

    var no_flat_sortable_selector = '#manage_fields #order_fields #sortable_' + segment;
    var flat_sortable_selector    = '#manage_fields #order_fields';

    el.draggable({
        connectToSortable: [no_flat_sortable_selector, flat_sortable_selector].join(','),
        helper: "clone",
        revert: "invalid",
	    start: function ( event, ui ) {
		    jQuery(ui.helper[0]).removeClass( 'blink' );
	    },
        stop: function ( event, ui ) {
	        el.removeClass( 'blink' );

            var moved_to_sortable      = jQuery(ui.helper[0]).closest(flat_sortable_selector).length;
            var move_to_sortable_group = jQuery(ui.helper[0]).closest(no_flat_sortable_selector).length;

            if (!moved_to_sortable) {
                return;
            }

            moving_products_and_coupons_group_blocks_to_first_item(format);

	        // change static field key index to prevent fields with identical keys
	        var tmp_prefix = ['products', 'coupons'].indexOf(segment) === -1 ? '' : 'plain_' + segment + '_';
	        if ( jQuery(ui.helper[0]).find('input[value*="' + tmp_prefix + 'static_field"]').length > 0 ) {
		        var suffix = 0;
		        jQuery('#order_fields input[value*="' + tmp_prefix + 'static_field_"]').each(function () {

			        var match = jQuery(this).attr('value').match(/.*static_field_(\d+)/);

			        if (!match) {
				        return true;
			        }

			        var n = parseInt(match[1]);

			        if(n > suffix) {
				        suffix = n;
			        }
		        });

		        var field_key = tmp_prefix + 'static_field_' + (suffix + 1);
		        jQuery(ui.helper[0]).find('input[name="orders[][key]"]').first().val(field_key);
	        }
            // end change static field key

            var moving_copy_original_el = jQuery(ui.helper[0]);

            moving_copy_original_el
                .attr('style', '')
                .addClass('ui-draggabled')
                .removeClass('segment_field')
                .find('input').prop('disabled', false);


            if (is_flat_format(format) || move_to_sortable_group || ['products', 'coupons'].indexOf(segment) === -1) {
                return;
            }

            jQuery(no_flat_sortable_selector).append(moving_copy_original_el.clone());

            moving_copy_original_el.remove();

            check_sortable_groups();
        },
  });

}

function moving_products_and_coupons_group_blocks_to_first_item(format) {

    if ( is_flat_format ( format ) ) {

        var first_products_field = jQuery('#order_fields [value*="plain_products_"]').first().closest('li');
        var first_coupons_field  = jQuery('#order_fields [value*="plain_coupons_"]').first().closest('li');

        if (first_products_field.length) {
            var products_group_block = jQuery('#order_fields .flat-products-group').clone();
            jQuery('#order_fields .flat-products-group').remove();
            first_products_field.before(products_group_block);
        }

        if (first_coupons_field.length) {
            var coupons_group_block = jQuery('#order_fields .flat-coupons-group').clone();
            jQuery('#order_fields .flat-coupons-group').remove();
            first_coupons_field.before(coupons_group_block);
        }

        return;
    }

    var first_products_field = jQuery('#order_fields [name="products[][key]"]').first().closest('li');

    if (!jQuery('#sortable_products > li').length && first_products_field.length) {
        var products_group_block = jQuery('#order_fields .flat-products-group').clone();
        jQuery('#order_fields .flat-products-group').remove();
        first_products_field.before(products_group_block);
    }

    var first_coupons_field  = jQuery('#order_fields [name="coupons[][key]"]').first().closest('li');

    if (!jQuery('#sortable_coupons > li').length && first_coupons_field.length) {
        var coupons_group_block = jQuery('#order_fields .flat-coupons-group').clone();
        jQuery('#order_fields .flat-coupons-group').remove();
        first_coupons_field.before(coupons_group_block);
    }
}

function synch_selected_fields (old_format, new_format) {

    var settings = jQuery('#export_job_settings').serializeJSON();

    if (is_flat_format(old_format) && is_flat_format(new_format)) {
        window['selected_order_fields']             = settings.orders || [];
        window['selected_order_products_fields']    = [];
        window['selected_order_coupons_fields']     = [];
        return;
    }

    if (!is_flat_format(old_format) && !is_flat_format(new_format)) {
        window['selected_order_fields']             = settings.orders || [];
        window['selected_order_products_fields']    = settings.products || [];
        window['selected_order_coupons_fields']     = settings.coupons || [];
        return;
    }

    if (is_flat_format(old_format) && !is_flat_format(new_format)) {

        var products = [];
        var coupons  = [];
        var orders   = [];

        (settings.orders || []).forEach(function (item) {

            if (item.key.indexOf('plain_products') > -1) {
                item.key = item.key.replace('plain_products_', '');
                products.push(item);
                return true;
            }

            if (item.key.indexOf('plain_coupons') > -1) {
                item.key = item.key.replace('plain_coupons_', '');
                coupons.push(item);
                return true;
            }

            orders.push(item);
        });

        window['selected_order_fields']           = orders;
        window['selected_order_products_fields']  = products;
        window['selected_order_coupons_fields']   = coupons;

        return;
    }

    if (!is_flat_format(old_format) && is_flat_format(new_format)) {

        var products = [];
        var coupons  = [];
        var orders   = [];

        (settings.products || []).forEach(function (item) {
            item.key     = 'plain_products_' + item.key;
            products.push(item);
        });

        (settings.coupons || []).forEach(function (item) {
            item.key     = 'plain_coupons_' + item.key;
            coupons.push(item);
        });

        (settings.orders || []).forEach(function (item) {

            orders.push(item);

            if (item.key === 'products') {
                orders = orders.concat(products);
            }

            if (item.key === 'coupons') {
                orders = orders.concat(coupons);
            }
        });

        window['selected_order_fields']           = orders;
        window['selected_order_products_fields']  = [];
        window['selected_order_coupons_fields']   = [];

        return;
    }

}

function woe_show_error_message(text) {
	if (!text)
		 text = "Please, open section 'Misc Settings' and \n mark checkbox 'Enable debug output' \n to see exact error message";
	alert(text);
}

function init_image_uploaders() {
	var custom_uploader;
	jQuery( '.image-upload-button' ).click( function ( e ) {
		e.preventDefault();
		if ( custom_uploader ) {
			custom_uploader.open();
			return;
		}

		custom_uploader = wp.media.frames.file_frame = wp.media( {
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		} );

		var self = this;
		custom_uploader.on( 'select', function () {
			attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
			jQuery( self ).siblings( 'input[type="hidden"]' ).val( attachment.url );
			jQuery( self ).siblings( 'img' ).attr( 'src', attachment.url ).removeClass('hidden');
			jQuery( self ).siblings( '.image-clear-button' ).removeClass('hidden');
		} );

		custom_uploader.open();
	} );

	jQuery( '.image-clear-button' ).click( function ( e ) {
		jQuery( this ).siblings( 'input[type="hidden"]' ).val( '' );
		jQuery( this ).siblings( 'img' ).attr( 'src', '' ).addClass( 'hidden' );
		jQuery( this ).addClass( 'hidden' );
	} );

	return custom_uploader;
}
