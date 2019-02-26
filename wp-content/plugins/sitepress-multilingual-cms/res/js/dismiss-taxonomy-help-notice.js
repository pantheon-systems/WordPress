/*globals jQuery, ajaxurl, wpml_notice_information*/

function wpml_dismiss_taxonomy_translation_notice(element) {
    "use strict";
    var notice = jQuery(element);
    jQuery.ajax({
        url:      ajaxurl,
        type:     'POST',
        data:     {
            action: 'otgs-dismiss-notice',
            id: wpml_notice_information.notice_id,
            group: wpml_notice_information.notice_group,
            nonce: notice.data('nonce')
        },
        success: function () {
            notice.parents('.otgs-notice').remove();
        }
    });
}
