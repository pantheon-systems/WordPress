var select2WODropdownOpts = {
    containerCssClass : 'without-dropdown',
    dropdownCssClass: 'without-dropdown',
};

String.prototype.hashCode = function() {
	var hash = 0, i, chr;
	if (this.length === 0) return hash;
	for (i = 0; i < this.length; i++) {
		chr   = this.charCodeAt(i);
		hash  = ((hash << 5) - hash) + chr;
		hash |= 0; // Convert to 32bit integer
	}
	return hash;
};
var formSubmitting = false;

var setFormSubmitting = function() { formSubmitting = true; };

window.onload = function () {
    var form = jQuery( '#export_job_settings' );
	var on_load_form_data = form.serialize();

	var isDirty = function ( on_load_form_data ) {
		return on_load_form_data.hashCode() !== form.serialize().hashCode()
	};

	window.addEventListener( "beforeunload", function ( e ) {
		if ( isDirty( on_load_form_data ) && ! formSubmitting ) {
			(
				e || window.event
			).returnValue = false; //Gecko + IE
			return false; //Gecko + Webkit, Safari, Chrome etc.
		} else {
			return undefined;
		}
	} );

	//force style for popup!
//	var style = jQuery('<style>#TB_ajaxContent { overflow: auto !important; }</style>');
//    jQuery('html > head').append(style);
};

