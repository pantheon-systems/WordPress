jQuery(document).ready(function ($) {
    var string_locator_search_active = false;

    var add_notice = function( title, message, format ) {
        $(".notices").append( '<div class="notice notice-' + format + ' is-dismissible"><p><strong>' + title + '</strong><br />' + message + '</p></div>' );
    };

    var throw_error = function( title, message ) {
        string_locator_search_active = false;
        $(".string-locator-feedback").hide();
        add_notice( title, message, 'error' );
    };

    var finalize_string_locator_search = function() {
        string_locator_search_active = false;

        $("#string-locator-feedback-text").text('');

        var search_finalized = {
            action : 'string-locator-clean',
            nonce : string_locator.search_nonce
        };

        $.post(
            string_locator.ajax_url,
            search_finalized,
            function( response ) {
                $(".string-locator-feedback").hide();
                if ( $("tbody", ".tools_page_string-locator").is(":empty") ) {
                    $("tbody", ".tools_page_string-locator").html( '<tr><td colspan="3">' + string_locator.search_no_results + '</td></tr>' );
                }
            }
        ).fail(function(xhr, textStatus, errorThrown) {
            throw_error( xhr.status + ' ' + errorThrown, string_locator.search_error );
        });
    };

    var clear_string_locator_result_area = function() {
        $(".notices").html('');
        $("#string-locator-search-progress").removeAttr( 'value' );
        $("tbody", ".tools_page_string-locator").html('');
    };

    var perform_string_locator_single_search = function( maxCount, thisCount ) {
        if ( thisCount >= maxCount || ! string_locator_search_active ) {
            $("#string-locator-feedback-text").html( string_locator.saving_results_string );
            finalize_string_locator_search();
            return false;
        }

        var search_request = {
            action  : 'string-locator-search',
            filenum : thisCount,
            nonce   : string_locator.search_nonce
        };

        $.post(
            string_locator.ajax_url,
            search_request,
            function ( response ) {
                if ( ! response.success ) {
                    if ( false === response.data.continue ) {
                        throw_error( string_locator.warning_title, response.data.message );
                        return false;
                    } else {
						add_notice( string_locator.warning_title, response.data.message, 'warning' );
                    }
                }

                if ( undefined !== response.data.search ) {
                    $("#string-locator-search-progress").val( response.data.filenum );
                    $("#string-locator-feedback-text").html( string_locator.search_current_prefix + response.data.next_file );

                    string_locator_append_result( response.data.search );
                }
                var nextCount = response.data.filenum + 1;
                perform_string_locator_single_search( maxCount, nextCount );
            },
            'json'
        ).fail(function(xhr, textStatus, errorThrown) {
            throw_error( xhr.status + ' ' + errorThrown, string_locator.search_error );
        });
    };

    var string_locator_append_result = function( total_entries ) {
        if ( $(".no-items", ".tools_page_string-locator").is(':visible') ) {
            $(".no-items", ".tools_page_string-locator").hide();
        }
        if ( Array !== total_entries.constructor ) {
            return false;
        }

        total_entries.forEach( function ( entries ) {
            if ( entries ) {
                for (var i = 0, amount = entries.length; i < amount; i++) {

                    var entry = entries[i];

                    if (undefined !== entry.stringresult) {
                        var builtHTML = '<tr>' +
                            '<td>' + entry.stringresult + '<div class="row-actions"><span class="edit"><a href="' + entry.editurl + '" aria-label="Edit">Edit</a></span></div></td>' +
                            '<td>' + entry.filename + '</td>' +
                            '<td>' + entry.linenum + '</td>' +
                            '</tr>';

                        $("tbody", ".tools_page_string-locator").append(builtHTML);
                    }
                }
            }
        } );
    };


    $("#string-locator-search-form").on( 'submit', function (e) {
        e.preventDefault();
        $("#string-locator-feedback-text").text(string_locator.search_preparing );
        $(".string-locator-feedback").show();
        string_locator_search_active = true;
        clear_string_locator_result_area();

        var directory_request = {
            action    : 'string-locator-get-directory-structure',
            directory : $("#string-locator-search").val(),
            search    : $("#string-locator-string").val(),
            regex     : $("#string-locator-regex").is(':checked'),
            nonce     : string_locator.search_nonce
        };

        $("table.tools_page_string-locator").show();

        $.post(
            string_locator.ajax_url,
            directory_request,
            function ( response ) {
                if ( ! response.success ) {
                    add_notice( response.data, 'alert' );
                    return;
                }
                $("#string-locator-search-progress").attr( 'max', response.data.total ).val( response.data.current );
                $("#string-locator-feedback-text").text(string_locator.search_started );
                perform_string_locator_single_search( response.data.total, 0 );
            },
            'json'
        ).fail(function(xhr, textStatus, errorThrown) {
            throw_error( xhr.status + ' ' + errorThrown, string_locator.search_error );
        });
    });
});