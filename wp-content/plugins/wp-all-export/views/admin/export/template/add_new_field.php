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
                return false;
            });

            $('input[name="combine_multiple_fields"]').change(function () {
                if ($(this).val() == '1') {
                    $('#combine_multiple_fields_value_container').slideDown();
                    $('#combine_multiple_fields_data').slideDown();
                    $('.php_snipped').slideUp();
                    $('.wp-all-export-advanced-field-options-content').hide();
                    $('.export-single').slideUp();
                    $('.single-field-options').slideUp();
                    $('.column_name').val('');

                    $('.wpallexport-plugin .save_action').addClass('disabled');

                } else {
                    $('#combine_multiple_fields_value_container').slideUp();
                    $('#combine_multiple_fields_data').slideUp();
                    $('.export-single').slideDown();
                    $('.php_snipped').slideDown();
                    $('.single-field-options').slideDown();
                    $('.column_name').val($('select[name="column_value_type"]').find('option:selected').text());
                    $('.wp-all-export-advanced-field-options-content').show();

                    $('.wpallexport-plugin .save_action').removeClass('disabled');
                }
            });

            $('#combine_multiple_fields_value').droppable({
                drop: function (event, ui) {

                    $('.add-new-field-notice').slideDown();
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

            });

            $('#combine_multiple_fields_value').keydown(function (event, ui) {

                    $('.add-new-field-notice').slideDown();
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
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
    <div class="wp-all-export-field-options" style="width: 54%; float:left; max-height: 70vh;">
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
            <div class="wpallexport-free-edition-notice add-new-field-notice" style="margin: 15px 0; display: none;">
                <a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=custom-fields&utm_campaign=free+wp+all+export+plugin">
                    <?php _e('Upgrade to Pro to use Custom Export Fields','wp_all_export_plugin');?></a>
            </div>
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
        <input type="button" class="delete_action" value="<?php _e("Delete", "wp_all_export_plugin"); ?>"
               style="border: none;"/>
        <input type="button" class="save_action" value="<?php _e("Save", "wp_all_export_plugin"); ?>"
               style="border: none;"/>
    </div>
</form>