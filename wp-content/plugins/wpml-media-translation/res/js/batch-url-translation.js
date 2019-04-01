var WPML_Media_Batch_Url_Translation = WPML_Media_Batch_Url_Translation || {

    hasDialog: false,
    dialog: jQuery('#batch-media-translation-wrap'),
    form: jQuery('#batch-media-translation-form'),
    globalScope: 0,
    attachmentId: 0,

    createDialog: function (attachmentId, postsList) {
        this.hasDialog = true;
        this.attachmentId = attachmentId;

        if (postsList.length > 0) {
            this.dialog.find('.usage').show();
            var ul = this.dialog.find('.usage ul');
            for (var i in postsList) {
                var li = postsList[i].url ?
                    '<a href="' + postsList[i].url + '">' + postsList[i].title + '</a>' :
                    postsList[i].title;
                ul.append('<li>' + li + '</li>');
            }
        } else {
            this.dialog.find('.no-usage').show();
        }
    },

    showDialog: function () {
        this.dialog.show();
        this.dialog.scrollTop(0);
    },

    closeDialog: function (event) {
        var self = WPML_Media_Batch_Url_Translation;
        if (typeof event !== 'undefined') {
            event.preventDefault();
        }
        self.dialog.hide();
        self.reset();
    },

    setInProgress: function (on) {
        this.form.find('input.button-primary:submit').prop('disabled', on);
        this.form.find('input[name=global-scan-scope]').prop('disabled', on);
    },

    runScan: function () {
        var self = WPML_Media_Batch_Url_Translation;
        var form = jQuery(this);

        self.globalScope = form.find('input[name=global-scan-scope]:checked').val();

        var nextAction = [];
        nextAction['wpml_media_translate_media_url_in_posts'] = 'wpml_media_translate_media_url_in_custom_fields';

        if ( wpml_media_batch_translation.is_st_enabled ) {
			nextAction['wpml_media_translate_media_url_in_custom_fields'] = 'wpml_media_translate_media_url_in_strings';
		}

        self.setInProgress(true);
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            data: form.serialize(),
            success: function (response) {
                self.setStatus(response.message);
                self.scan(null, 'wpml_media_translate_media_url_in_posts', nextAction);
            }
        })
        return false;
    },

    reset: function () {
        this.dialog.find('.usage').hide();
        this.dialog.find('.no-usage').hide();
        this.dialog.find('.usage ul').html('');
        this.attachmentId = 0;
        this.setStatus('');
        this.dialog.hide();
        this.hasDialog = false;
    },

    setStatus: function (text) {
        this.dialog.find('.status').html(text);
    },

    setComplete: function (text) {
        this.setStatus(text);
        this.setInProgress(false);
        this.form.hide();
        this.dialog.removeClass('notice-info').addClass('notice-success');
        window.setTimeout(this.closeDialog, 3000);
    },

    scan: function (offset, action, nextAction) {
        var self = WPML_Media_Batch_Url_Translation;
        if (typeof offset === 'undefined') {
            offset = 0;
        }
        jQuery.ajax(
            {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: action,
                    global: self.globalScope,
                    attachment_id: self.attachmentId,
                    offset: offset
                },
                dataType: 'json',
                success: function (response) {
                    self.setStatus(response.data.message);
                    if (response.data.continue > 0) {
                        self.scan(response.data.offset, action, nextAction);
                    } else {
                        if (nextAction[action]) {
                            self.scan(null, nextAction[action], nextAction);
                        } else {
                            self.setComplete(wpml_media_batch_translation.complete);
                        }
                    }
                }
            }
        );
    }

};


jQuery(function ($) {
    "use strict";

    WPML_Media_Batch_Url_Translation.form.on('submit', WPML_Media_Batch_Url_Translation.runScan);
    WPML_Media_Batch_Url_Translation.dialog.on('click', '.js-close', WPML_Media_Batch_Url_Translation.closeDialog);

});