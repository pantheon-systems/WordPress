/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


( function ( $ ) {

    /* === Licence API === */

    var licence_activation = function ( button ) {
        button.on( 'click', function ( e, button ) {
            e.preventDefault();

            var t                  = $( this ),
                form_id            = t.data( 'formid' ),
                form               = $( '#' + form_id ),
                data               = form.serialize(),
                message            = $( form ).find( '.message' ),
                message_wrapper    = $( form ).find( '.message-wrapper' ),
                email              = form.find( '.user-email' ),
                licence_key        = form.find( '.licence-key' ),
                email_val          = form.find( '.user-email' ).val(),
                licence_key_val    = form.find( '.licence-key' ).val(),
                error              = false,
                error_fields       = new Array(),
                product_row        = form.find( '.product-row' ),
                licence_activation = $( '.licence-activation' ),
                spinner            = $( '#products-to-active' ).find( '.spinner' ),
                is_mail            = function( val ){
                    /* https://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex */
                    var re = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
                    return re.test( val );
                };

            /* Init Input Fields */
            message.empty();
            message_wrapper.removeClass( 'visible' );
            email.removeClass( 'require' );
            licence_key.removeClass( 'require' );
            product_row.removeClass( 'error' );
            spinner.addClass( 'show' );
            t.add( licence_activation ).prop( "disabled", true ).addClass( 'clicked' );

            if ( '' === email_val || ! is_mail( email_val ) ) {
                error = true;
                email.addClass( 'require' );

                if( '' === email_val ){
                    error_fields[ error_fields.length ] = licence_message.email;
                }

                else {
                    error_fields[ error_fields.length ] = licence_message.email;
                }

            }

            if ( '' === licence_key_val ) {
                error = true;
                error_fields[ error_fields.length ] = licence_message.license_key;
                licence_key.addClass( 'require' );
            }

            if ( false === error ) {
                jQuery.ajax( {
                                 type   : 'POST',
                                 url    : typeof ajaxurl != 'undefined' ? ajaxurl : yith_ajax.url,
                                 data   : data,
                                 success: function ( response ) {

                                     spinner.removeClass( 'show' );
                                     t.add( licence_activation ).prop( "disabled", false ).removeClass( 'clicked' );

                                     if ( true === response.activated ) {
                                         $( '.product-licence-activation' ).empty().replaceWith( response.template );
                                         licence_api();
                                     } else if ( false !== response && typeof response.error !== 'undefined' ) {
                                         message.text( response.error );
                                         message_wrapper.addClass( 'visible' );
                                         product_row.addClass( 'error' );
                                     } else {
                                         message.text( licence_message.server );
                                         message_wrapper.addClass( 'visible' );
                                         product_row.addClass( 'error' );
                                     }

                                     if ( typeof response.debug !== 'undefined' ) {
                                         console.log( response.debug );
                                     }
                                 }
                             } );
            } else {
                if ( error_fields.length == 1 ) {
                    message.text( licence_message.error.replace( '%field%', error_fields[ 0 ] ) );
                    message_wrapper.addClass( 'visible' );
                    product_row.addClass( 'error' );
                } else {
                    var message_text = licence_message.errors;
                    for ( var i = 0; i < error_fields.length; i++ ) {
                        message_text = message_text.replace( '%field_' + ( i + 1 ) + '%', error_fields[ i ] );
                        message_wrapper.addClass( 'visible' );
                    }
                    message.text( message_text );
                    message_wrapper.addClass( 'visible' );
                    product_row.addClass( 'error' );
                }

                spinner.removeClass( 'show' );
                t.add( licence_activation ).prop( "disabled", false ).removeClass( 'clicked' );
            }
        } );
    };

    var licence_update = function ( button ) {
        button.on( 'click', function ( e ) {
            e.preventDefault();

            var t    = $( this ),
                form = $( '#licence-check-update' ),
                data = form.serialize();

            t.prop( "disabled", true ).addClass( 'clicked' );
            form.find( 'div.spinner' ).addClass( 'show' );

            jQuery.ajax( {
                             type   : 'POST',
                             url    : ajaxurl,
                             data   : data,
                             success: function ( response ) {
                                 $( '.product-licence-activation' ).empty().replaceWith( response.template );
                                 licence_api();
                             }
                         } );
        } );
    };

    var licence_deactivate = function ( button ) {
        button.on( 'click', function ( e ) {
            e.preventDefault();

            var check = script_info.is_debug == true ? true : confirm( licence_message.are_you_sure );

            if ( check == true ) {
                var t               = $( this ),
                    licence_key     = t.data( 'licence-key' ),
                    licence_email   = t.data( 'licence-email' ),
                    product_init    = t.data( 'product-init' ),
                    action          = t.data( 'action' ),
                    renew           = $( '.licence-renew' ),
                    deactive        = $( '.licence-deactive' ),
                    message         = $( '#yith-licence-notice' ),
                    activated_table = $( '.activated-table' );

                t.add( renew ).add( deactive ).prop( "disabled", true ).addClass( 'clicked' );
                $( '#activated-products' ).find( '.spinner' ).addClass( 'show' );

                jQuery.ajax( {
                                 type   : 'POST',
                                 url    : ajaxurl,
                                 data   : {
                                     action      : action,
                                     licence_key : licence_key,
                                     email       : licence_email,
                                     product_init: product_init
                                 },
                                 success: function ( response ) {
                                     message.css( 'maxWidth', activated_table.width() );
                                     if ( false == response ) {
                                         message.find( 'p.yith-licence-notice-message' ).html( licence_message.server );
                                         message.removeClass( 'notice-success' ).addClass( 'notice-error visible' );
                                         t.add( renew ).add( deactive ).add( renew ).prop( "disabled", false ).removeClass( 'clicked' );
                                         $( '#activated-products' ).find( '.spinner' ).removeClass( 'show' );
                                     }

                                     else {
                                         if ( false == response.activated ) {
                                             $( '.product-licence-activation' ).empty().replaceWith( response.template );
                                             licence_api();
                                         }

                                         if ( typeof response.error != 'undefined' ) {
                                             message.find( 'p.yith-licence-notice-message' ).html( response.error );
                                             message.removeClass( 'notice-success' ).addClass( 'notice-error visible' );
                                             t.add( renew ).add( deactive ).add( renew ).prop( "disabled", false ).removeClass( 'clicked' );
                                             $( '#activated-products' ).find( '.spinner' ).removeClass( 'show' );
                                         }
                                     }
                                 }
                             } );
            }
        } );
    };

    var licence_api = function () {
        var button      = $( '.licence-activation' ),
            check       = $( '.licence-check' ),
            deactivated = $( '.licence-deactive' );

        licence_activation( button );
        licence_update( check );
        licence_deactivate( deactivated );
    };

    licence_api();

    $( 'body' ).on( 'click', '.yit-changelog-button', function ( e ) {
        $( '#TB_window' ).remove();
    } );

} )( jQuery );