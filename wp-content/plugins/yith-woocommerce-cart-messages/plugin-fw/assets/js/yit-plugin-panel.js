/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


(function ( $ ) {
    //dependencies handler
    $( '[data-dep-target]' ).each( function () {
        var t = $( this );

        var field = '#' + t.data( 'dep-target' ),
            dep   = '#' + t.data( 'dep-id' ),
            value = t.data( 'dep-value' ),
            type  = t.data( 'dep-type' );

        dependencies_handler( field, dep, value.toString(), type );

        $( dep ).on( 'change', function () {
            dependencies_handler( field, dep, value.toString(), type );
        } ).change();
    } );

    //Handle dependencies.
    function dependencies_handler( id, deps, values, type ) {
        var result = true;

        //Single dependency
        if ( typeof( deps ) == 'string' ) {
            if ( deps.substr( 0, 6 ) == ':radio' ) {
                deps = deps + ':checked';
            }

            var val = $( deps ).val();

            if ( $( deps ).attr( 'type' ) == 'checkbox' ) {
                var thisCheck = $( deps );
                if ( thisCheck.is( ':checked' ) ) {
                    val = 'yes';
                }
                else {
                    val = 'no';
                }
            }

            values = values.split( ',' );

            for ( var i = 0; i < values.length; i++ ) {
                if ( val != values[ i ] ) {
                    result = false;
                }
                else {
                    result = true;
                    break;
                }
            }
        }

        var $current_field     = $( id ),
            $current_container = $( id + '-container' ).closest( 'tr' ); // container for YIT Plugin Panel

        if ( $current_container.length < 1 ) {
            // container for YIT Plugin Panel WooCommerce
            $current_container = $current_field.closest( '.yith-plugin-fw-panel-wc-row' );
        }

        var types = type.split( '-' ), j;
        for ( j in types ) {
            var current_type = types[ j ];
            
            if ( !result ) {
                switch ( current_type ) {
                    case 'disable':
                        $current_container.addClass( 'yith-disabled' );
                        $current_field.attr( 'disabled', true );
                        break;
                    case 'hideme':
                        $current_field.hide();
                        break;
                    default:
                        $current_container.hide();
                }

            } else {
                switch ( current_type ) {
                    case 'disable':
                        $current_container.removeClass( 'yith-disabled' );
                        $current_field.attr( 'disabled', false );
                        break;
                    case 'hideme':
                        $current_field.show();
                        break;
                    default:
                        $current_container.show();
                }
            }
        }
    }

    //connected list
    $( '.rm_connectedlist' ).each( function () {
        var ul       = $( this ).find( 'ul' );
        var input    = $( this ).find( ':hidden' );
        var sortable = ul.sortable( {
                                        connectWith: ul,
                                        update     : function ( event, ui ) {
                                            var value = {};

                                            ul.each( function () {
                                                var options = {};

                                                $( this ).children().each( function () {
                                                    options[ $( this ).data( 'option' ) ] = $( this ).text();
                                                } );

                                                value[ $( this ).data( 'list' ) ] = options;
                                            } );

                                            input.val( (JSON.stringify( value )).replace( /[\\"']/g, '\\$&' ).replace( /\u0000/g, '\\0' ) );
                                        }
                                    } ).disableSelection();
    } );

    //google analytics generation
    $( document ).ready( function () {
        $( '.google-analytic-generate' ).click( function () {
            var editor   = $( '#' + $( this ).data( 'textarea' ) ).data( 'codemirrorInstance' );
            var gatc     = $( '#' + $( this ).data( 'input' ) ).val();
            var basename = $( this ).data( 'basename' );

            var text = "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){\n";
            text += "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement( o ),\n";
            text += "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\n";
            text += "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');\n\n";
            text += "ga('create', '" + gatc + "', '" + basename + "');\n";
            text += "ga('send', 'pageview');\n";
            editor.replaceRange(
                text,
                editor.getCursor( 'start' ),
                editor.getCursor( 'end' )
            )
        } )
    } );


    // prevents the WC message for changes when leaving the panel page
    $( '.yith-plugin-fw-panel .woo-nav-tab-wrapper' ).removeClass( 'woo-nav-tab-wrapper' ).addClass( 'yith-nav-tab-wrapper' );

})( jQuery );