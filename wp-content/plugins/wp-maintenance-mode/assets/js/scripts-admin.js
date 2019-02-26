jQuery(function($) {
    /**
     * TABS
     */
    var hash = window.location.hash;
    if (hash !== '') {
        $('.nav-tab-wrapper').children().removeClass('nav-tab-active');
        $('.nav-tab-wrapper a[href="' + hash + '"]').addClass('nav-tab-active');

        $('.tabs-content').children().addClass('hidden');
        $('.tabs-content div' + hash.replace('#', '#tab-')).removeClass('hidden');
    }

    $('.nav-tab-wrapper a').click(function() {
        var tab_id = $(this).attr('href').replace('#', '#tab-');

        // active tab
        $(this).parent().children().removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // active tab content
        $('.tabs-content').children().addClass('hidden');
        $('.tabs-content div' + tab_id).removeClass('hidden');
    });

    /**
     * COLOR PICKER
     */
    $('.color_picker_trigger').wpColorPicker();

    /**
     * CHOSEN.JS MULTISELECT
     * @used for "Backend role" and "Frontend role" -> General tab
     */
    $('.chosen-select').chosen({disable_search_threshold: 10});

    /**
     * BACKGROUND UPLOADER
     */
    var image_custom_uploader;
    $('#upload_image_trigger').click(function(e) {
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (image_custom_uploader) {
            image_custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        image_custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Upload Background',
            button: {
                text: 'Choose Background'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        image_custom_uploader.on('select', function() {
            attachment = image_custom_uploader.state().get('selection').first().toJSON();
            var url = '';
            url = attachment.url;
            $('.upload_image_url').val(url);
        });

        //Open the uploader dialog
        image_custom_uploader.open();
    });

    /**
     * SHOW DESIGN BACKGROUND TYPE BASED ON SELECTED FIELD
     */
    show_bg_type = function(selected_val) {
        $('.design_bg_types').hide();
        $('#show_' + selected_val).show();
    };

    show_bg_type($('#design_bg_type').val());

    $('#design_bg_type').change(function() {
        var selected_val = $(this).val();

        show_bg_type(selected_val);
    });

    /**
     * PREDEFINED BACKGROUND
     */
    $('ul.bg_list li').click(function() {
        $(this).parent().children().removeClass('active');
        $(this).addClass('active');
    });

    /**
     * SUBSCRIBERS EXPORT
     */
    $('#subscribers-export').click(function() {
        $('<iframe />').attr('src', wpmm_vars.ajax_url + '?action=wpmm_subscribers_export').appendTo('body').hide();
    });

    /**
     * SUBSCRIBERS EMPTY LIST
     *
     * @since 2.0.4
     */
    $('#subscribers-empty-list').click(function() {
        $.post(wpmm_vars.ajax_url, {
            action: 'wpmm_subscribers_empty_list'
        }, function(response) {
            if (!response.success) {
                alert(response.data);
                return false;
            }

            $('#subscribers_wrap').html(response.data);
        }, 'json');
    });

    /**
     * RESET SETTINGS
     */
    $('.reset_settings').click(function() {
        var tab = $(this).data('tab'),
                nonce = $('#tab-' + tab + ' #_wpnonce').val();

        $.post(wpmm_vars.ajax_url, {
            action: 'wpmm_reset_settings',
            tab: tab,
            _wpnonce: nonce
        }, function(response) {
            if (!response.success) {
                alert(response.data);
                return false;
            }

            window.location.reload(true);
        }, 'json');
    });

    /**
     * COUNTDOWN TIMEPICKER
     */
    $('.countdown_start').datetimepicker({timeFormat: 'HH:mm:ss', dateFormat: 'dd-mm-yy'});


    /**
     * BOT AVATAR UPLOADER
     */
    var avatar_custom_uploader;
    $('#avatar_upload_trigger').click(function(e) {
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (avatar_custom_uploader) {
            avatar_custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        avatar_custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Upload Avatar',
            button: {
                text: 'Choose picture'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        avatar_custom_uploader.on('select', function() {
            attachment = avatar_custom_uploader.state().get('selection').first().toJSON();
            var url = '';
            url = attachment.url;
            $('.upload_avatar_url').val(url);
        });

        //Open the uploader dialog
        avatar_custom_uploader.open();
    });

});