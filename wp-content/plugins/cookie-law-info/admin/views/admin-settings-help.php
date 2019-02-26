<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="cookie-law-info-tab-content" data-id="<?php echo $target_id;?>">	        
	<ul class="cli_sub_tab">
        <li style="border-left:none; padding-left: 0px;" data-target="shortcodes"><a><?php _e('Shortcodes', 'cookie-law-info'); ?></a></li>
        <li data-target="help-links"><a><?php _e('Help Links', 'cookie-law-info'); ?></a></li>
    </ul>

    <div class="cli_sub_tab_container">
        <div class="cli_sub_tab_content" data-id="shortcodes" style="display:block;">
            <div style="font-size: 14px;">
        	<h3><?php _e('Cookie bar shortcodes', 'cookie-law-info'); ?></h3>
            <?php _e('You can enter the shortcodes in the "message" field of the Cookie Law Info bar. They add nicely formatted buttons and/or links into the cookie bar, without you having to add any HTML.', 'cookie-law-info'); ?>
        	</div>
            <ul class="cli-shortcodes">
            	<li>
            		<div style="font-weight: bold;">[cookie_accept]</div>
            		<span><?php _e('If you just want a standard green "Accept" button that closes the header and nothing more, use this shortcode. It is already styled, you don\'t need to customise it.', 'cookie-law-info'); ?></span>
            	</li>
            	<li>
            		<div style="font-weight: bold;">[cookie_accept colour="red"]</div>
            		<?php _e('Alternatively you can add a colour value. Choose from: red, blue, orange, yellow, green or pink.', 'cookie-law-info'); ?><br /><em><?php _e('Careful to use the British spelling of "colour" for the attribute.', 'cookie-law-info'); ?></em>
            	</li>
            	<li>
            	<div style="font-weight: bold;">[cookie_button]</div>
            	<?php _e('This is the "main button" you customise above.', 'cookie-law-info'); ?>
            	</li>

            	<li><div style="font-weight: bold;">[cookie_reject]</div>
            		<?php _e('This is the cookie reject button shortcode.', 'cookie-law-info'); ?>
            	</li>

            	<li><div style="font-weight: bold;">[cookie_link]</div>
            		<?php _e('This is the "read more" link you customise above.', 'cookie-law-info'); ?>
            	</li>
            	</ul>
            	<div style="font-size: 14px;">
        	        <h3 style="margin-bottom:5px; margin-top:25px;"><?php _e('Other shortcodes', 'cookie-law-info'); ?></h3>
        	        <?php _e('These shortcodes can be used in pages and posts on your website. It is not recommended to use these inside the cookie bar itself.', 'cookie-law-info'); ?>
        	    </div>

        	    <ul class="cli-shortcodes">
            	<li>
            		<div style="font-weight: bold;">[cookie_audit]</div>
            			<?php _e('This prints out a nice table of cookies, in line with the guidance given by the ICO.', 'cookie-law-info'); ?> <em><?php _e('You need to enter the cookies your website uses via the Cookie Law Info menu in your WordPress dashboard.', 'cookie-law-info'); ?></em>
                	<div style="font-weight: bold;">
                		[cookie_audit style="winter"] <br />
        		        [cookie_audit not_shown_message="No records found"] <br />
        		        [cookie_audit style="winter" not_shown_message="Not found"]
                	</div>
                	Styles included:	simple, classic, modern, rounded, elegant, winter. Default is classic.
            	</li>
                <li>
                	<div style="font-weight: bold;">[delete_cookies]</div>
                	<?php _e('This shortcode will display a normal HTML link which when clicked, will delete the cookie set by Cookie Law Info (this cookie is used to remember that the cookie bar is closed).', 'cookie-law-info'); ?>
                </li>
                <li>
                	<div style="font-weight: bold;">[delete_cookies text="Click here to delete"]</div>
                	<?php _e('Add any text you like- useful if you want e.g. another language to English.', 'cookie-law-info'); ?>
                </li>
                <li>
                    <div style="font-weight: bold;">[cookie_after_accept] Your content goes here... [/cookie_after_accept]</div>
                    <?php _e('Add content after accepting the cookie notice.', 'cookie-law-info'); ?>
                    You can use `do_shortcode` function to add shortcodes inside the template file.
                </li>
                </ul>
        </div>
        <div class="cli_sub_tab_content" data-id="help-links" style="float: left; height:auto;">
            <?php
            $admin_img_path=plugin_dir_url(CLI_PLUGIN_FILENAME).'admin/images/';
            ?>
            <h3><?php _e('Help Links', 'cookie-law-info'); ?></h3>
            <ul class="cli-help-links">
                <li>
                    <img src="<?php echo $admin_img_path;?>documentation.png">
                    <h3><?php _e('Documentation', 'cookie-law-info'); ?></h3>
                    <p><?php _e('Refer to our documentation to set and get started', 'cookie-law-info'); ?></p>
                    <a target="_blank" href="http://cookielawinfo.com/user-guide/" class="button button-primary">
                        <?php _e('Documentation', 'cookie-law-info'); ?>        
                    </a>
                </li>
                <li>
                    <img src="<?php echo $admin_img_path;?>support.png">
                    <h3><?php _e('Help and Support', 'cookie-law-info'); ?></h3>
                    <p><?php _e('We would love to help you on any queries or issues.', 'cookie-law-info'); ?></p>
                    <a target="_blank" href="https://wordpress.org/support/plugin/cookie-law-info/" class="button button-primary">
                        <?php _e('Contact Us', 'cookie-law-info'); ?>
                    </a>
                </li>               
            </ul>
        </div>    
    </div>
</div>