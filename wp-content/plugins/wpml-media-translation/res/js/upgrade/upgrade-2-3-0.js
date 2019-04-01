var WPML_Media_2_3_0_Upgrade = WPML_Media_2_3_0_Upgrade || {};

jQuery(function ($) {

    "use strict";

    var updateContainer = $("#wpml-media-2-3-0-update");
    var updateButton = updateContainer.find(".button-primary");
    var spinner = updateContainer.find(".spinner");
    var nonce = updateContainer.find("input[name=nonce]").val();
    var statusContainer = updateContainer.find('.status');

    var mediaFlagNoticeContainer = false;
    if ($('#wpml-media-posts-media-flag').length) {
        mediaFlagNoticeContainer = $('#wpml-media-posts-media-flag');
    } else if ($('.otgs-notice[data-id=wpml-media-posts-media-flag]').length) {
        mediaFlagNoticeContainer = $('.otgs-notice[data-id=wpml-media-posts-media-flag]');
    }
    if (mediaFlagNoticeContainer) {
        mediaFlagNoticeContainer.hide();
        $('.wrap-wpml-media-upgrade h2').hide();
    }

    updateButton.on("click", function () {
        showProgress();
        runUpgrade();
    });

    function showProgress() {
        spinner.css({visibility: "visible"});
        updateButton.prop("disabled", true);
    }

    function hideProgress() {
        spinner.css({visibility: "hidden"});
        updateButton.prop("disabled", false);
    }

    function setStatus(statusText) {
        statusContainer.html(statusText);
    }

    function runUpgrade() {
        var data = {
            action: "wpml_media_2_3_0_upgrade",
            nonce: nonce,
            step: "reset-new-content-settings"
        };
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
                if (response.data.status) {
                    setStatus(response.data.status);
                }
                runAttachmentMigration(0, 1, 0);
            }
        });
    }

    function runAttachmentMigration(offset, batchSizeFactor, timestamp) {
        var data = {
            action: "wpml_media_2_3_0_upgrade",
            nonce: nonce,
            step: "migrate-attachments",
            offset: offset,
            batch_size_factor: batchSizeFactor,
            timestamp: timestamp
        };
        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
                if (response.data.status) {
                    setStatus(response.data.status);
                }
                if (response.data.goon) {
                    runAttachmentMigration(response.data.offset, response.data.batch_size_factor, response.data.timestamp);
                } else {
                    if (mediaFlagNoticeContainer) {
                        $('#wpml-media-2-3-0-update').hide();
                        if (mediaFlagNoticeContainer.find('input.button-primary').length) {
                            mediaFlagNoticeContainer.show();
                            mediaFlagNoticeContainer.find('input.button-primary').trigger('click');
                        } else {
                            location.href = mediaFlagNoticeContainer.find('a').attr('href')+'&run_setup=1&redirect_to='+location.href;
                        }
                    } else {
                        hideProgress();
                        location.reload();
                    }
                }
            }
        });
    }

});
