<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<style>
    .vvv_textbox{
        height: 150px;
        width:100%;
    }
</style>
<script type="text/javascript">
    var cli_non_necessary_success_message='<?php echo __('Settings updated.', 'cookie-law-info');?>';
    var cli_non_necessary_error_message='<?php echo __('Unable to update Settings.', 'cookie-law-info');?>';
</script>
<div class="wrap">
    <div class="cookie-law-info-form-container">
        <div class="cli-plugin-toolbar top">
            <h3><?php echo __('Non-necessary Cookie Settings','cookie-law-info'); ?></h3>
        </div>
        <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>" id="cli_non-ncessary_form">
            <?php wp_nonce_field('cookielawinfo-update-thirdparty'); ?> 
            <table class="form-table cli_non_necessary_form">
                <tr>
                    <td>
                        <label for="thirdparty_on_field"><?php echo __('Enable Non-necessary Cookie','cookie-law-info'); ?></label>
                        <input type="radio" id="thirdparty_on_field_yes" name="thirdparty_on_field" class="styled" value="true" <?php echo ( filter_var($stored_options['thirdparty_on_field'], FILTER_VALIDATE_BOOLEAN) == true ) ? ' checked="checked" ' : ' '; ?> /> Yes
                        <input type="radio" id="thirdparty_on_field_no" name="thirdparty_on_field" class="styled" value="false" <?php echo ( filter_var($stored_options['thirdparty_on_field'], FILTER_VALIDATE_BOOLEAN) == false ) ? ' checked="checked" ' : ''; ?> /> No
                    </td>
                </tr>
                <tr>
                    <td>
                       <label for="thirdparty_head_section"><?php echo __('This script will be added to the page HEAD section if the above settings is enabled and user has give consent.','cookie-law-info');?></label>
                        <textarea name="thirdparty_head_section" class="vvv_textbox"><?php
                        echo apply_filters('format_to_edit', stripslashes($stored_options['thirdparty_head_section']));
                        ?></textarea>
                        <span class="cli_form_help">
                        <?php echo __('Print scripts in the head tag on the front end if above cookie settings is enabled and user has given consent.','cookie-law-info'); ?> <br />
                                eg:- &lt;script&gt;console.log("header script");&lt;/script&gt
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="thirdparty_body_section"><?php echo __('This script will be added right after the BODY section if the above settings is enabled and user has given consent.','cookie-law-info'); ?></label>
                        <textarea name="thirdparty_body_section" class="vvv_textbox"><?php echo apply_filters('format_to_edit', stripslashes($stored_options['thirdparty_body_section']));?></textarea>
                        <span class="cli_form_help">
                            <?php echo __('Print scripts before the closing body tag on the front end if above cookie settings is enabled and user has given consent.','cookie-law-info'); ?> <br />eg:- &lt;script&gt;console.log("body script");&lt;/script&gt;
                        </span>
                    </td>
                </tr>
            </table>
            <div class="cli-plugin-toolbar bottom">
                <div class="left">
                </div>
                <div class="right">
                    <input type="submit" name="update_admin_settings_form" value="<?php _e('Update Settings', 'cookie-law-info'); ?>" class="button-primary" style="float:right;" onClick="return cli_store_settings_btn_click(this.name)" />
                    <span class="spinner" style="margin-top:9px"></span>
                </div>
            </div>
        </form>
    </div>
</div>