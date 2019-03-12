<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="cookie-law-info-tab-content" data-id="<?php echo $target_id;?>">
    
    <ul class="cli_sub_tab">
        <li style="border-left:none; padding-left: 0px;" data-target="accept-button"><a><?php _e('Accept Button', 'cookie-law-info'); ?></a></li>
        <li data-target="reject-button"><a><?php _e('Reject Button', 'cookie-law-info'); ?></a></li>
        <li data-target="read-more-button"><a><?php _e('Read More Link', 'cookie-law-info'); ?></a></li>
    </ul>

    <div class="cli_sub_tab_container">

        <div class="cli_sub_tab_content" data-id="accept-button" style="display:block;">
            <h3><?php _e('Main Button', 'cookie-law-info'); ?> <code>[cookie_button]</code></h3>
            <p><?php _e('This button/link can be customised to either simply close the cookie bar, or follow a link. You can also customise the colours and styles, and show it as a link or a button.', 'cookie-law-info'); ?></p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="button_1_text_field"><?php _e('Text', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="text" name="button_1_text_field" value="<?php echo stripslashes($the_options['button_1_text']) ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_1_link_colour_field"><?php _e('Text colour', 'cookie-law-info'); ?></label></th>
                    <td>
                        <?php
                            echo '<input type="text" name="button_1_link_colour_field" id="cli-colour-link-button-1" value="' . $the_options['button_1_link_colour'] . '" class="my-color-field" />';
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_1_as_button_field"><?php _e('Show as', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" cli_frm_tgl-target="cli_accept_type" id="button_1_as_button_field_yes" name="button_1_as_button_field" class="styled cli_form_toggle" value="true" <?php echo ( $the_options['button_1_as_button'] == true ) ? ' checked="checked"' : ' '; ?> /> <?php _e('Button', 'cookie-law-info'); ?>
                        
                        <input type="radio" cli_frm_tgl-target="cli_accept_type" id="button_1_as_button_field_no" name="button_1_as_button_field" class="styled cli_form_toggle" value="false" <?php echo ( $the_options['button_1_as_button'] == false ) ? ' checked="checked"' : ''; ?>  /> <?php _e('Link', 'cookie-law-info'); ?>
                    </td>
                </tr>
                <tr valign="top" class="cli-indent-15" cli_frm_tgl-id="cli_accept_type" cli_frm_tgl-val="true">
                    <th scope="row"><label for="button_1_button_colour_field"><?php _e('Background colour', 'cookie-law-info'); ?></label></th>
                    <td>
                        <?php
                        echo '<input type="text" name="button_1_button_colour_field" id="cli-colour-btn-button-1" value="' . $the_options['button_1_button_colour'] . '" class="my-color-field" />';
                        ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="button_1_action_field"><?php _e('Action', 'cookie-law-info'); ?></label></th>
                    <td>
                        <select name="button_1_action_field" id="cli-plugin-button-1-action" class="vvv_combobox cli_form_toggle" cli_frm_tgl-target="cli_accept_action">
                            <?php $this->print_combobox_options($this->get_js_actions(), $the_options['button_1_action']) ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top" class="cli-plugin-row cli-indent-15" cli_frm_tgl-id="cli_accept_action" cli_frm_tgl-val="CONSTANT_OPEN_URL">
                    <th scope="row"><label for="button_1_url_field"><?php _e('URL', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="text" name="button_1_url_field" id="button_1_url_field" value="<?php echo $the_options['button_1_url'] ?>" />
                        <span class="cli_form_help"><?php _e('Button will only link to URL if Action = Open URL', 'cookie-law-info'); ?></span>
                    </td>
                </tr>

                <tr valign="top" class="cli-plugin-row cli-indent-15" cli_frm_tgl-id="cli_accept_action" cli_frm_tgl-val="CONSTANT_OPEN_URL">
                    <th scope="row"><label for="button_1_new_win_field"><?php _e('Open URL in new window?', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" id="button_1_new_win_field_yes" name="button_1_new_win_field" class="styled" value="true" <?php echo ( $the_options['button_1_new_win'] == true ) ? ' checked="checked"' : ''; ?> /><?php _e('Yes', 'cookie-law-info'); ?>
                        
                        <input type="radio" id="button_1_new_win_field_no" name="button_1_new_win_field" class="styled" value="false" <?php echo ( $the_options['button_1_new_win'] == false ) ? ' checked="checked"' : ''; ?> /> <?php _e('No', 'cookie-law-info'); ?>
                    </td>
                </tr>
                
                
                
                <tr valign="top">
                    <th scope="row"><label for="button_1_button_size_field"><?php _e('Size', 'cookie-law-info'); ?></label></th>
                    <td>
                        <select name="button_1_button_size_field" class="vvv_combobox">
                            <?php $this->print_combobox_options($this->get_button_sizes(), $the_options['button_1_button_size']); ?>
                        </select>
                    </td>
                </tr>
            </table><!-- end custom button -->
        </div>

        <div class="cli_sub_tab_content" data-id="reject-button">
            <h3><?php _e('Reject Button', 'cookie-law-info'); ?> <code>[cookie_reject]</code></h3>
            <table class="form-table" >
                <tr valign="top">
                    <th scope="row"><label for="button_3_text_field"><?php _e('Text', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="text" name="button_3_text_field" value="<?php echo stripslashes($the_options['button_3_text']) ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_3_link_colour_field"><?php _e('Text colour', 'cookie-law-info'); ?></label></th>
                    <td>
                        <?php
                            echo '<input type="text" name="button_3_link_colour_field" id="cli-colour-link-button-3" value="' . $the_options['button_3_link_colour'] . '" class="my-color-field" />';
                        ?>
                    </td>
                </tr>               
                <tr valign="top">
                    <th scope="row"><label for="button_3_as_button_field"><?php _e('Show as', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" id="button_3_as_button_field_yes" name="button_3_as_button_field" class="styled cli_form_toggle" cli_frm_tgl-target="cli_reject_type" value="true" <?php echo ( $the_options['button_3_as_button'] == true ) ? ' checked="checked"' : ' '; ?>  /> <?php _e('Button', 'cookie-law-info'); ?>
                        
                        <input type="radio" id="button_3_as_button_field_no" name="button_3_as_button_field" class="styled cli_form_toggle" cli_frm_tgl-target="cli_reject_type" value="false" <?php echo ( $the_options['button_3_as_button'] == false ) ? ' checked="checked"' : ''; ?> /><?php _e('Link', 'cookie-law-info'); ?>
                    </td>
                </tr>
                <tr valign="top" cli_frm_tgl-id="cli_reject_type" cli_frm_tgl-val="true">
                    <th scope="row"><label for="button_3_button_colour_field"><?php _e('Background colour', 'cookie-law-info'); ?></label></th>
                    <td>
                        <?php
                            echo '<input type="text" name="button_3_button_colour_field" id="cli-colour-btn-button-3" value="' . $the_options['button_3_button_colour'] . '" class="my-color-field" />';
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_3_action_field"><?php _e('Action', 'cookie-law-info'); ?></label></th>
                    <td>
                        <select name="button_3_action_field" id="cli-plugin-button-3-action" class="vvv_combobox cli_form_toggle" cli_frm_tgl-target="cli_reject_action">
                            <?php
                                $action_list = $this->get_js_actions();
                                $action_list['Close Header'] = '#cookie_action_close_header_reject';
                                $this->print_combobox_options($action_list, $the_options['button_3_action']);
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top" class="cli-plugin-row" cli_frm_tgl-id="cli_reject_action" cli_frm_tgl-val="CONSTANT_OPEN_URL">
                    <th scope="row"><label for="button_3_url_field"><?php _e('URL', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="text" name="button_3_url_field" id="button_3_url_field" value="<?php echo $the_options['button_3_url'] ?>" />
                        <span class="cli_form_help"><?php _e('Button will only link to URL if Action = Open URL', 'cookie-law-info'); ?></span>
                    </td>
                </tr>

                <tr valign="top" class="cli-plugin-row" cli_frm_tgl-id="cli_reject_action" cli_frm_tgl-val="CONSTANT_OPEN_URL">
                    <th scope="row"><label for="button_3_new_win_field"><?php _e('Open URL in new window?', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" id="button_3_new_win_field_yes" name="button_3_new_win_field" class="styled" value="true" <?php echo ( $the_options['button_3_new_win'] == true ) ? ' checked="checked"' : ''; ?>  /><?php _e('Yes', 'cookie-law-info'); ?>
                        <input type="radio" id="button_3_new_win_field_no" name="button_3_new_win_field" class="styled" value="false" <?php echo ( $the_options['button_3_new_win'] == false ) ? ' checked="checked"' : ''; ?> /><?php _e('No', 'cookie-law-info'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_3_button_size_field"><?php _e('Size', 'cookie-law-info'); ?></label></th>
                    <td>
                        <select name="button_3_button_size_field" class="vvv_combobox">
                            <?php $this->print_combobox_options($this->get_button_sizes(), $the_options['button_3_button_size']); ?>
                        </select>
                    </td>
                </tr>
            </table><!-- end custom button -->
        </div>
    
        <div class="cli_sub_tab_content" data-id="read-more-button">
            <h3><?php _e('Read More Link', 'cookie-law-info'); ?> <code>[cookie_link]</code></h3>
            <p><?php _e('This button/link can be used to provide a link out to your Privacy & Cookie Policy. You can customise it any way you like.', 'cookie-law-info'); ?></p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="button_2_text_field"><?php _e('Text', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="text" name="button_2_text_field" value="<?php echo stripslashes($the_options['button_2_text']) ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_2_link_colour_field"><?php _e('Text colour', 'cookie-law-info'); ?></label></th>
                    <td>
                        <?php
                            echo '<input type="text" name="button_2_link_colour_field" id="cli-colour-link-button-1" value="' . $the_options['button_2_link_colour'] . '" class="my-color-field" />';
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_2_as_button_field"><?php _e('Show as', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" id="button_2_as_button_field_yes" name="button_2_as_button_field" class="styled cli_form_toggle" cli_frm_tgl-target="cli_readmore_type" value="true" <?php echo ( $the_options['button_2_as_button'] == true ) ? ' checked="checked"' : ''; ?>  /> <?php _e('Button', 'cookie-law-info'); ?>
                               
                        <input type="radio" id="button_2_as_button_field_no" name="button_2_as_button_field" class="styled cli_form_toggle" cli_frm_tgl-target="cli_readmore_type" value="false" <?php echo ( $the_options['button_2_as_button'] == false ) ? ' checked="checked"' : ''; ?> /> <?php _e('Link', 'cookie-law-info'); ?>
                    </td>
                </tr>
                <tr valign="top" cli_frm_tgl-id="cli_readmore_type" cli_frm_tgl-val="true">
                    <th scope="row"><label for="button_2_button_colour_field"><?php _e('Background colour', 'cookie-law-info'); ?></label></th>
                    <td>
                        <?php
                            echo '<input type="text" name="button_2_button_colour_field" id="cli-colour-btn-button-1" value="' . $the_options['button_2_button_colour'] . '" class="my-color-field" />';
                        ?>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><label for="button_2_url_type_field"><?php _e('URL or Page?', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" id="button_2_url_type_field_url" name="button_2_url_type_field" class="styled cli_form_toggle" cli_frm_tgl-target="cli_readmore_url_type" value="url" <?php echo ( $the_options['button_2_url_type'] == 'url' ) ? ' checked="checked"' : ''; ?>  /> <?php _e('URL', 'cookie-law-info'); ?>
                               
                        <input type="radio" id="button_2_url_type_field_page" name="button_2_url_type_field" class="styled cli_form_toggle" cli_frm_tgl-target="cli_readmore_url_type" value="page" <?php echo ( $the_options['button_2_url_type'] == 'page' ) ? ' checked="checked"' : ''; ?> /> <?php _e('Page', 'cookie-law-info'); ?>
                    </td>
                </tr>

                <tr valign="top" cli_frm_tgl-id="cli_readmore_url_type" cli_frm_tgl-val="url">
                    <th scope="row"><label for="button_2_url_field"><?php _e('URL', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="text" name="button_2_url_field" id="button_2_url_field" value="<?php echo $the_options['button_2_url'] ?>" />
                    </td>
                </tr>
                <tr valign="top" cli_frm_tgl-id="cli_readmore_url_type" cli_frm_tgl-val="page">
                    <th scope="row"><label for="button_2_page_field"><?php _e('Page', 'cookie-law-info'); ?></label></th>
                    <td>
                        <select name="button_2_page_field" class="vvv_combobox" id="button_2_page_field">
                            <option value="0">--<?php _e('Select One','cookie-law-info'); ?>--</option>
                            <?php
                            foreach($all_pages as $page) 
                            {
                                ?>
                                <option value="<?php echo $page->ID; ?>" <?php echo ($the_options['button_2_page']==$page->ID ? 'selected' : ''); ?>> <?php echo $page->post_title;?> </option>;
                                <?php
                            }
                            ?>
                        </select>
                        <?php
                        if($the_options['button_2_page']>0)
                        {
                            $privacy_policy_page=get_post($the_options['button_2_page']);
                            $privacy_page_exists=0;
                            if($privacy_policy_page instanceof WP_Post)
                            {
                                if($privacy_policy_page->post_status==='publish') 
                                {
                                    $privacy_page_exists=1;
                                }  
                            }
                            if($privacy_page_exists==0)
                            {
                                ?>
                                <span class="cli_form_er cli_privacy_page_not_exists_er" style="display: inline;"><?php echo _e('The currently selected page does not exist. Please select a new page.'); ?></span>
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_2_new_win_field"><?php _e('Open in new window?', 'cookie-law-info'); ?></label></th>
                    <td>
                        <input type="radio" id="button_2_new_win_field_yes" name="button_2_new_win_field" class="styled" value="true" <?php echo ( $the_options['button_2_new_win'] == true ) ? ' checked="checked"' : ''; ?> /> <?php _e('Yes', 'cookie-law-info'); ?>
                               <input type="radio" id="button_2_new_win_field_no" name="button_2_new_win_field" class="styled" value="false" <?php echo ( $the_options['button_2_new_win'] == false ) ? ' checked="checked"' : ''; ?> /> <?php _e('No', 'cookie-law-info'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="button_2_button_size_field"><?php _e('Size', 'cookie-law-info'); ?></label></th>
                    <td>
                        <select name="button_2_button_size_field" class="vvv_combobox">
                            <?php $this->print_combobox_options($this->get_button_sizes(), $the_options['button_2_button_size']); ?>
                        </select>
                    </td>
                </tr>
            </table><!-- end custom button -->
        </div>

    </div>
    <?php 
    include "admin-settings-save-button.php";
    ?>
</div>