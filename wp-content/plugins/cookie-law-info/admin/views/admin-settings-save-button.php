<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div style="clear: both;"></div>
<div class="cli-plugin-toolbar bottom">
    <div class="left">
    </div>
    <div class="right">
        <input type="submit" name="update_admin_settings_form" value="<?php _e('Update Settings', 'cookie-law-info'); ?>" class="button-primary" style="float:right;" onClick="return cli_store_settings_btn_click(this.name)" />
        <span class="spinner" style="margin-top:9px"></span>
    </div>
</div>