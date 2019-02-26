jQuery(function ($) {

    "use strict";

    var notice = $('#wpml-media-welcome-notice');

    notice.on('click', '.js-toggle', toggleWelcomeNotice);
    notice.on('click', '.js-dismiss', dismissWelcomeNotice);

    function toggleWelcomeNotice() {
        notice.toggleClass('minimized expanded');

        var a = $(this);
        var altText = a.html();
        a.html(a.data('alt-text'));
        a.data('alt-text', altText);

        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: wpmlMediaWelcomeNotice.toggleAjaxAction,
                nonce: wpmlMediaWelcomeNotice.nonce
            }
        })

        return false;
    }

    function dismissWelcomeNotice() {
        notice.fadeOut(function () {
            $(this).remove();
            $('.icl_tm_wrap .overlay').remove();
        });
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: wpmlMediaWelcomeNotice.dismissAjaxAction,
                nonce: wpmlMediaWelcomeNotice.nonce
            }
        })
        return false;
    }

    if (!notice.is(':visible')) {
        var overlay = $('<div class="overlay"></div>');
        var tmWrap = $('.icl_tm_wrap');
        overlay.append(notice)
        tmWrap.prepend(overlay);

        notice.show();
    }

    notice.on('click', '.wpml-external-link, .button-lg', function (event) {
        var url = $(this).attr('href');
        window.open(url, $(this).attr('target'));
        notice.find('.js-dismiss').show();
        event.preventDefault();
        return false;
    })

});