<script type="text/javascript">
    (function ($) {

        var get_valid_ajaxurl = function () {
            var $URL = ajaxurl;
            if (typeof export_id != "undefined") {
                if ($URL.indexOf("?") == -1) {
                    $URL += '?id=' + export_id;
                }
                else {
                    $URL += '&id=' + export_id;
                }
            }
            return $URL;
        };

        // Preview export file
        var doPreview = function (ths, tagno, elementId) {

            $('.wp-all-export-edit-column.cc').css('visibility', 'hidden');

            ths.pointer({
                content: '<div class="wpallexport-preview-preload wpallexport-pointer-preview"></div>',
                position: {
                    edge: 'right',
                    align: 'center'
                },
                pointerWidth: 715,
                close: function () {

                    if(window.$pmxeBackupElementContent){
                        window.$pmxeBackupElement.html(window.$pmxeBackupElementContent);
                    }
                    $.post(ajaxurl, {
                        pointer: 'pksn1',
                        action: 'dismiss-wp-pointer'
                    });
                    $('.wp-all-export-edit-column.cc').css('visibility', 'visible');
                }
            }).pointer('open');

            var $pointer = $('.wpallexport-pointer-preview').parents('.wp-pointer').first();

            var $leftOffset = ($(window).width() - 715) / 2;

            $pointer.css({'position': 'fixed', 'top': '15%', 'left': $leftOffset + 'px'});

            var $form = $('form.wpallexport-step-3');
            var requestData = $form.serializeArray();

            if(elementId) {

                var $element = $('.custom_column[rel="' + elementId + '"]')
                window.$pmxeBackupElementContent = $element.html();
                window.$pmxeBackupElement = $element;

                //cc_label
                $element.find('input[name="cc_label[]"]').val($('input.column_name').val());
                //cc_php
                $element.find('input[name="cc_php[]"]').val($('#coperate_php').is(':checked') ? '1' : '0');
                //cc_code
                $element.find('input[name="cc_code[]"]').val($('.php_code').val());
                //cc_sql
                $element.find('input[name="cc_sql[]"]').val($('textarea.column_value').val());
                //cc_options
                $element.find('input[name="cc_options[]"]').val($('select[name=column_value_type]').find('option:selected').attr('options'));
                //cc_type
                $element.find('input[name="cc_type[]"]').val($('select[name=column_value_type]').val());
                //cc_value
                $element.find('input[name="cc_value[]"]').val($('select[name=column_value_type]').find('option:selected').attr('label'));
                //cc_name
                $element.find('input[name="cc_name[]"]').val($('input.column_name').val());
                //cc_settings
                $element.find('input[name="cc_settings[]"]').val("");
                //cc_combine_multiple_fields
                $element.find('input[name="cc_combine_multiple_fields[]"]').val($('input[name="combine_multiple_fields"]:checked').val());
                //cc_combine_multiple_fields_value
                $element.find('input[name="cc_combine_multiple_fields_value[]"]').val($('#combine_multiple_fields_value').val());

                requestData = $form.serializeArray();


            } else {
                var $addAnotherForm = $('fieldset.wp-all-export-edit-column');
                var $elementName = $addAnotherForm.find('input.column_name');

                var $phpFunction = $addAnotherForm.find('.php_code:visible');

                // element type
                var $elementType = $addAnotherForm.find('select[name=column_value_type]');
                // element label, options and other stuff
                var $elementDetails = $elementType.find('option:selected');

                requestData.push({
                    name: "ids[]",
                    value:1
                });
                requestData.push({
                    name: "cc_label[]",
                    value: $elementName.val()
                });
                requestData.push({
                    name: "cc_php[]",
                    value: $addAnotherForm.find('#coperate_php').is(':checked') ? '1' : '0'
                });
                requestData.push({
                    name: "cc_code[]",
                    value: $phpFunction.val()
                });
                requestData.push({
                    name: "cc_sql[]",
                    value:  $addAnotherForm.find('textarea.column_value').val()
                });
                requestData.push({
                    name: "cc_type[]",
                    value: $elementType.val()
                });
                requestData.push({
                    name: "cc_options[]",
                    value:1
                });
                requestData.push({
                    name: "cc_value[]",
                    value: $elementDetails.attr('label')
                });
                requestData.push({
                    name: "cc_name[]",
                    value: $elementName.val()
                });
                requestData.push({
                    name: "cc_settings[]",
                    value: ""
                });
                requestData.push({
                    name: "cc_options[]",
                    value: $elementDetails.attr('options')
                });
                requestData.push({
                    name: "cc_combine_multiple_fields[]",
                    value: $addAnotherForm.find('input[name="combine_multiple_fields"]:checked').val()
                });
                requestData.push({
                    name: "cc_combine_multiple_fields_value[]",
                    value: $addAnotherForm.find('#combine_multiple_fields_value').val()
                });

            }
            
            requestData = $.param(requestData);

            var request = {
                action: 'wpae_preview',
                data: requestData,
                tagno: tagno,
                multiple_field_contents: $('#multiple_field_contents').val(),
                security: wp_all_export_security
            };
            var url = get_valid_ajaxurl();
            var show_cdata = $('#show_cdata_in_preview').val();

            if (url.indexOf("?") == -1) {
                url += '?show_cdata=' + show_cdata;
            } else {
                url += '&show_cdata=' + show_cdata;
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: request,
                success: function (response) {

                    ths.pointer({'content': response.html});

                    $pointer.css({'position': 'fixed', 'top': '15%', 'left': $leftOffset + 'px'});

                    var $preview = $('.wpallexport-preview');

                    $preview.parent('.wp-pointer-content').removeClass('wp-pointer-content').addClass('wpallexport-pointer-content');

                    $preview.find('.navigation a').unbind('click').die('click').live('click', function () {

                        tagno += '#prev' == $(this).attr('href') ? -1 : 1;

                        doPreview(ths, tagno);

                    });

                },
                error: function (jqXHR, textStatus) {
                    // Handle an eval error
                    if (jqXHR.responseText.indexOf('[[ERROR]]') !== -1) {
                        vm.preiviewText = $('.wpallexport-preview-title').text();

                        var json = jqXHR.responseText.split('[[ERROR]]')[1];
                        json = $.parseJSON(json);
                        ths.pointer({
                            'content': '<div id="post-preview" class="wpallexport-preview">' +
                            '<p class="wpallexport-preview-title">' + json.title + '</p>\
						<div class="wpallexport-preview-content">' + json.error + '</div></div></div>'
                        });

                        $pointer.css({'position': 'fixed', 'top': '15%', 'left': $leftOffset + 'px'});

                    } else {
                        ths.pointer({
                            'content': '<div id="post-preview" class="wpallexport-preview">' +
                            '<p class="wpallexport-preview-title">An error occured</p>\
                            <div class="wpallexport-preview-content">An unknown error occured</div></div></div>'
                        });
                        $pointer.css({'position': 'fixed', 'top': '15%', 'left': $leftOffset + 'px'});
                    }

                },
                dataType: "json"
            });

        };

        function processElementName($element, $elementName) {
            if ($element.find('input[name^=cc_type]').val().indexOf('image_') !== -1) {
                $elementName = 'Image ' + $elementName;
            }
            if ($element.find('input[name^=cc_type]').val().indexOf('attachment_') !== -1) {
                $elementName = 'Attachment ' + $elementName;
            }
            return $elementName;
        }

        $(function () {

            var $addAnotherForm = $('fieldset.wp-all-export-edit-column');
            $addAnotherForm.click(function () {
                var rel = $addAnotherForm.attr('rel');
            });

            $('select[name="column_value_type"]').change(function(){
                $('.column_name').val($(this).find('option:selected').text());
            });

            $('.column_name').keyup(function(){
                if($(this).val != '' && $(this).hasClass('error')) {
                    $(this).removeClass('error');
                }
            });

            $('.preview_action').unbind('click').click(function (event) {
                console.log('Preview action');
                if($('.column_name').val() == '') {
                    $('.column_name').addClass('error');
                    event.stopPropagation();
                    return false;
                }

                var elementId = $addAnotherForm.attr('rel');
                elementId = parseInt(elementId);

                doPreview($(this), 1, elementId);

            });

            $('input[name="combine_multiple_fields"]').change(function () {
                if ($(this).val() == '1') {
                    $('#combine_multiple_fields_value_container').slideDown();
                    $('#combine_multiple_fields_data').slideDown();
                    $('.php_snipped').slideUp();

                    $('.export-single').slideUp();
                    $('.single-field-options').slideUp();
                    $('.column_name').val('');
                } else {
                    $('#combine_multiple_fields_value_container').slideUp();
                    $('#combine_multiple_fields_data').slideUp();
                    $('.export-single').slideDown();
                    $('.php_snipped').slideDown();
                    $('.single-field-options').slideDown();
                    $('.column_name').val($('select[name="column_value_type"]').find('option:selected').text());

                }
            });

            $('#combine_multiple_fields_value').droppable({
                drop: function (event, ui) {

                    function getCodeToPlace($elementName) {
                        return "{" + $elementName + "}";
                    }

                    if (ui.draggable.find('input[name^=rules]').length) {
                        var content = "";
                        $('li.' + ui.draggable.find('input[name^=rules]').val()).each(function () {
                            var $elementName = $(this).find('input[name^=cc_name]').val();
                            $elementName = processElementName($(this), $elementName);
                            content = content + getCodeToPlace($elementName);
                        });

                    }
                    else {
                        var $elementName = ui.draggable.find('.custom_column').find('input[name^=cc_name]').val();
                        var $element = ui.draggable.find('.custom_column');
                        $elementName = processElementName($element, $elementName);

                        $(this).val($(this).val() + getCodeToPlace($elementName));
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

            });

            var availableNames = [
            ];

            $('#available_data .wpallexport-xml-element').each(function(){

                var text = $(this).html();
                if(availableNames.indexOf(text) < 0) {
                    availableNames.push(text);
                }
            });

            $('.column_name').autocomplete({
                source: availableNames,
                close: function() {
                    return false;
                }
            });
        });

    })(jQuery);
</script>
<form>
    <div class="wp-all-export-field-options" style="width: 54%; float:left; max-height: 770px;">
        <div class="input" style="margin-bottom: 15px;">
            <label style="padding:4px; display: block;" class="wpae_column_name"><?php _e('Column name', 'wp_all_export_plugin'); ?></label>
            <label style="padding:4px; display: none;" class="wpae_element_name"><?php _e('Element name', 'wp_all_export_plugin'); ?></label>
            <div class="clear"></div>
            <input type="text" class="column_name" value="" style="width:100%; padding: 8px; border-radius: 5px; color: #000;"/>
        </div>
        <!-- SINGLE ELEMENT -->
        <div class="input">
            <label>
            <input type="radio" name="combine_multiple_fields" value="0" checked="checked" /> <?php _e('Select a field to export', 'wp_all_export_plugin') ?></label>
        </div>
        <div class="input export-single wpae-select-field" style="margin-left:25px; margin-top:10px;">
            <div class="clear"></div>
            <?php echo $available_fields_view; ?>
        </div>

        <!-- Advanced Field Options -->
        <?php include_once 'advanced_field_options.php'; ?>

        <!-- COMBINE ELEMENTS -->
        <div class="input" style="margin-top: 5px;">
            <label><input type="radio" name="combine_multiple_fields" value="1" /> <?php _e('Custom export field', 'wp_all_export_plugin') ?></label>
        </div>
        <div class="elements export-multiple" id="combine_multiple_fields_value_container" style="margin-top: 10px; margin-left: 25px; display: none;">
            <textarea placeholder="<?php _e('You can drag and drop fields from Available Data, add static text, and use PHP functions', PMXE_Plugin::LANGUAGE_DOMAIN); ?>" id="combine_multiple_fields_value" style="width: 100%;" rows="7"></textarea>
        </div>

        <!-- Functions editor -->
        <?php include_once 'functions_editor.php'; ?>

    </div>
    <div style="width: 35%; float: right; margin-right: 33px; margin-top:10px; display: none;" class="wpae_available_data export-multiple"
         id="combine_multiple_fields_data">
        <fieldset id="available_data" class="optionsset rad4 wpae_available_data dialog-available-data" style="margin-bottom: 10px; ">
            <div class="title"><?php _e('Available Data', 'wp_all_export_plugin'); ?></div>
            <div class="wpallexport-xml resetable wpallexport-pointer-data available-data">
                <ul>
                    <?php echo $available_data_view; ?>
                </ul>
            </div>
        </fieldset>
    </div>
    <div style="clear:both;"></div>
    <div class="input wp-all-export-edit-column-buttons">
        <input type="button" class="close_action" value="<?php _e("Cancel", "wp_all_export_plugin"); ?>"
               style="border: none;"/>
        <input type="button" class="preview_action" value="<?php _e("Preview", "wp_all_export_plugin"); ?>"
               style="border: none; margin-left: 195px;"/>
        <input type="button" class="delete_action" value="<?php _e("Delete", "wp_all_export_plugin"); ?>"
               style="border: none;"/>
        <input type="button" class="save_action" value="<?php _e("Save", "wp_all_export_plugin"); ?>"
               style="border: none;"/>
    </div>
</form>