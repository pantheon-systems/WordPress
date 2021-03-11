var sucessRequests = 0;
var totalRequests = 0;
var name = "";

(function ($) {

    "use strict";

    $(document).ready(function () {
        owpDemoImport.init();
        owpCustomizer.init();
    });

    var owpDemoImport = {
        importData: {},
        allowPopupClosing: true,
        init: function () {
            var that = this;

            // Categories filter
            this.categoriesFilter();

            // Search functionality.
            $('.owp-search-input').on('keyup', function () {
                if (0 < $(this).val().length) {
                    // Hide all items.
                    $('.owp-demo-wrap .themes').find('.theme-wrap').hide();

                    // Show just the ones that have a match on the import name.
                    $('.owp-demo-wrap .themes').find('.theme-wrap[data-name*="' + $(this).val().toLowerCase() + '"]').show();
                } else {
                    $('.owp-demo-wrap .themes').find('.theme-wrap').show();
                }
            });

            // Prevent the popup from showing when the live preview button
            $('.owp-demo-wrap .theme-actions a.button').on('click', function (e) {
                e.stopPropagation();
            });

            $('.owp-open-popup').click(function (e) {
                if ($(this).hasClass("active")) {
                    $(this).removeClass('active');
                    $(".install-demos-button").addClass('disabled');
                    $(".install-demos-button").attr("disabled", true);
                } else {
                    $('.owp-open-popup').removeClass('active');
                    $(this).addClass('active');
                    $(".install-demos-button").removeClass('disabled');
                    $(".install-demos-button").attr("disabled", false);
                }
            });
            // Get demo data
            $('.install-demos-button').click(function (e) {
                e.preventDefault();
                if ($('.owp-open-popup.active').length > 0) {
                    // Vars
                    var $selected_demo = $('.owp-open-popup.active:first').data('demo-id'),
                            $loading_icon = $('.preview-' + $selected_demo),
                            $disable_preview = $('.preview-all-' + $selected_demo);

                    $(".demo-import-loader").show();

                    that.getDemoData($selected_demo);
                } else {
                    window.location.href = $(this).attr("data-next_step");
                }
            });

            $(document).on('click', '.install-now', this.installNow);
            $(document).on('click', '.activate-now', this.activatePlugins);
            $(document).on('wp-plugin-install-success', this.installSuccess);
            $(document).on('wp-plugin-installing', this.pluginInstalling);
            $(document).on('wp-plugin-install-error', this.installError);

        },
        // Category filter.
        categoriesFilter: function () {

            // Cache selector to all items
            var $items = $('.owp-demo-wrap .themes').find('.theme-wrap'),
                    fadeoutClass = 'owp-is-fadeout',
                    fadeinClass = 'owp-is-fadein',
                    animationDuration = 200;

            // Hide all items.
            var fadeOut = function () {
                var dfd = $.Deferred();

                $items.addClass(fadeoutClass);

                setTimeout(function () {
                    $items.removeClass(fadeoutClass).hide();

                    dfd.resolve();
                }, animationDuration);

                return dfd.promise();
            };

            var fadeIn = function (category, dfd) {
                var filter = category ? '[data-categories*="' + category + '"]' : 'div';

                if ('all' === category) {
                    filter = 'div';
                }

                $items.filter(filter).show().addClass('owp-is-fadein');

                setTimeout(function () {
                    $items.removeClass(fadeinClass);

                    dfd.resolve();
                }, animationDuration);
            };

            var animate = function (category) {
                var dfd = $.Deferred();

                var promise = fadeOut();

                promise.done(function () {
                    fadeIn(category, dfd);
                });

                return dfd;
            };

            $('.owp-navigation-link').on('click', function (event) {
                event.preventDefault();

                // Remove 'active' class from the previous nav list items.
                $(this).parent().siblings().removeClass('active');

                // Add the 'active' class to this nav list item.
                $(this).parent().addClass('active');

                var category = this.hash.slice(1);

                // show/hide the right items, based on category selected
                var $container = $('.owp-demo-wrap .themes');
                $container.css('min-width', $container.outerHeight());

                var promise = animate(category);

                promise.done(function () {
                    $container.removeAttr('style');
                });
            });

        },
        // Get demo data.
        getDemoData: function (demo_name) {
            var that = this;

            // Get import data
            $.ajax({
                url: owpDemos.ajaxurl,
                type: 'get',
                data: {
                    action: 'owp_ajax_get_import_data',
                    demo_name: demo_name,
                    security: owpDemos.owp_import_data_nonce
                },
                complete: function (data) {
                    $(".demo-import-loader").hide();
                    that.importData = $.parseJSON(data.responseText);
                }
            });

            // Run the import
            $.ajax({
                url: owpDemos.ajaxurl,
                type: 'get',
                data: {
                    action: 'owp_wizard_ajax_get_demo_data',
                    demo_name: demo_name,
                    demo_data_nonce: owpDemos.demo_data_nonce
                },
                complete: function (data) {
                    console.log(data);
                    $(".owp-demo-wrap").html(data.responseText);

                    $('html,body').animate({
                        scrollTop: $("#owp-demo-plugins").offset().top
                    }, 500);

                    that.runPopup(data);

                }

            });

        },
        // Run popup.
        runPopup: function (data) {
            var that = this


            // Close popup
            $('.owp-demo-popup-close, .owp-demo-popup-overlay').on('click', function (e) {
                e.preventDefault();
                if (that.allowPopupClosing === true) {
                    that.closePopup();
                }
            });

            // Display the step two
            $('.owp-plugins-next').on('click', function (e) {
                e.preventDefault();

                // Hide step one
                $('#owp-demo-plugins').hide();

                // Display step two
                $('#owp-demo-import-form').show();

            });

            // if clicked on import data button
            $('#owp-demo-import-form').submit(function (e) {
                e.preventDefault();

                // Vars
                var demo = $(this).find('[name="owp_import_demo"]').val(),
                        nonce = $(this).find('[name="owp_import_demo_data_nonce"]').val(),
                        contentToImport = [];

                // Check what need to be imported
                $(this).find('input[type="checkbox"]').each(function () {
                    if ($(this).is(':checked') === true) {
                        contentToImport.push($(this).attr('name'));
                    }
                });

                // Hide the checkboxes and show the loader
                $(this).hide();
                $('.owp-loader').show();
                $('#owp-demo-import-form,#owp-demo-plugins').hide();
                // Start importing the content
                totalRequests = contentToImport.length;
                that.importContent({
                    demo: demo,
                    nonce: nonce,
                    contentToImport: contentToImport,
                    isXML: $('#owp_import_xml').is(':checked')
                });
            });

        },
        // importing the content.
        importContent: function (importData) {
            var that = this,
                    currentContent,
                    importingLimit,
                    timerStart = Date.now(),
                    ajaxData = {
                        owp_import_demo: importData.demo,
                        owp_import_demo_data_nonce: importData.nonce
                    };

            this.allowPopupClosing = false;
            $('.owp-demo-popup-close').fadeOut();

            // When all the selected content has been imported
            if (importData.contentToImport.length === 0) {
                if (sucessRequests != totalRequests) {
                    $('.owp-loader').hide();
                    $(".owp-error").show();
                    $(".wizard-install-demos-buttons-wrapper.final-step").show();

                } else {
                    // Show the imported screen after 1 second
                    setTimeout(function () {
                        $('.owp-loader').hide();
                        $('.owp-last').show();
                        window.location.href = $(".wizard-install-demos-buttons-wrapper.final-step .skip-btn").attr("href");
                    }, 1000);

                    // Notify the server that the importing process is complete
                    $.ajax({
                        url: owpDemos.ajaxurl,
                        type: 'post',
                        data: {
                            action: 'owp_after_import',
                            owp_import_demo: importData.demo,
                            owp_import_demo_data_nonce: importData.nonce,
                            owp_import_is_xml: importData.isXML
                        },
                        complete: function (data) {
                        }
                    });

                    this.allowPopupClosing = true;
                    $('.owp-demo-popup-close').fadeIn();

                    return;
                }
            }

            // Check the content that was selected to be imported.
            for (var key in this.importData) {

                // Check if the current item in the iteration is in the list of importable content
                var contentIndex = $.inArray(this.importData[ key ][ 'input_name' ], importData.contentToImport);

                // If it is:
                if (contentIndex !== -1) {

                    // Get a reference to the current content
                    currentContent = key;

                    // Remove the current content from the list of remaining importable content
                    importData.contentToImport.splice(contentIndex, 1);

                    // Get the AJAX action name that corresponds to the current content
                    ajaxData.action = this.importData[ key ]['action'];

                    // After an item is found get out of the loop and execute the rest of the function
                    break;
                }
            }

            // Tell the user which content is currently being imported
            $('.owp-import-status').append('<p class="owp-importing">' + this.importData[ currentContent ]['loader'] + '</p>');

            // Tell the server to import the current content
            var ajaxRequest = $.ajax({
                url: owpDemos.ajaxurl,
                type: 'post',
                data: ajaxData,
                complete: function (data) {
                    clearTimeout(importingLimit);

                    // Indicates if the importing of the content can continue
                    var continueProcess = true;

                    // Check if the importing of the content was successful or if there was any error
                    if (data.status === 500 || data.status === 502 || data.status === 503) {
                        $('.owp-importing')
                                .addClass('owp-importing-failed')
                                .removeClass('owp-importing')
                                .text(owpDemos.content_importing_error + ' ' + data.status);
                    } else if (data.responseText.indexOf('successful import') !== -1) {
                        $('.owp-importing').addClass('owp-imported').removeClass('owp-importing');
                        sucessRequests++;
                    } else {

                        var errors = $.parseJSON(data.responseText),
                                errorMessage = '';

                        // Iterate through the list of errors
                        for (var error in errors) {
                            errorMessage += errors[ error ];

                            // If there was an error with the importing of the XML file, stop the process
                            if (error === 'xml_import_error') {
                                continueProcess = false;
                            }
                        }

                        // Display the error message
                        $('.owp-importing')
                                .addClass('owp-importing-failed')
                                .removeClass('owp-importing')
                                .text(errorMessage);

                        that.allowPopupClosing = true;
                        $('.owp-demo-popup-close').fadeIn();
                    }

                    // Continue with the loading only if an important error was not encountered
                    if (continueProcess === true) {

                        // Load the next content in the list
                        that.importContent(importData);
                    }

                }
            });

            // Set a time limit of 15 minutes for the importing process.
            importingLimit = setTimeout(function () {

                // Abort the AJAX request
                ajaxRequest.abort();

                // Allow the popup to be closed
                that.allowPopupClosing = true;
                $('.owp-demo-popup-close').fadeIn();

                $('.owp-importing')
                        .addClass('owp-importing-failed')
                        .removeClass('owp-importing')
                        .text(owpDemos.content_importing_error);
            }, 15 * 60 * 1000);

        },
        // Close demo popup.
        closePopup: function () {
            $('html').css({
                'overflow': '',
                'margin-right': ''
            });

            // Hide loader
            $('.preview-icon').hide();
            $('.preview-all').hide();

            // Hide demo popup
            $('#owp-demo-popup-wrap').fadeOut();

            // Remove content in the popup
            setTimeout(function () {
                $('#owp-demo-popup-content').html('');
            }, 600);
        },
        // Install required plugins.
        installNow: function (e) {
            e.preventDefault();

            // Vars
            var $button = $(e.target),
                    $document = $(document);

            if ($button.hasClass('updating-message') || $button.hasClass('button-disabled')) {
                return;
            }

            if (wp.updates.shouldRequestFilesystemCredentials && !wp.updates.ajaxLocked) {
                wp.updates.requestFilesystemCredentials(e);

                $document.on('credential-modal-cancel', function () {
                    var $message = $('.install-now.updating-message');

                    $message
                            .removeClass('updating-message')
                            .text(wp.updates.l10n.installNow);

                    wp.a11y.speak(wp.updates.l10n.updateCancel, 'polite');
                });
            }

            wp.updates.installPlugin({
                slug: $button.data('slug')
            });
        },
        // Activate required plugins.
        activatePlugins: function (e) {
            e.preventDefault();

            // Vars
            var $button = $(e.target),
                    $init = $button.data('init'),
                    $slug = $button.data('slug');

            if ($button.hasClass('updating-message') || $button.hasClass('button-disabled')) {
                return;
            }

            $button.addClass('updating-message button-primary').html(owpDemos.button_activating);

            $.ajax({
                url: owpDemos.ajaxurl,
                type: 'POST',
                data: {
                    action: 'owp_ajax_required_plugins_activate',
                    init: $init,
                },
            }).done(function (result) {

                if (result.success) {

                    $button.removeClass('button-primary install-now activate-now updating-message')
                            .attr('disabled', 'disabled')
                            .addClass('disabled')
                            .text(owpDemos.button_active);

                }

            });
        },
        // Install success.
        installSuccess: function (e, response) {
            e.preventDefault();

            var $message = $('.owp-plugin-' + response.slug).find('.button');

            // Transform the 'Install' button into an 'Activate' button.
            var $init = $message.data('init');

            $message.removeClass('install-now installed button-disabled updated-message')
                    .addClass('updating-message')
                    .html(owpDemos.button_activating);

            // WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
            setTimeout(function () {

                $.ajax({
                    url: owpDemos.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'owp_ajax_required_plugins_activate',
                        init: $init,
                    },
                }).done(function (result) {

                    if (result.success) {

                        $message.removeClass('button-primary install-now activate-now updating-message')
                                .attr('disabled', 'disabled')
                                .addClass('disabled')
                                .text(owpDemos.button_active);

                    } else {
                        $message.removeClass('updating-message');
                    }

                });

            }, 1200);
        },
        // Plugin installing.
        pluginInstalling: function (e, args) {
            e.preventDefault();

            var $card = $('.owp-plugin-' + args.slug),
                    $button = $card.find('.button');

            $button.addClass('updating-message');
        },
        // Plugin install error.
        installError: function (e, response) {
            e.preventDefault();

            var $card = $('.owp-plugin-' + response.slug);

            $card.removeClass('button-primary').addClass('disabled').html(wp.updates.l10n.installFailedShort);
        }

    };

    var owpCustomizer = {
        init: function () {

            var custom_uploader;

            $('.upload_image_button').on("click", function (e) {
                e.preventDefault();
                var element = $(this);
                name = element.attr("data-name");

                //If the uploader object has already been created, reopen the dialog
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }

                //Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });

                //When a file is selected, grab the URL and set it as the text field's value
                custom_uploader.on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    console.log(attachment);
                    $('#' + name).val(attachment.id);
                    $('#' + name + "-img").attr("src", attachment.url).show();
                    $('.remove_image_button[data-name="' + name + '"]').show();
                });

                //Open the uploader dialog
                custom_uploader.open();
            });

            // The "Remove" button (remove the value from input type='hidden')
            $('.remove_image_button').on('click', function (e) {
                e.preventDefault();
                var element = $(this);
                var name = element.attr("data-name");
                $('#' + name).val('');
                $('#' + name + "-img").attr("src", "").hide();
                element.hide();
                return false;
            });


            if ($(".color-picker-field").length > 0) {
                $(".color-picker-field").each(function (){
                    $(this).wpColorPicker();
                });

            }
        }
    };

})(jQuery);