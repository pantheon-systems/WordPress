jQuery(document).ready(function ($) {

    if (typeof wc_memberships_memebers_area_endpoint != 'undefined') {

        var tabs = ['my-membership-content', 'my-membership-products', 'my-membership-discounts', 'my-membership-notes'];

        for (var i = 0; i < tabs.length; i++) {
            $('.' + tabs[i] + ' a').each(function () {
                var href = $(this).attr('href');
                if (href) {
                    $(this).attr('href', href.replace(wc_memberships_memebers_area_endpoint.original, wc_memberships_memebers_area_endpoint.translated));
                }
            });
        }

    }

});