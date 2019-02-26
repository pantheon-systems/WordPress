/*
 global yith_framework_enhanced_select_params
 */
jQuery( document ).ready( function ( $ ) {
    "use strict";

    $( document.body )
        .on( 'yith-framework-enhanced-select-init', function () {
            // Post Search
            $( ':input.yith-post-search' ).filter( ':not(.enhanced)' ).each( function () {
                var default_data = {
                        action   : 'yith_plugin_fw_json_search_posts',
                        security : yith_framework_enhanced_select_params.search_posts_nonce,
                        post_type: 'post'
                    },
                    current_data = $.extend( default_data, $( this ).data() ),
                    select2_args = {
                        allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                        placeholder       : $( this ).data( 'placeholder' ),
                        minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                        escapeMarkup      : function ( m ) {
                            return m;
                        },
                        ajax              : {
                            url        : yith_framework_enhanced_select_params.ajax_url,
                            dataType   : 'json',
                            quietMillis: 250,
                            data       : function ( term ) {
                                current_data.term = term;
                                return current_data;
                            },
                            results    : function ( data ) {
                                var terms = [];
                                if ( data ) {
                                    $.each( data, function ( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    } );
                                }
                                return {
                                    results: terms
                                };
                            },
                            cache      : true
                        }
                    };

                if ( $( this ).data( 'multiple' ) === true ) {
                    select2_args.multiple        = true;
                    select2_args.initSelection   = function ( element, callback ) {
                        var data     = $.parseJSON( element.attr( 'data-selected' ) );
                        var selected = [];

                        $( element.val().split( ',' ) ).each( function ( i, val ) {
                            selected.push( {
                                               id  : val,
                                               text: data[ val ]
                                           } );
                        } );
                        return callback( selected );
                    };
                    select2_args.formatSelection = function ( data ) {
                        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                    };
                } else {
                    select2_args.multiple      = false;
                    select2_args.initSelection = function ( element, callback ) {
                        var data = {
                            id  : element.val(),
                            text: element.attr( 'data-selected' )
                        };
                        return callback( data );
                    };
                }

                $( this ).select2( select2_args ).addClass( 'enhanced' );
            } );

            // Term Search
            $( ':input.yith-term-search' ).filter( ':not(.enhanced)' ).each( function () {
                var default_data = {
                        action   : 'yith_plugin_fw_json_search_terms',
                        security : yith_framework_enhanced_select_params.search_terms_nonce,
                        taxonomy: 'category'
                    },
                    current_data = $.extend( default_data, $( this ).data() ),
                    select2_args = {
                        allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                        placeholder       : $( this ).data( 'placeholder' ),
                        minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                        escapeMarkup      : function ( m ) {
                            return m;
                        },
                        ajax              : {
                            url        : yith_framework_enhanced_select_params.ajax_url,
                            dataType   : 'json',
                            quietMillis: 250,
                            data       : function ( term ) {
                                current_data.term = term;
                                return current_data;
                            },
                            results    : function ( data ) {
                                var terms = [];
                                if ( data ) {
                                    $.each( data, function ( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    } );
                                }
                                return {
                                    results: terms
                                };
                            },
                            cache      : true
                        }
                    };

                if ( $( this ).data( 'multiple' ) === true ) {
                    select2_args.multiple        = true;
                    select2_args.initSelection   = function ( element, callback ) {
                        var data     = $.parseJSON( element.attr( 'data-selected' ) );
                        var selected = [];

                        $( element.val().split( ',' ) ).each( function ( i, val ) {
                            selected.push( {
                                               id  : val,
                                               text: data[ val ]
                                           } );
                        } );
                        return callback( selected );
                    };
                    select2_args.formatSelection = function ( data ) {
                        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                    };
                } else {
                    select2_args.multiple      = false;
                    select2_args.initSelection = function ( element, callback ) {
                        var data = {
                            id  : element.val(),
                            text: element.attr( 'data-selected' )
                        };
                        return callback( data );
                    };
                }

                $( this ).select2( select2_args ).addClass( 'enhanced' );
            } );
        } ).trigger( 'yith-framework-enhanced-select-init' );
    
} );