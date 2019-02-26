var WPML_Media_Selector = WPML_Media_Selector || {};

jQuery(function ($) {

    "use strict";

    var dashboardTable = $('#icl-tm-translation-dashboard');

    dashboardTable.find('tbody :checkbox').on('change', showMediaSelector);

    function showMediaSelector() {

        var hasMedia = $(this).closest('tr').data('has-media');
        if (hasMedia) {
            var checkbox = $(this);
            var postSelected = checkbox.prop('checked');
            var currentRow = $(this).closest('tr');
            var postId = currentRow.attr('id').replace(/^row_/, '');

            var mediaSelectorRow = $('#js-wpml-media-selector-' + postId);
        }

        if (postSelected) {
            if (mediaSelectorRow.length) {
                mediaSelectorRow.show();
            } else {
                loadMediaSelectorContent(currentRow, postId, checkbox);
            }
        } else if ( mediaSelectorRow ) {
            mediaSelectorRow.find('label :checkbox').prop('checked', false);
            mediaSelectorRow.hide();
        }
    }

    function loadMediaSelectorContent(currentRow, postId, checkbox) {

        var mediaSelectorContainer = {};
        var rowWidth = currentRow.find('td:visible').length;
        var data = {
            action: "wpml_media_load_image_selector",
            post_id: postId,
            languages: getTargetLanguages()
        };

        var mediaSelectorRow = $(
            '<tr class="hidden"><td colspan="' + rowWidth + '"></td></tr>' +
            '<tr id="js-wpml-media-selector-' + postId + '" class="wpml-media-selector">' +
            '<td colspan="' + rowWidth + '"></td>' +
            '</tr>'
        );
        mediaSelectorRow.insertAfter(currentRow);

        var postType = currentRow.data('post-type');
        var preLoader = $('#wpml-media-selector-preloader').html().replace(/%POST_TYPE%/, postType);
        mediaSelectorContainer = $('#js-wpml-media-selector-' + postId).find('td');
        mediaSelectorContainer.html(preLoader);

        checkbox.prop('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
                if (response.success && response.data.media_files_count) {
                    mediaSelectorContainer.find('.wpml-media-selector-wrapper-inner').html(response.data.html);
                } else {
                    mediaSelectorRow.remove();
                }
                checkbox.prop('disabled', false);
            }
        });
    }

    function getTargetLanguages() {
        var languages = [];
        $(':radio[name^="tr_action"][value="1"]:checked', '#icl_tm_languages').each(function () {
            languages.push($(this).attr('name').replace(/^tr_action\[/, '').replace(/\]$/, ''));
        });

        return languages;
    }

    $('#wpml-media-basket-notice').on('click', hideBasketNotice);

    function hideBasketNotice() {
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {action: 'dismiss_media_basket_notice'},
            success: function (response) {
                if (response.data.status) {
                    $('#wpml-media-basket-notice').fadeOut();
                }
            }
        });
    }

    $('body').on('click', '.js-wpml-media-selector-toggle', toogleMediaList);

    function toogleMediaList(event) {
        event.preventDefault();
        $(this).toggleClass('collapsed');
        $('.wpml-media-selector-wrapper').toggle();
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {action: 'wpml_media_toogle_show_media_selector'}
        });
        return false;
    }

});