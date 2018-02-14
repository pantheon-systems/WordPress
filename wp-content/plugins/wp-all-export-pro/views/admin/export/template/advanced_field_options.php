<div class="single-field-options" style="margin-left: 25px; margin-bottom: 10px;">

    <div class="wp-all-export-advanced-field-options-content" style="padding-bottom: 0;">
        <!-- Options for SQL field -->
        <div class="input cc_field sql_field_type">
            <a href="#help" rel="sql" class="help" style="display:none;"
               title="<?php _e('%%ID%% will be replaced with the ID of the post being exported, example: SELECT meta_value FROM wp_postmeta WHERE post_id=%%ID%% AND meta_key=\'your_meta_key\';', 'wp_all_export_plugin'); ?>">?</a>
            <textarea style="width:100%;" rows="5" class="column_value"></textarea>
        </div>
        <!-- Options for ACF Repeater field -->
        <div class="input cc_field repeater_field_type">
            <input type="hidden" name="repeater_field_item_per_line" value="0"/>
            <input type="checkbox" id="repeater_field_item_per_line" class="switcher"
                   name="repeater_field_item_per_line" value="1" style="margin: 2px;"/>
            <label
                for="repeater_field_item_per_line"><?php _e("Display each repeater row in its own csv line", "wp_all_export_plugin"); ?></label>
            <div class="input switcher-target-repeater_field_item_per_line"
                 style="margin-top: 10px; padding-left: 15px;">
                <input type="hidden" name="repeater_field_fill_empty_columns" value="0"/>
                <input type="checkbox" id="repeater_field_fill_empty_columns" name="repeater_field_fill_empty_columns"
                       value="1"/>
                <label
                    for="repeater_field_fill_empty_columns"><?php _e("Fill in empty columns", "wp_all_export_plugin"); ?></label>
                <a href="#help" class="wpallexport-help" style="position: relative; top: 0px;"
                   title="<?php _e('If enabled, each repeater row will appear as its own csv line with all post info filled in for every column.', 'wp_all_export_plugin'); ?>">?</a>
            </div>
        </div>
        <!-- Options for Content field -->
        <div class="input cc_field content_field_type">
            <input type="hidden" name="export_images_from_gallery" value="0"/>
            <input type="checkbox" id="export_images_from_gallery" name="export_images_from_gallery" value="1"
                   style="margin: 2px;"/>
            <label
                for="export_images_from_gallery"><?php _e("Export images from Gallery shortcodes in post content", "wp_all_export_plugin"); ?></label>
        </div>
        <!-- Options for Image field from Media section -->
        <div class="input cc_field image_field_type">
            <div class="input">
                <input type="hidden" name="image_field_is_export_featured" value="0"/>
                <input type="checkbox" id="is_image_export_featured" name="image_field_is_export_featured" value="1"
                       style="margin: 2px;" checked="checked"/>
                <label
                    for="is_image_export_featured"><?php _e("Export featured image", "wp_all_export_plugin"); ?></label>
            </div>
            <div class="input">
                <input type="hidden" name="image_field_is_export_attached_images" value="0"/>
                <input type="checkbox" id="is_image_export_attached_images" class="switcher"
                       name="image_field_is_export_attached_images" value="1" style="margin: 2px;" checked="checked"/>
                <label
                    for="is_image_export_attached_images"><?php _e("Export attached images", "wp_all_export_plugin"); ?></label>
                <div class="switcher-target-is_image_export_attached_images" style="margin: 5px 2px;">
                    <label><?php _e("Separator", "wp_all_export_plugin"); ?></label>
                    <input type="text" name="image_field_separator" value="|" style="width: 40px; text-align:center;">
                </div>
            </div>
        </div>
        <!-- Options for Date field -->
        <div class="input cc_field date_field_type">
            <select class="date_field_export_data" style="width: 100%; height: 30px;">
                <option value="unix"><?php _e("UNIX timestamp - PHP time()", "wp_all_export_plugin"); ?></option>
                <option value="php"><?php _e("Natural Language PHP date()", "wp_all_export_plugin"); ?></option>
            </select>
            <div class="input pmxe_date_format_wrapper">
                <label
                    style="padding:4px; display: block;"><?php _e("date() Format", "wp_all_export_plugin"); ?></label>
                <input type="text" class="pmxe_date_format" value="" placeholder="Y-m-d" style="width: 100%;"/>
            </div>
        </div>
        <!-- Options for Up/Cross sells products -->
        <div class="input cc_field linked_field_type">
            <select class="linked_field_export_data" style="width: 100%; height: 30px;">
                <option value="sku"><?php _e("Product SKU", "wp_all_export_plugin"); ?></option>
                <option value="id"><?php _e("Product ID", "wp_all_export_plugin"); ?></option>
                <option value="name"><?php _e("Product Name", "wp_all_export_plugin"); ?></option>
            </select>
        </div>
    </div>
    <!-- PHP snippet options -->
    <div class="input php_snipped">
        <input type="checkbox" id="coperate_php" name="coperate_php" value="1" class="switcher" style="margin: 2px;"/>
        <label
            for="coperate_php"><?php _e("Export the value returned by a PHP function", "wp_all_export_plugin"); ?></label>
        <a href="#help" class="wpallexport-help"
           title="<?php _e('The value of the field chosen for export will be passed to the PHP function.', 'wp_all_export_plugin'); ?>"
           style="top: 0;">?</a>
        <div class="switcher-target-coperate_php" style="margin-top:5px; padding-left: 3px;">
            <?php echo "&lt;?php "; ?>
            <input type="text" class="php_code" value="" style="width:50%;" placeholder='your_function_name'/>
            <?php echo "(\$value); ?&gt;"; ?>

        </div>
    </div>

</div>
