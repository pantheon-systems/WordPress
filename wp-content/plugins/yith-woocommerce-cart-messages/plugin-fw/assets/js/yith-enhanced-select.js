/* global yith_framework_enhanced_select_params */

jQuery( document ).ready( function ( $ ) {
    "use strict";

    $( document.body )
        .on( 'yith-framework-enhanced-select-init', function () {
            // Post Search
            $( '.yith-post-search' ).filter( ':not(.enhanced)' ).each( function () {
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
                            url           : ajaxurl,
                            dataType      : 'json',
                            quietMillis   : 250,
                            data          : function ( params ) {
                                var default_data_to_return = {
                                    term: params.term
                                };

                                return $.extend( default_data_to_return, current_data );
                            },
                            processResults: function ( data ) {
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
                            cache         : true
                        }
                    };

                $( this ).select2( select2_args ).addClass( 'enhanced' );

                if ( $( this ).data( 'sortable' ) ) {
                    var $select = $( this );
                    var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

                    $list.sortable( {
                                        placeholder         : 'ui-state-highlight select2-selection__choice',
                                        forcePlaceholderSize: true,
                                        items               : 'li:not(.select2-search__field)',
                                        tolerance           : 'pointer',
                                        stop                : function () {
                                            $( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
                                                var id     = $( this ).data( 'data' ).id;
                                                var option = $select.find( 'option[value="' + id + '"]' )[ 0 ];
                                                $select.prepend( option );
                                            } );
                                        }
                                    } );
                }
            } );

            // TERM SEARCH
            $( '.yith-term-search' ).filter( ':not(.enhanced)' ).each( function () {
                var default_data = {
                        action  : 'yith_plugin_fw_json_search_terms',
                        security: yith_framework_enhanced_select_params.search_terms_nonce,
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
                            url           : ajaxurl,
                            dataType      : 'json',
                            quietMillis   : 250,
                            data          : function ( params ) {
                                var default_data_to_return = {
                                    term: params.term
                                };

                                return $.extend( default_data_to_return, current_data );
                            },
                            processResults: function ( data ) {
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
                            cache         : true
                        }
                    };

                $( this ).select2( select2_args ).addClass( 'enhanced' );

                if ( $( this ).data( 'sortable' ) ) {
                    var $select = $( this );
                    var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

                    $list.sortable( {
                                        placeholder         : 'ui-state-highlight select2-selection__choice',
                                        forcePlaceholderSize: true,
                                        items               : 'li:not(.select2-search__field)',
                                        tolerance           : 'pointer',
                                        stop                : function () {
                                            $( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
                                                var id     = $( this ).data( 'data' ).id;
                                                var option = $select.find( 'option[value="' + id + '"]' )[ 0 ];
                                                $select.prepend( option );
                                            } );
                                        }
                                    } );
                }
            } );

        } ).trigger( 'yith-framework-enhanced-select-init' );
    
} );