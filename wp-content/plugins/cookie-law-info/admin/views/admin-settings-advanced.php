<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="cookie-law-info-tab-content" data-id="<?php echo $target_id;?>">
    <h3>Advanced</h3>
    <p><?php _e('Sometimes themes apply settings that clash with plugins. If that happens, try adjusting these settings.', 'cookie-law-info'); ?></p>

    <table class="form-table">
        <!--
        <tr valign="top">
                <th scope="row"><label for="use_colour_picker_field">Use colour picker on this page?</label></th>
                <td>
                        <input type="radio" id="use_colour_picker_field_yes" name="use_colour_picker_field" class="styled" value="true" <?php //echo ( $the_options['use_colour_picker'] == true ) ? ' checked="checked" />' : ' />';  ?> Yes
                        <input type="radio" id="use_colour_picker_field_no" name="use_colour_picker_field" class="styled" value="false" <?php //echo ( $the_options['use_colour_picker'] == false ) ? ' checked="checked" />' : ' />';  ?> No
                        <span class="cli_form_help">You will need to refresh your browser once the page re-loads in order to show the colour pickers.</span>
                </td>
        </tr>
        -->
        <tr valign="top">
            <th scope="row"><?php _e('Reset all values', 'cookie-law-info'); ?></th>
            <td>
                <input type="submit" name="delete_all_settings" value="<?php _e('Delete settings and reset', 'cookie-law-info'); ?>" class="button-secondary" onclick="cli_store_settings_btn_click(this.name); if(confirm('<?php _e('Are you sure you want to delete all your settings?', 'cookie-law-info'); ?>')){  }else{ return false;};" />
                <span class="cli_form_help"><?php _e('Warning: this will actually delete your current settings.', 'cookie-law-info'); ?></span>
            </td>
        </tr>
        <!--
        <tr valign="top">
                <th scope="row">Revert to previous version's settings</th>
                <td>
                        <input type="submit" name="revert_to_previous_settings" value="Revert to old settings" class="button-secondary" onclick="return confirm('You will lose your current settings. Are you sure?');" />
                        <span class="cli_form_help">Warning: this will actually delete your current settings.</span>
                </td>
        </tr>
        -->
    </table>
    <?php 
    //include "admin-settings-save-button.php";
    ?>
</div>