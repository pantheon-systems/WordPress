<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="cookie-law-info-tab-content" data-id="<?php echo $target_id;?>">
    <h3><?php _e('Cookie Bar', 'cookie-law-info'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="bar_heading_text_field"><?php _e('Message Heading', 'cookie-law-info'); ?></label></th>
            <td>
                <input type="text" name="bar_heading_text_field" value="<?php echo $the_options['bar_heading_text'] ?>" />
                <span class="cli_form_help"><?php _e('Leave it blank, If you do not need a heading', 'cookie-law-info'); ?>
                </span>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="notify_message_field"><?php _e('Message', 'cookie-law-info'); ?></label></th>
            <td>
                <?php
                    echo '<textarea name="notify_message_field" class="vvv_textbox">';
                    echo apply_filters('format_to_edit', stripslashes($the_options['notify_message'])) . '</textarea>';
                ?>
                <span class="cli_form_help"><?php _e('Shortcodes allowed: see the Help Guide tab', 'cookie-law-info'); ?> <br /><em><?php _e('Examples: "We use cookies on this website [cookie_accept] to find out how to delete cookies [cookie_link]."', 'cookie-law-info'); ?></em></span>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="background_field"><?php _e('Cookie Bar Colour', 'cookie-law-info'); ?></label></th>
            <td>
                <?php
                /** RICHARDASHBY EDIT */
                //echo '<input type="text" name="background_field" id="cli-colour-background" value="' .$the_options['background']. '" />';
                echo '<input type="text" name="background_field" id="cli-colour-background" value="' . $the_options['background'] . '" class="my-color-field" data-default-color="#fff" />';
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="text_field"><?php _e('Text Colour', 'cookie-law-info'); ?></label></th>
            <td>
                <?php
                /** RICHARDASHBY EDIT */
                echo '<input type="text" name="text_field" id="cli-colour-text" value="' . $the_options['text'] . '" class="my-color-field" data-default-color="#000" />';
                ?>
            </td>
        </tr>
        <!--
        <tr valign="top">
            <th scope="row"><label for="border_on_field"><?php _e('Show Border?', 'cookie-law-info'); ?></label></th>
            <td>
                <input type="radio" id="border_on_field_yes" name="border_on_field" class="styled" value="true" <?php echo ( $the_options['border_on'] == true ) ? ' checked="checked" />' : ' />'; ?> <?php _e('Yes', 'cookie-law-info'); ?>
                <input type="radio" id="border_on_field_no" name="border_on_field" class="styled" value="false" <?php echo ( $the_options['border_on'] == false ) ? ' checked="checked" />' : ' />'; ?> <?php _e('No', 'cookie-law-info'); ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="border_field"><?php _e('Border Colour', 'cookie-law-info'); ?></label></th>
            <td>
                <?php
                    echo '<input type="text" name="border_field" id="cli-colour-border" value="' . $the_options['border'] . '" class="my-color-field" />';
                ?>
            </td>
        </tr>
        -->
        
        <tr valign="top">
            <th scope="row"><label for="font_family_field"><?php _e('Font', 'cookie-law-info'); ?></label></th>
            <td>
                <select name="font_family_field" class="vvv_combobox">
                    <?php $this->print_combobox_options($this->get_fonts(), $the_options['font_family']) ?>
                </select>
            </td>
        </tr>
    </table>

    <?php 
    include "admin-settings-save-button.php";
    ?>
</div>