function bind_events() {

	// for filter by ORDER custom fields
    jQuery( '#custom_fields' ).change( function() {

        jQuery( '#select_custom_fields' ).attr( 'disabled', 'disabled' );
        var data = {
            'cf_name': jQuery( this ).val(),
            method: "get_order_custom_fields_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };
        var val_op = jQuery( '#custom_fields_compare' ).val();
        jQuery( '#text_custom_fields' ).val( '' );
        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_custom_fields' ).remove();
            jQuery( '#select_custom_fields--select2 select' ).select2('destroy');
            jQuery( '#select_custom_fields, #select_custom_fields--select2' ).remove();
            if ( response ) {
                var options = '<option>' + export_messages.empty + '</option>';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                var $select = jQuery( '<div id="select_custom_fields--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top;'
                    + 'display: ' + (('LIKE' === val_op || 'NOT SET' === val_op|| 'IS SET' === val_op) ? 'none' : 'inline-block') + ';">'
                    + '<select id="select_custom_fields">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_custom_fields' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="select_custom_fields" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_custom_fields' ) );
            }
	        jQuery( '#custom_fields_compare').trigger('change');
        }, 'json' );
    } );
    jQuery( '#add_custom_fields' ).click( function() {

        var val = !jQuery( "#select_custom_fields" ).is(':disabled') ? jQuery( "#select_custom_fields" ).val() : jQuery( "#text_custom_fields" ).val();
        var val2 = jQuery( '#custom_fields' ).val();
        var val_op = jQuery( '#custom_fields_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            var result = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#custom_fields_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {
                if ( export_messages.empty === val ) {
	                result = val2 + ' ' + val_op + ' empty';
	                jQuery( '#custom_fields_check' ).append( '<option selected="selected" value="' + result + '">' + result + '</option>' );
                } else {
	                jQuery( '#custom_fields_check' ).append( '<option selected="selected" value="' + result + '">' + result + '</option>' );
                }

                jQuery( '#custom_fields_check' ).select2_i18n();

                jQuery( '#custom_fields_check option' ).each( function() {
                    jQuery( '#custom_fields_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_custom_fields" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#custom_fields_compare').change(function() {
        var val_op = jQuery( '#custom_fields_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#select_custom_fields--select2" ).hide();
            jQuery( "#text_custom_fields" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else if ( 'NOT SET' === val_op || 'IS SET' === val_op ) {
            jQuery( "#select_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' ) . val(' ');
            jQuery( "#select_custom_fields--select2" ).hide();
            jQuery( "#text_custom_fields" ).css('display', 'none' ).attr( 'disabled', false ). val(' ');
        }
        else {
            jQuery( "#select_custom_fields" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( '#select_custom_fields--select2' ).css('display', 'inline' );
            jQuery( "#text_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
	//end of change

	// for filter by USER custom fields
	bind_events_users();

    //PRODUCT ATTRIBUTES BEGIN
    jQuery( '#attributes' ).change( function() {

        jQuery( '#select_attributes' ).attr( 'disabled', 'disabled' );
        var data = {
            'attr': jQuery( this ).val(),
            method: "get_products_attributes_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        var val_op = jQuery( '#attributes_compare' ).val();
        jQuery( '#text_attributes' ).val( '' );

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_attributes--select2 select' ).select2('destroy');
            jQuery( '#select_attributes, #select_attributes--select2' ).remove();

            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                var $select = jQuery( '<div id="select_attributes--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top;'
                    + 'display: ' + (('LIKE' === val_op) ? 'none' : 'inline-block') + ';">'
                    + '<select id="select_attributes">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_attributes' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="select_attributes" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_attributes' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_attributes' ).click( function() {

        var val = !jQuery( "#select_attributes" ).is(':disabled') ? jQuery( "#select_attributes" ).val() : jQuery( "#text_attributes" ).val();
        var val2 = jQuery( '#attributes' ).val();
        var val_op = jQuery( '#attributes_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#attributes_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#attributes_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#attributes_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#attributes_check option' ).each( function() {
                    jQuery( '#attributes_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_attributes" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#attributes_compare').change(function() {
        var val_op = jQuery( '#attributes_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_attributes" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#select_attributes--select2" ).hide();
            jQuery( "#text_attributes" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_attributes" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#select_attributes--select2" ).css('display', 'inline' );
            jQuery( "#text_attributes" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
    //PRODUCT ATTRIBUTES END

    jQuery( '#itemmeta' ).change( function() {
		var selected64 = jQuery( this ).find(":selected").data("base64");

        jQuery( '#select_itemmeta' ).attr( 'disabled', 'disabled' );
        var data = {
            'item': window.atob(selected64),
            method: "get_products_itemmeta_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        var val_op = jQuery( '#itemmeta_compare' ).val();
        jQuery( '#text_itemmeta' ).val( '' );

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_itemmeta--select2 select' ).select2('destroy');
            jQuery( '#select_itemmeta, #select_itemmeta--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                var $select = jQuery( '<div id="select_itemmeta--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top;'
                    + 'display: ' + (('LIKE' === val_op) ? 'none' : 'inline-block') + ';">'
                    + '<select id="select_itemmeta">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_itemmeta' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="select_itemmeta" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_itemmeta' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_itemmeta' ).click( function() {

        var val = !jQuery( "#select_itemmeta" ).is(':disabled') ? jQuery( "#select_itemmeta" ).val() : jQuery( "#text_itemmeta" ).val();
		var selected64 = jQuery( '#itemmeta' ).find(":selected").data("base64");
        var val2 = window.atob(selected64).replace(/&/g,'&amp;');
        var val_op = jQuery( '#itemmeta_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#itemmeta_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#itemmeta_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#itemmeta_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#itemmeta_check option' ).each( function() {
                    jQuery( '#itemmeta_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove(); // jQuerySelectorEscape ?
                } );

                jQuery( "input#select_itemmeta" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#itemmeta_compare').change(function() {
        var val_op = jQuery( '#itemmeta_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_itemmeta" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#select_itemmeta--select2" ).hide();
            jQuery( "#text_itemmeta" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_itemmeta" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#select_itemmeta--select2" ).css('display', 'inline' );
            jQuery( "#text_itemmeta" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });

    //PRODUCT TAXONOMIES BEGIN
    jQuery( '#taxonomies' ).change( function() {

        jQuery( '#select_taxonomies' ).attr( 'disabled', 'disabled' );
        var data = {
            'tax': jQuery( this ).val(),
            method: "get_products_taxonomies_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_taxonomies--select2 select' ).select2('destroy');
            jQuery( '#select_taxonomies, #select_taxonomies--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                var $select = jQuery( '<div id="select_taxonomies--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top; display: inline-block;">'
                    + '<select id="select_taxonomies">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_taxonomies' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="select_taxonomies" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_taxonomies' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_taxonomies' ).click( function() {

        var val = !jQuery( "#select_taxonomies" ).is(':disabled') ? jQuery( "#select_taxonomies" ).val() : jQuery( "#text_taxonomies" ).val();
        var val2 = jQuery( '#taxonomies' ).val();
        var val_op = jQuery( '#taxonomies_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#taxonomies_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#taxonomies_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#taxonomies_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#taxonomies_check option' ).each( function() {
                    jQuery( '#taxonomies_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_taxonomies" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#taxonomies_compare').change(function() {
        var val_op = jQuery( '#taxonomies_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_taxonomies" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#text_taxonomies" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_taxonomies" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#text_taxonomies" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
    //PRODUCT TAXONOMIES END

	// for filter by PRODUCT custom fields
    jQuery( '#product_custom_fields' ).change( function() {

        jQuery( '#select_product_custom_fields' ).attr( 'disabled', 'disabled' );
        var data = {
            'cf_name': jQuery( this ).val(),
            method: "get_product_custom_fields_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        var val_op = jQuery( '#product_custom_fields_compare' ).val();
        jQuery( '#text_product_custom_fields' ).val( '' );

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_product_custom_fields--select2 select' ).select2('destroy');
            jQuery( '#select_product_custom_fields, #select_product_custom_fields--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                var $select = jQuery( '<div id="select_product_custom_fields--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top;'
                    + 'display: ' + (('LIKE' === val_op) ? 'none' : 'inline-block') + ';">'
                    + '<select id="select_product_custom_fields">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_product_custom_fields' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="select_product_custom_fields" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_product_custom_fields' ) );
            }
        }, 'json' );
    } );
    jQuery( '#add_product_custom_fields' ).click( function() {

        var val = !jQuery( "#select_product_custom_fields" ).is(':disabled') ? jQuery( "#select_product_custom_fields" ).val() : jQuery( "#text_product_custom_fields" ).val();
        var val2 = jQuery( '#product_custom_fields' ).val();
        var val_op = jQuery( '#product_custom_fields_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#product_custom_fields_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#product_custom_fields_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#product_custom_fields_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#product_custom_fields_check option' ).each( function() {
                    jQuery( '#product_custom_fields_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_product_custom_fields" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#product_custom_fields_compare').change(function() {
        var val_op = jQuery( '#product_custom_fields_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_product_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#select_product_custom_fields--select2" ).hide();
            jQuery( "#text_product_custom_fields" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_product_custom_fields" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#select_product_custom_fields--select2" ).css('display', 'inline' );
            jQuery( "#text_product_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
	//end of change


    jQuery( '#orders_add_custom_field' ).click( function() {
        jQuery( "#fields_control > div" ).hide();
        jQuery( "#fields_control .div_custom" ).show();

        //add_custom_field(jQuery("#order_fields"),'products','CSV');
        return false;
    } );
    jQuery( '#orders_add_custom_meta' ).click( function() {
		jQuery('#custom_meta_order_mode_used').attr('checked', false);
		jQuery('#custom_meta_order_mode_used').change();
        jQuery( "#fields_control > div" ).hide();
        jQuery( "#fields_control .div_meta" ).show();

        //add_custom_field(jQuery("#order_fields"),'products','CSV');
        return false;
    } );

    jQuery( '.button_cancel' ).click( function() {
        reset_field_contorls();
        return false;
    } );

///*CUSTOM FIELDS BINDS
    jQuery( '#button_custom_field' ).click( function() {
        var colname = jQuery( '#colname_custom_field' ).val();
        var value = jQuery( '#value_custom_field' ).val();
        var format_field = jQuery( '#format_custom_field' ).val();
        if ( !colname )
        {
            alert( export_messages.empty_column_name );
			jQuery( '#colname_custom_field' ).focus();
            return false
        }

        var segment = jQuery('.segment_choice.active').attr('data-segment');

        add_custom_field( jQuery( "#" + segment + '_unselected_segment' ), 'orders', output_format, colname, value, segment, format_field );

        reset_field_contorls();

        jQuery(this).siblings('.button-cancel').trigger('click');

        return false;
    } );

    jQuery('input[name=custom_meta_order_mode]').change(function() {
        if ( !jQuery(this).prop('checked') ) {
            var options = '<option></option>';
            jQuery.each( window.order_custom_meta_fields, function( index, value ) {
                options += '<option value="' + escapeStr(value) + '">' + value + '</option>';
            } );
            jQuery( '#select_custom_meta_order' ).html( options );
        }
        else {
            var json = makeJsonVar(jQuery( '#export_job_settings' ));
            var data = "json="+ json +"&action=order_exporter&method=get_used_custom_order_meta&woe_nonce=" + woe_nonce;

            jQuery.post( ajaxurl, data, function( response ) {
                if ( response ) {
                    var options = '<option></option>';
                    jQuery.each( response, function( index, value ) {
                        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
                    } );
                    jQuery( '#select_custom_meta_order' ).html( options );
                }
            }, 'json' );
        }
    });

    jQuery('input[name=custom_meta_products_mode]').change(function() {
	    jQuery( '#select_custom_meta_products' ).prop( "disabled", true );
	    jQuery( '#select_custom_meta_order_items' ).prop( "disabled", true );
        if ( !jQuery( this ).is( ':checked' ) ) {
            var options = '<option></option>';
            jQuery.each( window.order_products_custom_meta_fields, function( index, value ) {
	            options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
            } );
            jQuery( '#select_custom_meta_products' ).html( options );
	        jQuery( '#select_custom_meta_products' ).prop( "disabled", false );

	        options = '<option></option>';
	        jQuery.each( window.order_order_item_custom_meta_fields, function( index, value ) {
		        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
	        } );
	        jQuery( '#select_custom_meta_order_items' ).html( options );
	        jQuery( '#select_custom_meta_order_items' ).prop( "disabled", false );
        }
        else {
//            jQuery('#modal-manage-products').html(jQuery('#TB_ajaxContent').html());
            var data = jQuery( '#export_job_settings' ).serialize(),
                data_products = data + "&action=order_exporter&method=get_used_custom_products_meta&mode=" + mode + "&id=" + job_id + '&woe_nonce=' + woe_nonce;
                data_order_items = data + "&action=order_exporter&method=get_used_custom_order_items_meta&mode=" + mode + "&id=" + job_id + '&woe_nonce='+ woe_nonce;

            jQuery.post( ajaxurl, data_products, function( response ) {
                if ( response ) {
                    var options = '<option></option>';
                    jQuery.each( response, function( index, value ) {
                        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
                    } );
                    jQuery( '#select_custom_meta_products' ).html( options );
	                jQuery( '#select_custom_meta_products' ).prop( "disabled", false );
                }
            }, 'json' );

	        jQuery.post( ajaxurl, data_order_items, function( response ) {
		        if ( response ) {
			        var options = '<option></option>';
			        jQuery.each( response, function( index, value ) {
				        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
			        } );
			        jQuery( '#select_custom_meta_order_items' ).html( options );
			        jQuery( '#select_custom_meta_order_items' ).prop( "disabled", false );
		        }
	        }, 'json' );

//            jQuery('#modal-manage-products').html('');
        }
    });
	jQuery('input[name=custom_meta_products_mode]').trigger('change');

    jQuery('input[name=custom_meta_coupons_mode]').change(function() {
        if (jQuery(this).val() == 'all') {
            var options = '<option></option>';
            jQuery.each( window.order_coupons_custom_meta_fields, function( index, value ) {
                options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
            } );
            jQuery( '#select_custom_meta_coupons' ).html( options );
        }
        else {
            var data = jQuery( '#export_job_settings' ).serialize()
            data = data + "&action=order_exporter&method=get_used_custom_coupons_meta&woe_nonce=" + woe_nonce;

            jQuery.post( ajaxurl, data, function( response ) {
                if ( response ) {
                    var options = '<option></option>';
                    jQuery.each( response, function( index, value ) {
                        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
                    } );
                    jQuery( '#select_custom_meta_coupons' ).html( options );
                }
            }, 'json' );
        }
    });

    jQuery( '#button_custom_meta' ).click( function() {
        var label = jQuery( '#select_custom_meta_order' ).val();
        var colname = jQuery( '#colname_custom_meta' ).val();
        var format_field = jQuery( '#format_custom_meta' ).val();
		if (! label) //try custom text
			label = jQuery( '#text_custom_meta_order' ).val();;
        if ( !label )
        {
            alert( export_messages.empty_meta_key );
			jQuery( '#select_custom_meta_order' ).focus();
            return false
        }
        if ( !colname )
        {
            alert( export_messages.empty_column_name );
			jQuery( '#colname_custom_meta' ).focus();
            return false
        }

        var segment = jQuery('.segment_choice.active').attr('data-segment');

        add_custom_meta( jQuery( "#" + segment + '_unselected_segment' ), 'orders', output_format, label, colname, segment, format_field );

        reset_field_contorls();

        jQuery(this).siblings('.button-cancel').trigger('click');

        return false;
    } );

/////////////END CUSTOM FIELDS BINDS

    // SHIPPING LOCATIONS
    jQuery( '#shipping_locations' ).change( function() {

        jQuery( '#text_shipping_locations' ).attr( 'disabled', 'disabled' );
        var data = {
            'item': jQuery( this ).val(),
            method: "get_order_shipping_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#text_shipping_locations--select2 select' ).select2('destroy');
            jQuery( '#text_shipping_locations, #text_shipping_locations--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );

                var $select = jQuery( '<div id="text_shipping_locations--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top; display: inline-block;"><select id="text_shipping_locations">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_shipping_locations' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="text_shipping_locations" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_shipping_locations' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_shipping_locations' ).click( function() {

        var val = jQuery( "#text_shipping_locations" ).val();
        var val2 = jQuery( '#shipping_locations' ).val();
        var val_op = jQuery( '#shipping_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + val_op + val;

            var f = true;
            jQuery( '#shipping_locations_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#shipping_locations_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#shipping_locations_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#shipping_locations_check option' ).each( function() {
                    jQuery( '#shipping_locations_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#text_shipping_locations" ).val( '' );
            }
        }
        return false;
    } );

    // BILLING LOCATIONS
    jQuery( '#billing_locations' ).change( function() {

        jQuery( '#text_billing_locations' ).attr( 'disabled', 'disabled' );
        var data = {
            'item': jQuery( this ).val(),
            method: "get_order_billing_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#text_billing_locations--select2 select' ).select2('destroy');
            jQuery( '#text_billing_locations, #text_billing_locations--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                var $select = jQuery( '<div id="text_billing_locations--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top; display: inline-block;">'
                    + '<select id="text_billing_locations">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_billing_locations' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="text_billing_locations" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_billing_locations' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_billing_locations' ).click( function() {

        var val = jQuery( "#text_billing_locations" ).val();
        var val2 = jQuery( '#billing_locations' ).val();
        var val_op = jQuery( '#billing_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + val_op + val;

            var f = true;
            jQuery( '#billing_locations_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#billing_locations_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#billing_locations_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#billing_locations_check option' ).each( function() {
                    jQuery( '#billing_locations_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#text_billing_locations" ).val( '' );
            }
        }
        return false;
    } )


    // ITEM NAMES
    jQuery( '#item_names' ).change( function() {

        jQuery( '#text_item_names' ).attr( 'disabled', 'disabled' );
        var data = {
            'item_type': jQuery( this ).val(),
            method: "get_order_item_names",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#text_item_names--select2 select' ).select2('destroy');
            jQuery( '#text_item_names, #text_item_names--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );

                var $select = jQuery( '<div id="text_item_names--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top; display: inline-block;"><select id="text_item_names">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_item_names' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="text_item_names" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_item_names' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_item_names' ).click( function() {

        var val = jQuery( "#text_item_names" ).val();
        var val2 = jQuery( '#item_names' ).val();
        var val_op = jQuery( '#item_name_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + val_op + val;

            var f = true;
            jQuery( '#item_names_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#item_names_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#item_names_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#item_names_check option' ).each( function() {
                    jQuery( '#item_names_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#text_item_names" ).val( '' );
            }
        }
        return false;
    } );

    // ITEM METADATA
    jQuery( '#item_metadata' ).change( function() {

        jQuery( '#text_item_metadata' ).attr( 'disabled', 'disabled' );
        var data = {
            'meta_key': jQuery( this ).val(),
            method: "get_order_item_meta_key_values",
            action: "order_exporter",
            woe_nonce: woe_nonce,
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#text_item_metadata--select2 select' ).select2('destroy');
            jQuery( '#text_item_metadata, #text_item_metadata--select2' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );

                var $select = jQuery( '<div id="text_item_metadata--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top; display: inline-block;"><select id="text_item_metadata">' + options + '</select></div>' );
                $select.insertBefore( jQuery( '#add_item_metadata' ) )
                $select.find('select').select2_i18n({ tags: true });
            }
            else {
                jQuery( '<input type="text" id="text_item_metadata" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_item_metadata' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_item_metadata' ).click( function() {

        var val = jQuery( "#text_item_metadata" ).val();
        var val2 = jQuery( '#item_metadata' ).val();
        var val_op = jQuery( '#item_metadata_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + val_op + val;

            var f = true;
            jQuery( '#item_metadata_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#item_metadata_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#item_metadata_check' ).select2_i18n(select2WODropdownOpts);

                jQuery( '#item_metadata_check option' ).each( function() {
                    jQuery( '#item_metadata_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#text_item_metadata" ).val( '' );
            }
        }
        return false;
    } );

}

function bind_events_users() {
	// for filter by ORDER custom fields
	jQuery( '#user_custom_fields' ).change( function () {

		jQuery( '#select_user_custom_fields' ).attr( 'disabled', 'disabled' );
		var data = {
			'cf_name': jQuery( this ).val(),
			method: "get_user_custom_fields_values",
			action: "order_exporter",
                        woe_nonce: woe_nonce,
		};
		var val_op = jQuery( '#select_user_custom_fields' ).val();
		jQuery( '#text_user_custom_fields' ).val( '' );
		jQuery.post( ajaxurl, data, function ( response ) {
			jQuery( '#select_user_custom_fields' ).remove();
			jQuery( '#select_user_custom_fields--select2 select' ).select2( 'destroy' );
			jQuery( '#select_user_custom_fields, #select_user_custom_fields--select2' ).remove();
			if ( response ) {
				var options = '<option>' + export_messages.empty + '</option>';
				jQuery.each( response, function ( index, value ) {
					options += '<option>' + value + '</option>';
				} );
				var $select = jQuery( '<div id="select_user_custom_fields--select2" style="margin-top: 0px;margin-right: 6px; vertical-align: top;'
				                      + 'display: ' + (
					                      (
						                      'LIKE' === val_op || 'NOT SET' === val_op || 'IS SET' === val_op
					                      ) ? 'none' : 'inline-block'
				                      ) + ';">'
				                      + '<select id="select_user_custom_fields">' + options + '</select></div>' );
				$select.insertBefore( jQuery( '#add_user_custom_fields' ) )
				$select.find( 'select' ).select2_i18n( {tags: true} );
			}
			else {
				jQuery( '<input type="text" id="select_user_custom_fields" style="margin-right: 8px;">' ).insertBefore(
					jQuery( '#add_user_custom_fields' ) );
			}
		}, 'json' );
	} );
	jQuery( '#add_user_custom_fields' ).click( function () {

		var val = ! jQuery( "#select_user_custom_fields" ).is( ':disabled' ) ? jQuery(
			"#select_user_custom_fields" ).val() : jQuery( "#text_user_custom_fields" ).val();
		var val2 = jQuery( '#user_custom_fields' ).val();
		var val_op = jQuery( '#user_custom_fields_compare' ).val();
		if ( val != null && val2 != null && val.length && val2.length ) {
			var result = val2 + ' ' + val_op + ' ' + val;

			var f = true;
			jQuery( '#user_custom_fields_check' ).next().find( 'ul li' ).each( function () {
				if ( jQuery( this ).attr( 'title' ) == val ) {
					f = false;
				}
			} );

			if ( f ) {
				if ( export_messages.empty === val ) {
					result = val2 + ' ' + val_op + ' empty';
					jQuery(
						'#user_custom_fields_check' ).append( '<option selected="selected" value="' + result + '">' + result + '</option>' );
				} else {
					jQuery(
						'#user_custom_fields_check' ).append( '<option selected="selected" value="' + result + '">' + result + '</option>' );
				}

				jQuery( '#user_custom_fields_check' ).select2_i18n();

				jQuery( '#user_custom_fields_check option' ).each( function () {
					jQuery( '#user_custom_fields_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
				} );

				jQuery( "input#select_user_custom_fields" ).val( '' );
			}
		}
        return false;
	} );

	jQuery( '#user_custom_fields_compare').change(function() {
		var val_op = jQuery( '#user_custom_fields_compare' ).val();
		if ( 'LIKE' === val_op ) {
			jQuery( "#select_user_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
			jQuery( "#select_user_custom_fields--select2" ).hide();
			jQuery( "#text_user_custom_fields" ).css('display', 'inline' ).attr( 'disabled', false );
		}
		else if ( 'NOT SET' === val_op || 'IS SET' === val_op ) {
			jQuery( "#select_user_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' ) . val(' ');
			jQuery( "#select_user_custom_fields--select2" ).hide();
			jQuery( "#text_user_custom_fields" ).css('display', 'none' ).attr( 'disabled', false ). val(' ');
		}
		else {
			jQuery( "#select_user_custom_fields" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
			jQuery( '#select_user_custom_fields--select2' ).css('display', 'inline' );
			jQuery( "#text_user_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
		}
	});
}

function add_bind_for_custom_fields( prefix, output_format, $to ) {
    jQuery( '#button_custom_field_' + prefix + '' ).off();
    jQuery( '#button_custom_field_' + prefix + '' ).click( function() {
        var colname = jQuery( '#colname_custom_field_' + prefix + '' ).val();
        var value = jQuery( '#value_custom_field_' + prefix + '' ).val();
        var format_field = jQuery( '#format_custom_field_' + prefix + '' ).val();
        if ( !colname )
        {
            alert( export_messages.empty_column_name );
			jQuery( '#colname_custom_field_' + prefix + '' ).focus();
            return false
        }
        if ( !value && 'products' !== prefix )
        {
            alert( export_messages.empty_value );
			jQuery( '#value_custom_field_' + prefix + '' ).focus();
            return false
        }

        jQuery( '#colname_custom_field_' + prefix + '' ).val( "" );

        jQuery( '#value_custom_field_' + prefix + '' ).val( "" );
	    jQuery( '#format_custom_field_' + prefix + '' ).val( "" );

        var segment = jQuery('.segment_choice.active').attr('data-segment');

        add_custom_field( jQuery( "#" + segment + '_unselected_segment' ), prefix, output_format, colname, value, segment, format_field );

        jQuery(this).siblings('.button-cancel').trigger('click');

        return false;
    } );

	jQuery( '#button_custom_meta_' + prefix + '' ).off();
	jQuery( '#button_custom_meta_' + prefix + '' ).click( function() {
		var prefix_items = 'order_items',
            original_prefix = prefix,
			prefix_items_select = jQuery( '#select_custom_meta_' + prefix_items + '' ),
			prefix_product_select = jQuery( '#select_custom_meta_' + prefix + '' ),
			prefix_product_text = jQuery( '#text_custom_meta_' + prefix + '' ),
			prefix_items_text = jQuery( '#text_custom_meta_' + prefix_items + '' );

		var type = (
                    prefix_items_select.val() ||
                    prefix_product_select.val() ||
                    prefix_product_text.val() ||
                    prefix_items_text.val()
                ) ? 'meta' : 'taxonomies';

		if ( 'meta' === type ) {
			original_prefix = prefix_product_select.val() || prefix_product_text.val() ? prefix : prefix_items;
		} else {
			original_prefix = prefix;
        }
		type = type + '_' + original_prefix;
		var label = jQuery( '#select_custom_' + type + '' ).val();
		var colname = jQuery( '#colname_custom_meta_' + prefix + '' ).val();
		var field_format = jQuery( '#format_custom_meta_' + prefix + '' ).val();

                if (! label ) //try custom text
                    label = jQuery( '#text_custom_' + type ).val();

		if ( !label )
		{
			alert( export_messages.empty_meta_key_and_taxonomy );
			return false
		}
		if ( colname == undefined || colname == '' ) {
			colname = label;
		}
		if ( !colname )
		{
			alert( export_messages.empty_column_name );
			return false
		}

                var segment = jQuery('.segment_choice.active').attr('data-segment');

                add_custom_meta( jQuery( "#" + segment + '_unselected_segment' ), prefix, output_format, label, colname, segment, field_format );

                jQuery(this).siblings('.button-cancel').trigger('click');

		jQuery( '#select_custom_' + type + '' ).val( "" );
		jQuery( '#colname_custom_meta_' + prefix + '' ).val( "" );
		jQuery( '#format_custom_meta_' + prefix + '' ).val( "" );
		return false;
	} );

}

function reset_field_contorls() {
    jQuery( '.tab-actions-forms' )
        .find( 'input,select' )
        .val( '' );
}

function reset_field_contorls_() {
    jQuery( '#fields_control' )
        .find( 'input' )
        .not('.segment_products input')
        .not('.segment_coupons input')
        .val( '' );
    jQuery( "#fields_control > div" ).hide();
    jQuery( "#fields_control .div1" ).show();
    jQuery( "#fields_control .div2" ).show();
    jQuery( "#fields_control .flat_format_controls" ).show();
}

function formatItem( item ) {
    var markup = '<div class="clearfix">' +
        '<div>';
    if ( typeof item.photo_url !== "undefined" )
        markup += '<img src="' + item.photo_url + '" style="width: 20%;float:left;" />';
    markup += '<div style="width:75%;float:left;  padding: 5px;">' + item.text + '</div>' +
        '</div>' +
        '</div><div style="clear:both"></div>';

    return markup;
}

function add_custom_field( to, index_p, format, colname, value, segment, format_field ) {

    value   = escapeStr(value);
    colname = escapeStr(colname);

    if ( is_flat_format(format) ) {
	    _index = 'plain_' + index_p + '_';
	    _index_p = 'orders[]';
    } else {
	    _index = '';
	    _index_p = index_p + '[]';
    }

    var label_prefix = '';

    if (is_flat_format(format)) {

        if (segment === 'products' ) {
            label_prefix = '[P] '
        }

        if (segment === 'coupons' ) {
            label_prefix = '[C] '
        }
    }

    var suffix = 0;

    jQuery('#unselected_fields input[value*="static_field_"]').each(function () {

        var match = jQuery(this).attr('value').match(/static_field_(\d+)/);

        if (!match) {
            return true;
        }

        var n = parseInt(match[1]);

        if(n > suffix) {
            suffix = n;
        }
    });

    var field_key = 'static_field_' + (suffix + 1);

    var delete_btn = '<div class="mapping_col_3 mapping_row-delete_field_block"><a href="#" class="mapping_row-delete_field"><span class="dashicons dashicons-trash"></span></a></div>';

//    console.log( to, index_p, format, colname, value );
    var row = jQuery('<li class="mapping_row segment_field segment_'+ segment +'">\
                    <div class="mapping_col_1" style="width: 10px">\
                            <input class="mapping_fieldname" type=hidden name="' + _index_p + '[segment]" value="'+ (segment ? segment : 'misc') +'">\
                            <input class="mapping_fieldname" type=hidden name="' + _index_p + '[key]" value="'+ _index + field_key +'">\
                            <input class="mapping_fieldname" type=hidden name="' + _index_p + '[label]" value="' + colname + '">\
                            <input class="mapping_fieldname" type=hidden name="' + _index_p + '[format]" value="' + format_field + '">\
                    </div>\
                    <div class="mapping_col_2" title="'+field_key+'">' + '<span class="field-prefix">' + label_prefix + '</span>' + colname + '<a href="#" onclick="return remove_custom_field(this);" class="mapping_row-delete_custom_field" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a></div>\
                    <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + _index_p + '[colname]" value="' + colname + '"></div>\
                    <div class="mapping_col_3 custom-field-value"><input class="mapping_fieldname" type=input name="' + _index_p + '[value]" value="' + value + '"></div>'+ delete_btn +'\
            </li>\
                        ');

    row.find('input').prop('disabled', 'disabled');

    to.prepend( row );

    activate_draggable_field(
        to.find('.segment_field').first(),
        segment,
        format
    );

	to.find('.segment_field').first().addClass('blink');

    var field = {
        key: field_key,
        colname: colname,
        'default': 0,
        label: colname,
        format: 'string',
        value: value,
    };

    window.all_fields[segment].unshift(field);
}

function add_custom_meta( to, index_p, format, label, colname, segment, format_field ) {

    label   = escapeStr(label);
    colname = escapeStr(colname);

    if ( is_flat_format(format) ) {
            _index = 'plain_' + index_p + '_' + label;
            _index_p = 'orders[]';
    } else {
            _index = label;
            _index_p = index_p + '[]';
    }

    var label_prefix = '';

    if (is_flat_format(format)) {

        if (segment === 'products' ) {
            label_prefix = '[P] '
        }

        if (segment === 'coupons' ) {
            label_prefix = '[C] '
        }
    }

    var delete_btn = '<div class="mapping_col_3 mapping_row-delete_field_block"><a href="#" class="mapping_row-delete_field"><span class="dashicons dashicons-trash"></span></a></div>';

    var row = jQuery('<li class="mapping_row segment_field segment_'+ segment +'">\
        <div class="mapping_col_1" style="width: 10px">\
                <input class="mapping_fieldname" type=hidden name="' + _index_p + '[segment]" value="'+ (segment ? segment : 'misc') +'">\
                <input class="mapping_fieldname" type=hidden name="' + _index_p + '[key]" value="'+ _index +'">\
                <input class="mapping_fieldname" type=hidden name="' + _index_p + '[label]" value="' + label + '">\
                <input class="mapping_fieldname" type=hidden name="' + _index_p + '[format]" value="' + format_field + '">\
        </div>\
        <div class="mapping_col_2" title="'+label+'">' + '<span class="field-prefix">' + label_prefix + '</span>' + label + '<a href="#" onclick="return remove_custom_field(this);" class="mapping_row-delete_custom_field" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a></div>\
        <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + _index_p + '[colname]" value="' + colname + '"></div>'+ delete_btn +'\
</li>\
                        ');

    row.find('input').prop('disabled', 'disabled');

    to.prepend( row );

    activate_draggable_field(
        to.find('.segment_field').first(),
        segment,
        format
    );

	to.find('.segment_field').first().addClass('blink');

    var field = {
        key: label,
        colname: colname,
        'default': 0,
        label: label,
        segment: segment,
        format: 'string',
        value: 'undefined',
    };

    window.all_fields[segment].unshift(field);
}

function scroll_to_added_field( to ) {
	// scroll to added element

	to.parent().scrollTop(to[0].scrollHeight);

	if ( to.parent().prop('id') === 'fields' ) {
		jQuery( window ).scrollTop( to.parent().offset().top + to.parent().outerHeight() - jQuery( window ).height() / 2 );
	}
}

function formatItemSelection( item ) {
    return item.text;
}

jQuery.fn.extend(
	{
		select2_i18n: function ($attrs) {
		    if ( typeof $attrs !== 'object' ) {
		        $attrs = {};
            }
			$attrs = Object.assign({ language: script_data.select2_locale }, $attrs);
			jQuery(this).select2($attrs);
		}
	} );

function select2_inits()
{
    jQuery( "#from_status, #to_status" ).select2_i18n({multiple: true});
    jQuery( "#statuses" ).select2_i18n();
    jQuery( "#shipping_methods" ).select2_i18n();
    jQuery( "#user_roles" ).select2_i18n();
    jQuery( "#payment_methods" ).select2_i18n();
    jQuery( "#attributes" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#attributes_check" ).select2_i18n(select2WODropdownOpts);
    jQuery( "#itemmeta" ).select2_i18n( {
        width: 220
    } );
    jQuery( "#itemmeta_check" ).select2_i18n(select2WODropdownOpts);

    jQuery( "#custom_fields" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#custom_fields_check" ).select2_i18n();

    jQuery( "#product_custom_fields" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#product_custom_fields_check" ).select2_i18n(select2WODropdownOpts);

	jQuery( "#user_custom_fields" ).select2_i18n( {
		width: 150
	} );
	jQuery( "#user_custom_fields_check" ).select2_i18n();

    jQuery( "#taxonomies" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#taxonomies_check" ).select2_i18n(select2WODropdownOpts);

    jQuery( "#shipping_locations" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#shipping_locations_check" ).select2_i18n(select2WODropdownOpts);

    jQuery( "#billing_locations" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#billing_locations_check" ).select2_i18n(select2WODropdownOpts);

    jQuery( "#item_names" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#item_names_check" ).select2_i18n(select2WODropdownOpts);

    jQuery( "#item_metadata" ).select2_i18n( {
        width: 150
    } );
    jQuery( "#item_metadata_check" ).select2_i18n(select2WODropdownOpts);

    jQuery( "#product_categories" ).select2_i18n( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_categories",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#product_vendors" ).select2_i18n( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_vendors",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#products" ).select2_i18n( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_products",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#user_names" ).select2_i18n( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_users",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#coupons" ).select2_i18n( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_coupons",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );
}

function escapeStr(str)
{
    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
    };

    jQuery.each( entityMap, function( key, value ) {
        str = String(str).replace( value, key );
    });

    return String(str).replace(/[&<>"'\/]/g, function (s) {
      return entityMap[s];
    });

}

function jQuerySelectorEscape(expression) {
      return expression.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
}

//for warning
function setup_alert_date_filter() {
	default_date_filter_color = jQuery( "#my-date-filter" ).css('color');
	try_color_date_filter();
	jQuery( '#from_date' ).change( function() { try_color_date_filter(); });
	jQuery( '#to_date' ).change( function() { try_color_date_filter(); });
}
function try_color_date_filter() {
	var color = default_date_filter_color;
	if( jQuery( "#from_date" ).val() || jQuery( "#to_date" ).val() )
		 color = 'red';
	jQuery( "#my-date-filter" ).css('color', color);
}

jQuery( document ).ready( function ($) {

	$( "#the-list" ).sortable( { handle: '.woe-row-sort-handle' } );

	$( '.check-column .order_part' ).hide();

	$start_reorder_button = $( '#start_reorder' );
	$apply_reorder_button = $( '#apply_reorder' );
	$cancel_reorder_button = $( '#cancel_reorder' );
	$start_reorder_button.click( function ( e ) {
		$order_ids = $( "#the-list" ).sortable( "toArray", {attribute: 'data-job_id'} );

		$start_reorder_button.hide();
		$apply_reorder_button.show();
		$cancel_reorder_button.show();

		$( '.check-column .cb_part' ).hide();
		$( '.check-column .order_part' ).show();


	} );

	$apply_reorder_button.click( function ( e ) {
		$start_reorder_button.show();
		$apply_reorder_button.hide();
		$cancel_reorder_button.hide();

		$( '.check-column .cb_part' ).show();
		$( '.check-column .order_part' ).hide();

		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': "order_exporter",
				'method': 'reorder_jobs',
				'new_jobs_order': $( "#the-list" ).sortable( "toArray", {attribute: 'data-job_id'} ),
				'tab_name': $tab_name,
                                woe_nonce: woe_nonce,
			},
			error: function ( response ) {},
			dataType: 'json',
			type: 'POST',
			success: function() {

			}
		});

	} );

	$cancel_reorder_button.click( function ( e ) {
		$start_reorder_button.show();
		$apply_reorder_button.hide();
		$cancel_reorder_button.hide();

		$( '.check-column .cb_part' ).show();
		$( '.check-column .order_part' ).hide();

		$( $order_ids ).each( function ( $key, $job_id ) {
			$element = $( '[data-job_id="' + $job_id + '"' ).detach();
			$( "#the-list" ).append( $element );
		} );
	} );



} );
