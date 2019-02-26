<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
$cli_admin_view_path=plugin_dir_path(CLI_PLUGIN_FILENAME).'admin/views/';
$cli_img_path=CLI_PLUGIN_URL . 'images/';
$plugin_name = 'wtgdprcookieconsent';
$cli_activation_status=get_option($plugin_name.'_activation_status');

//taking pages for privacy policy URL.
$args_for_get_pages=array(
    'sort_order'    => 'ASC',
    'sort_column'   => 'post_title',
    'hierarchical'  => 0,
    'child_of'      => 0,
    'parent'        => -1,
    'offset'        => 0,
    'post_type'     => 'page',
    'post_status'   => 'publish'
);
$all_pages=get_pages($args_for_get_pages);
?>
<script type="text/javascript">
    var cli_settings_success_message='<?php echo __('Settings updated.', 'cookie-law-info');?>';
    var cli_settings_error_message='<?php echo __('Unable to update Settings.', 'cookie-law-info');?>';
    var cli_reset_settings_success_message='<?php echo __('Settings reset to defaults.', 'cookie-law-info');?>';
    var cli_reset_settings_error_message='<?php echo __('Unable to reset settings.', 'cookie-law-info');?>';
</script>
<div class="wrap">
    <h2 class="wp-heading-inline"><?php _e('Cookie Law Settings', 'cookie-law-info'); ?></h2>
    
    <table class="cli_notify_table cli_bar_state">
        <tr valign="middle" class="cli_bar_on" style="<?php echo $the_options['is_on'] == true ? '' : 'display:none;';?>">
            <th scope="row" style="padding-left:15px;">
                <label><img id="cli-plugin-status-icon" src="<?php echo $cli_img_path;?>tick.png" /></label>
            </th>
            <td style="padding-left: 10px;">
                <?php _e('Your Cookie Law Info bar is switched on', 'cookie-law-info'); ?>
            </td>
        </tr>
        <tr valign="middle" class="cli_bar_off" style="<?php echo $the_options['is_on'] == true ? 'display:none;' : '';?>">
            <th scope="row" style="padding-left:15px;">
                <label><img id="cli-plugin-status-icon" src="<?php echo $cli_img_path;?>cross.png" /></label>
            </th>
            <td style="padding-left: 10px;">
                <?php _e('Your Cookie Law Info bar is switched off', 'cookie-law-info'); ?>
            </td>
        </tr>
    </table>


    <div class="cli_settings_left">
        <div class="nav-tab-wrapper wp-clearfix cookie-law-info-tab-head">
            <?php
            $tab_head_arr=array(
                'cookie-law-info-general'=>'General',
                'cookie-law-info-message-bar'=>'Customise Cookie Bar',
                'cookie-law-info-buttons'=>'Customise Buttons',
                'cookie-law-info-advanced'=>'Advanced',
                'cookie-law-info-help'=>'Help Guide'
            );
            Cookie_Law_Info::generate_settings_tabhead($tab_head_arr);
            ?>
        </div>
        <div class="cookie-law-info-tab-container">
            <?php
            $setting_views_a=array(
                'cookie-law-info-general'=>'admin-settings-general.php',
                'cookie-law-info-message-bar'=>'admin-settings-messagebar.php',
                'cookie-law-info-buttons'=>'admin-settings-buttons.php',                      
                'cookie-law-info-advanced'=>'admin-settings-advanced.php',         
            );
            $setting_views_b=array(           
                'cookie-law-info-help'=>'admin-settings-help.php',           
            );
            ?>
            <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]);?>" id="cli_settings_form">
                <input type="hidden" name="cli_update_action" value="" id="cli_update_action" />
                <?php
                // Set nonce:
                if (function_exists('wp_nonce_field'))
                {
                    wp_nonce_field('cookielawinfo-update-' . CLI_SETTINGS_FIELD);
                }
                foreach ($setting_views_a as $target_id=>$value) 
                {
                    $settings_view=$cli_admin_view_path.$value;
                    if(file_exists($settings_view))
                    {
                        include $settings_view;
                    }
                }
                ?> 
                <?php 
                //settings form fields for module
                do_action('cli_module_settings_form');?>            
            </form>
            <?php
            foreach ($setting_views_b as $target_id=>$value) 
            {
                $settings_view=$cli_admin_view_path.$value;
                if(file_exists($settings_view))
                {
                    include $settings_view;
                }
            }
            ?>
            <?php do_action('cli_module_out_settings_form');?>
        </div>
    </div>
    <div class="cli_settings_right">
     <?php include $cli_admin_view_path."goto-pro.php"; ?>   
    </div>

</div>