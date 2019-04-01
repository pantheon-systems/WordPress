jQuery(document).ready(function ($) {
    var dialogBox = $("#wpml-media-dialog");
    var dialogForm = $("#wpml-media-dialog-form");

    var uploadedMediaTranslation = false;

    dialogBox.dialog({
        resizable: false,
        draggable: false,
        height: "auto",
        width: 800,
        autoOpen: false,
        modal: true,
        closeOnEscape: false,
        dialogClass: "otgs-ui-dialog wpml-media-dialog wpml-dialog-translate",
        title: wpml_media_popup.title,
        create: function () {
            $("#jquery-ui-style-css").attr("disabled", "disabled");
        },
        open: function (event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            repositionDialog();
            if (WPML_Media_Batch_Url_Translation.hasDialog) {
                WPML_Media_Batch_Url_Translation.reset();
            }
        },
        close: function (event, ui) {
            $("#jquery-ui-style-css").removeAttr("disabled");
            if (WPML_Media_Batch_Url_Translation.hasDialog) {
                WPML_Media_Batch_Url_Translation.showDialog();
            }
        },
        buttons: [
            {
                text: wpml_media_popup.cancel,
                class: "alignleft",
                click: function () {
                    $(this).find('.spinner').remove();
                    $(this).dialog("close");
                }
            },
            {
                text: wpml_media_popup.save,
                class: "button-primary alignright",
                disabled: true,
                click: function () {
                    var thisDialog = $(this);
					disableFormSave();
                    var ajaxLoader = $("<span class=\"spinner\"></span>");
                    var translationForm = thisDialog.find("form");
                    ajaxLoader.insertBefore(".wpml-media-dialog .button-primary").css({
                        visibility: "visible",
                        float: "none"
                    });
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        dataType: "json",
                        data: translationForm.serialize(),
                        success: function (response) {

                            if (response.success) {
                                var originalAttachmentId = translationForm.find("input[name='original-attachment-id']").val();
                                var translatedLanguage = translationForm.find("input[name='translated-language']").val();
                                var mediaTranslationWrap = $("#media-attachment-" + originalAttachmentId + "-" + translatedLanguage);
								var batchMediaTranslationWrap = $( '#batch-media-translation-wrap' );

                                var isMediaUpload = false;
								var isRestoreMedia = false;
                                if (response.data.thumb) {
                                    mediaTranslationWrap.find("img").attr("src", response.data.thumb).fadeIn();
                                    mediaTranslationWrap.data("thumb", response.data.thumb);
                                    mediaTranslationWrap.data("media-is-translated", 1);
                                    mediaTranslationWrap.find(".otgs-ico-edit").hide();

									isMediaUpload = translationForm.find("input[name=update-media-file]").val();
                                } else {
                                    mediaTranslationWrap.find("img").attr("src", "").hide();
                                    mediaTranslationWrap.data("thumb", "");
                                    mediaTranslationWrap.find(".otgs-ico-edit").show();
                                    mediaTranslationWrap.find("img")
                                        .closest(".js-open-media-translation-dialog")
                                        .removeClass("wpml-media-translation-image");
                                    mediaTranslationWrap.data("media-is-translated", 0);

									isRestoreMedia = translationForm.find("input[name=restore-media]").val();
                                }

								if (isMediaUpload || isRestoreMedia) {
									WPML_Media_Batch_Url_Translation.createDialog(originalAttachmentId, response.data.usage);
									batchMediaTranslationWrap.find('#batch-media-translation-form').show();
									batchMediaTranslationWrap.removeClass( 'notice-success' );
									batchMediaTranslationWrap.addClass( 'notice-info' );
								}

                                mediaTranslationWrap.attr('title', mediaTranslationWrap.data('language-name') + ': ' +
                                    wpml_media_popup.status_labels[response.data.status]);

                                mediaTranslationWrap.data("title", $("#media-title-translation").val());
                                mediaTranslationWrap.data("caption", $("#media-caption-translation").val());
                                mediaTranslationWrap.data("alt_text", $("#media-alt-text-translation").val());
                                mediaTranslationWrap.data("description", $("#media-description-translation").val());

                                if (response.data.attachment_id) {
                                    mediaTranslationWrap.data("attachment-id", response.data.attachment_id);
                                }

                                if (mediaTranslationWrap.find(".otgs-ico-add:visible").length) {
                                    var addIcon = mediaTranslationWrap.find(".otgs-ico-add");
                                    addIcon.removeClass("otgs-ico-add").addClass("otgs-ico-edit");
                                    if (response.data.thumb) {
                                        addIcon.hide();
                                    }
                                    if (response.data.thumb) {
                                        mediaTranslationWrap.find("img")
                                            .closest(".js-open-media-translation-dialog")
                                            .addClass("wpml-media-translation-image");
                                    }
                                }

                                thisDialog.dialog("close");
                                ajaxLoader.remove();

                                translationForm.find("input[name=restore-media]").val(0);
                                translationForm.find("input[name=update-media-file]").val(0);
                            }

                        }
                    });
                }
            }
        ]
    });

	function disableFormSave() {
	    $(".wpml-media-dialog .ui-dialog-buttonset .button-primary").prop("disabled", true);
	}

    function enableFormSave(e) {
        if (typeof e !== 'undefined') {
            var charCode = (e.which) ? e.which : e.keyCode;
        }
        if (typeof e === 'undefined' || charCode >= 32 || charCode == 8) {
            $(".wpml-media-dialog .ui-dialog-buttonset .button-primary").prop("disabled", false);
        }
    }

    dialogForm.on("keyup", "input, textarea", enableFormSave);

    $(window).resize(repositionDialog);

    function repositionDialog() {
        var winH = $(window).height() - 180;
        $(".wpml-media-dialog").css({
            "max-width": "95%"
        });

        $(".wpml-media-dialog .ui-dialog-content").css({
            "max-height": winH
        });

        dialogBox.dialog("option", "position", {
            my: "center",
            at: "center",
            of: window
        });
    }

    $(".js-open-media-translation-dialog").click(function () {

        var attachmentRow = $(this).closest(".wpml-media-attachment-row");
        var translatedMedia = $(this).closest(".wpml-media-wrapper");

        hideAllMediaTextFields();
        resetProgressAnimation();

        updateDialogImages(attachmentRow, translatedMedia);

        if (translatedMedia.data("media-is-translated")) {
            enableUsingTranslatedMediaFile();
        } else {
            enableUsingOriginalMediaFile();
        }

        updateDialogFormFields(attachmentRow, translatedMedia);

        updateDialogHiddenFormFields(attachmentRow, translatedMedia);

        dialogBox.dialog('open');

    });

    function updateDialogImages(attachmentRow, translatedMedia) {

        $('#wpml-media-dialog .wpml-header-original .wpml-title-flag img').attr('src', attachmentRow.data('flag'));
        $('#wpml-media-dialog .wpml-header-translation .wpml-title-flag img').attr('src', translatedMedia.data('flag'));

		$('.wpml-media-original-image .wpml-media-original-title')
            .html(attachmentRow.data('is-image') ? '' : attachmentRow.data('file-name'));
		$('.wpml-media-upload-handle .wpml-media-translated-title')
            .html(attachmentRow.data('is-image') || !translatedMedia.data('media-is-translated') ? '' : translatedMedia.data('file-name'));

		$('#wpml-media-dialog .wpml-header-original strong').html(attachmentRow.data('language-name'));
        $('#wpml-media-dialog .wpml-header-translation strong').html(translatedMedia.data('language-name'));

		var originalImg = $('#wpml-media-dialog .wpml-form-row .wpml-media-original-image img');
		var translatedImg = $('#wpml-media-dialog .wpml-form-row .wpml-media-upload-handle img');

		originalImg.attr('src', attachmentRow.data('thumb'))
			.attr('alt', attachmentRow.data('language-code'));
		translatedImg.attr('src', translatedMedia.data('thumb') ? translatedMedia.data('thumb') : attachmentRow.data('thumb'))
			.attr('alt', translatedMedia.data('language-code'));
		if(!attachmentRow.data('is-image')){
			originalImg.addClass('is-non-image');
			translatedImg.addClass('is-non-image');
		}else{
			originalImg.removeClass('is-non-image');
			translatedImg.removeClass('is-non-image');
		}

		$('#wpml-media-file-upload-form input:file').attr('accept', attachmentRow.data('mime-type'));
    }

    function updateDialogFormFields(attachmentRow, translatedMedia) {
        if (attachmentRow.data("title")) {
            $("#media-title-original").val(attachmentRow.data("title"));
            $("#media-title-translation").val(translatedMedia.data("title"));
            $(".wpml-form-row-title").show();
        }
        if (attachmentRow.data("caption")) {
            $("#media-caption-original").val(attachmentRow.data("caption"));
            $("#media-caption-translation").val(translatedMedia.data("caption"));
            $(".wpml-form-row-caption").show();
        }
        if (attachmentRow.data("alt_text")) {
            $("#media-alt-text-original").val(attachmentRow.data("alt_text"));
            $("#media-alt-text-translation").val(translatedMedia.data("alt_text"));
            $(".wpml-form-row-alt-text").show();
        }
        if (attachmentRow.data("description")) {
            $("#media-description-original").val(attachmentRow.data("description"));
            $("#media-description-translation").val(translatedMedia.data("description"));
            $(".wpml-form-row-description").show();
        }
    }

    function updateDialogHiddenFormFields(attachmentRow, translatedMedia) {
        dialogForm.find("input[name=original-attachment-id]").val(attachmentRow.data("attachment-id"));
        dialogForm.find("input[name=translated-attachment-id]").val(translatedMedia.data("attachment-id"));
        dialogForm.find("input[name=translated-language]").val(translatedMedia.data("language-code"));

        dialogForm.find("input[name=restore-media]").val(0);

        $("#wpml-media-file-upload-form").find("input[name=attachment-id]").val(translatedMedia.data("attachment-id"));
        $("#wpml-media-file-upload-form").find("input[name=original-attachment-id]").val(attachmentRow.data("attachment-id"));
        $("#wpml-media-file-upload-form").find("input[name=language]").val(translatedMedia.data("language-code"));
    }

    function enableUsingTranslatedMediaFile() {
        dialogForm.find('.wpml-media-upload-text').hide();
        dialogForm.find('.js-wpml-media-revert').show();
    }

    function enableUsingOriginalMediaFile() {
        dialogForm.find('.wpml-media-upload-text').show();
        dialogForm.find('.js-wpml-media-revert').hide();
    }

    function hideAllMediaTextFields() {
        $("#wpml-media-dialog")
            .find(".wpml-form-row-title, .wpml-form-row-caption, .wpml-form-row-alt-text, .wpml-form-row-description")
            .hide();
    }

    function resetProgressAnimation() {
        $(".wpml-media-dialog").find(".spinner").remove();
    }

    dialogBox.find(".js-button-copy").click(function (event) {
        event.preventDefault();
        var formRow = $(this).closest('.wpml-form-row');
        var originalInput = formRow.find('input[id$="original"],textarea[id$="original"]');
        var translationInput = formRow.find('input[id$="translation"],textarea[id$="translation"]');
        if (translationInput.val() !== originalInput.val()) {
            translationInput.val(originalInput.val());
            enableFormSave();
        }
        return false;
    });

    function triggerMediaUpload(event) {
        event.preventDefault();
        $("#wpml-media-file-upload-form").find("input[type=file]").trigger("click");
        return false;
    }

    function restoreMediaFile(event) {
        event.preventDefault();

        var imagesRow = $(this).closest(".wpml-form-row");
        var originalImage = imagesRow.find(".wpml-media-original-image img");
        var translatedImage = imagesRow.find(".wpml-media-translation-image img");

		dialogForm.find("input[name=update-media-file]").val(0);

        translatedImage.attr("src", originalImage.attr("src"));

        dialogForm.find("input[name=restore-media]").val(1);

        enableUsingOriginalMediaFile();
        enableFormSave();

        return false;
    }

    $('.js-wpml-media-revert').on('click', 'a', restoreMediaFile);

    $("#wpml-media-dialog .wpml-form-row").on("click", ".wpml-media-translation-image", triggerMediaUpload);

    $("#wpml-media-file-upload-form").find("input[type=file]").change(
        function (e) {
            var file = $(this)[0].files[0];
            var upload = new Upload(file);
            upload.doUpload();
        }
    );

    // Async file upload
    var Upload = function (file) {
        this.file = file;
        this.progressBar = $('#wpml-media-upload-progress-animation');
    };

    Upload.prototype.getType = function () {
        return this.file.type;
    };
    Upload.prototype.getSize = function () {
        return this.file.size;
    };
    Upload.prototype.getName = function () {
        return this.file.name;
    };
    Upload.prototype.doUpload = function () {
        var that = this;
        var formData = new FormData();

        this.resetError();

        var attachmentId = 0;

        formData.append("file", this.file, this.getName());
        var fields = $("#wpml-media-file-upload-form").serializeArray();
        $.each(fields, function (i, field) {
            formData.append(field.name, field.value);

            if (field.name == 'attachment-id') {
                attachmentId = field.value;
            }
        });

        that.progressBar.show();

        $.ajax({
            type: "POST",
            url: ajaxurl,
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener("progress", that.progressHandling, false);
                }
                return myXhr;
            },
            success: function (response) {
                that.progressBar.hide();
                if (response.success) {
                    var translatedImgTag = $('#wpml-media-dialog .wpml-form-row .wpml-media-upload-handle img');
                    translatedImgTag.attr('src', response.data.thumb);
                    dialogForm.find("input[name=translated-attachment-id]").val(response.data.attachment_id);
					if (translatedImgTag.hasClass('is-non-image')) {
						dialogForm.find('.wpml-media-translated-title').html(response.data.name);
					}

                    enableFormSave();
                    dialogForm.find("input[name=update-media-file]").val(1);

                    enableUsingTranslatedMediaFile();

                    // Reset 'file' field
                    $("#wpml-media-file-upload-form").find("input[type=file]").val("");
                } else {
                    that.setError(response.data);
                }
            },
            async: true,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            timeout: 60000
        });
    };

    Upload.prototype.progressHandling = function (event) {
        var percent = 0;
        var position = event.loaded || event.position;
        var total = event.total;
        var progress_bar_id = "#wpml-media-upload-progress-animation";
        if (event.lengthComputable) {
            percent = Math.ceil(position / total * 100);
        }
        $(progress_bar_id + " .upload-progress-bar").css("width", +percent + "%");
        $(progress_bar_id + " .status").text(percent + "%");
    };

    Upload.prototype.setError = function (text) {
        $("#wpml-media-upload-error").html(text);
    };

    Upload.prototype.resetError = function () {
        this.setError('');
    };


    function showTextsChangedNotice(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode >= 32 || charCode == 8) {
            dialogBox.find('.text-change-notice').show();
        }
    }

    dialogForm.on("keyup", "input, textarea", showTextsChangedNotice);

    function dismissTextsChangedNotice() {
        dialogBox.find('.text-change-notice').fadeOut();
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'wpml_media_editor_text_edit_notice_dismissed'},
            success: function () {
            },
        })
        return false;
    }

    dialogBox.find('.text-change-notice').on('click', '.notice-dismiss', dismissTextsChangedNotice);


});